<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://steadyhq.com
 * @since      1.0.0
 *
 * @package    Steady_WP
 * @subpackage Steady_WP/includes
 */

class Steady_WP {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      Steady_WP_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	public $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $steady_wp    The string used to uniquely identify this plugin.
	 */
	protected $steady_wp;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;


	/**
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'STEADY_WP_VERSION' ) ) {
			$this->version = STEADY_WP_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->steady_wp = 'steady-wp';
	}

	public function init() {
		$this->load_dependencies();
		$this->set_loader();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	private function set_loader() {
		if ( ! $this->loader ) {
			$this->loader = new Steady_WP_Loader();
		}
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		// Plugin
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-steady-wp-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-steady-wp-i18n.php';

		// Admin
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-steady-wp-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/includes/class-steady-wp-settings.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/includes/class-steady-wp-editor.php';

		// Public
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-steady-wp-public.php';

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Steady_WP_I18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {
		$plugin_i18n = new Steady_WP_I18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Steady_WP_Admin( $this->get_steady_wp(), $this->get_version() );
		$plugin_admin->init( $this->loader );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		$plugin_public = new Steady_WP_Public( $this->get_steady_wp(), $this->get_version() );
		$plugin_public->init( $this->loader );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_steady_wp() {
		return $this->steady_wp;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Steady_WP_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
