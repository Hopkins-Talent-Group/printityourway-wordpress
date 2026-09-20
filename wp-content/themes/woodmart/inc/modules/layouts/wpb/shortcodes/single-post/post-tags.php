<?php
/**
 * Post tags shortcode.
 *
 * @package Woodmart
 */

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit; // Direct access not allowed.
}

use XTS\Modules\Layouts\Main;

if ( ! function_exists( 'woodmart_shortcode_single_post_tags' ) ) {
	/**
	 * Post tags shortcode.
	 *
	 * @param array $settings Shortcode settings.
	 * @return string Shortcode HTML output.
	 */
	function woodmart_shortcode_single_post_tags( $settings ) {
		$settings = wp_parse_args(
			$settings,
			array(
				'css'       => '',
				'alignment' => 'left',
			)
		);

		$wrapper_classes  = apply_filters( 'vc_shortcodes_css_class', '', '', $settings );
		$wrapper_classes .= ' text-' . woodmart_vc_get_control_data( $settings['alignment'], 'desktop' );

		if ( $settings['css'] ) {
			$wrapper_classes .= ' ' . vc_shortcode_custom_css_class( $settings['css'] );
		}

		ob_start();

		Main::setup_preview();

		if ( get_the_tag_list() ) {
			woodmart_enqueue_inline_style( 'single-post-el-tags' );
			echo '<div class="wd-wpb wd-single-post-tags-list' . esc_attr( $wrapper_classes ) . '">';
			echo '<div class="wd-tags-list wd-style-1">';
			echo wp_kses( get_the_tag_list(), woodmart_get_allowed_html() );
			echo '</div>';
			echo '</div>';
		}

		Main::restore_preview();

		return ob_get_clean();
	}
}
