<?php
/**
 * Zakeke API
 *
 * Handles API endpoint requests.
 *
 * @package  Zakeke/API
 * @version    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Zakeke_API {

	/**
	 * Setup class.
	 */
	public function __construct() {
		// Init REST API routes.
		add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );
		add_filter( 'woocommerce_rest_is_request_to_rest_api', array( __CLASS__, 'is_request_to_rest_api' ) );
	}

	public $Zakeke_REST_Access_Controller = null;
	public $Zakeke_REST_Enabled_Controller = null;
	public $Zakeke_REST_Settings_Controller = null;


	/**
	 * Init Zakeke REST API.
	 */
	public function rest_api_init() {
		$this->rest_api_includes();

		$this->Zakeke_REST_Access_Controller = new Zakeke_REST_Access_Controller();
		$this->Zakeke_REST_Access_Controller->register_routes();

		$this->Zakeke_REST_Enabled_Controller = new Zakeke_REST_Enabled_Controller();
		$this->Zakeke_REST_Enabled_Controller->register_routes();

		$this->Zakeke_REST_Settings_Controller = new Zakeke_REST_Settings_Controller();
		$this->Zakeke_REST_Settings_Controller->register_routes();
	}

	/**
	 * Add Zakeke as REST api
	 *
	 * @return bool
	 */
	public static function is_request_to_rest_api( $is_api_request ) {
		if ( $is_api_request ) {
			return true;
		}

		$rest_prefix = trailingslashit( rest_get_url_prefix() );

		// Check if our endpoint.
		if ( isset( $_SERVER['REQUEST_URI'] ) && false !== strpos( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ), $rest_prefix . 'zakeke/' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Include REST API classes.
	 */
	private function rest_api_includes() {
		include_once 'api/class-zakeke-rest-access-controller.php';
		include_once 'api/class-zakeke-rest-enabled-controller.php';
		include_once 'api/class-zakeke-rest-settings-controller.php';
	}
}
