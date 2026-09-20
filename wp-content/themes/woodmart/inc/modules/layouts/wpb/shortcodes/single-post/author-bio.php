<?php
/**
 * Author biography shortcode.
 *
 * @package Woodmart
 */

use XTS\Modules\Layouts\Main;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'woodmart_shortcode_post_author_bio' ) ) {
	/**
	 * Author biography shortcode.
	 *
	 * @param array $settings Shortcode settings.
	 * @return string Shortcode HTML output.
	 */
	function woodmart_shortcode_post_author_bio( $settings ) {
		$default_settings = array(
			'css'       => '',
			'alignment' => 'left',
		);

		if ( woodmart_is_blog_archive() && ( ! is_author() || 'woodmart_layout' === get_post_type() ) ) {
			return '';
		}

		$settings = wp_parse_args( $settings, $default_settings );

		$wrapper_classes  = apply_filters( 'vc_shortcodes_css_class', '', '', $settings );
		$wrapper_classes .= ' text-' . woodmart_vc_get_control_data( $settings['alignment'], 'desktop' );

		if ( $settings['css'] ) {
			$wrapper_classes .= ' ' . vc_shortcode_custom_css_class( $settings['css'] );
		}

		ob_start();

		Main::setup_preview();

		echo '<div class="wd-wpb wd-single-post-author-bio' . esc_attr( $wrapper_classes ) . '">';
		get_template_part( 'author-bio' );
		echo '</div>';

		Main::restore_preview();

		return ob_get_clean();
	}
}
