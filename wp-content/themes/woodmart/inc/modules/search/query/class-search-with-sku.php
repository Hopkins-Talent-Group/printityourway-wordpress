<?php
/**
 * Search_With_Sku class.
 *
 * @package woodmart
 */

namespace XTS\Modules\Search\Query;

use XTS\Singleton;

/**
 * Search_With_Sku class.
 */
class Search_With_Sku extends Singleton {
	/**
	 * Init.
	 */
	public function init() {
		add_filter( 'woocommerce_price_filter_sql', array( $this, 'update_price_filter_query_by_sku' ) );
		add_filter( 'woocommerce_get_filtered_term_product_counts_query', array( $this, 'update_filtered_term_product_counts_by_sku' ) );

		add_filter( 'relevanssi_content_to_index', array( $this, 'add_variation_skus_to_relevanssi_index' ), 10, 2 );
	}

	/**
	 * Extends the WordPress search query by adding a condition for product sku search.
	 *
	 * This function adds an additional condition to the SQL query to search within specified product sku
	 * and searches by their names using the `LIKE` expression.
	 *
	 * @param string $search The current SQL search query.
	 * @param string $search_term The search term to be used for searching within taxonomies.
	 *
	 * @return string The modified SQL search query.
	 */
	public function extend_query( $search, $search_term ) {
		global $wpdb;

		if ( empty( $search_term ) ) {
			return $search;
		}

		$search_terms = explode( ',', $search_term );
		$search_ids   = array();

		foreach ( $search_terms as $term ) {
			$search_ids = $this->get_product_ids_by_sku( $term );
		}

		$search_ids = array_filter( array_map( 'absint', $search_ids ) );

		if ( count( $search_ids ) > 0 ) {
			$search = str_replace( ')))', ")) OR ( {$wpdb->posts}.ID IN (" . implode( ',', $search_ids ) . ')))', $search );
		}

		return $search;
	}

	/**
	 * Updates the WooCommerce price filter SQL query to include product SKUs in the search.
	 *
	 * @param string $sql The original SQL query for the WooCommerce price filter.
	 *
	 * @return string The modified SQL query with SKU search included.
	 */
	public function update_price_filter_query_by_sku( $sql ) {
		if ( isset( $_GET['s'] ) && apply_filters( 'woodmart_search_by_sku', woodmart_get_opt( 'search_by_sku' ) ) ) {
			$sql = preg_replace( '/\s+(?=\)+$)/', '', $sql ); // The latest SQL request characters should be ")))" to use the function woodmart_sku_search_query_new.
			$sql = $this->extend_query( $sql, $_GET['s'] );
		}

		return $sql;
	}

	/**
	 * Updates the WooCommerce filtered term product counts query to include product SKUs in the search.
	 *
	 * @param array $query The original SQL query for the WooCommerce filtered term product counts.
	 *
	 * @return array The modified SQL query with SKU search included.
	 */
	public function update_filtered_term_product_counts_by_sku( $query ) {
		global $wpdb;

		if ( empty( $_GET['s'] ) || ! apply_filters( 'woodmart_search_by_sku', woodmart_get_opt( 'search_by_sku' ) ) ) {
			return $query;
		}

		$search_ids = $this->get_product_ids_by_sku( woodmart_clean( $_GET['s'] ) );

		if ( $search_ids ) {
			$placeholders   = implode( ',', array_fill( 0, count( $search_ids ), '%d' ) );
			$sql            = $wpdb->prepare( "{$wpdb->posts}.ID IN ($placeholders)", ...$search_ids );
			$query['where'] = str_replace( '))', ") OR ({$sql}))", $query['where'] );
		}

		return $query;
	}

	/**
	 * Adds product SKUs to the Relevanssi index content.
	 *
	 * @param string $content The content to be indexed.
	 * @param object $post The post object.
	 *
	 * @return string The modified content with SKUs included.
	 */
	public function add_variation_skus_to_relevanssi_index( $content, $post ) {
		if ( ! apply_filters( 'woodmart_search_by_sku', woodmart_get_opt( 'search_by_sku' ) ) || ! woodmart_get_opt( 'relevanssi_search' ) || ! function_exists( 'relevanssi_do_query' ) ) {
			return $content;
		}

		if ( 'product' !== $post->post_type ) {
			return $content;
		}

		$products = get_posts(
			array(
				'post_parent'    => $post->ID,
				'post_type'      => 'product_variation',
				'posts_per_page' => -1,
				'fields'         => 'ids',
			)
		);

		foreach ( $products as $product_id ) {
			$sku = get_post_meta( $product_id, '_sku', true );
			if ( ! empty( $sku ) ) {
				$content .= ' ' . sanitize_text_field( $sku );
			}
		}

		return $content;
	}

	/**
	 * Get product IDs by SKU.
	 *
	 * @param string $sku The SKU to search for.
	 *
	 * @return array The product IDs that match the SKU.
	 */
	public function get_product_ids_by_sku( $sku ) {
		global $wpdb;

		$search_ids = array();

		// Include the search by id if admin area.
		if ( apply_filters( 'woodmart_search_by_id', true ) && is_numeric( $sku ) ) {
			$search_ids[] = $sku;
		}

		$like = '%' . $wpdb->esc_like( $sku ) . '%';

		// Search for variations with a matching sku and return the parent.
		$sku_to_parent_id = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT p.post_parent as post_id
					FROM {$wpdb->posts} as p
					JOIN {$wpdb->wc_product_meta_lookup} ml
					ON p.ID = ml.product_id
					AND ml.sku
					LIKE %s
					WHERE p.post_parent <> 0 
					GROUP BY p.post_parent",
				$like
			)
		);

		// Search for a regular product that matches the sku.
		$sku_to_id = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT product_id
				FROM {$wpdb->wc_product_meta_lookup}
				WHERE sku
				LIKE %s",
				$like
			),
			ARRAY_N
		);

		$sku_to_id_results = array();
		if ( is_array( $sku_to_id ) ) {
			foreach ( $sku_to_id as $id ) {
				$sku_to_id_results[] = $id[0];
			}
		}

		$product_ids = array_merge( $search_ids, $sku_to_id_results, $sku_to_parent_id );
		$product_ids = array_filter( array_map( 'absint', $product_ids ) );

		return $product_ids;
	}
}
