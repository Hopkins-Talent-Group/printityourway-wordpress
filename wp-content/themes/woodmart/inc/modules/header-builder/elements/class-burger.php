<?php

namespace XTS\Modules\Header_Builder\Elements;

use XTS\Modules\Header_Builder\Element;

/**
 * ------------------------------------------------------------------------------------------------
 * Mobile menu burger icon
 * ------------------------------------------------------------------------------------------------
 */
class Burger extends Element {

	public function __construct() {
		parent::__construct();
		$this->template_name = 'burger';
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
			'type'            => 'burger',
			'title'           => esc_html__( 'Mobile menu', 'woodmart' ),
			'text'            => esc_html__( 'Mobile burger icon', 'woodmart' ),
			'icon'            => 'xts-i-burger-circle',
			'editable'        => true,
			'container'       => false,
			'edit_on_create'  => true,
			'drag_target_for' => array(),
			'drag_source'     => 'content_element',
			'removable'       => true,
			'addable'         => true,
			'params'          => array(
				// Elements.
				'close_btn'                    => array(
					'id'    => 'close_btn',
					'title' => esc_html__( 'Show close button', 'woodmart' ),
					'hint'  => '<video src="' . WOODMART_TOOLTIP_URL . 'hb_mobile_menu_close_btn.mp4" autoplay loop muted></video>',
					'type'  => 'switcher',
					'tab'   => esc_html__( 'General', 'woodmart' ),
					'group' => esc_html__( 'Elements', 'woodmart' ),
					'value' => false,
				),
				'show_wishlist'                => array(
					'id'    => 'show_wishlist',
					'title' => esc_html__( 'Show wishlist', 'woodmart' ),
					'hint'  => '<video src="' . WOODMART_TOOLTIP_URL . 'hb_mobile_menu_show_wishlist.mp4" autoplay loop muted></video>',
					'type'  => 'switcher',
					'tab'   => esc_html__( 'General', 'woodmart' ),
					'group' => esc_html__( 'Elements', 'woodmart' ),
					'value' => true,
				),
				'show_compare'                 => array(
					'id'    => 'show_compare',
					'title' => esc_html__( 'Show compare', 'woodmart' ),
					'hint'  => '<video src="' . WOODMART_TOOLTIP_URL . 'hb_mobile_menu_show_compare.mp4" autoplay loop muted></video>',
					'type'  => 'switcher',
					'tab'   => esc_html__( 'General', 'woodmart' ),
					'group' => esc_html__( 'Elements', 'woodmart' ),
					'value' => true,
				),
				'show_account'                 => array(
					'id'    => 'show_account',
					'title' => esc_html__( 'Show account', 'woodmart' ),
					'hint'  => '<video src="' . WOODMART_TOOLTIP_URL . 'hb_mobile_menu_show_account.mp4" autoplay loop muted></video>',
					'type'  => 'switcher',
					'tab'   => esc_html__( 'General', 'woodmart' ),
					'group' => esc_html__( 'Elements', 'woodmart' ),
					'value' => true,
				),
				'show_html_block'              => array(
					'id'          => 'show_html_block',
					'title'       => esc_html__( 'Show HTML Blocks', 'woodmart' ),
					'hint'        => '<img src="' . WOODMART_TOOLTIP_URL . 'hb_show_html_block.jpg" alt="">',
					'type'        => 'switcher',
					'tab'         => esc_html__( 'General', 'woodmart' ),
					'group'       => esc_html__( 'Elements', 'woodmart' ),
					'description' => esc_html__( 'HTML Blocks that were assigned to the menu items will be shown as items submenus.', 'woodmart' ),
					'value'       => false,
				),
				'languages'                    => array(
					'id'          => 'languages',
					'title'       => esc_html__( 'Show WPML languages', 'woodmart' ),
					'hint'        => '<video src="' . WOODMART_TOOLTIP_URL . 'hb_mobile_menu_wpml.mp4" autoplay loop muted></video>',
					'type'        => 'switcher',
					'tab'         => esc_html__( 'General', 'woodmart' ),
					'group'       => esc_html__( 'Elements', 'woodmart' ),
					'value'       => false,
					'description' => esc_html__( 'Show the language switcher if the WPML plugin is enabled.', 'woodmart' ),
					'extra_class' => 'xts-col-6',
				),
				'show_language_flag'           => array(
					'id'          => 'show_language_flag',
					'title'       => esc_html__( 'Show flag of WPML languages', 'woodmart' ),
					'type'        => 'switcher',
					'tab'         => esc_html__( 'General', 'woodmart' ),
					'group'       => esc_html__( 'Elements', 'woodmart' ),
					'value'       => true,
					'requires'    => array(
						'languages' => array(
							'comparison' => 'equal',
							'value'      => true,
						),
					),
					'extra_class' => 'xts-col-6',
				),
				// Search form.
				'search_form'                  => array(
					'id'    => 'search_form',
					'title' => esc_html__( 'Show search form', 'woodmart' ),
					'hint'  => '<video src="' . WOODMART_TOOLTIP_URL . 'hb_mobile_menu_search_form.mp4" autoplay loop muted></video>',
					'type'  => 'switcher',
					'tab'   => esc_html__( 'General', 'woodmart' ),
					'group' => esc_html__( 'Search form', 'woodmart' ),
					'value' => true,
				),
				'popular_requests'             => array(
					'id'          => 'popular_requests',
					'title'       => esc_html__( 'Show popular requests', 'woodmart' ),
					'hint'        => '<video src="' . WOODMART_TOOLTIP_URL . 'hb_search_display_popular_requests.mp4" autoplay loop muted></video>',
					'type'        => 'switcher',
					'description' => __( 'You can write a list of popular requests in Theme Settings -> General -> Search', 'woodmart' ),
					'tab'         => esc_html__( 'General', 'woodmart' ),
					'group'       => esc_html__( 'Search form', 'woodmart' ),
					'value'       => false,
					'requires'    => array(
						'search_form' => array(
							'comparison' => 'equal',
							'value'      => true,
						),
					),
				),
				'search_history_enabled'       => array(
					'id'          => 'search_history_enabled',
					'title'       => esc_html__( 'Show search history', 'woodmart' ),
					'hint'        => '<video src="' . WOODMART_TOOLTIP_URL . 'search-history-enabled.mp4" autoplay loop muted></video>',
					'description' => esc_html__( 'Allowing users to quickly access their previous searches.', 'woodmart' ),
					'type'        => 'switcher',
					'tab'         => esc_html__( 'General', 'woodmart' ),
					'group'       => esc_html__( 'Search form', 'woodmart' ),
					'on-text'     => esc_html__( 'Yes', 'woodmart' ),
					'off-text'    => esc_html__( 'No', 'woodmart' ),
					'value'       => false,
					'requires'    => array(
						'search_form' => array(
							'comparison' => 'equal',
							'value'      => true,
						),
					),
				),
				'search_extra_content_enabled' => array(
					'id'       => 'search_extra_content_enabled',
					'title'    => esc_html__( 'Search extra content', 'woodmart' ),
					'hint'     => '<video src="' . WOODMART_TOOLTIP_URL . 'full-screen-search-extra-content.mp4" autoplay loop muted></video>',
					'tab'      => esc_html__( 'General', 'woodmart' ),
					'group'    => esc_html__( 'Search form', 'woodmart' ),
					'type'     => 'switcher',
					'value'    => false,
					'requires' => array(
						'search_form' => array(
							'comparison' => 'equal',
							'value'      => true,
						),
					),
				),
				'search_extra_content'         => array(
					'id'          => 'search_extra_content',
					'type'        => 'select',
					'tab'         => esc_html__( 'General', 'woodmart' ),
					'group'       => esc_html__( 'Search form', 'woodmart' ),
					'value'       => '',
					'options'     => $search_extra_content_options,
					'description' => $search_extra_content_description,
					'requires'    => array(
						'search_form'                  => array(
							'comparison' => 'equal',
							'value'      => true,
						),
						'search_extra_content_enabled' => array(
							'comparison' => 'equal',
							'value'      => true,
						),
					),
				),
				'ajax'                         => array(
					'id'          => 'ajax',
					'title'       => esc_html__( 'Search with AJAX', 'woodmart' ),
					'description' => esc_html__( 'Enable instant AJAX search functionality for this form.', 'woodmart' ),
					'hint'        => '<video src="' . WOODMART_TOOLTIP_URL . 'hb_search_ajax.mp4" autoplay loop muted></video>',
					'type'        => 'switcher',
					'tab'         => esc_html__( 'General', 'woodmart' ),
					'group'       => esc_html__( 'Search form', 'woodmart' ),
					'value'       => true,
					'requires'    => array(
						'search_form' => array(
							'comparison' => 'equal',
							'value'      => true,
						),
					),
				),
				'ajax_result_count'            => array(
					'id'          => 'ajax_result_count',
					'title'       => esc_html__( 'AJAX search results count', 'woodmart' ),
					'description' => esc_html__( 'Number of products to display in AJAX search results.', 'woodmart' ),
					'type'        => 'slider',
					'tab'         => esc_html__( 'General', 'woodmart' ),
					'group'       => esc_html__( 'Search form', 'woodmart' ),
					'from'        => 3,
					'to'          => 50,
					'value'       => 20,
					'units'       => '',
					'requires'    => array(
						'search_form' => array(
							'comparison' => 'equal',
							'value'      => true,
						),
						'ajax'        => array(
							'comparison' => 'equal',
							'value'      => true,
						),
					),
				),
				'post_type'                    => array(
					'id'       => 'post_type',
					'title'    => esc_html__( 'Post type', 'woodmart' ),
					'type'     => 'selector',
					'tab'      => esc_html__( 'General', 'woodmart' ),
					'group'    => esc_html__( 'Search form', 'woodmart' ),
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
						'search_form' => array(
							'comparison' => 'equal',
							'value'      => true,
						),
					),
				),
				'include_cat_search'           => array(
					'id'          => 'include_cat_search',
					'title'       => esc_html__( 'Include categories in search', 'woodmart' ),
					'hint'        => '<video src="' . WOODMART_TOOLTIP_URL . 'include-cat-search.mp4" autoplay loop muted></video>',
					'description' => esc_html__( 'When enabled, the search function will also look for and display categories that match the search query.', 'woodmart' ),
					'type'        => 'switcher',
					'tab'         => esc_html__( 'General', 'woodmart' ),
					'group'       => esc_html__( 'Search form', 'woodmart' ),
					'value'       => false,
					'requires'    => array(
						'search_form' => array(
							'comparison' => 'equal',
							'value'      => true,
						),
						'ajax'        => array(
							'comparison' => 'equal',
							'value'      => true,
						),
						'post_type'   => array(
							'comparison' => 'equal',
							'value'      => 'product',
						),
					),
				),
				// Category.
				'categories_menu'              => array(
					'id'    => 'categories_menu',
					'title' => esc_html__( 'Show categories menu', 'woodmart' ),
					'hint'  => '<video src="' . WOODMART_TOOLTIP_URL . 'hb_mobile_menu_categories_menu.mp4" autoplay loop muted></video>',
					'type'  => 'switcher',
					'tab'   => esc_html__( 'General', 'woodmart' ),
					'group' => esc_html__( 'Category', 'woodmart' ),
					'value' => false,
				),
				'primary_menu_title'           => array(
					'id'          => 'primary_menu_title',
					'title'       => esc_html__( 'First menu tab title', 'woodmart' ),
					'type'        => 'text',
					'tab'         => esc_html__( 'General', 'woodmart' ),
					'group'       => esc_html__( 'Category', 'woodmart' ),
					'value'       => '',
					'description' => esc_html__( 'You can rewrite mobile menu tab title with this option. Or leave empty to have a default one - Menu.', 'woodmart' ),
					'requires'    => array(
						'categories_menu' => array(
							'comparison' => 'equal',
							'value'      => true,
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'secondary_menu_title'         => array(
					'id'          => 'secondary_menu_title',
					'title'       => esc_html__( 'Second menu tab title', 'woodmart' ),
					'type'        => 'text',
					'tab'         => esc_html__( 'General', 'woodmart' ),
					'group'       => esc_html__( 'Category', 'woodmart' ),
					'value'       => '',
					'description' => esc_html__( 'You can rewrite mobile menu tab title with this option. Or leave empty to have a default one - Categories.', 'woodmart' ),
					'requires'    => array(
						'categories_menu' => array(
							'comparison' => 'equal',
							'value'      => true,
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'menu_id'                      => array(
					'id'          => 'menu_id',
					'title'       => esc_html__( 'Choose menu', 'woodmart' ),
					'type'        => 'select',
					'tab'         => esc_html__( 'General', 'woodmart' ),
					'group'       => esc_html__( 'Category', 'woodmart' ),
					'value'       => '',
					'callback'    => 'get_menu_options_with_empty',
					'description' => esc_html__( 'Choose which menu to display.', 'woodmart' ),
					'requires'    => array(
						'categories_menu' => array(
							'comparison' => 'equal',
							'value'      => true,
						),
					),
				),
				'tabs_swap'                    => array(
					'id'          => 'tabs_swap',
					'title'       => esc_html__( 'Swap menus', 'woodmart' ),
					'type'        => 'switcher',
					'tab'         => esc_html__( 'General', 'woodmart' ),
					'group'       => esc_html__( 'Category', 'woodmart' ),
					'value'       => false,
					'description' => esc_html__( 'Swap the positions of the first and secondary menus.', 'woodmart' ),
					'requires'    => array(
						'categories_menu' => array(
							'comparison' => 'equal',
							'value'      => true,
						),
					),
				),
				// General.
				'menu_layout'                  => array(
					'id'          => 'menu_layout',
					'title'       => esc_html__( 'Menu layout', 'woodmart' ),
					'type'        => 'selector',
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'General', 'woodmart' ),
					'value'       => 'dropdown',
					'options'     => array(
						'dropdown'  => array(
							'value' => 'dropdown',
							'label' => esc_html__( 'Dropdown', 'woodmart' ),
							'hint'  => '<video src="' . WOODMART_TOOLTIP_URL . 'hb_mobile_menu_menu_layout_dropdown.mp4" autoplay loop muted></video>',
						),
						'drilldown' => array(
							'value' => 'drilldown',
							'label' => esc_html__( 'Drilldown', 'woodmart' ),
							'hint'  => '<video src="' . WOODMART_TOOLTIP_URL . 'hb_mobile_menu_menu_layout_drilldown.mp4" autoplay loop muted></video>',
						),
					),
					'description' => esc_html__( 'Change the layout of the submenus of the mobile menus.', 'woodmart' ),
				),
				'drilldown_animation'          => array(
					'id'          => 'drilldown_animation',
					'title'       => esc_html__( 'Drilldown animation', 'woodmart' ),
					'type'        => 'selector',
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'General', 'woodmart' ),
					'value'       => 'slide',
					'options'     => array(
						'slide'   => array(
							'value' => 'slide',
							'label' => esc_html__( 'Slide', 'woodmart' ),
							'hint'  => '<video src="' . WOODMART_TOOLTIP_URL . 'hb_mobile_menu_menu_drilldown_animation_slide.mp4" autoplay loop muted></video>',
						),
						'fade-in' => array(
							'value' => 'fade-in',
							'label' => esc_html__( 'Fade in', 'woodmart' ),
							'hint'  => '<video src="' . WOODMART_TOOLTIP_URL . 'hb_mobile_menu_menu_drilldown_animation_fade_in.mp4" autoplay loop muted></video>',
						),
					),
					'requires'    => array(
						'menu_layout' => array(
							'comparison' => 'equal',
							'value'      => 'drilldown',
						),
					),
					'description' => esc_html__( 'Change the navigation animation through the drilldown menu.', 'woodmart' ),
				),
				'submenu_opening_action'       => array(
					'id'          => 'submenu_opening_action',
					'title'       => esc_html__( 'Submenu opening action', 'woodmart' ),
					'type'        => 'selector',
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'General', 'woodmart' ),
					'value'       => 'only_arrow',
					'options'     => array(
						'only_arrow'     => array(
							'value' => 'arrow',
							'label' => esc_html__( 'Arrow', 'woodmart' ),
							'hint'  => '<video src="' . WOODMART_TOOLTIP_URL . 'hb_mobile_menu_submenu_opening_arrow.mp4" autoplay loop muted></video>',
						),
						'item_and_arrow' => array(
							'value' => 'item_and_arrow',
							'label' => esc_html__( 'Label and arrow', 'woodmart' ),
							'hint'  => '<video src="' . WOODMART_TOOLTIP_URL . 'hb_mobile_menu_submenu_opening_item_and_arrow.mp4" autoplay loop muted></video>',
						),
					),
					'description' => esc_html__( 'Specify which parent menu element needs to be clicked to open the submenu.', 'woodmart' ),
				),
				'position'                     => array(
					'id'          => 'position',
					'title'       => esc_html__( 'Position', 'woodmart' ),
					'type'        => 'selector',
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'General', 'woodmart' ),
					'value'       => 'left',
					'options'     => array(
						'left'  => array(
							'value' => 'left',
							'hint'  => '<img src="' . WOODMART_TOOLTIP_URL . 'hb_mobile_menu_position_left.jpg" alt="">',
							'label' => esc_html__( 'Left', 'woodmart' ),
						),
						'right' => array(
							'value' => 'right',
							'hint'  => '<img src="' . WOODMART_TOOLTIP_URL . 'hb_mobile_menu_position_right.jpg" alt="">',
							'label' => esc_html__( 'Right', 'woodmart' ),
						),
					),
					'description' => esc_html__( 'Position of the mobile menu sidebar.', 'woodmart' ),
				),
				// Icon.
				'style'                        => array(
					'id'          => 'style',
					'title'       => esc_html__( 'Display', 'woodmart' ),
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
					'description' => esc_html__( 'You can show the icon only or display "Menu" text too.', 'woodmart' ),
				),
				'icon_design'                  => array(
					'id'      => 'icon_design',
					'title'   => esc_html__( 'Icon design', 'woodmart' ),
					'type'    => 'selector',
					'tab'     => esc_html__( 'Style', 'woodmart' ),
					'group'   => esc_html__( 'Icon', 'woodmart' ),
					'value'   => '1',
					'options' => array(
						'1' => array(
							'value' => '1',
							'label' => esc_html__( 'First', 'woodmart' ),
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/mobile-menu-icons/first.jpg',
						),
						'6' => array(
							'value' => '6',
							'label' => esc_html__( 'Second', 'woodmart' ),
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/mobile-menu-icons/second.jpg',
						),
						'7' => array(
							'value' => '7',
							'label' => esc_html__( 'Third', 'woodmart' ),
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/mobile-menu-icons/third.jpg',
						),
						'8' => array(
							'value' => '8',
							'label' => esc_html__( 'Fourth', 'woodmart' ),
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/mobile-menu-icons/fourth.jpg',
						),
					),
				),
				'wrap_type'                    => array(
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
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/bg-wrap-type/menu-wrap-icon.jpg',
						),
						'icon_and_text' => array(
							'value' => 'icon_and_text',
							'label' => esc_html__( 'Icon and text', 'woodmart' ),
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/bg-wrap-type/menu-wrap-icon-and-text.jpg',
						),
					),
					'requires' => array(
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
				'color'                        => array(
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
						'icon_design' => array(
							'comparison' => 'equal',
							'value'      => array( '7', '8' ),
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'hover_color'                  => array(
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
						'icon_design' => array(
							'comparison' => 'equal',
							'value'      => array( '7', '8' ),
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'bg_color'                     => array(
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
						'icon_design' => array(
							'comparison' => 'equal',
							'value'      => array( '7', '8' ),
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'bg_hover_color'               => array(
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
						'icon_design' => array(
							'comparison' => 'equal',
							'value'      => array( '7', '8' ),
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'icon_color'                   => array(
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
						'icon_design' => array(
							'comparison' => 'equal',
							'value'      => '8',
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'icon_hover_color'             => array(
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
						'icon_design' => array(
							'comparison' => 'equal',
							'value'      => '8',
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'icon_bg_color'                => array(
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
						'icon_design' => array(
							'comparison' => 'equal',
							'value'      => '8',
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'icon_bg_hover_color'          => array(
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
						'icon_design' => array(
							'comparison' => 'equal',
							'value'      => '8',
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'icon_type'                    => array(
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
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/default-icons/burger-default.jpg',
						),
						'custom'  => array(
							'value' => 'custom',
							'label' => esc_html__( 'Custom', 'woodmart' ),
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/upload.jpg',
						),
					),
				),
				'custom_icon'                  => array(
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
				'custom_icon_width'            => array(
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
				'icon_width'                   => array(
					'id'          => 'icon_width',
					'title'       => esc_html__( 'Width', 'woodmart' ),
					'type'        => 'slider',
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Menu icon', 'woodmart' ),
					'from'        => 0,
					'to'          => 60,
					'value'       => '',
					'units'       => 'px',
					'selectors'   => array(
						'wd-nav-mobile .wd-nav-img' => array(
							'--nav-img-width: {{VALUE}}px;',
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'icon_height'                  => array(
					'id'          => 'icon_height',
					'title'       => esc_html__( 'Height', 'woodmart' ),
					'type'        => 'slider',
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Menu icon', 'woodmart' ),
					'from'        => 0,
					'to'          => 60,
					'value'       => '',
					'units'       => 'px',
					'selectors'   => array(
						'wd-nav-mobile .wd-nav-img' => array(
							'--nav-img-height: {{VALUE}}px;',
						),
					),
					'extra_class' => 'xts-col-6',
				),
			),
		);
	}
}
