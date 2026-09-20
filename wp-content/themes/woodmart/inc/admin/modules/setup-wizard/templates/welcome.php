<?php
/**
 * Welcome template.
 *
 * @package woodmart
 */

?>

<div class="xts-wizard-content-inner xts-wizard-welcome">

	<div class="xts-wizard-welcome-img">
		<img src="<?php echo esc_url( $this->get_image_url( 'welcome.png' ) ); ?>" alt="logo">
	</div>

	<h3>
		<?php esc_html_e( 'Thank you for choosing our theme!', 'woodmart' ); ?>
	</h3>

	<p>
		<?php
		esc_html_e(
			'During the next steps, you will choose and configure the basic settings of your website by enabling automatic updates, installing the required plugins, and selecting a pre-built website with demo content.',
			'woodmart'
		);
		?>
	</p>

	<p class="xts-wizard-signature">
		<span>
			<?php esc_html_e( 'Good Luck!', 'woodmart' ); ?>
		</span>

		<img src="<?php echo esc_url( $this->get_image_url( 'signature.svg' ) ); ?>" alt="signature">
	</p>

	<div class="xts-step-actions">
		<a class="xts-btn xts-color-primary xts-next" href="<?php echo esc_url( $this->get_page_url( 'activation' ) ); ?>">
			<?php esc_html_e( 'Let\'s start', 'woodmart' ); ?>
		</a>
		<a class="xts-inline-btn xts-color-primary xts-skip xts-skip-setup" href="<?php echo esc_url( admin_url( 'admin.php?page=xts_dashboard&skip_setup' ) ); ?>">
			<?php esc_html_e( 'Skip setup', 'woodmart' ); ?>
		</a>
	</div>

</div>
