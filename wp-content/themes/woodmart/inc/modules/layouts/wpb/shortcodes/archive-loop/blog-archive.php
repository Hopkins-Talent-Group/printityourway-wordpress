<?php
/**
 * Blog loop shortcode.
 *
 * @package Woodmart
 */

use XTS\Modules\Layouts\Main;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'woodmart_shortcode_blog_archive_loop' ) ) {
	/**
	 * Blog loop shortcode.
	 *
	 * @param array $settings Shortcode attributes.
	 */
	function woodmart_shortcode_blog_archive_loop( $settings ) {
		$default_settings = array(
			'css'                  => '',
			'parts_media'          => true,
			'parts_title'          => true,
			'parts_meta'           => true,
			'parts_text'           => true,
			'parts_btn'            => true,
			'parts_published_date' => true,
			'is_wpb'               => true,
			'el_id'                => '',
			'wrapper_classes'      => '',
		);

		$settings = wp_parse_args( $settings, $default_settings );

		Main::setup_preview();

		$settings['wrapper_classes'] .= ' wd-blog-archive';

		if ( $settings['is_wpb'] && 'wpb' === woodmart_get_current_page_builder() ) {
			if ( isset( $settings['blog_columns'] ) ) {
				$encoded_data = $settings['blog_columns'];

				$blog_columns        = woodmart_vc_get_control_data( $encoded_data, 'desktop' );
				$blog_columns_tablet = woodmart_vc_get_control_data( $encoded_data, 'tablet' );
				$blog_columns_mobile = woodmart_vc_get_control_data( $encoded_data, 'mobile' );

				if ( $blog_columns ) {
					$settings['blog_columns'] = $blog_columns;
				}

				if ( $blog_columns_tablet ) {
					$settings['blog_columns_tablet'] = $blog_columns_tablet;
				}

				if ( $blog_columns_mobile ) {
					$settings['blog_columns_mobile'] = $blog_columns_mobile;
				}
			}

			$settings['wrapper_classes'] .= ' wd-wpb';
			$settings['wrapper_classes'] .= apply_filters( 'vc_shortcodes_css_class', '', '', $settings ); // phpcs:ignore.

			if ( $settings['css'] ) {
				$settings['wrapper_classes'] .= ' ' . vc_shortcode_custom_css_class( $settings['css'] );
			}
		}

		if ( isset( $settings['parts_title'] ) ) {
			woodmart_set_loop_prop( 'parts_title', $settings['parts_title'] );
		}

		if ( isset( $settings['parts_meta'] ) ) {
			woodmart_set_loop_prop( 'parts_meta', $settings['parts_meta'] );
		}

		if ( isset( $settings['parts_text'] ) ) {
			woodmart_set_loop_prop( 'parts_text', $settings['parts_text'] );
		}

		if ( isset( $settings['parts_btn'] ) ) {
			woodmart_set_loop_prop( 'parts_btn', $settings['parts_btn'] );
		}

		if ( isset( $settings['parts_published_date'] ) ) {
			woodmart_set_loop_prop( 'parts_published_date', $settings['parts_published_date'] );
		}

		if ( isset( $settings['img_size'] ) ) {
			woodmart_set_loop_prop( 'img_size', $settings['img_size'] );
		}

		if ( ! empty( $settings['blog_columns'] ) ) {
			woodmart_set_loop_prop( 'blog_columns', $settings['blog_columns'] );
		}

		if ( ! empty( $settings['blog_columns_tablet'] ) ) {
			woodmart_set_loop_prop( 'blog_columns_tablet', $settings['blog_columns_tablet'] );
		}

		if ( ! empty( $settings['blog_columns_mobile'] ) ) {
			woodmart_set_loop_prop( 'blog_columns_mobile', $settings['blog_columns_mobile'] );
		}

		if ( isset( $settings['blog_design'] ) && 'inherit' !== $settings['blog_design'] ) {
			woodmart_set_loop_prop( 'blog_design', $settings['blog_design'] );
		}

		if ( isset( $settings['blog_design'] ) && in_array( $settings['blog_design'], array( 'mask', 'masonry', 'meta-image' ), true ) ) {
			woodmart_set_loop_prop( 'blog_spacing', isset( $settings['blog_spacing'] ) ? $settings['blog_spacing'] : '20' );
		}

		if ( isset( $settings['blog_spacing_tablet'] ) ) {
			woodmart_set_loop_prop( 'blog_spacing_tablet', $settings['blog_spacing_tablet'] );
		}

		if ( isset( $settings['blog_spacing_mobile'] ) ) {
			woodmart_set_loop_prop( 'blog_spacing_mobile', $settings['blog_spacing_mobile'] );
		}

		woodmart_set_loop_prop( 'blog_masonry', ! empty( $settings['blog_masonry'] ) );

		$required_controls = array(
			'blog_design',
			'img_size',
			'img_size_custom',
			'blog_masonry',
			'parts_published_date',
			'parts_title',
			'parts_meta',
			'parts_text',
			'parts_btn',
			'wrapper_classes',
			'el_id',
		);

		$filtered_settings = array_intersect_key( $settings, array_flip( $required_controls ) );

		ob_start();
		woodmart_main_loop( $filtered_settings );
		Main::restore_preview();
		woodmart_reset_loop();
		return ob_get_clean();
	}
}
