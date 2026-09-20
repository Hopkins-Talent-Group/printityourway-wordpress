<?php
/**
 * Post date meta shortcode.
 *
 * @package Woodmart
 */

use XTS\Modules\Layouts\Main;

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'woodmart_shortcode_single_post_date_meta' ) ) {
	/**
	 * Post date meta shortcode function.
	 *
	 * @param array $settings Shortcode settings.
	 * @return array
	 */
	function woodmart_shortcode_single_post_date_meta( $settings ) {
		$default_settings = array(
			'css'       => '',
			'alignment' => 'left',
		);

		$settings         = wp_parse_args( $settings, $default_settings );
		$wrapper_classes  = apply_filters( 'vc_shortcodes_css_class', '', '', $settings );
		$wrapper_classes .= ' text-' . woodmart_vc_get_control_data( $settings['alignment'], 'desktop' );

		if ( $settings['css'] ) {
			$wrapper_classes .= ' ' . vc_shortcode_custom_css_class( $settings['css'] );
		}

		ob_start();
		woodmart_enqueue_inline_style( 'post-types-mod-predefined' );

		Main::setup_preview();
		?>
		<div class="wd-wpb wd-single-post-date<?php echo esc_attr( $wrapper_classes ); ?>">
			<span class="wd-modified-date">
				<?php woodmart_post_modified_date(); ?>
			</span>

			<span class="wd-post-date wd-style-default">
				<time class="published" datetime="<?php echo get_the_date( 'c' ); // phpcs:ignore ?>">
					<?php echo esc_html( _x( 'On', 'meta-date', 'woodmart' ) ) . ' ' . get_the_date(); ?>
				</time>
			</span>
		</div>	
		<?php
		Main::restore_preview();
		return ob_get_clean();
	}
}
