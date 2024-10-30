<?php

// add_action( 'woocommerce_after_checkout_validation', 'misha_validate_fields', 10, 2);
/*function misha_validate_fields($fields, $errors)
{


	$fields_string = "<pre>";
	$fields_string .= print_r($fields, true);
	$fields_string .= "</pre>";

	$errors->add('validation', $fields_string);

	if (preg_match('/\\d/', $fields['billing_first_name']) || preg_match('/\\d/', $fields['billing_last_name'])) {
		$errors->add('validation', 'Teste. Test. Really?');
	}

	// die();

}*/

// VALIDATION BEFORE FINISHING ORDER
add_action('woocommerce_after_checkout_validation', 'validate_checkout', 10, 2);
function validate_checkout($data, $errors)
{

	global $woocommerce;

	// CHECK VALIDATION ACCORDING TO PLUGIN CONFIG
	$pluginName = WPCP_API_NAME;
    $options = get_option($pluginName);
	$checkout_stock = $options['check_checkout_stock'];
	$software = $options['software'];
	
	if ( $checkout_stock ) {

		$functions = new Wpcpapi_Functions();

		$items = $woocommerce->cart->get_cart();
		foreach ($items as $item => $value) {

			$product = wc_get_product($value['data']->get_id());
			$product_id = $product->get_id();

			$product_name = $product->get_title();
			$order_product_quantity = $value['quantity'];

			// $errors->add('validation', 'Quantity: ' . $order_product_quantity );

			// CHECK STOCK
			// $ws_product_stock = $functions->getProductStockById($product_id);
			$ws_product_stock = $functions->getProductStockFromWs($software, $product_id);

			// CHANGE VALIDATION ACCORDING TO PRODUCT AND CONFIG
			// get_manage_stock
			if ( $product->managing_stock() ) {

				// IF ORDERED STOCK LESS THAN STOCK AVAILABLE
				if ($ws_product_stock < $order_product_quantity) {
					$error_text = $product_name . ' - Stock indisponivel.';
					$errors->add('validation', $error_text);
				}
			}
		}

	}

	// $errors->add('validation', 'Force error to validate all.');
}

// AFTER ORDER COMPLETE
/*
	woocommerce_order_status_pending
	woocommerce_order_status_failed
	woocommerce_order_status_on-hold
	woocommerce_order_status_processing
	woocommerce_order_status_completed
	woocommerce_order_status_refunded
	woocommerce_order_status_cancelled
*/
// add_action( 'woocommerce_order_status_pending', 'woocommerce_order_finished', 10, 1 );
function woocommerce_order_finished($order_id)
{

	$order = wc_get_order($order_id);
	$order_data = $order->get_data();

	// CHECK STOCK 
	$plugin = new Wpcpapi_Functions();

	foreach ($order->get_items() as $item_key => $item_values) {

		$product_id = $item_values->get_product_id();

		$item_data = $item_values->get_data();
		$quantity = $item_values->get_quantity();
	}

	// FUNCTION TO CHECK STOCK

}
