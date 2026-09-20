<?php
/**
 * Blog archive map.
 *
 * @package Woodmart
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'woodmart_get_vc_map_blog_archive_loop' ) ) {
	/**
	 * Blog loop map.
	 */
	function woodmart_get_vc_map_blog_archive_loop() {
		return array(
			'base'        => 'woodmart_blog_archive_loop',
			'name'        => esc_html__( 'Blog archive', 'woodmart' ),
			'category'    => woodmart_get_tab_title_category_for_wpb( esc_html__( 'Posts elements', 'woodmart' ) ),
			'description' => esc_html__( 'Show blog content', 'woodmart' ),
			'icon'        => WOODMART_ASSETS . '/images/vc-icon/pa-blog-icons/blog-archive.svg',
			'params'      => array(
				array(
					'type'       => 'woodmart_css_id',
					'param_name' => 'woodmart_css_id',
				),

				array(
					'type'       => 'woodmart_title_divider',
					'holder'     => 'div',
					'title'      => esc_html__( 'Design', 'woodmart' ),
					'param_name' => 'design_divider',
				),

				array(
					'heading'          => esc_html__( 'Design', 'woodmart' ),
					'type'             => 'dropdown',
					'param_name'       => 'blog_design',
					'value'            => array(
						esc_html__( 'Inherit from Theme Settings', 'woodmart' ) => 'inherit',
						esc_html__( 'Default', 'woodmart' ) => 'default',
						esc_html__( 'Default alternative', 'woodmart' ) => 'default-alt',
						esc_html__( 'Small images', 'woodmart' ) => 'small-images',
						esc_html__( 'Chess', 'woodmart' ) => 'chess',
						esc_html__( 'Grid', 'woodmart' )  => 'masonry',
						esc_html__( 'Mask on image', 'woodmart' ) => 'mask',
						esc_html__( 'Meta on image', 'woodmart' ) => 'meta-image',
						esc_html__( 'List', 'woodmart' )  => 'list',
					),
					'std'              => 'inherit',
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'hint'             => esc_html__( 'You can use different design for your blog styled for the theme', 'woodmart' ),
				),
				array(
					'type'             => 'woodmart_switch',
					'heading'          => esc_html__( 'Masonry', 'woodmart' ),
					'param_name'       => 'blog_masonry',
					'true_state'       => 1,
					'false_state'      => 0,
					'default'          => 0,
					'dependency'       => array(
						'element' => 'blog_design',
						'value'   => array( 'masonry', 'mask' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'type'       => 'textfield',
					'heading'    => esc_html__( 'Images size', 'woodmart' ),
					'param_name' => 'img_size',
					'hint'       => esc_html__( 'Enter image size. Example: \'thumbnail\', \'medium\', \'large\', \'full\' or other sizes defined by current theme. Alternatively enter image size in pixels: 200x100 (Width x Height). Leave empty to use \'thumbnail\' size.', 'woodmart' ),
					'dependency' => array(
						'element'            => 'blog_design',
						'value_not_equal_to' => array( 'inherit' ),
					),
				),

				array(
					'heading'    => esc_html__( 'Columns', 'woodmart' ),
					'type'       => 'wd_slider',
					'param_name' => 'blog_columns',
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
							'max'  => 4,
							'step' => 1,
						),
					),
					'selectors'  => array(),
					'dependency' => array(
						'element' => 'blog_design',
						'value'   => array( 'masonry', 'mask', 'meta-image' ),
					),
				),

				array(
					'type'             => 'woodmart_button_set',
					'heading'          => esc_html__( 'Space between posts', 'woodmart' ),
					'param_name'       => 'blog_spacing_tabs',
					'tabs'             => true,
					'value'            => array(
						esc_html__( 'Desktop', 'woodmart' ) => 'desktop',
						esc_html__( 'Tablet', 'woodmart' ) => 'tablet',
						esc_html__( 'Mobile', 'woodmart' ) => 'mobile',
					),
					'dependency'       => array(
						'element' => 'blog_design',
						'value'   => array( 'mask', 'masonry', 'meta-image' ),
					),
					'default'          => 'desktop',
					'edit_field_class' => 'wd-res-control wd-custom-width vc_col-sm-12 vc_column',
				),
				array(
					'type'             => 'dropdown',
					'param_name'       => 'blog_spacing',
					'value'            => array(
						'0'  => 0,
						'2'  => 2,
						'6'  => 6,
						'10' => 10,
						'20' => 20,
						'30' => 30,
					),
					'std'              => '20',
					'dependency'       => array(
						'element' => 'blog_design',
						'value'   => array( 'mask', 'masonry', 'meta-image' ),
					),
					'wd_dependency'    => array(
						'element' => 'blog_spacing_tabs',
						'value'   => array( 'desktop' ),
					),
					'edit_field_class' => 'wd-res-item vc_col-sm-12 vc_column',
				),
				array(
					'type'             => 'dropdown',
					'param_name'       => 'blog_spacing_tablet',
					'value'            => array(
						esc_html__( 'Inherit', 'woodmart' ) => '',
						'0'  => 0,
						'2'  => 2,
						'6'  => 6,
						'10' => 10,
						'20' => 20,
						'30' => 30,
					),
					'std'              => '',
					'dependency'       => array(
						'element' => 'blog_design',
						'value'   => array( 'mask', 'masonry', 'meta-image' ),
					),
					'wd_dependency'    => array(
						'element' => 'blog_spacing_tabs',
						'value'   => array( 'tablet' ),
					),
					'edit_field_class' => 'wd-res-item vc_col-sm-12 vc_column',
				),
				array(
					'type'             => 'dropdown',
					'param_name'       => 'blog_spacing_mobile',
					'value'            => array(
						esc_html__( 'Inherit', 'woodmart' ) => '',
						'0'  => 0,
						'2'  => 2,
						'6'  => 6,
						'10' => 10,
						'20' => 20,
						'30' => 30,
					),
					'std'              => '',
					'dependency'       => array(
						'element' => 'blog_design',
						'value'   => array( 'mask', 'masonry', 'meta-image' ),
					),
					'wd_dependency'    => array(
						'element' => 'blog_spacing_tabs',
						'value'   => array( 'mobile' ),
					),
					'edit_field_class' => 'wd-res-item vc_col-sm-12 vc_column',
				),

				/**
				 * Elements visibility
				 */

				array(
					'type'       => 'woodmart_title_divider',
					'holder'     => 'div',
					'title'      => esc_html__( 'Elements', 'woodmart' ),
					'param_name' => 'visibility_divider',
				),
				array(
					'type'             => 'woodmart_switch',
					'heading'          => esc_html__( 'Title for posts', 'woodmart' ),
					'param_name'       => 'parts_title',
					'true_state'       => '1',
					'false_state'      => '0',
					'default'          => '1',
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'woodmart_switch',
					'heading'          => esc_html__( 'Meta information', 'woodmart' ),
					'param_name'       => 'parts_meta',
					'true_state'       => '1',
					'false_state'      => '0',
					'default'          => '1',
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'woodmart_switch',
					'heading'          => esc_html__( 'Post text', 'woodmart' ),
					'param_name'       => 'parts_text',
					'true_state'       => '1',
					'false_state'      => '0',
					'default'          => '1',
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'woodmart_switch',
					'heading'          => esc_html__( 'Read more button', 'woodmart' ),
					'param_name'       => 'parts_btn',
					'true_state'       => '1',
					'false_state'      => '0',
					'default'          => '1',
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'woodmart_switch',
					'heading'          => esc_html__( 'Published date', 'woodmart' ),
					'param_name'       => 'parts_published_date',
					'true_state'       => '1',
					'false_state'      => '0',
					'default'          => '1',
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
			),
		);
	}
}
