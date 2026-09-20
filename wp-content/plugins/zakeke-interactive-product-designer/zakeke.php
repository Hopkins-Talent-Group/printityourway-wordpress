<?php
/**
 * Plugin Name: Zakeke Interactive Product Designer
 * Plugin URI: https://www.zakeke.com/
 * Description: Innovative platform to let your customers to customize products in your e-store. Multi-language, mult-currency, 3D view and print-ready outputs.
 * Version: 4.1.14
 * Author: Zakeke
 * Author URI: https://www.zakeke.com
 * Requires at least: 5.0
 * Tested up to: 6.8
 * WC requires at least: 4.0
 * WC tested up to: 9.8
 *
 * Text Domain: zakeke
 * Domain Path: /i18n/languages/
 *
 * @package Zakeke
 *
 * License: GPL2+
 *
 * Zakeke Interactive Product Designer is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Zakeke Interactive Product Designer is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Zakeke Interactive Product Designer. If not, see write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Zakeke' ) ) :

	/**
	 * Main Zakeke Class.
	 */
	final class Zakeke {

		const CAPABILITY = 'edit_zakeke';

		/**
		 * Zakeke version.
		 *
		 * @var string
		 */
		public $version = '4.1.14';

		/**
		 * Zakeke api instance.
		 *
		 * @var Zakeke_API
		 */
		private $api;

		/**
		 * The single instance of the class.
		 *
		 * @var Zakeke
		 */
		protected static $_instance = null;

		/**
		 * Main Zakeke Instance.
		 *
		 * Ensures only one instance of Zakeke is loaded or can be loaded.
		 *
		 * @static
		 * @see get_zakeke()
		 * @return Zakeke - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**
		 * Cloning is forbidden.
		 *     * @since 1.0
		 */
		public function __clone() {
			wc_doing_it_wrong( __FUNCTION__, __( 'Cloning is forbidden.', 'woocommerce' ), '2.1' );
		}

		/**
		 * Unserializing instances of this class is forbidden.
		 *
		 * @since 1.0
		 */
		public function __wakeup() {
			wc_doing_it_wrong( __FUNCTION__, __( 'Unserializing instances of this class is forbidden.', 'woocommerce' ), '4.6' );
			die();
		}

		public function construct() {
			$this->define_constants();

			$this->load_plugin_textdomain();

			if ( self::woocommerce_did_load() ) {
				$this->includes();
				$this->init_hooks();

				register_activation_hook( __FILE__, array( $this, 'activate' ) );
				register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
			}

			add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
		}

		/**
		 * Zakeke Constructor.
		 */
		public function __construct() {
			if ( self::woocommerce_did_load() ) {
				$this->construct();
			} else {
				add_action( 'plugins_loaded' , array( $this, 'construct' ) );
			}
		}

		/**
		 * Initialize Zakeke for the first run.
		 */
		public function activate() {
			global $wp_rewrite;
			add_option('zakeke_do_activation_redirect', true);
			$wp_rewrite->flush_rules(false);
		}

		/**
		 * Clear the Zakeke settings on the deactivation.
		 */
		public function deactivate() {
			$integration = new Zakeke_Integration();
			$integration->update_option('username', '');
			$integration->update_option('password', '');
			$integration->update_option('client_id', '');
			$integration->update_option('secret_key', '');
		}

		private static function woocommerce_did_load() {
			return function_exists( 'WC' ) && version_compare( WC()->version, 3.4, '>=' );
		}

		/**
		 * Define constant if not already set.
		 *
		 * @param  string $name
		 * @param  string|bool $value
		 */
		private function define( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}

		/**
		 * Define Zakeke Constants.
		 */
		private function define_constants() {
			$this->define( 'ZAKEKE_PLUGIN_FILE', __FILE__ );
			$this->define( 'ZAKEKE_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
			$this->define( 'ZAKEKE_VERSION', $this->version );
			$this->define( 'ZAKEKE_BASE_URL', 'https://portal.zakeke.com' );
			$this->define( 'ZAKEKE_ADMIN_URL', 'https://admin.zakeke.com' );
			$this->define( 'ZAKEKE_WEBSERVICE_URL', 'https://api.zakeke.com' );
			$this->define( 'ZAKEKE_WIDTH_BREAKPOINT', 768 );
		}

		/**
		 * What type of request is this?
		 *
		 * @param  string $type admin, ajax, cron or frontend.
		 *
		 * @return bool
		 */
		private function is_request( $type ) {
			switch ( $type ) {
				case 'admin':
					return is_admin();
				case 'ajax':
					return defined( 'DOING_AJAX' );
				case 'cron':
					return defined( 'DOING_CRON' );
				case 'frontend':
					return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
			}
		}

		/**
		 * Include required frontend files.
		 */
		public function frontend_includes() {
			include_once  'includes/class-zakeke-shop.php' ;
			include_once  'includes/class-zakeke-ajax.php' ;
			include_once  'includes/class-zakeke-product-page.php' ;
			include_once  'includes/class-zakeke-designer.php' ;
			include_once  'includes/class-zakeke-configurator.php' ;
			include_once  'includes/class-zakeke-cart.php' ;
			include_once  'includes/class-zakeke-multiplevariants.php' ;
			include_once  'includes/class-zakeke-namenumbers.php' ;
			include_once  'includes/class-zakeke-guest.php' ;

			include_once  'includes/support/class-dynamic-pricing-and-discounts-for-woocommerce.php' ;
			include_once  'includes/support/class-checkout-for-woocommerce.php' ;
			include_once  'includes/support/class-tier-pricing-table-premium.php' ;
			include_once  'includes/support/class-wpml.php' ;
			include_once  'includes/support/wf-invoice-support.php' ;
			include_once  'includes/support/class-aelia-currency.php' ;
		}

		/**
		 * Include required core files used in admin and on the frontend.
		 */
		public function includes() {
			include_once  'includes/zakeke-core-functions.php' ;
			include_once  'includes/class-zakeke-integration.php' ;
			include_once  'includes/class-zakeke-api.php' ;
			include_once  'includes/class-zakeke-install.php' ;
			include_once  'includes/auth/class-zakeke-auth-base.php' ;
			include_once  'includes/auth/class-zakeke-auth.php' ;
			include_once  'includes/auth/class-zakeke-auth-legacy.php' ;
			include_once  'includes/class-zakeke-webservice.php' ;
			include_once  'includes/class-zakeke-order.php' ;

			if ( $this->is_request( 'admin' ) ) {
				include_once  'includes/admin/class-zakeke-admin-get-started.php' ;
				include_once  'includes/admin/class-zakeke-admin-order.php' ;
			}

			if ( $this->is_request( 'frontend' ) ) {
				$this->frontend_includes();
			}

			$this->api = new Zakeke_API();
		}

		/**
		 * Don't duplicate Zakeke metadata
		 *
		 * @param WC_Product $duplicate
		 * @param WC_Product $product
		 */
		public function product_duplicate( $duplicate, $product ) {
			$duplicate->delete_meta_data( 'zakeke_enabled' );
		}

		/**
		 * Hook into actions and filters.
		 */
		private function init_hooks() {
			add_filter( 'woocommerce_integrations', array( $this, 'add_integration' ) );

			if ( is_admin() ) {
				add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'add_links' ) );
			}

			add_action( 'woocommerce_product_duplicate_before_save', array( $this, 'product_duplicate' ), 10, 2 );


			add_filter('http_request_args', 'bal_http_request_args', 100, 1);
			function bal_http_request_args($r)
			{
				$r['timeout'] = 15;
				return $r;
			}

			add_action('http_api_curl', 'bal_http_api_curl', 100, 1);
			function bal_http_api_curl($handle)
			{
				curl_setopt( $handle, CURLOPT_CONNECTTIMEOUT, 15 );
				curl_setopt( $handle, CURLOPT_TIMEOUT, 15 );
			}
		}

		/**
		 * Show row meta on the plugin screen.
		 *
		 * @param    mixed $links Plugin Row Meta
		 * @param    mixed $file Plugin Base file
		 *
		 * @return    array
		 */
		public static function plugin_row_meta( $links, $file ) {
			if ( plugin_basename( __FILE__ ) == $file ) {
				$row_meta = array(
					'docs' => '<a href="https://zakeke.zendesk.com/hc/en-us/sections/360004488294-WooCommerce-" target="_blank" aria-label="' . esc_attr__( 'View Zakeke documentation',
							'zakeke' ) . '">' . esc_html__( 'Documentation', 'zakeke' ) . '</a>',
					'register' => '<a href="https://admin.zakeke.com/Admin/Register?utm_medium=plugin&utm_campaign=zakeke_processo_installazione" target="_blank" aria-label="' . esc_attr__( 'Register to Zakeke',
							'zakeke' ) . '">' . esc_html__( 'Register to Zakeke to use the plugin', 'zakeke' ) . '</a>'
				);

				return array_merge( $links, $row_meta );
			}

			return (array) $links;
		}

		/**
		 * Add Documentation and Register to Zakeke links
		 *
		 * @param array $links
		 *
		 * @return array
		 */
		public function add_links( $links ) {
			$action_links = array(
				'settings' => '<a href="' . esc_url( $this->settings_url() ) . '" aria-label="' . esc_attr__( 'View Zakeke settings',
						'zakeke' ) . '">' . esc_html__( 'Settings', 'woocommerce' ) . '</a>'
			);

			return array_merge( $action_links, $links );
		}

		/**
		 * Get the plugin path.
		 *
		 * @return string
		 */
		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( __FILE__ ) );
		}

		/**
		 * Get the plugin url.
		 *
		 * @return string
		 */
		public function plugin_url() {
			return untrailingslashit( plugins_url( '/', __FILE__ ) );
		}

		/**
		 * Get the settings url.
		 *
		 * @return string
		 */
		public function settings_url() {
			$settings_url = admin_url( 'admin.php?page=wc-settings&tab=integration&section=zakeke' );

			return $settings_url;
		}

		/**
		 * Load Localization files.
		 */
		private function load_plugin_textdomain() {
			load_plugin_textdomain( 'zakeke', false, plugin_basename( dirname( __FILE__ ) ) . '/i18n/languages' );
		}

		/**
		 * Add the Zakeke settings tab to WooCommerce
		 */
		public function add_integration( $integrations ) {
			$integrations[] = 'Zakeke_Integration';

			return $integrations;
		}
	}

endif;

/**
 * Main instance of Zakeke.
 *
 * Returns the main instance of Zakeke to prevent the need to use globals.
 *
 * @return Zakeke
 */
function get_zakeke() {
	return Zakeke::instance();
}

$GLOBALS['zakeke'] = get_zakeke();

add_action( 'before_woocommerce_init', function() {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
} );
