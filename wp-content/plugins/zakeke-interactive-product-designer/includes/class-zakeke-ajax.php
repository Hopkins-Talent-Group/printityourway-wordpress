<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Zakeke_AJAX {

	/**
	 * Hook in ajax handlers.
	 */
	public static function init() {
		add_action( 'wc_ajax_zakeke_get_auth', array( __CLASS__, 'get_auth' ) );
		add_action( 'wc_ajax_zakeke_get_attributes', array( __CLASS__, 'get_attributes' ) );
		add_action( 'wc_ajax_zakeke_get_price', array( __CLASS__, 'get_price' ) );
		add_action( 'wc_ajax_zakeke_get_configurator_price', array( __CLASS__, 'get_configurator_price' ) );
		add_action( 'wc_ajax_zakeke_share', array( __CLASS__, 'share' ) );
		add_action( 'wc_ajax_zakeke_update_cart', array( __CLASS__, 'update_cart' ) );
	}

	/**
	 * Get a client-to-server authentication token.
	 */
	public static function get_auth() {
		ob_start();

		$auth = zakeke_get_auth();

		$user_id = get_current_user_id();
		if ( $user_id > 0 ) {
			$auth->set_customer( (string) $user_id );
		} else {
			$auth->set_guest( zakeke_guest_code() );
		}

		try {
			wp_send_json( array(
				'token' => $auth->get_auth_token(),
				'type'  => $auth->get_token_type()
			) );
		} catch ( Exception $e ) {
			wc_add_notice( 'We can\'t customize this product right now.', 'error' );
			wp_send_json( array(
				'error' => $e->getMessage()
			) );
		}
	}

	/**
	 * Get a matching variation price based on posted attributes.
	 */
	public static function get_attributes() {
		ob_start();

		if ( ! empty( $_REQUEST['product_id'] ) ) {
			$product_id = sanitize_text_field( wp_unslash( $_REQUEST['product_id'] ) );
		} elseif ( ! empty( $_REQUEST['add-to-cart'] ) ) {
			$product_id = sanitize_text_field( wp_unslash( $_REQUEST['add-to-cart'] ) );
		} elseif ( ! empty( $_REQUEST['ztmp_prefix_add-to-cart'] ) ) {
			$product_id = sanitize_text_field( wp_unslash( $_REQUEST['ztmp_prefix_add-to-cart'] ) );
		} else {
			return;
		}

		$product = wc_get_product( absint( $product_id ) );
		if ( ! $product ) {
			die();
		}

		$attributes = array();
		$variations = array();
		if ( $product->is_type( 'variable' ) ) {
			foreach ( $product->get_variation_attributes() as $key => $values ) {
				$attribute_values = array();
				$attribute_id = '';

				if ( taxonomy_exists( $key ) ) {
					// Handle global attributes (taxonomy-based) - use URL encoding
					$attribute_id = preg_replace_callback( '/%[0-9A-F]{2}/', function ( array $matches ) {
						return strtolower( $matches[0] );
					}, rawurlencode( $key ) );

					$terms = wc_get_product_terms(
						$product->get_id(),
						$key,
						array(
							'fields' => 'all',
						)
					);

					foreach ( $terms as $term ) {
						if ( in_array( $term->slug, $values, true ) ) {
							$attribute_values[] = array(
								'id'    => $term->slug,
								'label' => apply_filters( 'woocommerce_variation_option_name', $term->name, $term, $key, $product )
							);
						}
					}
				} else {
					// Handle non-global attributes (custom product attributes) - use sanitize_title
					$attribute_id = sanitize_title( $key );

					foreach ( $values as $value ) {
						if ( ! empty( $value ) ) {
							$attribute_values[] = array(
								'id'    => $value,
								'label' => apply_filters( 'woocommerce_variation_option_name', $value, null, $key, $product )
							);
						}
					}
				}

				$attributes[] = array(
					'id'     => $attribute_id,
					'label'  => wc_attribute_label( $key, $product ),
					'values' => $attribute_values
				);
			}

			foreach ( $product->get_available_variations() as $key => $variation ) {
				$variation_attributes = array();
				foreach ( $variation['attributes'] as $attribute_key => $attribute_value ) {
					$variation_attributes[] = array(
						'Id'    => str_replace( 'attribute_', '', $attribute_key ),
						'Value' => array(
							'Id' => $attribute_value
						)
					);
				}

				$variations[] = $variation_attributes;
			}
		}

		wp_send_json( array(
				'attributes' => $attributes,
				'variants'   => $variations,
			)
		);
	}

	/**
	 * Get a matching variation price based on posted attributes.
	 */
	public static function get_price() {
		ob_start();

		if ( ! empty( $_REQUEST['product_id'] ) ) {
			$product_id = sanitize_text_field( wp_unslash( $_REQUEST['product_id'] ) );
		} elseif ( ! empty( $_REQUEST['add-to-cart'] ) ) {
			$product_id = sanitize_text_field( wp_unslash( $_REQUEST['add-to-cart'] ) );
		} elseif ( ! empty( $_REQUEST['ztmp_prefix_add-to-cart'] ) ) {
			$product_id = sanitize_text_field( wp_unslash( $_REQUEST['ztmp_prefix_add-to-cart'] ) );
		} else {
			return;
		}

		$qty = 1;
		if ( isset( $_REQUEST['quantity'] ) ) {
			// Sanitize
			$qty = wc_stock_amount( preg_replace( '/[^0-9\.]/', '', sanitize_text_field( wp_unslash( $_REQUEST['quantity'] ) ) ) );
			if ( $qty <= 0 ) {
				$qty = 1;
			}
		}

		$product = wc_get_product( absint( $product_id ) );
		if ( ! $product ) {
			die();
		}

		if ( $product->is_type( 'variable' ) ) {
			/**
			 * Get the product information.
			 *
			 * @var WC_Product_Data_Store_Interface $data_store
			 */
			$data_store   = WC_Data_Store::load( 'product' );
			$variation_id = $data_store->find_matching_product_variation( $product, wp_unslash( $_REQUEST ) );
			if ( $variation_id ) {
				$product = wc_get_product( $variation_id );
			}
		}

		do_action( 'zakeke_before_ajax_price', $product, $qty );

		$integration = new Zakeke_Integration();
		$hide_price  = $integration->hide_price;

		$original_price     = 0.0;
		$zakeke_final_price = 0.0;

		if ( 'yes' !== $hide_price ) {
			$zakeke_price   = 0.0;
			$original_price = $product->get_price() * $qty;

			if ( isset( $_REQUEST['zakeke-percent-price'] ) ) {
				$zakeke_percent_price = (float) sanitize_text_field( wp_unslash( $_REQUEST['zakeke-percent-price'] ) );
				if ( $zakeke_percent_price > 0.0 ) {
					$zakeke_price += $original_price * ( $zakeke_percent_price / 100 );
				}
			}
			if ( isset( $_REQUEST['zakeke-price'] ) ) {
				$zakeke_price += (float) sanitize_text_field( wp_unslash( $_REQUEST['zakeke-price'] ) );
			}
			if ( isset( $_REQUEST['zakeke-conditions'] ) ) {
				$zakekePerc = 0.0;
				$zakeke_price_conditions = json_decode( sanitize_text_field( wp_unslash( ( $_REQUEST['zakeke-conditions'] ) ) ), true );
				foreach ($zakeke_price_conditions as $zakeke_price_condition) {
					if (1 === $zakeke_price_condition['priceType']) {
						$zakekePerc += $zakeke_price_condition['priceToAdd'];
					}
				}

				if (0.0 !== $zakekePerc) {
					$zakeke_price += $original_price * ( (float) $zakekePerc / 100 );
				}
			}

			$zakeke_final_price = $zakeke_price;
		}

		$response = array(
			'is_purchasable'      => $product->is_purchasable(),
			'is_in_stock'         => $product->is_in_stock(),
			'price_including_tax' => (float) zakeke_wc_get_price_to_display( $product, array( 'price' => $original_price + $zakeke_final_price ) )
		);

		// Apply filter to allow modification of the price endpoint response
		$response = apply_filters( 'zakeke_get_price_response', $response, $product, $qty );

		wp_send_json( $response );
	}

	/**
	 * Get a matching variation price based on posted attributes.
	 */
	public static function get_configurator_price() {
		ob_start();

		if ( ! empty( $_REQUEST['product_id'] ) ) {
			$product_id = sanitize_text_field( wp_unslash( $_REQUEST['product_id'] ) );
		} elseif ( ! empty( $_REQUEST['add-to-cart'] ) ) {
			$product_id = sanitize_text_field( wp_unslash( $_REQUEST['add-to-cart'] ) );
		} else {
			return;
		}

		$qty = 1;
		if ( ! empty( $_REQUEST['quantity'] ) ) {
			// Sanitize
			$qty = wc_stock_amount( preg_replace( '/[^0-9\.]/', '', sanitize_text_field( wp_unslash( $_REQUEST['quantity'] ) ) ) );
			if ( $qty <= 0 ) {
				$qty = 1;
			}
		}

		$product = wc_get_product( absint( $product_id ) );
		if ( ! $product ) {
			die();
		}

		if ( $product->is_type( 'variable' ) ) {
			/**
			 * Get the product information.
			 *
			 * @var WC_Product_Data_Store_Interface $data_store
			 */
			$data_store   = WC_Data_Store::load( 'product' );
			$variation_id = $data_store->find_matching_product_variation( $product, wp_unslash( $_REQUEST ) );
			if ( $variation_id ) {
				$product = wc_get_product( $variation_id );
			}
		}

		do_action( 'zakeke_before_ajax_configurator_price', $product, $qty );

		$integration = new Zakeke_Integration();
		$hide_price  = $integration->hide_price;

		$original_price     = 0.0;
		$zakeke_final_price = 0.0;

		if ( 'yes' !== $hide_price ) {
			$original_price = (float) wc_get_price_to_display( $product );

			if ( isset( $_REQUEST['zakeke_price'] ) ) {
				$zakeke_price = (float) sanitize_text_field( wp_unslash( $_REQUEST['zakeke_price'] ) );
				$zakeke_final_price = (float) wc_get_price_to_display( $product, array( 'price' => $zakeke_price ) );
			}
		}

		wp_send_json( array(
			'is_purchasable'      => $product->is_purchasable(),
			'is_in_stock'         => $product->is_in_stock(),
			'price_including_tax' => $original_price + $zakeke_final_price
		) );
	}

	public static function share() {
		ob_start();

		if ( ! isset( $_REQUEST['path'] ) ) {
			wp_send_json( array(
				'error' => __( 'Missing path parameter' )
			) );
		}

		$path     = sanitize_text_field( wp_unslash( $_REQUEST['path'] ) );
		$response = zakeke_retry( function () use ( $path ) {
			$response = wp_remote_request( ZAKEKE_ADMIN_URL . '/SharedPreview' . $path );
			if ( is_wp_error( $response ) ) {
				throw new Exception( 'Zakeke_AJAX::share ' . $path );
			} elseif ( is_array( $response )
			           && $response['response']['code']
			           && floor( $response['response']['code'] ) / 100 >= 4 ) {
				if ( 404 === $response['response']['code'] ) {
					status_header( 404 );
					nocache_headers();
					include get_query_template( '404' );
					die();
				}

				throw new Exception( 'Zakeke_AJAX::share ' . $path );
			}

			return $response;
		} );

		header( 'Content-type: image/png' );
		ob_clean();
		echo wp_remote_retrieve_body( $response );
		die();
	}

	public static function update_cart() {
		ob_start();

		$webservice  = new Zakeke_Webservice();
		$integration = new Zakeke_Integration();

		foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
			if ( ! isset( $values['zakeke_data'] ) ) {
				continue;
			}

			$zakeke_data = $values['zakeke_data'];

			$cart_item_data = &WC()->cart->cart_contents[ $cart_item_key ];

			$qty = $cart_item_data['quantity'];
			if ( $qty <= 0 ) {
				$qty = 1;
			}

			$zakeke_cart_data = $webservice->cart_info( $zakeke_data['design'], $qty );

			$cart_item_data['zakeke_data']['previews'] = $zakeke_cart_data->previews;

			if ( 'yes' !== $integration->hide_price ) {
				$cart_item_data['zakeke_data']['pricing'] = $zakeke_cart_data->pricing;

				$original_price = $zakeke_data['original_final_price'];

				$zakeke_price = zakeke_calculate_price(
					$original_price,
					$cart_item_data['zakeke_data']['pricing'],
					$qty
				);

				/**
				 * Get the product information.
				 *
				 * @var WC_Product $product
				 */
				$product = $values['data'];

				if ( get_option( 'woocommerce_tax_display_shop' ) === 'excl' ) {
					$zakeke_tax_price = (float) zakeke_wc_get_price_excluding_tax( $product, array( 'price' => $zakeke_price ) );
				} else {
					$zakeke_tax_price = (float) zakeke_wc_get_price_including_tax( $product, array( 'price' => $zakeke_price ) );
				}

				$zakeke_excl_tax_price = (float) zakeke_wc_get_price_excluding_tax( $product, array( 'price' => $zakeke_price ) );

				$cart_item_data['zakeke_data']['price']          = $zakeke_price;
				$cart_item_data['zakeke_data']['price_tax']      = $zakeke_tax_price;
				$cart_item_data['zakeke_data']['price_excl_tax'] = $zakeke_excl_tax_price;

				$product->set_price( $original_price + $zakeke_price );
			}

			WC()->cart->set_session();
		}

		wp_send_json( array(
			'return_url' => zakeke_return_url()
		) );
	}
}

Zakeke_AJAX::init();
