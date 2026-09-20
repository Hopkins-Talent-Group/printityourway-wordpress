<?php
/**
 * Toggle map.
 *
 * @package Elements
 */

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

if ( ! function_exists( 'woodmart_get_vc_map_toggle' ) ) {
	/**
	 * Displays the shortcode settings fields in the admin.
	 */
	function woodmart_get_vc_map_toggle() {
		$typography = woodmart_get_typography_map(
			array(
				'key'      => 'title',
				'selector' => '{{WRAPPER}} > .wd-el-toggle-head > .wd-el-toggle-title',
			)
		);

		return array(
			'base'            => 'woodmart_toggle',
			'name'            => esc_html__( 'Toggle', 'woodmart' ),
			'description'     => esc_html__( 'Toggle visibility of a large amount of content', 'woodmart' ),
			'category'        => woodmart_get_tab_title_category_for_wpb( esc_html__( 'Theme elements', 'woodmart' ) ),
			'icon'            => WOODMART_ASSETS . '/images/vc-icon/toggle.svg',
			'content_element' => true,
			'is_container'    => true,
			'js_view'         => 'VcColumnView',
			'params'          => array(
				array(
					'param_name' => 'woodmart_css_id',
					'type'       => 'woodmart_css_id',
				),

				array(
					'param_name' => 'title_divider',
					'type'       => 'woodmart_title_divider',
					'title'      => esc_html__( 'Title', 'woodmart' ),
					'holder'     => 'div',
				),

				array(
					'type'       => 'textfield',
					'heading'    => esc_html__( 'Toggle title', 'woodmart' ),
					'param_name' => 'element_title',
					'std'        => esc_html__( 'Title', 'woodmart' ),
				),

				array(
					'heading'          => esc_html__( 'Color', 'woodmart' ),
					'type'             => 'wd_colorpicker',
					'param_name'       => 'title_color',
					'selectors'        => array(
						'{{WRAPPER}} > .wd-el-toggle-head > .wd-el-toggle-title' => array(
							'color: {{VALUE}};',
						),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				$typography['font_family'],
				$typography['font_size'],
				$typography['font_weight'],
				$typography['text_transform'],
				$typography['font_style'],
				$typography['line_height'],

				array(
					'param_name' => 'opener_divider',
					'type'       => 'woodmart_title_divider',
					'title'      => esc_html__( 'Opener', 'woodmart' ),
					'holder'     => 'div',
				),

				array(
					'heading'          => esc_html__( 'Color', 'woodmart' ),
					'type'             => 'wd_colorpicker',
					'param_name'       => 'opener_color',
					'selectors'        => array(
						'{{WRAPPER}} .wd-el-toggle-icon:before' => array(
							'color: {{VALUE}};',
						),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'type'             => 'wd_slider',
					'param_name'       => 'opener_size',
					'heading'          => esc_html__( 'Size', 'woodmart' ),
					'devices'          => array(
						'desktop' => array(
							'unit'  => 'px',
							'value' => '',
						),
						'tablet'  => array(
							'unit'  => 'px',
							'value' => '',
						),
						'mobile'  => array(
							'unit'  => 'px',
							'value' => '',
						),
					),
					'range'            => array(
						'px' => array(
							'min'  => 0,
							'max'  => 100,
							'step' => 1,
						),
					),
					'selectors'        => array(
						'{{WRAPPER}} .wd-el-toggle-icon:before' => array(
							'font-size: {{VALUE}}{{UNIT}};',
						),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'param_name' => 'title_divider',
					'type'       => 'woodmart_title_divider',
					'title'      => esc_html__( 'Settings', 'woodmart' ),
					'holder'     => 'div',
				),

				array(
					'type'             => 'wd_slider',
					'param_name'       => 'heading_spacing',
					'heading'          => esc_html__( 'Heading spacing', 'woodmart' ),
					'devices'          => array(
						'desktop' => array(
							'unit'  => 'px',
							'value' => '',
						),
						'tablet'  => array(
							'unit'  => 'px',
							'value' => '',
						),
						'mobile'  => array(
							'unit'  => 'px',
							'value' => '',
						),
					),
					'range'            => array(
						'px' => array(
							'min'  => 0,
							'max'  => 200,
							'step' => 1,
						),
					),
					'generate_zero'    => true,
					'selectors'        => array(
						'{{WRAPPER}} > .wd-el-toggle-content' => array(
							'margin-top: {{VALUE}}{{UNIT}};',
						),
					),
					'edit_field_class' => 'vc_col-sm-12 vc_column',
				),

				array(
					'type'             => 'woodmart_button_set',
					'heading'          => esc_html__( 'State', 'woodmart' ),
					'param_name'       => 'state_tabs',
					'tabs'             => true,
					'value'            => array(
						esc_html__( 'Desktop', 'woodmart' ) => 'desktop',
						esc_html__( 'Tablet', 'woodmart' ) => 'tablet',
						esc_html__( 'Mobile', 'woodmart' ) => 'mobile',
					),
					'default'          => 'desktop',
					'edit_field_class' => 'wd-res-control wd-custom-width vc_col-sm-12 vc_column',
				),
				array(
					'param_name'       => 'state',
					'type'             => 'dropdown',
					'value'            => array(
						esc_html__( 'Closed', 'woodmart' ) => 'closed',
						esc_html__( 'Opened', 'woodmart' ) => 'opened',
						esc_html__( 'Always opened', 'woodmart' ) => 'static',
					),
					'wd_dependency'    => array(
						'element' => 'state_tabs',
						'value'   => array( 'desktop' ),
					),
					'edit_field_class' => 'wd-res-item vc_col-sm-12 vc_column',
				),
				array(
					'param_name'       => 'state_tablet',
					'type'             => 'dropdown',
					'value'            => array(
						esc_html__( 'Closed', 'woodmart' ) => 'closed',
						esc_html__( 'Opened', 'woodmart' ) => 'opened',
						esc_html__( 'Always opened', 'woodmart' ) => 'static',
					),
					'wd_dependency'    => array(
						'element' => 'state_tabs',
						'value'   => array( 'tablet' ),
					),
					'edit_field_class' => 'wd-res-item vc_col-sm-12 vc_column',
				),
				array(
					'param_name'       => 'state_mobile',
					'type'             => 'dropdown',
					'value'            => array(
						esc_html__( 'Closed', 'woodmart' ) => 'closed',
						esc_html__( 'Opened', 'woodmart' ) => 'opened',
						esc_html__( 'Always opened', 'woodmart' ) => 'static',
					),
					'wd_dependency'    => array(
						'element' => 'state_tabs',
						'value'   => array( 'mobile' ),
					),
					'edit_field_class' => 'wd-res-item vc_col-sm-12 vc_column',
				),

				array(
					'type'        => 'woodmart_switch',
					'heading'     => esc_html__( 'Rotate icon on open', 'woodmart' ),
					'param_name'  => 'rotate_icon',
					'true_state'  => 1,
					'false_state' => 0,
					'default'     => 1,
				),

				/**
				 * Design Options.
				 */
				array(
					'type'       => 'css_editor',
					'heading'    => esc_html__( 'CSS box', 'woodmart' ),
					'param_name' => 'css',
					'group'      => esc_html__( 'Design Options', 'js_composer' ),
				),
				function_exists( 'woodmart_get_vc_responsive_spacing_map' ) ? woodmart_get_vc_responsive_spacing_map() : '',

				array(
					'type'        => 'woodmart_switch',
					'heading'     => esc_html__( 'Enable FAQ Scheme', 'woodmart' ),
					'group'       => esc_html__( 'Advanced', 'woodmart' ),
					'param_name'  => 'faq_schema',
					'true_state'  => 'yes',
					'false_state' => 'no',
					'default'     => 'no',
					'hint'        => __( 'Adds FAQ schema to the site, improving its visibility in search engines. Learn more in the <a href="https://developers.google.com/search/docs/appearance/structured-data/faqpage">Google documentation</a>', 'woodmart' ),
				),

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

if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
	/**
	 * Create woodmart nested carousel wrapper.
	 */
	class WPBakeryShortCode_woodmart_toggle extends WPBakeryShortCodesContainer {} // phpcs:ignore.
}
