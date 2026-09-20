<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Zakeke_Auth Class.
 */
class Zakeke_Auth extends Zakeke_Auth_Base {

	/**
	 * Get the type of the token.
	 *
	 * @return string
	 */
	public function get_token_type() {
		return 'tokenOwin';
	}

	/**
	 * Translate the eventual customer and guest identifier to the legacy auth method
	 *
	 * @return array
	 */
	private function translate_additional_data() {
		$customer = $this->get_customer();
		$guest    = $this->get_guest();

		$data = array();

		if ( ! is_null( $customer )  ) {
			$data['customercode'] = $customer;
		}

		if ( ! is_null( $guest ) ) {
			$data['visitorcode'] = $guest;
		}

		return $data;
	}

	private function _get_auth_token() {
		global $wp_version;

		$client_id  = $this->integration->get_option( 'client_id' );
		$secret_key = $this->integration->get_option( 'secret_key' );

		$data = array_merge( $this->translate_additional_data(), array(
			'grant_type' => 'client_credentials'
		) );

		if ( ! empty( $this->get_access_type() ) ) {
			$data['access_type'] = $this->get_access_type();
		}

		$request_args = array(
			'method'      => 'POST',
			'headers'     => array(
				'Authorization' => 'Basic ' . base64_encode( $client_id . ':' . $secret_key ),
				'Content-Type' => 'application/x-www-form-urlencoded',
				'Accept'       => 'application/json',
				'User-Agent'   => 'woocommerce-zakeke-configurator/' . ZAKEKE_VERSION . '; WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' )
			),
			'body'        => http_build_query($data, '', '&')
		);

		$url          = ZAKEKE_WEBSERVICE_URL . '/token';
		$raw_response = wp_remote_request( $url, $request_args );

		$this->maybe_log( $url, 'POST', $request_args, $raw_response );

		if ( is_wp_error( $raw_response )
			|| ( is_array( $raw_response )
				&& $raw_response['response']['code']
				&& floor( $raw_response['response']['code'] ) / 100 >= 4 )
		) {
			throw new Exception( 'Zakeke_Auth::get_auth_token failed' );
		}

		$json   = wp_remote_retrieve_body( $raw_response );
		$result = json_decode( $json, true );

		return $result['access_token'];
	}

	/**
	 * Zakeke authentication token.
	 *
	 * @throws Exception
	 * @return string
	 */
	public function get_auth_token() {
		return zakeke_retry(function () {
			return $this->_get_auth_token();
		});
	}

	/**
	 * Zakeke authentication token.
	 *
	 * @param array $request_args
	 *
	 * @throws Exception
	 * @return array
	 */
	public function set_authentication( $request_args ) {
		$auth_token                               = $this->get_auth_token();
		$request_args['headers']['Authorization'] = 'Bearer ' . $auth_token;
		return $request_args;
	}

	/**
	 * Zakeke authentication token for the customizer.
	 *
	 * @param array $data
	 * @param string $token
	 *
	 * @return array
	 */
	public function set_customizer_token( $data, $token ) {
		$data['tokenOwin'] = $token;
		return $data;
	}

	/**
	 * Conditionally log Zakeke Auth Call
	 *
	 * @param  string $url Zakeke url.
	 * @param  string $method HTTP Method.
	 * @param  array $args HTTP Request Body.
	 * @param  array $response WP HTTP Response.
	 *
	 * @return void
	 */
	private function maybe_log( $url, $method, $args, $response ) {
		if ( 'yes' !== $this->integration->debug ) {
			return;
		}

		$this->logger->add( 'zakeke', "Zakeke Auth Call URL: $url \n METHOD: $method \n BODY: " . print_r( $args,
				true ) . ' \n RESPONSE: ' . print_r( $response, true ) );
	}
}
