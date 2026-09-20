<?php
/**
 * The default template for displaying content
 *
 * Used for both single and index/archive/search.
 */

$woodmart_loop         = woodmart_loop_prop( 'woodmart_loop' );
$blog_design           = woodmart_loop_prop( 'blog_design' );
$blog_style            = woodmart_get_opt( 'blog_style', 'shadow' );
$post_format           = get_post_format();
$thumb_classes         = '';
$gallery               = array();
$gallery_slider        = apply_filters( 'woodmart_gallery_slider', true );
$gallery_inner_classes = '';
$blog_excerpt          = woodmart_get_content( woodmart_loop_prop( 'parts_btn' ), 'full' === woodmart_get_opt( 'blog_excerpt' ), true );
$has_excerpt           = woodmart_loop_prop( 'parts_text' ) && ( get_the_excerpt() || $blog_excerpt );
$hide_condition        = 'mask' !== $blog_design || ( 'mask' === $blog_design && 'quote' === get_post_format() );

$classes = array(
	'wd-post',
	'blog-design-' . $blog_design,
	'blog-post-loop',
);

if ( 'chess' === $blog_design ) {
	$classes[] = 'blog-design-small-images';
}

if ( 'shadow' === $blog_style ) {
	$classes[] = 'blog-style-bg';

	if ( woodmart_get_opt( 'blog_with_shadow', true ) ) {
		$classes[] = 'wd-add-shadow';
	}
} else {
	$classes[] = 'blog-style-' . $blog_style;
}

if ( 'grid' === woodmart_loop_prop( 'blog_layout' ) ) {
	$classes[] = 'wd-col';
}

if ( ! get_the_title() ) {
	$classes[] = 'post-no-title';
}

if ( woodmart_loop_prop( 'parts_meta' ) && get_the_category_list( ', ' ) ) {
	woodmart_enqueue_inline_style( 'post-types-mod-categories-style-bg' );
}

