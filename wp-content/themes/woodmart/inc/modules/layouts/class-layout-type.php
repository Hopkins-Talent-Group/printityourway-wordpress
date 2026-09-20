<?php

namespace XTS\Modules\Layouts;

use XTS\Singleton;

abstract class Layout_Type extends Singleton {
	/**
	 * Constructor.
	 */
	public function init() {
		add_filter( 'template_include', array( $this, 'override_template' ), 20 );
		add_action( 'body_class', array( $this, 'get_body_classes' ) );
	}

	/**
	 * Check.
	 *
	 * @param array  $condition Condition.
	 * @param string $type      Layout type.
	 */
	public function check( $condition, $type = '' ) {
	}

	/**
	 * Override templates.
	 *
	 * @param string $template Template.
	 */
	public function override_template( $template ) {
	}

	/**
	 * Display template.
	 */
	protected function display_template() {
		Main::get_instance()->set_is_custom_layout( true );
	}

	/**
	 * Before template content.
	 */
	public function before_template_content() {
		get_header();
		do_action( 'woocommerce_before_main_content' );
	}

	/**
	 * After template content.
	 */
	public function after_template_content() {
		do_action( 'woocommerce_after_main_content' );
		get_footer();
	}

	/**
	 * Template content.
	 *
	 * @param string $type Template type.
	 */
	public function template_content( $type ) {
		$id   = apply_filters( 'wpml_object_id', Main::get_instance()->get_layout_id( $type ), 'woodmart_layout' );
		$post = get_post( $id );

		if ( ! $post || 'woodmart_layout' !== $post->post_type || ! $id ) {
			return;
		}

		echo woodmart_get_post_content( $id ); // phpcs:ignore
	}

	/**
	 * Get body classes.
	 *
	 * @param array $classes Classes for the body element.
	 * @return array
	 */
	public function get_body_classes( $classes ) {
		return $classes;
	}
}
