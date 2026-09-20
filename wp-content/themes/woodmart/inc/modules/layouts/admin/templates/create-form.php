<?php
/**
 * Form template.
 *
 * @package Woodmart
 *
 * @var array $layout_types Layout types.
 * @var Admin $admin        Admin instance.
 */

$layout_default_name = 'New layout';
$current_tab         = isset( $_GET['wd_layout_type_tab'] ) ? $_GET['wd_layout_type_tab'] : 'all';  // phpcs:ignore

if ( 'all' !== $current_tab ) {
	$layout_default_name = ucfirst( str_replace( '_', ' ', $current_tab ) ) . ' layout';
}

if ( 'checkout' === $current_tab ) {
	$layout_types = array(
		'checkout_content' => esc_html__( 'Checkout top content', 'woodmart' ),
		'checkout_form'    => esc_html__( 'Checkout form', 'woodmart' ),
	);

	if ( 'native' === woodmart_get_opt( 'current_builder' ) ) {
		$layout_types = array( 'checkout_form' => esc_html__( 'Checkout', 'woodmart' ) );
	}

	$layout_types['thank_you_page'] = esc_html__( 'Thank you page', 'woodmart' );
} elseif ( 'cart' === $current_tab ) {
	$layout_types = array(
		'cart'       => esc_html__( 'Cart', 'woodmart' ),
		'empty_cart' => esc_html__( 'Empty cart', 'woodmart' ),
	);
} elseif ( 'post' === $current_tab ) {
	$layout_types = array(
		'single_post'      => esc_html__( 'Single post', 'woodmart' ),
		'single_portfolio' => esc_html__( 'Single project', 'woodmart' ),
	);
} elseif ( 'archive' === $current_tab ) {
	$layout_types = array(
		'blog_archive'      => esc_html__( 'Blog', 'woodmart' ),
		'portfolio_archive' => esc_html__( 'Portfolio', 'woodmart' ),
	);
}

$wrapper_classes = ' xts-layout-type-' . $current_tab;
?>
<form>
	<div class="xts-layout-fields<?php echo esc_attr( $wrapper_classes ); ?>">
		<div class="xts-layout-field xts-layout-type-select">
			<label for="wd_layout_type">
				<?php esc_html_e( 'Layout type', 'woodmart' ); ?>
			</label>
			<select class="xts-layout-type" id="wd_layout_type" name="wd_layout_type" required>
				<option value="">
					<?php esc_html_e( 'Select...', 'woodmart' ); ?>
				</option>
				<?php foreach ( $layout_types as $key => $label ) : ?>
					<?php
					$current_tab = isset( $_GET['wd_layout_type_tab'] ) ? $_GET['wd_layout_type_tab'] : ''; // phpcs:ignore

					if ( 'checkout' === $current_tab ) {
						$current_tab = 'checkout_form';
					}
					?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $current_tab, $key ); ?>>
						<?php echo esc_html( $label ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>

		<div class="xts-layout-field">
			<label for="wd_layout_name">
				<?php esc_html_e( 'Layout name', 'woodmart' ); ?>
			</label>
			<input class="xts-layout-name" id="wd_layout_name" name="wd_layout_name" type="text" placeholder="<?php esc_attr_e( 'Enter layout name', 'woodmart' ); ?>" required value="<?php echo esc_attr( $layout_default_name ); ?>">
		</div>
	</div>

	<div class="xts-layout-conditions">
		<label class="xts-layout-conditions-title">
			<?php esc_html_e( 'Conditions', 'woodmart' ); ?>
		</label>

		<a href="javascript:void(0);" class="xts-layout-condition-add xts-hidden xts-inline-btn xts-color-primary xts-i-add">
			<?php esc_html_e( 'Add condition', 'woodmart' ); ?>
		</a>
	</div>

	<?php $admin->get_predefined_layouts(); ?>
	<div class="xts-popup-actions xts-layout-submit-wrap">
		<button class="xts-disabled xts-layout-submit xts-btn xts-color-primary xts-i-add" type="submit">
			<?php esc_html_e( 'Create layout', 'woodmart' ); ?>
		</button>
	</div>
</form>
