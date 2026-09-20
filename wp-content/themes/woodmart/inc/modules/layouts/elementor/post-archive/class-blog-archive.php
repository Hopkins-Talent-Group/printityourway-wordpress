<?php
/**
 * Blog archive.
 *
 * @package Woodmart
 */

namespace XTS\Modules\Layouts;

use Elementor\Controls_Manager;
use Elementor\Plugin;
use Elementor\Widget_Base;
use XTS\Modules\Layouts\Main;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Elementor widget that inserts an embeddable content into the page, from any given URL.
 */
class Blog_Archive extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'wd_blog_archive';
	}

	/**
	 * Get widget content.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Blog archive', 'woodmart' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'wd-icon-blog-archive';
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
		return Main::is_layout_type( 'blog_archive' );
	}

	/**
	 * Register the widget controls.
	 *
	 * @since 1.0.0
	 * @access protected
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
			'extra_width_classes',
			array(
				'type'         => 'wd_css_class',
				'default'      => 'wd-blog-archive wd-width-100',
				'prefix_class' => '',
			)
		);

		$this->add_control(
			'blog_design',
			array(
				'label'       => esc_html__( 'Design', 'woodmart' ),
				'description' => esc_html__( 'Choose one of the designs available in the theme.', 'woodmart' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'inherit',
				'options'     => array(
					'inherit'      => esc_html__( 'Inherit from Theme Settings', 'woodmart' ),
					'default'      => esc_html__( 'Default', 'woodmart' ),
					'default-alt'  => esc_html__( 'Default alternative', 'woodmart' ),
					'small-images' => esc_html__( 'Small images', 'woodmart' ),
					'chess'        => esc_html__( 'Chess', 'woodmart' ),
					'masonry'      => esc_html__( 'Grid', 'woodmart' ),
					'mask'         => esc_html__( 'Mask on image', 'woodmart' ),
					'meta-image'   => esc_html__( 'Meta on image', 'woodmart' ),
					'list'         => esc_html__( 'List', 'woodmart' ),
				),
			)
		);

		$this->add_control(
			'blog_masonry',
			array(
				'label'        => esc_html__( 'Masonry grid', 'woodmart' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '0',
				'label_on'     => esc_html__( 'Yes', 'woodmart' ),
				'label_off'    => esc_html__( 'No', 'woodmart' ),
				'return_value' => '1',
				'condition'    => array(
					'blog_design' => array( 'masonry', 'mask' ),
				),
			)
		);

		$this->add_control(
			'img_size',
			array(
				'label'     => esc_html__( 'Image size', 'woodmart' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'large',
				'options'   => woodmart_get_all_image_sizes_names( 'elementor' ),
				'condition' => array(
					'blog_design!' => array( 'inherit' ),
				),
			)
		);

		$this->add_control(
			'img_size_custom',
			array(
				'label'       => esc_html__( 'Image dimension', 'woodmart' ),
				'type'        => Controls_Manager::IMAGE_DIMENSIONS,
				'description' => esc_html__( 'You can crop the original image size to any custom size. You can also set a single value for height or width in order to keep the original size ratio.', 'woodmart' ),
				'condition'   => array(
					'img_size'     => 'custom',
					'blog_design!' => array( 'inherit' ),
				),
			)
		);

		$this->add_responsive_control(
			'blog_columns',
			array(
				'label'      => esc_html__( 'Columns', 'woodmart' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => array(
					'size' => 3,
				),
				'size_units' => '',
				'range'      => array(
					'px' => array(
						'min'  => 1,
						'max'  => 4,
						'step' => 1,
					),
				),
				'devices'    => array( 'desktop', 'tablet', 'mobile' ),
				'classes'    => 'wd-hide-custom-breakpoints',
				'condition'  => array(
					'blog_design' => array( 'masonry', 'mask', 'meta-image' ),
				),
			)
		);

		$this->add_responsive_control(
			'blog_spacing',
			array(
				'label'     => esc_html__( 'Space between', 'woodmart' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					0  => esc_html__( '0 px', 'woodmart' ),
					2  => esc_html__( '2 px', 'woodmart' ),
					6  => esc_html__( '6 px', 'woodmart' ),
					10 => esc_html__( '10 px', 'woodmart' ),
					20 => esc_html__( '20 px', 'woodmart' ),
					30 => esc_html__( '30 px', 'woodmart' ),
				),
				'default'   => 20,
				'devices'   => array( 'desktop', 'tablet', 'mobile' ),
				'classes'   => 'wd-hide-custom-breakpoints',
				'condition' => array(
					'blog_design' => array( 'masonry', 'mask', 'meta-image' ),
				),
			)
		);

		$this->end_controls_section();

		/**
		 * Visibility settings.
		 */
		$this->start_controls_section(
			'visibility_style_section',
			array(
				'label' => esc_html__( 'Elements visibility', 'woodmart' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'parts_title',
			array(
				'label'        => esc_html__( 'Title for posts', 'woodmart' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '1',
				'label_on'     => esc_html__( 'Yes', 'woodmart' ),
				'label_off'    => esc_html__( 'No', 'woodmart' ),
				'return_value' => '1',
			)
		);

		$this->add_control(
			'parts_meta',
			array(
				'label'        => esc_html__( 'Meta information', 'woodmart' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '1',
				'label_on'     => esc_html__( 'Yes', 'woodmart' ),
				'label_off'    => esc_html__( 'No', 'woodmart' ),
				'return_value' => '1',
			)
		);

		$this->add_control(
			'parts_text',
			array(
				'label'        => esc_html__( 'Post text', 'woodmart' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '1',
				'label_on'     => esc_html__( 'Yes', 'woodmart' ),
				'label_off'    => esc_html__( 'No', 'woodmart' ),
				'return_value' => '1',
			)
		);

		$this->add_control(
			'parts_btn',
			array(
				'label'        => esc_html__( 'Read more button', 'woodmart' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '1',
				'label_on'     => esc_html__( 'Yes', 'woodmart' ),
				'label_off'    => esc_html__( 'No', 'woodmart' ),
				'return_value' => '1',
			)
		);

		$this->add_control(
			'parts_published_date',
			array(
				'label'        => esc_html__( 'Published date', 'woodmart' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '1',
				'label_on'     => esc_html__( 'Yes', 'woodmart' ),
				'label_off'    => esc_html__( 'No', 'woodmart' ),
				'return_value' => '1',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render the widget output on the frontend.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( isset( $_POST['action'] ) && 'wd_layout_create' === $_POST['action'] && ! empty( $_POST['predefined_name'] ) ) {
			return;
		}

		Main::setup_preview();

		if ( isset( $settings['parts_title'] ) ) {
			woodmart_set_loop_prop( 'parts_title', $settings['parts_title'] );
		}

		if ( isset( $settings['parts_meta'] ) ) {
			woodmart_set_loop_prop( 'parts_meta', $settings['parts_meta'] );
		}

		if ( isset( $settings['parts_text'] ) ) {
			woodmart_set_loop_prop( 'parts_text', $settings['parts_text'] );
		}

		if ( isset( $settings['parts_btn'] ) ) {
			woodmart_set_loop_prop( 'parts_btn', $settings['parts_btn'] );
		}

		if ( isset( $settings['parts_published_date'] ) ) {
			woodmart_set_loop_prop( 'parts_published_date', $settings['parts_published_date'] );
		}

		if ( isset( $settings['img_size'] ) ) {
			woodmart_set_loop_prop( 'img_size', $settings['img_size'] );
		}

		if ( ! empty( $settings['img_size_custom'] ) ) {
			woodmart_set_loop_prop( 'img_size_custom', $settings['img_size_custom'] );
		}

		if ( ! empty( $settings['blog_columns']['size'] ) ) {
			woodmart_set_loop_prop( 'blog_columns', $settings['blog_columns']['size'] );
		}

		if ( ! empty( $settings['blog_columns_tablet']['size'] ) ) {
			woodmart_set_loop_prop( 'blog_columns_tablet', $settings['blog_columns_tablet']['size'] );
		}

		if ( ! empty( $settings['blog_columns_mobile']['size'] ) ) {
			woodmart_set_loop_prop( 'blog_columns_mobile', $settings['blog_columns_mobile']['size'] );
		}

		if ( isset( $settings['blog_spacing'] ) && 'inherit' !== $settings['blog_spacing'] ) {
			woodmart_set_loop_prop( 'blog_spacing', $settings['blog_spacing'] );
		}

		if ( isset( $settings['blog_spacing_tablet'] ) && 'inherit' !== $settings['blog_spacing_tablet'] ) {
			woodmart_set_loop_prop( 'blog_spacing_tablet', $settings['blog_spacing_tablet'] );
		}

		if ( isset( $settings['blog_spacing_mobile'] ) && 'inherit' !== $settings['blog_spacing_mobile'] ) {
			woodmart_set_loop_prop( 'blog_spacing_mobile', $settings['blog_spacing_mobile'] );
		}

		if ( isset( $settings['blog_design'] ) && 'inherit' !== $settings['blog_design'] ) {
			woodmart_set_loop_prop( 'blog_design', $settings['blog_design'] );
		}

		woodmart_set_loop_prop( 'blog_masonry', ! empty( $settings['blog_masonry'] ) );

		$required_controls = array(
			'blog_design',
			'img_size',
			'img_size_custom',
			'blog_masonry',
			'parts_published_date',
			'parts_title',
			'parts_meta',
			'parts_text',
			'parts_btn',
		);

		$filtered_settings = array_intersect_key( $settings, array_flip( $required_controls ) );
		woodmart_main_loop( $filtered_settings );
		Main::restore_preview();
		woodmart_reset_loop();
	}
}

Plugin::instance()->widgets_manager->register( new Blog_Archive() );
