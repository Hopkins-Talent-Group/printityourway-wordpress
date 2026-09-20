<?php
/**
 * Condition template.
 *
 * @package Woodmart
 */

?>
<div class="xts-layout-condition-template xts-hidden">
	<div class="xts-layout-condition">
		<select class="xts-layout-condition-comparison" name="wd_layout_condition_comparison" aria-label="<?php esc_attr_e( 'Condition comparison', 'woodmart' ); ?>">
			<option value="include">
				<?php esc_html_e( 'Include', 'woodmart' ); ?>
			</option>
			<option value="exclude">
				<?php esc_html_e( 'Exclude', 'woodmart' ); ?>
			</option>
		</select>

		<select class="xts-layout-condition-type" name="wd_layout_condition_type" data-type="shop_archive" aria-label="<?php esc_attr_e( 'Condition type', 'woodmart' ); ?>">
			<option value="all">
				<?php esc_html_e( 'All product archives', 'woodmart' ); ?>
			</option>
			<option value="shop_page">
				<?php esc_html_e( 'Shop page', 'woodmart' ); ?>
			</option>
			<option value="product_search">
				<?php esc_html_e( 'Shop search results', 'woodmart' ); ?>
			</option>
			<option value="product_cats">
				<?php esc_html_e( 'Product categories', 'woodmart' ); ?>
			</option>
			<option value="product_tags">
				<?php esc_html_e( 'Product tags', 'woodmart' ); ?>
			</option>
			<?php if ( taxonomy_exists( 'product_brand' ) ) : ?>
				<option value="product_brands">
					<?php esc_html_e( 'Product brand', 'woodmart' ); ?>
				</option>
			<?php endif; ?>
			<option value="product_attr">
				<?php esc_html_e( 'Product attribute', 'woodmart' ); ?>
			</option>
			<option value="product_term">
				<?php esc_html_e( 'Product term (category, tag, brand, attribute)', 'woodmart' ); ?>
			</option>
			<option value="product_cat_children">
				<?php esc_html_e( 'Child product categories', 'woodmart' ); ?>
			</option>
			<option value="filtered_product_term">
				<?php esc_html_e( 'Filtered by attribute', 'woodmart' ); ?>
			</option>
			<option value="filtered_product_by_term">
				<?php esc_html_e( 'Filtered by term', 'woodmart' ); ?>
			</option>
			<option value="filtered_product_term_any">
				<?php esc_html_e( 'Filtered by any attribute', 'woodmart' ); ?>
			</option>
			<option value="filtered_product_stock_status">
				<?php esc_html_e( 'Filtered by stock status', 'woodmart' ); ?>
			</option>
		</select>

		<select class="xts-layout-condition-type" name="wd_layout_condition_type" data-type="single_product" aria-label="<?php esc_attr_e( 'Condition type', 'woodmart' ); ?>">
			<option value="all">
				<?php esc_html_e( 'All products', 'woodmart' ); ?>
			</option>
			<option value="product">
				<?php esc_html_e( 'Single product id', 'woodmart' ); ?>
			</option>
			<option value="product_cat">
				<?php esc_html_e( 'Product category', 'woodmart' ); ?>
			</option>
			<option value="product_cat_children">
				<?php esc_html_e( 'Child product categories', 'woodmart' ); ?>
			</option>
			<option value="product_tag">
				<?php esc_html_e( 'Product tag', 'woodmart' ); ?>
			</option>
			<?php if ( taxonomy_exists( 'product_brand' ) ) : ?>
				<option value="product_brand">
					<?php esc_html_e( 'Product brand', 'woodmart' ); ?>
				</option>
			<?php endif; ?>
			<option value="product_attr_term">
				<?php esc_html_e( 'Product attribute', 'woodmart' ); ?>
			</option>
			<option value="product_type">
				<?php esc_html_e( 'Product type', 'woodmart' ); ?>
			</option>
		</select>

		<select class="xts-layout-condition-type" name="wd_layout_condition_type" data-type="checkout_form" aria-label="<?php esc_attr_e( 'Condition type', 'woodmart' ); ?>">
			<option value="checkout_form">
				<?php esc_html_e( 'Checkout page form', 'woodmart' ); ?>
			</option>
		</select>

		<select class="xts-layout-condition-type" name="wd_layout_condition_type" data-type="checkout_content" aria-label="<?php esc_attr_e( 'Condition type', 'woodmart' ); ?>">
			<option value="checkout_content">
				<?php esc_html_e( 'Checkout page content', 'woodmart' ); ?>
			</option>
		</select>

		<select class="xts-layout-condition-type" name="wd_layout_condition_type" data-type="cart" aria-label="<?php esc_attr_e( 'Condition type', 'woodmart' ); ?>">
			<option value="cart">
				<?php esc_html_e( 'Cart page', 'woodmart' ); ?>
			</option>
		</select>

		<select class="xts-layout-condition-type" name="wd_layout_condition_type" data-type="empty_cart" aria-label="<?php esc_attr_e( 'Condition type', 'woodmart' ); ?>">
			<option value="empty_cart">
				<?php esc_html_e( 'Empty cart page', 'woodmart' ); ?>
			</option>
		</select>
		
		<select class="xts-layout-condition-type" name="wd_layout_condition_type" data-type="single_post" aria-label="<?php esc_attr_e( 'Condition type', 'woodmart' ); ?>">
			<option value="all">
				<?php esc_html_e( 'All posts', 'woodmart' ); ?>
			</option>
			<option value="post_id">
				<?php esc_html_e( 'Single post id', 'woodmart' ); ?>
			</option>
			<option value="post_cat">
				<?php esc_html_e( 'Post category', 'woodmart' ); ?>
			</option>
			<option value="post_tag">
				<?php esc_html_e( 'Post tag', 'woodmart' ); ?>
			</option>
			<option value="post_format">
				<?php esc_html_e( 'Post format', 'woodmart' ); ?>
			</option>
		</select>

		<select class="xts-layout-condition-type" name="wd_layout_condition_type" data-type="single_portfolio" aria-label="<?php esc_attr_e( 'Condition type', 'woodmart' ); ?>">
			<option value="all">
			<?php esc_html_e( 'All projects', 'woodmart' ); ?>
			</option>
			<option value="project_id">
				<?php esc_html_e( 'Single project id', 'woodmart' ); ?>
			</option>
			<option value="project_cat">
				<?php esc_html_e( 'Project category', 'woodmart' ); ?>
			</option>
		</select>

		<select class="xts-layout-condition-type" name="wd_layout_condition_type" data-type="blog_archive" aria-label="<?php esc_attr_e( 'Condition type', 'woodmart' ); ?>">
			<option value="all">
				<?php esc_html_e( 'All blog archives', 'woodmart' ); ?>
			</option>
			<option value="blog_search_result">
				<?php esc_html_e( 'Blog search results', 'woodmart' ); ?>
			</option>
			<option value="blog_category"> 
				<?php esc_html_e( 'Blog categories', 'woodmart' ); ?>
			</option>
			<option value="blog_tag">
				<?php esc_html_e( 'Blog tags', 'woodmart' ); ?>
			</option>
			<option value="blog_author">
				<?php esc_html_e( 'Blog author page', 'woodmart' ); ?>
			</option>
			<option value="blog_date">
				<?php esc_html_e( 'Blog archives by date', 'woodmart' ); ?>
			</option>
		</select>
		
		<select class="xts-layout-condition-type" name="wd_layout_condition_type" data-type="portfolio_archive" aria-label="<?php esc_attr_e( 'Condition type', 'woodmart' ); ?>">
			<option value="all">
				<?php esc_html_e( 'All portfolio archives', 'woodmart' ); ?>
			</option>
			<option value="portfolio_search_result">
				<?php esc_html_e( 'Portfolio search results', 'woodmart' ); ?>
			</option>
			<option value="portfolio_category">
				<?php esc_html_e( 'Portfolio categories', 'woodmart' ); ?>
			</option>
		</select>

		<select class="xts-layout-condition-query xts-hidden" name="wd_layout_condition_query" placeholder="<?php echo esc_attr__( 'Start typing...', 'woodmart' ); ?>" aria-label="<?php esc_attr_e( 'Condition query', 'woodmart' ); ?>"></select>

		<a href="javascript:void(0);" class="xts-layout-condition-remove xts-bordered-btn xts-color-warning xts-style-icon xts-i-close" title="<?php esc_attr_e( 'Remove condition', 'woodmart' ); ?>"></a>
	</div>
</div>
