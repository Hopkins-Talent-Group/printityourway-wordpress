<?php
/**
 * Post excerpt shortcode.
 *
 * @package Woodmart
 */

use XTS\Modules\Layouts\Main;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'woodmart_shortcode_single_post_excerpt' ) ) {
	/**
	 * Post excerpt shortcode function.
	 *
	 * @param array $settings Shortcode settings.
	 * @return string Shortcode HTML output.
	 */
	function woodmart_shortcode_single_post_excerpt( $settings ) {
		$default_settings = array(
			'css'       => '',
			'alignment' => 'left',
		);

		$settings = wp_parse_args( $settings, $default_settings );

		$wrapper_classes  = apply_filters( 'vc_shortcodes_css_class', '', '', $settings );
		$wrapper_classes .= ' text-' . woodmart_vc_get_control_data( $settings['alignment'], 'desktop' );

		if ( $settings['css'] ) {
			$wrapper_classes .= ' ' . vc_shortcode_custom_css_class( $settings['css'] );
		}

		ob_start();

		Main::setup_preview();

		$excerpt = get_post_field( 'post_excerpt', get_the_ID() );

		if ( $excerpt ) {
		   echo '<div class="wd-wpb wd-single-post-excerpt' . esc_attr( $wrapper_classes ) . '">' . $excerpt . '</div>'; // phpcs:ignore.
		}

		Main::restore_preview();

		return ob_get_clean();
	}
}
