<?php
/**
 * Image map.
 *
 * @package Woodmart
 */

namespace XTS\Modules\Layouts;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Elementor widget that inserts an embeddable content into the page, from any given URL.
 */
class Post_Image extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'wd_single_post_image';
	}

	/**
	 * Get widget content.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Post image', 'woodmart' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'wd-icon-post-image';
	}

	/**
	 * Get widget categories.
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( 'wd-posts-elements' );
	}

	/**
	 * Show in panel.
	 *
	 * @return bool Whether to show the widget in the panel or not.
	 */
	public function show_in_panel() {
		return Main::is_layout_type( 'single_post' ) || Main::is_layout_type( 'single_portfolio' );
	}

	/**
	 * Register the widget controls.
	 */
	protected function register_controls() {
		/**
		 * Style tab.
		 */

		/**
		 * General settings.
		 */
		$this->start_controls_section(
			'general_style_section',
			array(
				'label' => esc_html__( 'General', 'woodmart' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'css_classes',
			array(
				'type'         => 'wd_css_class',
				'default'      => 'wd-single-post-thumb',
				'prefix_class' => '',
			)
		);

		$this->add_control(
			'alignment',
			array(
				'label'        => esc_html__( 'Alignment', 'woodmart' ),
				'type'         => 'wd_buttons',
				'options'      => array(
					'left'   => array(
						'title' => esc_html__( 'Left', 'woodmart' ),
						'image' => WOODMART_ASSETS_IMAGES . '/settings/align/left.jpg',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'woodmart' ),
						'image' => WOODMART_ASSETS_IMAGES . '/settings/align/center.jpg',
					),
					'right'  => array(
						'title' => esc_html__( 'Right', 'woodmart' ),
						'image' => WOODMART_ASSETS_IMAGES . '/settings/align/right.jpg',
					),
				),
				'prefix_class' => 'text-',
				'default'      => 'left',
			)
		);

		$this->add_control(
			'post_date',
			array(
				'label'        => esc_html__( 'Post date', 'woodmart' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'On', 'woodmart' ),
				'label_off'    => esc_html__( 'Off', 'woodmart' ),
				'return_value' => 'yes',
				'default'      => 'no',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render the widget output on the frontend.
	 */
	protected function render() {
		Main::setup_preview();

		$settings              = $this->get_settings_for_display();
		$gallery_slider        = apply_filters( 'woodmart_gallery_slider', true );
		$gallery               = array();
		$post_format           = get_post_format();
		$thumb_classes         = '';
		$gallery_inner_classes = '';

		if ( 'gallery' === $post_format && $gallery_slider ) {
			$gallery = get_post_gallery( false, false );

			if ( ! empty( $gallery['src'] ) ) {
				woodmart_enqueue_js_library( 'swiper' );
				woodmart_enqueue_js_script( 'swiper-carousel' );
				woodmart_enqueue_inline_style( 'swiper' );
				woodmart_enqueue_inline_style( 'blog-mod-gallery' );

				$thumb_classes         .= ' wd-carousel-container wd-post-gallery';
				$gallery_inner_classes .= ' color-scheme-light';
			}
		}

		if ( ( has_post_thumbnail() || ! empty( $gallery['src'] ) ) && ! post_password_required() && ! is_attachment() ) : ?>
			<?php woodmart_enqueue_inline_style( 'post-types-mod-predefined' ); ?>
			<div class="wd-post-image<?php echo esc_attr( $thumb_classes ); ?>">
				<?php
				if ( isset( $settings['post_date'] ) && 'yes' === $settings['post_date'] ) {
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
			<?php
		endif;
		Main::restore_preview();
	}
}

Plugin::instance()->widgets_manager->register( new Post_Image() );
