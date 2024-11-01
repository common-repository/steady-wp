<?php
// This file is based on wp-includes/js/tinymce/langs/wp-langs.php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( '_WP_Editors' ) ) {
	require ABSPATH . WPINC . '/class-wp-editor.php';
}

function steady_wp_tinymce_translation() {
	$strings    = array(
		'paywall_code_exists' => __( 'The Steady Paywall can only be included once. Please remove the Paywall code first to insert it at another position.', 'steady-wp' ),
		'insert_paywall_code' => __( 'Insert Steady Paywall', 'steady-wp' ),
		'steady_paywall'      => __( 'Steady Paywall', 'steady-wp' ),
	);
	$locale     = _WP_Editors::$mce_locale;
	$translated = 'tinyMCE.addI18n("' . $locale . '.steady_wp_translation", ' . wp_json_encode( $strings ) . '); ';
	return $translated;
}
$strings = steady_wp_tinymce_translation();


