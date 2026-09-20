<?php
/**
 * Guide tours class.
 *
 * @package woodmart
 */

namespace XTS\Admin\Modules\Guide_Tour;

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

/**
 * Guide tours class.
 */
class Main {
	/**
	 * Guide tours.
	 *
	 * @var array
	 */
	public $guide_tours = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		if ( ! is_user_logged_in() || ! current_user_can( 'administrator' ) ) {
			return;
		}

		add_action( 'woodmart_after_welcome_box_content', array( $this, 'render_guide_tours' ) );

		add_action( 'init', array( $this, 'set_guides' ) );
		add_action( 'init', array( $this, 'save_complete_tour' ) );

		if ( empty( $_GET['wd_tour'] ) && empty( $_COOKIE['woodmart_guide_tour'] ) ) { // phpcs:ignore
			return;
		}

		add_action( 'admin_footer', array( $this, 'render_navigation_tours' ) );
		add_action( 'wp_footer', array( $this, 'render_navigation_tours' ) );
		add_action( 'elementor/editor/after_enqueue_scripts', array( $this, 'render_navigation_tours' ) );
		add_action( 'elementor/editor/before_enqueue_scripts', 'woodmart_enqueue_admin_scripts' );

		add_action( 'woodmart_admin_localized_string_array', array( $this, 'add_tour_settings' ) );
		add_action( 'woodmart_localized_string_array', array( $this, 'add_tour_settings' ) );

