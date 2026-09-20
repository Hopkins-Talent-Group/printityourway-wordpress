<?php

namespace XTS\Modules\Layouts;

use WP_Query;

class Single_Post extends Layout_Type {
	/**
	 * Check.
	 *
	 * @param array  $condition Condition.
	 * @param string $type      Layout type.
	 */
	public function check( $condition, $type = '' ) {
		global $post;

		if ( 'post_format' !== $condition['condition_type'] ) {
			$condition['condition_query'] = apply_filters( 'wpml_object_id', $condition['condition_query'], $condition['condition_type'] );
		}

		$is_active = false;
		$type      = str_replace( 'single_', '', $type );

		switch ( $condition['condition_type'] ) {
			case 'all':
				$is_active = is_singular( $type ) || ( wp_is_serving_rest_request() && ! empty( $post ) && $type === $post->post_type );
				break;
			case 'post_id':
			case 'project_id':
				$is_active = (int) get_the_ID() === (int) $condition['condition_query'];
				break;
			case 'post_cat':
			case 'project_cat':
			case 'post_tag':
				$taxonomy = 'category';

				if ( 'post_tag' === $condition['condition_type'] ) {
					$taxonomy = 'post_tag';
				} elseif ( 'project_cat' === $condition['condition_type'] ) {
					$taxonomy = 'project-cat';
				}

				$terms = wp_get_post_terms( get_the_ID(), $taxonomy, array( 'fields' => 'ids' ) );
				if ( $terms ) {
					$is_active = in_array( (int) $condition['condition_query'], $terms, true );
				}
				break;
			case 'post_format':
				$post_format = get_post_format();
				$is_active   = $post_format === $condition['condition_query'];
				break;
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
			( is_singular( 'post' ) && Main::get_instance()->has_custom_layout( 'single_post' ) ) ||
			( is_singular( 'portfolio' ) && Main::get_instance()->has_custom_layout( 'single_portfolio' ) )
		) {
			$this->display_template();
			return false;
		}

		return $template;
	}

	/**
	 * Display custom template on the single page.
	 */
	protected function display_template() {
		parent::display_template();
		$this->before_template_content();
		$single_post      = Main::get_instance()->has_custom_layout( 'single_post' );
		$single_portfolio = Main::get_instance()->has_custom_layout( 'single_portfolio' );
		?>
		<?php while ( have_posts() ) : ?>
			<?php the_post(); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class( ( $single_post || $single_portfolio ) && 'native' === woodmart_get_opt( 'current_builder' ) ? 'entry-content' : '' ); ?>>
				<?php if ( $single_post ) : ?>
					<?php $this->template_content( 'single_post' ); ?>
				<?php elseif ( $single_portfolio ) : ?>
					<?php $this->template_content( 'single_portfolio' ); ?>
				<?php endif; ?>
			</article>
		<?php endwhile; ?>
		<?php
		$this->after_template_content();
	}


	/**
	 * Before template content.
	 */
	public function before_template_content() {
		get_header();
	}

	/**
	 * Before template content.
	 */
	public function after_template_content() {
		get_footer();
	}

	/**
	 * Get preview post id.
	 *
	 * @return int
	 */
	public static function get_preview_post_id() {
		if ( Main::get_instance()->has_custom_layout( 'single_post' ) ) {
			$post_id   = woodmart_get_opt( 'single_post_builder_post_data' );
			$post_type = 'post';

			if ( $post_id ) {
				return $post_id;
			}
		} elseif ( Main::get_instance()->has_custom_layout( 'single_portfolio' ) ) {
			$post_id   = woodmart_get_opt( 'single_project_builder_post_data' );
			$post_type = 'portfolio';

			if ( $post_id ) {
				return $post_id;
			}
		} else {
			return;
		}

		$random_post = new WP_Query(
			array(
				'posts_per_page' => '1',
				'post_type'      => $post_type,
			)
		);

		while ( $random_post->have_posts() ) {
			$random_post->the_post();
			$post_id = get_the_ID();
		}

		wp_reset_postdata();

		return $post_id;
	}

	/**
	 * Setup post data.
	 */
	public static function setup_postdata() {
		global $post;
		$is_layout = Main::is_layout_type( 'single_post' ) || Main::is_layout_type( 'single_portfolio' );

		if (
			$is_layout && (
			( $post && 'woodmart_layout' === $post->post_type ) ||
			is_singular( 'woodmart_layout' ) ||
			wp_doing_ajax() ||
			( isset( $_POST['action'] ) && 'editpost' === $_POST['action'] ) || // phpcs:ignore
			( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ) ||
			( wp_is_post_revision( $post ) )
		) {
			$post_id = self::get_preview_post_id();
			$post = get_post( $post_id ); // phpcs:ignore

			setup_postdata( $post );
		}
	}

	/**
	 * Reset post data.
	 */
	public static function reset_postdata() {
		if ( is_singular( 'woodmart_layout' ) || wp_doing_ajax() ) {
			wp_reset_postdata();
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
			if ( Main::get_instance()->has_custom_layout( 'single_portfolio' ) ) {
				$classes[] = 'single-portfolio';
			} elseif ( Main::get_instance()->has_custom_layout( 'single_post' ) ) {
				$classes[] = 'single-post';
			}
		}

		return $classes;
	}
}

Single_Post::get_instance();
