<?php
if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

use XTS\Modules\Product_Category_Filter_Walker;

if ( ! class_exists( 'XTS\Modules\Product_Category_Filter_Walker' ) ) {
	require_once get_parent_theme_file_path( WOODMART_FRAMEWORK . '/modules/product-category-filter-walker/class-product-category-filter-walker.php' );
}

/**
 * Filter by categories.
 */

if ( ! class_exists( 'WOODMART_Product_Category_Filter' ) ) {
	/**
	 * Product Category Filter Widget.
	 */
	class WOODMART_Product_Category_Filter extends WPH_Widget {
		/**
		 * Define the widget options and create it.
		 */
		public function __construct() {
			if ( ! woodmart_woocommerce_installed() ) {
				return;
			}

			$this->create_widget(
				array(
					'label'       => esc_html__( 'WOODMART Product Category Filter', 'woodmart' ),
					'description' => esc_html__( 'This widget allows users to filter products by categories, making it easier to navigate and find specific items within the store.', 'woodmart' ),
					'slug'        => 'wd-product-category-filter',
					'fields'      => array(
						array(
							'id'   => 'title',
							'type' => 'text',
							'std'  => esc_html__( 'Filter by category', 'woodmart' ),
							'name' => esc_html__( 'Title', 'woodmart' ),
						),
						array(
							'id'     => 'orderby',
							'type'   => 'dropdown',
							'std'    => 'name',
							'name'   => esc_html__( 'Order by', 'woodmart' ),
							'fields' => array(
								esc_html__( 'Category order', 'woodmart' ) => 'order',
								esc_html__( 'Name', 'woodmart' ) => 'name',
							),
						),
						array(
							'id'     => 'display',
							'type'   => 'dropdown',
							'std'    => 'list',
							'name'   => esc_html__( 'Layout', 'woodmart' ),
							'fields' => array(
								esc_html__( 'List', 'woodmart' ) => 'list',
								esc_html__( 'Dropdown', 'woodmart' ) => 'dropdown',
							),
						),
						array(
							'id'     => 'checkboxes',
							'type'   => 'dropdown',
							'std'    => 'off',
							'name'   => esc_html__( 'Show checkboxes', 'woodmart' ),
							'fields' => array(
								esc_html__( 'OFF', 'woodmart' ) => 'off',
								esc_html__( 'ON', 'woodmart' )  => 'on',
							),
						),
						array(
							'id'     => 'structure',
							'type'   => 'dropdown',
							'std'    => 'hierarchical',
							'name'   => esc_html__( 'Filter structure', 'woodmart' ),
							'fields' => array(
								esc_html__( 'Hierarchical', 'woodmart' ) => 'hierarchical',
								esc_html__( 'Show children only', 'woodmart' ) => 'children_only',
								esc_html__( 'Flat list', 'woodmart' ) => 'flat_list',
							),
						),
						array(
							'id'         => 'show_search_input',
							'type'       => 'checkbox',
							'std'        => 0,
							'name'       => esc_html__( 'Show search input', 'woodmart' ),
							'dependency' => array(
								'element'            => 'display',
								'value_not_equal_to' => array( 'dropdown' ),
							),
						),
						array(
							'id'   => 'count',
							'type' => 'checkbox',
							'std'  => 0,
							'name' => esc_html__( 'Show product counts', 'woodmart' ),
						),
						array(
							'id'   => 'hide_empty',
							'type' => 'checkbox',
							'std'  => 0,
							'name' => esc_html__( 'Hide empty categories', 'woodmart' ),
						),
						array(
							'id'   => 'max_depth',
							'type' => 'text',
							'std'  => '',
							'name' => esc_html__( 'Maximum depth', 'woodmart' ),
						),
					),
				)
			);

			add_filter( 'woocommerce_product_query_tax_query', array( $this, 'filter_products' ) );
		}

		/**
		 * Render widget on frontend.
		 *
		 * @param array $args Display arguments including 'before_title', 'after_title', 'before_widget', and 'after_widget'.
		 * @param array $settings The settings for the particular instance of the widget.
		 *
		 * @return void
		 */
		public function widget( $args, $settings ) {
			$settings = wp_parse_args(
				$settings,
				array(
					'title'             => esc_html__( 'Filter by category', 'woodmart' ),
					'orderby'           => 'name',
					'display'           => 'list',
					'checkboxes'        => 'off',
					'structure'         => 'hierarchical',
					'show_search_input' => 0,
					'count'             => 0,
					'hide_empty'        => 0,
					'max_depth'         => '',
				)
			);

			$title           = apply_filters( 'widget_title', $settings['title'], $settings );
			$categories      = $this->get_layered_get_categories();
			$wrapper_classes = '';

			if ( 'layered-nav' === woodmart_get_opt( 'shop_widgets_collapse' ) ) {
				$wrapper_classes .= ' wd-widget-collapse';
			}

			$chosen_categories = false;

			if ( ! empty( $_GET['filter_category'] ) ) { // phpcs:ignore.
				$chosen_categories = array_map( 'sanitize_text_field', explode( ',', wc_clean( wp_unslash( $_GET['filter_category'] ) ) ) ); // phpcs:ignore.
			}

			if ( 'disable' !== woodmart_get_opt( 'shop_widgets_collapse', 'disable' ) && is_array( $chosen_categories ) ) {
				$wrapper_classes .= ' wd-opened-initially wd-opened';
			}

			if ( $wrapper_classes ) {
				$args['before_widget'] = str_replace( 'class="', 'class="' . $wrapper_classes . ' ', $args['before_widget'] );
			}

			$view_args = $this->get_view_args( $settings );

			if ( empty( $view_args ) ) {
				return;
			}

			woodmart_enqueue_inline_style( 'widget-product-cat' );

			if ( 'on' === $settings['checkboxes'] ) {
				woodmart_enqueue_inline_style( 'woo-mod-widget-checkboxes' );
			}

			echo wp_kses_post( $args['before_widget'] );

			if ( ! empty( $title ) ) {
				echo wp_kses_post( $args['before_title'] . $title . $args['after_title'] );
			}

			if ( ! empty( $categories ) ) {
				switch ( $settings['display'] ) {
					case 'list':
						$this->render_list_view( $args, $settings, $view_args );
						break;
					case 'dropdown':
						$this->render_dropdown_view( $args, $settings, $view_args );
						break;
				}
			}

			echo wp_kses_post( $args['after_widget'] );
		}

		/**
		 * Render a list style widget on the frontend.
		 *
		 * @param array $args Display arguments including 'before_title', 'after_title', 'before_widget', and 'after_widget'.
		 * @param array $settings The settings for the particular instance of the widget.
		 * @param array $view_args Common arguments for rendering a widget in two styles: list and dropdown.
		 *
		 * @return void
		 */
		public function render_list_view( $args, $settings, $view_args ) {
			$scroll_enabled = woodmart_get_opt( 'widgets_scroll' );
			$list_classes   = array( 'product-categories' );

			if ( 'on' === $settings['checkboxes'] ) {
				$list_classes[] = 'wd-checkboxes-on';
			}

			if ( $scroll_enabled ) {
				$list_classes[] = 'wd-scroll-content';
			}

			if ( $settings['show_search_input'] ) {
				woodmart_enqueue_inline_style( 'filter-search' );
				woodmart_enqueue_js_script( 'search-by-filters' );
			}

			$list_args = array_merge(
				$view_args,
				array(
					'title_li'         => '',
					'pad_counts'       => 1,
					'show_option_none' => esc_html__( 'No product categories exist.', 'woodmart' ),
					'view_type'        => 'list',
				)
			);

			if ( woodmart_get_opt( 'categories_toggle' ) ) {
				woodmart_enqueue_js_script( 'categories-accordion' );
			}
			?>

			<?php if ( $settings['show_search_input'] ) : ?>
				<div class="wd-filter-wrapper">
					<div class="wd-filter-search wd-search">
						<input type="text" placeholder="<?php esc_attr_e( 'Find a Category', 'woodmart' ); ?>" aria-label="<?php esc_attr_e( 'Find a Category', 'woodmart' ); ?>">
						<span class="wd-filter-search-clear wd-action-btn wd-style-icon wd-cross-icon">
							<a href="#" aria-label="<?php esc_attr__( 'Clear search', 'woodmart' ); ?>"></a>
						</span>
					</div>
			<?php endif; ?>

			<?php if ( $scroll_enabled ) : ?>
				<div class="wd-scroll">
			<?php endif; ?>

				<ul class="<?php echo esc_attr( implode( ' ', $list_classes ) ); ?>">
					<?php echo wp_list_categories( $list_args ); ?>
				</ul>

			<?php if ( $scroll_enabled ) : ?>
				</div>
			<?php endif; ?>

			<?php if ( $settings['show_search_input'] ) : ?>
				</div>
			<?php endif; ?>
			<?php
		}

		/**
		 * Render a dropdown style widget on the frontend.
		 *
		 * @param array $args Display arguments including 'before_title', 'after_title', 'before_widget', and 'after_widget'.
		 * @param array $settings The settings for the particular instance of the widget.
		 * @param array $view_args Common arguments for rendering a widget in two styles: list and dropdown.
		 *
		 * @return void
		 */
		public function render_dropdown_view( $args, $settings, $view_args ) {
			global $wp;

			wp_enqueue_style( 'select2' );
			woodmart_enqueue_inline_style( 'select2' );
			woodmart_enqueue_inline_style( 'woo-mod-widget-dropdown-form' );

			wp_enqueue_script( 'selectWoo' );
			woodmart_enqueue_js_script( 'filter-dropdowns' );

			$list_args         = array_merge(
				$view_args,
				array(
					'name'      => '',
					'style'     => '',
					'view_type' => 'dropdown',
				)
			);
			$categories        = get_terms(
				array_merge(
					$list_args,
					array(
						'taxonomy'   => $list_args['taxonomy'],
						'pad_counts' => true,
					)
				)
			);
			$chosen_categories = $this->get_chosen_categories();

			if ( '' === get_option( 'permalink_structure' ) ) {
				$form_action = remove_query_arg(
					array( 'page', 'paged' ),
					add_query_arg(
						$wp->query_string,
						'',
						home_url( $wp->request )
					)
				);
			} else {
				$form_action = preg_replace( '%\/page/[0-9]+%', '', home_url( trailingslashit( $wp->request ) ) );
			}
			?>
			<form method="get" action="<?php echo esc_url( $form_action ); ?>" class="wd-product-category-filter-form wd-filter-form">
				<select class="wd-dropdown-product-cat" multiple="multiple" data-placeholder="<?php esc_attr_e( 'Any Categories', 'woodmart' ); ?>" data-noResults="<?php esc_html_e( 'No matches found', 'woodmart' ); ?>" data-slug='category'>
					<option value=""><?php esc_html_e( 'Any Categories', 'woodmart' ); ?></option>
					<?php echo walk_category_dropdown_tree( $categories, $list_args['depth'], $list_args ); // phpcs:ignore. ?>
				</select>

				<button class="wd-product-category-filter-submit" type="submit" value="<?php esc_attr_e( 'Apply', 'woodmart' ); ?>">
					<?php esc_html_e( 'Apply', 'woodmart' ); ?>
				</button>

				<input type="hidden" name="filter_category" value="<?php echo esc_attr( implode( ',', $chosen_categories ) ); ?>">
				<?php echo wc_query_string_form_fields( null, array( 'filter_category' ), '', true ); // phpcs:ignore. ?>
			</form>
			<?php
		}

		/**
		 * Add chosen categories to WooCommerce tax query.
		 *
		 * @param array $tax_query WC tax query.
		 */
		public function filter_products( $tax_query ) {
			if ( ! isset( $_GET['filter_category'] ) || empty( $_GET['filter_category'] ) ) {
				return $tax_query;
			}

			$categories  = array_map( 'sanitize_text_field', explode( ',', $_GET['filter_category'] ) );
			$tax_query[] = array(
				'taxonomy' => 'product_cat',
				'field'    => 'slug',
				'terms'    => $categories,
				'operator' => 'IN',
			);

			return $tax_query;
		}

		/**
		 * Get list with args for render dropdown or list view template.
		 *
		 * @param array $settings List of widget instance settings.
		 *
		 * @return array
		 */
		public function get_view_args( $settings ) {
			$chosen_categories_ids = array();
			$chosen_categories     = $this->get_chosen_categories();

			if ( ! empty( $chosen_categories ) ) {
				$chosen_categories_ids = $this->convert_categories_slugs_to_ids( $chosen_categories );
			}

			$walker = new Product_Category_Filter_Walker( $chosen_categories_ids );

			$list_args = array(
				'taxonomy'     => 'product_cat',
				'show_count'   => $settings['count'],
				'hide_empty'   => $settings['hide_empty'],
				'hierarchical' => 'hierarchical' === $settings['structure'],
				'depth'        => $settings['max_depth'],
				'max_depth'    => $settings['max_depth'],
				'menu_order'   => false,
				'walker'       => $walker,
			);

			if ( 'children_only' === $settings['structure'] && ( ! empty( $chosen_categories_ids ) || is_product_category() ) ) {
				$include = array();

				if ( is_product_category() ) {
					$product_cat_slug   = get_query_var( 'product_cat' );
					$term               = get_term_by( 'slug', $product_cat_slug, 'product_cat' );
					$chosen_category_id = $term->term_id;

					$include = get_terms(
						array(
							'taxonomy'     => 'product_cat',
							'fields'       => 'ids',
							'parent'       => $chosen_category_id,
							'hierarchical' => true,
							'hide_empty'   => $settings['hide_empty'],
						)
					);
				} else {
					$include = get_terms(
						array(
							'taxonomy'     => 'product_cat',
							'fields'       => 'ids',
							'parent'       => 0,
							'hierarchical' => true,
							'hide_empty'   => $settings['hide_empty'],
						)
					);
				}

				if ( empty( $include ) ) {
					return array();
				}

				$list_args['include'] = implode( ',', $include );
			} elseif ( 'children_only' === $settings['structure'] ) {
				$list_args['depth']        = 1;
				$list_args['child_of']     = 0;
				$list_args['hierarchical'] = 1;
			}

			if ( 'order' === $settings['orderby'] ) {
				$list_args['orderby']  = 'meta_value_num';
				$list_args['meta_key'] = 'order'; // phpcs:ignore.
			}

			return $list_args;
		}

		/**
		 * Get chosen categories list.
		 *
		 * @return array
		 */
		public function get_chosen_categories() {
			$base_link         = woodmart_filters_get_page_base_url();
			$parsed_url        = wp_parse_url( $base_link );
			$chosen_categories = array();

			if ( ! empty( $parsed_url['query'] ) ) {
				$query_args = array();

				wp_parse_str( $parsed_url['query'], $query_args );

				if ( isset( $query_args['filter_category'] ) ) {
					$filter_category   = explode( ',', $query_args['filter_category'] );
					$chosen_categories = array_merge( $chosen_categories, $filter_category );
				}
			}

			$chosen_categories = array_unique( $chosen_categories );

			return $chosen_categories;
		}

		/**
		 * Convert categories slugs to ids.
		 *
		 * @param array $slugs List of categories slugs.
		 *
		 * @return array
		 */
		public function convert_categories_slugs_to_ids( $slugs ) {
			$ids = array();

			foreach ( $slugs as $slug ) {
				$term = get_term_by( 'slug', $slug, 'product_cat' );

				if ( $term ) {
					$ids[] = $term->term_id;
				}
			}

			return $ids;
		}
	}
}
