<?php
use XTS\Gutenberg\Block_CSS;

$block_css = new Block_CSS( $attrs );

if ( ! empty( $attrs['productsWithBackground'] ) && 'yes' === $attrs['productsWithBackground'] ) {
	$block_css->add_css_rules(
		$block_selector . ' .wd-products-with-bg, ' . $block_selector . ' .wd-products-with-bg .wd-product',
		array(
			array(
				'attr_name' => 'productsBackgroundCode',
				'template'  => '--wd-prod-bg: {{value}};',
			),
			array(
				'attr_name' => 'productsBackgroundVariable',
				'template'  => '--wd-prod-bg: var({{value}});',
			),
			array(
				'attr_name' => 'productsBackgroundCode',
				'template'  => '--wd-bordered-bg: {{value}};',
			),
			array(
				'attr_name' => 'productsBackgroundVariable',
				'template'  => '--wd-bordered-bg: var({{value}});',
			),
		)
	);
}

if ( ! empty( $attrs['productsBorderedGrid'] ) && 'enable' === $attrs['productsBorderedGrid'] ) {
	$block_css->add_css_rules(
		$block_selector . ' [class*="products-bordered-grid"], ' . $block_selector . ' [class*="products-bordered-grid"] .wd-product',
		array(
			array(
				'attr_name' => 'productsBorderColorCode',
				'template'  => '--wd-bordered-brd: {{value}};',
			),
			array(
				'attr_name' => 'productsBorderColorVariable',
				'template'  => '--wd-bordered-brd: var({{value}});',
			),
		)
	);
}

$block_css->merge_with(
	wd_get_block_advanced_css(
		array(
			'selector'       => $block_selector,
			'selector_hover' => $block_selector_hover,
		),
		$attrs
	)
);

return $block_css->get_css_for_devices();
