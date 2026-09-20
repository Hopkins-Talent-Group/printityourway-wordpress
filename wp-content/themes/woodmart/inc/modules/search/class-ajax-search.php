<?php
/**
 * The ajax search class.
 *
 * @package woodmart
 */

namespace XTS\Modules\Search;

use XTS\Singleton;
use WP_Query;

/**
 * The ajax search class.
 */
class Ajax_Search extends Singleton {
	/**
	 * Init.
	 */
	public function init() {
		add_action( 'wp_ajax_woodmart_ajax_search', array( $this, 'get_ajax_suggestions' ), 10 );
		add_action( 'wp_ajax_nopriv_woodmart_ajax_search', array( $this, 'get_ajax_suggestions' ), 10 );
	}

	/**
	 * Get the ajax search suggestions.
	 *
	 * @return void
	 */
	public function get_ajax_suggestions() {
		$main_suggestions = $this->get_main_suggestions();
		$cat_suggestions  = $this->get_product_categories_suggestions();
		$blog_suggestions = $this->get_blog_suggestions();

		$suggestions = $this->build_suggestions( $main_suggestions, $cat_suggestions, $blog_suggestions );

		wp_send_json(
			array(
				'suggestions' => $suggestions,
			)
		);
	}

	/**
	 * Get the divider for the suggestions.
	 *
	 * @param string $group The group name.
	 *
	 * @return array The divider data.
	 */
	public function get_divider( $group ) {
		switch ( $group ) {
			case 'portfolio':
				$divider = esc_html__( 'Projects', 'woodmart' );
				break;
			case 'page':
				$divider = esc_html__( 'Pages', 'woodmart' );
				break;
			case 'post':
				$divider = esc_html__( 'Blog posts', 'woodmart' );
				break;
			default:
				$divider = esc_html__( 'Products', 'woodmart' );
				break;
		}

		return array(
			'value'   => '',
			'divider' => $divider,
			'group'   => $group,
		);
	}

	/**
	 * Get the main suggestions.
	 *
	 * @return array The main suggestions.
	 */
	public function get_main_suggestions() {
		$post_type  = $this->get_post_type();
		$query_args = $this->build_query_args( $post_type );
		$query      = new WP_Query( $query_args );

		$this->apply_relevanssi_filter( $query );

		if ( $query->have_posts() ) {
			$suggestions = $this->get_query_suggestions( $query, $post_type );
		} else {
			$suggestions = array(
				array(
					'value'              => ( 'product' === $post_type ) ? esc_html__( 'No products found', 'woodmart' ) : esc_html__( 'No items found', 'woodmart' ),
					'products_not_found' => true,
					'permalink'          => '',
					'group'              => $post_type,
				),
			);
		}

		return $suggestions;
	}

	/**
	 * Get the product categories suggestions.
	 *
	 * @return array The product categories suggestions.
	 */
	public function get_product_categories_suggestions() {
		if ( ! woodmart_woocommerce_installed() ) {
			return array();
		}

		$post_type = $this->get_post_type();
		$search    = sanitize_text_field( $_REQUEST['query'] );

		if ( ! isset( $_REQUEST['include_cat_search'] ) || 'yes' !== $_REQUEST['include_cat_search'] || 'product' !== $post_type || empty( $search ) ) {
			return array();
		}

		$suggestions  = array();
		$search_query = array(
			'taxonomy'   => 'product_cat',
			'number'     => ! empty( $_REQUEST['number'] ) ? (int) $_REQUEST['number'] : 5,
			'orderby'    => 'name',
			'search'     => $search,
			'hide_empty' => false,
		);

		if ( ! empty( $_REQUEST['product_cat'] ) ) {
			$category               = get_term_by( 'slug', sanitize_text_field( $_REQUEST['product_cat'] ), 'product_cat' );
			$search_query['parent'] = $category->term_id;
		}

		$categories = get_categories( $search_query );

		if ( empty( $categories ) ) {
			return array();
		}

		$suggestions[] = array(
			'value'   => '',
			'divider' => esc_html__( 'Categories', 'woodmart' ),
			'group'   => 'categories',
		);

		foreach ( $categories as $category ) {
			$category_name = $category->name;
			$icon_data     = get_term_meta( $category->term_taxonomy_id, 'category_icon_alt', true );
			$icon_html     = $this->get_category_icon( $category_name, $icon_data );

			$data = array(
				'value'     => $category_name,
				'permalink' => get_term_link( $category ),
				'group'     => 'categories',
			);

			if ( $icon_html ) {
				$data['thumbnail'] = $icon_html;
			}

			$suggestions[] = $data;
		}

		return $suggestions;
	}

