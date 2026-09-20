<?php use XTS\Modules\Layouts\Main;

if ( ! function_exists( 'wd_gutenberg_tp_customer_details' ) ) {
	function wd_gutenberg_tp_customer_details( $block_attributes ) {
		$wrapper_classes = wd_get_gutenberg_element_classes( $block_attributes );
		$el_id           = wd_get_gutenberg_element_id( $block_attributes );
		$is_builder      = 'woodmart_layout' === get_post_type();

		if ( ! empty( $block_attributes['textAlign'] ) || ! empty( $block_attributes['textAlignTablet'] ) || ! empty( $block_attributes['textAlignMobile'] ) ) {
			$wrapper_classes .= ' wd-align';
		}

		Main::setup_preview();
		global $order;

		ob_start();

		if ( $order || is_a( $order, 'WC_Order' ) ) {
			echo '<div id="' . esc_attr( $el_id ) . '" class="wd-el-tp-customer-details' . esc_attr( $wrapper_classes ) . '">';
			$show_customer_details = $is_builder || $order->get_user_id() === get_current_user_id();
			if ( $show_customer_details ) {
				wc_get_template( 'order/order-details-customer.php', array( 'order' => $order ) );
			}
			echo '</div>';
		}

		Main::restore_preview();
		return ob_get_clean();
	}
}
