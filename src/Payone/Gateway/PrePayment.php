<?php

namespace Payone\Gateway;

class PrePayment extends GatewayBase {
	const GATEWAY_ID = 'bs_payone_prepayment';

	public function __construct() {
		parent::__construct(self::GATEWAY_ID);

		$this->icon               = '';
		$this->method_title       = 'BS PAYONE Vorkasse';
		$this->method_description = 'method_description';
	}

	public function init_form_fields() {
		$this->init_common_form_fields( __( 'Prepayment', 'payone' ) );
	}

	public function payment_fields() {
		$options = get_option( \Payone\Admin\Option\Account::OPTION_NAME );

		include PAYONE_VIEW_PATH . '/gateway/pre-payment/payment-form.php';
	}

	public function process_payment( $order_id ) {
		global $woocommerce;
		$order = new \WC_Order( $order_id );

		$transaction = new \Payone\Transaction\PrePayment( $this->requestType );
		$response    = $transaction->execute( $order );

		// @todo Fehler abfangen und transaktions-ID in Order ablegen.

		$order->set_transaction_id( $response->get( 'txid' ) );

		// Mark as on-hold (we're awaiting the cheque)
		$order->update_status( 'on-hold', __( 'Überweisung wird abgewartet', 'woocommerce' ) );

		// Reduce stock levels
		wc_reduce_stock_levels( $order_id );

		// Remove cart
		$woocommerce->cart->empty_cart();

		// Return thankyou redirect
		return array(
			'result'   => 'success',
			'redirect' => $this->get_return_url( $order ),
		);
	}
}