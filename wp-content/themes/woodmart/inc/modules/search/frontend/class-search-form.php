<?php
/**
 * The search form class.
 *
 * @package woodmart
 */

namespace XTS\Modules\Search\Frontend;

use WPBMap;
use WOODMART_Custom_Walker_Category;

/**
 * The search form class.
 */
class Search_Form {
	/**
	 * Arguments.
	 *
	 * @var array
	 */
	public $args = array();

	/**
	 * Search type.
	 *
	 * @var string
	 */
	public $search_type = '';

	/**
	 * Construct.
	 *
	 * @param array $args Arguments.
	 */
	public function __construct( $args ) {
		$this->args = $this->get_arguments( $args );
	}

	/**
	 * Get search type.
	 *
	 * @return string
	 */
	public function get_search_type() {
		return $this->search_type;
	}

	/**
	 * Get search arguments.
	 *
	 * @param array $args Arguments.
	 *
	 * @return array
	 */
	public function get_arguments( $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'ajax'                            => false,
				'post_type'                       => 'post',
				'show_categories'                 => false,
				'type'                            => 'form',
				'thumbnail'                       => true,
				'price'                           => true,
				'count'                           => 20,
				'icon_type'                       => '',
				'search_style'                    => 'default',
				'custom_icon'                     => '',
				'el_classes'                      => '',
				'wrapper_classes'                 => '',
				'popular_requests'                => false,
				'popular_requests_custom_classes' => '',
				'cat_selector_style'              => 'bordered',
				'el_id'                           => '',
				'include_cat_search'              => false,
				'search_history_enabled'          => false,
				'search_history_custom_classes'   => '',
				'search_extra_content'            => 'disable',
			)
		);

		return apply_filters( 'woodmart_search_form_args', $args );
	}

	/**
	 * Get template.
	 *
	 * @param string $template_name Template name.
	 * @param array  $args          Arguments for template.
	 *
	 * @return void
	 */
	public function get_template( $template_name, $args = array() ) {
		if ( ! empty( $args ) && is_array( $args ) ) {
			extract( $args ); // phpcs:ignore
		}

		include WOODMART_THEMEROOT . '/inc/modules/search/frontend/templates/' . $template_name . '.php';
	}

	/**
	 * Get css classes for main search wrapper.
	 *
	 * @return string
	 */
	public function get_wrapper_classes() {
		$wrapper_classes = '';

		if ( $this->args['wrapper_classes'] ) {
			$wrapper_classes .= ' ' . $this->args['wrapper_classes'];
		}

		if ( 'light' === whb_get_dropdowns_color() && 'form' !== $this->args['type'] ) {
			$wrapper_classes .= ' color-scheme-light';
		}

		$wrapper_classes .= woodmart_get_old_classes( ' woodmart-search-' . $this->args['type'] );

		return $wrapper_classes;
	}

	/**
	 * Get attributes for main search wrapper.
	 *
	 * @return string
	 */
	public function get_wrapper_atts() {
		$wrapper_atts = '';

		if ( $this->args['el_id'] ) {
			$wrapper_atts .= ' id="' . $this->args['el_id'] . '"';
		}

		return $wrapper_atts;
	}

	/**
	 * Get css classes for search form.
	 *
	 * @return string
	 */
	public function get_class() {
		$class = '';

		if ( $this->args['show_categories'] && 'product' === $this->args['post_type'] ) {
			$class .= ' wd-with-cat';
			$class .= woodmart_get_old_classes( ' has-categories-dropdown' );
		}

		if ( $this->args['search_style'] ) {
			$class .= ' wd-style-' . $this->args['search_style'];
			$class .= woodmart_get_old_classes( ' search-style-' . $this->args['search_style'] );
		}

		if ( 'product' === $this->args['post_type'] && $this->args['show_categories'] && $this->args['cat_selector_style'] ) {
			$class .= ' wd-cat-style-' . $this->args['cat_selector_style'];
		}

		if ( $this->args['ajax'] ) {
			$class .= ' woodmart-ajax-search';
		}

		if ( $this->args['el_classes'] ) {
			$class .= ' ' . $this->args['el_classes'];
		}

		return $class;
	}

	/**
	 * Get data attributes for search form.
	 *
	 * @return string
	 */
	public function get_data() {
		$data = '';

		if ( $this->args['ajax'] ) {
			$include_cat_search = $this->args['include_cat_search'] && 'product' === $this->args['post_type'] ? 'yes' : 'no';

			$ajax_args = array(
				'thumbnail'          => $this->args['thumbnail'],
				'price'              => $this->args['price'],
				'post_type'          => $this->args['post_type'],
				'count'              => $this->args['count'],
				'sku'                => woodmart_get_opt( 'show_sku_on_ajax' ) ? '1' : '0',
				'symbols_count'      => apply_filters( 'woodmart_ajax_search_symbols_count', 3 ),
				'include_cat_search' => $include_cat_search,
			);

			foreach ( $ajax_args as $key => $value ) {
				$data .= ' data-' . $key . '="' . $value . '"';
			}
		}

		return $data;
	}

	/**
	 * Get placeholder for search input.
	 *
	 * @return string
	 */
	public function get_placeholder() {
		$placeholder = '';

		switch ( $this->args['post_type'] ) {
			case 'product':
				$placeholder = esc_attr_x( 'Search for products', 'submit button', 'woodmart' );
				break;
			case 'portfolio':
				$placeholder = esc_attr_x( 'Search for projects', 'submit button', 'woodmart' );
				break;
			case 'page':
				$placeholder = esc_attr_x( 'Search for pages', 'submit button', 'woodmart' );
				break;
			default:
				$placeholder = esc_attr_x( 'Search for posts', 'submit button', 'woodmart' );
				break;
		}

		return $placeholder;
	}

	/**
	 * Get css classes for search submit button.
	 *
	 * @return string
	 */
	public function get_btn_classes() {
		$btn_classes = '';

		if ( 'custom' === $this->args['icon_type'] ) {
			$btn_classes .= ' wd-with-img';
			$btn_classes .= woodmart_get_old_classes( ' woodmart-searchform-custom-icon' );
		}

		return $btn_classes;
	}

	/**
	 * Get popular search requests.
	 *
	 * @return array|string
	 */
	public function get_popular_search_requests() {
		$popular_search_requests = '';

		if ( $this->args['popular_requests'] ) {
			$request = woodmart_get_opt( 'popular_requests' );

			if ( $request ) {
				woodmart_enqueue_inline_style( 'popular-requests' );

				$popular_search_requests = explode( "\n", $request );
			}
		}

		return $popular_search_requests;
	}

	/**
	 * Get css classes for search results dropdown.
	 *
	 * @return string
	 */
	public function get_dropdowns_classes() {
		$dropdowns_classes = '';

		if ( 'light' === whb_get_dropdowns_color() ) {
			$dropdowns_classes .= ' color-scheme-light';
		}

		$dropdowns_classes .= woodmart_get_old_classes( ' woodmart-search-results' );

		return $dropdowns_classes;
	}

	/**
	 * Get additional content for the search form, which can be one or combined for desktop and mobile.
	 *
	 * @return string
	 */
	public function get_extra_content() {
		$extra_content       = $this->args['search_extra_content'];
		$extra_content_html  = '';
		$search_area_classes = '';

		if ( 'full-screen' === $this->get_search_type() ) {
			$search_area_classes .= ' wd-scroll-content';
		}

		if ( is_array( $extra_content ) ) {
			foreach ( $extra_content as $device => $extra_content_id ) {
				$responsive_classes       = '';
				$extra_content_inner_html = $this->get_extra_content_html( $extra_content_id );

				if ( empty( $extra_content_inner_html ) ) {
					continue;
				}

				if ( 'desktop' === $device ) {
					$responsive_classes = ' wd-hide-md';
				} elseif ( 'mobile' === $device ) {
					$responsive_classes = ' wd-hide-lg';
				}

				ob_start();

				?>
					<div class="wd-search-area wd-entry-content<?php echo esc_attr( $search_area_classes ); ?><?php echo esc_attr( $responsive_classes ); ?>">
						<?php echo $extra_content_inner_html; // phpcs:ignore. ?>
					</div>
				<?php

				$extra_content_html .= ob_get_clean();
			}
		} else {
			$extra_content_inner_html = $this->get_extra_content_html( $extra_content );

			if ( empty( $extra_content_inner_html ) ) {
				return '';
			}

			ob_start();

			?>
				<div class="wd-search-area wd-entry-content<?php echo esc_attr( $search_area_classes ); ?>">
					<?php echo $extra_content_inner_html; // phpcs:ignore. ?>
				</div>
			<?php

			$extra_content_html = ob_get_clean();
		}

		return $extra_content_html;
	}

	/**
	 * Get the html of one additional content by ID or by following the theme settings.
	 *
	 * @param string|int $extra_content_id Extra content id.
	 *
	 * @return string
	 */
	public function get_extra_content_html( $extra_content_id ) {
		if ( 'disable' === $extra_content_id ) {
			return '';
		}

		ob_start();

		if ( 'inherit' === $extra_content_id ) {
			if ( 'text' === woodmart_get_opt( 'full_search_content_type', 'content' ) && woodmart_get_opt( 'full_search_content_text' ) ) {
				echo do_shortcode( woodmart_get_opt( 'full_search_content_text' ) );
			} elseif ( 'content' === woodmart_get_opt( 'full_search_content_type', 'content' ) && woodmart_get_opt( 'full_search_content_html_block' ) ) {
				echo woodmart_get_html_block( woodmart_get_opt( 'full_search_content_html_block' ) ); //phpcs:ignore
			}
		} elseif ( is_numeric( $extra_content_id ) ) {
			echo woodmart_get_html_block( $extra_content_id ); //phpcs:ignore
		}

		return ob_get_clean();
	}

	/**
	 * Get arguments that will be used in the template for rendering.
	 *
	 * @return array
	 */
	public function get_render_args() {
		$render_args = array(
			'args'                    => $this->args,
			'wrapper_classes'         => $this->get_wrapper_classes(),
			'wrapper_atts'            => $this->get_wrapper_atts(),
			'class'                   => $this->get_class(),
			'data'                    => $this->get_data(),
			'placeholder'             => $this->get_placeholder(),
			'btn_classes'             => $this->get_btn_classes(),
			'popular_search_requests' => $this->get_popular_search_requests(),
			'dropdowns_classes'       => $this->get_dropdowns_classes(),
			'extra_content'           => $this->get_extra_content(),
		);

		return $render_args;
	}

	/**
	 * Check if before search content exists.
	 *
	 * @return bool
	 */
	public function before_search_content_exists() {
		return $this->args['search_history_enabled'] || $this->get_popular_search_requests() || ! empty( $this->get_extra_content() );
	}

	/**
	 * Enqueue scripts and styles.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		woodmart_enqueue_inline_style( 'wd-search-form' );

		if ( $this->before_search_content_exists() || $this->args['ajax'] ) {
			woodmart_enqueue_inline_style( 'wd-search-results' );
		}

		if ( $this->before_search_content_exists() ) {
			woodmart_enqueue_js_script( 'before-search-content' );
		}

		if ( $this->args['ajax'] ) {
			woodmart_enqueue_js_library( 'autocomplete' );
			woodmart_enqueue_js_script( 'ajax-search' );
		}

		if ( $this->get_popular_search_requests() ) {
			woodmart_enqueue_inline_style( 'popular-requests' );
		}

		if ( 'full-screen' !== $this->args['type'] ) {
			woodmart_enqueue_js_script( 'clear-search' );
		}

		if ( $this->args['search_history_enabled'] ) {
			woodmart_enqueue_inline_style( 'search-history' );

			woodmart_enqueue_js_script( 'search-history' );
		}
	}

	/**
	 * Show search requests.
	 *
	 * @param array  $search_requests List of requests.
	 * @param string $post_type Search post type.
	 * @param string $classes Custom css classes.
	 */
	public function show_search_requests( $search_requests, $post_type, $classes = '' ) {
		?>
		<div class="wd-search-requests<?php echo esc_attr( $classes ); ?>">
			<span class="wd-search-title title"><?php echo esc_html__( 'Popular requests', 'woodmart' ); ?></span>
			<ul>
				<?php foreach ( $search_requests as $request ) : ?>
					<li>
						<a href="<?php echo esc_url( get_site_url() . '/?s=' . rawurlencode( trim( $request ) ) . '&post_type=' . $post_type ); ?>">
							<?php echo esc_html( $request ); ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php
	}

	/**
	 * Show categories dropdown.
	 *
	 * @return void
	 */
	public function show_categories_dropdown() {
		$args = apply_filters(
			'woodmart_header_search_categories_dropdown_args',
			array(
				'taxonomy'   => 'product_cat',
				'hide_empty' => true,
				'parent'     => 0,
			)
		);

		$terms = get_terms( $args );

		if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
			$dropdown_classes = '';

			if ( 'light' === whb_get_dropdowns_color() ) {
				$dropdown_classes .= ' color-scheme-light';
			}

			$dropdown_classes .= woodmart_get_old_classes( ' list-wrapper' );

			woodmart_enqueue_inline_style( 'wd-search-cat' );

			woodmart_enqueue_js_script( 'simple-dropdown' );
			woodmart_enqueue_js_script( 'menu-setup' );
			?>
			<div class="wd-search-cat wd-event-click wd-scroll<?php echo esc_attr( woodmart_get_old_classes( ' search-by-category' ) ); ?>">
				<input type="hidden" name="product_cat" value="0">
				<a href="#" rel="nofollow" data-val="0">
					<span>
						<?php esc_html_e( 'Select category', 'woodmart' ); ?>
					</span>
				</a>
				<div class="wd-dropdown wd-dropdown-search-cat wd-dropdown-menu wd-scroll-content wd-design-default<?php echo esc_attr( $dropdown_classes ); ?>">
					<ul class="wd-sub-menu<?php echo esc_attr( woodmart_get_old_classes( ' sub-menu' ) ); ?>">
						<li style="display:none;"><a href="#" data-val="0"><?php esc_html_e( 'Select category', 'woodmart' ); ?></a></li>
						<?php
						if ( ! apply_filters( 'woodmart_show_only_parent_categories_dropdown', false ) ) {
							$args = array(
								'title_li'           => false,
								'taxonomy'           => 'product_cat',
								'use_desc_for_title' => false,
								'walker'             => new WOODMART_Custom_Walker_Category(),
							);

							wp_list_categories( $args );
						} else {
							foreach ( $terms as $term ) {
								?>
									<li><a href="#" data-val="<?php echo esc_attr( $term->slug ); ?>"><?php echo esc_html( $term->name ); ?></a></li>
								<?php
							}
						}
						?>
					</ul>
				</div>
			</div>
			<?php
		}
	}

	/**
	 * Render search form.
	 *
	 * @return void
	 */
	public function render() {
		$render_args = $this->get_render_args();

		ob_start();

		$this->enqueue_scripts();

		$this->get_template( $this->get_search_type(), $render_args );

		echo apply_filters( 'get_search_form', ob_get_clean(), $this->args ); // phpcs:ignore.
	}
}
