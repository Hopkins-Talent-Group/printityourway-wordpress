<?php
/**
 * Post content shortcode.
 *
 * @package Woodmart
 */

use XTS\Modules\Layouts\Main;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'woodmart_shortcode_single_post_content' ) ) {
	/**
	 * Post content shortcode.
	 *
	 * @param array $settings Shortcode settings.
	 * @return string
	 */
	function woodmart_shortcode_single_post_content( $settings ) {
		$default_settings = array(
			'css' => '',
		);

		$settings        = wp_parse_args( $settings, $default_settings );
		$wrapper_classes = apply_filters( 'vc_shortcodes_css_class', '', '', $settings );
		if ( $settings['css'] ) {
			$wrapper_classes .= ' ' . vc_shortcode_custom_css_class( $settings['css'] );
		}

		ob_start();

		Main::setup_preview();
		$content = get_the_content();

		if ( ! $content || wp_is_serving_rest_request() ) {
			Main::restore_preview();
			return '';
		}

		?>
		<div class="wd-wpb wd-single-post-content<?php echo esc_attr( $wrapper_classes ); ?>">
			<?php
			echo apply_filters( 'the_content', $content ); //phpcs:ignore.

			wp_link_pages(
				array(
					'before'      => '<div class="page-links"><span class="page-links-title">' . esc_html__( 'Pages:', 'woodmart' ) . '</span>',
					'after'       => '</div>',
					'link_before' => '<span>',
					'link_after'  => '</span>',
				)
			);
			?>
		</div>
		<?php

		Main::restore_preview();
		return ob_get_clean();
	}
}
