<?php if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );}

use XTS\Modules\Layouts\Main;

/**
* ------------------------------------------------------------------------------------------------
* Breadcrumbs shortcode
* ------------------------------------------------------------------------------------------------
*/

if ( ! function_exists( 'woodmart_shortcode_page_heading' ) ) {
	function woodmart_shortcode_page_heading( $settings ) {
		global $post;

		if ( function_exists( 'dokan_is_store_page' ) && dokan_is_store_page() ) {
			return '';
		}

		$settings = wp_parse_args(
			$settings,
			array(
				'tag'             => 'h2',
				'css'             => '',
				'alignment'       => 'left',
				'el_id'           => '',
				'wrapper_classes' => '',
				'is_wpb'          => true,
			)
		);

		$wrapper_classes = '';
		$classes         = '';

		$wrapper_classes .= ' reset-last-child';

		if ( ! empty( $settings['wrapper_classes'] ) ) {
			$wrapper_classes .= $settings['wrapper_classes'];
		}

		if ( $settings['is_wpb'] && 'wpb' === woodmart_get_current_page_builder() ) {
			$wrapper_classes .= ' wd-wpb';
			$wrapper_classes .= ' text-' . woodmart_vc_get_control_data( $settings['alignment'], 'desktop' );
			$wrapper_classes .= apply_filters( 'vc_shortcodes_css_class', '', '', $settings );

			if ( $settings['css'] ) {
				$wrapper_classes .= ' ' . vc_shortcode_custom_css_class( $settings['css'] );
			}
		}

		$title_tag          = $settings['tag'];
		$page_for_posts     = get_option( 'page_for_posts' );
		$single_post_design = woodmart_get_opt( 'single_post_design' );

		Main::setup_preview();

		if ( woodmart_is_blog_archive() || ( 'large_image' !== $single_post_design && ( ( $post && 'post' === $post->post_type ) || Main::is_layout_type( 'single_post' ) ) ) || Main::is_layout_type( 'blog_archive' ) ) {
			$title = ( ! empty( $page_for_posts ) ) ? get_the_title( $page_for_posts ) : esc_html__( 'Blog', 'woodmart' );

			if ( is_tag() ) {
				$title = esc_html__( 'Tag Archives: ', 'woodmart' ) . single_tag_title( '', false );
			}

			if ( is_category() ) {
				$title = single_cat_title( '', false );
			}

			if ( is_date() ) {
				if ( is_day() ) {
					$title = esc_html__( 'Daily Archives: ', 'woodmart' ) . get_the_date();
				} elseif ( is_month() ) {
					$title = esc_html__( 'Monthly Archives: ', 'woodmart' ) . get_the_date( _x( 'F Y', 'monthly archives date format', 'woodmart' ) );
				} elseif ( is_year() ) {
					$title = esc_html__( 'Yearly Archives: ', 'woodmart' ) . get_the_date( _x( 'Y', 'yearly archives date format', 'woodmart' ) );
				} else {
					$title = esc_html__( 'Archives', 'woodmart' );
				}
			}

			if ( is_author() ) {
				the_post();
				$title    = esc_html__( 'Posts by ', 'woodmart' ) . '<a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '" title="' . esc_attr( get_the_author() ) . '" rel="me">' . get_the_author() . '</a>';
				$classes .= ' vcard';
				rewind_posts();
			}

			if ( is_search() ) {
				$title = esc_html__( 'Search Results for: ', 'woodmart' ) . get_search_query();
			}
		} elseif ( ( ! woodmart_get_opt( 'single_portfolio_title_in_page_title' ) && ( ( $post && 'portfolio' === $post->post_type ) || Main::is_layout_type( 'single_portfolio' ) ) ) || woodmart_is_portfolio_archive() ) {
			$title = get_the_title( woodmart_get_portfolio_page_id() );

			if ( is_tax( 'project-cat' ) ) {
				$title = single_term_title( '', false );
			}
		} elseif ( woodmart_is_shop_archive() || Main::get_instance()->has_custom_layout( 'shop_archive' ) ) {
			$title = woocommerce_page_title( false );
		} elseif ( Main::is_layout_type( 'cart' ) ) {
			$title = esc_html__( 'Cart', 'woodmart' );
		} elseif ( Main::is_layout_type( 'checkout_form' ) || Main::is_layout_type( 'checkout_content' ) ) {
			$title = esc_html__( 'Checkout', 'woodmart' );
		} else {
			$title = get_the_title();
		}

		ob_start();
		?>
		<div
		<?php if ( ! empty( $settings['el_id'] ) ) : ?>
		id="<?php echo esc_attr( $settings['el_id'] ); ?>"
		<?php endif; ?>
		class="wd-el-page-heading<?php echo esc_attr( $wrapper_classes ); ?>">
			<<?php echo esc_attr( $title_tag ); ?> class="entry-title title<?php echo esc_attr( $classes ); ?>">
			<?php echo wp_kses_post( $title ); ?>
			</<?php echo esc_attr( $title_tag ); ?>>
		</div>
		<?php

		Main::restore_preview();

		return ob_get_clean();
	}
}
