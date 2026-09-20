<?php
if ( ! function_exists( 'wd_gutenberg_page_heading' ) ) {
	function wd_gutenberg_page_heading( $block_attributes ) {
		$block_attributes['is_wpb']          = false;
		$block_attributes['tag']             = isset( $block_attributes['htmlTag'] ) ? $block_attributes['htmlTag'] : 'h2';
		$block_attributes['el_id']           = wd_get_gutenberg_element_id( $block_attributes );
		$block_attributes['wrapper_classes'] = wd_get_gutenberg_element_classes( $block_attributes );

		if ( ! empty( $block_attributes['textAlign'] ) || ! empty( $block_attributes['textAlignTablet'] ) || ! empty( $block_attributes['textAlignMobile'] ) ) {
			$block_attributes['wrapper_classes'] .= ' wd-align';
		}

		return woodmart_shortcode_page_heading( $block_attributes );
	}
}
