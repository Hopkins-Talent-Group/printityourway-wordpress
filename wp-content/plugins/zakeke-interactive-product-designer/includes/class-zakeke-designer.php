<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Zakeke_Designer Class.
 */
class Zakeke_Designer {

	/**
	 * Setup class.
	 */
	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register_scripts' ), 20 );
		add_shortcode( 'zakeke', __CLASS__ . '::output' );
		if ( ! self::should_show_designer() ) {
			return;
		}

		remove_action( 'wp_loaded', array( 'WC_Form_Handler', 'add_to_cart_action' ), 20 );

		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ), 20 );
		add_filter( 'template_include', array( __CLASS__, 'template_loader' ), 1100001 );
	}

	private static function should_show_designer() {
		return (
			( ( ! empty( $_REQUEST['zdesign'] ) && 'new' === $_REQUEST['zdesign'] )
			  || ( ! empty( $_REQUEST['zdesign_edit'] ) ) )
			&& ! isset( $_REQUEST['tc_cart_edit_key'] )
		);
	}

	public static function register_scripts() {
		wp_register_style( 'zakeke-designer', get_zakeke()->plugin_url() . '/assets/css/frontend/designer.css',
			array(), ZAKEKE_VERSION );
		wp_register_style( 'zakeke-designer-from-shortcode', get_zakeke()->plugin_url() . '/assets/css/frontend/designer-from-shortcode.css',
			array(), ZAKEKE_VERSION );

		wp_register_script(
			'zakeke-designer',
			apply_filters( 'zakeke_javascript_designer', get_zakeke()->plugin_url() . '/assets/js/frontend/designer.js' ),
			array( 'jquery' ),
			ZAKEKE_VERSION
		);
	}

	/**
	 * Enqueue Zakeke designer css and js code
	 *
	 * @param bool $from_shortcode
	 *
	 * @return void
	 */
	public static function enqueue_scripts($from_shortcode = false) {
		if ($from_shortcode) {
			wp_enqueue_style( 'zakeke-designer-from-shortcode' );
		} else {
			wp_enqueue_style( 'zakeke-designer' );
		}
		wp_enqueue_script( 'zakeke-designer' );
	}

	/**
	 * Load the Zakeke designer template.
	 *
	 * @param mixed $template
	 *
	 * @return string
	 */
	public static function template_loader( $template ) {
		return zakeke_template_loader( 'zakeke.php' );
	}

	/**
	 * Load the Zakeke configurator template.
	 *
	 * @return string
	 */
	private static function template_loader_shortcode() {
		$file     = 'zakeke-designer.php';
		$template = locate_template( $file );
		if ( ! $template ) {
			$template = get_zakeke()->plugin_path() . '/templates/' . $file;
		}

		return $template;
	}

	public static function output( $atts = array() ) {
		if ( '' == $atts ) {
			$atts = array();
		}

		if ( isset( $atts['product_id'] ) ) {
			$product = wc_get_product( intval( $atts['product_id'] ) );
		} else {
			$product = wc_get_product();
			if ($product !== false) {
				$atts['product_id'] = $product->get_id();
			}
		}

		if ( ! $product ) {
			return '<-- Zakeke: product not found --!>';
		}

		$template = null;
		if ( isset( $atts['template'] ) ) {
			$template = $atts['template'];
		}

		if ( isset( $atts['quantity'] ) ) {
			$_REQUEST['quantity'] = $atts['quantity'];
		}

		self::enqueue_scripts(true);

		$final_atts             = $atts;
		$final_atts['product']  = $product;
		$final_atts['template'] = $template;

		ob_start();
		include self::template_loader_shortcode();

		return ob_get_clean();
	}
}

Zakeke_Designer::init();
