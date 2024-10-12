<?php
/**
 * Plugin Name: Auto Apply Coupon Code
 * Description: Copy coupon code link and auto apply from url also show popup comparison of coupon code which give maximum discount
 * Version: 1.0.0
 * Text Domain: auto-apply-coupon
 * Author: msanjay23
 * Author URI: https://profiles.wordpress.org/msanjay23/
 * License: GPLv3
 * 
 * @package 
 * @category Core 
 * @author 
 */

// Exit if accessed directly 
if( !defined( 'ABSPATH' ) ) exit; 

/**
 * Basic plugin definitions 
 * 
 * @package 
 * @since 1.0.0
 */
if( !defined( 'AUTO_APPLY_COUPON_VERSION' ) ) {
	define( 'AUTO_APPLY_COUPON_VERSION', '1.0.0' ); // version of plugin
}
if( !defined( 'AUTO_APPLY_COUPON_DIR' ) ) {
	define( 'AUTO_APPLY_COUPON_DIR', dirname(__FILE__) ); // plugin dir
}
if( !defined( 'AUTO_APPLY_COUPON_PLUGIN_BASENAME' ) ) {
	define( 'AUTO_APPLY_COUPON_PLUGIN_BASENAME', basename( AUTO_APPLY_COUPON_DIR ) ); //Plugin base name
}
if( !defined( 'AUTO_APPLY_COUPON_URL' ) ) {
	define( 'AUTO_APPLY_COUPON_URL', plugin_dir_url(__FILE__) ); // plugin url
}
if( !defined( 'AUTO_APPLY_COUPON_INCLUDE_DIR' ) ) {
	define( 'AUTO_APPLY_COUPON_INCLUDE_DIR', AUTO_APPLY_COUPON_DIR . '/includes/' ); 
}
if( !defined( 'AUTO_APPLY_COUPON_INCLUDE_URL' ) ) {
	define( 'AUTO_APPLY_COUPON_INCLUDE_URL', AUTO_APPLY_COUPON_URL . 'includes/' ); // plugin include url
}
if( !defined( 'AUTO_APPLY_COUPON_ADMIN_DIR' ) ) {
	define( 'AUTO_APPLY_COUPON_ADMIN_DIR', AUTO_APPLY_COUPON_DIR . '/includes/admin' ); // plugin admin dir 
}

/**
 * Load Text Domain
 * 
 * This gets the plugin ready for translation.
 */
function auto_apply_coupon_load_textdomain() {
	
	// Set filter for plugin's languages directory
	$lang_dir	= dirname( plugin_basename( __FILE__ ) ) . '/languages/';
	$lang_dir	= apply_filters( 'auto_apply_coupon_languages_directory', $lang_dir );
	
	// Traditional WordPress plugin locale filter
	$locale	= apply_filters( 'plugin_locale',  get_locale(), 'auto-apply-coupon' );
	$mofile	= sprintf( '%1$s-%2$s.mo', 'auto-apply-coupon', $locale );
	
	// Setup paths to current locale file
	$mofile_local	= $lang_dir . $mofile;
	$mofile_global	= WP_LANG_DIR . '/' . AUTO_APPLY_COUPON_PLUGIN_BASENAME . '/' . $mofile;
	
	if ( file_exists( $mofile_global ) ) { // Look in global /wp-content/languages/auto-apply-coupon folder
		load_textdomain( 'auto-apply-coupon', $mofile_global );
	} elseif ( file_exists( $mofile_local ) ) { // Look in local /wp-content/plugins/auto-apply-coupon/languages/ folder
		load_textdomain( 'auto-apply-coupon', $mofile_local );
	} else { // Load the default language files
		load_plugin_textdomain( 'auto-apply-coupon', false, $lang_dir );
	}	
}

/**
 * Load Plugin
 */
function auto_apply_coupon_plugin_loaded() {
	auto_apply_coupon_load_textdomain();
}
add_action( 'plugins_loaded', 'auto_apply_coupon_plugin_loaded' );


/**
 * Declaration of global variable
 */ 
global $auto_apply_coupon;

include_once( AUTO_APPLY_COUPON_INCLUDE_DIR . '/class-auto-apply-coupon.php' );
$auto_apply_coupon = new Auto_Apply_Coupon();

include_once( AUTO_APPLY_COUPON_ADMIN_DIR . '/class-auto-apply-coupon-admin.php' );
$auto_apply_coupon_admin = new Auto_Apply_Coupon_Admin();

