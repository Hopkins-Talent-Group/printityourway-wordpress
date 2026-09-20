<?php
/**
 * Free gifts class.
 *
 * @package woodmart
 */

namespace XTS\Modules\Free_Gifts;

use WC_Cart;
use WC_Product;
use XTS\Admin\Modules\Options;
use XTS\Singleton;
use XTS\Modules\Layouts\Main as Layouts;

/**
 * Free gifts class.
 */
class Main extends Singleton {
	/**
	 * Manager instance.
	 *
	 * @var Manager instanse.
	 */
	public $manager;

	/**
	 * Init.
	 */
	public function init() {
		add_action( 'init', array( $this, 'add_options' ) );

		if ( ! woodmart_woocommerce_installed() || ! woodmart_get_opt( 'free_gifts_enabled', 0 ) || woodmart_get_opt( 'free_gifts_limit', 5 ) < 1 ) {
			add_action( 'woocommerce_after_calculate_totals', array( $this, 'remove_gifts_from_cart' ) );

			return;
		}

		$this->include_files();

		$this->manager = Manager::get_instance();

		add_action( 'wp_ajax_woodmart_add_gift_product', array( $this, 'add_manual_gift_product' ) );
		add_action( 'wp_ajax_nopriv_woodmart_add_gift_product', array( $this, 'add_manual_gift_product' ) );

		add_action( 'woocommerce_before_calculate_totals', array( $this, 'change_price' ) );

		add_action( 'woocommerce_after_calculate_totals', array( $this, 'update_gifts_in_cart' ) );

		add_filter( 'woocommerce_before_mini_cart_contents', array( $this, 'cart_item_price_on_ajax' ) );

		add_filter( 'woocommerce_get_cart_contents', array( $this, 'sorting_cart_contents' ) );
	}

