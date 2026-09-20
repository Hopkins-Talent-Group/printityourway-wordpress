<?php
/**
 * Custom product tabs class.
 *
 * @package woodmart
 */

namespace XTS\Modules\Custom_Product_Tabs;

use XTS\Singleton;

/**
 * Custom product tabs class.
 */
class Frontend extends Singleton {
	/**
	 * Manager instance.
	 *
	 * @var Manager instanse.
	 */
	public $manager;

	/**
	 * Init.
	 */
	public function init() {
		add_filter( 'wp', array( $this, 'hooks' ) );
	}

	public function hooks() {
		$this->manager = Manager::get_instance();

		add_filter( 'woocommerce_product_tabs', array( $this, 'add_custom_product_tabs' ), 98 );
	}

	/**
	 * Add custom product tabs.
	 *
	 * @param array $tabs List of single product tabs args.
	 *
	 * @return array
	 */
	public function add_custom_product_tabs( $tabs ) {
		global $product;

		$allowed_tabs = $this->manager->get_allowed_tabs( $product );
		$tabs         = array_merge( $tabs, $allowed_tabs );

		return $tabs;
	}
}

Frontend::get_instance();
