<?php use XTS\Modules\Layouts\Main;

if ( ! function_exists( 'wd_gutenberg_tp_order_details' ) ) {
	function wd_gutenberg_tp_order_details( $block_attributes ) {
		$wrapper_classes = wd_get_gutenberg_element_classes( $block_attributes );
		$el_id           = wd_get_gutenberg_element_id( $block_attributes );

		Main::setup_preview();
		global $order;
		ob_start();

		if ( $order || is_a( $order, 'WC_Order' ) ) {
			$downloads = $order->get_downloadable_items();

			echo '<div id="' . esc_attr( $el_id ) . '" class="wd-el-tp-order-details' . esc_attr( $wrapper_classes ) . '">';

			if ( $downloads ) {
				wc_get_template(
					'order/order-downloads.php',
					array(
						'downloads'  => $downloads,
						'show_title' => true,
					)
				);
			}

			woodmart_order_details( $order );

			echo '</div>';
		}

		Main::restore_preview();
		return ob_get_clean();
	}
}