	/**
	 * Get the category icon.
	 *
	 * @param string $category_name The category name.
	 * @param array  $icon_data     The icon data.
	 *
	 * @return string The category icon HTML.
	 */
	public function get_category_icon( $category_name, $icon_data ) {
		$image_output = '';

		if ( empty( $icon_data['id'] ) ) {
			return '';
		}

		$image_output .= wp_get_attachment_image(
			$icon_data['id'],
			'woocommerce_thumbnail',
			false,
			array(
				'alt'     => esc_attr( $category_name ),
				'loading' => ! wp_lazy_loading_enabled( 'img', '' ) ? 'lazy' : '',
			)
		);

		return $image_output;
	}

	/**
	 * Get the blog suggestions.
	 *
	 * @return array The blog suggestions.
	 */
	public function get_blog_suggestions() {
		$post_type = $this->get_post_type();

		if ( ! woodmart_get_opt( 'enqueue_posts_results' ) || in_array( $post_type, array( 'post', 'any' ), true ) ) {
			return array();
		}

		$query_args  = $this->build_query_args( 'post' );
		$query       = new WP_Query( $query_args );
		$suggestions = $this->get_query_suggestions( $query, 'post' );

		return $suggestions;
	}

	/**
	 * Build the suggestions array.
	 *
	 * @param array $main_suggestions   The main suggestions.
	 * @param array $cat_suggestions    The category suggestions.
	 * @param array $blog_suggestions   The blog suggestions.
	 *
	 * @return array The combined suggestions.
	 */
	public function build_suggestions( $main_suggestions, $cat_suggestions, $blog_suggestions ) {
		$post_type   = $this->get_post_type();
		$suggestions = $cat_suggestions;

		if ( ! empty( $cat_suggestions ) || ! empty( $blog_suggestions ) ) {
			$suggestions[] = $this->get_divider( $post_type );
		}

		$suggestions = array_merge( $suggestions, $main_suggestions );

		if ( ! empty( $blog_suggestions ) ) {
			$suggestions[] = $this->get_divider( 'post' );
			$suggestions   = array_merge( $suggestions, $blog_suggestions );
		}

		if ( 1 === count( $suggestions ) && isset( $suggestions[0]['products_not_found'] ) ) {
			$suggestions[0]['no_results'] = true;
		}

		return $suggestions;
	}

	/**
	 * Get the query suggestions.
	 *
	 * @param WP_Query $query     The WP_Query object.
	 * @param string   $post_type The post type.
	 *
	 * @return array The query suggestions.
	 */
	public function get_query_suggestions( $query, $post_type ) {
		$suggestions = array();

		while ( $query->have_posts() ) {
			$query->the_post();

			if ( 'product' === $post_type && woodmart_woocommerce_installed() ) {
				$product = wc_get_product( get_the_ID() );

				$suggestions[] = array(
					'value'     => html_entity_decode( get_the_title() ),
					'permalink' => get_the_permalink(),
					'price'     => $product->get_price_html(),
					'thumbnail' => $product->get_image(),
					'sku'       => $product->get_sku() ? esc_html__( 'SKU:', 'woodmart' ) . ' ' . $product->get_sku() : '',
					'group'     => $post_type,
				);
			} else {
				$suggestions[] = array(
					'value'     => html_entity_decode( get_the_title() ),
					'permalink' => get_the_permalink(),
					'thumbnail' => get_the_post_thumbnail( null, 'medium', '' ),
					'group'     => $post_type,
				);
			}
		}

		wp_reset_postdata();

		return $suggestions;
	}

