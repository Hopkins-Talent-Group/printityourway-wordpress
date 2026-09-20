<?php
/**
 * Dropdown search template.
 *
 * @package Woodmart
 */

?>
<div class="wd-search-<?php echo esc_attr( $args['type'] ); ?> <?php echo esc_html( $wrapper_classes ); ?>"<?php echo wp_kses( $wrapper_atts, true ); ?>>

<form role="search" method="get" class="searchform <?php echo esc_attr( $class ); ?>" action="<?php echo esc_url( home_url( '/' ) ); ?>" <?php echo ! empty( $data ) ? $data : ''; ?> autocomplete="off">
	<input type="text" class="s" placeholder="<?php echo esc_attr( $placeholder ); ?>" value="<?php echo get_search_query(); ?>" name="s" aria-label="<?php esc_attr_e( 'Search', 'woodmart' ); ?>" title="<?php echo esc_attr( $placeholder ); ?>"<?php echo esc_attr( apply_filters( 'woodmart_show_required_in_search_form', true ) ? ' required' : '' ); ?>/>
	<input type="hidden" name="post_type" value="<?php echo esc_attr( $args['post_type'] ); ?>">

	<span class="wd-clear-search<?php echo get_search_query() ? '' : ' wd-hide'; ?>"></span>

	<?php if ( $args['show_categories'] && 'product' === $args['post_type'] ) : ?>
		<?php $this->show_categories_dropdown(); ?>
	<?php endif; ?>

	<button type="submit" class="searchsubmit<?php echo esc_attr( $btn_classes ); ?>">
		<span>
			<?php echo esc_attr_x( 'Search', 'submit button', 'woodmart' ); ?>
		</span>
		<?php
		if ( 'custom' === $args['icon_type'] ) {
			echo whb_get_custom_icon( $args['custom_icon'] ); // phpcs:ignore.
		}
		?>
	</button>
</form>

<?php if ( $args['ajax'] || $popular_search_requests || ! empty( $extra_content ) || $args['search_history_enabled'] ) : ?>
	<div class="wd-search-results-wrapper">
		<div class="wd-search-results wd-dropdown-results <?php echo esc_attr( $dropdowns_classes ); ?>">
			<div class="wd-scroll-content">
				<?php if ( $args['search_history_enabled'] ) : ?>
					<div class="wd-search-history"></div>
				<?php endif; ?>

				<?php if ( $popular_search_requests ) : ?>
					<?php $this->show_search_requests( $popular_search_requests, $args['post_type'] ); ?>
				<?php endif; ?>

				<?php if ( ! empty( $extra_content ) ) : ?>
					<?php echo $extra_content; // phpcs:ignore. ?>
				<?php endif; ?>
			</div>
		</div>
	</div>
<?php endif ?>

</div>
<?php
