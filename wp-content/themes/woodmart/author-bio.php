<?php
/**
 * The template for displaying Author bios
 */

use XTS\Modules\Layouts\Main;

if ( ! woodmart_get_opt( 'blog_author_bio' ) && is_singular( 'post' ) && ! Main::get_instance()->is_custom_layout() ) {
	return;
}
?>

<div class="wd-author-bio wd-design-1">
	<?php
	woodmart_enqueue_inline_style( 'blog-el-author-bio' );
	$author_bio_avatar_size = apply_filters( 'woodmart_author_bio_avatar_size', 74 );
	$author_bio_description = get_the_author_meta( 'description' );
	echo get_avatar( get_the_author_meta( 'user_email' ), $author_bio_avatar_size, '', 'author-avatar' );
	?>
	<h4 class="wd-author-title"><?php printf( esc_html__( 'About %s', 'woodmart' ), get_the_author() ); ?></h4>
	<?php if ( $author_bio_description ) : ?>
	<p class="wd-author-area-info"><?php the_author_meta( 'description' ); ?></p>
	<?php endif; ?>
	<a class="wd-author-link" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author">
		<?php printf( wp_kses( __( 'View all posts by %s', 'woodmart' ), array( 'span' => array('class') ) ), get_the_author() ); ?>
	</a>
</div>