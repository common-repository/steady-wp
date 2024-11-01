<?php

/**
 * The plugin bootstrap file
 *
 * @link              https://steadyhq.com
 * @since             1.0.0
 * @package           Steady_WP
 *
 * @wordpress-plugin
 * Plugin Name:       Steady for WordPress
 * Plugin URI:        https://steadyHQ.com
 * Description:       Steady is the perfect plugin for regular payments: offer subscriptions, pledges, use a flexible paywall or start a subscription crowdfunding.
 * Version:           1.3.3
 * Author:            Steady
 * Author URI:        https://steadyHQ.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       steady-wp
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'STEADY_WP_VERSION', '1.3.3' );
if ( ! defined( 'STEADY_WP_URL' ) ) {
	define( 'STEADY_WP_URL', 'https://steadyhq.com' );
}
define( 'STEADY_WP_KEY_PATTERN', '/^[A-Za-z0-9\-_]{40,}$/' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-steady-wp.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_steady_wp() {

	$plugin = new Steady_WP();
	$plugin->init();
	$plugin->run();

}
run_steady_wp();
