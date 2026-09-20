<?php
/**
 * Post comment form shortcode.
 *
 * @package Woodmart
 */

use XTS\Modules\Layouts\Main;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'woodmart_shortcode_single_post_comment_form' ) ) {
	/**
	 * Post comment form shortcode.
	 *
	 * @param array $settings Shortcode settings.
	 * @return string Shortcode HTML output.
	 */
	function woodmart_shortcode_single_post_comment_form( $settings ) {
		$default_settings = array(
			'css' => '',
		);

		$settings = wp_parse_args( $settings, $default_settings );

		$wrapper_classes = apply_filters( 'vc_shortcodes_css_class', '', '', $settings );

		if ( $settings['css'] ) {
			$wrapper_classes .= ' ' . vc_shortcode_custom_css_class( $settings['css'] );
		}

		ob_start();

		Main::setup_preview();

		if ( ! comments_open() || post_password_required() ) {
			return '';
		}

		woodmart_enqueue_inline_style( 'post-types-mod-comments' );
		woodmart_enqueue_inline_style( 'single-post-el-comments' );

		echo '<div class="wd-wpb wd-single-post-comments-form' . esc_attr( $wrapper_classes ) . '">';
		echo '<div id="comments" class="wd-post-comments-form comments-area">';
		comment_form( array( 'comment_notes_after' => '' ), get_the_id() );
		echo '</div>';
		echo '</div>';
		Main::restore_preview();
		return ob_get_clean();
	}
}
