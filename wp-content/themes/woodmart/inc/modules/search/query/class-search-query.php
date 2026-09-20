<?php
/**
 * Search query class.
 *
 * @package woodmart
 */

namespace XTS\Modules\Search\Query;

use XTS\Singleton;

/**
 * Search query class.
 */
class Search_Query extends Singleton {
	/**
	 * Init.
	 */
	public function init() {
		add_filter( 'posts_search', array( $this, 'extend_search_query' ), 10, 2 );
	}

	/**
	 * Change default search query.
	 *
	 * @param string $search The search query.
	 * @param object $query  The WP_Query object.
	 *
	 * @return string
	 */
	public function extend_search_query( $search, $query ) {
		global $wpdb;

		if ( ! woodmart_woocommerce_installed() || empty( $search ) ) {
			return $search;
		}

		$is_main_search = ! is_admin() && $query->is_search() && $query->is_main_query();
		$is_ajax_search = is_ajax() && isset( $_REQUEST['action'] ) && isset( $_REQUEST['query'] ) && isset( $_REQUEST['post_type'] ) && ! empty( $_REQUEST['query'] ) && 'woodmart_ajax_search' === $_REQUEST['action'];
		$post_type      = $is_ajax_search ? sanitize_text_field( $_REQUEST['post_type'] ) : $query->get( 'post_type' );
		$post_types     = (array) $post_type;

		if ( ! in_array( 'product', $post_types, true ) ) {
			return $search;
		}

		if ( $is_main_search || $is_ajax_search ) {
			$search_term = $is_ajax_search ? sanitize_text_field( $_REQUEST['query'] ) : $query->get( 's' );
			$search_term = $wpdb->esc_like( $search_term );

			if ( woodmart_get_opt( 'search_synonyms' ) ) {
				$search_with_synonyms = Search_With_Synonyms::get_instance();
				$search               = $search_with_synonyms->extend_query( $search, $search_term );
			}

			if ( apply_filters( 'woodmart_search_by_sku', woodmart_get_opt( 'search_by_sku' ) ) ) {
				$search_with_sku = Search_With_Sku::get_instance();
				$search          = $search_with_sku->extend_query( $search, $search_term );
			}

			if (
				woodmart_get_opt( 'search_by_product_categories' ) ||
				woodmart_get_opt( 'search_by_product_tag' ) ||
				woodmart_get_opt( 'search_by_product_attributes' ) ||
				woodmart_get_opt( 'search_by_product_brands' )
			) {
				$search_with_taxonomies = Search_With_Taxonomies::get_instance();
				$search                 = $search_with_taxonomies->extend_query( $search, $search_term );
			}
		}

		return $search;
	}
}

Search_Query::get_instance();
