<?php use XTS\Modules\Layouts\Main;

if ( ! function_exists( 'wd_gutenberg_portfolio_archive_categories' ) ) {
	function wd_gutenberg_portfolio_archive_categories( $block_attributes ) {
		$filters_type     = woodmart_get_opt( 'portfolio_filters_type', 'masonry' );
		$filters          = woodmart_get_opt( 'portoflio_filters' );
		$wrapper_classes  = ' wd-portfolio-archive-nav';
		$wrapper_classes .= wd_get_gutenberg_element_classes( $block_attributes );
		$el_id            = wd_get_gutenberg_element_id( $block_attributes );

		if ( ! empty( $block_attributes['textAlign'] ) || ! empty( $block_attributes['textAlignTablet'] ) || ! empty( $block_attributes['textAlignMobile'] ) ) {
			$wrapper_classes .= ' wd-align';
		}

		ob_start();
		Main::setup_preview();
		if ( have_posts() && $filters && ( ( ( 'links' === $filters_type && is_tax() ) || ! is_tax() ) ) ) {
			woodmart_portfolio_filters( '', $filters_type, $wrapper_classes, $el_id );
		}
		Main::restore_preview();
		return ob_get_clean();
	}
}
