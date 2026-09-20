<?php
/**
 * REST API Zakeke shop settings controller
 *
 * Handle requests to the /settings endpoint.
 *
 * @package  Zakeke/API
 * @since    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * REST API Zakeke shop settings class.
 *
 * @package Zakeke/API
 * @extends WC_REST_Controller
 */
class Zakeke_REST_Settings_Controller extends WC_REST_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'zakeke/v1';

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'settings';

	/**
	 * Register the routes for shop settings necessary to Zakeke.
	 */
	public function register_routes() {
		register_rest_route( $this->namespace, '/' . $this->rest_base, array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_item' ),
				'permission_callback' => array( $this, 'get_item_permissions_check' )
			),
			'schema' => array( $this, 'get_item_schema' ),
		) );
	}

	/**
	 * Check if a given request has access to read shop settings.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_Error|boolean
	 */
	public function get_item_permissions_check( $request ) {
		if ( current_user_can( 'manage_options' ) || current_user_can( 'manage_woocommerce' ) || current_user_can( Zakeke::CAPABILITY ) ) {
			return true;
		}

		return new WP_Error( 'woocommerce_rest_cannot_create',
			__( 'Sorry, you are not allowed to create resources.', 'woocommerce' ),
			array( 'status' => rest_authorization_required_code() )
		);
	}

	/**
	 * Get shop settings necessary to Zakeke.
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return array
	 */
	public function get_item( $request ) {
		$data = array(
			'currency' => get_woocommerce_currency()
		);

		return rest_ensure_response( $data );
	}

	/**
	 * Prepare the shop settings output for response.
	 *
	 * @param string $item
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response $response Response data.
	 */
	public function prepare_item_for_response( $item, $request ) {
		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$item    = $this->add_additional_fields_to_object( $item, $request );
		$item    = $this->filter_response_by_context( $item, $context );

		// Wrap the data in a response object.
		$response = rest_ensure_response( $item );
		return $response;
	}

	/**
	 * Get the shop settings schema, conforming to JSON Schema.
	 *
	 * @return array
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'zakeke_enabled',
			'type'       => 'object',
			'properties' => array(
				'currency' => array(
					'description' => __( 'Shop currency.', 'zakeke' ),
					'type'        => 'string',
					'context'     => array( 'view' )
				)
			)
		);

		return $this->add_additional_fields_schema( $schema );
	}
}
