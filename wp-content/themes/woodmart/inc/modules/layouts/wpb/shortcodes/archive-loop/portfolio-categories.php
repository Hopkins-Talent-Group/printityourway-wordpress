<?php
/**
 * Portfolio categories shortcode.
 *
 * @package Woodmart
 */

use XTS\Modules\Layouts\Main;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'woodmart_shortcode_portfolio_archive_categories' ) ) {
	/**
	 * Portfolio categories shortcode.
	 *
	 * @param array $settings Shortcode attributes.
	 */
	function woodmart_shortcode_portfolio_archive_categories( $settings ) {
		$default_settings = array(
			'css'       => '',
			'alignment' => 'center',
		);

		$settings         = wp_parse_args( $settings, $default_settings );
		$wrapper_classes  = ' wd-wpb wd-portfolio-archive-nav';
		$wrapper_classes .= apply_filters( 'vc_shortcodes_css_class', '', '', $settings );
		$wrapper_classes .= ' text-' . woodmart_vc_get_control_data( $settings['alignment'], 'desktop' );

		if ( $settings['css'] ) {
			$wrapper_classes .= ' ' . vc_shortcode_custom_css_class( $settings['css'] );
		}

		ob_start();
		Main::setup_preview();

		$filters_type = woodmart_get_opt( 'portfolio_filters_type', 'masonry' );
		$filters      = woodmart_get_opt( 'portoflio_filters' );

		if ( have_posts() && $filters && ( ( ( 'links' === $filters_type && is_tax() ) || ! is_tax() ) ) ) {
			woodmart_portfolio_filters( '', $filters_type, $wrapper_classes );
		}

		Main::restore_preview();
		return ob_get_clean();
	}
}
