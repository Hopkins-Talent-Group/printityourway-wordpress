<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Zakeke support class for https://aelia.co/shop/currency-switcher-woocommerce/
 *
 * @package Zakeke/support
 */
class Zakeke_Support_Checkout_AeliaCurrency {

	/**
	 * Hook in Zakeke support handlers.
	 */
	public static function init() {
		if(!empty($GLOBALS['woocommerce-aelia-currencyswitcher'])) {
			add_filter( 'zakeke_order_item_total', array( __CLASS__, 'order_item_total' ), 10, 2 );
		}
	}

	/**
	 * Get the order item total in base currency
	 */
	public static function order_item_total($total, $order_item) {
		$base_currency_meta_total = $order_item->get_meta('_line_total_base_currency');
		if (empty($base_currency_meta_total)) {
			return $total;
		}

		return (float) $base_currency_meta_total;
	}
}

Zakeke_Support_Checkout_AeliaCurrency::init();

