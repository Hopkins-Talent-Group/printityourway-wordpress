<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check whether the product is customizable without applying any additional filter on the result.
 *
 * @param int $product_id
 *
 * @return bool Whether the product is customizable.
 */
function zakeke_internal_is_customizable( $product_id ) {
	$zakeke_enabled = get_post_meta( $product_id, 'zakeke_enabled', 'no' );

	return 'yes' === $zakeke_enabled;
}

/**
 * Check whether the product is customizable.
 *
 * @param int $product_id
 *
 * @return bool Whether the product is customizable.
 */
function zakeke_is_customizable( $product_id ) {
	$zakeke_enabled = zakeke_internal_is_customizable( $product_id );

	return apply_filters( 'zakeke_is_customizable', $zakeke_enabled, $product_id );
}

/**
 * Check whether the product is configurable without applying additional filters.
 *
 * @param int $product_id
 *
 * @return bool Whether the product is configurable.
 */
function zakeke_internal_configurator_is_customizable( $product_id ) {
	$zakeke_enabled = get_post_meta( $product_id, 'zakeke_configurator_enabled', 'no' );

	return 'yes' === $zakeke_enabled;
}

/**
 * Check whether the product is configurable.
 *
 * @param int $product_id
 *
 * @return bool Whether the product is configurable.
 */
function zakeke_configurator_is_customizable( $product_id ) {
	$zakeke_enabled = zakeke_internal_configurator_is_customizable( $product_id );

	return apply_filters( 'zakeke_configurator_is_customizable', $zakeke_enabled, $product_id );
}

/**
 * Check whether the product has a provider.
 *
 * @param int $product_id
 *
 * @return bool Whether the product has a provider.
 */
function zakeke_has_provider( $product_id ) {
	$has_provider = get_post_meta( $product_id, 'zakeke_provider', 'no' );

	return 'yes' === $has_provider;
}

/**
 * Get the Zakeke guest identifier using cookies.
 *
 * @return string
 */
function zakeke_guest_code() {
	if ( isset( $_COOKIE['zakeke-guest'] ) ) {
		$value = sanitize_text_field(wp_unslash($_COOKIE['zakeke-guest']));
		if ( strlen( $value ) === 32 ) {
			return $value;
		} else {
			return wp_generate_password( 32, false );
		}
	}

	$value = wp_generate_password( 32, false );
	/**Ten years */
	$period = 315360000;
	wc_setcookie( 'zakeke-guest', $value, time() + $period, is_ssl() );

	return $value;
}

function zakeke_return_url() {
	$cart_page_id = wc_get_page_id('cart');
	if (-1 === $cart_page_id || 'publish' !==  get_post_status($cart_page_id)) {
		return wc_get_checkout_url();
	}

	return wc_get_cart_url();
}

function zakeke_get_shop_url() {
	$shop_page_url = get_permalink( wc_get_page_id( 'shop' ) );
	if (!$shop_page_url) {
		return home_url();
	}

	return $shop_page_url;
}

/**
 * Get the designer url without auth token
 *
 * @param array $request_params
 * @param bool $mobile
 * @param WC_Product|null $product
 * @return string
 */
