<?php
/**
 * RevSlider integration.
 *
 * @package woodmart
 */

if ( ! defined( 'RS_REVISION' ) ) {
	return;
}

if ( ! function_exists( 'woodmart_revslider_post_saving_during_cart_register' ) ) {
	/**
	 * Skip saving post during cart register.
	 *
	 * @param bool $skip Skip saving post.
	 * @return bool
	 */
	function woodmart_revslider_post_saving_during_cart_register( $skip ) {
		if ( class_exists( 'RevSliderFront' ) ) {
			remove_action( 'save_post', array( 'RevSliderFront', 'set_post_saving' ) );
		}

		return $skip;
	}

	add_filter( 'woodmart_skip_register_cart', 'woodmart_revslider_post_saving_during_cart_register' );
}
