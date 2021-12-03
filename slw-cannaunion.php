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

$HTTP_HOST = $_SERVER['HTTP_HOST'];
$https = $_SERVER['HTTPS'] == 'on' ? 'https://':'http://';
$site_url = site_url();

$site_code = str_replace($https.$HTTP_HOST.'/','',$site_url);
$site_code = str_replace($https.$HTTP_HOST,'',$site_code);

{
  /**
   * Define
   */
  define('SLWC_VERSION', '1.0.0');
  define('SLWC_DIR', plugin_dir_path(__FILE__));
  define('SLWC_URI', plugin_dir_url(__FILE__));
  define('SITE_CODE', $site_code);
}

/**
 * Composer
 */
require_once(SLWC_DIR . '/vendor/autoload.php');

/**
 * Init
 */
function initiate_slwc_plugin() {

  if(SITE_CODE != ''){

    require(SLWC_DIR . '/inc/static.php');
    require(SLWC_DIR . '/inc/helpers.php');
    require(SLWC_DIR . '/inc/options.php');

    //Hook validation checkout woo
    add_action( 'woocommerce_after_checkout_validation', 'cannaunion_validate_countries', 10, 2);

  }else {

    if(!class_exists('SlwMain')) {
      function slwc_admin_notice_requirement() {
        ?>
        <div class="notice notice-warning is-dismissible">
          <p><?php _e( 'Install "Stock Locations for WooCommerce" first before use "Stock Locations Woo for Cannaunion", Thank you!', 'slwc' ); ?></p>
        </div>
        <?php
      }
      add_action( 'admin_notices', 'slwc_admin_notice_requirement' );
      return;
    } else {
      /**
       * Includes
       */
      require(SLWC_DIR . '/inc/static.php');
      require(SLWC_DIR . '/inc/helpers.php');
      require(SLWC_DIR . '/inc/hooks.php');
      require(SLWC_DIR . '/inc/ajax.php');
      require(SLWC_DIR . '/inc/options.php');
    }
  }
}

add_action( 'plugins_loaded', 'initiate_slwc_plugin' );

{
  /**
   * Boot
   */
  function slwc_boot() {
    \Carbon_Fields\Carbon_Fields::boot();
  }

  add_action('after_setup_theme', 'slwc_boot');
}
