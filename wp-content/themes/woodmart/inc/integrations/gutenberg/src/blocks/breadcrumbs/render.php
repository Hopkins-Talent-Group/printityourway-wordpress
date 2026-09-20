<?php if ( ! function_exists( 'wd_gutenberg_breadcrumbs' ) ) {
	function wd_gutenberg_breadcrumbs( $block_attributes ) {
		$classes = wd_get_gutenberg_element_classes( $block_attributes );

		if ( ! empty( $block_attributes['textAlign'] ) || ! empty( $block_attributes['textAlignTablet'] ) || ! empty( $block_attributes['textAlignMobile'] ) ) {
			$classes .= ' wd-align';
		}

		$block_attributes['is_wpb']          = false;
		$block_attributes['wrapper_classes'] = $classes;
		$block_attributes['el_id']           = wd_get_gutenberg_element_id( $block_attributes );

		if ( ! empty( $block_attributes['nowrapMd'] ) ) {
			$block_attributes['nowrap_md'] = 'yes';
		}

		return woodmart_shortcode_el_breadcrumbs( $block_attributes );
	}
}
