<?php

use XTS\Gutenberg\Block_Attributes;

if ( ! function_exists( 'wd_get_tp_order_meta_attrs' ) ) {
	function wd_get_tp_order_meta_attrs() {
		$attr = new Block_Attributes();
		$attr->add_attr(
			array(
				'orderData'    => array(
					'type'    => 'string',
					'default' => '',
				),
				'textAlign'    => array(
					'type'       => 'string',
					'responsive' => true,
				),
				'orderMetaKey' => array(
					'type' => 'string',
				),
			)
		);

		$attr->add_attr( wd_get_color_control_attrs( 'color' ) );
		$attr->add_attr( wd_get_typography_control_attrs(), 'tp' );

		wd_get_advanced_tab_attrs( $attr );

		return $attr->get_attr();
	}
}
