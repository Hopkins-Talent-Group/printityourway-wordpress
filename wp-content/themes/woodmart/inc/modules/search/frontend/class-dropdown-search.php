<?php
/**
 * The main search class.
 *
 * @package woodmart
 */

namespace XTS\Modules\Search\Frontend;

/**
 * The dropdown search form class.
 */
class Dropdown_Search extends Search_Form {
	/**
	 * Search type.
	 *
	 * @var string
	 */
	public $search_type = 'dropdown';

	/**
	 * Get css classes for main search wrapper.
	 *
	 * @return string
	 */
	public function get_wrapper_classes() {
		$wrapper_classes = parent::get_wrapper_classes();

		if ( 'dropdown' === $this->args['type'] ) {
			$wrapper_classes .= ' wd-dropdown';
		}

		return $wrapper_classes;
	}

	/**
	 * Get css classes for search results dropdown.
	 *
	 * @return string
	 */
	public function get_dropdowns_classes() {
		$dropdowns_classes  = 'wd-dropdown wd-scroll';
		$dropdowns_classes .= parent::get_dropdowns_classes();

		return $dropdowns_classes;
	}

	/**
	 * Enqueue scripts and styles.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		parent::enqueue_scripts();

		woodmart_enqueue_inline_style( 'wd-search-dropdown' );
	}
}
