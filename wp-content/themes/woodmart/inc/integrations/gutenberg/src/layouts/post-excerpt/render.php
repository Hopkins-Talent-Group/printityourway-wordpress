<?php use XTS\Modules\Layouts\Main;

if ( ! function_exists( 'wd_gutenberg_single_post_excerpt' ) ) {
	function wd_gutenberg_single_post_excerpt( $block_attributes ) {
		$el_id           = wd_get_gutenberg_element_id( $block_attributes );
		$wrapper_classes = wd_get_gutenberg_element_classes( $block_attributes );

		if ( ! empty( $block_attributes['textAlign'] ) || ! empty( $block_attributes['textAlignTablet'] ) || ! empty( $block_attributes['textAlignMobile'] ) ) {
			$wrapper_classes .= ' wd-align';
		}

		ob_start();

		Main::setup_preview();

		$excerpt = get_post_field( 'post_excerpt', get_the_ID() );

		if ( $excerpt ) : ?>
			<div id="<?php echo esc_attr( $el_id ); ?>" class="wd-single-post-excerpt<?php echo esc_attr( $wrapper_classes ); ?>">
				<?php echo $excerpt; // phpcs:ignore. ?>
			</div>
			<?php
		endif;

		Main::restore_preview();

		return ob_get_clean();
	}
}
