<?php
/**
 * Toggle shortcode.
 *
 * @package WoodMart
 */

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

if ( ! function_exists( 'woodmart_shortcode_toggle' ) ) {
	function woodmart_shortcode_toggle( $settings, $content = '' ) {
		$settings = wp_parse_args(
			$settings,
			array(
				'css'           => '',
				'element_title' => esc_html__( 'Title', 'woodmart' ),
				'rotate_icon'   => true,
				'state'         => 'closed',
				'state_tablet'  => 'closed',
				'state_mobile'  => 'closed',
			)
		);

		$wrapper_classes  = 'wd-el-toggle';
		$wrapper_classes .= apply_filters( 'vc_shortcodes_css_class', '', '', $settings );
		$wrapper_classes .= ' wd-wpb';

		$wrapper_classes .= ' wd-state-' . $settings['state'] . '-lg';
		$wrapper_classes .= ' wd-state-' . $settings['state_tablet'] . '-md-sm';
		$wrapper_classes .= ' wd-state-' . $settings['state_mobile'] . '-sm';

		$wrapper_classes .= in_array( $settings['state'], array( 'opened', 'static' ), true ) ? ' wd-active-lg' : '';
		$wrapper_classes .= in_array( $settings['state_tablet'], array( 'opened', 'static' ), true ) ? ' wd-active-md-sm' : '';
		$wrapper_classes .= in_array( $settings['state_mobile'], array( 'opened', 'static' ), true ) ? ' wd-active-sm' : '';

		$wrapper_classes .= $settings['rotate_icon'] ? ' wd-icon-rotate' : '';

		if ( function_exists( 'vc_shortcode_custom_css_class' ) ) {
			$wrapper_classes .= ' ' . vc_shortcode_custom_css_class( $settings['css'] );
		}

		ob_start();

		woodmart_enqueue_inline_style( 'el-toggle' );
		woodmart_enqueue_js_script( 'toggle-element' );
		?>
		<div class="<?php echo esc_attr( trim( $wrapper_classes ) ); ?>">
			<div class="wd-el-toggle-head">
				<div class="wd-el-toggle-title title">
					<?php echo wp_kses( $settings['element_title'], true ); ?>
				</div>
				<div class="wd-el-toggle-icon"></div>
			</div>
			<div class="wd-el-toggle-content">
				<div class="wd-el-toggle-content-inner">
					<?php echo do_shortcode( $content ); ?>
				</div>
			</div>
		</div>
		<?php

		return apply_filters( 'woodmart_shortcode_toggle_content', ob_get_clean(), $settings, $content );
	}
}
