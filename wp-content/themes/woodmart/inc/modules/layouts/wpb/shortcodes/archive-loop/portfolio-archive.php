<?php
/**
 * Blog loop shortcode.
 *
 * @package Woodmart
 */

use XTS\Modules\Layouts\Main;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'woodmart_shortcode_portfolio_archive_loop' ) ) {
	/**
	 * Blog loop shortcode.
	 *
	 * @param array $settings Shortcode attributes.
	 */
	function woodmart_shortcode_portfolio_archive_loop( $settings ) {
		$default_settings = array(
			'css'             => '',
			'wrapper_classes' => '',
			'is_wpb'          => true,
			'el_id'           => '',
		);

		$settings        = wp_parse_args( $settings, $default_settings );
		$wrapper_classes = $settings['wrapper_classes'];

		Main::setup_preview();

		if ( $settings['is_wpb'] && 'wpb' === woodmart_get_current_page_builder() ) {
			if ( isset( $settings['portfolio_columns'] ) ) {
				$encoded_data             = $settings['portfolio_columns'];
				$portfolio_columns        = woodmart_vc_get_control_data( $encoded_data, 'desktop' );
				$portfolio_columns_tablet = woodmart_vc_get_control_data( $encoded_data, 'tablet' );
				$portfolio_columns_mobile = woodmart_vc_get_control_data( $encoded_data, 'mobile' );

				if ( $portfolio_columns ) {
					$settings['portfolio_column'] = $portfolio_columns;
				}

				if ( $portfolio_columns_tablet ) {
					$settings['portfolio_columns_tablet'] = $portfolio_columns_tablet;
				}

				if ( $portfolio_columns_mobile ) {
					$settings['portfolio_columns_mobile'] = $portfolio_columns_mobile;
				}
			}

			$wrapper_classes .= ' wd-wpb';
			$wrapper_classes .= apply_filters( 'vc_shortcodes_css_class', '', '', $settings ); // phpcs:ignore.

			if ( $settings['css'] ) {
				$wrapper_classes .= ' ' . vc_shortcode_custom_css_class( $settings['css'] );
			}
		}

		if ( ! empty( $settings['portfolio_image_size'] ) ) {
			woodmart_set_loop_prop( 'portfolio_image_size', $settings['portfolio_image_size'] );
		}

		if ( isset( $settings['portfolio_column'] ) ) {
			woodmart_set_loop_prop( 'portfolio_column', $settings['portfolio_column'] );
		}

		if ( isset( $settings['portfolio_columns_tablet'] ) ) {
			woodmart_set_loop_prop( 'portfolio_columns_tablet', $settings['portfolio_columns_tablet'] );
		}

		if ( isset( $settings['portfolio_columns_mobile'] ) ) {
			woodmart_set_loop_prop( 'portfolio_columns_mobile', $settings['portfolio_columns_mobile'] );
		}

		if ( isset( $settings['portfolio_style'] ) && 'inherit' !== $settings['portfolio_style'] ) {
			woodmart_set_loop_prop( 'portfolio_style', $settings['portfolio_style'] );
			woodmart_set_loop_prop( 'portfolio_spacing', isset( $settings['portfolio_spacing'] ) ? $settings['portfolio_spacing'] : '20' );
		}

		if ( isset( $settings['portfolio_spacing_tablet'] ) ) {
			woodmart_set_loop_prop( 'portfolio_spacing_tablet', $settings['portfolio_spacing_tablet'] );
		}

		if ( isset( $settings['portfolio_spacing_mobile'] ) ) {
			woodmart_set_loop_prop( 'portfolio_spacing_mobile', $settings['portfolio_spacing_mobile'] );
		}

		$required_controls = array(
			'portfolio_style',
			'portfolio_image_size',
			'portfolio_image_size_custom',
		);

		$filtered_settings = array_intersect_key( $settings, array_flip( $required_controls ) );

		if ( 'fragments' === woodmart_is_woo_ajax() && isset( $_GET['loop'] ) ) { // phpcs:ignore	
			woodmart_set_loop_prop( 'portfolio_loop', (int) sanitize_text_field( $_GET['loop'] ) ); // phpcs:ignore	
		}

		ob_start();

		?>
		<div 
		<?php if ( ! empty( $settings['el_id'] ) ) : ?>
			id="<?php echo esc_attr( $settings['el_id'] ); ?>"
		<?php endif; ?>
		class="wd-portfolio-archive<?php echo esc_attr( $wrapper_classes ); ?>">
			<?php if ( have_posts() ) : ?>
			<div class="wd-portfolio-element">
				<?php woodmart_get_portfolio_main_loop( false, $filtered_settings ); ?>
			</div>
			<?php else : ?>
				<?php get_template_part( 'content', 'none' ); ?>
			<?php endif; ?>
		</div>
		<?php

		Main::restore_preview();
		woodmart_reset_loop();
		return ob_get_clean();
	}
}