function zakeke_customizer_url( $request_params, $mobile, $product = null, $template = null ) {
	$integration = new Zakeke_Integration();

	if ( is_null( $product ) ) {
		if ( isset( $request_params['ztmp_prefix_add-to-cart'] ) ) {
			$product = wc_get_product( $request_params['ztmp_prefix_add-to-cart'] );
		} elseif ( isset( $request_params['add-to-cart'] ) ) {
			$product = wc_get_product( $request_params['add-to-cart'] );
		} elseif ( isset( $request_params['product_id'] ) ) {
			$product = wc_get_product( $request_params['product_id'] );
		}

		if ( ! $product ) {
			$product = wc_get_product();
		}
	}

	$quantity = empty( $request_params['quantity'] ) ? 1 : wc_stock_amount( $request_params['quantity'] );

	if ( ! wc_tax_enabled() ) {
		$tax_policy = 'hidden';
	} elseif ( get_option( 'woocommerce_tax_display_shop' ) === 'excl' ) {
		$tax_policy = 'excluding';
	} else {
		$tax_policy = 'including';
	}

	$culture =  get_locale();
	if (function_exists('weglot_get_current_language')) {
		$culture = weglot_get_current_language();
	}

	$data = array(
		'name'            => $product->get_title(),
		'qty'             => $quantity,
		'currency'        => get_woocommerce_currency(),
		'taxPricesPolicy' => $tax_policy,
		'culture'         => str_replace( '_', '-', $culture ),
		'modelCode'       => (string) apply_filters( 'zakeke_iframe_modelCode', $product->get_id() ),
		'ecommerce'       => 'woocommerce',
		'attribute'       => array(),
		'mv'              => 1,
		'nn'              => 1,
		'isBulkAsNNSupported' => 1,
		'enableShareDesignUrl' => 'true'
	);

	if ( 'yes' === $integration->show_cart_sides ) {
		$data['isClientPreviewsEnabled'] = '1';
	}

	$default_attributes = $product->get_default_attributes();
	if ( $default_attributes ) {
		foreach ( $default_attributes as $attribute_slug => $attribute ) {
			$data['attribute'][ $attribute_slug ] = $attribute;
		}
	}

	foreach ( $request_params as $key => $value ) {
		$prefix = substr( $key, 0, 10 );
		if ( 'attribute_' === $prefix ) {
			$short_key                       = substr( $key, 10 );
			$data['attribute'][ $short_key ] = $value;
		}
	}

	if ( isset( $request_params['zdesign_edit'] ) ) {
		$data['designdocid']  = $request_params['zdesign_edit'];
		$data['hideVariants'] = 'true';
	}

	if ( isset( $request_params['zshared'] ) ) {
		$data['sharedDesignDocId']  = $request_params['zshared'];
	}

	if ( ! is_null( $template ) ) {
		$data['loadTemplateID'] = $template;
	}

	$path = '/Customizer/index.html';
	if ( $mobile ) {
		$path = '/Customizer/index.mobile.html';
	}

	$share_base_url         = WC_AJAX::get_endpoint( 'zakeke_share' );
	$data['shareUrlPrefix'] = get_site_url( get_current_blog_id(), '/' ) . $share_base_url . '&path=';

	$url = ZAKEKE_BASE_URL . $path . '?' . http_build_query( apply_filters( 'zakeke_designer_url_data', $data ) );

	return $url;
}

/**
 * Get the data to bootstap the customizer
 *
 * @param WC_Product|null $product
 * @return string
 */
function zakeke_customizer_config( $product = null, $template = null, $from_shortcode = false ) {
	$params = $_REQUEST;

	if (null !== $product) {
		$params['ztmp_prefix_add-to-cart'] = $product->get_id();

		if ($product->is_type('variable')) {
			$default_attributes = $product->get_default_attributes();
			if ($default_attributes) {
				foreach ($default_attributes as $attribute_slug => $attribute) {
					$params['attribute_' . $attribute_slug] = $attribute;
				}
			}
		}

		if (isset($params['add-to-cart'])) {
			unset($params['add-to-cart']);
		}
	}

	$integration = new Zakeke_Integration();

	$config = array(
		'zakekeUrl' => ZAKEKE_BASE_URL,
		'customizerLargeUrl' => zakeke_customizer_url($params, false, $product, $template),
		'customizerSmallUrl' => zakeke_customizer_url($params, true, $product, $template),
		'wc_cart_url' => zakeke_return_url(),
		'wc_ajax_url' => WC_AJAX::get_endpoint('%%endpoint%%'),
		'from_shortcode' => $from_shortcode,
		'params' => $params,
		'share_return_to_product_page' => 'yes' === $integration->share_return_to_product_page
	);

	return apply_filters('zakeke_customizer_config', json_encode($config));
}

/**
 * Calculate Zakeke price.
 *
 * @param float $price
 * @param array $pricing
 * @param int $qty
 *
 * @return float
 */
function zakeke_calculate_price( $price, $pricing, $qty) {
	$zakekePrice = 0.0;

	if ( $pricing['modelPriceDeltaPerc'] > 0 ) {
		$zakekePrice += $price * ( (float) $pricing['modelPriceDeltaPerc'] / 100 );
	} else {
		$zakekePrice += (float) $pricing['modelPriceDeltaValue'];
	}

	if ( 0 != $pricing['designPrice'] ) {
		if ( isset($pricing['pricingModel']) && 'advanced' === $pricing['pricingModel'] ) {
			$zakekePrice += (float) $pricing['designPrice'] / $qty;
		} else {
			$zakekePrice += (float) $pricing['designPrice'];
		}
	}

	if (isset($pricing['conditions']) && count($pricing['conditions']) > 0) {
		$zakekePerc = 0.0;
		foreach ($pricing['conditions'] as $condition) {
			if (1 === $condition['priceType']) {
				$zakekePerc += $condition['priceToAdd'];
			}
		}

		if (0.0 !== $zakekePerc) {
			$zakekePrice += $price * ( (float) $zakekePerc / 100 );
		}
	}

	return $zakekePrice;
}

