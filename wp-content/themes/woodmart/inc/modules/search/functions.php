<?php

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

use XTS\Modules\Search\Frontend\Dropdown_Search;
use XTS\Modules\Search\Frontend\Full_Screen_Search;

if ( ! function_exists( 'woodmart_search_form' ) ) {
	/**
	 * Search form function.
	 *
	 * @param array $args Arguments.
	 */
	function woodmart_search_form( $args = array() ) {
		$search_type = isset( $args['display'] ) ? $args['display'] : 'form';

		switch ( $search_type ) {
			case 'full-screen-2':
				$dropdown_search_args    = isset( $args['dropdown_search_args'] ) ? $args['dropdown_search_args'] : $args;
				$full_screen_search_args = isset( $args['full_screen_search_args'] ) ? $args['full_screen_search_args'] : $args;
				$dropdown_form           = new Dropdown_Search( $dropdown_search_args );

				$dropdown_form->render();

				Full_Screen_Search::add_args( $full_screen_search_args );
				break;
			case 'full-screen':
				$search_args = isset( $args['full_screen_search_args'] ) ? $args['full_screen_search_args'] : $args;

				Full_Screen_Search::add_args( $search_args );

				break;
			case 'dropdown':
			case 'form':
				$search_args   = isset( $args['dropdown_search_args'] ) ? $args['dropdown_search_args'] : $args;
				$dropdown_form = new Dropdown_Search( $search_args );

				$dropdown_form->render();

				break;
		}
	}
}
