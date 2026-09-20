<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Zakeke_Shop Class.
 */
class Zakeke_Shop {

	/**
	 * Setup class.
	 */
	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ), 20 );

		add_filter( 'woocommerce_product_supports', array( __CLASS__, 'no_ajax_add_to_cart' ), 30, 3 );
	}

	public static function enqueue_scripts() {
		$integration = new Zakeke_Integration();

		if ( 'yes' === $integration->show_cart_sides ) {
			wp_register_style( 'zakeke-glide', get_zakeke()->plugin_url() . '/assets/css/frontend/libs/glide.core.css',
				array(), ZAKEKE_VERSION );
			wp_register_style( 'zakeke-glide-theme', get_zakeke()->plugin_url() . '/assets/css/frontend/libs/glide.theme.css',
				array( 'zakeke-glide' ), ZAKEKE_VERSION );

			wp_register_style( 'zakeke-shop', get_zakeke()->plugin_url() . '/assets/css/frontend/shop.css',
				array( 'zakeke-glide-theme' ), ZAKEKE_VERSION );

			wp_register_script(
				'zakeke-glide',
				get_zakeke()->plugin_url() . '/assets/js/frontend/libs/glide.js',
				array( 'jquery' ),
				ZAKEKE_VERSION
			);

			wp_enqueue_style( 'zakeke-glide' );
			wp_enqueue_script( 'zakeke-glide' );
		}

		wp_register_style( 'zakeke-shop', get_zakeke()->plugin_url() . '/assets/css/frontend/shop.css',
			array( ), ZAKEKE_VERSION );

		wp_register_script(
			'zakeke-shop',
			apply_filters( 'zakeke_javascript_designer', get_zakeke()->plugin_url() . '/assets/js/frontend/shop.js' ),
			array( ),
			ZAKEKE_VERSION
		);

		wp_enqueue_style( 'zakeke-shop' );
		wp_enqueue_script( 'zakeke-shop' );
	}

	public static function no_ajax_add_to_cart( $enabled, $feature, $product ) {
		if ( 'ajax_add_to_cart' == $feature
			 && ( zakeke_is_customizable( $product->get_id() )
				  || zakeke_configurator_is_customizable( $product->get_id() ) ) ) {
			$enabled = false;
		}

		return $enabled;
	}
}

Zakeke_Shop::init();
