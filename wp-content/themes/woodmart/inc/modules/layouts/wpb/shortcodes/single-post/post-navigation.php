<?php
/**
 * Post navigation shortcode.
 *
 * @package Woodmart
 */

use XTS\Modules\Layouts\Main;

if ( ! function_exists( 'woodmart_shortcode_single_post_navigation' ) ) {
	/**
	 * Post navigation shortcode.
	 *
	 * @param array $settings Shortcode settings.
	 * @return string Shortcode HTML output.
	 */
	function woodmart_shortcode_single_post_navigation( $settings ) {
		$wrapper_classes = apply_filters( 'vc_shortcodes_css_class', '', '', $settings );

		if ( isset( $settings['css'] ) && $settings['css'] ) {
			$wrapper_classes .= ' ' . vc_shortcode_custom_css_class( $settings['css'] );
		}

		ob_start();

		Main::setup_preview();
		echo '<div class="wd-wpb wd-single-post-nav' . esc_attr( $wrapper_classes ) . '">';
		woodmart_posts_navigation();
		echo '</div>';
		Main::restore_preview();

		return ob_get_clean();
	}
}
