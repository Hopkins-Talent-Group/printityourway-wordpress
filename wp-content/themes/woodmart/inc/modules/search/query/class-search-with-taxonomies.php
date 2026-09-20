<?php
/**
 * Search query class.
 *
 * @package woodmart
 */

namespace XTS\Modules\Search\Query;

use XTS\Singleton;

/**
 * Search synonyms class.
 */
class Search_With_Taxonomies extends Singleton {
	/**
	 * Init.
	 */
	public function init() {
		add_filter( 'posts_search_orderby', array( $this, 'change_search_orderby_query' ), 20, 2 );
	}

	/**
	 * Extends the WordPress search query by adding a condition for taxonomy search.
	 *
	 * This function adds an additional condition to the SQL query to search within specified taxonomies
	 * and searches by their names using the `LIKE` expression.
	 *
	 * @param string $search The current SQL search query.
	 * @param string $search_term The search term to be used for searching within taxonomies.
	 *
	 * @return string The modified SQL search query.
	 */
	public function extend_query( $search, $search_term ) {
		global $wpdb;
		$search_taxonomies = array();

		if ( woodmart_get_opt( 'search_by_product_categories' ) ) {
			$search_taxonomies[] = 'product_cat';
		}

		if ( woodmart_get_opt( 'search_by_product_tag' ) ) {
			$search_taxonomies[] = 'product_tag';
		}

		if ( woodmart_get_opt( 'search_by_product_brands' ) && taxonomy_exists( 'product_brand' ) ) {
			$search_taxonomies[] = 'product_brand';
		}

		if ( woodmart_get_opt( 'search_by_product_attributes' ) ) {
			$search_taxonomies = array_merge(
				$search_taxonomies,
				array_filter(
					get_object_taxonomies( 'product', 'names' ),
					function ( $taxonomy ) {
						return strpos( $taxonomy, 'pa_' ) === 0;
					}
				)
			);
		}

		$search_taxonomies_str = "'" . implode( "','", $search_taxonomies ) . "'";
		$search_terms          = explode( ',', $search_term );
		$last_key              = array_key_last( $search_terms );
		$search_term_query     = '';

		foreach ( $search_terms as $key => $term ) {
			$term_like          = '%' . trim( $term ) . '%';
			$search_term_query .= $wpdb->prepare(
				'(
					(t.name LIKE %s) OR
					(tt.description LIKE %s)
				)',
				$term_like,
				$term_like
			);

			if ( $last_key !== $key ) {
				$search_term_query .= ' OR ';
			}
		}

