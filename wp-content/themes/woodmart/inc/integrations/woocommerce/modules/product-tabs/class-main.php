<?php
/**
 * Custom product tabs class.
 *
 * @package woodmart
 */

namespace XTS\Modules\Custom_Product_Tabs;

use XTS\Admin\Modules\Options;
use XTS\Singleton;

/**
 * Custom product tabs class.
 */
class Main extends Singleton {
	/**
	 * Init.
	 */
	public function init() {
		add_action( 'init', array( $this, 'add_options' ) );

		if ( ! woodmart_woocommerce_installed() || ! woodmart_get_opt( 'custom_product_tabs_enabled' ) ) {
			return;
		}

		$this->include_files();
	}

	/**
	 * Add options in theme settings.
	 *
	 * @return void
	 */
	public function add_options() {
		Options::add_field(
			array(
				'id'          => 'custom_product_tabs_enabled',
				'name'        => esc_html__( 'Custom tabs', 'woodmart' ),
				'description' => esc_html__( 'Enables a custom post type for adding tabs to single product pages. You can find it under Dashboard → Products → Custom Tabs.', 'woodmart' ),
				'type'        => 'switcher',
				'section'     => 'product_tabs',
				'default'     => '0',
				'on-text'     => esc_html__( 'On', 'woodmart' ),
				'off-text'    => esc_html__( 'Off', 'woodmart' ),
				'priority'    => 105,
			)
		);
	}

	/**
	 * Include files.
	 *
	 * @return void
	 */
	public function include_files() {
		$files = array(
			'class-manager',
			'class-admin',
			'class-frontend',
		);

		foreach ( $files as $file ) {
			require_once get_parent_theme_file_path( WOODMART_FRAMEWORK . '/integrations/woocommerce/modules/product-tabs/' . $file . '.php' );
		}
	}
}

Main::get_instance();
