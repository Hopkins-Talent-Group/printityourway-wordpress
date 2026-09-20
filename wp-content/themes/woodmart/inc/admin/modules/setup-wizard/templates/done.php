<?php
/**
 * Done template.
 *
 * @package woodmart
 */

?>

<div class="xts-wizard-content-inner xts-wizard-done">
	<div class="xts-wizard-img">
		<img src="<?php echo esc_url( $this->get_image_url( 'done.svg' ) ); ?>" alt="done">
	</div>

	<h3>
		<?php esc_html_e( 'Everything is ready!', 'woodmart' ); ?>
	</h3>

	<p>
		<?php
		esc_html_e(
			'Congratulations! You have successfully installed our theme and are ready to start building your amazing website! With our theme, you have full control over the layout and style, giving you the flexibility to create a site that suits your vision perfectly.',
			'woodmart'
		);
		?>
	</p>

	<div class="xts-step-actions">
		<a class="xts-btn xts-color-primary xts-i-theme-settings" href="<?php echo esc_url( admin_url( 'admin.php?page=xts_dashboard' ) ); ?>">
			<?php esc_html_e( 'Start customizing', 'woodmart' ); ?>
		</a>
		<a class="xts-inline-btn xts-color-primary" href="<?php echo esc_url( get_home_url() ); ?>">
			<?php esc_html_e( 'Close and view website', 'woodmart' ); ?>
		</a>
	</div>
</div>
