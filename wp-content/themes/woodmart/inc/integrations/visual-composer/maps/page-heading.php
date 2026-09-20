<?php if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );}
/**
* ------------------------------------------------------------------------------------------------
*  Page heading element map
* ------------------------------------------------------------------------------------------------
*/

if ( ! function_exists( 'woodmart_get_vc_map_page_heading' ) ) {
	function woodmart_get_vc_map_page_heading() {
		return array(
			'name'        => esc_html__( 'Page heading', 'woodmart' ),
			'base'        => 'woodmart_page_heading',
			'category'    => woodmart_get_tab_title_category_for_wpb( esc_html__( 'Site', 'woodmart' ) ),
			'description' => esc_html__( 'Shows the title of currently viewing page', 'woodmart' ),
			'icon'        => WOODMART_ASSETS . '/images/vc-icon/page-heading.svg',
			'params'      => woodmart_get_page_heading_params(),
		);
	}
}

if ( ! function_exists( 'woodmart_get_page_heading_params' ) ) {
	function woodmart_get_page_heading_params() {
		$typography = woodmart_get_typography_map(
			array(
				'key'              => 'typography',
				'selector'         => '{{WRAPPER}} .title',
				'edit_field_class' => 'vc_col-sm-6 vc_column',
			)
		);

		return array(
			array(
				'type'       => 'woodmart_css_id',
				'param_name' => 'woodmart_css_id',
			),

			array(
				'type'       => 'woodmart_title_divider',
				'holder'     => 'div',
				'title'      => esc_html__( 'Style', 'woodmart' ),
				'param_name' => 'title_divider',
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
					'left'   => WOODMART_ASSETS_IMAGES . '/settings/align/left.jpg',
					'center' => WOODMART_ASSETS_IMAGES . '/settings/align/center.jpg',
					'right'  => WOODMART_ASSETS_IMAGES . '/settings/align/right.jpg',
				),
			),

			$typography['font_family'],
			$typography['font_size'],
			$typography['font_weight'],
			$typography['text_transform'],
			$typography['font_style'],
			$typography['line_height'],

			array(
				'heading'          => esc_html__( 'Color', 'woodmart' ),
				'type'             => 'wd_colorpicker',
				'param_name'       => 'title_color',
				'selectors'        => array(
					'{{WRAPPER}} .title' => array(
						'color: {{VALUE}};',
					),
				),
				'edit_field_class' => 'vc_col-sm-6 vc_column',
			),

			array(
				'type'       => 'woodmart_title_divider',
				'holder'     => 'div',
				'title'      => esc_html__( 'Extra options', 'woodmart' ),
				'param_name' => 'extra_divider',
			),

			array(
				'heading'          => esc_html__( 'Title tag', 'woodmart' ),
				'type'             => 'dropdown',
				'param_name'       => 'tag',
				'value'            => array(
					'h1'   => 'h1',
					'h2'   => 'h2',
					'h3'   => 'h3',
					'h4'   => 'h4',
					'h5'   => 'h5',
					'h6'   => 'h6',
					'p'    => 'p',
					'div'  => 'div',
					'span' => 'span',
				),
				'std'              => 'h2',
				'edit_field_class' => 'vc_col-sm-6 vc_column',
			),

			array(
				'heading'    => esc_html__( 'CSS box', 'woodmart' ),
				'group'      => esc_html__( 'Design Options', 'js_composer' ),
				'type'       => 'css_editor',
				'param_name' => 'css',
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
		);
	}
}
