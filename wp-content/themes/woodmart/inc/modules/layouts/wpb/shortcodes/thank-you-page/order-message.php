<?php
/**
 * Order message shortcode.
 *
 * @package Woodmart
 */

use XTS\Modules\Layouts\Main;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'woodmart_shortcode_tp_order_message' ) ) {
	/**
	 * Order message shortcode.
	 *
	 * @param array $settings Shortcode settings.
	 * @return string HTML output.
	 */
	function woodmart_shortcode_tp_order_message( $settings ) {
		$default_settings = array(
			'css'       => '',
			'alignment' => 'left',
		);
		$settings         = wp_parse_args( $settings, $default_settings );

		Main::setup_preview();

		global $order;

		$wrapper_classes  = ' wd-wpb';
		$wrapper_classes .= apply_filters( 'vc_shortcodes_css_class', '', '', $settings );
		$wrapper_classes .= ' text-' . woodmart_vc_get_control_data( $settings['alignment'], 'desktop' );

		if ( $settings['css'] ) {
			$wrapper_classes .= ' ' . vc_shortcode_custom_css_class( $settings['css'] );
		}

		ob_start();

		echo '<div class="wd-el-tp-order-message' . esc_attr( $wrapper_classes ) . '">';
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
