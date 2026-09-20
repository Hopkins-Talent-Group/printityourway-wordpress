<?php
/**
 * REST API Zakeke enabled product to be customizable controller
 *
 * Handle requests to the /enabled endpoint.
 *
 * @package  Zakeke/API
 * @since    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * REST API Zakeke enabled product to be customizable class.
 *
 * @package Zakeke/API
 * @extends WC_REST_Controller
 */
class Zakeke_REST_Enabled_Controller extends WC_REST_Controller {

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
	protected $rest_base = 'enabled';

	/**
	 * Register the routes for Zakeke customizable products.
	 */
	public function register_routes() {
		register_rest_route( $this->namespace, '/' . $this->rest_base, array(
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'create_item' ),
				'permission_callback' => array( $this, 'create_item_permissions_check' ),
				'args'                => array_merge( $this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ),
					array(
						'id' => array(
							'type'        => 'integer',
							'description' => __( 'Product id of the product that became customizable.', 'zakeke' ),
							'required'    => true
						),
						'environment' => array(
							'type'        => 'string',
							'description' => __( 'Product environment of the product that became customizable.', 'zakeke' ),
							'required'    => false
						),
					) ),
			),
			'schema' => array( $this, 'get_public_item_schema' ),
		) );

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<id>.*)', array(
			'args'   => array(
				'id' => array(
					'description' => __( 'Product id of the product that is no longer customizable.', 'zakeke' ),
					'type'        => 'integer',
					'required'    => true
				),
			),
			array(
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => array( $this, 'delete_item' ),
				'permission_callback' => array( $this, 'delete_item_permissions_check' ),
				'args'                => array(
					'force' => array(
						'default'     => false,
						'type'        => 'boolean',
						'description' => __( 'Required to be true, as resource does not support trashing.',
							'woocommerce' ),
					),
				),
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
	public function create_item_permissions_check( $request ) {
		if ( current_user_can( 'manage_options' ) || current_user_can( 'manage_woocommerce' ) || current_user_can( Zakeke::CAPABILITY ) ) {
			return true;

		}

		return new WP_Error( 'woocommerce_rest_cannot_create',
			__( 'Sorry, you are not allowed to create resources.', 'woocommerce' ),
			array( 'status' => rest_authorization_required_code() )
		);
	}

	/**
	 * Check if a given request has access make a product no longer customizable by Zakeke.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 *
	 * @return boolean
	 */
	public function delete_item_permissions_check( $request ) {
		if ( current_user_can( 'manage_options' ) || current_user_can( 'manage_woocommerce' ) || current_user_can( Zakeke::CAPABILITY ) ) {
			return true;

		}

		return new WP_Error( 'woocommerce_rest_cannot_delete',
			__( 'Sorry, you are not allowed to delete this resource.', 'woocommerce' ),
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
	public function create_item( $request ) {
		$product_id = $request['id'];

		if ( empty( $request['environment'] ) || 'customizer' === $request['environment'] ) {
			update_post_meta( $product_id, 'zakeke_enabled', 'yes' );
			update_post_meta( $product_id, 'zakeke_configurator_enabled', 'no' );
		} else {
			update_post_meta( $product_id, 'zakeke_enabled', 'no' );
			update_post_meta( $product_id, 'zakeke_configurator_enabled', 'yes' );
		}

		$request->set_param('context', 'edit');
		$response = $this->prepare_item_for_response($product_id, $request);
		$response = rest_ensure_response($response);

		return $response;
	}

	/**
	 * The product is not customizable with Zakeke.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function delete_item( $request ) {
		$product_id = $request['id'];

		update_post_meta( $product_id, 'zakeke_enabled', 'no' );
		update_post_meta( $product_id, 'zakeke_configurator_enabled', 'no' );

		$request->set_param( 'context', 'edit' );
		$response = $this->prepare_item_for_response( $product_id, $request );
		$response = rest_ensure_response( $response );

		return $response;
	}

	/**
	 * Prepare output for response.
	 *
	 * @param string $item Product id
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response $response Response data.
	 */
	public function prepare_item_for_response( $item, $request ) {
		$data = array(
			'id' => $item
		);

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		// Wrap the data in a response object.
		$response = rest_ensure_response( $data );

		return $response;
	}

	/**
	 * Get the customizable product schema, conforming to JSON Schema.
	 *
	 * @return array
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'zakeke_enabled',
			'type'       => 'object',
			'properties' => array(
				'id' => array(
					'description' => __( 'Product id.', 'zakeke' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' )
				)
			),
		);

		return $this->add_additional_fields_schema( $schema );
	}

	/**
	 * Get the query params for collections.
	 *
	 * @return array
	 */
	public function get_collection_params() {
		return array(
			'context' => $this->get_context_param( array( 'default' => 'view' ) ),
		);
	}
}
