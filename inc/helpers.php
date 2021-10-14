<?php 
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

add_action('init', function() {
  // echo '<pre>';
  // slwc_country_code_options();
  // print_r(slwc_get_location_by_ip(slwc_get_client_ip()));
  // print_r(slwc_get_all_term_product_locations());
  // echo '</pre>';
});