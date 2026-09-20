<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Zakeke support class for WPML
 *
 * @package Zakeke/support
 */
class Zakeke_Support_Checkout_For_WPML {
	/**
	 * Hook in Zakeke support handlers.
	 */
	public static function init() {
		if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
			add_filter( 'zakeke_is_customizable', array( __CLASS__, 'zakeke_is_customizable' ), 10, 2 );
			add_filter( 'zakeke_iframe_modelCode', array( __CLASS__, 'zakeke_iframe_modelCode' ) );

			add_filter( 'zakeke_configurator_is_customizable', array( __CLASS__, 'zakeke_configurator_is_customizable' ), 10, 2 );
			add_filter( 'zakeke_configurator_iframe_modelCode', array( __CLASS__, 'zakeke_configurator_iframe_modelCode' ) );
		}
	}

	/**
	 * Check if the product is translated and if not check if the original one is customizable
	 *
	 * @param bool $is_customizable
	 * @param int $product_id
	 * @return bool If the product or the original one is customizable
	 */
	public static function zakeke_is_customizable( $is_customizable, $product_id ) {
		if ($is_customizable) {
			return $is_customizable;
		}

		$original_product_id = apply_filters( 'wpml_original_element_id', null,$product_id, 'post_product');
		if ($original_product_id === $product_id) {
			return $is_customizable;
		}

		return zakeke_internal_is_customizable( $original_product_id );
	}

	/**
	 * Check if the product is translated and if not check if the original one is configurable
	 *
	 * @param bool $is_customizable
	 * @param int $product_id
	 * @return bool If the product or the original one is configurable
	 */
	public static function zakeke_configurator_is_customizable( $is_customizable, $product_id ) {
		if ($is_customizable) {
			return $is_customizable;
		}

		$original_product_id = apply_filters( 'wpml_original_element_id', null,$product_id, 'post_product');
		if ($original_product_id === $product_id) {
			return $is_customizable;
		}

		return zakeke_internal_configurator_is_customizable( $original_product_id );
	}

	/**
	 * Check if to pass to Zakeke the current product id or the original one
	 *
	 * @param int $product_id
	 * @return int The product id to load in Zakeke
	 */
	public static function zakeke_iframe_modelCode( $product_id ) {
		if ( zakeke_internal_is_customizable( $product_id ) ) {
			return $product_id;
		}

		return apply_filters( 'wpml_original_element_id', null, $product_id, 'post_product' );
	}

	/**
	 * Check if to pass to Zakeke the current product id or the original one
	 *
	 * @param int $product_id
	 * @return int The product id to load in Zakeke
	 */
	public static function zakeke_configurator_iframe_modelCode( $product_id ) {
		if ( zakeke_internal_configurator_is_customizable( $product_id ) ) {
			return $product_id;
		}

		return apply_filters( 'wpml_original_element_id', null, $product_id, 'post_product' );
	}
}

Zakeke_Support_Checkout_For_WPML::init();