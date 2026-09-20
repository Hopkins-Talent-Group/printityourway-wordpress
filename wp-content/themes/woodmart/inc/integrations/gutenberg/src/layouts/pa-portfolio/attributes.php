<?php

use XTS\Gutenberg\Block_Attributes;

if ( ! function_exists( 'wd_get_portfolio_archive_block_attrs' ) ) {
	function wd_get_portfolio_archive_block_attrs() {
		$attr = new Block_Attributes();

		$attr->add_attr(
			array(
				'portfolio_style'      => array(
					'type'    => 'string',
					'default' => 'inherit',
				),
				'portfolio_image_size' => array(
					'type'    => 'string',
					'default' => 'medium',
				),
				'imgSizeCustomWidth'   => array(
					'type' => 'string',
				),
				'imgSizeCustomHeight'  => array(
					'type' => 'string',
				),
				'columns'              => array(
					'type'       => 'number',
					'default'    => 3,
					'responsive' => true,
				),
				'spacing'              => array(
					'type'       => 'string',
					'default'    => '20',
					'responsive' => true,
				),
			)
		);

		wd_get_advanced_tab_attrs( $attr );

		return $attr->get_attr();
	}
}
