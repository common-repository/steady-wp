<?php

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Steady_WP_Editor {


	public function __construct( $steady_wp, $version ) {
		$this->steady_wp = $steady_wp;
		$this->version   = $version;
	}

	/**
	 * TinyMCE translations
	 */
	public function tinymce_translation_add_locale( $locales ) {
		$plugin_name = 'steady_btn_paywall';
		$locales[ $plugin_name ] = plugin_dir_path( __FILE__ ) . 'includes/tinymce-i18n.php';
		return $locales;
	}

	/**
	 * TinyMCE Button
	 */
	public function enqueue_tinymce_scripts( $plugin_array ) {
		if ( Steady_WP_Settings::is_connected() ) {
			//enqueue TinyMCE plugin script with its ID.
			$plugin_name                  = 'steady_btn_paywall';
			$plugin_array[ $plugin_name ] = plugin_dir_url( __DIR__ ) . 'js/tinymce.js';
		}

		return $plugin_array;
	}

	public function register_buttons_editor( $buttons ) {
		if ( Steady_WP_Settings::is_connected() ) {
			// register buttons with their id.
			array_push( $buttons, 'add_paywall' );
		}

		return $buttons;
	}

	public function set_paywallcode_in_paragaph( $content ) {
		// replace old paywall code
		$content = str_replace( '___STEADY_PAYWALL___', '<p><!--steady-paywall--></p>', $content );

		// make sure paywall code is not surrounded by text on same line
		// wrap it in a <p> manually. WordPress does the rest.
		$content = preg_replace( '/^(.+\n)([^<]*)(<\!--steady-paywall-->)([^\n]*)([^$]+)/m', '${1}${2}<p>${3}</p>${4}${5}', $content );
		return $content;
	}

	public function js_vars() {
		$path = plugins_url() . '/' . $this->steady_wp;
		?>
		<script type='text/javascript'>
			var steady_wp_path = '<?php echo esc_js( $path ); ?>';
		</script>
		<?php
	}
}
