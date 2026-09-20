<?php

namespace XTS\Modules\Header_Builder\Elements;

use XTS\Modules\Header_Builder\Element;

/**
 * ------------------------------------------------------------------------------------------------
 *  Search icon for mobile devices
 * ------------------------------------------------------------------------------------------------
 */
class Mobilesearch extends Search {

	public function __construct() {
		parent::__construct();
		$this->template_name = 'mobile-search';
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
			'type'            => 'mobilesearch',
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
			'mobile'          => true,
			'params'          => array(
				// General.
				'display'                => array(
					'id'          => 'display',
					'title'       => esc_html__( 'Display', 'woodmart' ),
					'type'        => 'selector',
					'tab'         => esc_html__( 'General', 'woodmart' ),
					'group'       => esc_html__( 'General', 'woodmart' ),
					'value'       => 'icon',
					'options'     => array(
						'icon'          => array(
							'value' => 'icon',
							'hint'        => '<video src="' . WOODMART_TOOLTIP_URL . 'hb-mobile-search-icon.mp4" autoplay loop muted></video>',
							'label' => esc_html__( 'Icon', 'woodmart' ),
						),
						'full-screen'   => array(
							'value' => 'full-screen',
							'hint'        => '<video src="' . WOODMART_TOOLTIP_URL . 'hb-mobile-search-full-screen.mp4" autoplay loop muted></video>',
							'label' => esc_html__( 'Full screen', 'woodmart' ),
						),
						'full-screen-2' => array(
							'value' => 'full-screen-2',
							'hint'        => '<video src="' . WOODMART_TOOLTIP_URL . 'hb-mobile-search-full-screen-2.mp4" autoplay loop muted></video>',
							'label' => esc_html__( 'Full screen 2', 'woodmart' ),
						),
						'form'          => array(
							'value' => 'form',
							'hint'        => '<video src="' . WOODMART_TOOLTIP_URL . 'hb-mobile-search-form.mp4" autoplay loop muted></video>',
							'label' => esc_html__( 'Form', 'woodmart' ),
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
					'requires'    => array(
						'display' => array(
							'comparison' => 'not_equal',
							'value'      => 'icon',
						),
					),
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
					'requires'    => array(
						'display' => array(
							'comparison' => 'not_equal',
							'value'      => 'icon',
						),
					),
				),
				'search_extra_content_enabled' => array(
					'id'       => 'search_extra_content_enabled',
					'title'    => esc_html__( 'Search extra content', 'woodmart' ),
					'hint'        => '<video src="' . WOODMART_TOOLTIP_URL . 'full-screen-search-extra-content.mp4" autoplay loop muted></video>',
					'tab'      => esc_html__( 'General', 'woodmart' ),
					'group'    => esc_html__( 'General', 'woodmart' ),
					'type'     => 'switcher',
					'value'    => false,
					'requires' => array(
						'display' => array(
							'comparison' => 'not_equal',
							'value'      => 'icon',
						),
					),
				),
				'search_extra_content'         => array(
					'id'          => 'search_extra_content',
					'type'        => 'select',
					'tab'         => esc_html__( 'General', 'woodmart' ),
					'group'       => esc_html__( 'General', 'woodmart' ),
					'value'       => '',
					'options'     => $search_extra_content_options,
					'description' => $search_extra_content_description,
					'requires'    => array(
						'display'                      => array(
							'comparison' => 'not_equal',
							'value'      => 'icon',
						),
						'search_extra_content_enabled' => array(
							'comparison' => 'equal',
							'value'      => true,
						),
					),
				),
				// Search result.
				'ajax'                   => array(
					'id'          => 'ajax',
					'title'       => esc_html__( 'Search with AJAX', 'woodmart' ),
					'description' => esc_html__( 'Enable instant AJAX search functionality for this form.', 'woodmart' ),
					'hint'        => '<video src="' . WOODMART_TOOLTIP_URL . 'hb_search_ajax.mp4" autoplay loop muted></video>',
					'type'        => 'switcher',
					'tab'         => esc_html__( 'General', 'woodmart' ),
					'group'       => esc_html__( 'Search result', 'woodmart' ),
					'value'       => true,
					'requires'    => array(
						'display' => array(
							'comparison' => 'not_equal',
							'value'      => 'icon',
						),
					),
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
						'display' => array(
							'comparison' => 'not_equal',
							'value'      => 'icon',
						),
						'ajax'    => array(
							'comparison' => 'equal',
							'value'      => true,
						),
					),
				),
				'post_type'              => array(
					'id'       => 'post_type',
					'title'    => esc_html__( 'Post type', 'woodmart' ),
					'type'     => 'selector',
					'tab'      => esc_html__( 'General', 'woodmart' ),
					'group'    => esc_html__( 'Search result', 'woodmart' ),
					'value'    => 'product',
					'options'  => array(
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
					'requires' => array(
						'display' => array(
							'comparison' => 'not_equal',
							'value'      => 'icon',
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
						'display'   => array(
							'comparison' => 'not_equal',
							'value'      => 'icon',
						),
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
							'value'      => array( 'full-screen', 'icon' ),
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
							'value'      => array( 'full-screen', 'icon' ),
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
					'value'     => 42,
					'units'     => 'px',
					'selectors' => array(
						'{{WRAPPER}} form.searchform' => array(
							'--wd-form-height: {{VALUE}}px;',
						),
					),
					'requires'  => array(
						'display' => array(
							'comparison' => 'not_equal',
							'value'      => array( 'full-screen', 'icon' ),
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
						'{{WRAPPER}}.wd-search-form.wd-header-search-form-mobile .searchform' => array(
							'--wd-form-color: {{VALUE}};',
						),
					),
					'requires'    => array(
						'display' => array(
							'comparison' => 'not_equal',
							'value'      => array( 'full-screen', 'icon' ),
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
						'{{WRAPPER}}.wd-search-form.wd-header-search-form-mobile .searchform' => array(
							'--wd-form-placeholder-color: {{VALUE}};',
						),
					),
					'requires'    => array(
						'display' => array(
							'comparison' => 'not_equal',
							'value'      => array( 'full-screen', 'icon' ),
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
						'{{WRAPPER}}.wd-search-form.wd-header-search-form-mobile .searchform' => array(
							'--wd-form-brd-color: {{VALUE}};',
						),
					),
					'requires'    => array(
						'display' => array(
							'comparison' => 'not_equal',
							'value'      => array( 'full-screen', 'icon' ),
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
						'{{WRAPPER}}.wd-search-form.wd-header-search-form-mobile .searchform' => array(
							'--wd-form-brd-color-focus: {{VALUE}};',
						),
					),
					'requires'    => array(
						'display' => array(
							'comparison' => 'not_equal',
							'value'      => array( 'full-screen', 'icon' ),
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
						'{{WRAPPER}}.wd-search-form.wd-header-search-form-mobile .searchform' => array(
							'--wd-form-bg: {{VALUE}};',
						),
					),
					'requires'    => array(
						'display' => array(
							'comparison' => 'not_equal',
							'value'      => array( 'full-screen', 'icon' ),
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

	public function get_wrapper_classes( $args ) {
		$params          = $args['params'];
		$wrapper_classes = '';

		if ( in_array( $params['display'], array( 'form', 'full-screen-2' ), true ) ) {
			$wrapper_classes .= 'wd-header-search-form-mobile';
			$wrapper_classes .= ' wd-display-' . $params['display'];

			if ( isset( $args['id'] ) ) {
				$wrapper_classes .= ' whb-' . $args['id'];
			}
		}

		return $wrapper_classes;
	}

	public function get_full_screen_search_args( $args ) {
		$params      = $args['params'];
		$search_args = array();

		if ( 'full-screen' !== $params['display'] && 'full-screen-2' !== $params['display'] ) {
			return array();
		}

		$search_args           = parent::get_full_screen_search_args( $args );
		$search_args['device'] = 'mobile';

		return $search_args;
	}
}
