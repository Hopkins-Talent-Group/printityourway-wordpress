<?php
/**
 * Order overview shortcode.
 *
 * @package Woodmart
 */

use XTS\Modules\Layouts\Main;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'woodmart_shortcode_tp_order_overview' ) ) {
	/**
	 * Order overview shortcode.
	 *
	 * @param array $settings Shortcode settings.
	 * @return string HTML output.
	 */
	function woodmart_shortcode_tp_order_overview( $settings ) {
		$default_settings = array(
			'css'       => '',
			'alignment' => 'left',
		);
		$settings         = wp_parse_args( $settings, $default_settings );

		Main::setup_preview();

		$wrapper_classes  = ' wd-wpb';
		$wrapper_classes .= apply_filters( 'vc_shortcodes_css_class', '', '', $settings );
		$wrapper_classes .= ' text-' . woodmart_vc_get_control_data( $settings['alignment'], 'desktop' );

		if ( $settings['css'] ) {
			$wrapper_classes .= ' ' . vc_shortcode_custom_css_class( $settings['css'] );
		}

		ob_start();
		global $order;
		echo '<div class="wd-el-tp-order-overview' . esc_attr( $wrapper_classes ) . '">';
		woodmart_order_overview( $order, $wrapper_classes );
		echo '</div>';
		Main::restore_preview();
		return ob_get_clean();
	}
}
