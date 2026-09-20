<?php
/**
 * Post meta value shortcode.
 *
 * @package Woodmart
 */

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit; // Direct access not allowed.
}

use XTS\Modules\Layouts\Main;

if ( ! function_exists( 'woodmart_shortcode_single_post_meta_value' ) ) {
	/**
	 * Post meta value shortcode.
	 *
	 * @param array $settings Shortcode settings.
	 * @return string Shortcode HTML output.
	 */
	function woodmart_shortcode_single_post_meta_value( $settings ) {
		$settings = wp_parse_args(
			$settings,
			array(
				'css'       => '',
				'meta_key'  => '', // phpcs:ignore.
				'alignment' => 'left',
			)
		);

		$wrapper_classes  = apply_filters( 'vc_shortcodes_css_class', '', '', $settings );
		$wrapper_classes .= ' text-' . woodmart_vc_get_control_data( $settings['alignment'], 'desktop' );

		if ( isset( $settings['css'] ) && $settings['css'] ) {
			$wrapper_classes .= ' ' . vc_shortcode_custom_css_class( $settings['css'] );
		}

		ob_start();

		Main::setup_preview();

		if ( ! empty( $settings['meta_key'] ) ) {
			echo '<div class="wd-wpb wd-single-post-meta-value' . esc_attr( $wrapper_classes ) . '">';
			echo get_post_meta( get_the_ID(), $settings['meta_key'], true ); // phpcs:ignore
			echo '</div>';
		}

		Main::restore_preview();

		return ob_get_clean();
	}
}
