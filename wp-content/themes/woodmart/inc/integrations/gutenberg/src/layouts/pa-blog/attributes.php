<?php

use XTS\Gutenberg\Block_Attributes;

if ( ! function_exists( 'wd_get_blog_archive_block_attrs' ) ) {
	function wd_get_blog_archive_block_attrs() {
		$attr = new Block_Attributes();

		$attr->add_attr(
			array(
				'blog_design'          => array(
					'type'    => 'string',
					'default' => 'inherit',
				),
				'blog_masonry'         => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'img_size'             => array(
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
				'parts_title'          => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'parts_meta'           => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'parts_text'           => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'parts_btn'            => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'parts_published_date' => array(
					'type'    => 'boolean',
					'default' => true,
				),
			)
		);

		wd_get_advanced_tab_attrs( $attr );

		return $attr->get_attr();
	}
}
