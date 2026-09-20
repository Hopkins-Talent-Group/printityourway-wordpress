<?php
/**
 * Post comments button shortcode.
 *
 * @package Woodmart
 */

use XTS\Modules\Layouts\Main;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'woodmart_shortcode_single_post_comments_button' ) ) {
	/**
	 * Post comments button shortcode.
	 *
	 * @param array $settings Shortcode settings.
	 * @return string Shortcode HTML output.
	 */
	function woodmart_shortcode_single_post_comments_button( $settings ) {
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
		if ( comments_open() || pings_open() ) {
			woodmart_enqueue_inline_style( 'blog-mod-comments-button' );
			echo '<div class="wd-wpb wd-single-post-reply' . esc_attr( $wrapper_classes ) . '">';
			echo '<div class="wd-post-reply wd-style-1">';
			woodmart_post_meta_reply();
			echo '</div>';
			echo '</div>';
		}
		Main::restore_preview();

		return ob_get_clean();
	}
}
