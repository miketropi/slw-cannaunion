<?php 
/**
 * Hooks 
 */

function slwc_woo_add_to_cart_validation($true, $product_id, $quantity) {
  // print_r($_POST); die;
  // $product_id = if($_POST['variation_id']) ? $_POST['variation_id'] : $_POST['product_id']; 
  // $product_stock = SlwStockAllocationHelper::getProductStockLocations($product_id);
  // return false;
  return true;
}

add_filter( 'woocommerce_add_to_cart_validation', 'slwc_woo_add_to_cart_validation', 20, 3 );