<?php

use XTS\Gutenberg\Blocks_Assets;
use XTS\Gutenberg\Post_CSS;
use XTS\Modules\Layouts\Main;

if ( ! function_exists( 'wd_gutenberg_shop_archive_active_filter' ) ) {
	function wd_gutenberg_shop_archive_active_filter( $block_attributes ) {
		if ( ! woodmart_woocommerce_installed() ) {
			return '';
		}

		ob_start();
		woodmart_get_active_filters();
		$active_filters_content = ob_get_clean();

		ob_start();

		Main::setup_preview();

		if ( ! empty( $active_filters_content ) ) {
		?>
			<div id="<?php echo esc_attr( wd_get_gutenberg_element_id( $block_attributes ) ); ?>" class="wd-shop-active-filters<?php echo esc_attr( wd_get_gutenberg_element_classes( $block_attributes ) ); ?>">
				<?php echo $active_filters_content; // phpcs:ignore. ?>
			</div>
		<?php
		}

		Main::restore_preview();

		return ob_get_clean();
	}
}
