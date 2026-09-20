<?php
/**
 * Post author meta shortcode.
 *
 * @package Woodmart
 */

use XTS\Modules\Layouts\Main;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'woodmart_shortcode_single_post_author_meta' ) ) {
	/**
	 * Post author meta shortcode.
	 *
	 * @param array $settings Array of settings.
	 * @return string Shortcode HTML output.
	 */
	function woodmart_shortcode_single_post_author_meta( $settings ) {
		$default_settings = array(
			'css'           => '',
			'avatar_width'  => '',
			'author_label'  => '1',
			'author_avatar' => '1',
			'author_name'   => '1',
			'alignment'     => 'left',
		);

		$args            = wp_parse_args( $settings, $default_settings );
		$wrapper_classes = apply_filters( 'vc_shortcodes_css_class', '', '', $args );

		if ( $args['alignment'] ) {
			$wrapper_classes .= ' text-' . woodmart_vc_get_control_data( $args['alignment'], 'desktop' );
		}

		if ( $args['css'] ) {
			$wrapper_classes .= ' ' . vc_shortcode_custom_css_class( $args['css'] );
		}

		ob_start();

		Main::setup_preview();

		$author_label  = ! empty( $args['author_label'] ) ? 'long' : '';
		$author_avatar = ! empty( $args['author_avatar'] );
		$author_name   = ! empty( $args['author_name'] );

		if ( ! $author_label && ! $author_avatar && ! $author_name ) {
			return '';
		}

		$avatar_size = woodmart_vc_get_control_data( $args['avatar_width'], 'desktop' );

		woodmart_enqueue_inline_style( 'blog-mod-author' );

		echo '<div class="wd-wpb wd-single-post-author' . esc_attr( $wrapper_classes ) . '">';
		echo '<div class="wd-post-author">';
		woodmart_post_meta_author( $author_avatar, $author_label, $author_name, $avatar_size ? $avatar_size : 22 );
		echo '</div>';
		echo '</div>';

		Main::restore_preview();

		return ob_get_clean();
	}
}
