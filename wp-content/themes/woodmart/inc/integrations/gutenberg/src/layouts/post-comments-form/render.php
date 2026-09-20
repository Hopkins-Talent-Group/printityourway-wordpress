<?php use XTS\Modules\Layouts\Main;

if ( ! function_exists( 'wd_gutenberg_single_post_comments_form' ) ) {
	function wd_gutenberg_single_post_comments_form( $block_attributes ) {
		$wrapper_classes = wd_get_gutenberg_element_classes( $block_attributes );
		$el_id           = wd_get_gutenberg_element_id( $block_attributes );

		if ( ! empty( $block_attributes['textAlign'] ) || ! empty( $block_attributes['textAlignTablet'] ) || ! empty( $block_attributes['textAlignMobile'] ) ) {
			$wrapper_classes .= ' wd-align';
		}

		ob_start();
		Main::setup_preview();
		if ( ! comments_open() || post_password_required() ) {
			return '';
		}

		woodmart_enqueue_inline_style( 'post-types-mod-comments' );
		woodmart_enqueue_inline_style( 'single-post-el-comments' );
		?>
		<div id="<?php echo esc_attr( $el_id ); ?>" class="wd-single-post-comments-form<?php echo esc_attr( $wrapper_classes ); ?>">
			<div id="comments" class="wd-post-comments-form comments-area">
				<?php comment_form( array( 'comment_notes_after' => '' ), get_the_ID() ); ?>
			</div>
		</div>
		<?php
		Main::restore_preview();
		return ob_get_clean();
	}
}
