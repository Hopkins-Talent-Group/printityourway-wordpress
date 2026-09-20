<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Zakeke_Auth_Legacy Class.
 */
class Zakeke_Auth_Legacy extends Zakeke_Auth_Base {

	/**
	 * Get the type of the token.
	 *
	 * @return string
	 */
	public function get_token_type() {
		return 'token';
	}

	/**
	 * Get the minimal data required to get the authentication token.
	 *
	 * @return array
	 */
	private function legacy_auth_data() {
		$data = array(
			'user' => $this->integration->get_option( 'username' ),
			'pwd'  => $this->integration->get_option( 'password' )
		);

		return $data;
	}

	/**
	 * Translate the eventual customer and guest identifier to the legacy auth method.
	 *
	 * @return array
	 */
	private function translate_additional_data() {
		$customer = $this->get_customer();
		$guest    = $this->get_guest();

		$data = array();

		if ( ! is_null( $customer )  ) {
			$data['cc'] = $customer;
		}

		if ( ! is_null( $guest ) ) {
			$data['vc'] = $guest;
		}

		return $data;
	}

	private function _get_auth_token() {
		$res = $this->webservice->request( 'GET', '/api/Login', array_merge( $this->translate_additional_data(), $this->legacy_auth_data() ) );
		return $res['token'];
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
		$auth_token                              = $this->get_auth_token();
		$request_args['headers']['X-Auth-Token'] = $auth_token;
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
		$data['token'] = $token;
		return $data;
	}
}