	/**
	 * Add options in theme settings.
	 *
	 * @return void
	 */
	public function add_options() {
		Options::add_field(
			array(
				'id'          => 'free_gifts_enabled',
				'name'        => esc_html__( 'Enable "Free gifts"', 'woodmart' ),
				'hint'        => wp_kses( '<img data-src="' . WOODMART_TOOLTIP_URL . 'free_gifts_enabled.jpg" alt="">', true ),
				'description' => esc_html__( 'Turn on this option to allow customers to receive free gifts with their purchases.', 'woodmart' ),
				'type'        => 'switcher',
				'section'     => 'free_gifts_section',
				'default'     => '0',
				'on-text'     => esc_html__( 'Yes', 'woodmart' ),
				'off-text'    => esc_html__( 'No', 'woodmart' ),
				'priority'    => 10,
				'class'       => 'xts-preset-field-disabled',
			)
		);

		Options::add_field(
			array(
				'id'       => 'free_gifts_limit',
				'name'     => esc_html__( 'Maximum Gifts in an Order', 'woodmart' ),
				'type'     => 'text_input',
				'section'  => 'free_gifts_section',
				'default'  => '5',
				'priority' => 20,
				'class'    => 'xts-preset-field-disabled',
			)
		);

		Options::add_field(
			array(
				'id'          => 'free_gifts_allow_multiple_identical_gifts',
				'name'        => esc_html__( 'Allow adding multiple identical gifts', 'woodmart' ),
				'description' => esc_html__( 'If enabled, the user can add the same product to the cart multiple times. It works if the "Manual Gifts" rule is selected for the gift.', 'woodmart' ),
				'type'        => 'switcher',
				'section'     => 'free_gifts_section',
				'default'     => '0',
				'on-text'     => esc_html__( 'Yes', 'woodmart' ),
				'off-text'    => esc_html__( 'No', 'woodmart' ),
				'priority'    => 25,
				'class'       => 'xts-preset-field-disabled',
			)
		);

		Options::add_field(
			array(
				'id'          => 'free_gifts_price_format',
				'name'        => esc_html__( 'Gift products price display', 'woodmart' ),
				'hint'        => '<video data-src="' . WOODMART_TOOLTIP_URL . 'free_gifts_price_format.mp4" autoplay loop muted></video>',
				'description' => esc_html__( 'Choose how to display the price of gift products, either as "Free" or "$0.00".', 'woodmart' ),
				'type'        => 'buttons',
				'section'     => 'free_gifts_section',
				'options'     => array(
					'text'     => array(
						'name'  => esc_html__( '"Free" text', 'woodmart' ),
						'value' => 'text',
					),
					'discount' => array(
						'name'  => esc_html__( 'Discount to zero', 'woodmart' ),
						'value' => 'discount',
					),
				),
				'default'     => 'text',
				'priority'    => 30,
			)
		);

		Options::add_field(
			array(
				'id'       => 'free_gift_on_cart',
				'name'     => esc_html__( 'Cart', 'woodmart' ),
				'group'    => esc_html__( 'Locations', 'woodmart' ),
				'hint'        => wp_kses( '<img data-src="' . WOODMART_TOOLTIP_URL . 'free_gifts_enabled.jpg" alt="">', true ),
				'type'     => 'switcher',
				'section'  => 'free_gifts_section',
				'default'  => true,
				'on-text'  => esc_html__( 'On', 'woodmart' ),
				'off-text' => esc_html__( 'Off', 'woodmart' ),
				'priority' => 40,
				'class'    => 'xts-col-12',
			)
		);

		Options::add_field(
			array(
				'id'          => 'free_gifts_table_location',
				'name'        => esc_html__( 'Cart free gifts table position', 'woodmart' ),
				'description' => esc_html__( 'Select the placement of the free gifts table on the cart page, either before or after the listed products.', 'woodmart' ),
				'type'        => 'buttons',
				'group'       => esc_html__( 'Locations', 'woodmart' ),
				'section'     => 'free_gifts_section',
				'options'     => array(
					'woocommerce_before_cart_table' => array(
						'name'  => esc_html__( 'Before cart table', 'woodmart' ),
						'value' => 'woocommerce_before_cart_table',
					),
					'woocommerce_after_cart_table'  => array(
						'name'  => esc_html__( 'After cart table', 'woodmart' ),
						'value' => 'woocommerce_after_cart_table',
					),
				),
				'default'     => 'woocommerce_after_cart_table',
				'priority'    => 50,
				'class'       => 'xts-col-12',
				'requires'    => array(
					array(
						'key'     => 'free_gift_on_cart',
						'compare' => 'equals',
						'value'   => true,
					),
				),
			)
		);

		Options::add_field(
			array(
				'id'       => 'free_gift_on_checkout',
				'name'     => esc_html__( 'Checkout', 'woodmart' ),
				'hint'        => wp_kses( '<img data-src="' . WOODMART_TOOLTIP_URL . 'free_gifts_enabled_on_checkout.jpg" alt="">', true ),
				'group'    => esc_html__( 'Locations', 'woodmart' ),
				'type'     => 'switcher',
				'section'  => 'free_gifts_section',
				'default'  => false,
				'on-text'  => esc_html__( 'On', 'woodmart' ),
				'off-text' => esc_html__( 'Off', 'woodmart' ),
				'priority' => 60,
				'class'    => 'xts-col-12',
			)
		);
	}

	/**
	 * Include files.
	 *
	 * @return void
	 */
	public function include_files() {
		$files = array(
			'class-manager',
			'class-admin',
			'class-frontend',
		);

		foreach ( $files as $file ) {
			require_once get_parent_theme_file_path( WOODMART_FRAMEWORK . '/integrations/woocommerce/modules/free-gifts/' . $file . '.php' );
		}
	}

