<?php
/**
 * Post categories shortcode.
 *
 * @package Woodmart
 */

use XTS\Modules\Layouts\Main;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'woodmart_shortcode_single_post_categories' ) ) {
	/**
	 * Post categories shortcode.
	 *
	 * @param array $settings Shortcode settings.
	 * @return string HTML output.
	 */
	function woodmart_shortcode_single_post_categories( $settings ) {
		$default_settings = array(
			'css'              => '',
			'categories_style' => 'default',
			'wrapper_classes'  => '',
			'alignment'        => 'left',
			'cats_bg'          => '',
			'el_id'            => '',
			'is_wpb'           => true,
		);

		$settings     = wp_parse_args( $settings, $default_settings );
		$is_portfolio = Main::get_instance()->has_custom_layout( 'single_portfolio' );

		Main::setup_preview();

		$el_id          = $settings['el_id'];
		$cats           = get_the_category_list( ', ' );
		$portfolio_cats = wp_get_post_terms( get_the_ID(), 'project-cat' );

		$wrapper_classes = $settings['wrapper_classes'];
		$classes         = ' wd-style-' . $settings['categories_style'];

		if ( $settings['is_wpb'] && 'wpb' === woodmart_get_current_page_builder() ) {
			$wrapper_classes .= ' wd-wpb';
			$wrapper_classes .= apply_filters( 'vc_shortcodes_css_class', '', '', $settings );
			$wrapper_classes .= ' text-' . woodmart_vc_get_control_data( $settings['alignment'], 'desktop' );

			if ( $settings['css'] ) {
				$wrapper_classes .= ' ' . vc_shortcode_custom_css_class( $settings['css'] );
			}
		}

		if ( $is_portfolio && ! empty( $portfolio_cats ) && ! is_wp_error( $portfolio_cats ) ) {
			if ( ! empty( $portfolio_cats ) && ! is_wp_error( $portfolio_cats ) ) {
				$cat_links = array();

				foreach ( $portfolio_cats as $cat ) {
					$cat_link = get_term_link( $cat );
					if ( ! is_wp_error( $cat_link ) ) {
						$cat_links[] = '<a href="' . esc_url( $cat_link ) . '">' . esc_html( $cat->name ) . '</a>';
					}
				}

				$cats = implode( ', ', $cat_links );
			}
		}

		ob_start();

		if ( $cats || $portfolio_cats ) {
			woodmart_enqueue_inline_style( 'post-types-mod-predefined' );
			if ( 'with-bg' === $settings['categories_style'] ) {
				woodmart_enqueue_inline_style( 'post-types-mod-categories-style-bg' );
			}
			?>
			<div 
			<?php if ( $el_id ) : ?>
			id="<?php echo esc_attr( $el_id ); ?>"
			<?php endif; ?>
			class="wd-single-post-cat<?php echo esc_attr( $wrapper_classes ); ?>">
				<div class="wd-post-cat<?php echo esc_attr( $classes ); ?>"><?php echo wp_kses_post( $cats ); ?></div>
			</div>
			<?php
		}

		Main::restore_preview();

		return ob_get_clean();
	}
}
