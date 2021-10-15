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
      wc_add_notice($error_message, 'error');

      return false;
    }
  }

  return $true;
}

add_filter('woocommerce_add_to_cart_validation', 'slwc_woo_add_to_cart_validation', 99, 3);

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