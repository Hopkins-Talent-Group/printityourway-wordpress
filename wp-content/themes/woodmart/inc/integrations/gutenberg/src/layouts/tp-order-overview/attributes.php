<?php

use XTS\Gutenberg\Block_Attributes;

if ( ! function_exists( 'wd_get_tp_order_overview_attrs' ) ) {
	function wd_get_tp_order_overview_attrs() {
		$attr = new Block_Attributes();

		$attr->add_attr(
			array(
				'textAlign' => array(
					'type'       => 'string',
					'responsive' => true,
				),
			)
		);

		$attr->add_attr( wd_get_color_control_attrs( 'labelColor' ) );
		$attr->add_attr( wd_get_typography_control_attrs(), 'labelTp' );
		$attr->add_attr( wd_get_color_control_attrs( 'valueColor' ) );
		$attr->add_attr( wd_get_typography_control_attrs(), 'valueTp' );

		wd_get_advanced_tab_attrs( $attr );
		return $attr->get_attr();
	}
}
