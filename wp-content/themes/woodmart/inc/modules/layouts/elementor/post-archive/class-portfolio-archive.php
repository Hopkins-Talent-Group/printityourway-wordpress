<?php
/**
 * Portfolio archive.
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
class Portfolio_Archive extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'wd_portfolio_archive';
	}

	/**
	 * Get widget content.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Portfolio archive', 'woodmart' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'wd-icon-portfolio-archive';
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
		return Main::is_layout_type( 'portfolio_archive' );
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
				'default'      => 'wd-portfolio-archive wd-width-100',
				'prefix_class' => '',
			)
		);

		$this->add_control(
			'portfolio_style',
			array(
				'label'   => esc_html__( 'Style', 'woodmart' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'inherit',
				'options' => array(
					'inherit'       => esc_html__( 'Inherit from Theme Settings', 'woodmart' ),
					'hover'         => esc_html__( 'Show text on mouse over', 'woodmart' ),
					'hover-inverse' => esc_html__( 'Alternative', 'woodmart' ),
					'text-shown'    => esc_html__( 'Text under image', 'woodmart' ),
					'parallax'      => esc_html__( 'Mouse move parallax', 'woodmart' ),
				),
			)
		);

		$this->add_control(
			'portfolio_image_size',
			array(
				'label'     => esc_html__( 'Image size', 'woodmart' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'large',
				'options'   => woodmart_get_all_image_sizes_names( 'elementor' ),
				'condition' => array(
					'portfolio_style!' => array( 'inherit' ),
				),
			)
		);

		$this->add_control(
			'portfolio_image_size_custom',
			array(
				'label'       => esc_html__( 'Image dimension', 'woodmart' ),
				'type'        => Controls_Manager::IMAGE_DIMENSIONS,
				'description' => esc_html__( 'You can crop the original image size to any custom size. You can also set a single value for height or width in order to keep the original size ratio.', 'woodmart' ),
				'condition'   => array(
					'portfolio_image_size' => 'custom',
					'portfolio_style!'     => array( 'inherit' ),
				),
			)
		);

		$this->add_responsive_control(
			'portfolio_column',
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
						'max'  => 6,
						'step' => 1,
					),
				),
				'devices'    => array( 'desktop', 'tablet', 'mobile' ),
				'classes'    => 'wd-hide-custom-breakpoints',
				'condition'  => array(
					'portfolio_style!' => array( 'inherit' ),
				),
			)
		);

		$this->add_responsive_control(
			'portfolio_spacing',
			array(
				'label'     => esc_html__( 'Space between', 'woodmart' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'0'  => esc_html__( '0 px', 'woodmart' ),
					'2'  => esc_html__( '2 px', 'woodmart' ),
					'6'  => esc_html__( '6 px', 'woodmart' ),
					'10' => esc_html__( '10 px', 'woodmart' ),
					'20' => esc_html__( '20 px', 'woodmart' ),
					'30' => esc_html__( '30 px', 'woodmart' ),
				),
				'default'   => 20,
				'devices'   => array( 'desktop', 'tablet', 'mobile' ),
				'classes'   => 'wd-hide-custom-breakpoints',
				'condition' => array(
					'portfolio_style!' => array( 'inherit' ),
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render the widget output on the frontend.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		Main::setup_preview();

		if ( ! empty( $settings['portfolio_image_size'] ) ) {
			woodmart_set_loop_prop( 'portfolio_image_size', $settings['portfolio_image_size'] );
		}

		if ( ! empty( $settings['portfolio_image_size_custom'] ) ) {
			woodmart_set_loop_prop( 'portfolio_image_size_custom', $settings['portfolio_image_size_custom'] );
		}

		if ( isset( $settings['portfolio_style'] ) && 'inherit' !== $settings['portfolio_style'] ) {
			woodmart_set_loop_prop( 'portfolio_style', $settings['portfolio_style'] );
		}

		if ( isset( $settings['portfolio_column']['size'] ) ) {
			woodmart_set_loop_prop( 'portfolio_column', $settings['portfolio_column']['size'] );
		}

		if ( isset( $settings['portfolio_column_tablet']['size'] ) ) {
			woodmart_set_loop_prop( 'portfolio_columns_tablet', $settings['portfolio_column_tablet']['size'] );
		}

		if ( isset( $settings['portfolio_column_mobile']['size'] ) ) {
			woodmart_set_loop_prop( 'portfolio_columns_mobile', $settings['portfolio_column_mobile']['size'] );
		}

		if ( isset( $settings['portfolio_spacing'] ) && 'inherit' !== $settings['portfolio_spacing'] ) {
			woodmart_set_loop_prop( 'portfolio_spacing', $settings['portfolio_spacing'] );
		}

		if ( isset( $settings['portfolio_spacing_tablet'] ) && 'inherit' !== $settings['portfolio_spacing_tablet'] ) {
			woodmart_set_loop_prop( 'portfolio_spacing_tablet', $settings['portfolio_spacing_tablet'] );
		}

		if ( isset( $settings['portfolio_spacing_mobile'] ) && 'inherit' !== $settings['portfolio_spacing_mobile'] ) {
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

		?>
		<?php if ( have_posts() ) : ?>
		<div class="wd-portfolio-element">
			<?php woodmart_get_portfolio_main_loop( false, $filtered_settings ); ?>
		</div>
		<?php else : ?>
			<?php get_template_part( 'content', 'none' ); ?>
		<?php endif; ?>
		<?php
		Main::restore_preview();
		woodmart_reset_loop();
	}
}

Plugin::instance()->widgets_manager->register( new Portfolio_Archive() );
