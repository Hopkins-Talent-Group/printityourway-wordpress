<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Zakeke support class for https://wordpress.org/plugins/dynamic-pricing-and-discounts-for-woocommerce-basic-version/
 *
 * @package Zakeke/support
 */
class Zakeke_Support_Dynamic_Pricing_And_Discounts_For_Woocommerce {

	/**
	 * Hook in Zakeke support handlers.
	 */
	public static function init() {
		add_action( 'zakeke_before_ajax_price', array( __CLASS__, 'set_quantity' ), 10, 2 );
	}

	/**
	 * Set the global quantity variable
	 *
	 * @param WC_Product $product
	 * @param int $qty
	 */
	public static function set_quantity( $product, $qty ) {
		if ( array_key_exists( 'xa_cart_quantities', $GLOBALS ) ) {
			global $xa_cart_quantities;
			$xa_cart_quantities[$product->get_id()] = $qty;
		}
	}

}

Zakeke_Support_Dynamic_Pricing_And_Discounts_For_Woocommerce::init();

