<?php use XTS\Modules\Layouts\Main;

if ( ! function_exists( 'wd_gutenberg_single_post_tags' ) ) {
	function wd_gutenberg_single_post_tags( $block_attributes ) {
		$el_id           = wd_get_gutenberg_element_id( $block_attributes );
		$wrapper_classes = wd_get_gutenberg_element_classes( $block_attributes );

		if ( ! empty( $block_attributes['textAlign'] ) || ! empty( $block_attributes['textAlignTablet'] ) || ! empty( $block_attributes['textAlignMobile'] ) ) {
			$wrapper_classes .= ' wd-align';
		}

		ob_start();

		Main::setup_preview();

		if ( get_the_tag_list() ) : ?>
			<?php woodmart_enqueue_inline_style( 'single-post-el-tags' ); ?>
			<div id="<?php echo esc_attr( $el_id ); ?>" class="wd-single-post-tags-list<?php echo esc_attr( $wrapper_classes ); ?>">
				<div class="wd-tags-list wd-style-1">
					<?php echo wp_kses( get_the_tag_list(), woodmart_get_allowed_html() ); ?>
				</div>
			</div>
			<?php
		endif;

		Main::restore_preview();

		return ob_get_clean();
	}
}
