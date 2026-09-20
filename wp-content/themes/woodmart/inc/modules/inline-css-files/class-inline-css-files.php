<?php
/**
 * Page css files.
 *
 * @package xts
 */

namespace XTS\Modules;

use XTS\Singleton;

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

/**
 * Page css files.
 */
class Inline_Css_Files extends Singleton {
	/**
	 * Page css files.
	 *
	 * @var array
	 */
	private $css_file_lists = array();

	/**
	 * Hooks.
	 */
	public function init() {
		add_action( 'init', array( $this, 'set_files_configuration' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_page_inline_css' ), 10000 );

		add_action( 'xts_theme_settings_save', array( $this, 'save_files_css_content' ), 10 );

		add_action( 'activated_plugin', array( $this, 'delete_data' ), 10 );
		add_action( 'deactivated_plugin', array( $this, 'delete_data' ), 10 );
	}

	/**
	 * Set files configuration.
	 *
	 * @param bool $all If true, set all files.
	 * @return void
	 */
	public function set_files_configuration( $all = false ) {
		if ( ! woodmart_get_opt( 'inline_critical_css' ) ) {
			return;
		}

		$files = array(
			'style-base',
			'header-base',
		);

		if ( $all || woodmart_woocommerce_installed() ) {
			$files[] = 'woocommerce-base';
		}

		$this->css_file_lists = apply_filters( 'woodmart_inline_file_css_lists', $files, $all );
	}

	/**
	 * Save css content.
	 *
	 * @return void
	 */
	public function save_files_css_content() {
		if ( ! $this->css_file_lists ) {
			return;
		}

		$config = woodmart_get_config( 'css-files' );

		foreach ( $this->css_file_lists as $slug ) {
			if ( ! isset( $config[ $slug ] ) ) {
				continue;
			}

			foreach ( $config[ $slug ] as $file ) {
				if ( isset( $file['wpb_file'] ) && 'wpb' === woodmart_get_current_page_builder() ) {
					$file['file'] = $file['wpb_file'];
				}

				if ( is_rtl() && isset( $file['rtl'] ) ) {
					$file['file'] = $file['file'] . '-rtl';
				}

				$src = WOODMART_THEMEROOT . $file['file'] . '.min.css';
				if ( ! file_exists( $src ) ) {
					continue;
				}

				$file_content = file_get_contents( $src ); // phpcs:ignore WordPress.WP.AlternativeFunctions

				if ( $file_content ) {
					$content = wp_slash( addslashes( trim( $file_content ) ) );

					update_option( 'wd_page_css_content_' . $file['name'], $content, false );
				}
			}
		}

		update_option( 'wd_page_css_content_theme_version', WOODMART_VERSION );
	}

	/**
	 * Delete all saved meta.
	 */
	public function delete_data() {
		$this->set_files_configuration( true );

		if ( ! $this->css_file_lists ) {
			return;
		}

		$config = woodmart_get_config( 'css-files' );

		foreach ( $this->css_file_lists as $slug ) {
			if ( ! isset( $config[ $slug ] ) ) {
				continue;
			}

			foreach ( $config[ $slug ] as $file ) {
				delete_option( 'wd_page_css_content_' . $file['name'] );
			}
		}

		wp_cache_flush();
	}


	/**
	 * Enqueue page inline css.
	 *
	 * @return void
	 */
	public function enqueue_page_inline_css() {
		if ( ! $this->css_file_lists ) {
			return;
		}

		if ( get_option( 'wd_page_css_content_theme_version' ) !== WOODMART_VERSION ) {
			$this->delete_data();

			return;
		}

		$config = woodmart_get_config( 'css-files' );

		foreach ( $this->css_file_lists as $slug ) {
			if ( ! isset( $config[ $slug ] ) ) {
				continue;
			}

			foreach ( $config[ $slug ] as $file ) {
				$content = get_option( 'wd_page_css_content_' . $file['name'] );

				if ( ! $content ) {
					continue;
				}

				wp_deregister_style( 'wd-' . $file['name'] );

				wp_register_style( 'wd-' . $file['name'] . '-file', false, array(), WOODMART_VERSION );
				wp_enqueue_style( 'wd-' . $file['name'] . '-file' );
				wp_add_inline_style( 'wd-' . $file['name'] . '-file', wp_unslash( stripslashes( $content ) ) );
			}
		}
	}
}

Inline_Css_Files::get_instance();
