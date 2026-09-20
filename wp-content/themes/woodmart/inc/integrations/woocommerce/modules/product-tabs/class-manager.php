<?php
/**
 * Custom product tabs class.
 *
 * @package woodmart
 */

namespace XTS\Modules\Custom_Product_Tabs;

use XTS\Singleton;
use WC_Product;
use Elementor\Plugin;
use XTS\Gutenberg\Blocks_Assets;
use XTS\Gutenberg\Post_CSS;

/**
 * Custom product tabs class.
 */
class Manager extends Singleton {
	/**
	 * Transient name for 'All Custom Product Tabs post ids'.
	 *
	 * @var string $transient_product_tabs_ids .
	 */
	public $transient_product_tabs_ids = 'wd_transient_product_tabs_ids';

	/**
	 * Constructor.
	 */
	public function init() {}

	/**
	 * Get list of product tabs ids.
	 *
	 * @return int[]
	 */
	public function get_all_tabs_ids() {
		$cache = get_transient( $this->transient_product_tabs_ids );

		if ( $cache ) {
			return $cache;
		}

		$tabs_ids = get_posts(
			array(
				'fields'         => 'ids',
				'posts_per_page' => apply_filters( 'woodmart_product_tabs_rule_limit', 100 ),
				'post_type'      => 'wd_product_tabs',
				'post_status'    => 'publish',
				'orderby'        => 'date',
				'order'          => 'ASC',
			)
		);

		set_transient( $this->transient_product_tabs_ids, $tabs_ids );

		return $tabs_ids;
	}

	/**
	 * Get lsit of allowed tabs for this product.
	 *
	 * @param WC_Product $product Product instance.
	 *
	 * @return array
	 */
	public function get_allowed_tabs( $product ) {
		$tabs_ids     = $this->get_all_tabs_ids();
		$allowed_tabs = array();

		foreach ( $tabs_ids as $id ) {
			$id = apply_filters( 'wpml_object_id', $id, 'wd_product_tabs', true, apply_filters( 'wpml_current_language', null ) );

			$conditions = maybe_unserialize( get_post_meta( $id, 'product_tab_condition', true ) );

			if ( ! is_array( $conditions ) ) {
				continue;
			}

			if ( $this->check_condition( $conditions, $product ) ) {
				$title = maybe_unserialize( get_post_meta( $id, 'product_tab_title', true ) );
				$title = ! empty( $title ) ? $title : get_the_title( $id );

				$tab_key  = get_post_field( 'post_name', $id );
				$priority = maybe_unserialize( get_post_meta( $id, 'product_tab_priority', true ) );
				$priority = ! empty( $priority ) && is_numeric( $priority ) ? $priority : 130;

				$allowed_tabs[ $tab_key ] = array(
					'title'    => ! empty( $title ) ? $title : esc_html__( 'Tab title', 'woodmart' ),
					'priority' => $priority,
					'content'  => woodmart_get_post_content( $id ),
					'callback' => array( $this, 'get_product_tab_content' ),
				);
			}
		}

		return $allowed_tabs;
	}

	/**
	 * Render custom product tab content.
	 *
	 * @param string $key Tab name (slug).
	 * @param array  $product_tab Tab args.
	 *
	 * @return void
	 */
	public function get_product_tab_content( $key, $product_tab ) {
		if ( ! empty( $product_tab['content'] ) ) {
			echo $product_tab['content']; // phpcs:ignore.
		}
	}

	/**
	 * Check condition.
	 *
	 * @param array      $conditions List of conditions arguments.
	 * @param WC_Product $product The product object for which you need to check rules.
	 *
	 * @return bool
	 */
	public function check_condition( $conditions, $product ) {
		$is_active  = false;
		$is_exclude = false;

		if ( 'variation' === $product->get_type() ) {
			$product = wc_get_product( $product->get_parent_id() );
		}

		foreach ( $conditions as $id => $condition ) {
			$conditions[ $id ]['product_tab_priority'] = $this->get_condition_priority( $condition['type'] );
		}

		uasort( $conditions, array( $this, 'sort_by_priority' ) );

		foreach ( $conditions as $condition ) {
			if ( isset( $condition['query'] ) ) {
				$condition['query'] = apply_filters( 'wpml_object_id', $condition['query'], $condition['type'], true, apply_filters( 'wpml_current_language', null ) );
			}

			switch ( $condition['type'] ) {
				case 'all':
					$is_active = 'include' === $condition['comparison'];

					if ( 'exclude' === $condition['comparison'] ) {
						$is_exclude = true;
					}
					break;
				case 'product':
					$is_needed_product = (int) $product->get_id() === (int) $condition['query'];

					if ( $is_needed_product ) {
						if ( 'exclude' === $condition['comparison'] ) {
							$is_active  = false;
							$is_exclude = true;
						} else {
							$is_active = true;
						}
					}

					break;
				case 'product_type':
					$is_needed_type = $product->get_type() === $condition['product-type-query'];

					if ( $is_needed_type ) {
						if ( 'exclude' === $condition['comparison'] ) {
							$is_active  = false;
							$is_exclude = true;
						} else {
							$is_active = true;
						}
					}
					break;
				case 'product_cat':
				case 'product_tag':
				case 'product_brand':
				case 'product_attr_term':
				case 'product_shipping_class':
					$terms = wp_get_post_terms( $product->get_id(), get_taxonomies(), array( 'fields' => 'ids' ) );

					if ( $terms ) {
						$is_needed_term = in_array( (int) $condition['query'], $terms, true );

						if ( $is_needed_term ) {
							if ( 'exclude' === $condition['comparison'] ) {
								$is_active  = false;
								$is_exclude = true;
							} else {
								$is_active = true;
							}
						}
					}
					break;
				case 'product_cat_children':
					$terms         = wp_get_post_terms( $product->get_id(), get_taxonomies(), array( 'fields' => 'ids' ) );
					$term_children = get_term_children( $condition['query'], 'product_cat' );

					if ( $terms ) {
						$is_needed_cat_children = count( array_diff( $terms, $term_children ) ) !== count( $terms );

						if ( $is_needed_cat_children ) {
							if ( 'exclude' === $condition['comparison'] ) {
								$is_active  = false;
								$is_exclude = true;
							} else {
								$is_active = true;
							}
						}
					}
					break;
			}

			if ( $is_exclude || $is_active ) {
				break;
			}
		}

		return $is_active;
	}

	/**
	 * Get condition priority;
	 *
	 * @param string $type Condition type.
	 *
	 * @return int
	 */
	public function get_condition_priority( $type ) {
		$priority = 50;

		switch ( $type ) {
			case 'all':
				$priority = 10;
				break;
			case 'product_cat_children':
				$priority = 20;
				break;
			case 'product_type':
			case 'product_cat':
			case 'product_tag':
			case 'product_brand':
			case 'product_attr_term':
			case 'product_shipping_class':
				$priority = 30;
				break;
			case 'product':
				$priority = 40;
				break;
		}

		return apply_filters( 'woodmart_condition_priority', $priority, $type );
	}

	/**
	 * Sort the conditions rule by priority DESC.
	 *
	 * @param array $a The first array to compare.
	 * @param array $b The first array to compare.
	 *
	 * @return int
	 */
	public function sort_by_priority( $a, $b ) {
		return $b['product_tab_priority'] <=> $a['product_tab_priority'];
	}
}

Manager::get_instance();
