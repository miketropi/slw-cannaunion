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
  // $endpoint = 'http://ip-api.com/php/';
  $api_mockup = "https://pro.ip-api.com/php/{YOUR_API}?key=KNUyyUdvyg4pq49";
  $endpoint = str_replace('{YOUR_API}', $ip, $api_mockup);
  $data = @unserialize(file_get_contents($endpoint));
  return $data['status'] === 'success' ? $data : false;
}

function slwc_get_client_ip() {
  // return '100.42.240.4'; # canada
  // return  '14.224.130.20'; # vn

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
  return explode(',', $ipaddress)[0];
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

function slwc_check_client_location_in_shop_location() {
  $user_location = slwc_get_location_by_ip(slwc_get_client_ip());
  $locations = slwc_get_all_term_product_locations();

  $key = array_search($user_location['countryCode'], array_column($locations, 'country_code'));
  if($key === false) {
    return false;
  } else {
    return $locations[$key];
  }
}

function slwc_check_product_available($product_ip) {
  $client_location = slwc_check_client_location_in_shop_location();
  $product_location = SlwStockAllocationHelper::getProductStockLocations($product_ip);
  if($client_location) {
    if(!$product_location) {
      return false;
    }

    if(in_array($client_location['term_id'], array_keys($product_location))) {
      return true;
    } else {
      return false;
    }
  } else {
    if(count($product_location) == 0) return false;

    foreach($product_location as $index => $term) {
      $store_access = carbon_get_term_meta($term->term_id, 'slwc_store_access');
      if($store_access == 'global') {
        return true;
      }
    }
  }

  return false;
}

add_action('init', function() {
  // echo slwc_get_client_ip();
  // echo '<pre>';
  // slwc_country_code_options();
  // print_r(slwc_get_location_by_ip(slwc_get_client_ip()));
  // print_r(slwc_get_all_term_product_locations());
  // echo '</pre>';

  // $location = SlwStockAllocationHelper::getProductStockLocations(60);
  // echo '<pre>'; print_r($location); echo '</pre>';
  // $l_ids = array_map(function($l) {
  //   return $l->term_id;
  // }, $location);
  // print_r(array_keys($location));

  // slwc_check_client_location_in_shop_location();
}, 999);

//get site code
function get_site_code(){
  $HTTP_HOST = $_SERVER['HTTP_HOST'];
  $https = $_SERVER['HTTPS'] == 'on' ? 'https://':'http://';
  $site_url = site_url();

  $site_code = str_replace($https.$HTTP_HOST.'/','',$site_url);
  $site_code = str_replace($https.$HTTP_HOST,'',$site_code);

  return $site_code;
}

function cannaunion_validate_countries( $fields, $errors ){
    if (isset($fields['billing_country']) && $fields['billing_country'] && !$fields['ship_to_different_address']){
        $SITE_CODE = get_site_code();
        if($SITE_CODE == 'uk' && $fields['billing_country'] != "GB"){
          $errors->add( 'validation', carbon_get_theme_option('slwc_message_warning_checkout_page'));
        }
        if($SITE_CODE == 'eu' && $fields['billing_country'] == "GB"){
          $errors->add( 'validation', carbon_get_theme_option('slwc_message_warning_checkout_page'));
        }
    }
    return $fields;
}
