<?php use XTS\Modules\Layouts\Main;

if ( ! function_exists( 'wd_gutenberg_author_bio' ) ) {
	function wd_gutenberg_author_bio( $block_attributes ) {
		if ( woodmart_is_blog_archive() && ( ! is_author() || 'woodmart_layout' === get_post_type() ) ) {
			return '';
		}

		$wrapper_classes = wd_get_gutenberg_element_classes( $block_attributes );
		$el_id           = wd_get_gutenberg_element_id( $block_attributes );

		if ( ! empty( $block_attributes['textAlign'] ) || ! empty( $block_attributes['textAlignTablet'] ) || ! empty( $block_attributes['textAlignMobile'] ) ) {
			$wrapper_classes .= ' wd-align';
		}

		ob_start();

		Main::setup_preview();

		?>
		<div id="<?php echo esc_attr( $el_id ); ?>" class="wd-single-post-author-bio<?php echo esc_attr( $wrapper_classes ); ?>">
			<?php get_template_part( 'author-bio' ); ?>
		</div>
		<?php

		Main::restore_preview();

		return ob_get_clean();
	}
}
