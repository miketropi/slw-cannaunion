<?php 
/**
 * Static 
 */

/**
 * Enqueue scripts 
 * 
 */
function slwc_enqueue_scripts() {
  wp_enqueue_style('slw-cannaunion', SLWC_URI . '/dist/css/main.slw-cannaunion.css', fasle, SLWC_VERSION);
  wp_enqueue_script('slw-cannaunion', SLWC_URI . '/dist/main.slw-cannaunion.bundle.js', ['jquery'], SLWC_VERSION, true);

  wp_localize_script('slw-cannaunion', 'PHP_DATA', [
    'ajax_url' => admin_url('admin-ajax.php'),
    'lang' => [],
  ]);
}

add_action('wp_enqueue_scripts', 'slwc_enqueue_scripts');