/**
 * Get the sum of all qty present in the cart for a specific design
 *
 * @param string $design
 * @param array $cart_items
 *
 * @return int
 */
function zakeke_cart_total_qty_for_design( $design, $cart_items) {
	$total_qty = 0;

	foreach ($cart_items as $cart_item_key => $values) {
		if ( ! isset( $values['zakeke_data'] ) || $design !== $values['zakeke_data']['design'] ) {
			continue;
		}

		$qty = (int) $values['quantity'];
		if ($qty <= 0) {
			$qty = 1;
		}

		$total_qty = $total_qty + $qty;
	}

	return $total_qty;
}

/**
 * Get an instance of the Zakeke auth based on the plugin configuration.
 *
 * @return Zakeke_Auth_Base
 */
function zakeke_get_auth() {
	$integration = new Zakeke_Integration();

	if ( strlen( $integration->get_option( 'client_id' ) ) === 0 ) {
		return new Zakeke_Auth_Legacy( $integration );
	} else {
		return new Zakeke_Auth( $integration );
	}
}

/**
 * Load the Zakeke designer template.
 *
 * @param string $file
 *
 * @return string
 */
function zakeke_template_loader( $file ) {
	$template = locate_template( $file );
	if ( ! $template ) {
		$template = get_zakeke()->plugin_path() . '/templates/' . $file;
	}

	return $template;
}

/**
 * Retry a function in case of exception
 *
 * @param $func
 * @param int $retryN
 * @return mixed
 */
function zakeke_retry( $func, $retryN = 1) {
	while ( true ) {
		if ($retryN <= 0) {
			return $func();
		}
		try {
			return $func();
		} catch (Exception $e) {
			$retryN--;
		}
	}
}

/**
 * For a given product, and optionally price/qty, work out the price with tax included, based on store settings.
 *
 * @since  3.0.0
 * @param  WC_Product $product WC_Product object.
 * @param  array      $args Optional arguments to pass product quantity and price.
 * @return float
 */
