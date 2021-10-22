<?php 
use SLW\SRC\Helpers\SlwStockAllocationHelper;
/**
 * Hooks 
 */

function slwc_woo_add_to_cart_validation($true, $product_id, $quantity) {
  if(isset($_POST['add-to-cart']) || isset($_POST['variation_id']) || isset($_POST['slw_add_to_cart_item_stock_location'])) {
    # get product single or variation 
    $product_id = ($_POST['variation_id']) ? (int) $_POST['variation_id'] : (int) $_POST['add-to-cart']; 
    
    # Client Qty
    $qty = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 1;

    # Stock location 
    $stock_location = $_POST['slw_add_to_cart_item_stock_location'];

    # validate
    $pass = slwc_qty_validation($product_id, $qty, $stock_location);
    
    if($pass === false) {
      # Get all stock location by product
      $product_stock = SlwStockAllocationHelper::getProductStockLocations($product_id);

      # Store selected
      $location_selected = $product_stock[$stock_location];

      $error_message = sprintf("%s (%s).", __('Your order quantity exceeds the quantity available in store', 'slwc'), "<u>$location_selected->name</u> available $location_selected->quantity in total");
      
      if(!$location_selected->quantity || $location_selected->quantity == 0) {
        $error_message = __('Product out of stock.', 'slwc');
      }
      
      wc_add_notice($error_message, 'error');

      return false;
    }
  } 

  return $true;
}

add_filter('woocommerce_add_to_cart_validation', 'slwc_woo_add_to_cart_validation', 99, 3);

function slwc_woo_add_to_cart_check_product_varialable($true, $product_id, $quantity) {
  $is_available = slwc_check_product_available($product_id);

  if($is_available !== true) {
    $error_message = __('Product unavailable.', 'slwc');
    wc_add_notice($error_message, 'error');
    return false;
  }
  
  return $true;
}

add_filter('woocommerce_add_to_cart_validation', 'slwc_woo_add_to_cart_check_product_varialable', 100, 3);


function slwc_woo_update_cart_validation($true, $cart_item_key, $values, $quantity) {
  // echo '<pre>'; print_r([$true, $cart_item_key, $values, $quantity]); echo '</pre>';
  // die;

  # get product single or variation 
  $product_id = ($values['variation_id']) ? (int) $values['variation_id'] : (int) $values['product_id']; 

  # Update Qty
  $qty = $quantity;

  # Stock location 
  $stock_location = $values['stock_location'];

  # validate
  $pass = slwc_qty_validation($product_id, $qty, $stock_location, true);

  if($pass === false) {
    # Product
    $_product = wc_get_product($product_id); 

    # Get all stock location by product
    $product_stock = SlwStockAllocationHelper::getProductStockLocations($product_id);

    # Store selected
    $location_selected = $product_stock[$stock_location];

    # Error message
    $error_message = sprintf( 
        "%s, %s (%s).", 
        '<a href="'. get_permalink($_product->get_id()) .'" target="_blank">(#'. $_product->get_id() .') '. $_product->get_name() .'</a>', 
        __('your order quantity exceeds the quantity available in store', 'slwc'), 
        "<u>{$location_selected->name}</u> available {$location_selected->quantity} in total"
      );

    wc_add_notice($error_message, 'error');
    return false;
  }

  return $true;
}

add_filter('woocommerce_update_cart_validation', 'slwc_woo_update_cart_validation', 99, 4);

/**
 * Add class stock in location 
 * 
 */
function slwc_product_class_stock_location($post_classes, $class, $product_id) {
  $post_type = get_post_type($product_id); //'product';
  if($post_type !== 'product') return $post_classes;
  $location_terms = wp_get_post_terms($product_id, 'location');
  // var_dump($location_terms);

  if($location_terms && count($location_terms) > 0) {
    foreach($location_terms as $index => $term) {
      $store_access = carbon_get_term_meta($term->term_id, 'slwc_store_access');
      $country_code = carbon_get_term_meta($term->term_id, 'slwc_country_code');
      array_push(
          $post_classes, 
          '__in-location-' . $term->term_id, 
          '__in-location-slug-' . $term->slug, 
          '__in-location-' . $term->term_id . '-access-' . $store_access,
          '__in-location-' . $term->term_id . '-country_code-' . $country_code
        );
    }
  }

  return $post_classes;
}

add_filter('post_class', 'slwc_product_class_stock_location', 20, 3);

/**
 * Add class product available 
 * 
 */
function slwc_product_class_available_by_location($post_classes, $class, $product_id) {
  $post_type = get_post_type($product_id); //'product';
  if($post_type !== 'product') return $post_classes;

  $is_available = slwc_check_product_available($product_id);
  if($is_available == true) {
    array_push($post_classes, '__slwc-product-available');
  } else {
    array_push($post_classes, '__slwc-product-unavailable');
  }
  return $post_classes;
}

add_filter('post_class', 'slwc_product_class_available_by_location', 22, 3);

/**
 * Add class out of stock
 * 
 */
function slwc_product_class_outofstock($post_classes, $class, $product_id) {
  global $product;
  $post_type = get_post_type($product_id); //'product';
  if($post_type !== 'product') return $post_classes;

  if($product->is_type('variable')) {
    return $post_classes;
  }

  $stock_location = SlwStockAllocationHelper::getProductStockLocations($product_id);
  if($stock_location && count($stock_location)) {
    foreach($stock_location as $term_id => $_s) {
      // print_r($_s->quantity);
      array_push($post_classes, '__slwc-qty-' . $term_id .'_' . $_s->quantity);
    }
  }

  return $post_classes;
}

add_filter('post_class', 'slwc_product_class_outofstock', 24, 3);