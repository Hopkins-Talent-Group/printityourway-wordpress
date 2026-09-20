<?php
/**
 * CartFlows.
 *
 * @package woodmart
 */

if ( ! defined( 'CARTFLOWS_FILE' ) ) {
	return;
}

if ( ! function_exists( 'woodmart_cartflows_is_override_checkout' ) ) {
	/**
	 * Checks if the CartFlows plugin is set to override the global WooCommerce checkout.
	 *
	 * @return bool True if override is enabled, false otherwise.
	 */
	function woodmart_cartflows_is_override_checkout() {
		$common_settings = get_option( '_cartflows_common', false );

		return ! empty( $common_settings ) && isset( $common_settings['override_global_checkout'] ) && 'enable' === $common_settings['override_global_checkout'];
	}
}

if ( ! function_exists( 'woodmart_cartflows_checkout_template_condition' ) ) {
	/**
	 * Fixes the conflict of options that overwrite the checkout template.
	 *
	 * @param bool $condition Is the theme trying to rewrite the template.
	 *
	 * @return bool
	 */
	function woodmart_cartflows_checkout_template_condition( $condition ) {
		if ( woodmart_cartflows_is_override_checkout() ) {
			return false;
		}

		return $condition;
	}

	add_filter( 'woodmart_replace_checkout_template_condition', 'woodmart_cartflows_checkout_template_condition' );
}

if ( ! function_exists( 'woodmart_cartflows_enqueue_styles' ) ) {
	/**
	 * Enqueues the custom stylesheet for CartFlows checkout integration.
	 *
	 * This function loads the 'base-adminbar.min.css' stylesheet specifically for CartFlows checkout pages.
	 *
	 * @return void
	 */
	function woodmart_cartflows_enqueue_styles() {
		wp_enqueue_style( 'wd-int-woo-cartflows-checkout', WOODMART_THEME_DIR . '/css/parts/int-woo-cartflows-checkout.min.css', array(), WOODMART_VERSION );
	}

	add_action( 'wp_enqueue_scripts', 'woodmart_cartflows_enqueue_styles', 10001 );
}

if ( ! function_exists( 'woodmart_cartflows_lazy_loading_force_deinit' ) ) {
	/**
	 * Forces the deinitialization of lazy loading on CartFlows checkout and order received pages.
	 *
	 * This function checks if the current page is the checkout or order received page,
	 * or if an AJAX request related to updating the order review is being made.
	 * If so, it deinitializes lazy loading to ensure compatibility with CartFlows.
	 *
	 * @return void
	 */
	function woodmart_cartflows_lazy_loading_force_deinit() {
		if ( ! is_checkout() && ! is_wc_endpoint_url( 'order-received' ) && ! ( is_ajax() && isset( $_GET['wc-ajax'] ) ) ) {
			return;
		}

		if ( woodmart_cartflows_is_override_checkout() || ( isset( $_GET['wc-ajax'] ) && 'update_order_review' === $_GET['wc-ajax'] ) ) {
			woodmart_lazy_loading_deinit( true );
		}
	}

	add_action( 'wp', 'woodmart_cartflows_lazy_loading_force_deinit' );
}

if ( ! function_exists( 'woodmart_cartflows_sticky_toolbar_deinit' ) ) {
	/**
	 * Deinitializes the sticky toolbar on CartFlows checkout and order received pages.
	 *
	 * Removes the sticky toolbar template from the footer if the current page is a CartFlows override checkout.
	 *
	 * @return void
	 */
	function woodmart_cartflows_sticky_toolbar_deinit() {
		if ( ! is_checkout() && ! is_wc_endpoint_url( 'order-received' ) ) {
			return;
		}

		if ( woodmart_cartflows_is_override_checkout() ) {
			remove_action( 'wp_footer', 'woodmart_sticky_toolbar_template' );
		}
	}

	add_action( 'wp', 'woodmart_cartflows_sticky_toolbar_deinit' );
}
