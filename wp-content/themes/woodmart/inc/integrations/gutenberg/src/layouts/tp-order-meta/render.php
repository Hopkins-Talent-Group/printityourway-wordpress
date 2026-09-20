<?php use XTS\Modules\Layouts\Main;

if ( ! function_exists( 'wd_gutenberg_tp_order_meta' ) ) {
	function wd_gutenberg_tp_order_meta( $block_attributes ) {
		global $order;

		Main::setup_preview();

		$el_id           = wd_get_gutenberg_element_id( $block_attributes );
		$wrapper_classes = wd_get_gutenberg_element_classes( $block_attributes );

		if ( ! $order || ! $block_attributes['orderData'] ) {
			return '';
		}

		if ( ! empty( $block_attributes['textAlign'] ) || ! empty( $block_attributes['textAlignTablet'] ) || ! empty( $block_attributes['textAlignMobile'] ) ) {
			$wrapper_classes .= ' wd-align';
		}

		ob_start();

		?>
		<div id="<?php echo esc_attr( $el_id ); ?>" class="wd-tp-order-meta<?php echo esc_attr( $wrapper_classes ); ?>">
			<?php
			switch ( $block_attributes['orderData'] ) {
				case 'order_id':
					echo esc_html( $order->get_id() );
					break;
				case 'order_date':
					echo esc_html( $order->get_date_created()->date_i18n( wc_date_format() ) );
					break;
				case 'order_status':
					echo esc_html( wc_get_order_status_name( $order->get_status() ) );
					break;
				case 'order_email':
					echo esc_html( $order->get_billing_email() );
					break;
				case 'order_total':
					echo wp_kses_post( wc_price( $order->get_total() ) );
					break;
				case 'payment_method':
					echo esc_html( $order->get_payment_method_title() );
					break;
				case 'shipping_method':
					echo esc_html( $order->get_shipping_method() );
					break;
			}

			if ( 'custom' === $block_attributes['orderData'] && ! empty( $block_attributes['orderMetaKey'] ) && strpos( $block_attributes['orderMetaKey'], '_' ) !== 0 ) {
				// Custom fields order meta.
				echo $order->get_meta( $block_attributes['orderMetaKey'] ); // phpcs:ignore.
			}

			?>
		</div>
		<?php

		Main::restore_preview();

		return ob_get_clean();
	}
}