	/**
	 * Add manual gift product.
	 *
	 * @return void
	 */
	public function add_manual_gift_product() {
		$product_id  = ! empty( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
		$is_checkout = ! empty( $_POST['is_checkout'] ) ? boolval( $_POST['is_checkout'] ) : false;

		check_ajax_referer( 'wd_free_gift_' . $product_id, 'security' );

		if ( empty( $product_id ) ) {
			wp_send_json_error(
				array(
					'error' => esc_html__( 'Cannot process action', 'woodmart' ),
				)
			);
		}

		if ( $this->manager->get_gifts_in_cart_count() >= woodmart_get_opt( 'free_gifts_limit', 5 ) ) {
			if ( ! wc_has_notice( $this->manager->get_notices( 'free_gifts_limit' ), 'error' ) ) {
				wc_add_notice( $this->manager->get_notices( 'free_gifts_limit' ), 'error' );
			}

			wp_send_json_error();
		}

		$variation_id = ! empty( $_POST['variation_id'] ) ? absint( $_POST['variation_id'] ) : 0;

		if ( ! empty( $variation_id ) ) {
			$product_id = $variation_id;
		}

		if ( ! woodmart_get_opt( 'free_gifts_allow_multiple_identical_gifts' ) && $this->manager->check_is_gift_in_cart( $product_id ) ) {
			if ( ! wc_has_notice( $this->manager->get_notices( 'already_added' ), 'error' ) ) {
				wc_add_notice( $this->manager->get_notices( 'already_added' ), 'error' );
			}

			wp_send_json_error();
		}

		if ( ! $is_checkout && ! wc_has_notice( $this->manager->get_notices( 'added_successfully' ) ) && wc_get_product( $product_id )->is_in_stock() ) {
			wc_add_notice( $this->manager->get_notices( 'added_successfully' ) );
		}

		WC()->cart->add_to_cart(
			$product_id,
			1,
			0,
			array(),
			array(
				'wd_is_free_gift' => true,
			)
		);

		wp_send_json_success();
	}

	/**
	 * Change price.
	 *
	 * @param WC_Cart $cart_object WC_Cart instance.
	 *
	 * @return void
	 */
	public function change_price( $cart_object ) {
		if ( 0 === $this->manager->get_gifts_in_cart_count() ) {
			return;
		}

		foreach ( $cart_object->get_cart() as $cart_item_key => $cart_item ) {
			if ( ! isset( $cart_item['wd_is_free_gift'] ) ) {
				continue;
			}

			if ( $cart_item['quantity'] > 1 && ! woodmart_get_opt( 'free_gifts_allow_multiple_identical_gifts' ) ) {
				$cart_object->set_quantity( $cart_item_key, 1 );
			}

			$free_gift_product = $cart_item['data'];
			$price             = apply_filters( 'woodmart_free_gift_set_product_cart_price', 0, $cart_item );

			$free_gift_product->set_price( $price );
		}
	}

	/**
	 * When option is disabled we need remove all gifts from cart.
	 *
	 * @param WC_Cart $cart_object WC_Cart instance.
	 *
	 * @return void
	 */
	public function remove_gifts_from_cart( $cart_object ) {
		if ( woodmart_get_opt( 'free_gifts_enabled', 0 ) || did_action( 'woocommerce_after_calculate_totals' ) > 1 ) {
			return;
		}

		$this->remove_all_gifts_from_cart( $cart_object );
	}

	/**
	 * Update gifts in cart. Remove gifts that are no longer eligible to be in the cart. Add automatic gifts.
	 *
	 * @return void
	 */
	public function update_gifts_in_cart() {
		if ( did_action( 'woocommerce_after_calculate_totals' ) > 1 ) {
			return;
		}

		$cart_object = WC()->cart;
		$totals      = $cart_object->get_totals();
		$gifts_rules = $this->manager->get_rules();

		if ( empty( $totals['total'] ) || empty( $gifts_rules ) || ! woodmart_get_opt( 'free_gifts_enabled', 0 ) ) {
			$this->remove_all_gifts_from_cart( $cart_object );
			return;
		}

		$gifts_rules = $this->apply_wpml_to_gift_rules( $gifts_rules );
		$gifts_rules = $this->filter_gift_rules_by_cart_totals( $gifts_rules, $totals );
		$gifts_rules = $this->remove_out_of_stock_gifts( $gifts_rules );

		list( $cart_products, $gift_cart_items ) = $this->split_cart_items( $cart_object );
		list( $allowed_rules, $excluded_rules )  = $this->get_allowed_and_excluded_rules( $gifts_rules, $cart_products );
		$allowed_rules                           = array_diff( $allowed_rules, $excluded_rules );

		list( $should_be_gift_ids, $automatic_gift_ids ) = $this->get_gift_ids_from_rules( $allowed_rules );

		$limit      = (int) woodmart_get_opt( 'free_gifts_limit', 5 );
		$gift_count = $this->remove_unwanted_gifts_from_cart( $cart_object, $gift_cart_items, $should_be_gift_ids, $limit );

		$this->add_automatic_gifts_to_cart( $cart_object, $gift_cart_items, $automatic_gift_ids, $gift_count, $limit );
	}

	/**
	 * Remove all gifts from cart.
	 *
	 * @param WC_Cart $cart_object WC_Cart instance.
	 *
	 * @return void
	 */
	public function remove_all_gifts_from_cart( $cart_object ) {
		foreach ( $cart_object->get_cart() as $cart_item_key => $cart_item ) {
			if ( isset( $cart_item['wd_is_free_gift'] ) ) {
				unset( $cart_object->cart_contents[ $cart_item_key ] );
			}
		}
	}

	/**
	 * Apply WPML to gift rules.
	 *
	 * @param array $gifts_rules Gift rules.
	 *
	 * @return array
	 */
	public function apply_wpml_to_gift_rules( $gifts_rules ) {
		if ( ! defined( 'WCML_VERSION' ) ) {
			return $gifts_rules;
		}

		foreach ( $gifts_rules as $post_id => $rule ) {
			if ( empty( $rule['free_gifts'] ) ) {
				continue;
			}

			foreach ( $rule['free_gifts'] as $key => $free_gift_id ) {
				$gifts_rules[ $post_id ]['free_gifts'][ $key ] = apply_filters( 'wpml_object_id', $free_gift_id, 'product', true, apply_filters( 'wpml_current_language', null ) );
			}
		}

		return $gifts_rules;
	}

	/**
	 * Filter gift rules by cart totals.
	 *
	 * @param array $gifts_rules Gift rules.
	 * @param array $totals Cart totals.
	 *
	 * @return array
	 */
	public function filter_gift_rules_by_cart_totals( $gifts_rules, $totals ) {
		return array_filter(
			$gifts_rules,
			function ( $rule ) use ( $totals ) {
				$cart_price = $totals['subtotal'];

				if ( isset( $rule['free_gifts_cart_price_type'] ) ) {
					switch ( $rule['free_gifts_cart_price_type'] ) {
						case 'subtotal':
							$cart_price = $totals['subtotal'];
							break;
						case 'subtotal_after_discount':
							$cart_price = $totals['subtotal'] - $totals['discount_total'];
							break;
						case 'total':
							$cart_price = $totals['total'];
							break;
						default:
							$cart_price = $totals['subtotal'];
							break;
					}
				}

				return ! empty( $rule['free_gifts'] ) && $this->manager->check_free_gifts_totals( $rule, $cart_price );
			}
		);
	}

	/**
	 * Remove out of stock gifts from rules.
	 *
	 * @param array $gifts_rules Gift rules.
	 *
	 * @return array
	 */
	public function remove_out_of_stock_gifts( $gifts_rules ) {
		return array_map(
			function ( $rule ) {
				foreach ( $rule['free_gifts'] as $key => $gifts_id ) {
					$rule['free_gifts'][ $key ] = intval( $gifts_id );

					if ( ! ( wc_get_product( $gifts_id ) )->is_in_stock() ) {
						unset( $rule['free_gifts'][ array_search( $gifts_id, $rule['free_gifts'], true ) ] );
					}
				}
				return $rule;
			},
			$gifts_rules
		);
	}

	/**
	 * Split cart items into products and gift items.
	 *
	 * @param WC_Cart $cart_object Cart object.
	 *
	 * @return array
	 */
	public function split_cart_items( $cart_object ) {
		$cart_products   = array();
		$gift_cart_items = array();

		foreach ( $cart_object->get_cart() as $cart_item_key => $cart_item ) {
			if ( isset( $cart_item['wd_is_free_gift'] ) ) {
				$gift_cart_items[ $cart_item_key ] = $cart_item;
			} else {
				$cart_products[] = $cart_item['data'];
			}
		}
		return array( $cart_products, $gift_cart_items );
	}

	/**
	 * Get allowed and excluded rules.
	 *
	 * @param array $gifts_rules Gift rules.
	 * @param array $cart_products Cart products.
	 *
	 * @return array
	 */
	public function get_allowed_and_excluded_rules( $gifts_rules, $cart_products ) {
		$allowed_rules  = array();
		$excluded_rules = array();

		foreach ( $gifts_rules as $rule_id => $rule ) {
			$rule_allowed = false;

			foreach ( $cart_products as $product ) {
				if ( $this->manager->check_free_gifts_condition( $rule, $product ) ) {
					$rule_allowed = true;
				} elseif ( ! empty( $rule['free_gifts_strict_exclude_mode'] ) ) {
					$excluded_rules[] = $rule_id;
					break;
				}
			}

			if ( $rule_allowed ) {
				$allowed_rules[] = $rule_id;
			}
		}
		return array( $allowed_rules, $excluded_rules );
	}

	/**
	 * Get gift ids from rules.
	 *
	 * @param array $allowed_rules Allowed rules.
	 *
	 * @return array
	 */
	public function get_gift_ids_from_rules( $allowed_rules ) {
		$should_be_gift_ids = array();
		$automatic_gift_ids = array();

		foreach ( $allowed_rules as $rule_id ) {
			$rule = $this->manager->get_single_post_rules( $rule_id );

			if ( ! empty( $rule['free_gifts'] ) && is_array( $rule['free_gifts'] ) ) {
				$rule['free_gifts'] = array_map( 'intval', $rule['free_gifts'] );
			}

			$should_be_gift_ids = array_merge( $should_be_gift_ids, $rule['free_gifts'] );

			if ( 'automatic' === $rule['free_gifts_rule_type'] ) {
				$automatic_gift_ids = array_merge( $automatic_gift_ids, $rule['free_gifts'] );
			}
		}

		return array( array_unique( $should_be_gift_ids ), array_unique( $automatic_gift_ids ) );
	}

	/**
	 * Remove unwanted gifts from cart.
	 *
	 * @param WC_Cart $cart_object Cart object.
	 * @param array   $gift_cart_items Gift cart items.
	 * @param array   $should_be_gift_ids Should be gift ids.
	 * @param int     $limit Limit of gifts.
	 *
	 * @return int
	 */
	public function remove_unwanted_gifts_from_cart( $cart_object, $gift_cart_items, $should_be_gift_ids, $limit ) {
		$gift_count = 0;

		foreach ( $gift_cart_items as $cart_item_key => $cart_item ) {
			$gift_id = $cart_item['data']->get_id();

			if (
				! in_array( $gift_id, $should_be_gift_ids, true ) ||
				! $cart_item['data']->is_in_stock() ||
				++$gift_count > $limit
			) {
				unset( $cart_object->cart_contents[ $cart_item_key ] );
			}
		}

		return $gift_count;
	}

	/**
	 * Add automatic gifts to cart.
	 *
	 * @param WC_Cart $cart_object Cart object.
	 * @param array   $gift_cart_items Gift cart items.
	 * @param array   $automatic_gift_ids Automatic gift ids.
	 * @param int     $gift_count Current gift count.
	 * @param int     $limit Limit of gifts.
	 *
	 * @return void
	 */
	public function add_automatic_gifts_to_cart( $cart_object, $gift_cart_items, $automatic_gift_ids, $gift_count, $limit ) {
		$current_gift_ids = array_map(
			function ( $item ) {
				return $item['data']->get_id();
			},
			$gift_cart_items
		);

		foreach ( $automatic_gift_ids as $gift_id ) {
			if ( $gift_count >= $limit ) {
				break;
			}

			if ( ! in_array( $gift_id, $current_gift_ids, true ) ) {
				$cart_object->add_to_cart(
					$gift_id,
					1,
					0,
					array(),
					array(
						'wd_is_free_gift'           => true,
						'wd_is_free_gift_automatic' => true,
					)
				);

				++$gift_count;
			}
		}
	}

	/**
	 * Gets sorted cart contents.
	 *
	 * @param array $cart_contents List of cart items.
	 *
	 * @return array
	 */
	public function sorting_cart_contents( $cart_contents ) {
		uasort( $cart_contents, array( $this, 'sort_data' ) );

		return $cart_contents;
	}

	/**
	 * Sort the products so that gifts are at the end of the list.
	 *
	 * @param array $a First array.
	 * @param array $b Next array.
	 *
	 * @return int
	 */
	private function sort_data( $a, $b ) {
		$a_is_gift = isset( $a['wd_is_free_gift'] );
		$b_is_gift = isset( $b['wd_is_free_gift'] );

		if ( ( $a_is_gift && $b_is_gift ) || ( ! $a_is_gift && ! $b_is_gift ) ) {
			return 0;
		}

		return ! $a_is_gift ? -1 : 1;
	}

	/**
	 * Update price in mini cart on get_refreshed_fragments action.
	 *
	 * @codeCoverageIgnore
	 * @return void
	 */
	public function cart_item_price_on_ajax() {
		if ( apply_filters( 'woodmart_do_not_recalulate_total_on_get_refreshed_fragments', false ) ) {
			return;
		}

		if ( wp_doing_ajax() && ! empty( $_GET['wc-ajax'] ) && 'get_refreshed_fragments' === $_GET['wc-ajax'] ) { // phpcs:ignore.
			WC()->cart->calculate_totals();
			WC()->cart->set_session();
			WC()->cart->maybe_set_cart_cookies();
		}
	}
}

Main::get_instance();
