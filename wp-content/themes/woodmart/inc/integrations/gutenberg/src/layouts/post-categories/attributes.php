<?php

use XTS\Gutenberg\Block_Attributes;

if ( ! function_exists( 'wd_get_single_post_categories_attrs' ) ) {
	function wd_get_single_post_categories_attrs() {
		$attr = new Block_Attributes();

		$attr->add_attr( wd_get_color_control_attrs( 'bgCatsColor' ) );
		$attr->add_attr( wd_get_color_control_attrs( 'linkColor' ) );
		$attr->add_attr( wd_get_color_control_attrs( 'linkColorHover' ) );

		$attr->add_attr( wd_get_typography_control_attrs(), 'tp' );

		$attr->add_attr(
			array(
				'textAlign'        => array(
					'type'       => 'string',
					'responsive' => true,
				),
				'categories_style' => array(
					'type'    => 'string',
					'default' => 'default',
				),
			)
		);

		wd_get_advanced_tab_attrs( $attr );

		return $attr->get_attr();
	}
}
