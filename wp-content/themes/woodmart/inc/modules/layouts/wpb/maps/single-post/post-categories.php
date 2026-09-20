<?php
/**
 * Post categories map.
 *
 * @package Woodmart
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'woodmart_get_vc_map_single_post_categories' ) ) {
	/**
	 * Post categories map.
	 */
	function woodmart_get_vc_map_single_post_categories() {
		$link_typography = woodmart_get_typography_map(
			array(
				'key'      => 'link_typography',
				'group'    => esc_html__( 'Style', 'woodmart' ),
				'selector' => '{{WRAPPER}}.wd-single-post-cat .wd-post-cat',
			)
		);

		return array(
			'base'        => 'woodmart_single_post_categories',
			'name'        => esc_html__( 'Post categories', 'woodmart' ),
			'category'    => woodmart_get_tab_title_category_for_wpb( esc_html__( 'Posts elements', 'woodmart' ) ),
			'description' => esc_html__( 'This post belongs to the following categories', 'woodmart' ),
			'icon'        => WOODMART_ASSETS . '/images/vc-icon/post-icons/post-categories.svg',
			'params'      => array(
				array(
					'group'      => esc_html__( 'Style', 'woodmart' ),
					'type'       => 'woodmart_css_id',
					'param_name' => 'woodmart_css_id',
				),

				array(
					'type'       => 'woodmart_title_divider',
					'group'      => esc_html__( 'Style', 'woodmart' ),
					'holder'     => 'div',
					'title'      => esc_html__( 'General', 'woodmart' ),
					'param_name' => 'general_divider',
				),

				array(
					'type'       => 'dropdown',
					'group'      => esc_html__( 'Style', 'woodmart' ),
					'param_name' => 'categories_style',
					'heading'    => esc_html__( 'Style', 'woodmart' ),
					'value'      => array(
						esc_html__( 'Default', 'woodmart' ) => 'default',
						esc_html__( 'With background', 'woodmart' ) => 'with-bg',
					),
					'std'        => 'default',
				),

				array(
					'type'       => 'wd_colorpicker',
					'heading'    => esc_html__( 'Background color', 'woodmart' ),
					'group'      => esc_html__( 'Style', 'woodmart' ),
					'param_name' => 'cats_bg',
					'selectors'  => array(
						'{{WRAPPER}} .wd-post-cat.wd-style-with-bg' => array(
							'background-color: {{VALUE}};',
						),
					),
					'dependency' => array(
						'element' => 'categories_style',
						'value'   => array( 'with-bg' ),
					),
				),

				array(
					'heading'    => esc_html__( 'Alignment', 'woodmart' ),
					'group'      => esc_html__( 'Style', 'woodmart' ),
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

				$link_typography['font_family'],
				$link_typography['font_size'],
				$link_typography['font_weight'],
				$link_typography['text_transform'],
				$link_typography['font_style'],
				$link_typography['line_height'],

				array(
					'type'       => 'woodmart_empty_space',
					'param_name' => 'woodmart_empty_space',
					'group'      => esc_html__( 'Style', 'woodmart' ),
				),

				array(
					'heading'          => esc_html__( 'Color', 'woodmart' ),
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'type'             => 'wd_colorpicker',
					'param_name'       => 'link_color',
					'selectors'        => array(
						'{{WRAPPER}}.wd-single-post-cat .wd-post-cat' => array(
							'--wd-link-color: {{VALUE}};',
						),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'heading'          => esc_html__( 'Hover color', 'woodmart' ),
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'type'             => 'wd_colorpicker',
					'param_name'       => 'link_hover_color',
					'selectors'        => array(
						'{{WRAPPER}}.wd-single-post-cat .wd-post-cat' => array(
							'--wd-link-color-hover: {{VALUE}};',
						),
					),
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
