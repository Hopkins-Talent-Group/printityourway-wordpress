<?php
/**
 * Payment instuctions shortcode.
 *
 * @package Woodmart
 */

use XTS\Modules\Layouts\Main;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'woodmart_shortcode_tp_payment_instructions' ) ) {
	/**
	 * Payment instuctions shortcode.
	 *
	 * @param array $settings Shortcode settings.
	 * @return string HTML output.
	 */
	function woodmart_shortcode_tp_payment_instructions( $settings ) {
		$default_settings = array(
			'css' => '',
		);

		$settings = wp_parse_args( $settings, $default_settings );

		Main::setup_preview();

		global $order;

		$wrapper_classes  = ' wd-wpb';
		$wrapper_classes .= apply_filters( 'vc_shortcodes_css_class', '', '', $settings );

		if ( $settings['css'] ) {
			$wrapper_classes .= ' ' . vc_shortcode_custom_css_class( $settings['css'] );
		}

		ob_start();

		echo '<div class="wd-el-tp-payment-instructions' . esc_attr( $wrapper_classes ) . '">';

		if ( $order && is_a( $order, 'WC_Order' ) ) {
			$payment_method = $order->get_payment_method();
			do_action( 'woocommerce_thankyou_' . $payment_method, $order->get_id() );
		}

		echo '</div>';

		Main::restore_preview();
		return ob_get_clean();
	}
}
