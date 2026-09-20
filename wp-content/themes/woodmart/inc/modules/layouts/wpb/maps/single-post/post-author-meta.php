<?php
/**
 * Post author meta map.
 *
 * @package Woodmart
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}


if ( ! function_exists( 'woodmart_get_vc_map_single_post_author_meta' ) ) {
	/**
	 * Post author meta map.
	 */
	function woodmart_get_vc_map_single_post_author_meta() {
		$typography = woodmart_get_typography_map(
			array(
				'key'      => 'typography',
				'group'    => esc_html__( 'Style', 'woodmart' ),
				'selector' => '{{WRAPPER}} .wd-post-author',
			)
		);

		return array(
			'base'        => 'woodmart_single_post_author_meta',
			'name'        => esc_html__( 'Post author', 'woodmart' ),
			'description' => esc_html__( 'Post published by', 'woodmart' ),
			'category'    => woodmart_get_tab_title_category_for_wpb( esc_html__( 'Posts elements', 'woodmart' ) ),
			'icon'        => WOODMART_ASSETS . '/images/vc-icon/post-icons/post-author.svg',
			'params'      => array(
				array(
					'type'       => 'woodmart_css_id',
					'param_name' => 'woodmart_css_id',
					'group'      => esc_html__( 'Style', 'woodmart' ),
				),

				array(
					'type'       => 'woodmart_title_divider',
					'group'      => esc_html__( 'Style', 'woodmart' ),
					'holder'     => 'div',
					'title'      => esc_html__( 'General', 'woodmart' ),
					'param_name' => 'general_divider',
				),

				array(
					'heading'      => esc_html__( 'Alignment', 'woodmart' ),
					'group'        => esc_html__( 'Style', 'woodmart' ),
					'param_name'   => 'alignment',
					'type'         => 'woodmart_image_select',
					'value'        => array(
						esc_html__( 'Left', 'woodmart' )   => 'left',
						esc_html__( 'Center', 'woodmart' ) => 'center',
						esc_html__( 'Right', 'woodmart' )  => 'right',
					),
					'images_value' => array(
						'center' => WOODMART_ASSETS_IMAGES . '/settings/align/center.jpg',
						'left'   => WOODMART_ASSETS_IMAGES . '/settings/align/left.jpg',
						'right'  => WOODMART_ASSETS_IMAGES . '/settings/align/right.jpg',
					),
					'std'          => 'left',
					'wood_tooltip' => true,
				),

				$typography['font_family'],
				$typography['font_size'],
				$typography['font_weight'],
				$typography['text_transform'],
				$typography['font_style'],
				$typography['line_height'],

				array(
					'type'       => 'woodmart_title_divider',
					'holder'     => 'div',
					'group'      => esc_html__( 'Style', 'woodmart' ),
					'title'      => esc_html__( 'Avatar', 'woodmart' ),
					'param_name' => 'author_divider',
				),

				array(
					'heading'     => esc_html__( 'Avatar', 'woodmart' ),
					'group'       => esc_html__( 'Style', 'woodmart' ),
					'type'        => 'woodmart_switch',
					'param_name'  => 'author_avatar',
					'true_state'  => '1',
					'false_state' => '0',
					'default'     => '1',
				),

				array(
					'heading'    => esc_html__( 'Width', 'woodmart' ),
					'group'      => esc_html__( 'Style', 'woodmart' ),
					'type'       => 'wd_slider',
					'param_name' => 'avatar_width',
					'devices'    => array(
						'desktop' => array(
							'value' => 22,
							'unit'  => 'px',
						),
					),
					'range'      => array(
						'px' => array(
							'min'  => 0,
							'max'  => 150,
							'step' => 1,
						),
					),
					'selectors'  => array(),
					'dependency' => array(
						'element' => 'author_avatar',
						'value'   => array( '1' ),
					),
				),

				array(
					'type'       => 'woodmart_title_divider',
					'holder'     => 'div',
					'group'      => esc_html__( 'Style', 'woodmart' ),
					'title'      => esc_html__( 'Label', 'woodmart' ),
					'param_name' => 'label_divider',
				),

				array(
					'heading'     => esc_html__( 'Label', 'woodmart' ),
					'group'       => esc_html__( 'Style', 'woodmart' ),
					'type'        => 'woodmart_switch',
					'param_name'  => 'author_label',
					'true_state'  => '1',
					'false_state' => '0',
					'default'     => '1',
				),

				array(
					'heading'          => esc_html__( 'Color', 'woodmart' ),
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'type'             => 'wd_colorpicker',
					'param_name'       => 'label_color',
					'selectors'        => array(
						'{{WRAPPER}} .wd-post-author' => array(
							'color: {{VALUE}};',
						),
					),
					'dependency'       => array(
						'element' => 'author_label',
						'value'   => array( '1' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'type'       => 'woodmart_title_divider',
					'holder'     => 'div',
					'group'      => esc_html__( 'Style', 'woodmart' ),
					'title'      => esc_html__( 'Author name', 'woodmart' ),
					'param_name' => 'link_divider',
				),

				array(
					'heading'     => esc_html__( 'Name', 'woodmart' ),
					'group'       => esc_html__( 'Style', 'woodmart' ),
					'type'        => 'woodmart_switch',
					'param_name'  => 'author_name',
					'true_state'  => '1',
					'false_state' => '0',
					'default'     => '1',
				),

				array(
					'heading'          => esc_html__( 'Color', 'woodmart' ),
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'type'             => 'wd_colorpicker',
					'param_name'       => 'link_color',
					'selectors'        => array(
						'{{WRAPPER}} .wd-post-author' => array(
							'--wd-link-color: {{VALUE}};',
						),
					),
					'dependency'       => array(
						'element' => 'author_name',
						'value'   => array( '1' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'heading'          => esc_html__( 'Hover color', 'woodmart' ),
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'type'             => 'wd_colorpicker',
					'param_name'       => 'link_hover_color',
					'selectors'        => array(
						'{{WRAPPER}} .wd-post-author' => array(
							'--wd-link-color-hover: {{VALUE}};',
						),
					),
					'dependency'       => array(
						'element' => 'author_name',
						'value'   => array( '1' ),
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
