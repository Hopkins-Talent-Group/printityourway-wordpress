<?php
/**
 * Post comments shortcode.
 *
 * @package Woodmart
 */

use XTS\Modules\Layouts\Main;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'woodmart_shortcode_single_post_comments' ) ) {
	/**
	 * Post comments shortcode.
	 *
	 * @param array $settings Shortcode settings.
	 * @return string Shortcode output.
	 */
	function woodmart_shortcode_single_post_comments( $settings ) {
		$default_settings = array(
			'css'             => '',
			'wrapper_classes' => '',
			'el_id'           => '',
			'is_wpb'          => true,
		);

		$settings        = wp_parse_args( $settings, $default_settings );
		$wrapper_classes = $settings['wrapper_classes'];
		$el_id           = $settings['el_id'];

		if ( $settings['is_wpb'] && 'wpb' === woodmart_get_current_page_builder() ) {
			$wrapper_classes  = apply_filters( 'vc_shortcodes_css_class', '', '', $settings );
			$wrapper_classes .= ' wd-wpb';

			if ( $settings['css'] ) {
				$wrapper_classes .= ' ' . vc_shortcode_custom_css_class( $settings['css'] );
			}
		}

		ob_start();

		Main::setup_preview();

		if ( post_password_required() ) {
			return;
		}

		woodmart_enqueue_inline_style( 'single-post-el-comments' );

		$post_id       = get_the_ID();
		$comments      = get_comments_number( $post_id );
		$comment_order = get_option( 'comment_order', 'asc' );
		$comment_args  = array(
			'post_id' => get_the_ID(),
			'orderby' => 'comment_date_gmt',
			'order'   => strtoupper( $comment_order ),
			'status'  => 'approve',
		);

		if ( is_user_logged_in() ) {
			$comment_args['include_unapproved'] = array( get_current_user_id() );
		} else {
			$unapproved_email = wp_get_unapproved_comment_author_email();

			if ( $unapproved_email ) {
				$comment_args['include_unapproved'] = array( $unapproved_email );
			}
		}

		$comments_list = get_comments( $comment_args );

		if ( $comments > 0 ) : ?>
			<?php woodmart_enqueue_inline_style( 'post-types-mod-comments' ); ?>
			<div
			<?php if ( $el_id ) : ?>
			id="<?php echo esc_attr( $el_id ); ?>"
			<?php endif; ?>
			class="wd-single-post-comments<?php echo esc_attr( $wrapper_classes ); ?>">
				<div class="wd-post-comments comments-area wd-style-1">
					<h2 class="comments-title">
						<?php
							printf(
								wp_kses( _nx( 'One thought on &ldquo;%2$s&rdquo;', '%1$s thoughts on &ldquo;%2$s&rdquo;', $comments, 'comments title', 'woodmart' ), array() ),
								number_format_i18n( $comments ),
								'<span>' . get_the_title() . '</span>'
							);
						?>
					</h2>
			
					<ol class="comment-list">
						<?php
							wp_list_comments(
								array(
									'style'       => 'ol',
									'short_ping'  => true,
									'avatar_size' => 74,
								),
								$comments_list
							);
						?>
					</ol>
			
					<?php
					if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
						?>
					<nav class="navigation comment-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Comment navigation', 'woodmart' ); ?>">
						<h1 class="screen-reader-text section-heading"><?php esc_html_e( 'Comment navigation', 'woodmart' ); ?></h1>
						<div class="nav-previous"><?php previous_comments_link( esc_html__( '&larr; Older Comments', 'woodmart' ) ); ?></div>
						<div class="nav-next"><?php next_comments_link( esc_html__( 'Newer Comments &rarr;', 'woodmart' ) ); ?></div>
					</nav>
					<?php endif; ?>
			
					<?php if ( ! comments_open() && get_comments_number() ) : ?>
						<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'woodmart' ); ?></p>
					<?php endif; ?>
				</div>
			</div>
			<?php
		endif;
		Main::restore_preview();

		return ob_get_clean();
	}
}
