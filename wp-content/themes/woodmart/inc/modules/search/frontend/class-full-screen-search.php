<?php
/**
 * The main search class.
 *
 * @package woodmart
 */

namespace XTS\Modules\Search\Frontend;

/**
 * The full screen search form class.
 */
class Full_Screen_Search extends Search_Form {
	/**
	 * The instance of the class.
	 *
	 * @var Full_Screen_Search
	 */
	protected static $instance = null;

	/**
	 * The arguments for the search form.
	 *
	 * @var array
	 */
	protected static $device_args = array(
		'desktop' => array(),
		'mobile'  => array(),
	);

	/**
	 * Search type.
	 *
	 * @var string
	 */
	public $search_type = 'full-screen';

	/**
	 * The constructor.
	 */
	private function __construct() {
		if ( woodmart_is_header_frontend_editor() ) {
			add_action( 'whb_after_header', array( $this, 'render_instance' ) );
		} else {
			add_action( 'woodmart_before_wp_footer', array( $this, 'render_instance' ), 100 );
		}
	}

	/**
	 * Create the instance of the class and add form args.
	 *
	 * @param array $args The arguments for the search form.
	 *
	 * @return void
	 */
	public static function add_args( $args ) {
		$device = $args['device'] ? $args['device'] : 'desktop';

		self::$device_args[ $device ] = $args;

		if ( null === self::$instance ) {
			self::$instance = new self();
		}
	}

	/**
	 * Checks if at least one option is active for a mobile or desktop device and returns true if so.
	 *
	 * @param string $key The key of the option to check.
	 *
	 * @return bool
	 */
	protected function get_enabled_option( $key ) {
		$desktop_value = self::$device_args['desktop'][ $key ] ? self::$device_args['desktop'][ $key ] : null;
		$mobile_value  = self::$device_args['mobile'][ $key ] ? self::$device_args['mobile'][ $key ] : null;

		return $desktop_value || $mobile_value;
	}

	/**
	 * Returns the visibility class based on the device arguments.
	 *
	 * @param string $key The key of the option to check.
	 *
	 * @return string
	 */
	protected function get_visibility_class( $key ) {
		$desktop_value = self::$device_args['desktop'][ $key ] ? self::$device_args['desktop'][ $key ] : null;
		$mobile_value  = self::$device_args['mobile'][ $key ] ? self::$device_args['mobile'][ $key ] : null;

		if ( $desktop_value === $mobile_value ) {
			return '';
		}

		if ( ! empty( self::$device_args['mobile'] ) && $desktop_value && ! $mobile_value ) {
			return ' wd-hide-md';
		}

		if ( ! empty( self::$device_args['desktop'] ) && ! $desktop_value && $mobile_value ) {
			return ' wd-hide-lg';
		}

		return '';
	}

	/**
	 * Returns the arguments for the search form.
	 *
	 * @param array $args The arguments for the search form.
	 *
	 * @return array The arguments for the search form.
	 */
	public function get_arguments( $args = array() ) {
		$desktop_type = isset( self::$device_args['desktop']['type'] ) ? self::$device_args['desktop']['type'] : 'full-screen';
		$mobile_type  = isset( self::$device_args['mobile']['type'] ) ? self::$device_args['mobile']['type'] : 'full-screen';

		if ( ! empty( self::$device_args['desktop'] ) && ! empty( self::$device_args['mobile'] ) && $desktop_type === $mobile_type ) {
			$extra_content = array();

			if ( ! empty( self::$device_args['desktop']['search_extra_content'] ) ) {
				$extra_content['desktop'] = self::$device_args['desktop']['search_extra_content'];
			}

			if ( ! empty( self::$device_args['mobile']['search_extra_content'] ) ) {
				$extra_content['mobile'] = self::$device_args['mobile']['search_extra_content'];
			}

			$args['search_history_enabled']          = $this->get_enabled_option( 'search_history_enabled' );
			$args['popular_requests']                = $this->get_enabled_option( 'popular_requests' );
			$args['search_history_custom_classes']   = $this->get_visibility_class( 'search_history_enabled' );
			$args['popular_requests_custom_classes'] = $this->get_visibility_class( 'popular_requests' );
			$args['search_extra_content']            = $extra_content;
		}

		if ( isset( $args['type'] ) && 'full-screen-2' === $args['type'] ) {
			$args['search_style'] = 'with-bg';
		}

		$args = parent::get_arguments( $args );

		return $args;
	}

	/**
	 * Get css classes for main search wrapper.
	 *
	 * @return string
	 */
	public function get_wrapper_classes() {
		$wrapper_classes  = 'wd-scroll wd-fill';
		$wrapper_classes .= parent::get_wrapper_classes();

		return $wrapper_classes;
	}

	/**
	 * Get additional search text.
	 *
	 * @return string
	 */
	public function get_description() {
		$description = '';

		switch ( $this->args['post_type'] ) {
			case 'product':
				$description = esc_html__( 'Start typing to see products you are looking for.', 'woodmart' );
				break;
			case 'portfolio':
				$description = esc_html__( 'Start typing to see projects you are looking for.', 'woodmart' );
				break;
			case 'page':
				$description = esc_html__( 'Start typing to see pages you are looking for.', 'woodmart' );
				break;
			default:
				$description = esc_html__( 'Start typing to see posts you are looking for.', 'woodmart' );
				break;
		}

		return $description;
	}

	/**
	 * Get css classes for search results dropdown.
	 *
	 * @return string
	 */
	public function get_dropdowns_classes() {
		$dropdowns_classes  = ' wd-scroll-content';
		$dropdowns_classes .= parent::get_dropdowns_classes();

		return $dropdowns_classes;
	}

	/**
	 * Get arguments that will be used in the template for rendering.
	 *
	 * @return array
	 */
	public function get_render_args() {
		$render_args = array(
			'description' => $this->get_description(),
		);

		$render_args = array_merge( $render_args, parent::get_render_args() );

		return $render_args;
	}

	/**
	 * Enqueue scripts and styles.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		woodmart_enqueue_js_script( 'search-full-screen' );
		woodmart_enqueue_inline_style( 'header-search-fullscreen' );

		if ( 'full-screen' === $this->args['type'] ) {
			woodmart_enqueue_inline_style( 'header-search-fullscreen-1' );
		} else {
			woodmart_enqueue_inline_style( 'header-search-fullscreen-2' );
		}

		parent::enqueue_scripts();
	}

	/**
	 * Render search form.
	 *
	 * @return void
	 */
	public function render() {
		$desktop_args = self::$device_args['desktop'] ? self::$device_args['desktop'] : null;
		$mobile_args  = self::$device_args['mobile'] ? self::$device_args['mobile'] : null;
		$args         = array();

		if ( ! empty( $desktop_args ) && ! empty( $mobile_args ) && $desktop_args['type'] !== $mobile_args['type'] ) {
			foreach ( self::$device_args as $args ) {
				$this->args = $this->get_arguments( $args );
				parent::render();
			}
		} else {
			if ( ! empty( $desktop_args ) ) {
				$args = $desktop_args;
			} elseif ( ! empty( $mobile_args ) ) {
				$args = $mobile_args;
			}

			$this->args = $this->get_arguments( $args );

			parent::render();
		}
	}

	/**
	 * Render the instance of the class.
	 *
	 * @return void
	 */
	public static function render_instance() {
		if ( null !== self::$instance ) {
			self::$instance->render();
		}
	}
}
