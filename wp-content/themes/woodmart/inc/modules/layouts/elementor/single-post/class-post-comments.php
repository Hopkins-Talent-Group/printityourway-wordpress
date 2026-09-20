<?php
/**
 * Single post/project comments map.
 *
 * @package Woodmart
 */

namespace XTS\Modules\Layouts;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Plugin;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Elementor widget that inserts an embeddable content into the page, from any given URL.
 */
class Post_Comments extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'wd_post_comments';
	}

	/**
	 * Get widget content.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Post comments', 'woodmart' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'wd-icon-post-comments';
	}

	/**
	 * Get widget categories.
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( 'wd-posts-elements' );
	}

	/**
	 * Show in panel.
	 *
	 * @return bool Whether to show the widget in the panel or not.
	 */
	public function show_in_panel() {
		return Main::is_layout_type( 'single_post' );
	}

	/**
	 * Register the widget controls.
	 */
	protected function register_controls() {
		/**
		 * Style tab.
		 */

		/**
		 * General settings.
		 */
		$this->start_controls_section(
			'general_style_section',
			array(
				'label' => esc_html__( 'Title', 'woodmart' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'css_classes',
			array(
				'type'         => 'wd_css_class',
				'default'      => 'wd-single-post-comments',
				'prefix_class' => '',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'label'    => esc_html__( 'Typography', 'woodmart' ),
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .comments-title',
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'Color', 'woodmart' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .comments-title' => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render the widget output on the frontend.
	 */
	protected function render() {
		Main::setup_preview();

		if ( post_password_required() ) {
			return;
		}

		$post_id       = get_the_ID();
		$comments      = get_comments_number( $post_id );
		$comment_order = get_option( 'comment_order', 'asc' );
		$comment_args  = array(
			'post_id' => get_the_ID(),
			'orderby' => 'comment_date_gmt',
			'order'   => strtoupper( $comment_order ),
			'status'  => 'approve',
		);

		woodmart_enqueue_inline_style( 'single-post-el-comments' );

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
			<?php
		endif;
		Main::restore_preview();
	}
}

Plugin::instance()->widgets_manager->register( new Post_Comments() );
