<?php
namespace XTS\Modules\Header_Builder\Elements;

use XTS\Modules\Header_Builder\Element;

/**
 * ------------------------------------------------------------------------------------------------
 * Search form. A few kinds of it.
 * ------------------------------------------------------------------------------------------------
 */
class Search extends Element {

	public function __construct() {
		parent::__construct();

		$this->template_name = 'search';
	}

	public function map() {
		$search_extra_content_options = array(
			array(
				'value' => '',
				'label' => esc_html__( 'Inherit from Theme Settings', 'woodmart' ),
			),
		) + $this->get_html_block_options();

		$search_extra_content_description = '';

		if ( function_exists( 'woodmart_get_html_block_links' ) ) {
			$search_extra_content_description .= woodmart_get_html_block_links();
		}

		$this->args = array(
			'type'            => 'search',
			'title'           => esc_html__( 'Search', 'woodmart' ),
			'text'            => esc_html__( 'Search form', 'woodmart' ),
			'icon'            => 'xts-i-search',
			'editable'        => true,
			'container'       => false,
			'edit_on_create'  => true,
			'drag_target_for' => array(),
			'drag_source'     => 'content_element',
			'removable'       => true,
			'addable'         => true,
			'desktop'         => true,
			'params'          => array(
				// General.
				'display'                => array(
					'id'          => 'display',
					'title'       => esc_html__( 'Display', 'woodmart' ),
					'type'        => 'selector',
					'tab'         => esc_html__( 'General', 'woodmart' ),
					'group'       => esc_html__( 'General', 'woodmart' ),
					'value'       => 'full-screen',
					'options'     => array(
						'full-screen'   => array(
							'value' => 'full-screen',
							'label' => esc_html__( 'Full screen', 'woodmart' ),
							'hint'  => '<video src="' . WOODMART_TOOLTIP_URL . 'hb_search_display_full_screen.mp4" autoplay loop muted></video>',
						),
						'full-screen-2' => array(
							'value' => 'full-screen',
							'label' => esc_html__( 'Full screen 2', 'woodmart' ),
							'hint'  => '<video src="' . WOODMART_TOOLTIP_URL . 'hb_search_display_full_screen_2.mp4" autoplay loop muted></video>',
						),
						'dropdown'      => array(
							'value' => 'dropdown',
							'label' => esc_html__( 'Dropdown', 'woodmart' ),
							'hint'  => '<video src="' . WOODMART_TOOLTIP_URL . 'hb_search_display_dropdown.mp4" autoplay loop muted></video>',
						),
						'form'          => array(
							'value' => 'form',
							'label' => esc_html__( 'Form', 'woodmart' ),
							'hint'  => '<video src="' . WOODMART_TOOLTIP_URL . 'hb_search_display_form.mp4" autoplay loop muted></video>',
						),
					),
					'description' => esc_html__( 'Display search icon/form in the header in different views.', 'woodmart' ),
				),
				'popular_requests'       => array(
					'id'          => 'popular_requests',
					'title'       => esc_html__( 'Show popular requests', 'woodmart' ),
					'hint'        => '<video src="' . WOODMART_TOOLTIP_URL . 'hb_search_display_popular_requests.mp4" autoplay loop muted></video>',
					'type'        => 'switcher',
					'description' => __( 'You can write a list of popular requests in Theme Settings -> General -> Search', 'woodmart' ),
					'tab'         => esc_html__( 'General', 'woodmart' ),
					'group'       => esc_html__( 'General', 'woodmart' ),
					'value'       => false,
				),
				'search_history_enabled' => array(
					'id'          => 'search_history_enabled',
					'title'       => esc_html__( 'Show search history', 'woodmart' ),
					'hint'        => '<video src="' . WOODMART_TOOLTIP_URL . 'search-history-enabled.mp4" autoplay loop muted></video>',
					'description' => esc_html__( 'Allowing users to quickly access their previous searches.', 'woodmart' ),
					'type'        => 'switcher',
					'tab'         => esc_html__( 'General', 'woodmart' ),
					'group'       => esc_html__( 'General', 'woodmart' ),
					'on-text'     => esc_html__( 'Yes', 'woodmart' ),
					'off-text'    => esc_html__( 'No', 'woodmart' ),
					'value'       => false,
				),
				'search_extra_content_enabled' => array(
					'id'    => 'search_extra_content_enabled',
					'title' => esc_html__( 'Search extra content', 'woodmart' ),
					'hint'        => '<video src="' . WOODMART_TOOLTIP_URL . 'full-screen-search-extra-content.mp4" autoplay loop muted></video>',
					'tab'   => esc_html__( 'General', 'woodmart' ),
					'group' => esc_html__( 'General', 'woodmart' ),
					'type'  => 'switcher',
					'value' => false,
				),
				'search_extra_content'   => array(
					'id'          => 'search_extra_content',
					'type'        => 'select',
					'tab'         => esc_html__( 'General', 'woodmart' ),
					'group'       => esc_html__( 'General', 'woodmart' ),
					'value'       => '',
					'options'     => $search_extra_content_options,
					'description' => $search_extra_content_description,
					'requires'    => array(
						'search_extra_content_enabled' => array(
							'comparison' => 'equal',
							'value'      => true,
						),
					),
				),
				'categories_dropdown'    => array(
					'id'          => 'categories_dropdown',
					'title'       => esc_html__( 'Show product categories dropdown', 'woodmart' ),
					'hint'        => '<video src="' . WOODMART_TOOLTIP_URL . 'hb_search_display_categories_dropdown.mp4" autoplay loop muted></video>',
					'type'        => 'switcher',
					'tab'         => esc_html__( 'General', 'woodmart' ),
					'group'       => esc_html__( 'General', 'woodmart' ),
					'value'       => false,
					'requires'    => array(
						'display' => array(
							'comparison' => 'not_equal',
							'value'      => array( 'full-screen', 'dropdown' ),
						),
					),
					'description' => esc_html__( 'Ability to search in a specific category.', 'woodmart' ),
				),
				'cat_selector_style'     => array(
					'id'       => 'cat_selector_style',
					'title'    => esc_html__( 'Product categories selector style', 'woodmart' ),
					'type'     => 'selector',
					'tab'      => esc_html__( 'General', 'woodmart' ),
					'group'    => esc_html__( 'General', 'woodmart' ),
					'value'    => 'bordered',
					'options'  => array(
						'default'   => array(
							'value' => 'default',
							'label' => esc_html__( 'Default', 'woodmart' ),
							'hint'  => '<img src="' . WOODMART_TOOLTIP_URL . 'hb_categories_selector_style_default.jpg" alt="">',
						),
						'bordered'  => array(
							'value' => 'bordered',
							'label' => esc_html__( 'Bordered', 'woodmart' ),
							'hint'  => '<img src="' . WOODMART_TOOLTIP_URL . 'hb_categories_selector_style_bordered.jpg" alt="">',
						),
						'separated' => array(
							'value' => 'separated',
							'label' => esc_html__( 'Separated', 'woodmart' ),
							'hint'  => '<img src="' . WOODMART_TOOLTIP_URL . 'hb_categories_selector_style_separated.jpg" alt="">',
						),
					),
					'requires' => array(
						'display'             => array(
							'comparison' => 'not_equal',
							'value'      => array( 'full-screen', 'dropdown' ),
						),
						'categories_dropdown' => array(
							'comparison' => 'equal',
							'value'      => true,
						),
					),
				),
				'bg_overlay'             => array(
					'id'          => 'bg_overlay',
					'title'       => esc_html__( 'Background overlay', 'woodmart' ),
					'hint'        => '<video src="' . WOODMART_TOOLTIP_URL . 'hb_search_bg_overlay.mp4" autoplay loop muted></video>',
					'description' => esc_html__( 'Highlight dropdowns by darkening the background behind.', 'woodmart' ),
					'type'        => 'switcher',
					'tab'         => esc_html__( 'General', 'woodmart' ),
					'group'       => esc_html__( 'General', 'woodmart' ),
					'value'       => false,
					'requires'    => array(
						'display' => array(
							'comparison' => 'equal',
							'value'      => array( 'dropdown', 'form' ),
						),
					),
				),
				// Search result.
				'ajax'                   => array(
					'id'          => 'ajax',
					'title'       => esc_html__( 'Search with AJAX', 'woodmart' ),
					'hint'        => '<video src="' . WOODMART_TOOLTIP_URL . 'hb_search_ajax.mp4" autoplay loop muted></video>',
					'type'        => 'switcher',
					'tab'         => esc_html__( 'General', 'woodmart' ),
					'group'       => esc_html__( 'Search result', 'woodmart' ),
					'value'       => true,
					'description' => esc_html__( 'Enable instant AJAX search functionality for this form.', 'woodmart' ),
				),
				'ajax_result_count'      => array(
					'id'          => 'ajax_result_count',
					'title'       => esc_html__( 'AJAX search results count', 'woodmart' ),
					'description' => esc_html__( 'Number of products to display in AJAX search results.', 'woodmart' ),
					'type'        => 'slider',
					'tab'         => esc_html__( 'General', 'woodmart' ),
					'group'       => esc_html__( 'Search result', 'woodmart' ),
					'from'        => 3,
					'to'          => 50,
					'value'       => 20,
					'units'       => '',
					'requires'    => array(
						'ajax' => array(
							'comparison' => 'equal',
							'value'      => true,
						),
					),
				),
				'post_type'              => array(
					'id'      => 'post_type',
					'title'   => esc_html__( 'Post type', 'woodmart' ),
					'type'    => 'selector',
					'tab'     => esc_html__( 'General', 'woodmart' ),
					'group'   => esc_html__( 'Search result', 'woodmart' ),
					'value'   => 'product',
					'options' => array(
						'product'   => array(
							'value' => 'product',
							'label' => esc_html__( 'Product', 'woodmart' ),
						),
						'post'      => array(
							'value' => 'post',
							'label' => esc_html__( 'Post', 'woodmart' ),
						),
						'portfolio' => array(
							'value' => 'portfolio',
							'label' => esc_html__( 'Portfolio', 'woodmart' ),
						),
						'page'      => array(
							'value' => 'page',
							'label' => esc_html__( 'Page', 'woodmart' ),
						),
						'any'       => array(
							'value' => 'any',
							'label' => esc_html__( 'All post types', 'woodmart' ),
						),
					),
				),
				'include_cat_search'     => array(
					'id'          => 'include_cat_search',
					'title'       => esc_html__( 'Include categories in search', 'woodmart' ),
					'hint'        => '<video src="' . WOODMART_TOOLTIP_URL . 'include-cat-search.mp4" autoplay loop muted></video>',
					'description' => esc_html__( 'When enabled, the search function will also look for and display categories that match the search query.', 'woodmart' ),
					'type'        => 'switcher',
					'tab'         => esc_html__( 'General', 'woodmart' ),
					'group'       => esc_html__( 'Search result', 'woodmart' ),
					'value'       => false,
					'requires'    => array(
						'ajax'      => array(
							'comparison' => 'equal',
							'value'      => true,
						),
						'post_type' => array(
							'comparison' => 'equal',
							'value'      => 'product',
						),
					),
				),
				// Form.
				'search_style'           => array(
					'id'       => 'search_style',
					'title'    => esc_html__( 'Search style', 'woodmart' ),
					'type'     => 'selector',
					'tab'      => esc_html__( 'Style', 'woodmart' ),
					'group'    => esc_html__( 'Form', 'woodmart' ),
					'value'    => 'default',
					'options'  => array(
						'default'   => array(
							'value' => 'default',
							'label' => esc_html__( 'Style 1', 'woodmart' ),
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/search/default.jpg',
						),
						'with-bg'   => array(
							'value' => 'with-bg',
							'label' => esc_html__( 'Style 2', 'woodmart' ),
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/search/with-bg.jpg',
						),
						'with-bg-2' => array(
							'value' => 'with-bg-2',
							'label' => esc_html__( 'Style 3', 'woodmart' ),
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/search/with-bg-2.jpg',
						),
						'4'         => array(
							'value' => '4',
							'label' => esc_html__( 'Style 4', 'woodmart' ),
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/search/fourth.jpg',
						),
					),
					'requires' => array(
						'display' => array(
							'comparison' => 'not_equal',
							'value'      => array( 'full-screen', 'dropdown' ),
						),
					),
				),
				'form_shape'             => array(
					'id'            => 'form_shape',
					'title'         => esc_html__( 'Form shape', 'woodmart' ),
					'type'          => 'select',
					'tab'           => esc_html__( 'Style', 'woodmart' ),
					'group'         => esc_html__( 'Form', 'woodmart' ),
					'value'         => '',
					'generate_zero' => true,
					'options'       => array(
						''   => array(
							'label' => esc_html__( 'Inherit', 'woodmart' ),
							'value' => '',
						),
						'0'  => array(
							'label' => esc_html__( 'Square', 'woodmart' ),
							'value' => '0',
						),
						'5'  => array(
							'label' => esc_html__( 'Rounded', 'woodmart' ),
							'value' => '5',
						),
						'35' => array(
							'label' => esc_html__( 'Round', 'woodmart' ),
							'value' => '35',
						),
					),
					'selectors'     => array(
						'{{WRAPPER}}' => array(
							'--wd-form-brd-radius: {{VALUE}}px;',
						),
					),
					'requires'      => array(
						'display' => array(
							'comparison' => 'not_equal',
							'value'      => array( 'full-screen', 'dropdown' ),
						),
					),
				),
				'form_height'            => array(
					'id'        => 'form_height',
					'title'     => esc_html__( 'Form height', 'woodmart' ),
					'type'      => 'slider',
					'tab'       => esc_html__( 'Style', 'woodmart' ),
					'group'     => esc_html__( 'Form', 'woodmart' ),
					'from'      => 30,
					'to'        => 100,
					'value'     => 46,
					'units'     => 'px',
					'selectors' => array(
						'{{WRAPPER}} form.searchform' => array(
							'--wd-form-height: {{VALUE}}px;',
						),
					),
				),
				'form_color'             => array(
					'id'          => 'form_color',
					'title'       => esc_html__( 'Form text color', 'woodmart' ),
					'type'        => 'color',
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Form', 'woodmart' ),
					'value'       => '',
					'selectors'   => array(
						'{{WRAPPER}}.wd-search-form.wd-header-search-form .searchform' => array(
							'--wd-form-color: {{VALUE}};',
						),
					),
					'requires'    => array(
						'display' => array(
							'comparison' => 'not_equal',
							'value'      => array( 'full-screen', 'dropdown' ),
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'form_placeholder_color' => array(
					'id'          => 'form_placeholder_color',
					'title'       => esc_html__( 'Form placeholder color', 'woodmart' ),
					'type'        => 'color',
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Form', 'woodmart' ),
					'value'       => '',
					'selectors'   => array(
						'{{WRAPPER}}.wd-search-form.wd-header-search-form .searchform' => array(
							'--wd-form-placeholder-color: {{VALUE}};',
						),
					),
					'requires'    => array(
						'display' => array(
							'comparison' => 'not_equal',
							'value'      => array( 'full-screen', 'dropdown' ),
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'form_brd_color'         => array(
					'id'          => 'form_brd_color',
					'title'       => esc_html__( 'Form border color', 'woodmart' ),
					'type'        => 'color',
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Form', 'woodmart' ),
					'value'       => '',
					'selectors'   => array(
						'{{WRAPPER}}.wd-search-form.wd-header-search-form .searchform' => array(
							'--wd-form-brd-color: {{VALUE}};',
						),
					),
					'requires'    => array(
						'display' => array(
							'comparison' => 'not_equal',
							'value'      => array( 'full-screen', 'dropdown' ),
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'form_brd_color_focus'   => array(
					'id'          => 'form_brd_color_focus',
					'title'       => esc_html__( 'Form border color focus', 'woodmart' ),
					'type'        => 'color',
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Form', 'woodmart' ),
					'value'       => '',
					'selectors'   => array(
						'{{WRAPPER}}.wd-search-form.wd-header-search-form .searchform' => array(
							'--wd-form-brd-color-focus: {{VALUE}};',
						),
					),
					'requires'    => array(
						'display' => array(
							'comparison' => 'not_equal',
							'value'      => array( 'full-screen', 'dropdown' ),
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'form_bg'                => array(
					'id'          => 'form_bg',
					'title'       => esc_html__( 'Form background color', 'woodmart' ),
					'type'        => 'color',
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Form', 'woodmart' ),
					'value'       => '',
					'selectors'   => array(
						'{{WRAPPER}}.wd-search-form.wd-header-search-form .searchform' => array(
							'--wd-form-bg: {{VALUE}};',
						),
					),
					'requires'    => array(
						'display' => array(
							'comparison' => 'not_equal',
							'value'      => array( 'full-screen', 'dropdown' ),
						),
					),
					'extra_class' => 'xts-col-6',
				),
				// Icon.
				'style'                  => array(
					'id'          => 'style',
					'title'       => esc_html__( 'Icon display', 'woodmart' ),
					'type'        => 'selector',
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Icon', 'woodmart' ),
					'value'       => 'icon',
					'options'     => array(
						'icon' => array(
							'value' => 'icon',
							'label' => esc_html__( 'Icon', 'woodmart' ),
						),
						'text' => array(
							'value' => 'text',
							'label' => esc_html__( 'Icon with text', 'woodmart' ),
						),
					),
					'description' => esc_html__( 'You can show the icon only or display "Search" text too.', 'woodmart' ),
					'requires'    => array(
						'display' => array(
							'comparison' => 'not_equal',
							'value'      => array( 'full-screen-2', 'form' ),
						),
					),
				),
				'icon_design'            => array(
					'id'       => 'icon_design',
					'title'    => esc_html__( 'Icon design', 'woodmart' ),
					'type'     => 'selector',
					'tab'      => esc_html__( 'Style', 'woodmart' ),
					'group'    => esc_html__( 'Icon', 'woodmart' ),
					'value'    => '1',
					'options'  => array(
						'1' => array(
							'value' => '1',
							'label' => esc_html__( 'First', 'woodmart' ),
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/search-icons/first.jpg',
						),
						'6' => array(
							'value' => '6',
							'label' => esc_html__( 'Second', 'woodmart' ),
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/search-icons/second.jpg',
						),
						'7' => array(
							'value' => '7',
							'label' => esc_html__( 'Third', 'woodmart' ),
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/search-icons/third.jpg',
						),
						'8' => array(
							'value' => '8',
							'label' => esc_html__( 'Fourth', 'woodmart' ),
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/search-icons/fourth.jpg',
						),
					),
					'requires' => array(
						'display' => array(
							'comparison' => 'not_equal',
							'value'      => array( 'full-screen-2', 'form' ),
						),
					),
				),
				'wrap_type'              => array(
					'id'       => 'wrap_type',
					'title'    => esc_html__( 'Background wrap type', 'woodmart' ),
					'type'     => 'selector',
					'tab'      => esc_html__( 'Style', 'woodmart' ),
					'group'    => esc_html__( 'Icon', 'woodmart' ),
					'value'    => 'icon_only',
					'options'  => array(
						'icon_only'     => array(
							'value' => 'icon_only',
							'label' => esc_html__( 'Icon only', 'woodmart' ),
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/bg-wrap-type/search-wrap-icon.jpg',
						),
						'icon_and_text' => array(
							'value' => 'icon_and_text',
							'label' => esc_html__( 'Icon and text', 'woodmart' ),
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/bg-wrap-type/search-wrap-icon-and-text.jpg',
						),
					),
					'requires' => array(
						'display'     => array(
							'comparison' => 'not_equal',
							'value'      => array( 'full-screen-2', 'form' ),
						),
						'style'       => array(
							'comparison' => 'equal',
							'value'      => 'text',
						),
						'icon_design' => array(
							'comparison' => 'equal',
							'value'      => array( '6', '7' ),
						),
					),
				),
				'color'                  => array(
					'id'          => 'color',
					'title'       => esc_html__( 'Color', 'woodmart' ),
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Icon', 'woodmart' ),
					'type'        => 'color',
					'value'       => '',
					'selectors'   => array(
						'whb-row .{{WRAPPER}}.wd-tools-element .wd-tools-inner, .whb-row .{{WRAPPER}}.wd-tools-element > a > .wd-tools-icon' => array(
							'color: {{VALUE}};',
						),
					),
					'requires'    => array(
						'display'     => array(
							'comparison' => 'not_equal',
							'value'      => array( 'full-screen-2', 'form' ),
						),
						'icon_design' => array(
							'comparison' => 'equal',
							'value'      => array( '7', '8' ),
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'hover_color'            => array(
					'id'          => 'hover_color',
					'title'       => esc_html__( 'Hover color', 'woodmart' ),
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Icon', 'woodmart' ),
					'type'        => 'color',
					'value'       => '',
					'selectors'   => array(
						'whb-row .{{WRAPPER}}.wd-tools-element:hover .wd-tools-inner, .whb-row .{{WRAPPER}}.wd-tools-element:hover > a > .wd-tools-icon' => array(
							'color: {{VALUE}};',
						),
					),
					'requires'    => array(
						'display'     => array(
							'comparison' => 'not_equal',
							'value'      => array( 'full-screen-2', 'form' ),
						),
						'icon_design' => array(
							'comparison' => 'equal',
							'value'      => array( '7', '8' ),
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'bg_color'               => array(
					'id'          => 'bg_color',
					'title'       => esc_html__( 'Background color', 'woodmart' ),
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Icon', 'woodmart' ),
					'type'        => 'color',
					'value'       => '',
					'selectors'   => array(
						'whb-row .{{WRAPPER}}.wd-tools-element .wd-tools-inner, .whb-row .{{WRAPPER}}.wd-tools-element > a > .wd-tools-icon' => array(
							'background-color: {{VALUE}};',
						),
					),
					'requires'    => array(
						'display'     => array(
							'comparison' => 'not_equal',
							'value'      => array( 'full-screen-2', 'form' ),
						),
						'icon_design' => array(
							'comparison' => 'equal',
							'value'      => array( '7', '8' ),
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'bg_hover_color'         => array(
					'id'          => 'bg_hover_color',
					'title'       => esc_html__( 'Hover background color', 'woodmart' ),
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Icon', 'woodmart' ),
					'type'        => 'color',
					'value'       => '',
					'selectors'   => array(
						'whb-row .{{WRAPPER}}.wd-tools-element:hover .wd-tools-inner, .whb-row .{{WRAPPER}}.wd-tools-element:hover > a > .wd-tools-icon' => array(
							'background-color: {{VALUE}};',
						),
					),
					'requires'    => array(
						'display'     => array(
							'comparison' => 'not_equal',
							'value'      => array( 'full-screen-2', 'form' ),
						),
						'icon_design' => array(
							'comparison' => 'equal',
							'value'      => array( '7', '8' ),
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'icon_color'             => array(
					'id'          => 'icon_color',
					'title'       => esc_html__( 'Icon color', 'woodmart' ),
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Icon', 'woodmart' ),
					'type'        => 'color',
					'value'       => '',
					'selectors'   => array(
						'{{WRAPPER}}.wd-tools-element.wd-design-8 .wd-tools-icon' => array(
							'color: {{VALUE}};',
						),
					),
					'requires'    => array(
						'display'     => array(
							'comparison' => 'not_equal',
							'value'      => array( 'full-screen-2', 'form' ),
						),
						'icon_design' => array(
							'comparison' => 'equal',
							'value'      => '8',
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'icon_hover_color'       => array(
					'id'          => 'icon_hover_color',
					'title'       => esc_html__( 'Hover icon color', 'woodmart' ),
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Icon', 'woodmart' ),
					'type'        => 'color',
					'value'       => '',
					'selectors'   => array(
						'{{WRAPPER}}.wd-tools-element.wd-design-8:hover .wd-tools-icon' => array(
							'color: {{VALUE}};',
						),
					),
					'requires'    => array(
						'display'     => array(
							'comparison' => 'not_equal',
							'value'      => array( 'full-screen-2', 'form' ),
						),
						'icon_design' => array(
							'comparison' => 'equal',
							'value'      => '8',
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'icon_bg_color'          => array(
					'id'          => 'icon_bg_color',
					'title'       => esc_html__( 'Icon background color', 'woodmart' ),
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Icon', 'woodmart' ),
					'type'        => 'color',
					'value'       => '',
					'selectors'   => array(
						'{{WRAPPER}}.wd-tools-element.wd-design-8 .wd-tools-icon' => array(
							'background-color: {{VALUE}};',
						),
					),
					'requires'    => array(
						'display'     => array(
							'comparison' => 'not_equal',
							'value'      => array( 'full-screen-2', 'form' ),
						),
						'icon_design' => array(
							'comparison' => 'equal',
							'value'      => '8',
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'icon_bg_hover_color'    => array(
					'id'          => 'icon_bg_hover_color',
					'title'       => esc_html__( 'Hover icon background color', 'woodmart' ),
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Icon', 'woodmart' ),
					'type'        => 'color',
					'value'       => '',
					'selectors'   => array(
						'{{WRAPPER}}.wd-tools-element.wd-design-8:hover .wd-tools-icon' => array(
							'background-color: {{VALUE}};',
						),
					),
					'requires'    => array(
						'display'     => array(
							'comparison' => 'not_equal',
							'value'      => array( 'full-screen-2', 'form' ),
						),
						'icon_design' => array(
							'comparison' => 'equal',
							'value'      => '8',
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'icon_type'              => array(
					'id'      => 'icon_type',
					'title'   => esc_html__( 'Icon type', 'woodmart' ),
					'type'    => 'selector',
					'tab'     => esc_html__( 'Style', 'woodmart' ),
					'group'   => esc_html__( 'Icon', 'woodmart' ),
					'value'   => 'default',
					'options' => array(
						'default' => array(
							'value' => 'default',
							'label' => esc_html__( 'Default', 'woodmart' ),
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/default-icons/search-default.jpg',
						),
						'custom'  => array(
							'value' => 'custom',
							'label' => esc_html__( 'Custom', 'woodmart' ),
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/upload.jpg',
						),
					),
				),
				'custom_icon'            => array(
					'id'          => 'custom_icon',
					'title'       => esc_html__( 'Upload an image', 'woodmart' ),
					'type'        => 'image',
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Icon', 'woodmart' ),
					'value'       => '',
					'description' => '',
					'requires'    => array(
						'icon_type' => array(
							'comparison' => 'equal',
							'value'      => 'custom',
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'custom_icon_width'      => array(
					'id'          => 'custom_icon_width',
					'title'       => esc_html__( 'Icon width', 'woodmart' ),
					'type'        => 'slider',
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Icon', 'woodmart' ),
					'from'        => 0,
					'to'          => 60,
					'value'       => 0,
					'units'       => 'px',
					'selectors'   => array(
						'{{WRAPPER}}' => array(
							'--wd-tools-icon-width: {{VALUE}}px;',
						),
					),
					'requires'    => array(
						'icon_type' => array(
							'comparison' => 'equal',
							'value'      => 'custom',
						),
					),
					'extra_class' => 'xts-col-6',
				),
			),
		);
	}

	public function get_extra_class( $args ) {
		$params      = $args['params'];
		$extra_class = '';

		if ( in_array( $params['display'], array( 'icon', 'dropdown', 'full-screen' ), true ) ) {
			$icon_type = $params['icon_type'];

			if ( 'custom' === $icon_type ) {
				$extra_class .= ' wd-tools-custom-icon';
			}

			if ( ! empty( $params['icon_design'] ) ) {
				$extra_class .= ' wd-design-' . $params['icon_design'];
			}

			if ( ! empty( $params['style'] ) ) {
				$extra_class .= ' wd-style-' . $params['style'];
			}

			if ( isset( $params['wrap_type'], $params['style'], $params['icon_design'] ) && 'icon_and_text' === $params['wrap_type'] && 'text' === $params['style'] && in_array( $params['icon_design'], array( '6', '7' ), true ) ) {
				$extra_class .= ' wd-with-wrap';
			}

			$extra_class .= ' wd-display-' . $params['display'];

			if ( isset( $args['id'] ) ) {
				$extra_class .= ' whb-' . $args['id'];
			}

			if ( 'dropdown' === $params['display'] ) {
				$extra_class .= ' wd-event-hover';

				if ( ! empty( $params['bg_overlay'] ) ) {
					$extra_class .= ' wd-with-overlay';
				}
			}

			$extra_class .= woodmart_get_old_classes( ' search-button' );
		}

		return $extra_class;
	}

	public function should_wrap_icon( $args ) {
		$params       = $args['params'];
		$icon_wrapper = false;

		if ( in_array( $params['display'], array( 'dropdown', 'full-screen', 'icon' ), true ) ) {
			if ( '8' === $params['icon_design'] ) {
				$icon_wrapper = true;
			} elseif (
				isset( $params['wrap_type'], $params['style'], $params['icon_design'] ) &&
				'icon_and_text' === $params['wrap_type'] &&
				'text' === $params['style'] &&
				in_array( $params['icon_design'], array( '6', '7' ), true )
			) {
				$icon_wrapper = true;
			}
		}

		return $icon_wrapper;
	}

	public function get_wrapper_classes( $args ) {
		$params          = $args['params'];
		$wrapper_classes = '';

		if ( in_array( $params['display'], array( 'form', 'full-screen-2' ), true ) ) {
			$wrapper_classes .= 'wd-header-search-form';
			$wrapper_classes .= ' wd-display-' . $params['display'];

			if ( isset( $args['id'] ) ) {
				$wrapper_classes .= ' whb-' . $args['id'];
			}

			if ( 'form' === $params['display'] && ! empty( $params['bg_overlay'] ) ) {
				$wrapper_classes .= ' wd-with-overlay';
			}
		}

		return $wrapper_classes;
	}

	public function get_dropdown_search_args( $args ) {
		$params      = $args['params'];
		$search_args = array();

		if ( 'full-screen' !== $params['display'] ) {
			$search_args = array(
				'count'              => isset( $params['ajax_result_count'] ) ? $params['ajax_result_count'] : 20,
				'include_cat_search' => isset( $params['include_cat_search'] ) ? $params['include_cat_search'] : false,
				'post_type'          => isset( $params['post_type'] ) ? $params['post_type'] : 'product',
				'icon_type'          => isset( $params['icon_type'] ) ? $params['icon_type'] : '',
				'custom_icon'        => isset( $params['custom_icon'] ) ? $params['custom_icon'] : '',
				'search_style'       => 'default',
				'cat_selector_style' => isset( $params['cat_selector_style'] ) ? $params['cat_selector_style'] : 'bordered',
				'wrapper_classes'    => $this->get_wrapper_classes( $args ),
			);

			if ( 'full-screen-2' === $params['display'] ) {
				$search_args['cat_selector_style'] = '';
			}

			if ( 'form' === $params['display'] ) {
				$search_args['show_categories'] = isset( $params['categories_dropdown'] ) ? $params['categories_dropdown'] : false;
			}

			if ( 'form' === $params['display'] || 'full-screen-2' === $params['display'] ) {
				$search_args['search_style'] = isset( $params['search_style'] ) ? $params['search_style'] : 'default';
			}

			if ( 'dropdown' === $params['display'] ) {
				$search_args['type'] = 'dropdown';
			}

			if ( 'form' === $params['display'] || 'dropdown' === $params['display'] ) {
				$search_extra_content = 'disable';

				if ( ! empty( $params['search_extra_content_enabled'] ) ) {
					$search_extra_content = ! empty( $params['search_extra_content'] ) ? $params['search_extra_content'] : 'inherit';
				}

				$search_args['ajax']                   = isset( $params['ajax'] ) ? $params['ajax'] : true;
				$search_args['popular_requests']       = isset( $params['popular_requests'] ) ? $params['popular_requests'] : false;
				$search_args['search_history_enabled'] = isset( $params['search_history_enabled'] ) ? $params['search_history_enabled'] : false;
				$search_args['search_extra_content']   = $search_extra_content;
			}
		}

		return $search_args;
	}

	public function get_full_screen_search_args( $args ) {
		$params      = $args['params'];
		$search_args = array();

		if ( 'full-screen' !== $params['display'] && 'full-screen-2' !== $params['display'] ) {
			return array();
		}

		$search_extra_content = 'disable';

		if ( ! empty( $params['search_extra_content_enabled'] ) ) {
			$search_extra_content = ! empty( $params['search_extra_content'] ) ? $params['search_extra_content'] : 'inherit';
		}

		$search_args['type']                   = isset( $params['display'] ) ? $params['display'] : 'full-screen';
		$search_args['popular_requests']       = isset( $params['popular_requests'] ) ? $params['popular_requests'] : '';
		$search_args['search_history_enabled'] = isset( $params['search_history_enabled'] ) ? $params['search_history_enabled'] : false;
		$search_args['search_extra_content']   = $search_extra_content;
		$search_args['post_type']              = isset( $params['post_type'] ) ? $params['post_type'] : 'product';
		$search_args['ajax']                   = isset( $params['ajax'] ) ? $params['ajax'] : true;
		$search_args['include_cat_search']     = isset( $params['include_cat_search'] ) ? $params['include_cat_search'] : false;
		$search_args['count']                  = ( isset( $params['ajax_result_count'] ) && $params['ajax_result_count'] ) ? $params['ajax_result_count'] : 20;

		if ( 'full-screen-2' === $params['display'] ) {
			$search_args['show_categories']    = isset( $params['categories_dropdown'] ) ? $params['categories_dropdown'] : '';
			$search_args['cat_selector_style'] = isset( $params['cat_selector_style'] ) ? $params['cat_selector_style'] : '';
		}

		$search_args['device'] = 'desktop';

		return $search_args;
	}

	protected function parse_args( $el ) {
		$args   = parent::parse_args( $el );
		$params = $args['params'];

		$args['params']['extra_class']             = $this->get_extra_class( $args );
		$args['params']['icon_wrapper']            = $this->should_wrap_icon( $args );
		$args['params']['dropdown_search_args']    = $this->get_dropdown_search_args( $args );
		$args['params']['full_screen_search_args'] = $this->get_full_screen_search_args( $args );

		return $args;
	}
}
