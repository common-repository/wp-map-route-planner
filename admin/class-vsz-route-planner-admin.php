<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       www.vsourz.com
 * @since      1.0.0
 *
 * @package    Vsz_route_planner
 * @subpackage Vsz_route_planner/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Vsz_route_planner
 * @subpackage Vsz_route_planner/admin
 * @author     Vsourz Development Team <info@vsourz.com>
 */
class Vsz_route_planner_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function vsz_rutp_enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Vsz_route_planner_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Vsz_route_planner_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_register_style( 'vsz_rutp_admin-css', plugin_dir_url( __FILE__ ) . 'css/vsz-route-planner-admin.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'vsz_rutp_print-css', plugin_dir_url( __FILE__ ) . 'css/vsz-rp-print.css', array(), $this->version, 'all' );
		wp_register_style( 'vsz_rutp_bootstrap-min-css', plugin_dir_url( __FILE__ ) . 'css/bootstrap.min.css', array(), $this->version, 'all' );
		wp_register_style( 'vsz_rutp_bootstrap-datepicker-min-css', plugin_dir_url( __FILE__ ) . 'css/bootstrap-datepicker.min.css', array(), $this->version, 'all' );
		wp_register_style( 'vsz_rutp_magnific-popup-css', plugin_dir_url( __FILE__ ) . 'css/magnific-popup.css', array(), $this->version, 'all' );
		wp_register_style( 'vsz_rutp_font-awesome-css', plugin_dir_url( __FILE__ ) . 'css/font-awesome.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function vsz_rutp_enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Vsz_route_planner_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Vsz_route_planner_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/vsz-route-planner-admin.js', array( 'jquery' ), $this->version, false );
		wp_register_script( 'vsz_rutp_bootstrap-datepicker-min-js', plugin_dir_url( __FILE__ ) . 'js/bootstrap-datepicker.min.js', array( 'jquery' ), $this->version, false );
		wp_register_script( 'vsz_rutp_bootstrap-min-js', plugin_dir_url( __FILE__ ) . 'js/bootstrap.min.js', array( 'jquery' ), $this->version, false );
		wp_register_script( 'vsz_rutp_jquery-magnific-popup-js', plugin_dir_url( __FILE__ ) . 'js/jquery.magnific-popup.js', array( 'jquery' ), $this->version, false );
		wp_register_script( 'vsz_rutp_oms-min-js', plugin_dir_url( __FILE__ ) . 'js/oms.min.js', array( 'jquery' ), $this->version, false );
		wp_register_script( 'vsz_rutp_freezeheader-js', plugin_dir_url( __FILE__ ) . 'js/freezeheader.js', array( 'jquery' ), $this->version, false );
		wp_register_script( 'vsz_rutp_urchin-js', plugin_dir_url( __FILE__ ) . 'js/urchin.js', array( 'jquery' ), $this->version, false );
		wp_register_script( 'vsz_rutp_styledMarker-js', plugin_dir_url( __FILE__ ) . 'js/styledMarker.js', array( 'jquery' ), $this->version, false );
	}

	////////////// to add admin screens for branch , medicine and email templates
	function vsz_rutp_my_plugin_menu(){

		/// Add delivery route planner page
		add_menu_page( "Route Planner", "Route Planner", "manage_options", "delivery_route_planner", "vsz_rutp_delivery_route_planner_callback", 'dashicons-location-alt' , 8);

		//// check for woocommerce exists or not
		if ( class_exists( 'WooCommerce' ) ){
			add_submenu_page("delivery_route_planner", "Woocommerce Orders", "Woocommerce Orders", "manage_options", "rp_woo_orders","vsz_rutp_woo_orders_callback");
		}

		add_submenu_page("delivery_route_planner", "Map Settings", "Map Settings", "manage_options", "rp_setting","vsz_rutp_setting_callback");

		/*
		*** callback function for delivery route planner screen
		*/
		function vsz_rutp_delivery_route_planner_callback(){
			if (!current_user_can('manage_options'))  {
				wp_die( __('You do not have sufficient permissions to access this page.') );
			}
			require_once plugin_dir_path( __FILE__ ) . 'partials/display-delivery-route-planner-form.php';
		}

		function vsz_rutp_setting_callback(){
			if (!current_user_can('manage_options'))  {
				wp_die( __('You do not have sufficient permissions to access this page.') );
			}
			require_once plugin_dir_path( __FILE__ ) . 'partials/setting.php';

		}

		function vsz_rutp_woo_orders_callback(){
			if (!current_user_can('manage_options'))  {
				wp_die( __('You do not have sufficient permissions to access this page.') );
			}
			require_once plugin_dir_path( __FILE__ ) . 'partials/viewWoocommerceOrders.php';
		}
	}

	//Handle get orders listing AJAX request here
	public function vsz_rutp_get_orders_callback(){
		include_once(plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/vsz-rp-woocommerce-get-searched-orders.php');
	}
	
	//Handle get all orders listing AJAX request here
	public function vsz_rutp_get_all_orders_callback(){
		include_once(plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/vsz-rp-woocommerce-get-all-orders.php');
	}

	//Handle get states listing AJAX request here
	public function vsz_rutp_get_states_callback(){
		include_once(plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/vsz-rp-get-states.php');
	}


}
