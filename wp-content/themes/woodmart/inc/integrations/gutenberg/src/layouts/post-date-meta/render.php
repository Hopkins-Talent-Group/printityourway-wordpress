<?php use XTS\Modules\Layouts\Main;

if ( ! function_exists( 'wd_gutenberg_single_post_date_meta' ) ) {
	function wd_gutenberg_single_post_date_meta( $block_attributes ) {
		$el_id           = wd_get_gutenberg_element_id( $block_attributes );
		$wrapper_classes = wd_get_gutenberg_element_classes( $block_attributes );

		if ( ! empty( $block_attributes['textAlign'] ) || ! empty( $block_attributes['textAlignTablet'] ) || ! empty( $block_attributes['textAlignMobile'] ) ) {
			$wrapper_classes .= ' wd-align';
		}

		ob_start();
		woodmart_enqueue_inline_style( 'post-types-mod-predefined' );

		Main::setup_preview();
		?>
		<div id="<?php echo esc_attr( $el_id ); ?>" class="wd-single-post-date<?php echo esc_attr( $wrapper_classes ); ?>">
			<span class="wd-modified-date">
				<?php woodmart_post_modified_date(); ?>
			</span>

			<span class="wd-post-date wd-style-default">
				<time class="published" datetime="<?php echo get_the_date( 'c' ); // phpcs:ignore ?>">
					<?php echo esc_html( _x( 'On', 'meta-date', 'woodmart' ) ) . ' ' . get_the_date(); ?>
				</time>
			</span>
		</div>
		<?php
		Main::restore_preview();
		return ob_get_clean();
	}
}
