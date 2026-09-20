<?php
/**
 * Categories map.
 *
 * @package Woodmart
 */

namespace XTS\Modules\Layouts;

use Elementor\Controls_Manager;
use Elementor\Plugin;
use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Elementor widget that inserts an embeddable content into the page, from any given URL.
 */
class Post_Categories extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'wd_single_post_categories';
	}

	/**
	 * Get widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Post categories', 'woodmart' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'wd-icon-post-categories';
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
				'default'      => 'wd-single-post-cat',
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
			'categories_style',
			array(
				'label'   => esc_html__( 'Style', 'woodmart' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'default' => esc_html__( 'Default', 'woodmart' ),
					'with-bg' => esc_html__( 'With background', 'woodmart' ),
				),
				'default' => 'default',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'label'    => esc_html__( 'Typography', 'woodmart' ),
				'name'     => 'typography',
				'selector' => '{{WRAPPER}}.wd-single-post-cat .wd-post-cat',
			)
		);

		$this->start_controls_tabs(
			'link_color_tabs'
		);

		$this->start_controls_tab(
			'link_color_tab',
			array(
				'label' => esc_html__( 'Idle', 'woodmart' ),
			)
		);

		$this->add_control(
			'link_idle_color',
			array(
				'label'     => esc_html__( 'Color', 'woodmart' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}.wd-single-post-cat .wd-post-cat' => '--wd-link-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'cats_bg',
			array(
				'label'     => esc_html__( 'Background color', 'woodmart' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wd-post-cat.wd-style-with-bg' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'categories_style' => array( 'with-bg' ),
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'link_hover_color_tab',
			array(
				'label' => esc_html__( 'Hover', 'woodmart' ),
			)
		);

		$this->add_control(
			'link_hover_color',
			array(
				'label'     => esc_html__( 'Color', 'woodmart' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}.wd-single-post-cat .wd-post-cat' => '--wd-link-color-hover: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Render the widget output on the frontend.
	 */
	protected function render() {
		$is_portfolio = Main::get_instance()->has_custom_layout( 'single_portfolio' );

		Main::setup_preview();

		$cats           = get_the_category_list( ', ' );
		$portfolio_cats = wp_get_post_terms( get_the_ID(), 'project-cat' );

		if ( $is_portfolio && ! empty( $portfolio_cats ) && ! is_wp_error( $portfolio_cats ) ) {
			if ( ! empty( $portfolio_cats ) && ! is_wp_error( $portfolio_cats ) ) {
				$cat_links = array();

				foreach ( $portfolio_cats as $cat ) {
					$cat_link = get_term_link( $cat );
					if ( ! is_wp_error( $cat_link ) ) {
						$cat_links[] = '<a href="' . esc_url( $cat_link ) . '">' . esc_html( $cat->name ) . '</a>';
					}
				}

				$cats = implode( ', ', $cat_links );
			}
		}

		if ( $cats || $portfolio_cats ) {
			woodmart_enqueue_inline_style( 'post-types-mod-predefined' );
			$args     = $this->get_settings_for_display();
			$classes  = 'wd-post-cat';
			$classes .= ' wd-style-' . $args['categories_style'];

			if ( 'with-bg' === $args['categories_style'] ) {
				woodmart_enqueue_inline_style( 'post-types-mod-categories-style-bg' );
			}

			echo '<div class="' . esc_attr( $classes ) . '">' . $cats . '</div>'; // phpcs:ignore
		}

		Main::restore_preview();
	}
}

Plugin::instance()->widgets_manager->register( new Post_Categories() );
