<?php
/**
 * Dummy content template.
 *
 * @package woodmart
 */

use XTS\Admin\Modules\Import;
use XTS\Admin\Modules\Import\Remove;

$wrapper_classes = '';
$import_classes  = '';

if ( Import::get_instance()->is_imported( 'base' ) && Remove::get_instance()->has_data_to_remove() ) {
	$wrapper_classes .= ' imported-base';
	$import_classes  .= ' xts-active';
} else {
	$wrapper_classes .= ' xts-active';
}
?>

<div class="xts-wizard-content-inner xts-wizard-dummy<?php echo esc_attr( $wrapper_classes ); ?>">
	<?php $this->get_skip_button( 'done' ); ?>
	<?php Import::get_instance()->render(); ?>
</div>

<div class="xts-wizard-content-inner xts-wizard-import-template<?php echo esc_attr( $import_classes ); ?>">
	<h3 class="xts-import-title">
		<?php esc_html_e( 'Importing', 'woodmart' ); ?>
	</h3>

	<p>
		<?php esc_html_e( 'The import process includes a homepage, products, posts, projects, images, and menus.', 'woodmart' ); ?>
	</p>

	<div class="xts-import-item"></div>

	<ul class="xts-import-status">
		<li><?php esc_html_e( 'Importing posts', 'woodmart' ); ?></li>
		<li><?php esc_html_e( 'Importing products', 'woodmart' ); ?></li>
		<li><?php esc_html_e( 'Importing pages', 'woodmart' ); ?></li>
		<li><?php esc_html_e( 'Importing images', 'woodmart' ); ?></li>
		<li><?php esc_html_e( 'Importing menus', 'woodmart' ); ?></li>
		<li><?php esc_html_e( 'Importing widgets', 'woodmart' ); ?></li>
		<li><?php esc_html_e( 'Importing header configuration', 'woodmart' ); ?></li>
		<li><?php esc_html_e( 'Applying theme settings', 'woodmart' ); ?></li>
		<li><?php esc_html_e( 'All content has been successfully imported and is ready to use.', 'woodmart' ); ?></li>
	</ul>

	<div class="xts-box">
		<div class="xts-notices-wrapper xts-import-notices"></div>
	</div>

	<div class="xts-note">
		<?php
		echo wp_kses(
			__(
				'<span>Note:</span> please do not close this tab while content is being imported. This may interrupt the installation process.',
				'woodmart'
			),
			true
		);
		?>
	</div>

	<div class="xts-step-actions">
		<?php $this->get_next_button( 'done' ); ?>
	</div>
</div>

<?php $this->show_part( 'done' ); ?>