if ( 'quote' === $post_format ) {
	woodmart_enqueue_inline_style( 'blog-loop-format-quote' );
} elseif ( 'gallery' === $post_format && $gallery_slider ) {
	$gallery = get_post_gallery( false, false );
	woodmart_enqueue_inline_style( 'blog-mod-gallery' );

	if ( ! empty( $gallery['ids'] ) ) {
		$thumb_classes         .= ' wd-carousel-container wd-post-gallery';
		$gallery_inner_classes .= ' color-scheme-light';

		$gallery['images_id'] = explode( ',', $gallery['ids'] );

		if ( ! has_post_thumbnail() ) {
			$classes[] = 'has-post-thumbnail';
		}
	}
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( $classes ); ?>>
	<div class="wd-post-inner article-inner">
		<?php if ( ( has_post_thumbnail() || ! empty( $gallery['images_id'] ) ) && ! post_password_required() && ! is_attachment() && woodmart_loop_prop( 'parts_media' ) ) : ?>
			<div class="wd-post-thumb entry-header<?php echo esc_attr( $thumb_classes ); ?>">
				<?php if ( 'gallery' === $post_format && $gallery_slider && ! empty( $gallery['images_id'] ) ) : ?>
					<?php
					woodmart_enqueue_js_library( 'swiper' );
					woodmart_enqueue_js_script( 'swiper-carousel' );
					woodmart_enqueue_inline_style( 'swiper' );
					?>
					<div class="wd-carousel-inner<?php echo esc_attr( $gallery_inner_classes ); ?>">
						<div class="wd-carousel wd-grid"<?php echo woodmart_get_carousel_attributes( array( 'slides_per_view' => 1, 'autoheight' => 'yes' ) ); //phpcs:ignore ?>>
							<div class="wd-carousel-wrap">
								<?php
								foreach ( $gallery['images_id'] as $image_id ) {
									?>
									<div class="wd-carousel-item">
										<?php echo woodmart_otf_get_image_html( $image_id, apply_filters( 'woodmart_gallery_post_format_size', 'large' ) ); ?>
									</div>
									<?php
								}
								?>
							</div>
						</div>
						<?php woodmart_get_carousel_nav_template( ' wd-post-arrows wd-pos-sep wd-custom-style' ); ?>
					</div>
				<?php else : ?>
					<div class="wd-post-img post-img-wrapp">
						<?php echo woodmart_get_post_thumbnail( 'large' ); ?>
					</div>
					<a class="wd-fill" href="<?php echo esc_url( get_permalink() ); ?>"></a>
				<?php endif; ?>

				<?php if ( woodmart_loop_prop( 'parts_published_date', true ) ) : ?>
					<?php woodmart_post_date(); ?>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<div class="wd-post-content article-body-container">
			<?php if ( woodmart_loop_prop( 'parts_meta' ) && get_the_category_list( ', ' ) ) : ?>
				<div class="wd-post-cat wd-style-with-bg meta-post-categories">
					<?php echo wp_kses( get_the_category_list( ', ' ), true ); ?>
				</div>
			<?php endif ?>

			<?php if ( woodmart_loop_prop( 'parts_title' ) ) : ?>
				<h3 class="wd-post-title wd-entities-title title post-title">
					<a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark"><?php the_title(); ?></a>
				</h3>
			<?php endif; ?>

			<?php if ( woodmart_loop_prop( 'parts_meta' ) ) : ?>
				<div class="wd-post-meta">
					<?php if ( is_sticky() ) : ?>
						<div class="wd-featured-post"></div>
					<?php endif; ?>
					<?php woodmart_enqueue_inline_style( 'blog-mod-author' ); ?>
					<div class="wd-post-author">
						<?php woodmart_post_meta_author( true, 'long' ); ?>
					</div>
					<div class="wd-modified-date">
						<?php woodmart_post_modified_date(); ?>
					</div>

					<?php if ( woodmart_is_social_link_enable( 'share' ) ) : ?>
						<div class="wd-post-share wd-tltp">
							<div class="tooltip top">
								<div class="tooltip-inner">
									<?php
									if ( function_exists( 'woodmart_shortcode_social' ) ) {
										echo woodmart_shortcode_social( // phpcs:ignore
											array(
												'size'  => 'small',
												'color' => 'light',
											)
										);}
									?>
								</div>
								<div class="tooltip-arrow"></div>
							</div>
						</div>
					<?php endif ?>

					<?php if ( comments_open() ) : ?>
						<?php woodmart_enqueue_inline_style( 'blog-mod-comments-button' ); ?>
						<div class="wd-post-reply wd-style-1">
							<?php woodmart_post_meta_reply(); ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( is_search() && get_the_excerpt() && woodmart_loop_prop( 'parts_text' ) && 'gallery' !== get_post_format() ) : // Only display Excerpts for Search. ?>
				<div class="entry-summary">
					<?php echo get_the_excerpt(); //phpcs:ignore ?>
				</div>
			<?php else : ?>
				<?php if ( $has_excerpt && $hide_condition ) : ?>
					<div class="wd-post-excerpt entry-content<?php echo woodmart_get_old_classes( ' woodmart-entry-content' ); //phpcs:ignore. ?>">
						<?php
							echo $blog_excerpt; //phpcs:ignore.

							wp_link_pages(
								array(
									'before'      => '<div class="page-links"><span class="page-links-title">' . esc_html__( 'Pages:', 'woodmart' ) . '</span>',
									'after'       => '</div>',
									'link_before' => '<span>',
									'link_after'  => '</span>',
								)
							);
						?>
					</div>
				<?php endif; ?>

				<?php if ( 'full' !== woodmart_get_opt( 'blog_excerpt' ) && woodmart_loop_prop( 'parts_btn' ) && $hide_condition ) : ?>
					<?php woodmart_render_read_more_btn( 'link' ); ?>
				<?php endif; ?>
			<?php endif; ?>
		</div>
	</div>
</article>

<?php
// Increase loop count.
woodmart_set_loop_prop( 'woodmart_loop', $woodmart_loop + 1 );
