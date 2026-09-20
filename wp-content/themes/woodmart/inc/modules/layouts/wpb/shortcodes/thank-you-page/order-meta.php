<?php
/**
 * Order meta shortcode.
 *
 * @package Woodmart
 */

use XTS\Modules\Layouts\Main;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'woodmart_shortcode_tp_order_meta' ) ) {
	/**
	 * Order meta shortcode.
	 *
	 * @param array $settings Shortcode settings.
	 * @return string HTML output.
	 */
	function woodmart_shortcode_tp_order_meta( $settings ) {
		global $order;

		if ( ! $order || empty( $settings['order_data'] ) ) {
			return '';
		}

		$default_settings = array(
			'css'        => '',
			'order_data' => '',
			'alignment'  => 'left',
		);

		$settings = wp_parse_args( $settings, $default_settings );

		Main::setup_preview();

		$wrapper_classes  = ' wd-wpb';
		$wrapper_classes .= apply_filters( 'vc_shortcodes_css_class', '', '', $settings );
		$wrapper_classes .= ' text-' . woodmart_vc_get_control_data( $settings['alignment'], 'desktop' );

		if ( $settings['css'] ) {
			$wrapper_classes .= ' ' . vc_shortcode_custom_css_class( $settings['css'] );
		}

		ob_start();
		echo '<div class="wd-el-tp-order-meta' . esc_attr( $wrapper_classes ) . '">';

		switch ( $settings['order_data'] ) {
			case 'order_id':
				echo esc_html( $order->get_id() );
				break;
			case 'order_date':
				echo esc_html( $order->get_date_created()->date_i18n( wc_date_format() ) );
				break;
			case 'order_status':
				echo esc_html( wc_get_order_status_name( $order->get_status() ) );
				break;
			case 'order_email':
				echo esc_html( $order->get_billing_email() );
				break;
			case 'order_total':
				echo wp_kses_post( wc_price( $order->get_total() ) );
				break;
			case 'payment_method':
				echo esc_html( $order->get_payment_method_title() );
				break;
			case 'shipping_method':
				echo esc_html( $order->get_shipping_method() );
				break;
		}

		if ( ! empty( $settings['meta_key'] ) && strpos( $settings['meta_key'], '_' ) !== 0 ) {
			// Custom fields order meta.
			echo $order->get_meta( $settings['meta_key'] ); // phpcs:ignore.
		}

		echo '</div>';
		Main::restore_preview();
		return ob_get_clean();
	}
}
