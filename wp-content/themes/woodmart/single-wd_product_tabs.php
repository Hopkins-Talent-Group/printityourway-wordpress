<?php
/**
 * The template for displaying all custom product tabs.
 *
 * @package xts
 */

if ( ! current_user_can( apply_filters( 'woodmart_product_tabs_access', 'edit_products' ) ) ) {
	wp_die( 'You do not have access.', '', array( 'back_link' => true ) );
}

get_header();

?>

<div class="container">
	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>
		<?php the_content(); ?>
	<?php endwhile; ?>
</div>

<?php

get_footer();
