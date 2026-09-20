<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Zakeke_Designer Class.
 */
class Zakeke_ProductPage {

	/**
	 * Setup class.
	 */
	public static function init() {
		add_filter( 'post_class', array( __CLASS__, 'post_class' ), 20, 3 );

		add_filter( 'woocommerce_loop_add_to_cart_args', array( __CLASS__, 'add_zakeke_class' ), 20, 2 );

		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );

		add_filter( 'woocommerce_product_add_to_cart_text', array( __CLASS__, 'add_to_cart_text' ), 20, 2 );
		add_filter( 'woocommerce_product_single_add_to_cart_text', array( __CLASS__, 'single_add_to_cart_text' ),
			20, 2 );
		add_filter( 'single_add_to_cart_text', array( __CLASS__, 'single_add_to_cart_text_wrapper' ),
			20, 2 );
		add_filter( 'woocommerce_product_add_to_cart_url', array( __CLASS__, 'add_to_cart_url' ), 30, 2 );

		add_action( 'woocommerce_before_single_product', array( __CLASS__, 'product_page' ) );

		add_action( 'woocommerce_after_add_to_cart_button', array( __CLASS__, 'add_input' ) );
		add_action( 'woocommerce_bv_before_add_to_cart_button', array( __CLASS__, 'add_input' ) );
	}

	private static function has_force_customization () {
		static $force_customization = null;

		if ( is_null( $force_customization ) ) {
			$integration         = new Zakeke_Integration();
			$force_customization = $integration->force_customization;
		}

		return 'yes' === $force_customization;
	}

	public static function post_class( $classes, $class = '', $post_id = '' ) {
		if ( ! ( $post_id
			&& ( zakeke_is_customizable( $post_id ) || zakeke_configurator_is_customizable( $post_id ) ) ) ) {
			return $classes;
		}

		$classes[] = 'product-type-zakeke';

		$zakeke_yith_wacp_is_excluded = apply_filters('zakeke_yith_wacp_is_excluded', true);
        if ($zakeke_yith_wacp_is_excluded) {
	        $classes[] = 'product-type-external';
        }

		return $classes;
	}

	public static function add_zakeke_class( $args, $product ) {
		if ( zakeke_is_customizable( $product->get_id() ) || zakeke_configurator_is_customizable( $product->get_id() ) ) {
			$classes = ' product-type-zakeke ';
			if ( isset( $args['class'] ) ) {
				$classes .= $args['class'];
			}
			$args['class'] = $classes;
		}

		return $args;
	}

	public static function enqueue_scripts() {
		wp_register_script( 'zakeke-product-page', get_zakeke()->plugin_url() . '/assets/js/frontend/product-page.js',
			array(), ZAKEKE_VERSION, false );
		wp_enqueue_script( 'zakeke-product-page' );
	}

	public static function product_page() {
		/**
		 * The current products.
		 *
		 * @var WC_Product $product
		 */
		global $product;

		if ( self::has_force_customization() || zakeke_has_provider( $product->get_id() ) ) {
			return;
		}

		if ( zakeke_configurator_is_customizable( $product->get_id() ) ) {
			add_action('woocommerce_before_add_to_cart_button', array(__CLASS__, 'add_configure_button'), 25);
			add_action( 'woocommerce_bv_before_add_to_cart_button', array(__CLASS__, 'add_configure_button') );
		} elseif ( zakeke_is_customizable( $product->get_id() ) ) {
			add_action( 'woocommerce_before_add_to_cart_button', array(__CLASS__, 'add_customize_button'), 25);
			add_action( 'woocommerce_bv_before_add_to_cart_button', array(__CLASS__, 'add_customize_button') );
		}
	}

	public static function add_customize_button() {
		?>
		<div class="group">
			<button class="zakeke-customize-button button" type="button">
				<?php esc_html_e( 'Customize','zakeke' ) ?>
			</button>
		</div>
		<?php
	}

	public static function add_configure_button() {
		?>
		<div class="group">
			<button class="zakeke-configurator-customize-button button" type="button">
				<?php esc_html_e( 'Configure','zakeke' ) ?>
			</button>
		</div>
		<?php
	}

    private static function yith_wacp_is_excluded() {
	    $zakeke_yith_wacp_is_excluded = apply_filters('zakeke_yith_wacp_is_excluded', true);
	    if ($zakeke_yith_wacp_is_excluded) {
		    ?>
            <input type="hidden" name="yith-wacp-is-excluded" value="yes"/>
		    <?php
	    }
    }

	public static function add_input() {
		/**
		 * The current product.
		 *
		 * @var WC_Product $product
		 */
		global $product;

		if ( zakeke_configurator_is_customizable( $product->get_id() ) ) {
            self::yith_wacp_is_excluded();

			if (self::has_force_customization()) {
				?>
				<input type="hidden" name="zconfiguration" value="new"/>
				<?php
			} else {
				?>
				<input type="hidden" name="zconfiguration"/>
				<?php
			}
		} elseif ( zakeke_is_customizable( $product->get_id() ) ) {
			self::yith_wacp_is_excluded();

			if (self::has_force_customization() || zakeke_has_provider($product->get_id())) {
				?>
				<input type="hidden" name="zdesign" id="zdesign" value="new"/>
				<?php
			} else {
				?>
				<input type="hidden" name="zdesign" id="zdesign"/>
				<?php
			}
		}

        if ( ! empty( $_REQUEST['zshared'] ) ) {
            ?>
	        <input type="hidden" name="zshared" id="zshared" value="<?php esc_attr_e( $_REQUEST['zshared'] ) ?>" />
            <?php
        }
	}

	/**
	 * Get the customize button text.
	 *
	 * @param string $text
	 * @param string $product_type
	 *
	 * @return string
	 */
	public static function single_add_to_cart_text_wrapper( $text, $product_type ) {
		/**
		 * The current product.
		 *
		 * @var WC_Product $product
		 */
		global $product;

		return self::single_add_to_cart_text( $text, $product );
	}

	/**
	 * Replace the add to cart text with customize.
	 *
	 * @param string $text
	 * @param WC_Product $product
	 *
	 * @return string
	 */
	public static function single_add_to_cart_text( $text, $product ) {
		if ( ! ( self::has_force_customization() || zakeke_has_provider( $product->get_id() ) ) ) {
			return $text;
		}

		if ( zakeke_configurator_is_customizable( $product->get_id() ) ) {
			$text = __( 'Configure', 'zakeke' );
		} elseif ( zakeke_is_customizable( $product->get_id() ) ) {
			$text = __( 'Customize', 'zakeke' );
		}

		return $text;
	}

	/**
	 * Replace the add to cart text with customize.
	 *
	 * @param string $text
	 * @param WC_Product $product
	 *
	 * @return string
	 */
	public static function add_to_cart_text( $text, $product ) {
		if ( 'simple' !== $product->get_type() || ! ( self::has_force_customization() || zakeke_has_provider( $product->get_id() ) ) ) {
			return $text;
		}

		if ( zakeke_configurator_is_customizable( $product->get_id() ) ) {
			$text = __( 'Configure', 'zakeke' );
		} elseif ( zakeke_is_customizable( $product->get_id() ) ) {
			$text = __( 'Customize', 'zakeke' );
		}

		return $text;
	}

	/**
	 * Replace the standard add to cart url with the customizer url.
	 *
	 * @param string $url
	 * @param WC_Product $product
	 *
	 * @return string
	 */
	public static function add_to_cart_url( $url, $product ) {
		$product_id = $product->get_id();
		if ( 'simple' !== $product->get_type() || ! ( self::has_force_customization() || zakeke_has_provider( $product_id ) ) ) {
			return $url;
		}

		$permalink = get_permalink( $product_id );
		if (strpos($permalink, '?') !== false) {
			$permalink = $permalink . '&';
		} else {
			$permalink = $permalink . '?';
		}

		if ( zakeke_configurator_is_customizable( $product_id ) ) {
			$url = $permalink . 'zconfiguration=new&add-to-cart=' . $product_id . '&_wp_http_referer=' . zakeke_get_shop_url();
		} elseif ( zakeke_is_customizable( $product_id ) ) {
			$url = $permalink . 'zdesign=new&ztmp_prefix_add-to-cart=' . $product_id . '&_wp_http_referer=' . zakeke_get_shop_url();
		}

		return $url;
	}
}

Zakeke_ProductPage::init();
