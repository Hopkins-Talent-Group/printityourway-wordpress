<?php
/**
 * Portfolio archive map.
 *
 * @package Woodmart
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'woodmart_get_vc_map_portfolio_archive_loop' ) ) {
	/**
	 * Portfolio archive map.
	 */
	function woodmart_get_vc_map_portfolio_archive_loop() {
		return array(
			'base'        => 'woodmart_portfolio_archive_loop',
			'name'        => esc_html__( 'Portfolio archive', 'woodmart' ),
			'category'    => woodmart_get_tab_title_category_for_wpb( esc_html__( 'Posts elements', 'woodmart' ) ),
			'description' => esc_html__( 'Show portfolio content', 'woodmart' ),
			'icon'        => WOODMART_ASSETS . '/images/vc-icon/pa-portfolio-icons/portfolio-archive.svg',
			'params'      => array(
				array(
					'type'       => 'woodmart_css_id',
					'param_name' => 'woodmart_css_id',
				),

				array(
					'type'       => 'woodmart_title_divider',
					'holder'     => 'div',
					'title'      => esc_html__( 'Style', 'woodmart' ),
					'param_name' => 'layout_divider',
				),

				array(
					'heading'    => esc_html__( 'Style', 'woodmart' ),
					'type'       => 'dropdown',
					'param_name' => 'portfolio_style',
					'value'      => array(
						esc_html__( 'Inherit from Theme Settings', 'woodmart' ) => 'inherit',
						esc_html__( 'Show text on mouse over', 'woodmart' )     => 'hover',
						esc_html__( 'Alternative', 'woodmart' )                 => 'hover-inverse',
						esc_html__( 'Text under image', 'woodmart' )            => 'text-shown',
						esc_html__( 'Mouse move parallax', 'woodmart' )         => 'parallax',
					),
					'std'        => 'inherit',
					'hint'       => esc_html__( 'You can use different style for your portfolio styled for the theme', 'woodmart' ),
				),

				array(
					'type'       => 'textfield',
					'heading'    => esc_html__( 'Images size', 'woodmart' ),
					'param_name' => 'portfolio_image_size',
					'hint'       => esc_html__( 'Enter image size. Example: \'thumbnail\', \'medium\', \'large\', \'full\' or other sizes defined by current theme. Alternatively enter image size in pixels: 200x100 (Width x Height). Leave empty to use \'thumbnail\' size.', 'woodmart' ),
					'dependency' => array(
						'element'            => 'portfolio_style',
						'value_not_equal_to' => array( 'inherit' ),
					),
				),

				array(
					'heading'    => esc_html__( 'Columns', 'woodmart' ),
					'type'       => 'wd_slider',
					'param_name' => 'portfolio_columns',
					'devices'    => array(
						'desktop' => array(
							'value' => 3,
							'unit'  => '',
						),
						'tablet'  => array(
							'value' => 2,
							'unit'  => '',
						),
						'mobile'  => array(
							'value' => 1,
							'unit'  => '',
						),
					),
					'range'      => array(
						'' => array(
							'min'  => 1,
							'max'  => 6,
							'step' => 1,
						),
					),
					'selectors'  => array(),
					'dependency' => array(
						'element'            => 'portfolio_style',
						'value_not_equal_to' => array( 'inherit' ),
					),
				),

				array(
					'type'             => 'woodmart_button_set',
					'heading'          => esc_html__( 'Space between posts', 'woodmart' ),
					'param_name'       => 'portfolio_spacing_tabs',
					'tabs'             => true,
					'value'            => array(
						esc_html__( 'Desktop', 'woodmart' ) => 'desktop',
						esc_html__( 'Tablet', 'woodmart' ) => 'tablet',
						esc_html__( 'Mobile', 'woodmart' ) => 'mobile',
					),
					'default'          => 'desktop',
					'edit_field_class' => 'wd-res-control wd-custom-width vc_col-sm-12 vc_column',
					'dependency'       => array(
						'element'            => 'portfolio_style',
						'value_not_equal_to' => array( 'inherit' ),
					),
				),
				array(
					'type'             => 'dropdown',
					'param_name'       => 'portfolio_spacing',
					'value'            => array(
						0  => 0,
						2  => 2,
						6  => 6,
						10 => 10,
						20 => 20,
						30 => 30,
					),
					'std'              => '20',
					'dependency'       => array(
						'element'            => 'portfolio_style',
						'value_not_equal_to' => array( 'inherit' ),
					),
					'wd_dependency'    => array(
						'element' => 'portfolio_spacing_tabs',
						'value'   => array( 'desktop' ),
					),
					'edit_field_class' => 'wd-res-item vc_col-sm-12 vc_column',
				),
				array(
					'type'             => 'dropdown',
					'param_name'       => 'portfolio_spacing_tablet',
					'value'            => array(
						esc_html__( 'Inherit', 'woodmart' ) => '',
						0  => 0,
						2  => 2,
						6  => 6,
						10 => 10,
						20 => 20,
						30 => 30,
					),
					'std'              => '',
					'dependency'       => array(
						'element'            => 'portfolio_style',
						'value_not_equal_to' => array( 'inherit' ),
					),
					'wd_dependency'    => array(
						'element' => 'portfolio_spacing_tabs',
						'value'   => array( 'tablet' ),
					),
					'edit_field_class' => 'wd-res-item vc_col-sm-12 vc_column',
				),
				array(
					'type'             => 'dropdown',
					'param_name'       => 'portfolio_spacing_mobile',
					'value'            => array(
						esc_html__( 'Inherit', 'woodmart' ) => '',
						0  => 0,
						2  => 2,
						6  => 6,
						10 => 10,
						20 => 20,
						30 => 30,
					),
					'std'              => '',
					'dependency'       => array(
						'element'            => 'portfolio_style',
						'value_not_equal_to' => array( 'inherit' ),
					),
					'wd_dependency'    => array(
						'element' => 'portfolio_spacing_tabs',
						'value'   => array( 'mobile' ),
					),
					'edit_field_class' => 'wd-res-item vc_col-sm-12 vc_column',
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
			),
		);
	}
}
