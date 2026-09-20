<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Zakeke_Webservice {

	/**
	 * Logger instance.
	 *
	 * @var WC_Logger
	 */
	private $logger;

	/**
	 * Debug mode.
	 *
	 * @var boolean
	 */
	private $debug;

	/**
	 * Setup class.
	 */
	public function __construct() {
		$this->logger = new WC_Logger();
		$integration  = new Zakeke_Integration();
		$this->debug  = $integration->debug;
	}

	/**
	 * Performs the underlying HTTP request.
	 *
	 * @param  string $method HTTP method (GET|POST|PUT|PATCH|DELETE)
	 * @param  string $resource Zakeke API resource to be called
	 * @param  array $args array of parameters to be passed
	 * @param Zakeke_Auth_Base $auth Authentication
	 *
	 * @throws Exception
	 * @return array          array of decoded result
	 */
	public function request( $method, $resource, $args = array(), $auth = null ) {
		$url = ZAKEKE_WEBSERVICE_URL . $resource;

		global $wp_version;

		$request_args = array(
			'method'      => $method,
			'redirection' => 5,
			'timeout'     => 45,
			'httpversion' => '1.1',
			'headers'     => array(
				'Content-Type' => 'application/json',
				'Accept'       => 'application/json',
				'User-Agent'   => 'woocommerce-zakeke/' . ZAKEKE_VERSION . '; WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' )
			),
		);

		if ( ! is_null( $auth ) ) {
			$request_args = $auth->set_authentication( $request_args );
		}

		// attach arguments (in body or URL)
		if ( ! empty( $args ) ) {
			if ('GET' === $method) {
				$url = $url . '?' . http_build_query($args);
			} else {
				$request_args['body'] = json_encode($args);
			}
		}

		$raw_response = wp_remote_request( $url, $request_args );

		$this->maybe_log( $url, $method, $args, $raw_response );

		if ( is_wp_error( $raw_response )
			 || ( is_array( $raw_response )
				  && $raw_response['response']['code']
				  && floor( $raw_response['response']['code'] ) / 100 >= 4 )
		) {
			if ( is_wp_error( $raw_response ) ) {
				$error_message = $raw_response->get_error_message();
			} else {
				$error_message = print_r( $raw_response, true );
			}
			$this->logger->add( 'zakeke', "Zakeke Webservice Call Error: $url \n METHOD: $method \n BODY: " . print_r( $args,
					true ) . ' \n ERROR: ' . $error_message );

			throw new Exception( 'Zakeke_Webservice::request failed' );
		}

		$json   = wp_remote_retrieve_body( $raw_response );
		$result = json_decode( $json, true );

		return $result;
	}

	/**
	 * Check Zakeke authentication credentials.
	 *
	 * @param string $username Zakeke username
	 * @param string $password Zakeke password
	 *
	 * @return bool
	 */
	public function are_valid_credentials( $username, $password ) {
		try {
			$this->request('GET', '/api/Login', array(
				'user' => $username,
				'pwd'  => $password
			));

			return true;
		} catch ( Exception $e ) {
			return false;
		}
	}

	/**
	 * Check Zakeke API keys.
	 *
	 * @param string $client_id Zakeke client id
	 * @param string $secret_key Zakeke secret key
	 *
	 * @return bool
	 */
	public function are_valid_api_keys( $client_id, $secret_key ) {
		try {
			$data = array(
				'grant_type' => 'client_credentials'
			);

			$request_args = array(
				'method'      => 'POST',
				'headers'     => array(
					'Authorization' => 'Basic ' . base64_encode( $client_id . ':' . $secret_key ),
					'Content-Type' => 'application/x-www-form-urlencoded',
					'Accept'       => 'application/json',
					'User-Agent'   => 'woocommerce-zakeke/' . ZAKEKE_VERSION . '; ' . get_bloginfo( 'url' )
				),
				'body'        => http_build_query($data, '', '&')
			);

			$url          = ZAKEKE_WEBSERVICE_URL . '/token';
			$raw_response = wp_remote_request( $url, $request_args );

			if ( is_wp_error( $raw_response )
				|| ( is_array( $raw_response )
					&& $raw_response['response']['code']
					&& floor( $raw_response['response']['code'] ) / 100 >= 4 )
			) {
				return false;
			}

			return true;
		} catch ( Exception $e ) {
			return false;
		}
	}

	/**
	 * Associate the guest with a customer
	 *
	 * @param string $guest_code - Guest identifier.
	 * @param string $customer_id - Customer identifier.
	 *
	 * @throws Exception
	 * @return void
	 */
	public function associate_guest( $guest_code, $customer_id ) {
		$auth = zakeke_get_auth();
		$auth->set_guest( $guest_code );
		$auth->set_customer( $customer_id );

		$auth->get_auth_token();
	}

	/**
	 * Get the needed data for adding a product to the cart
	 *
	 * @param string $designId Zakeke design identifier.
	 * @param int $qty Quantity.
	 * @param string|null $modificationId Zakeke design modification identifier.
	 *
	 * @throws Exception
	 * @return object
	 */
	public function cart_info( $designId, $qty, $modificationId = null ) {
		static $cache = array();
		$cache_key    = $designId . $qty;
		if ($modificationId) {
			$cache_key .= $modificationId;
		}
		if (isset($cache[$cache_key])) {
			return $cache[$cache_key];
		}

		$auth = zakeke_get_auth();
		$data = array(
			'qty' => $qty,
			'includePricingConditions' => 'true'
		);

		if ( $modificationId ) {
			$data['modificationID'] = $modificationId;
		}

		$resource = '/api/designdocs/' . $designId . '/cartinfo';

		$json = self::request( 'GET', $resource, $data, $auth );

		$res = new stdClass();

		$res->pricing = $json['pricing'];

		$preview        = new stdClass();
		$preview->url   = $json['tempPreviewUrl'];
		$preview->label = '';

		$res->previews = array($preview);

		if (isset($json['previewFiles'])) {
			foreach ($json['previewFiles'] as $previewFile) {
				$preview         = new stdClass();
				$preview->url    = $previewFile['url'];
				$preview->label  = $previewFile['sideName'];
				$res->previews[] = $preview;
			}
		}

		$cache[$cache_key] = $res;
		return $res;
	}

	/**
	 * Duplicate a given design
	 *
	 * @param string $designId Zakeke design identifier.
	 *
	 * @throws Exception
	 * @return array
	 */
	public function duplicate_design( $designId) {
		$auth     = zakeke_get_auth();
		$resource = '/api/designdocs/' . $designId . '/duplicate';
		return self::request( 'POST', $resource, array(), $auth );
	}

	/**
	 * Get the needed data for adding a configured product to the cart
	 *
	 * @param string $configuration Zakeke configuration identifier.
	 * @param int $qty Quantity.
	 *
	 * @throws Exception
	 * @return array
	 */
	public function configurator_cart_info( $configuration, $qty ) {
		$auth = zakeke_get_auth();
		$auth->set_access_type( $auth::AUTH_TYPE_S2S );
		$data = array(
			'qty' => $qty
		);

		$resource = '/v1/compositions/' . $configuration . '/cartinfo';
		return self::request( 'GET', $resource, $data, $auth );
	}

	/**
	 * Order containing Zakeke customized products placed.
	 *
	 * @param array $data The data of the order.
	 *
	 * @throws Exception
	 * @return void
	 */
	public function place_order( $data ) {
		$auth = zakeke_get_auth();
		if ( isset( $data['customerID'] ) ) {
			$auth->set_customer( $data['customerID'] );
		} elseif ( isset( $data['visitorID'] ) ) {
			$auth->set_guest( $data['visitorID'] );
		}

		$data['marketplaceID'] = '1';

		self::request( 'POST', '/api/orderdocs', $data, $auth );
	}

	/**
	 * Get the Zakeke design preview files
	 *
	 * @param string $designId Zakeke design identifier.
	 * @param string|null $modificationId Zakeke modification identifier.
	 *
	 * @throws Exception
	 * @return array
	 */
	public function get_previews( $designId, $modificationId = null ) {
		$auth = zakeke_get_auth();

		$data = array(
			'docid' => $designId
		);

		if ($modificationId) {
			$data['modificationID'] = $modificationId;
		}

		$json = self::request(
			'GET',
			'/api/designs/0/previewfiles',
			$data,
			$auth
		);

		$previews = array();
		foreach ( $json as $preview ) {
			if ( 'SVG' == $preview['format'] ) {
				continue;
			}

			$previewObj        = new stdClass();
			$previewObj->url   = $preview['url'];
			$previewObj->label = $preview['sideName'];
			$previews[]        = $previewObj;
		}

		return $previews;
	}

	/**
	 * Get the Zakeke design id from the design doc id.
	 *
	 * @param string $designId Zakeke design identifier (doc id).
	 *
	 * @throws Exception
	 * @return int
	 */
	public function get_designid( $designId ) {
		$auth = zakeke_get_auth();

		return self::request(
			'GET',
			"/api/docdesigns/{$designId}/designid",
			array(),
			$auth
		);
	}

	/**
	 * Get the Zakeke design output zip
	 *
	 * @param string $designId Zakeke design identifier.
	 *
	 * @throws Exception
	 * @return string
	 */
	public function get_zakeke_output_zip( $designId, $modificationId = null ) {
		$auth = zakeke_get_auth();

		$data = array(
			'docid' => $designId
		);

		if (is_null($modificationId)) {
			$json = self::request( 'GET', '/api/designs/0/outputfiles/zip', $data, $auth );
		} else {
			$json = self::request( 'GET', '/api/designs/0/outputfiles/zip/' . $modificationId, $data, $auth );
		}

		return $json['url'];
	}

	/**
	 * Conditionally log Zakeke Webservice Call
	 *
	 * @param  string $url Zakeke url.
	 * @param  string $method HTTP Method.
	 * @param  array $args HTTP Request Body.
	 * @param  array $response WP HTTP Response.
	 *
	 * @return void
	 */
	private function maybe_log( $url, $method, $args, $response ) {
		if ( 'yes' !== $this->debug ) {
			return;
		}

		$this->logger->add( 'zakeke', "Zakeke Webservice Call: $url \n METHOD: $method \n BODY: " . print_r( $args,
				true ) . ' \n RESPONSE: ' . print_r( $response, true ) );
	}
}
