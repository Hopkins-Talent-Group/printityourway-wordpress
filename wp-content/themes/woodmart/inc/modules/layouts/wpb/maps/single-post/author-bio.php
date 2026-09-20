<?php
/**
 * Author biography map.
 *
 * @package Woodmart
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'woodmart_get_vc_map_post_author_bio' ) ) {
	/**
	 * Author biography map.
	 */
	function woodmart_get_vc_map_post_author_bio() {
		$title_typography = woodmart_get_typography_map(
			array(
				'key'              => 'title_typography',
				'group'            => esc_html__( 'Style', 'woodmart' ),
				'selector'         => '{{WRAPPER}} .wd-author-title',
				'edit_field_class' => 'vc_col-sm-6 vc_column',
			)
		);

		$text_typography = woodmart_get_typography_map(
			array(
				'key'              => 'text_typography',
				'group'            => esc_html__( 'Style', 'woodmart' ),
				'selector'         => '{{WRAPPER}} .wd-author-area-info',
				'edit_field_class' => 'vc_col-sm-6 vc_column',
			)
		);

		$link_typography = woodmart_get_typography_map(
			array(
				'key'      => 'link_typography',
				'group'    => esc_html__( 'Style', 'woodmart' ),
				'selector' => '{{WRAPPER}} .wd-author-bio .wd-author-link',
			)
		);

		return array(
			'base'        => 'woodmart_post_author_bio',
			'name'        => esc_html__( 'Author bio', 'woodmart' ),
			'description' => esc_html__( 'Shows author\'s biography', 'woodmart' ),
			'category'    => woodmart_get_tab_title_category_for_wpb( esc_html__( 'Posts elements', 'woodmart' ) ),
			'icon'        => WOODMART_ASSETS . '/images/vc-icon/post-icons/author-bio.svg',
			'params'      => array(
				array(
					'type'       => 'woodmart_css_id',
					'group'      => esc_html__( 'Style', 'woodmart' ),
					'param_name' => 'woodmart_css_id',
				),
				array(
					'type'       => 'woodmart_title_divider',
					'group'      => esc_html__( 'Style', 'woodmart' ),
					'holder'     => 'div',
					'title'      => esc_html__( 'General', 'woodmart' ),
					'param_name' => 'alignment_divider',
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
						'left'   => WOODMART_ASSETS_IMAGES . '/settings/align/left.jpg',
						'center' => WOODMART_ASSETS_IMAGES . '/settings/align/center.jpg',
						'right'  => WOODMART_ASSETS_IMAGES . '/settings/align/right.jpg',
					),
				),
				array(
					'type'       => 'woodmart_title_divider',
					'group'      => esc_html__( 'Style', 'woodmart' ),
					'holder'     => 'div',
					'title'      => esc_html__( 'Title', 'woodmart' ),
					'param_name' => 'title_divider',
				),

				$title_typography['font_family'],
				$title_typography['font_size'],
				$title_typography['font_weight'],
				$title_typography['text_transform'],
				$title_typography['font_style'],
				$title_typography['line_height'],

				array(
					'type'             => 'wd_colorpicker',
					'heading'          => esc_html__( 'Color', 'woodmart' ),
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'param_name'       => 'title_color',
					'selectors'        => array(
						'{{WRAPPER}} .wd-author-title' => array(
							'color: {{VALUE}};',
						),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'type'       => 'woodmart_title_divider',
					'group'      => esc_html__( 'Style', 'woodmart' ),
					'holder'     => 'div',
					'title'      => esc_html__( 'Text', 'woodmart' ),
					'param_name' => 'text_divider',
				),

				$text_typography['font_family'],
				$text_typography['font_size'],
				$text_typography['font_weight'],
				$text_typography['text_transform'],
				$text_typography['font_style'],
				$text_typography['line_height'],

				array(
					'type'             => 'wd_colorpicker',
					'heading'          => esc_html__( 'Color', 'woodmart' ),
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'param_name'       => 'text_color',
					'selectors'        => array(
						'{{WRAPPER}} .wd-author-area-info' => array(
							'color: {{VALUE}};',
						),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'type'       => 'woodmart_title_divider',
					'group'      => esc_html__( 'Style', 'woodmart' ),
					'holder'     => 'div',
					'title'      => esc_html__( 'Link', 'woodmart' ),
					'param_name' => 'link_divider',
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
					'type'             => 'wd_colorpicker',
					'heading'          => esc_html__( 'Color', 'woodmart' ),
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'param_name'       => 'link_color',
					'selectors'        => array(
						'{{WRAPPER}} .wd-author-bio .wd-author-link' => array(
							'--wd-link-color: {{VALUE}};',
						),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'type'             => 'wd_colorpicker',
					'heading'          => esc_html__( 'Hover color', 'woodmart' ),
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'param_name'       => 'link_hover_color',
					'selectors'        => array(
						'{{WRAPPER}} .wd-author-bio .wd-author-link' => array(
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
