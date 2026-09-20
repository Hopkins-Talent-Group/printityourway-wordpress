<?php
/**
 * Customer details shortcode.
 *
 * @package Woodmart
 */

use XTS\Modules\Layouts\Main;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'woodmart_shortcode_tp_customer_details' ) ) {
	/**
	 * Customer details shortcode.
	 *
	 * @param array $settings Shortcode settings.
	 * @return string HTML output.
	 */
	function woodmart_shortcode_tp_customer_details( $settings ) {
		$default_settings = array(
			'css'       => '',
			'alignment' => 'left',
		);
		$settings         = wp_parse_args( $settings, $default_settings );
		$is_builder       = 'woodmart_layout' === get_post_type();

		Main::setup_preview();

		$wrapper_classes  = ' wd-wpb';
		$wrapper_classes .= apply_filters( 'vc_shortcodes_css_class', '', '', $settings );
		$wrapper_classes .= ' text-' . woodmart_vc_get_control_data( $settings['alignment'], 'desktop' );

		if ( $settings['css'] ) {
			$wrapper_classes .= ' ' . vc_shortcode_custom_css_class( $settings['css'] );
		}

		ob_start();

		global $order;

		if ( $order || is_a( $order, 'WC_Order' ) ) {
			$show_customer_details = $is_builder || $order->get_user_id() === get_current_user_id();
			if ( $show_customer_details ) {
				echo '<div class="wd-el-tp-customer-details' . esc_attr( $wrapper_classes ) . '">';
				wc_get_template( 'order/order-details-customer.php', array( 'order' => $order ) );
				echo '</div>';
			}
		}

		Main::restore_preview();

		return ob_get_clean();
	}
}
