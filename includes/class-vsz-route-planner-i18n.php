<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       www.vsourz.com
 * @since      1.0.0
 *
 * @package    Vsz_route_planner
 * @subpackage Vsz_route_planner/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Vsz_route_planner
 * @subpackage Vsz_route_planner/includes
 * @author     Vsourz Development Team <info@vsourz.com>
 */
class Vsz_route_planner_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function vsz_rutp_load_plugin_textdomain() {

		load_plugin_textdomain(
			'vsz_route_planner',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
