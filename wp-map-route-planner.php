<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * @wordpress-plugin
 * Plugin Name:       WP Map Route Planner
 * Plugin URI:        https://wordpress.org/plugins/wp-map-route-planner/
 * Description:       Allow users to draw custom routes and generate a catalogue of map routes and trails with points of interest on a WordPress site using Google Maps integration.
 * Version:           1.0.0
 * Author:            Vsourz Digital <mehul@vsourz.com>
 * Author URI:        https://www.vsourz.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       vsz-route-planner
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-vsz_route_planner-activator.php
 */
function activate_vsz_route_planner() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-vsz-route-planner-activator.php';
	Vsz_route_planner_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-vsz_route_planner-deactivator.php
 */
function deactivate_vsz_route_planner() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-vsz-route-planner-deactivator.php';
	Vsz_route_planner_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_vsz_route_planner' );
register_deactivation_hook( __FILE__, 'deactivate_vsz_route_planner' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-vsz-route-planner.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_vsz_route_planner() {

	$plugin = new Vsz_route_planner();
	$plugin->run();

}
run_vsz_route_planner();
