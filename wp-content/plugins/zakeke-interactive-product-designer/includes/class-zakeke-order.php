<?php

if ( ! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class Zakeke_Order {
    static private $_checkedOrders = [];

	/**
	 * Setup class.
	 */
	public static function init() {
		add_filter('woocommerce_checkout_create_order_line_item_object', array(__CLASS__, 'create_order_line_item_object'), 20, 4);
		add_action('woocommerce_new_order_item', array(__CLASS__, 'new_order_item'), 20, 3);
		add_action('woocommerce_order_item_get_formatted_meta_data', array(__CLASS__, 'order_item_get_formatted_meta_data'), 10, 2);
		add_action('woocommerce_thankyou', array(__CLASS__, 'new_order'), 20);
		add_action('woocommerce_order_status_processing', array(__CLASS__, 'new_order'));
		add_action('woocommerce_before_order_object_save', array(__CLASS__, 'update_order'));

		add_action('woocommerce_order_item_meta_start', array(__CLASS__, 'order_item_meta_start'), 20, 3);

		add_filter('woocommerce_order_item_get_formatted_meta_data', array(__CLASS__, 'api_order_item_get_formatted_meta_data'), 10, 2);

		add_filter('woocommerce_order_again_cart_item_data', array(__CLASS__, 'order_again_cart_item_data'), 10, 3);
	}

	/**
	 * Add the Zakeke data from the cart item to the order item.
	 *
	 * @param WC_Order_Item $line_item
	 * @param string $cart_item_key
	 * @param array $values
	 * @param WC_Order $order
	 * @return WC_Order_Item
	 */
	public static function create_order_line_item_object( $line_item, $cart_item_key, $values, $order) {
		if (isset($values['zakeke_data'])) {
            $line_item["zakeke_data"] = $values['zakeke_data'];
		} elseif (isset($values['zakeke_configurator_data'])) {
            $line_item["zakeke_configurator_data"] = $values['zakeke_configurator_data'];
		}

		return $line_item;
	}

	/**
	 * Add Zakeke meta to the order item.
	 *
	 * @param int $item_id
	 * @param WC_Order_Item $item
	 * @param int $order_id
	 * @throws Exception
	 */
	public static function new_order_item( $item_id, $item, $order_id) {
		if (isset($item["zakeke_data"])) {
			wc_add_order_item_meta($item_id, 'zakeke_data', $item["zakeke_data"]);
		} elseif (isset($item["zakeke_configurator_data"])) {
			$zakeke_configurator_data = array(
				'composition' => $item["zakeke_configurator_data"]['composition'],
				'preview'     => $item["zakeke_configurator_data"]['preview']
			);

			if (isset($item["zakeke_configurator_data"]['additional_properties'])) {
				$zakeke_configurator_data['additional_properties'] = $item["zakeke_configurator_data"]['additional_properties'];
			}

			wc_add_order_item_meta( $item_id, 'zakeke_configurator_data', $zakeke_configurator_data );
		}
	}

	public static function order_item_get_formatted_meta_data( $formatted_meta, $order_item ) {
		$zakeke_data = $order_item->get_meta( 'zakeke_configurator_data' );
		if ( $zakeke_data ) {
			$webservice = new Zakeke_Webservice();
			try {
				$info = $webservice->configurator_cart_info($zakeke_data['composition'], 1);
				foreach ($info['items'] as $item) {
					if (strpos($item['attributeCode'], 'zakekePlatform') !== false) {
						continue;
					}
					$formatted_meta[$item['itemGuid']] = (object) array(
						'key' => $item['attributeName'],
						'value' => $item['selectedOptionName'],
						'display_key' => $item['attributeName'],
						'display_value' => wpautop($item['selectedOptionName']),
					);
				}
			} catch (Exception $e) {
				return $formatted_meta;
			}
		}

		return $formatted_meta;
	}

	/**
	 * Check for order updates.
	 *
	 * @param WC_Order $order
	 */
	public static function update_order( $order) {
		if ($order->has_status('processing') || $order->has_status('completed')) {
            if ($order->get_id() && in_array($order->get_id(), self::$_checkedOrders)) {
                return;
            }

			self::new_order($order->get_id());
		}
	}

	public static function new_order( $order_id) {
		$order = wc_get_order($order_id);

        if ($order->get_meta('zakeke_placed_order') && !isset($_REQUEST['zakeke_force'])) {
            return;
        }

        self::$_checkedOrders[] = $order_id;

		$data = array(
			'orderCode' => $order_id,
			'ecommerceOrderNumber' => $order->get_order_number(),
			'sessionID' => get_current_user_id(),
			'total' => $order->get_total(),
			'orderStatusID' => 1,
			'details' => array(),
			'compositionDetails' => array()
		);

		if (!empty($order->get_billing_email())) {
			$data['email'] = $order->get_billing_email();
		}

		if ($order->has_shipping_address()) {
			$states = WC()->countries->get_states($order->get_shipping_country());

			$data['shippingAddress'] = array(
				'firstName' => $order->get_shipping_first_name(),
				'lastName' => $order->get_shipping_last_name(),
				'city' => $order->get_shipping_city(),
				'zip' => $order->get_shipping_postcode(),
				'provinceCode' => $order->get_shipping_state(),
				'province' => !empty($states[$order->get_shipping_state()]) ? $states[$order->get_shipping_state()] : null,
				'countryCode' => $order->get_shipping_country(),
				'country' => !empty(WC()->countries->countries[$order->get_shipping_country()]) ? WC()->countries->countries[$order->get_shipping_country()] : null,
				'address1' => $order->get_shipping_address_1(),
				'address2' => $order->get_shipping_address_2(),
				'company' => $order->get_shipping_company(),
                'phone' => $order->get_billing_phone()
			);
		}

		$guestCode = zakeke_guest_code();
		if ($order->get_customer_id() > 0) {
			$data['customerID'] = $order->get_customer_id();
		} elseif ($guestCode) {
			$data['visitorID'] = $guestCode;
		}

		foreach ($order->get_items('line_item') as $order_item_id => $item) {
			$product = $item->get_product();
			if (!$product) {
				continue;
			}

			$zakeke_data = $item->get_meta('zakeke_data');
			if ($zakeke_data) {
				$quantity = max(1, absint($item->get_quantity()));

				$retailPrice = max(0, $item->get_total() + $item->get_total_tax());
				if ($retailPrice > 0) {
					$retailPrice = $retailPrice / $quantity;
				}

                $order_item_total = max(0, $item->get_total());
                $order_item_total = apply_filters( 'zakeke_order_item_total', $order_item_total, $item );

				$modelUnitPrice = 0;
				if ($order_item_total > 0) {
					$modelUnitPrice = $order_item_total / $quantity;
				}

				$item_data = array(
					'designDocID' => $zakeke_data['design'],
					'orderDetailCode' => $order_item_id,
					'sku' => is_object($product) ? $product->get_sku() : null,
					'variantCode' => strval($item->get_variation_id() ? $item->get_variation_id() : $item->get_product_id()),
					'quantity' => $quantity,
					'designUnitPrice' => 0,
					'modelUnitPrice' => $modelUnitPrice,
					'retailPrice' => $retailPrice
				);

                if (isset($zakeke_data['modificationID'])) {
	                $item_data['designModificationID'] = $zakeke_data['modificationID'];
                }

				$data['details'][] = $item_data;
			}

			$zakeke_data = $item->get_meta('zakeke_configurator_data');
			if ($zakeke_data) {
				$order_item_total = $order->get_line_total( $item, false, false );
				$order_item_total = apply_filters( 'zakeke_order_item_total', $order_item_total, $item );

				$quantity = max(1, absint($item->get_quantity()));

                $unitPrice = 0;
                if ($order_item_total > 0) {
                    $unitPrice = $order_item_total / $quantity;
                }

				$item_data = array(
					'composition' => $zakeke_data['composition'],
					'orderDetailCode' => $order_item_id,
					'quantity' => $quantity,
					'unitPrice' => $unitPrice
				);

				$data['compositionDetails'][] = $item_data;
			}
		}

		if (count($data['details']) > 0 || count($data['compositionDetails']) > 0) {
			$webservice = new Zakeke_Webservice();
			try {
				zakeke_retry(function () use ( $webservice, $data) {
					$webservice->place_order($data);
				});

                $order->add_meta_data('zakeke_placed_order', 'true', true);
                $order->save();
			} catch (Exception $e) {
				return;
			}
		}
	}

	public static function order_item_meta_start( $item_id, $item, $order) {
		$zakeke_data = $item->get_meta('zakeke_data');
		if ($zakeke_data) {
			$integration = new Zakeke_Integration();

			?>
			<ul class="wc-item-meta">
				<li><strong class="wc-item-meta-label"><?php esc_html_e('Customization', 'zakeke'); ?>:</strong>
                    <?php if (count($zakeke_data['previews']) > 1 && 'yes' === $integration->show_all_sides) : ?>
                        <div>
                            <?php array_shift($zakeke_data['previews']); ?>
                            <?php foreach ($zakeke_data['previews'] as $preview): ?>
                                <img style="display:inline" src="<?php echo esc_url($preview->url); ?>" width="150" />
                            <?php endforeach; ?>
                        </div>
					<?php elseif (count($zakeke_data['previews']) >= 3) : ?>
						<div>
							<img style="display:inline" src="<?php echo esc_url($zakeke_data['previews'][1]->url); ?>" width="150" />
							<img style="display:inline" src="<?php echo esc_url($zakeke_data['previews'][2]->url); ?>" width="150" />
						</div>
					<?php elseif (count($zakeke_data['previews']) >= 2) : ?>
						<img src="<?php echo esc_url($zakeke_data['previews'][1]->url); ?>" width="150" />
					<?php else : ?>
						<img src="<?php echo esc_url($zakeke_data['previews'][0]->url); ?>" width="150" />
					<?php endif ?>
				</li>
				<?php if ($zakeke_data['price_tax'] > 0.0 && 'yes' === $integration->show_price_in_cart ) : ?>
					<li><strong class="wc-item-meta-label"><?php esc_html_e('Customization Price', 'zakeke'); ?>
							:</strong> <?php echo wc_price($zakeke_data['price_tax']); ?></li>
				<?php endif ?>
			</ul>
			<?php
		} else {
			$zakeke_data = $item->get_meta('zakeke_configurator_data');
			if ($zakeke_data) {
				?>
				<ul class="wc-item-meta">
					<li>
						<img src="<?php echo esc_url($zakeke_data['preview']); ?>" width="150" />
					</li>
				</ul>
				<?php
			}
		}
	}

	/**
	 * Add Zakeke meta to the order line items
	 *
	 * @param array $formatted_meta
	 * @param WC_Order_Item $order_item
	 * @return array
	 */
	public static function api_order_item_get_formatted_meta_data( $formatted_meta, $order_item) {
		if (defined('WC_API_REQUEST') && WC_API_REQUEST) {
			foreach ($order_item->get_meta_data() as $meta_id => $meta) {
				if ('zakeke_data' !== $meta->key) {
					continue;
				}

				$value = $meta->value;
				if (is_string($value)) {
					$value = unserialize($value);
					if (false === $value) {
						continue;
					}
				}

				$formatted_meta[$meta->id] = (object) array(
					'key' => $meta->key,
					'value' => array(
						'design' => $value['design']
					),
					'display_key' => $meta->key,
					'display_value' => $value['design']
				);
			}
		}

		return $formatted_meta;
	}

	/**
	 * Set data for order again
	 *
	 * @param array $cart_item_data
	 * @param WC_Order_Item $item
	 * @param WC_Order $order
	 *
	 * @return array
	 * @throws Exception
	 */
	public static function order_again_cart_item_data( $cart_item_data, $item, $order) {
		$zakeke_data = $item->get_meta('zakeke_data');
		if ($zakeke_data) {
			$cart_item_data['zakeke_data'] = $zakeke_data;

			$webservice                              = new Zakeke_Webservice();
			$new_design                              = $webservice->duplicate_design($zakeke_data['design']);
			$cart_item_data['zakeke_data']['design'] = $new_design['docID'];
		}

		return $cart_item_data;
	}
}

Zakeke_Order::init();
