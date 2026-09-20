<?php
/**
 * Post author meta.
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
class Post_Author extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'wd_post_author';
	}

	/**
	 * Get widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Post author', 'woodmart' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'wd-icon-post-author';
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
		return Main::is_layout_type( 'single_post' );
	}

	/**
	 * Register the widget controls.
	 */
	protected function register_controls() {
		/**
		 * Content tab
		 */

		/**
		 * General settings
		 */
		$this->start_controls_section(
			'general_content_section',
			array(
				'label' => esc_html__( 'General', 'woodmart' ),
			)
		);

		$this->add_control(
			'css_classes',
			array(
				'type'         => 'wd_css_class',
				'default'      => 'wd-single-post-author',
				'prefix_class' => '',
			)
		);

		$this->add_control(
			'author_avatar',
			array(
				'label'        => esc_html__( 'Avatar', 'woodmart' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '1',
				'label_on'     => esc_html__( 'On', 'woodmart' ),
				'label_off'    => esc_html__( 'Off', 'woodmart' ),
				'return_value' => '1',
			)
		);

		$this->add_control(
			'author_label',
			array(
				'label'        => esc_html__( 'Label', 'woodmart' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '1',
				'label_on'     => esc_html__( 'On', 'woodmart' ),
				'label_off'    => esc_html__( 'Off', 'woodmart' ),
				'return_value' => '1',
			)
		);

		$this->add_control(
			'author_name',
			array(
				'label'        => esc_html__( 'Name', 'woodmart' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '1',
				'label_on'     => esc_html__( 'On', 'woodmart' ),
				'label_off'    => esc_html__( 'Off', 'woodmart' ),
				'return_value' => '1',
			)
		);

		$this->end_controls_section();

		/**
		 * Style tab.
		 */
		$this->start_controls_section(
			'general_style_section',
			array(
				'label' => esc_html__( 'General', 'woodmart' ),
				'tab'   => Controls_Manager::TAB_STYLE,
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
			'avatar_width',
			array(
				'label'      => esc_html__( 'Avatar width', 'woodmart' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 5,
						'max'  => 150,
						'step' => 1,
					),
				),
				'default'    => array(
					'size' => 22,
				),
				'condition'  => array(
					'author_avatar' => array( '1' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'label'    => esc_html__( 'Typography', 'woodmart' ),
				'name'     => 'link_typography',
				'selector' => '{{WRAPPER}} .wd-post-author',
			)
		);

		$this->add_control(
			'label_color',
			array(
				'label'     => esc_html__( 'Label color', 'woodmart' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wd-post-author' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'author_label' => array( '1' ),
				),
			)
		);

		$this->start_controls_tabs(
			'link_color_tabs',
			array(
				'condition' => array(
					'author_name' => array( '1' ),
				),
			)
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
				'label'     => esc_html__( 'Name color', 'woodmart' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wd-post-author' => '--wd-link-color: {{VALUE}}',
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
				'label'     => esc_html__( 'Name color', 'woodmart' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wd-post-author' => '--wd-link-color-hover: {{VALUE}}',
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
		Main::setup_preview();
		$args          = $this->get_settings_for_display();
		$author_label  = ! empty( $args['author_label'] ) ? 'long' : '';
		$author_avatar = ! empty( $args['author_avatar'] );
		$author_name   = ! empty( $args['author_name'] );

		if ( ! $author_label && ! $author_avatar && ! $author_name ) {
			Main::restore_preview();
			return;
		}

		$avatar_size = ! empty( $args['avatar_width']['size'] ) ? $args['avatar_width']['size'] : 22;

		woodmart_enqueue_inline_style( 'blog-mod-author' );
		echo '<div class="wd-post-author">';
		woodmart_post_meta_author( $author_avatar, $author_label, $author_name, $avatar_size );
		echo '</div>';
		Main::restore_preview();
	}
}

Plugin::instance()->widgets_manager->register( new Post_Author() );
