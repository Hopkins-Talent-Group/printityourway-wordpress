<?php use XTS\Modules\Layouts\Main;

if ( ! function_exists( 'wd_gutenberg_tp_order_message' ) ) {
	function wd_gutenberg_tp_order_message( $block_attributes ) {
		$wrapper_classes = wd_get_gutenberg_element_classes( $block_attributes );
		$el_id           = wd_get_gutenberg_element_id( $block_attributes );

		Main::setup_preview();
		global $order;

		if ( ! empty( $block_attributes['textAlign'] ) || ! empty( $block_attributes['textAlignTablet'] ) || ! empty( $block_attributes['textAlignMobile'] ) ) {
			$wrapper_classes .= ' wd-align';
		}

		ob_start();

		echo '<div id="' . esc_attr( $el_id ) . '" class="wd-el-tp-order-message' . esc_attr( $wrapper_classes ) . '">';
		if ( ! $order || ! is_a( $order, 'WC_Order' ) ) :
			wc_get_template( 'checkout/order-received.php', array( 'order' => false ) );
		elseif ( $order->has_status( 'failed' ) ) : ?>
			<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed"><?php esc_html_e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce' ); ?></p>
			<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed-actions">
				<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="button pay"><?php esc_html_e( 'Pay', 'woocommerce' ); ?></a>
				<?php if ( is_user_logged_in() ) : ?>
					<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="button pay"><?php esc_html_e( 'My account', 'woocommerce' ); ?></a>
				<?php endif; ?>
			</p>
		<?php else : ?>
			<?php	wc_get_template( 'checkout/order-received.php', array( 'order' => $order ) ); ?>
			<?php
		endif;
		echo '</div>';

		Main::restore_preview();
		return ob_get_clean();
	}
}
