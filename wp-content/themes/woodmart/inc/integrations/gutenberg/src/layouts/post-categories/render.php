<?php if ( ! function_exists( 'wd_gutenberg_single_post_categories' ) ) {
	function wd_gutenberg_single_post_categories( $block_attributes ) {
		$wrapper_classes = wd_get_gutenberg_element_classes( $block_attributes );

		if ( ! empty( $block_attributes['textAlign'] ) || ! empty( $block_attributes['textAlignTablet'] ) || ! empty( $block_attributes['textAlignMobile'] ) ) {
			$wrapper_classes .= ' wd-align';
		}

		$block_attributes['is_wpb']          = false;
		$block_attributes['el_id']           = wd_get_gutenberg_element_id( $block_attributes );
		$block_attributes['wrapper_classes'] = $wrapper_classes;
		return woodmart_shortcode_single_post_categories( $block_attributes );
	}
}
