<?php use XTS\Modules\Layouts\Main;

if ( ! function_exists( 'wd_gutenberg_single_post_author_meta' ) ) {
	function wd_gutenberg_single_post_author_meta( $block_attributes ) {
		$default_attributes = array(
			'avatar_width'  => '',
			'alignment'     => 'left',
			'author_label'  => '1',
			'author_avatar' => '1',
			'author_name'   => '1',
		);

		$block_attributes = array_merge( $default_attributes, $block_attributes );
		$el_id            = wd_get_gutenberg_element_id( $block_attributes );
		$wrapper_classes  = wd_get_gutenberg_element_classes( $block_attributes );

		ob_start();

		Main::setup_preview();

		if ( ! empty( $block_attributes['textAlign'] ) || ! empty( $block_attributes['textAlignTablet'] ) || ! empty( $block_attributes['textAlignMobile'] ) ) {
			$wrapper_classes .= ' wd-align';
		}

		$author_label  = ! empty( $block_attributes['author_label'] ) ? 'long' : '';
		$author_avatar = ! empty( $block_attributes['author_avatar'] );
		$author_name   = ! empty( $block_attributes['author_name'] );

		if ( ! $author_label && ! $author_avatar && ! $author_name ) {
			Main::restore_preview();
			return '';
		}

		$avatar_size = $block_attributes['avatar_width'];

		woodmart_enqueue_inline_style( 'blog-mod-author' );
		?>
		<div id="<?php echo esc_attr( $el_id ); ?>" class="wd-single-post-author<?php echo esc_attr( $wrapper_classes ); ?>">
			<div class="wd-post-author">
				<?php woodmart_post_meta_author( $author_avatar, $author_label, $author_name, $avatar_size ? $avatar_size : 22 ); ?>
			</div>
		</div>
		<?php

		Main::restore_preview();

		return ob_get_clean();
	}
}