		add_filter( 'body_class', array( $this, 'add_body_class' ) );
		add_filter( 'admin_body_class', array( $this, 'add_body_class' ) );
	}

	/**
	 * Set guides.
	 *
	 * @return void
	 */
	public function set_guides() {
		if ( ( empty( $_GET['wd_tour'] ) && empty( $_COOKIE['woodmart_guide_tour'] ) ) && ( ! isset( $_GET['page'] ) || 'xts_dashboard' !== $_GET['page'] ) ) { // phpcs:ignore
			return;
		}

		$this->guide_tours = require dirname( __FILE__ ) . '/configs.php';
	}

	/**
	 * @return void
	 */
	public function save_complete_tour() {
		if ( empty( $_GET['wd_guide_done'] ) ) { // phpcs:ignore
			return;
		}

		$tour_id        = absint( $_GET['wd_guide_done'] ); // phpcs:ignore
		$complete_tours = get_option( 'woodmart_guide_tours_complete', array() );

		if ( ! in_array( $tour_id, $complete_tours, true ) ) {
			$complete_tours[] = $tour_id;
		}

		update_option( 'woodmart_guide_tours_complete', $complete_tours );
	}

	/**
	 * Render guide tours.
	 *
	 * @return void
	 */
	public function render_guide_tours() {
		if ( ! is_admin() || ! $this->guide_tours ) {
			return;
		}

		$complete_tours = get_option( 'woodmart_guide_tours_complete', array() );

		if ( ! empty( $_GET['wd_tour'] ) && $complete_tours && in_array( absint( $_GET['wd_tour'] ), $complete_tours ) ) {
			$complete_tours = array_diff( $complete_tours, array( $_GET['wd_tour'] ) );
		}

		?>
		<div class="xts-col-12 xts-col-xl-6">
			<div class="xts-box xts-info-boxes xts-guide xts-theme-style">
				<div class="xts-box-content">
					<div class="xts-guide-heading xts-row xts-sp-10">
						<div class="xts-col">
							<h4>
								<?php esc_html_e( 'Customize your website', 'woodmart' ); ?>
							</h4>
							<p>
								<?php esc_html_e( 'Customize your website with the help of our interactive guides. Click the buttons below to start exploring and customizing with ease.', 'woodmart' ); ?>
							</p>
						</div>
						<div class="xts-col-auto">
							<img src="<?php echo esc_url( WOODMART_ASSETS_IMAGES . '/dashboard/guide.jpg' ); ?>" alt="guide tour">
						</div>
					</div>

					<div class="xts-guide-tours">
						<?php foreach ( $this->guide_tours as $index => $tour ) : ?>
							<?php
							if ( isset( $tour['requires'] ) && ! $tour['requires'] ) {
								continue;
							}

							$classes = 'xts-guide-tour';

							if ( in_array( $index, $complete_tours, true ) ) {
								$classes .= ' xts-done';
							}

							?>
							<div class="<?php echo esc_attr( $classes ); ?>">
								<div class="xts-guide-tour-title">
									<h5>
										<?php echo esc_html( $tour['title'] ); ?>
									</h5>
									<?php if ( ! empty( $tour['description'] ) ) : ?>
										<p>
											<?php echo esc_html( $tour['description'] ); ?>
										</p>
									<?php endif; ?>
								</div>
								<?php if ( in_array( $index, $complete_tours, true ) ) : ?>
									<a href="<?php echo esc_url( add_query_arg( 'wd_tour', $index, $tour['url'] ) ); ?>" class="xts-btn-inline xts-color-primary xts-i-round-left">
										<?php esc_html_e( 'Restart the guide', 'woodmart' ); ?>
									</a>
								<?php else : ?>
									<a href="<?php echo esc_url( add_query_arg( 'wd_tour', $index, $tour['url'] ) ); ?>" class="xts-btn-inline xts-color-primary">
										<?php esc_html_e( 'Start the guide', 'woodmart' ); ?>
									</a>
							<?php endif; ?>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render navigation tours.
	 *
	 * @return void
	 */
	public function render_navigation_tours() {
		if ( ( empty( $_GET['wd_tour'] ) && empty( $_COOKIE['woodmart_guide_tour'] ) ) || ! $this->guide_tours ) { // phpcs:ignore
			return;
		}

		$tour_id            = 0;
		$current_step_index = 0;
		$collapse           = false;

		if ( ! empty( $_GET['wd_tour'] ) ) {
			$tour_id = absint( $_GET['wd_tour'] );

			$complete_tours = get_option( 'woodmart_guide_tours_complete', array() );

			if ( in_array( $tour_id, $complete_tours, true ) ) {
				$complete_tours = array_diff( $complete_tours, array( $tour_id ) );

				update_option( 'woodmart_guide_tours_complete', $complete_tours );
			}
		} elseif ( ! empty( $_COOKIE['woodmart_guide_tour'] ) ) {
			$raw_value = json_decode( stripslashes( $_COOKIE['woodmart_guide_tour'] ), true );

			$tour_id            = $raw_value['tour_id'] ?? 0;
			$current_step_index = $raw_value['step'] ?? $current_step_index;
			$collapse           = isset( $raw_value['collapse'] ) ? $raw_value['collapse'] : false;
		}

		if ( ! $tour_id || ! isset( $this->guide_tours[ $tour_id ] ) || ( isset( $this->guide_tours['requires'] ) && ! $this->guide_tours['requires'] ) ) {
			return;
		}

		if ( ! wp_script_is( 'js-cookie' ) ) {
			wp_enqueue_script( 'js-cookie', WOODMART_SCRIPTS . '/libs/cookie.min.js', array(), WOODMART_VERSION, true );
		}

		wp_enqueue_style( 'wd-admin-base', WOODMART_ASSETS . '/css/parts/base.min.css', array(), WOODMART_VERSION );
		wp_enqueue_style( 'wd-guide-tour', WOODMART_ASSETS . '/css/parts/lib-driver.min.css', array(), WOODMART_VERSION );

		wp_enqueue_script( 'wd-guide-tour-library', WOODMART_ASSETS . '/js/libs/guide-tour.min.js', array(), WOODMART_VERSION, true );

		if ( isset( $_GET['elementor-preview'] ) ) { // phpcs:ignore
			return;
		}

		wp_enqueue_script( 'wd-guide-tour', WOODMART_ASSETS . '/js/guideTour.js', array(), WOODMART_VERSION, true );

		$guide_tour = $this->guide_tours[ $tour_id ];
		$steps      = array();

		foreach ( $guide_tour['steps'] as $index => $raw_step ) {
			$item_class = '';

			if ( $index === $current_step_index ) {
				$item_class = 'xts-active';
			} elseif ( $index < $current_step_index ) {
				$item_class = 'xts-done';
			}

			$steps[ $raw_step['step'] ][] = array(
				'title' => $raw_step['title'],
				'class' => $item_class,
			);
		}

		$progress = round( ( $current_step_index / count( $guide_tour['steps'] ) ) * 100 );

		?>
			<div class="xts-tour-navigation<?php echo esc_attr( $collapse ? ' xts-collapse' : '' ); ?>">
				<div class="xts-tour-heading">
					<h4>
						<span class="xts-tour-title">
							<?php echo esc_html( $guide_tour['title'] ); ?>
						</span>
						<span class="xts-step-title">
							<?php if ( isset( $guide_tour['steps'][ $current_step_index ] ) ) : ?>
								<?php echo esc_html( $guide_tour['steps'][ $current_step_index ]['title'] ); ?>
							<?php endif; ?>
						</span>
					</h4>
					<a href="#" class="xts-tour-collapse">
						<span class="dashicons dashicons-arrow-down-alt2"></span>
					</a>
					<a href="#" class="xts-tour-close">
						<span class="dashicons dashicons-no-alt"></span>
					</a>
				</div>
				<div class="xts-tour-progress">
					<div class="xts-tour-progress-bar" style="width: <?php echo esc_attr( $progress ); ?>%"></div>
				</div>
				<div class="xts-tour-steps">
					<?php foreach ( $steps as $index => $step_group ) : ?>
						<?php
						$step_classes = '';
						$all_classes  = wp_list_pluck( $step_group, 'class' );

						if ( in_array( 'xts-active', $all_classes, true ) ) {
							$step_classes .= ' xts-active xts-open';
						} elseif ( count( $all_classes ) && count( array_unique( $all_classes ) ) === 1 && 'xts-done' === $all_classes[0] ) {
							$step_classes .= ' xts-done';
						}
						?>
						<div class="xts-tour-step<?php echo esc_attr( $step_classes ); ?>">
							<div class="xts-step-heading">
								<span><?php echo esc_html__( 'Step', 'woodmart' ) . ' ' . esc_html( $index ) . ':'; ?></span>
							</div>
							<div class="xts-step-content">
								<ul>
									<?php foreach ( $step_group as $step ) : ?>
										<li class="<?php echo esc_attr( $step['class'] ); ?>">
											<?php echo esc_html( $step['title'] ); ?>
										</li>
									<?php endforeach; ?>
								</ul>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		<?php
	}

	/**
	 * Add tour settings.
	 *
	 * @param array $settings Localized settings.
	 * @return array
	 */
	public function add_tour_settings( $settings ) {
		if ( ( empty( $_GET['wd_tour'] ) && empty( $_COOKIE['woodmart_guide_tour'] ) ) || ! $this->guide_tours ) { // phpcs:ignore
			return $settings;
		}

		$tour_id            = 0;
		$current_step_index = 0;

		if ( ! empty( $_GET['wd_tour'] ) ) { // phpcs:ignore
			$tour_id = esc_html( $_GET['wd_tour'] ); // phpcs:ignore
		} elseif ( ! empty( $_COOKIE['woodmart_guide_tour'] ) ) {
			$raw_value = json_decode( stripslashes( $_COOKIE['woodmart_guide_tour'] ), true );

			$tour_id            = $raw_value['tour_id'] ?? 0;
			$current_step_index = $raw_value['step'] ?? $current_step_index;
		}

		if ( ! $tour_id || empty( $this->guide_tours[ $tour_id ] ) || empty( $this->guide_tours[ $tour_id ]['steps'] ) || ( isset( $this->guide_tours['requires'] ) && ! $this->guide_tours['requires'] ) ) {
			if ( ! empty( $_COOKIE['woodmart_guide_tour'] ) ) {
				setcookie( 'woodmart_guide_tour', '', 0, COOKIEPATH, COOKIE_DOMAIN, woodmart_cookie_secure_param(), false );
				$_COOKIE['woodmart_guide_tour'] = '';
			}

			return $settings;
		}

		$steps          = $this->guide_tours[ $tour_id ]['steps'];
		$max_step_index = end( $steps )['step'];

		$settings['guide_next_text'] = esc_html__( 'Next', 'woodmart' );
		$settings['guide_back_text'] = esc_html__( 'Back', 'woodmart' );
		$settings['guide_done_text'] = esc_html__( 'Done', 'woodmart' );
		$settings['guide_url_end']   = admin_url( 'admin.php?page=xts_dashboard' );

		foreach ( $steps as $index => $item ) {
			$show_buttons = array();

			if ( ! in_array( $item['type'], array( 'button', 'hover' ), true ) ) {
				$show_buttons[] = 'next';
			} else if ( isset( $steps[ $index - 1 ] ) && ! in_array( $steps[ $index - 1 ]['type'], array( 'button', 'hover' ), true ) ) {
				$show_buttons[] = 'previous';
			}

			$settings['guide_tour'][] = array(
				'element'                  => $item['selector'],
				'step'                     => $index,
				'disableActiveInteraction' => ! in_array( $item['type'], array( 'button', 'hover' ), true ) && empty( $item['allow_click'] ),
				'isDone'                   => isset( $item['is_done'] ) ? $item['is_done'] : false,
				'skipIf'                   => isset( $item['skip_if'] ) ? $item['skip_if'] : false,
				'type'                     => $item['type'],
				'offset'                   => isset( $item['offset'] ) ? $item['offset'] : '',
				'popover'                  => array(
					'title'        => $item['title'],
					'description'  => $item['description'],
					'showButtons'  => $show_buttons,
					'progressText' => $item['step'] . ' ' . esc_html__( 'of', 'woodmart' ) . ' ' . $max_step_index,
					'popoverClass' => ! empty( $item['allow_click'] ) ? 'xts-step-optional' : '',
				),
			);
		}

		$value = wp_json_encode(
			array(
				'tour_id' => $tour_id,
				'step'    => $current_step_index,
			)
		);

		if ( empty( $_COOKIE['woodmart_guide_tour'] ) ) {
			setcookie( 'woodmart_guide_tour', $value, 0, COOKIEPATH, COOKIE_DOMAIN, woodmart_cookie_secure_param(), false );
			$_COOKIE['woodmart_guide_tour'] = $value;
		}

		return $settings;
	}

	/**
	 * Add body classes.
	 *
	 * @param array|string $body_classes Body classes.
	 * @return array|string
	 */
	public function add_body_class( $body_classes ) {
		if ( ! isset( $_GET['elementor-preview'] ) && ( ! empty( $_COOKIE['woodmart_guide_tour'] ) || ! empty( $_GET['wd_tour'] ) ) ) { // phpcs:ignore
			$tour_id            = 0;
			$current_step_index = 0;
			$classes            = '';

			if ( ! empty( $_COOKIE['woodmart_guide_tour'] ) ) {
				$raw_value = json_decode( stripslashes( $_COOKIE['woodmart_guide_tour'] ), true ); // phpcs:ignore

				$tour_id            = $raw_value['tour_id'] ?? 0;
				$current_step_index = $raw_value['step'] ?? 0;
			}

			if ( ! empty( $_GET['wd_tour'] ) ) { // phpcs:ignore
				$tour_id = absint( $_GET['wd_tour'] ); // phpcs:ignore
			}

			$classes .= 'wd-guide-tour-' . $tour_id;
			$classes .= ' wd-guide-step-' . $current_step_index;

			if ( is_array( $body_classes ) ) {
				$body_classes[] = $classes;
			} else {
				$body_classes .= ' ' . $classes;
			}
		}

		return $body_classes;
	}
}

new Main();
