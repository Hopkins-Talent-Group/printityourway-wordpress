<?php
/**
 * Post meta value map.
 *
 * @package Woodmart
 */

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'woodmart_get_vc_map_single_post_meta_value' ) ) {
	/**
	 * Post meta value map.
	 */
	function woodmart_get_vc_map_single_post_meta_value() {
		$typography = woodmart_get_typography_map(
			array(
				'key'              => 'typography',
				'selector'         => '{{WRAPPER}}',
				'edit_field_class' => 'vc_col-sm-6 vc_column',
			)
		);

		return array(
			'base'        => 'woodmart_single_post_meta_value',
			'name'        => esc_html__( 'Post meta value', 'woodmart' ),
			'description' => esc_html__( 'Custom fields data', 'woodmart' ),
			'icon'        => WOODMART_ASSETS . '/images/vc-icon/post-icons/post-meta-value.svg',
			'category'    => woodmart_get_tab_title_category_for_wpb( esc_html__( 'Posts elements', 'woodmart' ) ),
			'params'      => array(
				array(
					'type'       => 'woodmart_css_id',
					'param_name' => 'woodmart_css_id',
				),

				array(
					'type'       => 'textfield',
					'heading'    => esc_html__( 'Meta key', 'woodmart' ),
					'param_name' => 'meta_key',
				),

				array(
					'title'      => esc_html__( 'Style', 'woodmart' ),
					'type'       => 'woodmart_title_divider',
					'param_name' => 'style_divider',
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

				$typography['font_family'],
				$typography['font_size'],
				$typography['font_weight'],
				$typography['text_transform'],
				$typography['font_style'],
				$typography['line_height'],

				array(
					'heading'          => esc_html__( 'Color', 'woodmart' ),
					'type'             => 'wd_colorpicker',
					'param_name'       => 'color',
					'selectors'        => array(
						'{{WRAPPER}}' => array(
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
