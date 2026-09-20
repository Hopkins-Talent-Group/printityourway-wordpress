<?php
/**
 * Out of stock manager class.
 *
 * @package woodmart
 */

namespace XTS\Modules\Out_Of_Stock_Manager;

use XTS\Admin\Modules\Options;
use XTS\Singleton;
use XTS\Modules\Layouts\Main as Builder;

/**
 * Out of stock manager class.
 */
class Main extends Singleton {
	/**
	 * Init.
	 */
	public function init() {
		add_action( 'init', array( $this, 'add_options' ) );
		add_filter( 'posts_clauses', array( $this, 'change_main_products_loop_query' ), 2000, 2 );
	}

	/**
	 * Add options in theme settings.
	 */
	public function add_options() {
		Options::add_field(
			array(
				'id'       => 'show_out_of_stock_at_the_end',
				'name'     => esc_html__( 'Show "Out of stock" products at the end (experimental)', 'woodmart' ),
				'hint'     => '<video data-src="' . WOODMART_TOOLTIP_URL . 'show_out_of_stock_at_the_end.mp4" autoplay loop muted></video>',
				'type'     => 'switcher',
				'section'  => 'product_archive_section',
				'default'  => '0',
				'on-text'  => esc_html__( 'Yes', 'woodmart' ),
				'off-text' => esc_html__( 'No', 'woodmart' ),
				'priority' => 50,
			)
		);
	}

	/**
	 * Sort out-of-stock products to display last on the main products loop.
	 *
	 * @param array    $clauses Associative array of the clauses for the query.
	 * @param WP_Query $query Current query.
	 */
	public function change_main_products_loop_query( $clauses, $query ) {
		if (
			! function_exists( 'is_woocommerce' ) ||
			! is_woocommerce() ||
			! $query->is_main_query() ||
			! $query->get( 'wc_query' ) === 'product_query' ||
			! woodmart_get_opt( 'show_out_of_stock_at_the_end' )
		) {
			return $clauses;
		}

		global $wpdb;

		$clauses['join'] .= " LEFT JOIN {$wpdb->postmeta} AS stock_status_meta 
		ON ({$wpdb->posts}.ID = stock_status_meta.post_id AND stock_status_meta.meta_key = '_stock_status') ";

		$stock_order = "CASE stock_status_meta.meta_value 
			WHEN 'outofstock' THEN 1 
			ELSE 0 
		END ASC";

		if ( ! empty( $clauses['orderby'] ) ) {
			$clauses['orderby'] = $stock_order . ', ' . $clauses['orderby'];
		} else {
			$clauses['orderby'] = $stock_order;
		}

		return $clauses;
	}
}

Main::get_instance();
