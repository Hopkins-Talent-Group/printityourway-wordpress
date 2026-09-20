<?php use XTS\Modules\Layouts\Main;

if ( ! function_exists( 'wd_gutenberg_tp_payment_instructions' ) ) {
	function wd_gutenberg_tp_payment_instructions( $block_attributes ) {
		$wrapper_classes = wd_get_gutenberg_element_classes( $block_attributes );
		$el_id           = wd_get_gutenberg_element_id( $block_attributes );

		Main::setup_preview();
		global $order;

		ob_start();

		if ( $order && is_a( $order, 'WC_Order' ) ) {
			$payment_method = $order->get_payment_method();
			echo '<div id="' . esc_attr( $el_id ) . '" class="wd-el-tp-payment-instructions' . esc_attr( $wrapper_classes ) . '">';
			do_action( 'woocommerce_thankyou_' . $payment_method, $order->get_id() );
			echo '</div>';
		}

		Main::restore_preview();
		return ob_get_clean();
	}
}
