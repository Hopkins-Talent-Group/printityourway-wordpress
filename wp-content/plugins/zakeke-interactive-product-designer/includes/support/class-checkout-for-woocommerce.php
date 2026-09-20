<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Zakeke support class for https://www.checkoutwc.com
 *
 * @package Zakeke/support
 */
class Zakeke_Support_Checkout_For_Woocommerce {

	/**
	 * Hook in Zakeke support handlers.
	 */
	public static function init() {
		add_action( 'wp_loaded', array( __CLASS__, 'wc_checkout' ), 10, 0 );
	}

	/**
	 * Remove the changed WC add to cart handler when the designed is shown
	 */
	public static function wc_checkout() {
		if ( ! empty( $_REQUEST['zdesign'] ) && 'new' === $_REQUEST['zdesign'] ) {
			remove_action( 'wp', array( 'WC_Form_Handler', 'add_to_cart_action' ), 10 );
		}
	}
}

Zakeke_Support_Checkout_For_Woocommerce::init();

