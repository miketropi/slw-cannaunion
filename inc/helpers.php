<?php 
use SLW\SRC\Helpers\SlwStockAllocationHelper;
/**
 * Helpers 
 */

function slwc_country_code_options() {
  $content = file_get_contents(SLWC_DIR . '/inc/data/CountryCodes.json');
  $country = json_decode($content, true);
  $options = [
    '' => __('— Select —', 'slwc'),
  ];

  foreach($country as $c) {
    $options[$c['code']] = $c['name'] . ' ('. $c['code'] .')';
  }

  return $options;
}

/**
 * Get location by IP
 */
function slwc_get_location_by_ip($ip = '') {
  $endpoint = 'http://ip-api.com/php/';
  $data = unserialize(file_get_contents($endpoint . $ip));
  return $data['status'] === 'success' ? $data : false;
}

function slwc_get_client_ip() {
  $ipaddress = '';
  if (isset($_SERVER['HTTP_CLIENT_IP']))
    $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
  else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
    $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
  else if(isset($_SERVER['HTTP_X_FORWARDED']))
    $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
  else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
    $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
  else if(isset($_SERVER['HTTP_FORWARDED']))
    $ipaddress = $_SERVER['HTTP_FORWARDED'];
  else if(isset($_SERVER['REMOTE_ADDR']))
    $ipaddress = $_SERVER['REMOTE_ADDR'];
  else
    $ipaddress = 'UNKNOWN';
  return $ipaddress;
}

function slwc_get_all_term_product_locations() {
  $terms = get_terms([
    'taxonomy' => 'location',
    'hide_empty' => true,
  ]);

  if(count($terms) > 0) {
    return array_map(function($term) {
      return [
        'term_id' => $term->term_id,
        'name' => $term->name,
        'slug' => $term->slug,
        'country_code' => carbon_get_term_meta($term->term_id, 'slwc_country_code'),
        'access_store' => carbon_get_term_meta($term->term_id, 'slwc_store_access'),
      ];
    }, $terms);
  } else {
    return false;
  }
}

function slwc_find_item_in_cart($product_id = 0) {
  global $woocommerce;
  $cart_items = $woocommerce->cart->get_cart();
  if(count($cart_items) == 0 || $product_id == 0) return false;

  # Product type variation
  $key = array_search($product_id, array_column($cart_items, 'variation_id'));
  
  if($key !== false) {
    return $cart_items[array_keys($cart_items)[$key]];
  }

  # Product type single
  $key = array_search($product_id, array_column($cart_items, 'product_id'));
  if($key !== false) {
    return $cart_items[array_keys($cart_items)[$key]];
  }

  return false;
}

/**
 * Qty validate before add_to_cart or update cart
 * 
 * @param Int $product_id
 * @param Int $qty
 * @param Int $stock_location
 * @param Boolean $replace_qty
 * 
 * @return Boolean true/false
 */
function slwc_qty_validation($product_id = 0, $qty = 1, $stock_location = 0, $replace_qty = false) {
  if(empty($product_id) || empty($qty) || empty($stock_location)) return false;

  # Get all stock location by product
  $product_stock = SlwStockAllocationHelper::getProductStockLocations($product_id);

  # Store selected
  $location_selected = $product_stock[$stock_location];
  
  # Totally qty of store
  $qty_totally_location = (int) $location_selected->quantity;

  # Product exist in cart 
  $produt_in_cart = slwc_find_item_in_cart($product_id);
  
  if($produt_in_cart && $replace_qty == false) {
    $qty += (int) $produt_in_cart['quantity'];
  }

  return ($qty > $qty_totally_location) ? false : true;
}

add_action('init', function() {
  // echo '<pre>';
  // slwc_country_code_options();
  // print_r(slwc_get_location_by_ip(slwc_get_client_ip()));
  // print_r(slwc_get_all_term_product_locations());
  // echo '</pre>';
  // var_dump(SlwStockAllocationHelper::getProductStockLocations(62));
}, 999);