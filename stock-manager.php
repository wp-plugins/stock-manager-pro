<?php 
/**
 * Plugin Name: Stock Manager Pro
 * Plugin URI: http://www.marceldehoog.eu/stockmanagerpro
 * Description: View and adapt stock of your products in WooCommerce.
 * Version: 1.1
 * Author: Marcel de Hoog
 * Author URI: http://www.marceldehoog.eu
 * Text Domain: stockmanager
 */

$app_version = 'v1.1';
add_action( 'admin_menu', 'stock_report_menu' );
add_action( 'plugins_loaded', 'stockmanager_load_textdomain' );
add_action( 'admin_enqueue_scripts', 'stockmanager_load_scripts' );
add_action( 'admin_init', 'stockmanager_options_init' );
register_activation_hook( __FILE__, 'stockmanager_defaults' );
register_activation_hook( __FILE__, 'db_install' );

//	initialize options fields
function stockmanager_options_init() {

	register_setting( 'plugin_options_smp', 'plugin_options_smp' );

	add_settings_section( 'report_section', __( 'Report settings', 'stockmanager' ), 'section_report_text_fn', 'stock_manager' );
	add_settings_field( 'radio_buttons_1', __( 'Display variation columns', 'stockmanager' ), 'setting_radio_1_fn', 'stock_manager', 'report_section' );
	add_settings_field( 'radio_buttons_2', __( 'Low / no stock rows different color', 'stockmanager' ), 'setting_radio_2_fn', 'stock_manager', 'report_section' );
	add_settings_field( 'dropdown_3', __( 'Default number of rows', 'stockmanager' ), 'setting_dropdown_3_fn', 'stock_manager', 'report_section' );

	add_settings_section( 'history_section', __( 'History settings', 'stockmanager' ), 'section_history_text_fn', 'stock_manager' );
	add_settings_field( 'dropdown_4', __( 'Date format', 'stockmanager' ), 'setting_dropdown_4_fn', 'stock_manager', 'history_section' );
	add_settings_field( 'radio_buttons_5', __( 'Time format', 'stockmanager' ), 'setting_radio_5_fn', 'stock_manager', 'history_section' );
	add_settings_field( 'dropdown_6', __( 'Default number of rows', 'stockmanager' ), 'setting_dropdown_6_fn', 'stock_manager', 'history_section' );
}

//	Default options set
function stockmanager_defaults() {

	$tmp = get_option( 'plugin_options_smp' );

    if( ! is_array( $tmp ) ) {
		$arr = array( "radio_buttons_1"=>"No", "radio_buttons_2"=>"No", "dropdown_3"=>"10", "dropdown_4"=>"Y-m-d", "radio_buttons_5"=>"24hr", "dropdown_6"=>"10" );
		update_option( 'plugin_options_smp', $arr );
	}
}

//	Load scripts and css
function stockmanager_load_scripts() {

	wp_register_script( 'datatables', plugins_url( '/js/jquery.dataTables.min.js', __FILE__ ) );
	wp_register_style( 'stockmanager_css', plugins_url( '/css/stock-manager.css', __FILE__ ) );
	wp_enqueue_script( "jquery" );
	wp_enqueue_script( "datatables" );
	wp_enqueue_style( "stockmanager_css" ); 
}

//	Load language domain  
function stockmanager_load_textdomain() {
	load_plugin_textdomain( 'stockmanager', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
} 

//	Add submenu to woocommerce 
function stock_report_menu() {
	add_submenu_page( 'woocommerce', 'Stock Manager Pro', 'Stock Manager Pro', 'manage_options', 'stock-manager', 'get_stock_report' );
}

//	Get the report
function get_stock_report() {
    if ( ! current_user_can( 'manage_options' ) )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
    include __DIR__ . "/includes/stockmanager-main.php";
} 

// Install or update the database for the history
global $stockmanager_db_version;
$stockmanager_db_version = "1.0";

function db_install() {

   global $wpdb;
   global $stockmanager_db_version;

   $table_name = $wpdb->prefix . "stockmanager";

   $sql = "CREATE TABLE $table_name (
   id mediumint(9) NOT NULL AUTO_INCREMENT,
   time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
   comments VARCHAR(255) DEFAULT '' NOT NULL,
   mutations text NOT NULL,
   oldstock text NOT NULL,
   UNIQUE KEY id (id)
   );";

   require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
   dbDelta( $sql );
   add_option( "stockmanager_db_version", $stockmanager_db_version );
}

?>
