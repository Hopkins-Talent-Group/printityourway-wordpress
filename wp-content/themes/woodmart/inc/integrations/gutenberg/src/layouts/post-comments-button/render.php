<?php use XTS\Modules\Layouts\Main;

if ( ! function_exists( 'wd_gutenberg_single_post_comments_btn' ) ) {
	function wd_gutenberg_single_post_comments_btn( $block_attributes ) {
		$wrapper_classes = wd_get_gutenberg_element_classes( $block_attributes );
		$el_id           = wd_get_gutenberg_element_id( $block_attributes );

		if ( ! empty( $block_attributes['textAlign'] ) || ! empty( $block_attributes['textAlignTablet'] ) || ! empty( $block_attributes['textAlignMobile'] ) ) {
			$wrapper_classes .= ' wd-align';
		}

		ob_start();

		Main::setup_preview();

		if ( comments_open() || pings_open() ) : ?>
			<?php woodmart_enqueue_inline_style( 'blog-mod-comments-button' ); ?>
			<div id="<?php echo esc_attr( $el_id ); ?>" class="wd-single-post-reply<?php echo esc_attr( $wrapper_classes ); ?>">
				<div class="wd-post-reply wd-style-1">
					<?php woodmart_post_meta_reply(); ?>
				</div>
			</div>
			<?php
		endif;

		Main::restore_preview();

		return ob_get_clean();
	}
}
