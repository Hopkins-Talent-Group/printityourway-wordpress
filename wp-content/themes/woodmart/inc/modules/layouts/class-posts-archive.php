<?php

namespace XTS\Modules\Layouts;

use WP_Query;

class Posts_Archive extends Layout_Type {
	/**
	 * Switched data.
	 *
	 * @var array Switched data.
	 */
	private static $original_post = array();

	/**
	 * Check.
	 *
	 * @param  array  $condition  Condition.
	 * @param  string $type  Layout type.
	 */
	public function check( $condition, $type = '' ) {
		$is_active = false;

		if ( 'blog_archive' === $type ) {
			switch ( $condition['condition_type'] ) {
				case 'all':
					$is_active = woodmart_is_blog_archive() && 'portfolio' !== get_query_var( 'post_type', 'post' );
					break;
				case 'blog_search_result':
					$is_active = is_search() && 'post' === get_query_var( 'post_type', 'post' ); //phpcs:ignore.
					break;
				case 'blog_category':
					$is_active = is_category( $condition['condition_query'] );
					break;
				case 'blog_tag':
					$is_active = is_tag( $condition['condition_query'] );
					break;
				case 'blog_author':
					$is_active = is_author();
					break;
				case 'blog_date':
					$is_active = is_date();
					break;
			}

			if ( woodmart_is_woo_ajax() ) {
				$is_active = false;
			}
		} elseif ( 'portfolio_archive' === $type ) {
			switch ( $condition['condition_type'] ) {
				case 'all':
					$is_active = woodmart_is_portfolio_archive() || ( is_search() && 'portfolio' === get_query_var( 'post_type', 'portfolio' ) );
					break;
				case 'portfolio_search_result':
					$is_active = is_search() && 'portfolio' === get_query_var( 'post_type', 'portfolio' );
					break;
				case 'portfolio_category':
					$is_active = is_tax( 'project-cat', $condition['condition_query'] );
					break;
			}

			if ( 'fragments' === woodmart_is_woo_ajax() ) {
				$is_active = false;
			}
		}

		return $is_active;
	}

	/**
	 * Override templates.
	 *
	 * @param  string $template  Template.
	 *
	 * @return bool|string
	 */
	public function override_template( $template ) {
		if (
		( woodmart_is_blog_archive() && Main::get_instance()->has_custom_layout( 'blog_archive' ) ) ||
		( woodmart_is_portfolio_archive() && Main::get_instance()->has_custom_layout( 'portfolio_archive' ) )
			) {
			$this->display_template();

			return false;
		}

		return $template;
	}

	/**
	 * Display custom template on the blog page.
	 */
	protected function display_template() {
		parent::display_template();
		$this->before_template_content();
		?>
		<?php if ( Main::get_instance()->has_custom_layout( 'blog_archive' ) ) : ?>
			<?php $this->template_content( 'blog_archive' ); ?>
		<?php elseif ( Main::get_instance()->has_custom_layout( 'portfolio_archive' ) ) : ?>
			<?php $this->template_content( 'portfolio_archive' ); ?>
		<?php endif; ?>
		<?php
		$this->after_template_content();
	}

	/**
	 * Before template content.
	 */
	public function before_template_content() {
		if ( ! woodmart_is_woo_ajax() ) {
			get_header();
		} else {
			woodmart_page_top_part();
		}
		?>
		<div class="wd-content-area site-content entry-content">
		<?php
	}

	/**
	 * Before template content.
	 */
	public function after_template_content() {
		?>
		</div>
		<?php
		if ( ! woodmart_is_woo_ajax() ) {
			get_footer();
		} else {
			woodmart_page_bottom_part();
		}
	}

	/**
	 * Switch to preview query.
	 *
	 * @param  array $new_query  New query variables.
	 */
	public static function switch_to_preview_query( $new_query ) {
		global $wp_query, $post;
		$current_query_vars = $wp_query->query;

		if ( ! is_singular( 'woodmart_layout' ) && ! wp_doing_ajax() && ( ! wp_is_serving_rest_request() || 'woodmart_layout' !== $post->post_type ) ) {
			return;
		}

		if ( $current_query_vars === $new_query ) {
			self::$original_post = false;
			return;
		}

		$preview_query = new WP_Query( $new_query );
		$original_post = array(
			'switched' => $preview_query,
			'original' => $wp_query,
		);

		if ( isset( $post ) ) {
			$original_post['post'] = $post;
		}

		self::$original_post = $original_post;
		$wp_query            = $preview_query; // phpcs:ignore.

		unset( $GLOBALS['post'] );

		if ( $preview_query->have_posts() ) {
			$preview_query->the_post();
		}

		$preview_query->rewind_posts();
	}

	/**
	 * Restore default query for blog archive.
	 *
	 * @return void
	 */
	public static function restore_current_query() {
		$data = self::$original_post;

		if ( ! $data ) {
			return;
		}

		global $wp_query;
		$wp_query = $data['original']; // phpcs:ignore
		unset( $GLOBALS['post'] );

		if ( ! empty( $data['post'] ) ) {
			$GLOBALS['post'] = $data['post']; // phpcs:ignore
			setup_postdata( $GLOBALS['post'] );
		}
	}

	/**
	 * Get body classes.
	 *
	 * @param array $classes Classes for the body element.
	 * @return array
	 */
	public function get_body_classes( $classes ) {
		if ( is_singular( 'woodmart_layout' ) ) {
			if ( Main::get_instance()->has_custom_layout( 'portfolio_archive' ) ) {
				$classes[] = 'woodmart-archive-portfolio';
			} else if ( Main::get_instance()->has_custom_layout( 'blog_archive' ) ) {
				$classes[] = 'woodmart-archive-blog';
			}
		}

		return $classes;
	}
}

Posts_Archive::get_instance();
