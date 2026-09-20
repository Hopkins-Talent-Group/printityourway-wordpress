<?php

use XTS\Gutenberg\Block_Attributes;

if ( ! function_exists( 'wd_get_single_post_navigation_attrs' ) ) {
	function wd_get_single_post_navigation_attrs() {
		$attr = new Block_Attributes();

		$attr->add_attr( wd_get_typography_control_attrs(), 'labelTp' );
		$attr->add_attr( wd_get_typography_control_attrs(), 'titleTp' );

		$attr->add_attr( wd_get_color_control_attrs( 'labelColor' ) );
		$attr->add_attr( wd_get_color_control_attrs( 'titleColor' ) );
		$attr->add_attr( wd_get_color_control_attrs( 'titleColorHover' ) );

		wd_get_advanced_tab_attrs( $attr );

		return $attr->get_attr();
	}
}
