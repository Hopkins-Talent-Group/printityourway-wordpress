<?php
/**
 * Activation template.
 *
 * @package woodmart
 */

?>

<div class="xts-wizard-content-inner">
	<?php if ( ! woodmart_is_license_activated() ) : ?>
		<?php $this->get_skip_button( 'child-theme' ); ?>
	<?php endif; ?>
	<div class="xts-wizard-img">
		<img src="<?php echo esc_url( $this->get_image_url( 'key.svg' ) ); ?>" alt="license key">
	</div>
	<?php XTS\Registry::getInstance()->activation->form(); ?>
	<?php if ( woodmart_is_license_activated() ) : ?>
		<?php $this->get_next_button( 'child-theme' ); ?>
	<?php endif; ?>
</div>