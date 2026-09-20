<?php use XTS\Modules\Layouts\Main;
if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

/**
* ------------------------------------------------------------------------------------------------
* Portfolio element map
* ------------------------------------------------------------------------------------------------
*/

if ( ! function_exists( 'woodmart_get_vc_map_portfolio' ) ) {
	function woodmart_get_vc_map_portfolio() {
		$post_types_list = array(
			array( 'post', esc_html__( 'Post', 'woodmart' ) ),
			array( 'ids', esc_html__( 'List of IDs', 'woodmart' ) ),
		);

		if ( Main::is_layout_type( 'single_portfolio' ) ) {
			$post_types_list[] = array( 'related_projects', esc_html__( 'Related projects', 'woodmart' ) );
		}

		$typography = woodmart_get_typography_map(
			array(
				'key'      => 'title',
				'selector' => '{{WRAPPER}} .wd-el-title',
			)
		);

		return array(
			'name'        => esc_html__( 'Portfolio', 'woodmart' ),
			'base'        => 'woodmart_portfolio',
			'category'    => woodmart_get_tab_title_category_for_wpb( esc_html__( 'Theme elements', 'woodmart' ) ),
			'description' => esc_html__( 'Showcase your projects or gallery', 'woodmart' ),
			'icon'        => WOODMART_ASSETS . '/images/vc-icon/portfolio.svg',
			'params'      => array(
				array(
					'type'       => 'woodmart_css_id',
					'param_name' => 'woodmart_css_id',
				),

				/**
				 * Portfolio title.
				 */

				array(
					'type'       => 'woodmart_title_divider',
					'holder'     => 'div',
					'title'      => esc_html__( 'Title', 'woodmart' ),
					'param_name' => 'title_divider',
				),
				array(
					'type'       => 'textfield',
					'heading'    => esc_html__( 'Element title', 'woodmart' ),
					'param_name' => 'element_title',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => esc_html__( 'Tag', 'woodmart' ),
					'param_name'       => 'element_title_tag',
					'value'            => array(
						esc_html__( 'h1', 'woodmart' )   => 'h1',
						esc_html__( 'h2', 'woodmart' )   => 'h2',
						esc_html__( 'h3', 'woodmart' )   => 'h3',
						esc_html__( 'h4', 'woodmart' )   => 'h4',
						esc_html__( 'h5', 'woodmart' )   => 'h5',
						esc_html__( 'h6', 'woodmart' )   => 'h6',
						esc_html__( 'div', 'woodmart' )  => 'div',
						esc_html__( 'p', 'woodmart' )    => 'p',
						esc_html__( 'span', 'woodmart' ) => 'span',
					),
					'std'              => 'h4',
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'heading'          => esc_html__( 'Color', 'woodmart' ),
					'type'             => 'wd_colorpicker',
					'param_name'       => 'title_color',
					'selectors'        => array(
						'{{WRAPPER}} .wd-el-title' => array(
							'color: {{VALUE}};',
						),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				$typography['font_family'],
				$typography['font_size'],
				$typography['font_weight'],
				$typography['text_transform'],
				$typography['font_style'],
				$typography['line_height'],

				/**
				 * Layout
				 */
				array(
					'type'       => 'woodmart_title_divider',
					'holder'     => 'div',
					'title'      => esc_html__( 'Layout', 'woodmart' ),
					'param_name' => 'layout_divider',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => esc_html__( 'Layout', 'woodmart' ),
					'param_name'       => 'layout',
					'value'            => array(
						esc_html__( 'Grid', 'woodmart' ) => 'grid',
						esc_html__( 'Carousel', 'woodmart' ) => 'carousel',
					),
					'save_always'      => true,
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'woodmart_button_set',
					'heading'          => esc_html__( 'Columns', 'woodmart' ),
					'hint'             => esc_html__( 'Number of columns in the grid.', 'woodmart' ),
					'param_name'       => 'columns_tabs',
					'tabs'             => true,
					'value'            => array(
						esc_html__( 'Desktop', 'woodmart' ) => 'desktop',
						esc_html__( 'Tablet', 'woodmart' ) => 'tablet',
						esc_html__( 'Mobile', 'woodmart' ) => 'mobile',
					),
					'dependency'       => array(
						'element' => 'layout',
						'value'   => 'grid',
					),
					'default'          => 'desktop',
					'edit_field_class' => 'wd-res-control wd-custom-width vc_col-sm-12 vc_column',
				),
				array(
					'type'             => 'dropdown',
					'param_name'       => 'columns',
					'value'            => array(
						'1' => '1',
						'2' => '2',
						'3' => '3',
						'4' => '4',
						'5' => '5',
						'6' => '6',
					),
					'std'              => '3',
					'dependency'       => array(
						'element' => 'layout',
						'value'   => 'grid',
					),
					'wd_dependency'    => array(
						'element' => 'columns_tabs',
						'value'   => array( 'desktop' ),
					),
					'edit_field_class' => 'wd-res-item vc_col-sm-12 vc_column',
				),
				array(
					'type'             => 'dropdown',
					'param_name'       => 'columns_tablet',
					'value'            => array(
						esc_html__( 'Auto', 'woodmart' ) => 'auto',
						'1'                              => '1',
						'2'                              => '2',
						'3'                              => '3',
						'4'                              => '4',
						'5'                              => '5',
						'6'                              => '6',
					),
					'std'              => 'auto',
					'dependency'       => array(
						'element' => 'layout',
						'value'   => 'grid',
					),
					'wd_dependency'    => array(
						'element' => 'columns_tabs',
						'value'   => array( 'tablet' ),
					),
					'edit_field_class' => 'wd-res-item vc_col-sm-12 vc_column',
				),
				array(
					'type'             => 'dropdown',
					'param_name'       => 'columns_mobile',
					'value'            => array(
						esc_html__( 'Auto', 'woodmart' ) => 'auto',
						'1'                              => '1',
						'2'                              => '2',
						'3'                              => '3',
						'4'                              => '4',
						'5'                              => '5',
						'6'                              => '6',
					),
					'std'              => 'auto',
					'dependency'       => array(
						'element' => 'layout',
						'value'   => 'grid',
					),
					'wd_dependency'    => array(
						'element' => 'columns_tabs',
						'value'   => array( 'mobile' ),
					),
					'edit_field_class' => 'wd-res-item vc_col-sm-12 vc_column',
				),
				array(
					'type'             => 'woodmart_button_set',
					'heading'          => esc_html__( 'Space between projects', 'woodmart' ),
					'param_name'       => 'spacing_tabs',
					'tabs'             => true,
					'value'            => array(
						esc_html__( 'Desktop', 'woodmart' ) => 'desktop',
						esc_html__( 'Tablet', 'woodmart' ) => 'tablet',
						esc_html__( 'Mobile', 'woodmart' ) => 'mobile',
					),
					'default'          => 'desktop',
					'edit_field_class' => 'wd-res-control wd-custom-width vc_col-sm-12 vc_column',
				),
				array(
					'type'             => 'dropdown',
					'param_name'       => 'spacing',
					'value'            => array(
						esc_html__( 'Inherit from Theme Settings', 'woodmart' ) => '',
						0  => 0,
						2  => 2,
						6  => 6,
						10 => 10,
						20 => 20,
						30 => 30,
					),
					'std'              => '',
					'wd_dependency'    => array(
						'element' => 'spacing_tabs',
						'value'   => array( 'desktop' ),
					),
					'edit_field_class' => 'wd-res-item vc_col-sm-12 vc_column',
				),
				array(
					'type'             => 'dropdown',
					'param_name'       => 'spacing_tablet',
					'value'            => array(
						esc_html__( 'Inherit', 'woodmart' ) => '',
						0  => 0,
						2  => 2,
						6  => 6,
						10 => 10,
						20 => 20,
						30 => 30,
					),
					'std'              => '',
					'wd_dependency'    => array(
						'element' => 'spacing_tabs',
						'value'   => array( 'tablet' ),
					),
					'edit_field_class' => 'wd-res-item vc_col-sm-12 vc_column',
				),
				array(
					'type'             => 'dropdown',
					'param_name'       => 'spacing_mobile',
					'value'            => array(
						esc_html__( 'Inherit', 'woodmart' ) => '',
						0  => 0,
						2  => 2,
						6  => 6,
						10 => 10,
						20 => 20,
						30 => 30,
					),
					'std'              => '',
					'wd_dependency'    => array(
						'element' => 'spacing_tabs',
						'value'   => array( 'mobile' ),
					),
					'edit_field_class' => 'wd-res-item vc_col-sm-12 vc_column',
				),
				array(
					'type'             => 'woodmart_switch',
					'heading'          => esc_html__( 'Show categories filters', 'woodmart' ),
					'param_name'       => 'filters',
					'true_state'       => 1,
					'false_state'      => 0,
					'default'          => 0,
					'dependency'       => array(
						'element' => 'layout',
						'value'   => 'grid',
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'woodmart_button_set',
					'heading'          => esc_html__( 'Filters type', 'woodmart' ),
					'param_name'       => 'filters_type',
					'value'            => array(
						esc_html__( 'Links', 'woodmart' ) => 'links',
						esc_html__( 'Masonry', 'woodmart' ) => 'masonry',
					),
					'save_always'      => true,
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'default'          => 'masonry',
					'dependency'       => array(
						'element' => 'filters',
						'value'   => '1',
					),
				),
				/**
				 * Carousel
				 */
				array(
					'type'       => 'woodmart_title_divider',
					'holder'     => 'div',
					'title'      => esc_html__( 'Carousel', 'woodmart' ),
					'group'      => esc_html__( 'Carousel', 'woodmart' ),
					'param_name' => 'carousel_divider',
					'dependency' => array(
						'element' => 'layout',
						'value'   => array( 'carousel' ),
					),
				),
				/**
				 * Pagination
				 */
				array(
					'type'       => 'woodmart_title_divider',
					'holder'     => 'div',
					'title'      => esc_html__( 'Pagination', 'woodmart' ),
					'param_name' => 'pagination_divider',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => esc_html__( 'Pagination', 'woodmart' ),
					'param_name'       => 'pagination',
					'value'            => array(
						'' => '',
						esc_html__( 'Pagination', 'woodmart' ) => 'pagination',
						wp_kses( __( 'Load more button', 'woodmart' ), 'entities' ) => 'load_more',
						esc_html__( 'Infinit', 'woodmart' ) => 'infinit',
						esc_html__( 'Disable', 'woodmart' ) => 'disable',
					),
					'dependency'       => array(
						'element' => 'layout',
						'value'   => 'grid',
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'textfield',
					'heading'          => esc_html__( 'Number of projects per page', 'woodmart' ),
					'param_name'       => 'posts_per_page',
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				/**
				 * Design
				 */
				array(
					'type'       => 'woodmart_title_divider',
					'holder'     => 'div',
					'title'      => esc_html__( 'Design', 'woodmart' ),
					'group'      => esc_html__( 'Design', 'woodmart' ),
					'param_name' => 'extra_divider',
				),
				array(
					'type'         => 'woodmart_image_select',
					'heading'      => esc_html__( 'Style', 'woodmart' ),
					'param_name'   => 'style',
					'value'        => array(
						esc_html__( 'Inherit from Theme Settings', 'woodmart' ) => 'inherit',
						esc_html__( 'Show text on mouse over', 'woodmart' ) => 'hover',
						esc_html__( 'Alternative', 'woodmart' ) => 'hover-inverse',
						esc_html__( 'Text under image', 'woodmart' ) => 'text-shown',
						esc_html__( 'Mouse move parallax', 'woodmart' ) => 'parallax',
					),
					'group'        => esc_html__( 'Design', 'woodmart' ),
					'images_value' => array(
						'inherit'       => WOODMART_ASSETS_IMAGES . '/settings/empty.jpg',
						'hover'         => WOODMART_ASSETS_IMAGES . '/settings/portfolio/hover.jpg',
						'hover-inverse' => WOODMART_ASSETS_IMAGES . '/settings/portfolio/hover-inverse.jpg',
						'text-shown'    => WOODMART_ASSETS_IMAGES . '/settings/portfolio/text-shown.jpg',
						'parallax'      => WOODMART_ASSETS_IMAGES . '/settings/portfolio/hover.jpg',
					),
				),
				array(
					'type'             => 'textfield',
					'heading'          => esc_html__( 'Images size', 'woodmart' ),
					'group'            => esc_html__( 'Design', 'woodmart' ),
					'param_name'       => 'image_size',
					'hint'             => esc_html__( 'Enter image size. Example: \'thumbnail\', \'medium\', \'large\', \'full\' or other sizes defined by current theme. Alternatively enter image size in pixels: 200x100 (Width x Height). Leave empty to use \'thumbnail\' size.', 'woodmart' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				/**
				* Data settings
				*/
				array(
					'type'       => 'woodmart_title_divider',
					'holder'     => 'div',
					'group'      => esc_html__( 'Data Settings', 'woodmart' ),
					'title'      => esc_html__( 'Data settings', 'woodmart' ),
					'param_name' => 'data_divider',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => esc_html__( 'Data source', 'woodmart' ),
					'group'            => esc_html__( 'Data Settings', 'woodmart' ),
					'param_name'       => 'post_type',
					'value'            => $post_types_list,
					'hint'             => esc_html__( 'Select content type for your grid.', 'woodmart' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'autocomplete',
					'heading'          => esc_html__( 'Include only', 'woodmart' ),
					'group'            => esc_html__( 'Data Settings', 'woodmart' ),
					'param_name'       => 'include',
					'hint'             => esc_html__( 'Add projects in the query.', 'woodmart' ),
					'settings'         => array(
						'multiple' => true,
						'sortable' => true,
						'groups'   => true,
					),
					'dependency'       => array(
						'element' => 'post_type',
						'value'   => array( 'ids' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'woodmart_dropdown',
					'heading'          => esc_html__( 'Categories', 'woodmart' ),
					'group'            => esc_html__( 'Data Settings', 'woodmart' ),
					'param_name'       => 'categories',
					'callback'         => 'woodmart_get_projects_cats_array',
					'dependency'       => array(
						'element'            => 'post_type',
						'value_not_equal_to' => array( 'ids', 'related_projects' ),
						'callback'           => 'vc_grid_exclude_dependency_callback',
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => esc_html__( 'Order by', 'woodmart' ),
					'group'            => esc_html__( 'Data Settings', 'woodmart' ),
					'param_name'       => 'orderby',
					'value'            => array(
						'',
						esc_html__( 'Date', 'woodmart' )  => 'date',
						esc_html__( 'ID', 'woodmart' )    => 'ID',
						esc_html__( 'Title', 'woodmart' ) => 'title',
						esc_html__( 'Modified', 'woodmart' ) => 'modified',
						esc_html__( 'Menu order', 'woodmart' ) => 'menu_order',
					),
					'save_always'      => true,
					'hint'             => sprintf(
						wp_kses(
							__( 'Select how to sort retrieved projects. More at %s.', 'woodmart' ),
							array(
								'a' => array(
									'href'   => array(),
									'target' => array(),
								),
							)
						),
						'<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>'
					),
					'dependency'       => array(
						'element'            => 'post_type',
						'value_not_equal_to' => array( 'ids', 'related_projects' ),
						'callback'           => 'vc_grid_exclude_dependency_callback',
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'woodmart_button_set',
					'heading'          => esc_html__( 'Sort order', 'woodmart' ),
					'group'            => esc_html__( 'Data Settings', 'woodmart' ),
					'param_name'       => 'order',
					'value'            => array(
						esc_html__( 'Inherit', 'woodmart' ) => '',
						esc_html__( 'Descending', 'woodmart' ) => 'DESC',
						esc_html__( 'Ascending', 'woodmart' ) => 'ASC',
					),
					'save_always'      => true,
					'hint'             => sprintf(
						wp_kses(
							__( 'Designates the ascending or descending order. More at %s.', 'woodmart' ),
							array(
								'a' => array(
									'href'   => array(),
									'target' => array(),
								),
							)
						),
						'<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>'
					),
					'dependency'       => array(
						'element'            => 'post_type',
						'value_not_equal_to' => array( 'ids', 'related_projects' ),
						'callback'           => 'vc_grid_exclude_dependency_callback',
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				/**
				 * Extra
				 */
				array(
					'type'       => 'woodmart_title_divider',
					'holder'     => 'div',
					'title'      => esc_html__( 'Extra options', 'woodmart' ),
					'param_name' => 'extra_divider',
				),
				array(
					'type'             => 'woodmart_switch',
					'heading'          => esc_html__( 'Lazy loading for images', 'woodmart' ),
					'hint'             => esc_html__( 'Enable lazy loading for images for this element.', 'woodmart' ),
					'param_name'       => 'lazy_loading',
					'true_state'       => 'yes',
					'false_state'      => 'no',
					'default'          => 'no',
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'       => 'textfield',
					'heading'    => esc_html__( 'Extra class name', 'woodmart' ),
					'param_name' => 'el_class',
					'hint'       => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'woodmart' ),
				),
				array(
					'heading'    => esc_html__( 'CSS box', 'woodmart' ),
					'group'      => esc_html__( 'Design Options', 'js_composer' ),
					'type'       => 'css_editor',
					'param_name' => 'css',
				),
				woodmart_get_vc_responsive_spacing_map(),
			),
		);
	}
}

// Necessary hooks for portfolio autocomplete fields
add_filter( 'vc_autocomplete_woodmart_portfolio_include_callback', 'vc_include_projects_field_search', 10, 1 ); // Get suggestion(find). Must return an array
add_filter(
	'vc_autocomplete_woodmart_portfolio_include_render',
	'vc_include_field_render',
	10,
	1
); // Render exact product. Must return an array (label,value).


if ( ! function_exists( 'vc_include_projects_field_search' ) ) {
	/**
	 * Include search field to search query.
	 *
	 * @param string $search_string
	 *
	 * @return array
	 */
	function vc_include_projects_field_search( $search_string ) {
		$query = $search_string;
		$data  = array();
		$args  = array(
			's'         => $query,
			'post_type' => 'portfolio',
		);

		$args['vc_search_by_title_only'] = true;
		$args['numberposts']             = - 1;
		if ( 0 === strlen( $args['s'] ) ) {
			unset( $args['s'] );
		}
		add_filter( 'posts_search', 'vc_search_by_title_only', 500, 2 );
		$posts = get_posts( $args );
		if ( is_array( $posts ) && ! empty( $posts ) ) {
			foreach ( $posts as $post ) {
				$data[] = array(
					'value' => $post->ID,
					'label' => $post->post_title,
					'group' => $post->post_type,
				);
			}
		}

		return $data;
	}
}
