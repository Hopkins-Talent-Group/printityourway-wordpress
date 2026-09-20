<?php
/**
 * The Main class.
 *
 * @package woodmart
 */

namespace XTS\Modules\Search;

use XTS\Singleton;

/**
 * The Main class.
 */
class Main extends Singleton {
	/**
	 * Init.
	 */
	public function init() {
		$this->include_files();

		add_action( 'woocommerce_after_shop_loop', array( $this, 'show_blog_results_on_search_page' ), 100 );
		add_action( 'woodmart_after_portfolio_loop', array( $this, 'show_blog_results_on_search_page' ), 100 );
		add_action( 'woodmart_after_no_product_found', array( $this, 'show_blog_results_on_search_page' ), 100 );
	}

	/**
	 * Include files.
	 */
	public function include_files() {
		$files = array(
			'frontend/class-search-form',
			'frontend/class-dropdown-search',
			'frontend/class-full-screen-search',

			'query/class-search-query',
			'query/class-search-with-sku',
			'query/class-search-with-synonyms',
			'query/class-search-with-taxonomies',

			'class-ajax-search',
			'functions',
		);

		foreach ( $files as $file ) {
			require_once get_parent_theme_file_path( WOODMART_FRAMEWORK . '/modules/search/' . $file . '.php' );
		}
	}

	/**
	 * Show blog results on search page.
	 *
	 * @return void
	 */
	public function show_blog_results_on_search_page() {
		if ( ! is_search() || ! woodmart_get_opt( 'enqueue_posts_results' ) ) {
			return;
		}

		$search_query = get_search_query();
		$column       = woodmart_get_opt( 'search_posts_results_column' );
		$blog_results = woodmart_shortcode_blog(
			array(
				'slides_per_view' => $column,
				'blog_design'     => 'carousel',
				'search'          => $search_query,
				'items_per_page'  => 10,
			)
		);

		if ( empty( $blog_results ) ) {
			return;
		}

		$show_all_url = add_query_arg(
			array(
				's'         => esc_attr( $search_query ),
				'post_type' => 'post',
			),
			home_url()
		);

		ob_start();
		?>
		<div class="wd-blog-search-results">
			<h4 class="wd-el-title slider-title">
				<span><?php esc_html_e( 'Results from blog', 'woodmart' ); ?></span>
			</h4>

			<?php echo $blog_results; // phpcs:ignore. ?>

			<div class="wd-search-show-all">
				<a href="<?php echo esc_url( $show_all_url ); ?>" class="button">
					<?php esc_html_e( 'Show all blog results', 'woodmart' ); ?>
				</a>
			</div>
		</div>
		<?php

		echo ob_get_clean(); // phpcs:ignore.
	}
}

Main::get_instance();
