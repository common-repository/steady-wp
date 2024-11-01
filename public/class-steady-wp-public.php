<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://steadyhq.com
 * @since      1.0.0
 *
 * @package    Steady_WP
 * @subpackage Steady_WP/public
 */

class Steady_WP_Public {

	private $steady_wp;
	private $version;
	public $is_paywall_activated = false;
	public $is_skip_paywall      = false;
	public $has_subscription     = false;

	public function __construct( $steady_wp, $version ) {
		$this->steady_wp = $steady_wp;
		$this->version   = $version;
	}

	public function init( $loader ) {
		if ( Steady_WP_Settings::is_connected() ) {
			$loader->add_action( 'wp_enqueue_scripts', $this, 'register_steady_widget' );
			$loader->add_filter( 'post_class', $this, 'add_paywall_css_class', 10, 3 );
			$loader->add_filter( 'the_content', $this, 'cut_content' );
		}
	}

	/**
	 * Register Steady JavaScript-Widget
	 * @return [type] [description]
	 */
	public function register_steady_widget() {

		$widget_url = get_option( $this->steady_wp . '_settings_connect_widget_url' );
		if ( empty( $widget_url ) ) {
			return;
		}

		wp_enqueue_script(
			$this->steady_wp,
			$widget_url,
			array(),
			$this->version,
			false
		);
	}

	/**
	 * Add Paywall CSS class to post parent
	 */
	public function add_paywall_css_class( $classes, $class, $post_id ) {
		if ( $this->is_single_post() ) {
			$classes[] = 'steady-paywall-container';
		}
		return $classes;
	}

	/**
	 * Get API key from settings
	 */
	public function get_api_key() {
		$options = get_option( $this->steady_wp . '_settings', array() );
		return isset( $options['steady-wp_settings_connect_api_key'] ) ? $options['steady-wp_settings_connect_api_key'] : '';
	}

	/**
	 * Check if we're inside the main loop in a single post page.
	 */
	public function is_single_post() {
		return ( is_singular( 'post' ) && is_main_query() );
	}

	/**
	 * Setup data from wp api endpoint
	 */
	public function setup_api_data() {

		$api_key = $this->get_api_key();

		if ( empty( $api_key ) ) {
			return false;
		}

		$api_args = array(
			'headers' => array(
				'x-api-key' => $api_key,
			),
		);

		if ( isset( $_COOKIE['steady-token'] ) ) {
			$api_args['headers']['Authorization'] = 'Bearer ' . $_COOKIE['steady-token'];
		}

		// TODO: cache the reponse in transients
		$response = wp_remote_get( STEADY_WP_URL . '/api/v1/wordpress/data', $api_args );

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$http_code = $response['response']['code'];
		$result    = json_decode( $response['body'], true );

		if ( 200 !== $http_code ) {
			return false;
		}

		if ( isset( $result['errors'] ) ) {
			return false;
		}

		$this->is_paywall_activated = $result['paywall_activated'];
		$this->is_skip_paywall      = $result['paywall_passthrough_enabled'];
		$this->has_subscription     = $result['has_subscription'];

		return true;
	}

	/**
	 * Cut content (Paywall)
	 */
	public function cut_content( $content ) {

		if ( ! $this->is_single_post() ) {
			return $content;
		}

		// check for Paywall code
		preg_match( '/(.*)(<p><\!--steady-paywall--><\/p>)(.*)/s', $content, $matches );
		if ( empty( $matches ) ) {
			return $content;
		}

		$this->setup_api_data();

		// disable caching
		if ( ! defined( 'DONOTCACHEPAGE' ) ) {
			define( 'DONOTCACHEPAGE', true );
		}

		if ( ! $this->is_paywall_activated || $this->has_subscription ) {
			return $content;
		}

		// return cut content
		$first_part  = $matches[1];
		$second_part = $matches[3];
		$pw_code     = '<div id="steady_paywall" style="display: none;"></div>';

		if ( ! $this->is_skip_paywall ) {
			$cut_length  = 1050;
			$second_part = substr( $matches[3], 0, $cut_length ) . '…</p>';
		}

		$output = $first_part . $pw_code . $second_part;
		return $output;
	}

}
