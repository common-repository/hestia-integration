<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              hestia.delivery
 * @since             1.0.0
 * @package           hestia-integration
 *
 * @wordpress-plugin
 * Plugin Name:       Hestia Integration
 * Plugin URI:        http://hestia.delivery
 * Description:       Integration with Hestia Delivery.
 * Version:           1.0.1
 * Author:            Hestia
 * Author URI:        http://hestia.delivery
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       hestia-integration
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once plugin_dir_path( __FILE__ ) . 'config.php';

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WPCP_API_VER', '1.0.1' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/plugin-activator.php
 */
function activate_hestia() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/plugin-activator.php';
	Wpcpapi_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/plugin-deactivator.php
 */
function deactivate_hestia() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/plugin-deactivator.php';
	Wpcpapi_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_hestia' );
register_deactivation_hook( __FILE__, 'deactivate_hestia' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/plugin-core.php';


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_hestia() {

	$plugin = new Wpcpapi();
	$plugin->run();

}
run_hestia();
