<?php use XTS\Modules\Layouts\Main;

if ( ! function_exists( 'wd_gutenberg_single_post_content' ) ) {
	function wd_gutenberg_single_post_content( $block_attributes ) {
		if ( wp_is_serving_rest_request() ) {
			return '';
		}

		$wrapper_classes  = ' wd-entry-content';
		$wrapper_classes .= wd_get_gutenberg_element_classes( $block_attributes );
		$el_id            = wd_get_gutenberg_element_id( $block_attributes );

		Main::setup_preview();
		$content = get_the_content();

		if ( ! $content ) {
			Main::restore_preview();
			return '';
		}

		ob_start();
		?>
		<div id="<?php echo esc_attr( $el_id ); ?>" class="wd-single-post-content<?php echo esc_attr( $wrapper_classes ); ?>">
			<?php
			echo apply_filters('the_content', $content); //phpcs:ignore.

			wp_link_pages(
				array(
					'before'      => '<div class="page-links"><span class="page-links-title">' . esc_html__( 'Pages:', 'woodmart' ) . '</span>',
					'after'       => '</div>',
					'link_before' => '<span>',
					'link_after'  => '</span>',
				)
			);
			?>
		</div>
		<?php

		Main::restore_preview();
		return ob_get_clean();
	}
}
