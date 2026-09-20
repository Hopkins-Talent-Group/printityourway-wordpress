<?php
/**
 * Toggle map.
 *
 * @package xts
 */

namespace XTS\Elementor;

use Elementor\Group_Control_Typography;
use Elementor\Controls_Manager;
use Elementor\Plugin;
use Elementor\Modules\NestedElements\Base\Widget_Nested_Base;
use XTS\Modules\Seo_Scheme\Faq;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Elementor widget that inserts an embeddable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class Toggle extends Widget_Nested_Base {
	/**
	 * Get widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'wd_toggle';
	}

	/**
	 * Get widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Toggle', 'woodmart' );
	}

	/**
	 * Get widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'wd-icon-toggle';
	}

	/**
	 * Get widget categories.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( 'wd-elements' );
	}

	/**
	 * Show in panel.
	 *
	 * @return bool
	 */
	public function show_in_panel() {
		return Plugin::$instance->experiments->is_feature_active( 'nested-elements' );
	}

	/**
	 * Get default children elements.
	 *
	 * @return array[]
	 */
	protected function get_default_children_elements() {
		return array(
			array(
				'elType'   => 'container',
				'settings' => array(
					'_title'        => esc_html__( 'Toggle content', 'woodmart' ),
					'content_width' => 'full',
				),
			),
		);
	}

	/**
	 * Get default children title.
	 *
	 * @return string
	 */
	protected function get_default_children_title() {
		return esc_html__( 'Toggle content', 'woodmart' );
	}

	/**
	 * Get default children placeholder selector.
	 *
	 * @return string
	 */
	protected function get_default_children_placeholder_selector() {
		return '.wd-el-toggle-content';
	}

	/**
	 * Get default repeater title setting key.
	 *
	 * @return string
	 */
	protected function get_default_repeater_title_setting_key() {
		return '';
	}

	/**
	 * Register the widget controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {
		/**
		 * Content tab.
		 */

		/**
		 * General settings.
		 */
		$this->start_controls_section(
			'general_content_section',
			array(
				'label' => esc_html__( 'General', 'woodmart' ),
			)
		);

		$this->add_control(
			'element_title',
			array(
				'label'   => esc_html__( 'Title', 'woodmart' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Title',
			)
		);

		$this->add_responsive_control(
			'state',
			array(
				'label'          => esc_html__( 'State', 'woodmart' ),
				'type'           => Controls_Manager::SELECT,
				'options'        => array(
					'closed' => esc_html__( 'Closed', 'woodmart' ),
					'opened' => esc_html__( 'Opened', 'woodmart' ),
					'static' => esc_html__( 'Always opened', 'woodmart' ),
				),
				'default'        => 'closed',
				'tablet_default' => 'closed',
				'mobile_default' => 'closed',
			)
		);

		$this->add_control(
			'rotate_icon',
			array(
				'label'        => esc_html__( 'Rotate icon on open', 'woodmart' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '1',
				'label_on'     => esc_html__( 'Yes', 'woodmart' ),
				'label_off'    => esc_html__( 'No', 'woodmart' ),
				'return_value' => '1',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'faq_scheme_tab',
			array(
				'label' => esc_html__( 'FAQ Scheme', 'elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'faq_schema',
			array(
				'label'       => esc_html__( 'Enable FAQ Schema', 'elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => wp_kses(
					__( 'Adds FAQ schema to the site, improving its visibility in search engines. Learn more about <a href="https://developers.google.com/search/docs/appearance/structured-data/faqpage" target="_blank">Google documentation</a>', 'woodmart' ),
					true
				),
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

		$this->add_responsive_control(
			'heading_spacing',
			array(
				'label'      => esc_html__( 'Heading spacing', 'woodmart' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 1,
						'max'  => 100,
						'step' => 1,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .wd-el-toggle-content' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		/**
		 * Title settings.
		 */
		$this->start_controls_section(
			'title_style_section',
			array(
				'label' => esc_html__( 'Title', 'woodmart' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'label'    => esc_html__( 'Typography', 'woodmart' ),
				'selector' => '{{WRAPPER}} .wd-el-toggle-title',
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'Color', 'woodmart' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wd-el-toggle-title' => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'opener_style_section',
			array(
				'label' => esc_html__( 'Opener', 'woodmart' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'opener_color',
			array(
				'label'     => esc_html__( 'Color', 'woodmart' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wd-el-toggle-icon:before' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'opener_size',
			array(
				'label'      => esc_html__( 'Size', 'woodmart' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 1,
						'max'  => 100,
						'step' => 1,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .wd-el-toggle-icon:before' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function render() {
		$inner_content = '';
		$children      = $this->get_children();
		$settings      = wp_parse_args(
			$this->get_settings_for_display(),
			array(
				'element_title' => '',
				'rotate_icon'   => true,
				'state'         => 'closed',
				'state_tablet'  => 'closed',
				'state_mobile'  => 'closed',
			)
		);

		$this->add_render_attribute(
			array(
				'wrapper' => array(
					'class' => array(
						'wd-el-toggle',
						'wd-state-' . $settings['state'] . '-lg',
						'wd-state-' . $settings['state_tablet'] . '-md-sm',
						'wd-state-' . $settings['state_mobile'] . '-sm',
					),
				),
				'title'   => array(
					'class' => array(
						'wd-el-toggle-title',
						'title',
					),
				),
			)
		);

		if ( in_array( $settings['state'], array( 'opened', 'static' ), true ) ) {
			$this->add_render_attribute( 'wrapper', 'class', 'wd-active-lg' );
		}
		if ( in_array( $settings['state_tablet'], array( 'opened', 'static' ), true ) ) {
			$this->add_render_attribute( 'wrapper', 'class', 'wd-active-md-sm' );
		}
		if ( in_array( $settings['state_mobile'], array( 'opened', 'static' ), true ) ) {
			$this->add_render_attribute( 'wrapper', 'class', 'wd-active-sm' );
		}

		if ( $settings['rotate_icon'] ) {
			$this->add_render_attribute( 'wrapper', 'class', 'wd-icon-rotate' );
		}

		if ( $children ) {
			ob_start();

			foreach ( $children as $child ) {
				$child->print_element();
			}

			$inner_content = ob_get_clean();
		}

		if ( $settings['faq_schema'] ) {
			Faq::get_instance()->add_faq_schema(
				'{
					"@type": "Question",
					"name": ' . wp_json_encode( $settings['element_title'], JSON_UNESCAPED_UNICODE ) . ',
					"acceptedAnswer": {
						"@type": "Answer",
						"text": "' . trim( strip_tags( preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $inner_content ), apply_filters( 'woodmart_allowed_faq_schema_html_tags', '<br>' ) ) ) . '"
					}
				}'
			);
		}

		woodmart_enqueue_inline_style( 'el-toggle' );
		woodmart_enqueue_js_script( 'toggle-element' );
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); //phpcs:ignore ?>>
			<div class="wd-el-toggle-head">
				<div <?php echo $this->get_render_attribute_string( 'title' ); // phpcs:ignore ?>>
					<?php echo wp_kses( $settings['element_title'], true ); ?>
				</div>
				<div class="wd-el-toggle-icon"></div>
			</div>
			<div class="wd-el-toggle-content">
				<div class="wd-el-toggle-content-inner">
					<?php echo wp_kses_post( $inner_content ); ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render the widget output in the editor.
	 *
	 * @return void
	 */
	protected function content_template() {
		?>
		<#
		view.addRenderAttribute( 'wrapper', {
			'class': [
				'wd-el-toggle',
				'wd-state-' + settings.state + '-lg',
				'wd-state-' + settings.state_tablet + '-md-sm',
				'wd-state-' + settings.state_mobile + '-sm',
			],
		} );

		if ( 'opened' === settings['state'] || 'static' === settings['state'] ) {
			view.addRenderAttribute( 'wrapper', 'class', 'wd-active-lg' );
		}
		if ( 'opened' === settings['state_tablet'] || 'static' === settings['state_tablet'] ) {
			view.addRenderAttribute( 'wrapper', 'class', 'wd-active-md-sm' );
		}
		if ( 'opened' === settings['state_mobile'] || 'static' === settings['state_mobile'] ) {
			view.addRenderAttribute( 'wrapper', 'class', 'wd-active-sm' );
		}

		if ( settings.rotate_icon ) {
			view.addRenderAttribute( 'wrapper', 'class', 'wd-icon-rotate' );
		}

		view.addRenderAttribute( 'title', 'class', [ 'wd-el-toggle-title title' ] );
		#>

		<div {{{ view.getRenderAttributeString( 'wrapper' ) }}}>
			<div class="wd-el-toggle-head">
				<div {{{ view.getRenderAttributeString( 'title' ) }}}>
					{{{ settings.element_title }}}
				</div>
				<div class="wd-el-toggle-icon"></div>
			</div>
			<div class="wd-el-toggle-content">
				<div class="wd-el-toggle-content-inner"></div>
			</div>
		</div>
		<?php
	}
}

Plugin::instance()->widgets_manager->register( new Toggle() );
