<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Zakeke_Configurator Class.
 */
class Zakeke_Configurator {

	/**
	 * Setup class.
	 */
	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register_scripts' ), 20 );
		add_shortcode( 'zakeke_configurator', __CLASS__ . '::output' );

		if ( ! self::should_show_configurator() ) {
			return;
		}

		remove_action( 'wp_loaded', array( 'WC_Form_Handler', 'add_to_cart_action' ), 20 );

		add_filter( 'template_include', array( __CLASS__, 'template_loader' ), 20000000 );
	}

	private static function should_show_configurator() {
		return ( ! empty( $_REQUEST['zconfiguration'] ) && 'new' === $_REQUEST['zconfiguration'] );
	}

	public static function register_scripts() {
		wp_register_style( 'zakeke-configurator', get_zakeke()->plugin_url() . '/assets/css/frontend/configurator.css',
			array(), ZAKEKE_VERSION );

		wp_register_script( 'zakeke-configurator', get_zakeke()->plugin_url() . '/assets/js/frontend/configurator.js',
			array( 'jquery' ), ZAKEKE_VERSION );
	}

	public static function enqueue_scripts() {
		wp_enqueue_style( 'zakeke-configurator' );
		wp_enqueue_script( 'zakeke-configurator' );
	}

	/**
	 * Get the default parameters for the shortcode starting from a product
	 *
	 * @param WC_Product $product
	 *
	 * @return array
	 */
	private static function default_parameters( $product ) {
		$integration = new Zakeke_Integration();

		$quantity = empty( $_REQUEST['quantity'] ) ? 1 : wc_stock_amount( $_REQUEST['quantity'] );

		$data = array(
			'integrationVersion' => 2,
			'wc_ajax_url'  => WC_AJAX::get_endpoint( '%%endpoint%%' ),
			'priceAjaxUrl' => WC_AJAX::get_endpoint( 'zakeke_get_configurator_price' ),
			'zakekeUrl'    => ZAKEKE_BASE_URL,
			'modelCode'    => (string) $product->get_id(),
			'product_id'   =>  (string) $product->get_id(),
			'name'         => $product->get_title(),
			'qty'          => $quantity,
			'currency'     => get_woocommerce_currency(),
			'culture'      => str_replace( '_', '-', get_locale() ),
			'ecommerce'    => 'woocommerce',
			'attributes'   => array(),
			'enableShareCompositionUrl' => true,
			'share_return_to_product_page' => 'yes' === $integration->share_return_to_product_page,
			'wc_cart_url' => zakeke_return_url()
		);

		if ( $product->is_type( 'variable' ) ) {
			$attributes = $product->get_attributes();
			foreach ( $attributes as $attribute_slug => $attribute ) {
				if ( ! $attribute['data']['variation'] ) {
					continue;
				}

				$terms = wc_get_product_terms( $product->get_id(), $attribute_slug, array(
					'fields' => 'all'
				) );

				foreach ( $terms as $term ) {
					if ( $term->term_id === $attribute['data']['options'][0] ) {
						$data['attributes'][ $attribute_slug ] = $term->slug;
						break;
					}
				}
			}
		}

		$default_attributes = $product->get_default_attributes();
		if ( $default_attributes ) {
			foreach ( $default_attributes as $attribute_slug => $attribute ) {
				$data['attributes'][ $attribute_slug ] = $attribute;
			}
		}

		foreach ( $_REQUEST as $key => $value ) {
			$prefix = substr( $key, 0, 10 );
			if ( 'attribute_' === $prefix ) {
				$short_key                        = substr( $key, 10 );
				$data['attributes'][ $short_key ] = $value;
			} else {
				$data['request'][ $key ] = $value;
			}
		}

		if ( isset( $_REQUEST['zconfiguration'] ) ) {
			$zakekeOption = sanitize_text_field( wp_unslash( $_REQUEST['zconfiguration'] ) );
			if ( 'new' !== $zakekeOption ) {
				$data['compositionId'] = $zakekeOption;
			}
		}

		if ( isset( $_REQUEST['zshared'] ) ) {
			$shared = sanitize_text_field( wp_unslash( $_REQUEST['zshared'] ) );
			if ( $shared ) {
				$data['sharedCompositionDocId'] = $shared;
			}
		}

		return $data;
	}

	/**
	 * Load the Zakeke configurator template.
	 *
	 * @return string
	 */
	public static function template_loader() {
		$file     = 'zakeke-configurator-product-page.php';
		$template = locate_template( $file );
		if ( ! $template ) {
			$template = get_zakeke()->plugin_path() . '/templates/' . $file;
		}

		return $template;
	}

	/**
	 * Load the Zakeke configurator template.
	 *
	 * @return string
	 */
	private static function template_loader_shortcode() {
		$file     = 'zakeke-configurator.php';
		$template = locate_template( $file );
		if ( ! $template ) {
			$template = get_zakeke()->plugin_path() . '/templates/' . $file;
		}

		return $template;
	}

	public static function output( $atts = array() ) {
		global $product;

		if ( '' == $atts ) {
			$atts = array();
		}

		if ( isset( $atts['product_id'] ) ) {
			$product = wc_get_product( intval( $atts['product_id'] ) );
		} else {
			$product            = wc_get_product();
			if ($product !== false) {
				$atts['product_id'] = $product->get_id();
			} elseif (isset($_REQUEST['ztmp_prefix_add-to-cart'])) {
				$atts['product_id'] = sanitize_text_field( wp_unslash( $_REQUEST['ztmp_prefix_add-to-cart'] ) );
				$product = wc_get_product( $atts['product_id'] );
			}
		}

		if ( ! $product ) {
			return '<-- Zakeke Configurator: product_id parameter not set --!>';
		}

		$atts['modelCode'] = $atts['product_id'];

		$atts['modelCode'] = apply_filters('ffs_zakeke_model_code', $atts['modelCode'], $atts['product_id']);

		$atts['modelCode'] = apply_filters('zakeke_configurator_iframe_modelCode', $atts['modelCode']);

		self::enqueue_scripts();

		$final_atts = shortcode_atts(
			self::default_parameters( $product ),
			$atts,
			'zakeke_configurator'
		);

		ob_start();
		include self::template_loader_shortcode();

		return ob_get_clean();
	}
}

Zakeke_Configurator::init();
