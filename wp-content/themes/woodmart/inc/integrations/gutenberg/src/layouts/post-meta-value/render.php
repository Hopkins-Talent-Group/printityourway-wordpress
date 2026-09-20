<?php use XTS\Modules\Layouts\Main;

if ( ! function_exists( 'wd_gutenberg_single_post_meta_value' ) ) {
	function wd_gutenberg_single_post_meta_value( $block_attributes ) {
		if ( empty( $block_attributes['postMetaKey'] ) ) {
			return '';
		}

		Main::setup_preview();

		$meta_key = get_post_meta( get_the_ID(), $block_attributes['postMetaKey'], true );

		if ( empty( $meta_key ) ) {
			Main::restore_preview();
			return '';
		}

		$el_id           = wd_get_gutenberg_element_id( $block_attributes );
		$wrapper_classes = wd_get_gutenberg_element_classes( $block_attributes );

		if ( ! empty( $block_attributes['textAlign'] ) || ! empty( $block_attributes['textAlignTablet'] ) || ! empty( $block_attributes['textAlignMobile'] ) ) {
			$wrapper_classes .= ' wd-align';
		}

		ob_start();

		?>
		<div id="<?php echo esc_attr( $el_id ); ?>" class="wd-single-post-meta-value<?php echo esc_attr( $wrapper_classes ); ?>">
			<?php echo get_post_meta( get_the_ID(), $block_attributes['postMetaKey'], true ); // phpcs:ignore. ?>
		</div>
		<?php

		Main::restore_preview();

		return ob_get_clean();
	}
}
