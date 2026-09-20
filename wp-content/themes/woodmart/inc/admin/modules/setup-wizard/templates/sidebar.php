<?php
/**
 * Sidebar template.
 *
 * @package woodmart
 */

?>

<ul>
	<?php
	$index        = 0;
	$current_page = 'welcome';

	if ( isset( $_GET['step'] ) && ! empty( $_GET['step'] ) ) { // phpcs:ignore
		$current_page = trim( wp_unslash( $_GET['step'] ) ); // phpcs:ignore
	}

	$current_page_index = array_search( $current_page, array_keys( $this->available_pages ), true );

	?>
	<?php foreach ( $this->available_pages as $slug => $text ) : ?>
		<?php
		$classes = '';
		if ( $index > $current_page_index ) {
			$classes .= ' xts-disabled';
		}

		if ( $this->is_active_page( $slug ) ) {
			$classes .= ' xts-active';
		}

		?>
		<li class="<?php echo esc_attr( $classes ); ?>" data-slug="<?php echo esc_attr( $slug ); ?>">
			<a class="xts-wizard-nav-btn xts-fill" href="<?php echo esc_url( $this->get_page_url( $slug ) ); ?>"></a>
			<span class="xts-wizard-nav-content" href="<?php echo esc_url( $this->get_page_url( $slug ) ); ?>">
				<?php echo esc_html( $text ); ?>
			</span>
		</li>
		<?php $index++; ?>
	<?php endforeach; ?>
</ul>