		$new_search = " OR (
				{$wpdb->posts}.ID IN (
					SELECT DISTINCT tr.object_id
					FROM {$wpdb->term_relationships} tr
					JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
					JOIN {$wpdb->terms} t ON tt.term_id = t.term_id
					JOIN {$wpdb->termmeta} tm ON tt.term_id = tm.term_id
					WHERE tt.taxonomy IN ($search_taxonomies_str)
					AND ($search_term_query)
				)
		)";

		if ( ! is_user_logged_in() ) {
			$search = str_replace( " AND ({$wpdb->posts}.post_password = '') ", '', $search );
		}

		$start = strpos( $search, 'AND (' );
		$end   = strrpos( $search, ')', $start );

		if ( false !== $start && false !== $end ) {
			$start     += 5;
			$inner_text = substr( $search, $start, $end - $start );
		}

		$inner_text = str_replace( ')) AND ((', ')) OR ((', $inner_text );

		$inner_text .= $new_search;

		$search = " AND ($inner_text)";

		if ( ! is_user_logged_in() ) {
			$search .= " AND ({$wpdb->posts}.post_password = '') ";
		}

		return $search;
	}

	/**
	 * Extends the product search orderby conditions with taxonomies, tags, and attributes.
	 *
	 * This function adds additional sorting conditions to the main product search query
	 * in WooCommerce. It extends sorting by the following criteria:
	 * product title, product excerpt, product content, product categories,
	 * product tags, and product attributes.
	 *
	 * @param string   $search_orderby The primary orderby clause.
	 * @param WP_Query $query The current WP_Query object.
	 *
	 * @return string The updated orderby clause with additional criteria.
	 */
	public function change_search_orderby_query( $search_orderby, $query ) {
		global $wpdb;

		if ( ! woodmart_woocommerce_installed() ) {
			return $search_orderby;
		}

		$is_main_search = ! is_admin() && $query->is_search() && $query->is_main_query();
		$is_ajax_search = is_ajax() && isset( $_REQUEST['action'] ) && isset( $_REQUEST['query'] ) && isset( $_REQUEST['post_type'] ) && ! empty( $_REQUEST['query'] ) && 'woodmart_ajax_search' === $_REQUEST['action'];
		$post_type      = $is_ajax_search ? sanitize_text_field( $_REQUEST['post_type'] ) : $query->get( 'post_type' );
		$post_types     = (array) $post_type;

		if ( ! in_array( 'product', $post_types, true ) || empty( $search_orderby ) || ( ! woodmart_get_opt( 'search_by_product_categories' ) && ! woodmart_get_opt( 'search_by_product_tag' ) && ! woodmart_get_opt( 'search_by_product_attributes' ) ) ) {
			return $search_orderby;
		}

		if ( $is_main_search || $is_ajax_search ) {
			$search_term          = $is_ajax_search ? sanitize_text_field( $_REQUEST['query'] ) : $query->get( 's' );
			$search_term          = $wpdb->esc_like( $search_term );
			$search_orderby_title = array_map(
				function ( $term ) use ( $wpdb ) {
					$term = trim( $term, ',' );

					return trim( $wpdb->prepare( "{$wpdb->posts}.post_title LIKE %s ", '%' . $term . '%' ) );
				},
				explode( ' ', $search_term )
			);
			$num_terms            = count( $search_orderby_title );
			$new_search_orderby   = $wpdb->prepare( "WHEN {$wpdb->posts}.post_title LIKE %s THEN 1 ", '%' . $search_term . '%' );

			/*
			 * Sanity limit, sort as sentence when more than 6 terms
			 * (few searches are longer than 6 terms and most titles are not).
			 */
			if ( $num_terms < 7 ) {
				// All words in title.
				$new_search_orderby .= 'WHEN ' . implode( ' AND ', $search_orderby_title ) . ' THEN 2 ';
				// Any word in title, not needed when $num_terms == 1.
				if ( $num_terms > 1 ) {
					$new_search_orderby .= 'WHEN ' . implode( ' OR ', $search_orderby_title ) . ' THEN 3 ';
				}
			}

			$new_search_orderby .= $wpdb->prepare( "WHEN {$wpdb->posts}.post_excerpt LIKE %s THEN 4 ", '%' . $search_term . '%' );
			$new_search_orderby .= $wpdb->prepare( "WHEN {$wpdb->posts}.post_content LIKE %s THEN 5 ", '%' . $search_term . '%' );

			$search_terms            = explode( ',', $search_term );
			$last_key                = array_key_last( $search_terms );
			$search_query_categories = '';
			$search_query_attributes = '';
			$search_query_tag        = '';

			foreach ( $search_terms as $key => $term ) {
				$term_like = '%' . trim( $term ) . '%';

				$search_query_categories .= $wpdb->prepare(
					'(
						(t.name LIKE %s) OR
						(tt.description LIKE %s)
					)',
					$term_like,
					$term_like
				);

				$search_query_attributes .= $wpdb->prepare(
					'(pm.meta_value LIKE %s)',
					$term_like
				);

				$search_query_tag .= $wpdb->prepare(
					'(t.name LIKE %s)',
					$term_like
				);

				if ( $last_key !== $key ) {
					$search_query_categories .= ' OR ';
					$search_query_attributes .= ' OR ';
					$search_query_tag        .= ' OR ';
				}
			}

			if ( woodmart_get_opt( 'search_by_product_categories' ) ) {
				$new_search_orderby .= "WHEN (
					SELECT COUNT(*)
					FROM {$wpdb->term_relationships} tr
					JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
					JOIN {$wpdb->terms} t ON tt.term_id = t.term_id
					JOIN {$wpdb->termmeta} tm ON tt.term_id = tm.term_id
					WHERE tr.object_id = {$wpdb->posts}.ID
					AND ($search_query_categories)
				) > 0 THEN 6 ";
			}

			if ( woodmart_get_opt( 'search_by_product_attributes' ) ) {
				$new_search_orderby .= "WHEN (
					SELECT COUNT(*)
					FROM {$wpdb->postmeta} pm
					WHERE pm.post_id = {$wpdb->posts}.ID
					AND pm.meta_key LIKE 'attribute_%'
					AND ($search_query_attributes)
				) > 0 THEN 7 ";
			}

			if ( woodmart_get_opt( 'search_by_product_tag' ) ) {
				$new_search_orderby .= "WHEN (
					SELECT COUNT(*)
					FROM {$wpdb->term_relationships} tr
					JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
					JOIN {$wpdb->terms} t ON tt.term_id = t.term_id
					WHERE tr.object_id = {$wpdb->posts}.ID
					AND tt.taxonomy = 'product_tag'
					AND ($search_query_tag)
				) > 0 THEN 8 ";
			}

			if ( $new_search_orderby ) {
				$new_search_orderby = '(CASE ' . $new_search_orderby . 'ELSE 9 END)';
			}

			$search_orderby = $new_search_orderby;
		}

		return $search_orderby;
	}
}
