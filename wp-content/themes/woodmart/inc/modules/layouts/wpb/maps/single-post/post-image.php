<?php
/**
 * Post image map.
 *
 * @package Woodmart
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'woodmart_get_vc_map_single_post_image' ) ) {
	/**
	 * Post image map.
	 */
	function woodmart_get_vc_map_single_post_image() {
		return array(
			'base'        => 'woodmart_single_post_image',
			'name'        => esc_html__( 'Post image', 'woodmart' ),
			'description' => esc_html__( 'Featured image of the post', 'woodmart' ),
			'icon'        => WOODMART_ASSETS . '/images/vc-icon/post-icons/post-image.svg',
			'category'    => woodmart_get_tab_title_category_for_wpb( esc_html__( 'Posts elements', 'woodmart' ) ),
			'params'      => array(
				array(
					'type'       => 'woodmart_css_id',
					'param_name' => 'woodmart_css_id',
				),

				array(
					'heading'    => esc_html__( 'Alignment', 'woodmart' ),
					'type'       => 'wd_select',
					'param_name' => 'alignment',
					'style'      => 'images',
					'selectors'  => array(),
					'devices'    => array(
						'desktop' => array(
							'value' => 'left',
						),
					),
					'value'      => array(
						esc_html__( 'Left', 'woodmart' )   => 'left',
						esc_html__( 'Center', 'woodmart' ) => 'center',
						esc_html__( 'Right', 'woodmart' )  => 'right',
					),
					'images'     => array(
						'center' => WOODMART_ASSETS_IMAGES . '/settings/align/center.jpg',
						'left'   => WOODMART_ASSETS_IMAGES . '/settings/align/left.jpg',
						'right'  => WOODMART_ASSETS_IMAGES . '/settings/align/right.jpg',
					),
				),

				array(
					'type'             => 'woodmart_switch',
					'heading'          => esc_html__( 'Post date', 'woodmart' ),
					'param_name'       => 'post_date',
					'true_state'       => 'yes',
					'false_state'      => 'no',
					'std'              => 'no',
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'type'       => 'css_editor',
					'heading'    => esc_html__( 'CSS box', 'woodmart' ),
					'param_name' => 'css',
					'group'      => esc_html__( 'Design Options', 'woodmart' ),
				),

				woodmart_get_vc_responsive_spacing_map(),

				// Width option (with dependency Columns option, responsive).
				woodmart_get_responsive_dependency_width_map( 'responsive_tabs' ),
				woodmart_get_responsive_dependency_width_map( 'width_desktop' ),
				woodmart_get_responsive_dependency_width_map( 'custom_width_desktop' ),
				woodmart_get_responsive_dependency_width_map( 'width_tablet' ),
				woodmart_get_responsive_dependency_width_map( 'custom_width_tablet' ),
				woodmart_get_responsive_dependency_width_map( 'width_mobile' ),
				woodmart_get_responsive_dependency_width_map( 'custom_width_mobile' ),
			),
		);
	}
}
