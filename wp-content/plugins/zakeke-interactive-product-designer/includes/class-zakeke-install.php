<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Zakeke_Install Class.
 */
class Zakeke_Install {

	/**
	 * Hook in tabs.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'check_version' ), 5 );
	}

	/**
	 * Install Zakeke.
	 */
	public static function install() {
		self::create_capabilities();

		self::update_zakeke_version();
	}

	/**
	 * Create Zakeke capabilities.
	 */
	public static function create_capabilities() {
		global $wp_roles;

		if ( ! class_exists( 'WP_Roles' ) ) {
			return;
		}

		if ( ! isset( $wp_roles ) ) {
			$zakeke_wp_roles = new WP_Roles();
			$zakeke_wp_roles->add_cap( 'shop_manager', Zakeke::CAPABILITY );
			$zakeke_wp_roles->add_cap( 'administrator', Zakeke::CAPABILITY );
		}

		$wp_roles->add_cap( 'shop_manager', Zakeke::CAPABILITY );
		$wp_roles->add_cap( 'administrator', Zakeke::CAPABILITY );
	}

	/**
	 * Check Zakeke version and run the updater is required.
	 *
	 * This check is done on all requests and runs if he versions do not match.
	 */
	public static function check_version() {
		if ( ! defined( 'IFRAME_REQUEST' ) && get_option( 'zakeke_version' ) !== get_zakeke()->version ) {
			self::install();
		}
	}

	/**
	 * Update Zakeke version to current.
	 */
	private static function update_zakeke_version() {
		delete_option( 'zakeke_version' );
		add_option( 'zakeke_version', get_zakeke()->version );
	}
}

Zakeke_Install::init();
