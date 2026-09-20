<?php
/**
 * Register vc elements maps for Woodmart layout.
 *
 * @package Woodmart
 */

use XTS\Modules\Layouts\Main;

if ( ! function_exists( 'woodmart_vc_register_layouts_maps' ) ) {
	function woodmart_vc_register_layouts_maps() {
		if ( ! woodmart_is_core_installed() || ! woodmart_woocommerce_installed() ) {
			return;
		}

		$maps = array();

		$cart_maps = array(
			'woodmart_cart_table'  => 'woodmart_get_vc_map_cart_table',
			'woodmart_cart_totals' => 'woodmart_get_vc_map_cart_totals',
		);

		$empty_cart_maps = array(
			'woodmart_empty_cart' => 'woodmart_get_vc_map_empty_cart_template',
		);

		$checkout_form_maps = array(
			'woodmart_checkout_billing_details_form'  => 'woodmart_get_vc_map_checkout_billing_details_form',
			'woodmart_checkout_order_review'          => 'woodmart_get_vc_map_checkout_order_review',
			'woodmart_checkout_payment_methods'       => 'woodmart_get_vc_map_checkout_payment_methods',
			'woodmart_checkout_shipping_details_form' => 'woodmart_get_vc_map_checkout_shipping_details_form',
		);

		$checkout_content_maps = array(
			'woodmart_checkout_coupon_form' => 'woodmart_get_vc_map_checkout_coupon_form',
			'woodmart_checkout_login_form'  => 'woodmart_get_vc_map_checkout_login_form',
		);

		$thank_you_page_maps = array(
			'woodmart_tp_customer_details'     => 'woodmart_get_vc_map_tp_customer_details',
			'woodmart_tp_order_details'        => 'woodmart_get_vc_map_tp_order_details',
			'woodmart_tp_order_overview'       => 'woodmart_get_vc_map_tp_order_overview',
			'woodmart_tp_order_message'        => 'woodmart_get_vc_map_tp_order_message',
			'woodmart_tp_payment_instructions' => 'woodmart_get_vc_map_tp_payment_instructions',
			'woodmart_tp_order_meta'           => 'woodmart_get_vc_map_tp_order_meta',
		);

		$shop_archive_maps = array(
			'woodmart_shop_archive_active_filters'    => 'woodmart_get_vc_map_archive_active_filters',
			'woodmart_shop_archive_description'       => 'woodmart_get_vc_map_shop_archive_description',
			'woodmart_shop_archive_products'          => 'woodmart_get_vc_map_shop_archive_products',
			'woodmart_shop_archive_extra_description' => 'woodmart_get_vc_map_archive_extra_description',
			'woodmart_shop_archive_filters_area'      => 'woodmart_get_vc_map_shop_archive_filters_area',
			'woodmart_shop_archive_filters_area_btn'  => 'woodmart_get_vc_map_shop_archive_filters_area_btn',
			'woodmart_shop_archive_orderby'           => 'woodmart_get_vc_map_shop_archive_orderby',
			'woodmart_shop_archive_per_page'          => 'woodmart_get_vc_map_shop_archive_per_page',
			'woodmart_shop_archive_result_count'      => 'woodmart_get_vc_map_shop_archive_result_count',
			'woodmart_shop_archive_view'              => 'woodmart_get_vc_map_shop_archive_view',
			'woodmart_shop_archive_woocommerce_title' => 'woodmart_get_vc_map_shop_archive_woocommerce_title',
		);

		$blog_archive_maps = array(
			'woodmart_blog_archive_loop' => 'woodmart_get_vc_map_blog_archive_loop',
			'woodmart_post_author_bio'   => 'woodmart_get_vc_map_post_author_bio',
		);

		$portfolio_archive_maps = array(
			'woodmart_portfolio_archive_loop'       => 'woodmart_get_vc_map_portfolio_archive_loop',
			'woodmart_portfolio_archive_categories' => 'woodmart_get_vc_map_portfolio_archive_categories',
		);

		$single_product_maps = array(
			'woodmart_single_product_add_to_cart'        => 'woodmart_get_vc_map_single_product_add_to_cart',
			'woodmart_single_product_additional_info_table' => 'woodmart_get_vc_map_single_product_additional_info_table',
			'woodmart_single_product_brand_information'  => 'woodmart_get_vc_map_single_product_brand_information',
			'woodmart_single_product_brands'             => 'woodmart_get_vc_map_single_product_brands',
			'woodmart_single_product_compare_button'     => 'woodmart_get_vc_map_single_product_compare_button',
			'woodmart_single_product_content'            => 'woodmart_get_vc_map_single_product_content',
			'woodmart_single_product_countdown'          => 'woodmart_get_vc_map_single_product_countdown',
			'woodmart_single_product_dynamic_discounts_table' => 'woodmart_get_vc_map_single_product_dynamic_discounts_table',
			'woodmart_single_product_extra_content'      => 'woodmart_get_vc_map_single_product_extra_content',
			'woodmart_single_product_fbt_products'       => 'woodmart_get_vc_map_single_product_fbt_products',
			'woodmart_single_product_gallery'            => 'woodmart_get_vc_map_single_product_gallery',
			'woodmart_single_product_linked_variations'  => 'woodmart_get_vc_map_single_product_linked_variations',
			'woodmart_single_product_meta'               => 'woodmart_get_vc_map_single_product_product_meta',
			'woodmart_single_product_meta_value'         => 'woodmart_get_vc_map_single_product_meta_value',
			'woodmart_single_product_nav'                => 'woodmart_get_vc_map_single_product_nav',
			'woodmart_single_product_price'              => 'woodmart_get_vc_map_single_product_price',
			'woodmart_single_product_rating'             => 'woodmart_get_vc_map_single_product_rating',
			'woodmart_single_product_reviews'            => 'woodmart_get_vc_map_single_product_reviews',
			'woodmart_single_product_short_description'  => 'woodmart_get_vc_map_single_product_short_description',
			'woodmart_single_product_size_guide_button'  => 'woodmart_get_vc_map_single_product_size_guide_button',
			'woodmart_single_product_sold_counter'       => 'woodmart_get_vc_map_single_product_sold_counter',
			'woodmart_single_product_estimate_delivery'  => 'woodmart_get_vc_map_single_product_estimate_delivery',
			'woodmart_single_product_stock_progress_bar' => 'woodmart_get_vc_map_single_product_stock_progress_bar',
			'woodmart_single_product_stock_status'       => 'woodmart_get_vc_map_single_product_stock_status',
			'woodmart_single_product_tabs'               => 'woodmart_get_vc_map_single_product_tabs',
			'woodmart_single_product_title'              => 'woodmart_get_vc_map_single_product_title',
			'woodmart_single_product_visitor_counter'    => 'woodmart_get_vc_map_single_product_visitor_counter',
			'woodmart_single_product_wishlist_button'    => 'woodmart_get_vc_map_single_product_wishlist_button',
		);

		$single_post_maps = array(
			'woodmart_single_post_categories' => 'woodmart_get_vc_map_single_post_categories',
			'woodmart_single_post_content'    => 'woodmart_get_vc_map_single_post_content',
			'woodmart_single_post_excerpt'    => 'woodmart_get_vc_map_single_post_excerpt',
			'woodmart_single_post_image'      => 'woodmart_get_vc_map_single_post_image',
			'woodmart_single_post_meta_value' => 'woodmart_get_vc_map_single_post_meta_value',
			'woodmart_single_post_navigation' => 'woodmart_get_vc_map_single_post_navigation',
			'woodmart_single_post_title'      => 'woodmart_get_vc_map_single_post_title',
		);

		if ( Main::is_layout_type( 'single_post' ) ) {
			$single_post_maps = array_merge(
				$single_post_maps,
				array(
					'woodmart_post_author_bio'             => 'woodmart_get_vc_map_post_author_bio',
					'woodmart_single_post_author_meta'     => 'woodmart_get_vc_map_single_post_author_meta',
					'woodmart_single_post_comment_form'    => 'woodmart_get_vc_map_single_post_comment_form',
					'woodmart_single_post_comments'        => 'woodmart_get_vc_map_single_post_comments',
					'woodmart_single_post_comments_button' => 'woodmart_get_vc_map_single_post_comments_button',
					'woodmart_single_post_date_meta'       => 'woodmart_get_vc_map_single_post_date_meta',
					'woodmart_single_post_tags'            => 'woodmart_get_vc_map_single_post_tags',
				)
			);
		}

		$woocommerce_maps = array(
			'woodmart_woocommerce_hook'      => 'woodmart_get_vc_map_woocommerce_hook',
			'woodmart_woocommerce_notices'   => 'woodmart_get_vc_map_woocommerce_notices',
			'woodmart_page_title'            => 'woodmart_get_vc_map_page_title',
			'woodmart_shipping_progress_bar' => 'woodmart_get_vc_map_shipping_progress_bar',
		);

		if ( Main::is_layout_type( 'shop_archive' ) ) {
			$maps = array_merge( $maps, $shop_archive_maps );
		}

		if ( Main::is_layout_type( 'blog_archive' ) ) {
			$maps = array_merge( $maps, $blog_archive_maps );
		}

		if ( Main::is_layout_type( 'portfolio_archive' ) ) {
			$maps = array_merge( $maps, $portfolio_archive_maps );
		}

		if ( Main::is_layout_type( 'single_product' ) ) {
			$maps = array_merge( $maps, $single_product_maps );
		}

		if ( Main::is_layout_type( 'single_post' ) || Main::is_layout_type( 'single_portfolio' ) ) {
			ksort( $single_post_maps );
			$maps = array_merge( $maps, $single_post_maps );
		}

		if ( Main::is_layout_type( 'cart' ) ) {
			$maps = array_merge( $maps, $cart_maps );
		}

		if ( Main::is_layout_type( 'empty_cart' ) ) {
			$maps = array_merge( $maps, $empty_cart_maps );
		}

		if ( Main::is_layout_type( 'checkout_form' ) ) {
			$maps = array_merge( $maps, $checkout_form_maps );
		}

		if ( Main::is_layout_type( 'checkout_content' ) ) {
			$maps = array_merge( $maps, $checkout_content_maps );
		}

		if ( Main::is_layout_type( 'checkout_form' ) || Main::is_layout_type( 'cart' ) || Main::is_layout_type( 'checkout_content' ) ) {
			$maps = array_merge( $maps, array( 'woodmart_woocommerce_checkout_steps' => 'woodmart_get_vc_map_checkout_steps' ) );
		}

		if ( Main::is_layout_type( 'checkout_form' ) || Main::is_layout_type( 'cart' ) ) {
			$maps = array_merge( $maps, array( 'woodmart_cart_free_gifts' => 'woodmart_get_vc_map_free_gifts' ) );
		}

		if ( Main::is_layout_type( 'thank_you_page' ) ) {
			$maps = array_merge( $maps, $thank_you_page_maps );
		}

		if ( Main::is_layout_type( 'single_product' ) || Main::is_layout_type( 'shop_archive' ) || Main::is_layout_type( 'checkout_form' ) || Main::is_layout_type( 'cart' ) || Main::is_layout_type( 'checkout_content' ) || Main::is_layout_type( 'thank_you_page' ) ) {
			$maps = array_merge( $maps, array( 'woodmart_woocommerce_breadcrumb' => 'woodmart_get_vc_map_woocommerce_breadcrumb' ) );
		}

		$maps = array_merge( $maps, $woocommerce_maps );

		foreach ( $maps as $key => $callback ) {
			woodmart_vc_map( $key, $callback );
		}
	}

	add_action( 'vc_mapper_init_after', 'woodmart_vc_register_layouts_maps', 11 );
}
