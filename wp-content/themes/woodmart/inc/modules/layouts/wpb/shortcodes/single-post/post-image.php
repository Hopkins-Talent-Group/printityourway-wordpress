<?php
/**
 * Post image shortcode.
 *
 * @package Woodmart
 */

use XTS\Modules\Layouts\Main;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'woodmart_shortcode_single_post_image' ) ) {
	/**
	 * Post image shortcode function.
	 *
	 * @param array $settings Shortcode settings.
	 * @return string Shortcode HTML output.
	 */
	function woodmart_shortcode_single_post_image( $settings ) {
		$default_settings = array(
			'css'             => '',
			'alignment'       => 'left',
			'wrapper_classes' => '',
			'el_id'           => '',
			'is_wpb'          => true,
		);

		$settings = wp_parse_args( $settings, $default_settings );
		$el_id    = $settings['el_id'];

		$wrapper_classes = $settings['wrapper_classes'];

		if ( $settings['is_wpb'] && 'wpb' === woodmart_get_current_page_builder() ) {
			$wrapper_classes .= ' wd-wpb';
			$wrapper_classes .= apply_filters( 'vc_shortcodes_css_class', '', '', $settings );
			$wrapper_classes .= ' text-' . woodmart_vc_get_control_data( $settings['alignment'], 'desktop' );

			if ( $settings['css'] ) {
				$wrapper_classes .= ' ' . vc_shortcode_custom_css_class( $settings['css'] );
			}
		}

		ob_start();

		Main::setup_preview();

		$gallery_slider        = apply_filters( 'woodmart_gallery_slider', true );
		$gallery               = array();
		$post_format           = get_post_format();
		$gallery_inner_classes = '';
		$classes               = '';

		if ( 'gallery' === $post_format && $gallery_slider ) {
			$gallery = get_post_gallery( false, false );

			if ( ! empty( $gallery['src'] ) ) {
				woodmart_enqueue_js_library( 'swiper' );
				woodmart_enqueue_js_script( 'swiper-carousel' );
				woodmart_enqueue_inline_style( 'swiper' );
				woodmart_enqueue_inline_style( 'blog-mod-gallery' );

				$wrapper_classes       .= ' wd-carousel-container wd-post-gallery';
				$gallery_inner_classes .= ' color-scheme-light';
				$classes               .= ' wd-carousel-container';
			}
		}

		if ( ( has_post_thumbnail() || ! empty( $gallery['src'] ) ) && ! post_password_required() && ! is_attachment() ) : ?>
			<?php woodmart_enqueue_inline_style( 'post-types-mod-predefined' ); ?>
			<div 
			<?php if ( $el_id ) : ?>
			id="<?php echo esc_attr( $el_id ); ?>"
			<?php endif; ?>
			class="wd-single-post-thumb<?php echo esc_attr( $wrapper_classes ); ?>">
				<div class="wd-post-image<?php echo esc_attr( $classes ); ?>">
					<?php
					if ( ! empty( $settings['post_date'] ) && 'no' !== $settings['post_date'] ) {
						woodmart_post_date();
					}
					?>
					<?php if ( 'gallery' === $post_format && $gallery_slider && ! empty( $gallery['src'] ) ) : ?>
						<?php
						woodmart_enqueue_js_library( 'swiper' );
						woodmart_enqueue_js_script( 'swiper-carousel' );
						woodmart_enqueue_inline_style( 'swiper' );
						?>
						<div class="wd-carousel-inner<?php echo esc_attr( $gallery_inner_classes ); ?>">
							<div class="wd-carousel wd-grid"<?php echo woodmart_get_carousel_attributes( array( 'autoheight' => 'yes' ) ); //phpcs:ignore ?>>
								<div class="wd-carousel-wrap">
									<?php
									foreach ( $gallery['src'] as $src ) {
										if ( preg_match( '/data:image/is', $src ) ) {
											continue;
										}
										?>
										<div class="wd-carousel-item">
										<?php echo apply_filters( 'woodmart_image', '<img src="' . esc_url( $src ) . '" />' ); ?>
										</div>
										<?php
									}
									?>
								</div>
							</div>
							<?php woodmart_get_carousel_nav_template( ' wd-post-arrows wd-pos-sep wd-custom-style' ); ?>
						</div>
					<?php else : ?>
						<?php the_post_thumbnail(); ?>
					<?php endif ?>
					</div>
				</div>
			<?php
		endif;

		Main::restore_preview();

		return ob_get_clean();
	}
}
