<?php use XTS\Modules\Layouts\Main;

if ( ! function_exists( 'wd_gutenberg_single_post_navigation' ) ) {
	function wd_gutenberg_single_post_navigation( $block_attributes ) {
		$el_id           = wd_get_gutenberg_element_id( $block_attributes );
		$wrapper_classes = wd_get_gutenberg_element_classes( $block_attributes );

		ob_start();

		Main::setup_preview();
		?>
		<div id="<?php echo esc_attr( $el_id ); ?>" class="wd-single-post-nav<?php echo esc_attr( $wrapper_classes ); ?>">
			<?php woodmart_posts_navigation(); ?>
		</div>
		<?php
		Main::restore_preview();

		return ob_get_clean();
	}
}
