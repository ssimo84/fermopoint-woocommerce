<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://www.digitalissimoweb.it
 * @since             1.0.0
 * @package           Fermopoint
 *
 * @wordpress-plugin
 * Plugin Name:       Fermo!Point Woocommerce
 * Plugin URI:        plugin.digitalissimoweb.it/fermopoint
 * Description:       Module for integrating Fermo!Points collecting points system.
 * Version:           1.0.0
 * Author:            Digitalissimo
 * Author URI:        http://www.digitalissimoweb.it
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       fermopoint-for-woocommerce
 * Domain Path:       /languages
 
 
 Fermo!Point is the Collecting Parcels system that was missing!

Purchase Online and Collect your Parcel at the your most convenient time and location. Do not waste time waiting for the courier and no queues at the post office.

Fermo!Point carefully chooses the best Shopping Partners to work with and the Fermo!Point network of collection points consists of highly qualified shops with friendly and competent people.

Pick Up Points model is one of the fastest trends in eCommerce and integrating Fermo!Point in your online store means offering a First Class service level to your customers.

You can visit the Fermo!Point site at www.fermopoint.it
 
 */

const API_SERVER_SANDBOX = 'http://sandbox.fermopoint.it/api/v1.1/';
const API_SERVER_PRODUCTION = 'http://api.fermopoint.it/api/v1.1/';

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-fermopoint-activator.php
 */
function activate_fermopoint() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-fermopoint-activator.php';
	Fermopoint_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-fermopoint-deactivator.php
 */
function deactivate_fermopoint() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-fermopoint-deactivator.php';
	Fermopoint_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_fermopoint' );
register_deactivation_hook( __FILE__, 'deactivate_fermopoint' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-fermopoint.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_fermopoint() {

	$plugin = new Fermopoint();
	$plugin->run();

}
run_fermopoint();
