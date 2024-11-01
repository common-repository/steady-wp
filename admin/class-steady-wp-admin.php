<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://steadyhq.com
 * @since      1.0.0
 *
 * @package    Steady_WP
 */

class Steady_WP_Admin {

	private $steady_wp;
	private $version;
	private $pages;

	public function __construct( $steady_wp, $version ) {
		$this->steady_wp = $steady_wp;
		$this->version   = $version;
	}

	public function init( $loader ) {

		// load styles & scripts
		$loader->add_action( 'admin_enqueue_scripts', $this, 'enqueue_admin_styles' );
		$loader->add_action( 'admin_init', $this, 'register_editor_style' );
		$loader->add_action( 'enqueue_block_editor_assets', $this, 'enqueue_block_editor_assets' );

		// Steady_WP_Settings
		$steady_settings = new Steady_WP_Settings( $this->steady_wp, $this->version );
		$loader->add_action( 'admin_menu', $steady_settings, 'register_admin_pages' );
		$loader->add_action( 'admin_init', $steady_settings, 'register_settings_fields' );
		$loader->add_action( 'admin_notices', $steady_settings, 'register_admin_notices' );

		// Steady_WP_Editor
		$steady_editor = new Steady_WP_Editor( $this->steady_wp, $this->version );
		# mce_external_languages filters are not run in WP 5 (see script-loader.php, in WP 4 they were run by class-wp-editor.php)
		$loader->add_filter( 'mce_external_languages', $steady_editor, 'tinymce_translation_add_locale' );
		$loader->add_filter( 'mce_external_plugins', $steady_editor, 'enqueue_tinymce_scripts' );
		$loader->add_filter( 'mce_buttons', $steady_editor, 'register_buttons_editor' );
		$loader->add_filter( 'content_save_pre', $steady_editor, 'set_paywallcode_in_paragaph' );
		$loader->add_action( 'admin_head', $steady_editor, 'js_vars' );
		$loader->add_action( 'admin_head-post.php', $steady_editor, 'js_vars' );
		$loader->add_action( 'admin_head-post-new.php', $steady_editor, 'js_vars' );
	}

	/**
	 * Register the stylesheets for the settings area.
	 */
	public function enqueue_admin_styles( $hook ) {

		if ( 'toplevel_page_steady-wp_settings' === $hook ) {
			wp_enqueue_style(
				$this->steady_wp . '-admin',
				plugin_dir_url( __FILE__ ) . 'css/steady-wp-admin.css',
				array(),
				$this->version,
				'all'
			);
		}
	}

	/*
		Register Editor style
	 */
	public function register_editor_style() {
		add_editor_style( plugin_dir_url( __FILE__ ) . 'css/steady-wp-editor.css' );
	}

	/*
		Enqueue block editor CSS and JS
	 */
	public function enqueue_block_editor_assets() {
		$script_handle = $this->steady_wp . '-block';
		wp_enqueue_script(
			$script_handle,
			plugin_dir_url( __FILE__ ) . 'js/steady-wp-block.js',
			array( 'wp-i18n', 'wp-element', 'wp-blocks', 'wp-components', 'wp-editor', 'wp-edit-post' ),
			$this->version,
			true
		);

		$gettext_domain = 'steady-wp';
		wp_set_script_translations( $script_handle, $gettext_domain, plugin_dir_path( __DIR__ ) . 'languages' );

		wp_enqueue_style(
			$this->steady_wp . '-block',
			plugin_dir_url( __FILE__ ) . 'css/steady-wp-block.css',
			array(),
			$this->version
		);
	}

	/**
	 * Renders the contents of the given template to a string and returns it.
	 */
	public static function get_template_html( $template_path, $attributes = null ) {
		if ( ! $attributes ) {
			$attributes = array();
		}

		ob_start();

		require $template_path;

		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

}


