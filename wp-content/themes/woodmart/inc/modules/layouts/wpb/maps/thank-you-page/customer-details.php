<?php
/**
 * Customer details map.
 *
 * @package Woodmart
 */

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'woodmart_get_vc_map_tp_customer_details' ) ) {
	/**
	 * Customer details map.
	 */
	function woodmart_get_vc_map_tp_customer_details() {
		$typography_title = woodmart_get_typography_map(
			array(
				'key'              => 'title_typography',
				'selector'         => '{{WRAPPER}} .woocommerce-column__title',
				'edit_field_class' => 'vc_col-sm-6 vc_column',
			)
		);

		$typography_content = woodmart_get_typography_map(
			array(
				'key'              => 'content_typography',
				'selector'         => '{{WRAPPER}} address',
				'edit_field_class' => 'vc_col-sm-6 vc_column',
			)
		);

		return array(
			'base'        => 'woodmart_tp_customer_details',
			'name'        => esc_html__( 'Customer details', 'woodmart' ),
			'category'    => woodmart_get_tab_title_category_for_wpb( esc_html__( 'Thank you page', 'woodmart' ) ),
			'description' => esc_html__( 'Customer billing and shipping details', 'woodmart' ),
			'icon'        => WOODMART_ASSETS . '/images/vc-icon/tp-icons/tp-customer-details.svg',
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
					'type'       => 'woodmart_title_divider',
					'holder'     => 'div',
					'title'      => esc_html__( 'Title', 'woodmart' ),
					'param_name' => 'title_divider',
				),

				$typography_title['font_family'],
				$typography_title['font_size'],
				$typography_title['font_weight'],
				$typography_title['text_transform'],
				$typography_title['font_style'],
				$typography_title['line_height'],

				array(
					'heading'          => esc_html__( 'Color', 'woodmart' ),
					'type'             => 'wd_colorpicker',
					'param_name'       => 'title_color',
					'selectors'        => array(
						'{{WRAPPER}} .woocommerce-column__title' => array(
							'color: {{VALUE}};',
						),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'type'       => 'woodmart_title_divider',
					'holder'     => 'div',
					'title'      => esc_html__( 'Content', 'woodmart' ),
					'param_name' => 'content_divider',
				),

				$typography_content['font_family'],
				$typography_content['font_size'],
				$typography_content['font_weight'],
				$typography_content['text_transform'],
				$typography_content['font_style'],
				$typography_content['line_height'],

				array(
					'heading'          => esc_html__( 'Color', 'woodmart' ),
					'type'             => 'wd_colorpicker',
					'param_name'       => 'content_color',
					'selectors'        => array(
						'{{WRAPPER}} address' => array(
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
