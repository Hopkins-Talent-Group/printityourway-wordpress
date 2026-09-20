<?php
/**
 * Active filters shortcode.
 *
 * @package Woodmart
 */

use XTS\Modules\Layouts\Main;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'woodmart_shortcode_shop_archive_active_filters' ) ) {
	/**
	 * Active filters shortcode.
	 *
	 * @param array $settings Shortcode attributes.
	 */
	function woodmart_shortcode_shop_archive_active_filters( $settings ) {
		$default_settings = array(
			'css' => '',
		);

		$settings = wp_parse_args( $settings, $default_settings );

		$wrapper_classes = apply_filters( 'vc_shortcodes_css_class', '', '', $settings );

		if ( $settings['css'] ) {
			$wrapper_classes .= ' ' . vc_shortcode_custom_css_class( $settings['css'] );
		}

		ob_start();
		woodmart_get_active_filters();
		$active_filters_content = ob_get_clean();

		ob_start();

		Main::setup_preview();

		if ( ! empty( $active_filters_content ) ) {
		?>
		<div class="wd-shop-active-filters wd-wpb<?php echo esc_attr( $wrapper_classes ); ?>">
			<?php echo $active_filters_content; // phpcs:ignore. ?>
		</div>
		<?php
		}

		Main::restore_preview();

		return ob_get_clean();
	}
}
