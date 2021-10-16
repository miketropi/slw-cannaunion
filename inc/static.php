<?php 
/**
 * Static 
 */

/**
 * Enqueue scripts 
 * 
 */
function slwc_enqueue_scripts() {
  wp_enqueue_style('slw-cannaunion-style', SLWC_URI . '/dist/css/main.slw-cannaunion.css', false, SLWC_VERSION);
  wp_enqueue_script('slw-cannaunion-script', SLWC_URI . '/dist/main.slw-cannaunion.bundle.js', ['jquery'], SLWC_VERSION, true);

  $client_ip = slwc_get_client_ip(); // '14.224.130.20';
  // $client_ip = '14.224.130.20';
  wp_localize_script('slw-cannaunion-script', 'PHP_DATA', [
    'ajax_url' => admin_url('admin-ajax.php'),
    'user_location' => slwc_get_location_by_ip($client_ip),
    'product_locations' => slwc_get_all_term_product_locations(),
    'lang' => [],
  ]);
}

add_action('wp_enqueue_scripts', 'slwc_enqueue_scripts');