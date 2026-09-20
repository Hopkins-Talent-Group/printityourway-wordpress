<?php
/**
 * Order overview map.
 *
 * @package Woodmart
 */

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'woodmart_get_vc_map_tp_order_overview' ) ) {
	/**
	 * Order overview map.
	 */
	function woodmart_get_vc_map_tp_order_overview() {
		$typography_label = woodmart_get_typography_map(
			array(
				'key'              => 'typography_label',
				'selector'         => '{{WRAPPER}} .woocommerce-thankyou-order-details li > span',
				'edit_field_class' => 'vc_col-sm-6 vc_column',
			)
		);

		$typography_value = woodmart_get_typography_map(
			array(
				'key'              => 'typography_value',
				'selector'         => '{{WRAPPER}} .woocommerce-thankyou-order-details :is(strong,.amount)',
				'edit_field_class' => 'vc_col-sm-6 vc_column',
			)
		);

		return array(
			'base'        => 'woodmart_tp_order_overview',
			'name'        => esc_html__( 'Order overview', 'woodmart' ),
			'category'    => woodmart_get_tab_title_category_for_wpb( esc_html__( 'Thank you page', 'woodmart' ) ),
			'description' => esc_html__( 'Overview of your order\'s number, date, and total amount', 'woodmart' ),
			'icon'        => WOODMART_ASSETS . '/images/vc-icon/tp-icons/tp-order-overview.svg',
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
					'title'      => esc_html__( 'Label', 'woodmart' ),
					'param_name' => 'title_divider',
				),

				$typography_label['font_family'],
				$typography_label['font_size'],
				$typography_label['font_weight'],
				$typography_label['text_transform'],
				$typography_label['font_style'],
				$typography_label['line_height'],

				array(
					'heading'          => esc_html__( 'Color', 'woodmart' ),
					'type'             => 'wd_colorpicker',
					'param_name'       => 'lalel_color',
					'selectors'        => array(
						'{{WRAPPER}} .woocommerce-thankyou-order-details li > span' => array(
							'color: {{VALUE}};',
						),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'type'       => 'woodmart_title_divider',
					'holder'     => 'div',
					'title'      => esc_html__( 'Value', 'woodmart' ),
					'param_name' => 'content_divider',
				),

				$typography_value['font_family'],
				$typography_value['font_size'],
				$typography_value['font_weight'],
				$typography_value['text_transform'],
				$typography_value['font_style'],
				$typography_value['line_height'],

				array(
					'heading'          => esc_html__( 'Color', 'woodmart' ),
					'type'             => 'wd_colorpicker',
					'param_name'       => 'value_color',
					'selectors'        => array(
						'{{WRAPPER}} .woocommerce-thankyou-order-details :is(strong,.amount)' => array(
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
