<?php
/**
 * Post navigation map.
 *
 * @package Woodmart
 */

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'woodmart_get_vc_map_single_post_navigation' ) ) {
	/**
	 * Post navigation map.
	 */
	function woodmart_get_vc_map_single_post_navigation() {
		$title_typography = woodmart_get_typography_map(
			array(
				'key'      => 'title_typography',
				'group'    => esc_html__( 'Style', 'woodmart' ),
				'selector' => '{{WRAPPER}} .wd-page-nav-btn .wd-entities-title',
			)
		);

		$label_typography = woodmart_get_typography_map(
			array(
				'key'              => 'label_typography',
				'group'            => esc_html__( 'Style', 'woodmart' ),
				'selector'         => '{{WRAPPER}} .wd-page-nav-btn .wd-label',
				'edit_field_class' => 'vc_col-sm-6 vc_column',
			)
		);

		return array(
			'base'        => 'woodmart_single_post_navigation',
			'name'        => esc_html__( 'Post navigation', 'woodmart' ),
			'description' => esc_html__( 'Links to previous and next posts', 'woodmart' ),
			'icon'        => WOODMART_ASSETS . '/images/vc-icon/post-icons/post-navigation.svg',
			'category'    => woodmart_get_tab_title_category_for_wpb( esc_html__( 'Posts elements', 'woodmart' ) ),
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
					'title'      => esc_html__( 'Title', 'woodmart' ),
					'param_name' => 'title_color_divider',
				),

				$title_typography['font_family'],
				$title_typography['font_size'],
				$title_typography['font_weight'],
				$title_typography['text_transform'],
				$title_typography['font_style'],
				$title_typography['line_height'],

				array(
					'type'       => 'woodmart_empty_space',
					'param_name' => 'woodmart_empty_space',
					'group'      => esc_html__( 'Style', 'woodmart' ),
				),

				array(
					'heading'          => esc_html__( 'Color', 'woodmart' ),
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'type'             => 'wd_colorpicker',
					'param_name'       => 'title_color',
					'selectors'        => array(
						'{{WRAPPER}} .wd-page-nav-btn .wd-entities-title' => array(
							'color: {{VALUE}};',
						),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'heading'          => esc_html__( 'Color hover', 'woodmart' ),
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'type'             => 'wd_colorpicker',
					'param_name'       => 'title_hover_color',
					'selectors'        => array(
						'{{WRAPPER}} .wd-page-nav-btn:hover .wd-entities-title' => array(
							'color: {{VALUE}};',
						),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'type'       => 'woodmart_title_divider',
					'group'      => esc_html__( 'Style', 'woodmart' ),
					'holder'     => 'div',
					'title'      => esc_html__( 'Label', 'woodmart' ),
					'param_name' => 'label_color_divider',
				),

				$label_typography['font_family'],
				$label_typography['font_size'],
				$label_typography['font_weight'],
				$label_typography['text_transform'],
				$label_typography['font_style'],
				$label_typography['line_height'],

				array(
					'heading'          => esc_html__( 'Color', 'woodmart' ),
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'type'             => 'wd_colorpicker',
					'param_name'       => 'label_color',
					'selectors'        => array(
						'{{WRAPPER}} .wd-page-nav-btn .wd-label' => array(
							'color: {{VALUE}};',
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