function zakeke_wc_get_price_including_tax( $product, $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'qty'   => '',
			'price' => '',
		)
	);

	$price = '' !== $args['price'] ? (float) $args['price'] : $product->get_price();
	$qty   = '' !== $args['qty'] ? max( 0.0, (float) $args['qty'] ) : 1;

	if ( '' === $price ) {
		return '';
	} elseif ( empty( $qty ) ) {
		return 0.0;
	}

	$line_price   = $price * $qty;
	$return_price = $line_price;

	if ( $product->is_taxable() ) {
		if ( ! wc_prices_include_tax() ) {
			$tax_rates = WC_Tax::get_rates( $product->get_tax_class() );
			$taxes     = WC_Tax::calc_tax( $line_price, $tax_rates, false );

			if ( 'yes' === get_option( 'woocommerce_tax_round_at_subtotal' ) ) {
				$taxes_total = array_sum( $taxes );
			} else {
				$taxes_total = array_sum( array_map( 'wc_round_tax_total', $taxes ) );
			}

			$return_price = round( $line_price + $taxes_total, wc_get_price_decimals() );
		} else {
			$tax_rates      = WC_Tax::get_rates( $product->get_tax_class() );
			$base_tax_rates = WC_Tax::get_base_tax_rates( $product->get_tax_class( 'unfiltered' ) );

			/**
			 * If the customer is excempt from VAT, remove the taxes here.
			 * Either remove the base or the user taxes depending on woocommerce_adjust_non_base_location_prices setting.
			 */
			if ( ! empty( WC()->customer ) && WC()->customer->get_is_vat_exempt() ) { // @codingStandardsIgnoreLine.
				$remove_taxes = apply_filters( 'woocommerce_adjust_non_base_location_prices', true ) ? WC_Tax::calc_tax( $line_price, $base_tax_rates, true ) : WC_Tax::calc_tax( $line_price, $tax_rates, true );

				if ( 'yes' === get_option( 'woocommerce_tax_round_at_subtotal' ) ) {
					$remove_taxes_total = array_sum( $remove_taxes );
				} else {
					$remove_taxes_total = array_sum( array_map( 'wc_round_tax_total', $remove_taxes ) );
				}

				$return_price = round( $line_price - $remove_taxes_total, wc_get_price_decimals() );

				/**
				 * The woocommerce_adjust_non_base_location_prices filter can stop base taxes being taken off when dealing with out of base locations.
				 * e.g. If a product costs 10 including tax, all users will pay 10 regardless of location and taxes.
				 * This feature is experimental @since 2.4.7 and may change in the future. Use at your risk.
				 */
			} elseif ( $tax_rates !== $base_tax_rates && apply_filters( 'woocommerce_adjust_non_base_location_prices', true ) ) {
				$base_taxes   = WC_Tax::calc_tax( $line_price, $base_tax_rates, true );
				$modded_taxes = WC_Tax::calc_tax( $line_price - array_sum( $base_taxes ), $tax_rates, false );

				if ( 'yes' === get_option( 'woocommerce_tax_round_at_subtotal' ) ) {
					$base_taxes_total   = array_sum( $base_taxes );
					$modded_taxes_total = array_sum( $modded_taxes );
				} else {
					$base_taxes_total   = array_sum( array_map( 'wc_round_tax_total', $base_taxes ) );
					$modded_taxes_total = array_sum( array_map( 'wc_round_tax_total', $modded_taxes ) );
				}

				$return_price = round( $line_price - $base_taxes_total + $modded_taxes_total, wc_get_price_decimals() );
			}
		}
	}
	return apply_filters( 'woocommerce_get_price_including_tax', $return_price, $qty, $product );
}

/**
 * For a given product, and optionally price/qty, work out the price with tax excluded, based on store settings.
 *
 * @since  3.0.0
 * @param  WC_Product $product WC_Product object.
 * @param  array      $args Optional arguments to pass product quantity and price.
 * @return float
 */
function zakeke_wc_get_price_excluding_tax( $product, $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'qty'   => '',
			'price' => '',
		)
	);

	$price = '' !== $args['price'] ? (float) $args['price'] : $product->get_price();
	$qty   = '' !== $args['qty'] ? max( 0.0, (float) $args['qty'] ) : 1;

	if ( '' === $price ) {
		return '';
	} elseif ( empty( $qty ) ) {
		return 0.0;
	}

	$line_price = $price * $qty;

	if ( $product->is_taxable() && wc_prices_include_tax() ) {
		$tax_rates      = WC_Tax::get_rates( $product->get_tax_class() );
		$base_tax_rates = WC_Tax::get_base_tax_rates( $product->get_tax_class( 'unfiltered' ) );
		$remove_taxes   = apply_filters( 'woocommerce_adjust_non_base_location_prices', true ) ? WC_Tax::calc_tax( $line_price, $base_tax_rates, true ) : WC_Tax::calc_tax( $line_price, $tax_rates, true );
		$return_price   = $line_price - array_sum( $remove_taxes ); // Unrounded since we're dealing with tax inclusive prices. Matches logic in cart-totals class. @see adjust_non_base_location_price.
	} else {
		$return_price = $line_price;
	}

	return apply_filters( 'woocommerce_get_price_excluding_tax', $return_price, $qty, $product );
}

/**
 * Returns the price including or excluding tax, based on the 'woocommerce_tax_display_shop' setting.
 *
 * @since  3.0.0
 * @param  WC_Product $product WC_Product object.
 * @param  array      $args Optional arguments to pass product quantity and price.
 * @return float
 */
function zakeke_wc_get_price_to_display( $product, $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'qty'   => 1,
			'price' => $product->get_price(),
		)
	);

	$price = $args['price'];
	$qty   = $args['qty'];

	return 'incl' === get_option( 'woocommerce_tax_display_shop' ) ?
		zakeke_wc_get_price_including_tax(
			$product,
			array(
				'qty'   => $qty,
				'price' => $price,
			)
		) :
		zakeke_wc_get_price_excluding_tax(
			$product,
			array(
				'qty'   => $qty,
				'price' => $price,
			)
		);
}

