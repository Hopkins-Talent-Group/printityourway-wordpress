<?php
/**
 * Child theme template.
 *
 * @package woodmart
 */

?>

<div class="xts-wizard-content-inner xts-wizard-child-theme<?php echo is_child_theme() ? ' xts-installed' : ''; ?>">

	<?php $this->get_skip_button( 'page-builder' ); ?>

	<div class="xts-child-theme-response"></div>

	<h3>
		<?php esc_html_e( 'Child theme', 'woodmart' ); ?>
	</h3>

	<p>
		<?php esc_html_e( 'Install the child theme with one click.', 'woodmart' ); ?>
	</p>

	<div class="xts-theme-images">
		<div class="xts-main-image">
			<img  src="<?php echo esc_url( $this->get_image_url( 'parent.jpg' ) ); ?>" alt="parent">
		</div>
		<div class="xts-child-image">
			<img  src="<?php echo esc_url( $this->get_image_url( 'child.jpg' ) ); ?>" alt="child">
		</div>
		<span class="xts-child-checkmark"></span>
	</div>

	<p>
		<?php
		esc_html_e(
			'If you plan to make changes to the theme’s source code, we recommend using a child theme instead of modifying the main theme’s HTML, CSS, or PHP files directly. This way, you can safely update the parent theme without losing your customizations. Use the button below to create and activate a child theme.',
			'woodmart'
		);
		?>
	</p>

	<div class="xts-step-actions">
		<a href="#" class="xts-btn xts-color-primary xts-install-child-theme">
			<?php esc_html_e( 'Install child theme', 'woodmart' ); ?>
		</a>
		<?php $this->get_next_button( 'page-builder' ); ?>
	</div>
</div>