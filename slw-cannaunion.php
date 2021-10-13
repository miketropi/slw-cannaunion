<?php 
/**
 * Plugin Name:       		Stock Locations Woo for Cannaunion
 * Description:       		Custom plugin for Cannaunion
 * Version:					      1.0.0
 * Requires at least: 		4.9
 * Requires PHP:      		7.2
 * Author:            		Beplus
 * Author URI:        		#
 * License:           		GPL v2 or later
 * License URI:       		https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       		slwc
 * Domain Path:       		/languages
 * WC requires at least:	3.4
 * WC 
 */

{
  /**
   * Define 
   */
  define('SLWC_VERSION', '1.0.0');
  define('SLWC_DIR', plugin_dir_path(__FILE__));
  define('SLWC_URI', plugin_dir_url(__FILE__));
}

{
  require_once(SLWC_DIR . '/vendor/autoload.php');

  /**
   * Includes 
   */
  require(SLWC_DIR . '/inc/static.php');
  require(SLWC_DIR . '/inc/helpers.php');
  require(SLWC_DIR . '/inc/hooks.php');
  require(SLWC_DIR . '/inc/ajax.php');
}

{
  function slwc_boot() {
    \Carbon_Fields\Carbon_Fields::boot();
  }
  
  add_action('after_setup_theme', 'slwc_boot');
}
