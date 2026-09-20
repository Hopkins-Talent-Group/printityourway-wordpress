<?php
/**
 * Order message map.
 *
 * @package Woodmart
 */

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'woodmart_get_vc_map_tp_order_message' ) ) {
	/**
	 * Order message map.
	 */
	function woodmart_get_vc_map_tp_order_message() {
		$typography_success = woodmart_get_typography_map(
			array(
				'key'              => 'success_text_typography',
				'selector'         => '{{WRAPPER}} .woocommerce-thankyou-order-received',
				'edit_field_class' => 'vc_col-sm-6 vc_column',
			)
		);

		$typography_failed = woodmart_get_typography_map(
			array(
				'key'              => 'failed_text_typography',
				'selector'         => '{{WRAPPER}} .woocommerce-thankyou-order-failed',
				'edit_field_class' => 'vc_col-sm-6 vc_column',
			)
		);

		return array(
			'base'        => 'woodmart_tp_order_message',
			'name'        => esc_html__( 'Order message', 'woodmart' ),
			'category'    => woodmart_get_tab_title_category_for_wpb( esc_html__( 'Thank you page', 'woodmart' ) ),
			'description' => esc_html__( 'Success/failure message for the order', 'woodmart' ),
			'icon'        => WOODMART_ASSETS . '/images/vc-icon/tp-icons/tp-order-message.svg',
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
					'title'      => esc_html__( 'Success', 'woodmart' ),
					'param_name' => 'success_divider',
				),

				$typography_success['font_family'],
				$typography_success['font_size'],
				$typography_success['font_weight'],
				$typography_success['text_transform'],
				$typography_success['font_style'],
				$typography_success['line_height'],

				array(
					'heading'          => esc_html__( 'Color', 'woodmart' ),
					'type'             => 'wd_colorpicker',
					'param_name'       => 'lalel_color',
					'selectors'        => array(
						'{{WRAPPER}} .woocommerce-thankyou-order-received' => array(
							'color: {{VALUE}};',
						),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'type'       => 'woodmart_title_divider',
					'holder'     => 'div',
					'title'      => esc_html__( 'Failed', 'woodmart' ),
					'param_name' => 'failed_divider',
				),

				$typography_failed['font_family'],
				$typography_failed['font_size'],
				$typography_failed['font_weight'],
				$typography_failed['text_transform'],
				$typography_failed['font_style'],
				$typography_failed['line_height'],

				array(
					'heading'          => esc_html__( 'Color', 'woodmart' ),
					'type'             => 'wd_colorpicker',
					'param_name'       => 'value_color',
					'selectors'        => array(
						'{{WRAPPER}} .woocommerce-thankyou-order-failed' => array(
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
