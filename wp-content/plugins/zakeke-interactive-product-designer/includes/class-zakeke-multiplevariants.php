<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Zakeke_Multiplevariants {

	/**
	 * Setup class.
	 */
	public static function init() {
		add_action( 'wp_loaded', array( __CLASS__, 'add_to_cart_action' ), 20 );
	}

	/**
	 * Get the chosen attribute value from the multiplevariant selection or the initial POST request.
	 *
	 * @param string $attribute_key
	 * @param array $selection
	 *
	 * @return bool|string
	 */
	private static function get_attribute_value( $attribute_key, $selection ) {
		foreach ( $selection['attributes'] as $attribute ) {
			if ( 'attribute_' . $attribute['Id'] === $attribute_key ) {
				return $attribute['Value']['Id'];
			}
		}

		if ( isset( $_REQUEST[ $attribute_key ] ) ) {
			return sanitize_text_field( wp_unslash( $_REQUEST[ $attribute_key ] ) );
		}

		return false;
	}

	/**
	 * Add a specific product variation to the cart.
	 *
	 * @param WC_Product $adding_to_cart
	 * @param array $selection
	 * @param stdClass $zakeke_cart_data
	 * @param int $total_qty
	 *
	 * @return bool|string
	 * @throws Exception
	 */
	public static function add_variant_from_selection( $adding_to_cart, $selection, $zakeke_cart_data, $total_qty ) {
		// Gather posted attributes.
		$posted_attributes = array();

		foreach ( $adding_to_cart->get_attributes() as $attribute ) {
			if ( ! $attribute['is_variation'] ) {
				continue;
			}

			$attribute_key = 'attribute_' . sanitize_title( $attribute['name'] );

			if ( $attribute_key ) {
				if ( $attribute['is_taxonomy'] ) {
					// Don't use wc_clean as it destroys sanitized characters.
					$value = sanitize_title( wp_unslash( self::get_attribute_value( $attribute_key, $selection ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				} else {
					$value = html_entity_decode( wc_clean( wp_unslash( self::get_attribute_value( $attribute_key, $selection ) ) ), ENT_QUOTES, get_bloginfo( 'charset' ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				}

				$posted_attributes[ $attribute_key ] = $value;
			}
		}

		$data_store   = WC_Data_Store::load( 'product' );
		$variation_id = $data_store->find_matching_product_variation( $adding_to_cart, $posted_attributes );

		// Do we have a variation ID?
		if ( empty( $variation_id ) ) {
			throw new Exception( __( 'Please choose product options&hellip;', 'woocommerce' ) );
		}

		// Check the data we have is valid.
		$variation_data = wc_get_product_variation_attributes( $variation_id );

		foreach ( $adding_to_cart->get_attributes() as $attribute ) {
			if ( ! $attribute['is_variation'] ) {
				continue;
			}

			// Get valid value from variation data.
			$attribute_key = 'attribute_' . sanitize_title( $attribute['name'] );
			$valid_value   = isset( $variation_data[ $attribute_key ] ) ? $variation_data[ $attribute_key ] : '';

			/**
			 * If the attribute value was posted, check if it's valid.
			 *
			 * If no attribute was posted, only error if the variation has an 'any' attribute which requires a value.
			 */
			if ( isset( $posted_attributes[ $attribute_key ] ) ) {
				$value = $posted_attributes[ $attribute_key ];

				// Allow if valid or show error.
				if ( $valid_value === $value ) {
					$variations[ $attribute_key ] = $value;
				} elseif ( '' === $valid_value && in_array( $value, $attribute->get_slugs(), true ) ) {
					// If valid values are empty, this is an 'any' variation so get all possible values.
					$variations[ $attribute_key ] = $value;
				} else {
					/* translators: %s: Attribute name. */
					throw new Exception( sprintf( __( 'Invalid value posted for %s', 'woocommerce' ), wc_attribute_label( $attribute['name'] ) ) );
				}
			} elseif ( '' === $valid_value ) {
				$missing_attributes[] = wc_attribute_label( $attribute['name'] );
			}
		}
		if ( ! empty( $missing_attributes ) ) {
			/* translators: %s: Attribute name. */
			throw new Exception( sprintf( _n( '%s is a required field', '%s are required fields', count( $missing_attributes ), 'woocommerce' ), wc_format_list_of_items( $missing_attributes ) ) );
		}

		if (isset($_REQUEST['zdesign'])) {
			$cart_item_data = self::create_cart_item_data( $variation_id, $zakeke_cart_data, sanitize_text_field( wp_unslash( $_REQUEST['zdesign'] ) ), $total_qty );

			return WC()->cart->add_to_cart( $adding_to_cart->get_id(), $selection['quantity'], $variation_id, $variations, $cart_item_data );
		}

		return false;
	}

	/**
	 * Create the Zakeke item data for a specific variation.
	 *
	 * @param int $variation_id
	 * @param stdClass $zakeke_cart_data
	 * @param string $design
	 * @param int $qty
	 *
	 * @return array[]
	 */
	public static function create_cart_item_data( $variation_id, $zakeke_cart_data, $design, $qty ) {
		$product        = wc_get_product( $variation_id );
		$original_price = (float) $product->get_price();

		$zakeke_price = zakeke_calculate_price( $original_price, $zakeke_cart_data->pricing, $qty );

		if ( get_option( 'woocommerce_tax_display_shop' ) === 'excl' ) {
			$zakeke_tax_price = (float) wc_get_price_excluding_tax( $product, array( 'price' => $zakeke_price ) );
		} else {
			$zakeke_tax_price = (float) wc_get_price_including_tax( $product, array( 'price' => $zakeke_price ) );
		}

		$zakeke_excl_tax_price = (float) wc_get_price_excluding_tax( $product, array( 'price' => $zakeke_price ) );

		$original_final_excl_tax_price = (float) wc_get_price_excluding_tax( $product );

		$min_quantity = null;
		if (isset($zakeke_cart_data->min_quantity)) {
			$min_quantity = $zakeke_cart_data->min_quantity;
		}

		$quantity_step = null;
		if (isset($zakeke_cart_data->quantity_step)) {
			$quantity_step = $zakeke_cart_data->quantity_step;
		}

		return array(
			'zakeke_data' => array(
				'design'                        => $design,
				'previews'                      => $zakeke_cart_data->previews,
				'pricing'                       => $zakeke_cart_data->pricing,
				'price'                         => $zakeke_price,
				'price_tax'                     => $zakeke_tax_price,
				'price_excl_tax'                => $zakeke_excl_tax_price,
				'original_final_price'          => $original_price,
				'original_final_excl_tax_price' => $original_final_excl_tax_price,
				'min_quantity'                  => $min_quantity,
				'quantity_step'                 => $quantity_step
			)
		);
	}

	/**
	 * Adds the selected variations to the cart.
	 *
	 * @throws Exception
	 */
	public static function add_to_cart_action() {
		if ( ! isset( $_REQUEST['zakeke_selections'] ) ) {
			return;
		}

		$zakeke_selections = json_decode( wp_unslash( ( $_REQUEST['zakeke_selections'] ) ), true );

		if ( ! is_array( $zakeke_selections ) ) {
			return;
		}

		wc_nocache_headers();

		if ( ! empty( $_REQUEST['product_id'] ) ) {
			$product_id = sanitize_text_field( wp_unslash( $_REQUEST['product_id'] ) );
		} elseif ( ! empty( $_REQUEST['add-to-cart'] ) ) {
			$product_id = sanitize_text_field( wp_unslash( $_REQUEST['add-to-cart'] ) );
		} elseif ( ! empty( $_REQUEST['ztmp_prefix_add-to-cart'] ) ) {
			$product_id = sanitize_text_field( wp_unslash( $_REQUEST['ztmp_prefix_add-to-cart'] ) );
		} else {
			return;
		}

		$product_id     = apply_filters( 'woocommerce_add_to_cart_product_id', absint( wp_unslash( $product_id ) ) );
		$adding_to_cart = wc_get_product( $product_id );

		if ( ! $adding_to_cart ) {
			return;
		}

		$add_to_cart_handler = apply_filters( 'woocommerce_add_to_cart_handler', $adding_to_cart->get_type(), $adding_to_cart );

		if ( 'variable' !== $add_to_cart_handler && 'variation' !== $add_to_cart_handler ) {
			return;
		}

		$total_qty = 0;
		foreach ( $zakeke_selections as $selection ) {
			$total_qty = $total_qty + $selection['quantity'];
		}

		$webservice = new Zakeke_Webservice();
		if ( isset( $_REQUEST['zdesign'] ) ) {
			$zakeke_cart_data = $webservice->cart_info( sanitize_text_field( wp_unslash( $_REQUEST['zdesign'] ) ), $total_qty );

			foreach ( $zakeke_selections as $selection ) {
				self::add_variant_from_selection( $adding_to_cart, $selection, $zakeke_cart_data, $total_qty );
			}
		}
	}
}

Zakeke_Multiplevariants::init();
