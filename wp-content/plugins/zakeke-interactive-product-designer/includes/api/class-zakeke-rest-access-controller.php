<?php
/**
 * REST API Zakeke exchange access credentials controller
 *
 * Handle requests to the /access endpoint.
 *
 * @package  Zakeke/API
 * @since    1.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * REST API Zakeke exchange access credentials.
 *
 * @package Zakeke/API
 * @extends WC_REST_Controller
 */
class Zakeke_REST_Access_Controller extends WC_REST_Controller {

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
	protected $rest_base = 'access';

	/**
	 * Register the routes for Zakeke exchange access credentials.
	 */
	public function register_routes() {
		register_rest_route( $this->namespace, '/' . $this->rest_base, array(
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'set_access' ),
				'permission_callback' => array( $this, 'set_access_permissions_check' ),
				'args'                => array_merge( $this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ),
					array(
						'client_id' => array(
							'type'        => 'string',
							'description' => __( 'Zakeke API client id.', 'zakeke' ),
							'required'    => true
						),
						'secret_key' => array(
							'type'        => 'string',
							'description' => __( 'Zakeke API secret key.', 'zakeke' ),
							'required'    => true
						),
					) ),
			),
			'schema' => array( $this, 'get_public_item_schema' ),
		) );
	}

	/**
	 * Check if a given request has access make a product customizable by Zakeke.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 *
	 * @return boolean
	 */
	public function set_access_permissions_check( $request ) {
		if ( current_user_can( 'manage_options' ) || current_user_can( 'manage_woocommerce' ) || current_user_can( Zakeke::CAPABILITY ) ) {
			return true;
		}

		return new WP_Error( 'woocommerce_rest_cannot_create',
			__( 'Sorry, you are not allowed to create resources.', 'woocommerce' ),
			array( 'status' => rest_authorization_required_code() )
		);
	}

	/**
	 * The product is customizable with Zakeke.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function set_access( $request ) {
		$client_id  = $request['client_id'];
		$secret_key = $request['secret_key'];

		$integration = new Zakeke_Integration();

		$integration->update_option('client_id', $client_id);
		$integration->update_option('secret_key', $secret_key);

		$request->set_param('context', 'edit');
		$response = $this->prepare_item_for_response($request, $request);
		$response = rest_ensure_response($response);

		return $response;
	}

	/**
	 * Prepare output for response.
	 *
	 * @param array $item
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response $response Response data.
	 */
	public function prepare_item_for_response( $item, $request ) {
		$data = array(
			'client_id'  => $item['client_id'],
			'secret_key' => $item['secret_key']
		);

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		// Wrap the data in a response object.
		$response = rest_ensure_response( $data );

		return $response;
	}

}
