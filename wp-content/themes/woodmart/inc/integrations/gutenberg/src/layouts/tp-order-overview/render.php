<?php use XTS\Modules\Layouts\Main;

if ( ! function_exists( 'wd_gutenberg_tp_order_overview' ) ) {
	function wd_gutenberg_tp_order_overview( $block_attributes ) {
		$wrapper_classes = wd_get_gutenberg_element_classes( $block_attributes );
		$el_id           = wd_get_gutenberg_element_id( $block_attributes );

		if ( ! empty( $block_attributes['textAlign'] ) || ! empty( $block_attributes['textAlignTablet'] ) || ! empty( $block_attributes['textAlignMobile'] ) ) {
			$wrapper_classes .= ' wd-align';
		}

		Main::setup_preview();
		global $order;

		ob_start();

		if ( $order || is_a( $order, 'WC_Order' ) ) {
			echo '<div id="' . esc_attr( $el_id ) . '" class="wd-el-tp-order-overview' . esc_attr( $wrapper_classes ) . '">';
			woodmart_order_overview( $order );
			echo '</div>';
		}

		Main::restore_preview();
		return ob_get_clean();
	}
}
