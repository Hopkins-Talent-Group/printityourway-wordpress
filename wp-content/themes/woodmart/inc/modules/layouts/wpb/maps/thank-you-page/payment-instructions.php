<?php
/**
 * Payment instructions map.
 *
 * @package Woodmart
 */

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'woodmart_get_vc_map_tp_payment_instructions' ) ) {
	/**
	 * Payment instructions map.
	 */
	function woodmart_get_vc_map_tp_payment_instructions() {
		$instructions_typography = woodmart_get_typography_map(
			array(
				'key'              => 'instructions_typography',
				'selector'         => '{{WRAPPER}} p',
				'edit_field_class' => 'vc_col-sm-6 vc_column',
			)
		);

		return array(
			'base'        => 'woodmart_tp_payment_instructions',
			'name'        => esc_html__( 'Payment instructions', 'woodmart' ),
			'category'    => woodmart_get_tab_title_category_for_wpb( esc_html__( 'Thank you page', 'woodmart' ) ),
			'description' => esc_html__( 'Instructions based on the selected payment method', 'woodmart' ),
			'icon'        => WOODMART_ASSETS . '/images/vc-icon/tp-icons/tp-payment-instructions.svg',
			'params'      => array(
				array(
					'type'       => 'woodmart_css_id',
					'param_name' => 'woodmart_css_id',
				),

				array(
					'type'       => 'woodmart_title_divider',
					'holder'     => 'div',
					'title'      => esc_html__( 'Instructions', 'woodmart' ),
					'param_name' => 'instructions_divider',
				),

				$instructions_typography['font_family'],
				$instructions_typography['font_size'],
				$instructions_typography['font_weight'],
				$instructions_typography['text_transform'],
				$instructions_typography['font_style'],
				$instructions_typography['line_height'],

				array(
					'heading'          => esc_html__( 'Color', 'woodmart' ),
					'type'             => 'wd_colorpicker',
					'param_name'       => 'instructions_color',
					'selectors'        => array(
						'{{WRAPPER}} p' => array(
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
