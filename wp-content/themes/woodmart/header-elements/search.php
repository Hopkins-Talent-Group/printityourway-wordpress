<?php
/**
 * The search form template.
 *
 * @package woodmart
 */

woodmart_enqueue_inline_style( 'header-search' );

if ( in_array( $params['display'], array( 'form', 'dropdown' ), true ) && ! empty( $params['bg_overlay'] ) ) {
	woodmart_enqueue_js_script( 'menu-overlay' );
}

if ( in_array( $params['display'], array( 'form', 'full-screen-2' ), true ) ) {
	woodmart_enqueue_inline_style( 'header-search-form' );

	woodmart_search_form( $params );

	return;
}

if ( '8' === $params['icon_design'] ) {
	woodmart_enqueue_inline_style( 'mod-tools-design-8' );
}
?>
<div class="wd-header-search wd-tools-element<?php echo esc_attr( $params['extra_class'] ); ?>" title="<?php echo esc_attr__( 'Search', 'woodmart' ); ?>">
	<a href="#" rel="nofollow" aria-label="<?php esc_attr_e( 'Search', 'woodmart' ); ?>">
		<?php if ( $params['icon_wrapper'] ) : ?>
			<span class="wd-tools-inner">
		<?php endif; ?>

			<span class="wd-tools-icon<?php echo esc_attr( woodmart_get_old_classes( ' search-button-icon' ) ); ?>">
				<?php if ( 'custom' === $params['icon_type'] ) : ?>
					<?php echo whb_get_custom_icon( $params['custom_icon'] ); // phpcs:ignore. ?>
				<?php endif; ?>
			</span>

			<span class="wd-tools-text">
				<?php echo esc_html__( 'Search', 'woodmart' ); ?>
			</span>

		<?php if ( $params['icon_wrapper'] ) : ?>
			</span>
		<?php endif; ?>
	</a>

	<?php woodmart_search_form( $params ); ?>
</div>
