<?php
/**
 * Post title shortcode.
 *
 * @package Woodmart
 */

use XTS\Modules\Layouts\Main;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'woodmart_shortcode_single_post_title' ) ) {
	/**
	 * Post title shortcode.
	 *
	 * @param array $settings Shortcode settings.
	 * @return string Shortcode HTML output.
	 */
	function woodmart_shortcode_single_post_title( $settings ) {
		Main::setup_preview();
		$title = get_the_title();
		Main::restore_preview();

		if ( ! $title ) {
			return '';
		}

		$settings = wp_parse_args(
			$settings,
			array(
				'css'       => '',
				'alignment' => 'left',
				'tag'       => 'h1',
			)
		);

		$wrapper_classes  = apply_filters( 'vc_shortcodes_css_class', '', '', $settings );
		$wrapper_classes .= ' text-' . woodmart_vc_get_control_data( $settings['alignment'], 'desktop' );

		if ( $settings['css'] ) {
			$wrapper_classes .= ' ' . vc_shortcode_custom_css_class( $settings['css'] );
		}

		ob_start();

		woodmart_enqueue_inline_style( 'post-types-mod-predefined' );

		echo '<div class="wd-wpb wd-single-post-title' . esc_attr( $wrapper_classes ) . '">';
		echo '<' . esc_attr( $settings['tag'] ) . ' class="wd-post-title wd-entities-title entry-title title">';
		echo wp_kses_post( $title );
		echo '</' . esc_attr( $settings['tag'] ) . '>';
		echo '</div>';
		return ob_get_clean();
	}
}
