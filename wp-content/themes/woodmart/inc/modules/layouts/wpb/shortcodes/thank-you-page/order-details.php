<?php
/**
 * Order details shortcode.
 *
 * @package Woodmart
 */

use XTS\Modules\Layouts\Main;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'woodmart_shortcode_tp_order_details' ) ) {
	/**
	 * Order details shortcode.
	 *
	 * @param array $settings Shortcode settings.
	 * @return string HTML output.
	 */
	function woodmart_shortcode_tp_order_details( $settings ) {
		$default_settings = array(
			'css'       => '',
			'alignment' => 'left',
		);
		$settings         = wp_parse_args( $settings, $default_settings );

		Main::setup_preview();

		$wrapper_classes  = ' wd-wpb';
		$wrapper_classes .= apply_filters( 'vc_shortcodes_css_class', '', '', $settings );

		if ( $settings['css'] ) {
			$wrapper_classes .= ' ' . vc_shortcode_custom_css_class( $settings['css'] );
		}

		ob_start();

		global $order;

		if ( ! $order || ! is_a( $order, 'WC_Order' ) ) {
			Main::restore_preview();
			return '';
		}

		$downloads = $order->get_downloadable_items();

		echo '<div class="wd-el-tp-order-details' . esc_attr( $wrapper_classes ) . '">';

		if ( $downloads ) {
			wc_get_template(
				'order/order-downloads.php',
				array(
					'downloads'  => $downloads,
					'show_title' => true,
				)
			);
		}

		woodmart_order_details( $order );

		echo '</div>';

		Main::restore_preview();

		return ob_get_clean();
	}
}
