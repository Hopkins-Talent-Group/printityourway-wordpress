<?php use XTS\Modules\Layouts\Main;

if ( ! function_exists( 'wd_gutenberg_single_post_title' ) ) {
	function wd_gutenberg_single_post_title( $block_attributes ) {
		$el_id           = wd_get_gutenberg_element_id( $block_attributes );
		$wrapper_classes = wd_get_gutenberg_element_classes( $block_attributes );

		if ( ! empty( $block_attributes['textAlign'] ) || ! empty( $block_attributes['textAlignTablet'] ) || ! empty( $block_attributes['textAlignMobile'] ) ) {
			$wrapper_classes .= ' wd-align';
		}

		Main::setup_preview();
		$title = get_the_title();
		Main::restore_preview();

		if ( ! $title ) {
			return '';
		}

		ob_start();
		woodmart_enqueue_inline_style( 'post-types-mod-predefined' );
		?>
		<div id="<?php echo esc_attr( $el_id ); ?>" class="wd-single-post-title<?php echo esc_attr( $wrapper_classes ); ?>">
			<<?php echo esc_attr( $block_attributes['htmlTag'] ); ?> class="wd-post-title wd-entities-title entry-title title">
				<?php echo wp_kses_post( $title ); ?>
			</<?php echo esc_attr( $block_attributes['htmlTag'] ); ?>>
		</div>
		<?php
		return ob_get_clean();
	}
}
