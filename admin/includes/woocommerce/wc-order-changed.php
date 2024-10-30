<?php

// add_action( 'woocommerce_order_status_changed', 'wcpcapi_on_order_status_change', 10, 3 );
// add_action( 'woocommerce_order_status_processing', 'wcpcapi_on_order_status_change' );
// add_action('woocommerce_order_status_completed', 'wcpcapi_on_order_status_change');
// add_action( 'woocommerce_order_status_pending', 'wcpcapi_on_order_status_change' );
// add_action( 'woocommerce_order_status_failed', 'wcpcapi_on_order_status_change' );
// add_action( 'woocommerce_order_status_on-hold', 'wcpcapi_on_order_status_change' );
// add_action( 'woocommerce_order_status_refunded', 'wcpcapi_on_order_status_change' );
// add_action( 'woocommerce_order_status_cancelled', 'wcpcapi_on_order_status_change' );

add_action('woocommerce_order_status_changed', 'wcpcapi_on_order_status_change', 10, 3);
function wcpcapi_on_order_status_change($order_id, $status_from, $status_to)
{
    $hestia = new Wpcpapi_Functions();

    return $hestia->onOrderChange($order_id, $status_from, $status_to);

}


// Envia Factura e gera link no email para download.
add_action('woocommerce_email_before_order_table', 'wpcpapi_add_to_order_email', 10, 10);
function wpcpapi_add_to_order_email($order, $sent_to_admin)
{

	$order_id = $order->get_id();

	$functions = new Wpcpapi_Functions();
	$functions->createInvoice($order_id);

	/*
	if (!empty(WPCP_API_ORDER_STATUS_CHANGE) == 'completed') {
		if ($status_to == WPCP_API_ORDER_STATUS_CHANGE) {
			$functions->onOrderStatusChanged($order_id);
		}
	}
	*/
}