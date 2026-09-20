<?php
/**
 * Order meta map.
 *
 * @package Woodmart
 */

namespace XTS\Modules\Layouts;

use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Elementor widget that inserts an embeddable content into the page, from any given URL.
 */
class Order_Meta extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'wd_order_meta';
	}

	/**
	 * Get widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Order meta', 'woodmart' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'wd-icon-tp-order-meta';
	}

	/**
	 * Get widget categories.
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( 'wd-thank-you-page-elements' );
	}

	/**
	 * Show in panel.
	 *
	 * @return bool Whether to show the widget in the panel or not.
	 */
	public function show_in_panel() {
		return Main::is_layout_type( 'thank_you_page' );
	}

	/**
	 * Register the widget controls.
	 */
	protected function register_controls() {
		/**
		 * Style tab.
		 */

		/**
		 * General settings
		 */
		$this->start_controls_section(
			'general_style_section',
			array(
				'label' => esc_html__( 'General', 'woodmart' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'css_classes',
			array(
				'type'         => 'wd_css_class',
				'default'      => 'wd-tp-order-meta',
				'prefix_class' => '',
			)
		);

		$this->add_control(
			'order_data',
			array(
				'label'   => esc_html__( 'Select order data', 'woodmart' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'order_id'        => esc_html__( 'Order ID', 'woodmart' ),
					'order_date'      => esc_html__( 'Order date', 'woodmart' ),
					'order_status'    => esc_html__( 'Order status', 'woodmart' ),
					'order_email'     => esc_html__( 'Order email', 'woodmart' ),
					'order_total'     => esc_html__( 'Order total', 'woodmart' ),
					'payment_method'  => esc_html__( 'Payment method', 'woodmart' ),
					'shipping_method' => esc_html__( 'Shipping method', 'woodmart' ),
					'custom'          => esc_html__( 'Custom', 'woodmart' ),
				),
				'default' => '',
			)
		);

		$this->add_control(
			'meta_key',
			array(
				'label'     => esc_html__( 'Meta key', 'woodmart' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '',
				'condition' => array(
					'order_data' => 'custom',
				),
			)
		);

		$this->add_control(
			'alignment',
			array(
				'label'        => esc_html__( 'Alignment', 'woodmart' ),
				'type'         => 'wd_buttons',
				'options'      => array(
					'left'   => array(
						'title' => esc_html__( 'Left', 'woodmart' ),
						'image' => WOODMART_ASSETS_IMAGES . '/settings/align/left.jpg',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'woodmart' ),
						'image' => WOODMART_ASSETS_IMAGES . '/settings/align/center.jpg',
					),
					'right'  => array(
						'title' => esc_html__( 'Right', 'woodmart' ),
						'image' => WOODMART_ASSETS_IMAGES . '/settings/align/right.jpg',
					),
				),
				'prefix_class' => 'text-',
				'default'      => 'left',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'label'    => esc_html__( 'Typography', 'woodmart' ),
				'name'     => 'typography',
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->add_control(
			'color',
			array(
				'label'     => esc_html__( 'Color', 'woodmart' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render the widget output on the frontend.
	 */
	protected function render() {
		$settings = wp_parse_args(
			$this->get_settings_for_display(),
			array(
				'order_data' => '', // phpcs:ignore.
			)
		);

		global $order;

		if ( ! $order || ! $settings['order_data'] ) {
			return;
		}

		Main::setup_preview();

		switch ( $settings['order_data'] ) {
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

		if ( ! empty( $settings['meta_key'] ) && strpos( $settings['meta_key'], '_' ) !== 0 ) {
			// Custom fields order meta.
			echo $order->get_meta( $settings['meta_key'] ); // phpcs:ignore.
		}

		Main::restore_preview();
	}
}

Plugin::instance()->widgets_manager->register( new Order_Meta() );
