<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Zakeke support class for tier-pricing-table-premium
 *
 * @package Zakeke/support
 */
class Zakeke_Support_tier_pricing_table_premium {
	/**
	 * Hook in Zakeke support handlers.
	 */
	public static function init() {
		add_action( 'tiered_pricing_table/cart/product_cart_price', array( __CLASS__, 'price' ), 10, 2 );
		add_action( 'tiered_pricing_table/cart/product_cart_price/item', array( __CLASS__, 'price' ), 10, 2 );

		add_filter( 'tiered_pricing_table/cart/product_cart_regular_price/item', array( __CLASS__, 'price' ), 20, 2 );
		add_filter( 'tiered_pricing_table/cart/product_cart_old_price', array( __CLASS__, 'price' ), 20, 2 );
	}

	/**
	 * Add the Zakeke pricing rules to the price
	 */
	public static function price( $price, $cart_item ) {
		if ( isset( $cart_item['zakeke_data'] ) && false != $price ) {
			$zakeke_data = $cart_item['zakeke_data'];
			$qty         = $cart_item['quantity'];

			$zakeke_price = zakeke_calculate_price(
				$zakeke_data['original_final_price'],
				$zakeke_data['pricing'],
				$qty
			);

			return $price + $zakeke_price;
		}

		return $price;
	}

}

Zakeke_Support_tier_pricing_table_premium::init();