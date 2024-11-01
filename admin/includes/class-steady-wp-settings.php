<?php

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Steady_WP_Settings {


	public function __construct( $steady_wp, $version ) {
		$this->steady_wp          = $steady_wp;
		$this->version            = $version;
		$this->settings_page_slug = $this->steady_wp . '_settings';
		$this->settings_slug      = $this->settings_page_slug;
	}

	/**
	 * Static methods
	 */
	public static function is_connected() {
		return empty( get_option( 'steady-wp_settings_connect_is_connected' ) ) ? false : true;
	}

	public static function set_connected( $val ) {
		return update_option( 'steady-wp_settings_connect_is_connected', $val );
	}

	/**
	 * WP Admin Notices
	 */
	public function register_admin_notices() {
		// return if not admin or on Steady Settings page
		if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$curr_screen = get_current_screen();
		if ( property_exists( $curr_screen, 'parent_base' )
		&& 'steady-wp_settings' === $curr_screen->parent_base ) {
			return;
		}

		// display admin message when Plugin is not connected
		if ( ! $this->is_connected() ) {
			?>
		<div class="notice notice-warning is-dismissible">
			<p>
			<?php
			// translators: contains settings admin url
			$msg_text = sprintf( __( 'Steady plugin is activated but not connected. Please go to the <a href="%s"><strong>plugin page</strong></a>, to connect your WordPress installation with Steady.', 'steady-wp' ), admin_url( 'admin.php?page=steady-wp_settings' ) );
			echo wp_kses_post( $msg_text );
			?>
			</p>
		</div>
			<?php
		}
	}

	/**
	 * Admin Pages
	 * @return Array of pages
	 */
	private function get_pages() {
		$result = [
			[
				'_type'      => 'menu',
				'page_title' => 'Steady for WordPress',
				'menu_title' => __( 'Steady', 'steady-wp' ),
				'capability' => 'manage_options',
				'menu_slug'  => $this->settings_page_slug,
				'function'   => function () {
					$template_path = 'partials/steady-wp-admin-display.php';
					$attr          = [
						'steady_wp'          => $this->steady_wp,
						'settings_slug'      => $this->settings_slug,
						'settings_page_slug' => $this->settings_page_slug,
					];
					// don't need to escape here because it's espaced in the template
					// phpcs:ignore WordPress.Security.EscapeOutput
					echo Steady_WP_Admin::get_template_html( $template_path, $attr );
				},
				'icon_url'   => plugins_url() . '/' . $this->steady_wp . '/admin/img/icon_16.png',
			],
		];
		return $result;
	}

	/*
	Register Admin menu pages
	*/
	public function register_admin_pages() {
		$pages = $this->get_pages();
		foreach ( $pages as $page ) {
			if ( 'menu' === $page['_type'] ) {
				add_menu_page(
					$page['page_title'],
					$page['menu_title'],
					$page['capability'],
					$page['menu_slug'],
					$page['function'],
					$page['icon_url']
				);
			}
		}
	}

	/*
	Settings API sections
	*/
	private function get_sections() {
		$result = [
			[
				'id'    => $this->settings_slug . '_connect',
				'title' => __( 'Connect with Steady', 'steady-wp' ),
				'cb'    => 'steady_settings_connect_cb',
			],
		];
		return $result;
	}

	/*
	Settings API fields
	*/
	private function get_fields() {
		$result = [
			[
				'id'          => $this->settings_slug . '_connect_api_key',
				'title'       => __( 'WordPress Key', 'steady-wp' ),
				'description' => __( 'Enter the WordPress Key here', 'steady-wp' ),
				'type'        => 'text',
				'section'     => $this->settings_slug . '_connect',
			],
			/*
			Private Option fields ($this->settings_slug .):
			_connect_is_connected
			_connect_publication_title
			_connect_campaign_url
			_connect_widget_url
			*/
		];
		return $result;
	}

	/*
	Register Settings API sections & fields
	*/
	public function register_settings_fields() {
		$sections = $this->get_sections();
		$fields   = $this->get_fields();

		// register setting
		register_setting(
			$this->steady_wp,
			$this->settings_slug,
			array( $this, 'validate_field' )
		);

		// register sections
		foreach ( $sections as $section ) {
			add_settings_section(
				$section['id'],
				$section['title'],
				( array_key_exists( 'cb', $section ) ) ? array( $this, $section['cb'] ) : '',
				$this->settings_page_slug
			);
		}

		// register fields
		foreach ( $fields as $field_slug => $field_val ) {

			$default_field = [
				'id'          => '', // the ID of the setting in our options array, and the ID of the HTML form element
				'title'       => '', // the label for the HTML form element
				'helper'      => '', // helper
				'description' => '', // the description displayed under the HTML form element
				'default'     => '', // the default value for this setting
				'placeholder' => '',
				'type'        => 'text', // the HTML form element to use
				'section'     => '', // the section this setting belongs to
				'choices'     => array(), // (optional): the values in radio buttons or a drop-down menu
				'class'       => '', // the HTML form element class (can be used for validation purposes)
			];

			$field = wp_parse_args( $field_val, $default_field );

			add_settings_field(
				$field['id'],
				$field['title'],
				array( $this, 'field_callback' ),
				$this->settings_page_slug,
				$field['section'],
				$field
			);

		}
	}

	/*
	Settings Page
	*/
	public function steady_settings_connect_cb( $args ) {
		$steady_backend_url = STEADY_WP_URL . '/backend/publications/default/integrations/oauth_client/edit';
		echo esc_html( __( 'Follow these steps to connect your WordPress with Steady:', 'steady-wp' ) );
		echo '<ol>';
		// translators: contains steady backend url
		echo '<li>' . wp_kses_post( sprintf( __( 'Click <a href="%s" target="_blank"><strong>here</strong></a> and copy the WordPress Key from your Steady-Settings to your clipboard.', 'steady-wp' ), $steady_backend_url ) ) . '</li>';
		echo '<li>' . esc_html( __( 'Paste the copied WordPress Key into the field "WordPress Key" on this page.', 'steady-wp' ) ) . '</li>';
		echo '<li>' . esc_html( __( 'Click "Connect". Your WordPress should now be connected with Steady.', 'steady-wp' ) ) . '</li>';
		echo '</ol>';
	}

	public function field_callback( $args ) {
		$value = get_option( $args['id'] ); // Get the current value, if there is one
		if ( empty( $value ) ) { // If no value exists
			$value = $args['default']; // Set to our default
		}

		// Check which type of field we want
		switch ( $args['type'] ) {

			case 'text':
				$aria_describedby = '';
				$class            = 'regular-text ' . $args['class'];
				if ( ! empty( $args['description'] ) ) {
					$aria_describedby = 'aria-describedby="' . $args['id'] . '-description"';
				}
				printf(
					'<input name="%1$s" id="%2$s" type="%3$s" placeholder="%4$s" value="%5$s" class="%6$s" %7$s />',
					esc_attr( $this->settings_slug . '[' . $args['id'] . ']' ),
					esc_attr( $args['id'] ),
					esc_attr( $args['type'] ),
					esc_attr( $args['placeholder'] ),
					esc_attr( $value ),
					esc_attr( $class ),
					esc_attr( $aria_describedby )
				);
				break;

			case 'hidden':
				$class = '';
				if ( ! empty( $args['class'] ) ) {
					$class = 'class="' . $args['class'] . '"';
				}
				printf(
					'<input name="%1$s" id="%2$s" type="%3$s" value="%4$s" %5$s />',
					esc_attr( $this->settings_slug . '[' . $args['id'] . ']' ),
					esc_attr( $args['id'] ),
					esc_attr( $args['type'] ),
					esc_attr( $value ),
					esc_attr( $class )
				);
				break;
		}

		// If there is help text
		$helper = $args['helper'];
		if ( $helper ) {
			printf( '<span class="helper"> %s</span>', esc_html( $helper ) ); // Show it
		}

		// If there is a description text
		$description = $args['description'];
		if ( $description ) {
			printf( '<p class="description" id="%1$s-description">%2$s</p>', esc_attr( $args['id'] ), esc_html( $description ) ); // Show it
		}
	}

	/*
	Validate functions for Settings
	*/
	public function validate_field( $input ) {
		$valid_input = [];
		$fields      = $this->get_fields();

		// check for disconnect value
		// nonce isn't missing in the form, false positive because of $_POST
		// phpcs:ignore WordPress.Security.NonceVerification
		if ( isset( $_POST['disconnect_from_steady'] ) ) {
			$this->disconnect_from_steady();
			return;
		}

		// loop only expected fields
		foreach ( $fields as $field ) {
			if ( ! isset( $input[ $field['id'] ] ) ) {
				continue;
			}

			// get old values
			$old_val = get_option( $field['id'] );

			// API key
			if ( $field['id'] === $this->settings_slug . '_connect_api_key' ) {
				$api_key   = $this->validate_api_key( $input[ $field['id'] ], $old_val );
				$connected = false;
				if ( $api_key !== $old_val ) {
					$connected = $this->connect_with_steady( $api_key );
				}
				$valid_input[ $field['id'] ] = $connected ? $api_key : $old_val;
			}
			// other fields here ...
		}

		// display "settings updated" message
		if ( empty( get_settings_errors( $this->settings_slug ) ) ) {
			add_settings_error(
				$this->settings_slug,
				'settings-updated',
				__( 'Settings updated', 'steady-wp' ),
				'updated'
			);
		}

		return $valid_input;
	}

	/*
	Validate api key, connect to steady
	receive publication data
	*/
	public function validate_api_key( $api_key, $old_val ) {
		if ( preg_match( STEADY_WP_KEY_PATTERN, $api_key ) ) {
			return $api_key;
		} else {
			add_settings_error(
				$this->settings_slug,
				'apikey-invalid',
				__( 'Please provide a valid WordPress Key.', 'steady-wp' ),
				'error'
			);
			return $old_val;
		}
	}

	/*
	Connect WP with Steady
	returns hashed api key
	*/
	private function connect_with_steady( $api_key ) {

		$api_args = array(
			'headers' => array(
				'x-api-key' => $api_key,
			),
		);

		$response = wp_remote_get( STEADY_WP_URL . '/api/v1/publication', $api_args );

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$result = json_decode( $response['body'] );

		// check response for integrity
		if ( ! isset( $result->data->attributes->title )
			|| ! isset( $result->data->attributes->{'js-widget-url'} )
			|| ! isset( $result->data->attributes->{'campaign-page-url'} )
		) {
			add_settings_error(
				$this->settings_slug,
				'api-error',
				__( 'Steady responded with an error. Please try again later or get in touch with us if the problem persists.', 'steady-wp' ),
				'error'
			);
			return false;
		}

		// save widget url, api key etc.
		$this->set_connected( true );
		update_option( $this->settings_slug . '_connect_publication_title', $result->data->attributes->title );
		update_option( $this->settings_slug . '_connect_campaign_url', $result->data->attributes->{'campaign-page-url'} );
		update_option( $this->settings_slug . '_connect_widget_url', $result->data->attributes->{'js-widget-url'} );

		return true;
	}

	/*
	Disconnect from Steady
	*/
	private function disconnect_from_steady() {
		// save widget url, api key etc.
		delete_option( $this->settings_slug . '_connect_api_key' );
		$this->set_connected( false );
		delete_option( $this->settings_slug . '_connect_publication_title' );
		delete_option( $this->settings_slug . '_connect_campaign_url' );
		delete_option( $this->settings_slug . '_connect_widget_url' );
	}

}
