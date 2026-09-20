<?php
/**
 * Search Processor class.
 *
 * @package woodmart
 */

namespace XTS\Modules\Search\Query;

use XTS\Singleton;

/**
 * Class SearchProcessor
 *
 * This class processes search queries, including handling AJAX searches,
 * synonyms, exclusions, and building SQL search queries.
 */
class Search_With_Synonyms extends Singleton {
	/**
	 * Array of synonyms to be used in the search.
	 *
	 * @var array $synonyms Array of synonyms to be used in the search.
	 */
	private $synonyms;

	/**
	 * Prefix used to indicate terms to be excluded from the search.
	 *
	 * @var string $exclusion_prefix Prefix used to indicate terms to be excluded from the search.
	 */
	private $exclusion_prefix;

	/**
	 * Array of columns to search within.
	 *
	 * @var array $search_columns Array of columns to search within.
	 */
	private $search_columns;

	/**
	 * Init.
	 */
	public function init() {
		$this->synonyms         = $this->get_synonyms();
        $this->exclusion_prefix = apply_filters('wp_query_search_exclusion_prefix', '-'); // phpcs:ignore
		$this->search_columns   = array( 'post_title', 'post_excerpt', 'post_content' );
	}

	/**
	 * Get the synonyms from the theme options.
	 *
	 * @return array Array of synonyms.
	 */
	public function get_synonyms() {
		$synonyms_config = woodmart_get_opt( 'search_synonyms' );

		if ( empty( $synonyms_config ) ) {
			return array();
		}

		$synonyms_lines = array_map( 'strtolower', explode( PHP_EOL, $synonyms_config ) );
		$synonyms_list  = array();

		foreach ( $synonyms_lines as $line ) {
			$parts = explode( '=', $line );

			if ( count( $parts ) < 2 ) {
				continue;
			}

			$term    = trim( $parts[0] );
			$matches = array_map( 'trim', explode( ',', $parts[1] ) );

			$synonyms_list[ $term ] = $matches;
		}

		return $synonyms_list;
	}

	/**
	 * Get the original terms from the search term.
	 *
	 * @param string $search_term The search term.
	 * @return array Array of unique original terms.
	 */
	public function get_original_terms( $search_term ) {
		return array_unique( explode( ' ', $search_term ) );
	}

	/**
	 * Generate new terms including synonyms for the original terms.
	 *
	 * @param array $original_terms Array of original search terms.
	 * @return array Array of new search terms including synonyms.
	 */
	public function generate_new_terms( $original_terms ) {
		$new_terms = array();

		foreach ( $original_terms as $term ) {
			$new_terms[] = $term;

			if ( isset( $this->synonyms[ $term ] ) ) {
				foreach ( $this->synonyms[ $term ] as $synonym ) {
					$new_terms[] = $synonym;
				}
			}
		}
		return $new_terms;
	}

	/**
	 * Apply exclusion rules to the search terms.
	 *
	 * @param array $terms Array of search terms.
	 * @return array Array containing include terms and exclude terms.
	 */
	public function apply_exclusion( $terms ) {
		$include_terms = array();
		$exclude_terms = array();

		foreach ( $terms as $term ) {
			$exclude = $this->exclusion_prefix && str_starts_with( $term, $this->exclusion_prefix );

			if ( ! $exclude ) {
				if ( ! in_array( '-' . $term, $exclude_terms, true ) ) {
					$include_terms[] = $term;
				}
			} else {
				$exclude_terms[] = $term;
				$key             = array_search( substr( $term, 1 ), $include_terms, true );

				if ( false !== $key ) {
					unset( $include_terms[ $key ] );
				}
			}
		}
		return array( $include_terms, $exclude_terms );
	}

	/**
	 * Build the SQL search query from the terms.
	 *
	 * @param array $terms            Array of search terms.
	 * @param bool  $is_include_terms Whether to include or exclude the terms.
	 * @return string The SQL search query.
	 */
	public function build_search_query( $terms, $is_include_terms = true ) {
		global $wpdb;

		$search_query = '';

		foreach ( $terms as $term ) {
			if ( $is_include_terms ) {
				$like_op   = 'LIKE';
				$andor_op  = 'OR';
				$search_op = ' OR ';
			} else {
				$like_op   = 'NOT LIKE';
				$andor_op  = 'AND';
				$term      = substr( $term, 1 );
				$search_op = ' AND ';
			}

			if ( empty( $search_query ) ) {
				$search_op = '';
			}

			$like                 = '%' . $wpdb->esc_like( $term ) . '%';
			$search_columns_parts = array();

			foreach ( $this->search_columns as $search_column ) {
				$search_columns_parts[] = $wpdb->prepare( "({$wpdb->posts}.$search_column $like_op %s)", $like );
			}

			$search_query .= "$search_op(" . implode( " $andor_op ", $search_columns_parts ) . ')';
		}

		return $search_query;
	}

	/**
	 * Combine the include and exclude search queries.
	 *
	 * @param string $include_search The include search query.
	 * @param string $exclude_search The exclude search query.
	 * @return string The combined search query.
	 */
	public function combine_search_queries( $include_search, $exclude_search ) {
		if ( ! empty( $include_search ) && ! empty( $exclude_search ) ) {
			$include_search = "({$include_search})";
		}

		return $include_search . ( ! empty( $exclude_search ) ? ' AND ' . $exclude_search : '' );
	}

	/**
	 * Finalize the SQL search query.
	 *
	 * @param string $search The new search query.
	 *
	 * @return string The finalized search query.
	 */
	public function finalize_search_query( $search ) {
		global $wpdb;

		if ( ! empty( $search ) ) {
			$search = " AND ({$search}) ";

			if ( ! is_user_logged_in() ) {
				$search .= " AND ({$wpdb->posts}.post_password = '') ";
			}
		}

		return $search;
	}

	/**
	 * Extends the WordPress search query by adding a condition for synonyms search.
	 *
	 * This function adds an additional condition to the SQL query to search within specified synonyms
	 * and searches by their names using the `LIKE` expression.
	 *
	 * @param string $search The current SQL search query.
	 * @param string $search_term The search term to be used for searching within synonyms.
	 *
	 * @return string The modified SQL search query.
	 */
	public function extend_query( $search, $search_term ) {
		if ( empty( $this->synonyms ) ) {
			return $search;
		}

		$original_terms                      = $this->get_original_terms( $search_term );
		$new_terms                           = $this->generate_new_terms( $original_terms );
		list($include_terms, $exclude_terms) = $this->apply_exclusion( $new_terms );
		$include_search                      = $this->build_search_query( $include_terms, true );
		$exclude_search                      = $this->build_search_query( $exclude_terms, false );
		$search                              = $this->combine_search_queries( $include_search, $exclude_search );

		return $this->finalize_search_query( $search );
	}
}
