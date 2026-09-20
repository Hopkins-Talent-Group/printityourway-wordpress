<?php

namespace XTS\Modules\Layouts;

use Automattic\WooCommerce\Internal\Utilities\Users;
use WC_Order;
use WC_Product_Factory;

class Thank_You_Page extends Layout_Type {
	/**
	 * Check.
	 *
	 * @param array  $condition Condition.
	 * @param string $type      Layout type.
	 */
	public function check( $condition, $type = '' ) {
		global $post, $order;

		$order = wc_get_order( get_query_var( 'order-received' ) ); // phpcs:ignore.

		if ( ! is_a( $order, 'WC_Order' ) ) {
			return false;
		}

		$verify_known_shoppers = apply_filters( 'woocommerce_order_received_verify_known_shoppers', true );
		$order_customer_id     = $order->get_customer_id();
		$order_id              = $order->get_id();
		$nonce_is_valid        = wp_verify_nonce( filter_input( INPUT_POST, 'check_submission' ), 'wc_verify_email' );
		$supplied_email        = null;

		if ( $nonce_is_valid ) {
			$supplied_email = sanitize_email( wp_unslash( filter_input( INPUT_POST, 'email' ) ) );
		}

		if ( Users::should_user_verify_order_email( $order_id, $supplied_email, 'order-received' ) || ( $verify_known_shoppers && $order_customer_id && get_current_user_id() !== $order_customer_id ) ) {
			return false;
		}

		return woodmart_is_thank_you_page() || ( wp_is_serving_rest_request() && ! empty( $post ) && 'page' === $post->post_type );
	}

	/**
	 * Override templates.
	 *
	 * @param  string $template  Template.
	 *
	 * @return bool|string
	 */
	public function override_template( $template ) {
		if ( woodmart_woocommerce_installed() && woodmart_is_thank_you_page() && Main::get_instance()->has_custom_layout( 'thank_you_page' ) ) {
			$this->display_template();
			return false;
		}

		return $template;
	}

	/**
	 * Display custom template on the single page.
	 */
	protected function display_template() {
		parent::display_template();
		$this->before_template_content();
		?>
		<div class="woocommerce-order entry-content">
		<?php while ( have_posts() ) : ?>
			<?php the_post(); ?>
			<?php $this->template_content( 'thank_you_page' ); ?>
		<?php endwhile; ?>
		</div>
		<?php
		$this->after_template_content();
	}


	/**
	 * Before template content.
	 */
	public function before_template_content() {
		get_header();
		?>
		<div class="wd-content-area site-content">
		<?php
	}

	/**
	 * Before template content.
	 */
	public function after_template_content() {
		?>
		</div>
		<?php
		get_footer();
	}

	/**
	 * Setup post data.
	 */
	public static function setup_postdata() {
		global $post, $order, $wp_query;

		$products       = wc_get_products( array( 'limit' => 1 ) );
		$random_product = ! empty( $products ) ? $products[0] : false;

		if (
			! Main::is_layout_type( 'thank_you_page' ) ||
			! $random_product ||
			( 'woodmart_layout' !== $post->post_type ) &&
			! is_singular( 'woodmart_layout' ) &&
			! wp_doing_ajax() &&
			( ! isset( $_POST['action'] ) || 'editpost' !== $_POST['action'] ) &&
			( ! defined( 'DOING_AUTOSAVE' ) || ! DOING_AUTOSAVE )
		) {
			return;
		}

		$args = array(
			'limit'      => 1,
			'meta_query' => array( // phpcs:ignore.
				'relation' => 'OR',
				array(
					'key'     => '_refund_amount',
					'compare' => 'NOT EXISTS',
				),
				array(
					'key'     => '_refund_amount',
					'value'   => 0,
					'compare' => '=',
					'type'    => 'NUMERIC',
				),
			),
		);

		$orders       = wc_get_orders( $args );
		$random_order = ! empty( $orders ) ? $orders[0] : false;

		if ( ! $random_order && ! is_a( $order, 'WC_Order' ) ) {
			$dummy_customer_data = array(
				'first_name' => 'John',
				'last_name'  => 'Doe',
				'email'      => 'john.doe@example.com',
				'phone'      => '+1234567890',
				'address_1'  => '123 Dummy Street',
				'address_2'  => '',
				'city'       => 'Dummytown',
				'state'      => 'CA',
				'postcode'   => '12345',
				'country'    => 'US',
			);

			$dummy_order = new WC_Order();
			$dummy_order->add_product( $random_product, 1 );
			$dummy_order->set_address( $dummy_customer_data, 'billing' );
			$dummy_order->set_address( $dummy_customer_data, 'shipping' );
			$dummy_order->set_payment_method( 'cod' );
			$dummy_order->calculate_totals( false );
			$dummy_order->set_status( 'cancelled' );
			$dummy_order->save();

			$random_order = $dummy_order;
		}

		$order            = $random_order; // phpcs:ignore
		$checkout_page_id = wc_get_page_id( 'checkout' );
		if ( $checkout_page_id && get_post_status( $checkout_page_id ) === 'publish' ) {
			$post = get_post( $checkout_page_id ); // phpcs:ignore
			$wp_query->query_vars['order-received'] = $order->get_id();
		}
	}

	/**
	 * Reset post data.
	 */
	public static function reset_postdata() {
		global $order;

		if ( is_singular( 'woodmart_layout' ) || wp_doing_ajax() ) {
			wp_reset_postdata();
			unset( $order );
		}
	}
}

Thank_You_Page::get_instance();