	/**
	 * Build the query arguments.
	 *
	 * @param string $post_type The post type.
	 *
	 * @return array The query arguments.
	 */
	public function build_query_args( $post_type ) {
		$query_args = array(
			'posts_per_page' => ! empty( $_REQUEST['number'] ) ? (int) $_REQUEST['number'] : 5,
			'post_status'    => 'publish',
			'post_type'      => $post_type,
			'no_found_rows'  => 1,
		);

		if ( 'product' === $post_type && woodmart_woocommerce_installed() ) {
			$query_args['tax_query']  = $this->build_product_tax_query();
			$query_args['meta_query'] = $this->build_product_meta_query();
		}

		if ( ! empty( $_REQUEST['query'] ) ) {
			$query_args['s'] = sanitize_text_field( $_REQUEST['query'] );
		}

		return apply_filters( 'woodmart_ajax_search_args', $query_args, $post_type );
	}

	/**
	 * Build the product tax query.
	 *
	 * @return array The product tax query.
	 */
	public function build_product_tax_query() {
		$product_visibility_term_ids = wc_get_product_visibility_term_ids();

		$tax_query = array(
			'relation' => 'AND',
			array(
				'taxonomy' => 'product_visibility',
				'field'    => 'term_taxonomy_id',
				'terms'    => $product_visibility_term_ids['exclude-from-search'],
				'operator' => 'NOT IN',
			),
			array(
				'taxonomy' => 'product_visibility',
				'field'    => 'name',
				'terms'    => array( 'exclude-from-search' ),
				'operator' => 'NOT IN',
			),
		);

		if ( ! empty( $_REQUEST['product_cat'] ) ) {
			$tax_query[] = array(
				'taxonomy' => 'product_cat',
				'field'    => 'slug',
				'terms'    => strip_tags( $_REQUEST['product_cat'] ),
			);
		}

		return $tax_query;
	}

	/**
	 * Build the product meta query.
	 *
	 * @return array The product meta query.
	 */
	public function build_product_meta_query() {
		$meta_query = array();

		if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {
			$meta_query[] = array(
				'key'     => '_stock_status',
				'value'   => 'outofstock',
				'compare' => 'NOT IN',
			);
		}

		return $meta_query;
	}

	/**
	 * Get the post type from the request.
	 *
	 * @return string The post type.
	 */
	public function get_post_type() {
		$allowed_types = array( 'post', 'product', 'portfolio', 'any', 'page' );
		$post_type     = 'product';

		if ( ! empty( $_REQUEST['post_type'] ) && in_array( $_REQUEST['post_type'], $allowed_types ) ) {
			$post_type = strip_tags( $_REQUEST['post_type'] );
		}

		return $post_type;
	}

	/**
	 * Apply the Relevanssi filter to the query.
	 *
	 * @param WP_Query $query The WP_Query object.
	 */
	public function apply_relevanssi_filter( $query ) {
		if ( woodmart_get_opt( 'relevanssi_search' ) && function_exists( 'relevanssi_do_query' ) ) {
			add_filter( 'relevanssi_hits_filter', array( $this, 'update_hits_filter_by_product_sku' ), 10, 2 );
			relevanssi_do_query( $query );
		}
	}

	/**
	 * Update the hits filter by product SKU.
	 *
	 * @param array    $filter_data The filter data.
	 * @param WP_Query $query      The WP_Query object.
	 *
	 * @return array The updated filter data.
	 */
	public function update_hits_filter_by_product_sku( $filter_data, $query ) {
		if ( ! apply_filters( 'woodmart_search_by_sku', woodmart_get_opt( 'search_by_sku' ) ) || ! isset( $query->query['post_type'] ) || 'product' !== $query->query['post_type'] ) {
			return $filter_data;
		}

		$args = array(
			'post_type'      => 'product',
			'posts_per_page' => -1,
			'meta_query'     => array(
				array(
					'key'     => '_sku',
					'value'   => $query->query['s'],
					'compare' => 'LIKE',
				),
				array(
					'key'     => '_visibility',
					'value'   => array( 'catalog', 'hidden' ),
					'compare' => 'NOT IN',
				),
			),
		);

		$posts = get_posts( $args );

		if ( $posts ) {
			$product_ids = array_column( (array) $filter_data[0], 'ID' );

			foreach ( $posts as $post ) {
				if ( ! in_array( apply_filters( 'wpml_object_id', $post->ID, 'product', true ), $product_ids ) ) {
					array_unshift( $filter_data[0], $post );
				}
			}
		}

		return $filter_data;
	}
}

Ajax_Search::get_instance();
