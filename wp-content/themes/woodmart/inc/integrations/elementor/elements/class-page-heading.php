<?php
/**
 * Page heading map.
 *
 * @package Woodmart
 */

namespace XTS\Elementor;

use Elementor\Group_Control_Typography;
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
class Page_Heading extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'wd_page_heading';
	}

	/**
	 * Get widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Page heading', 'woodmart' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'wd-icon-page-heading';
	}

	/**
	 * Get widget categories.
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( 'wd-site-elements' );
	}

	/**
	 * Register the widget controls.
	 */
	protected function register_controls() {

		/**
		 * Content tab.
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
				'default'      => 'wd-el-page-heading',
				'prefix_class' => '',
			)
		);

		$this->add_control(
			'text_alignment',
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

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'label'    => esc_html__( 'Typography', 'woodmart' ),
				'name'     => 'typography',
				'selector' => '{{WRAPPER}} .title',
			)
		);

		$this->add_control(
			'text_color',
			array(
				'label'     => esc_html__( 'Color', 'woodmart' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .title' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'tag',
			array(
				'label'   => esc_html__( 'Title tag', 'woodmart' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'h1'   => esc_html__( 'h1', 'woodmart' ),
					'h2'   => esc_html__( 'h2', 'woodmart' ),
					'h3'   => esc_html__( 'h3', 'woodmart' ),
					'h4'   => esc_html__( 'h4', 'woodmart' ),
					'h5'   => esc_html__( 'h5', 'woodmart' ),
					'h6'   => esc_html__( 'h6', 'woodmart' ),
					'p'    => esc_html__( 'p', 'woodmart' ),
					'div'  => esc_html__( 'div', 'woodmart' ),
					'span' => esc_html__( 'span', 'woodmart' ),
				),
				'default' => 'h2',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render the widget output on the frontend.
	 */
	protected function render() {
		global $post;

		if ( function_exists( 'dokan_is_store_page' ) && dokan_is_store_page() ) {
			return '';
		}

		$settings = wp_parse_args(
			$this->get_settings_for_display(),
			array(
				'tag' => 'h2',
			)
		);

		$title_tag          = $settings['tag'];
		$page_for_posts     = get_option( 'page_for_posts' );
		$is_blog_builder    = Main::is_layout_type( 'blog_archive' );
		$single_post_design = woodmart_get_opt( 'single_post_design' );
		$classes            = '';

		Main::setup_preview();
		if ( woodmart_is_blog_archive() || ( 'large_image' !== $single_post_design && $post && 'post' === $post->post_type ) || $is_blog_builder ) {
			$title = ( ! empty( $page_for_posts ) ) ? get_the_title( $page_for_posts ) : esc_html__( 'Blog', 'woodmart' );

			if ( is_tag() ) {
				$title = esc_html__( 'Tag Archives: ', 'woodmart' ) . single_tag_title( '', false );
			}

			if ( is_category() ) {
				$title = single_cat_title( '', false );
			}

			if ( is_date() ) {
				if ( is_day() ) {
					$title = esc_html__( 'Daily Archives: ', 'woodmart' ) . get_the_date();
				} elseif ( is_month() ) {
					$title = esc_html__( 'Monthly Archives: ', 'woodmart' ) . get_the_date( _x( 'F Y', 'monthly archives date format', 'woodmart' ) );
				} elseif ( is_year() ) {
					$title = esc_html__( 'Yearly Archives: ', 'woodmart' ) . get_the_date( _x( 'Y', 'yearly archives date format', 'woodmart' ) );
				} else {
					$title = esc_html__( 'Archives', 'woodmart' );
				}
			}

			if ( is_author() ) {
				the_post();
				$title    = esc_html__( 'Posts by ', 'woodmart' ) . '<a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '" title="' . esc_attr( get_the_author() ) . '" rel="me">' . get_the_author() . '</a>';
				$classes .= ' vcard';
				rewind_posts();
			}

			if ( is_search() ) {
				$title = esc_html__( 'Search Results for: ', 'woodmart' ) . get_search_query();
			}
		} elseif ( ( ! woodmart_get_opt( 'single_portfolio_title_in_page_title' ) && $post && 'portfolio' === $post->post_type ) || woodmart_is_portfolio_archive() ) {
			$title = get_the_title( woodmart_get_portfolio_page_id() );

			if ( is_tax( 'project-cat' ) ) {
				$title = single_term_title( '', false );
			}
		} elseif ( woodmart_is_shop_archive() || Main::get_instance()->has_custom_layout( 'shop_archive' ) ) {
			$title = woocommerce_page_title( false );
		} elseif ( Main::is_layout_type( 'cart' ) ) {
			$title = esc_html__( 'Cart', 'woodmart' );
		} elseif ( Main::is_layout_type( 'checkout_form' ) || Main::is_layout_type( 'checkout_content' ) ) {
			$title = esc_html__( 'Checkout', 'woodmart' );
		} else {
			$title = get_the_title();
		}

		echo '<' . esc_attr( $title_tag ) . ' class="entry-title title' . esc_attr( $classes ) . '">';
		echo wp_kses_post( $title );
		echo '</' . esc_attr( $title_tag ) . '>';
		Main::restore_preview();
	}
}

Plugin::instance()->widgets_manager->register( new Page_Heading() );
