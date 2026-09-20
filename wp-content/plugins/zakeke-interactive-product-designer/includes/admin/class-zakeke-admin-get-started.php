<?php

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class Zakeke_Admin_Get_Started {


	/**
	 * Setup class.
	 */
	public static function init() {
		add_action('admin_init', array(__CLASS__, 'redirect'));
		add_action('admin_menu', array(__CLASS__, 'add_get_started_submenu'));
	}

	public static function activate() {
		global $wp_rewrite;
		add_option('zakeke_do_activation_redirect', true);
		$wp_rewrite->flush_rules(false);
	}

	/**
	 * Redirects the plugin to the about page after the activation
	 */
	public static function redirect() {
		if (get_option('zakeke_do_activation_redirect', false)) {
			delete_option('zakeke_do_activation_redirect');
			wp_redirect(admin_url('admin.php?page=zakeke-about'));
		}
	}

	/**
	 * Builds all the plugin menu and submenu
	 */
	public static function add_get_started_submenu() {
		add_menu_page(
			__('Get Started', 'zakeke'),
			__('Zakeke Product Designer', 'zakeke'),
			'manage_product_terms', 'zakeke-about',
			array(__CLASS__, 'get_about_page'),
			get_zakeke()->plugin_url() . '/assets/images/zakeke_icon.png'
		);
	}

	/**
	 * Check if permalinks are enabled
	 *
	 * @return bool
	 */
	private static function check_permalinks() {
		$permalinks = get_option('permalink_structure', false);
		return $permalinks && strlen($permalinks) > 0;
	}

	/**
	 * Check if the store is on a local machine
	 *
	 * @return bool
	 */
	private static function check_localhost() {
		return !strpos(get_site_url(), 'localhost');
	}

	/**
	 * Check if the store site url is correctly set to be in https if the store is using https
	 *
	 * @return bool
	 */
	private static function check_https() {
		if (is_ssl()) {
			return strpos(get_option('siteurl'), 'https://') !== false && strpos(get_option('home'), 'https://') !== false;
		}
		return true;
	}

	private static function get_issues() {
		$issues = array();
		if (!self::check_permalinks()) {
			$issues[] = __('Permalinks must be enabled: go to WordPress Admin Panel > Settings > Permalinks > Custom Settings: make sure that the option is not set on “Plain” (all other options are fine).', 'zakeke');
		}
		if (!self::check_localhost()) {
			$issues[] = 'You can\'t connect to Zakeke from localhost. Zakeke needs to be able reach your site to establish a connection.';
		}
		if (!self::check_https()) {
			$issues[] = 'Your site is using https, but your site url is set to be in http. Please update your site url to be in https by going to: WordPress admin > Settings > General.';
		}

		return $issues;
	}

	/**
	 * Start the WC integration
	 *
	 * @return string
	 */
	private static function get_zakeke_start_url() {
		return ZAKEKE_ADMIN_URL . '/Admin/E-Commerce/WooCommerce/Start?storeUrl=' . urlencode(trailingslashit(get_site_url()));
	}

	private static function render_connect() {
		$issues = self::get_issues();
		?>
        <div class="wrap" style="max-width: 1200px;margin-top: 20px">

            <div style="background-color: #fff;border-left: 4px solid #405761;padding: 45px 20px 20px 30px;position: relative;overflow: hidden;">
                <div class="text">
                    <img style="width:180px;"
                         src="<?php echo esc_url(get_zakeke()->plugin_url() . '/assets/images/zakeke_logo.png'); ?>">

                    <h2 style="font-size: 24px;line-height: 29px;position: relative;">
						<?php esc_html_e('Connect to Zakeke', 'zakeke'); ?>
                    </h2>
                    <p style="font-size: 16px;margin-bottom: 30px;position: relative;">
                        You're almost done! Just 2 more steps to have your WooCommerce store connected to Zakeke.
                    </p>

					<?php if (!empty($issues)) { ?>
                        <h3><?php esc_html_e('To connect your store to Zakeke, fix the following issues:', 'zakeke'); ?></h3>
                        <div style="background: #fff; border-left: 4px solid #dc3232; padding: 1px 12px; margin: 10px 0 20px 0; box-shadow: 0 1px 1px 0 rgba(0,0,0,.1); line-height: 1.5;">
                            <ul>
								<?php
								foreach ($issues as $issue) {
									echo '<li>' . wp_kses_post($issue) . '</li>';
								}
								?>
                            </ul>
                        </div>

                        <p><a
                                    class="button button-primary"
                                    style="background-color: #405761;border-width: 0;box-shadow: none;border-radius: 3px;color: #fff;height: auto;padding: 3px 14px;text-align: center;white-space: normal !important;height: 37px;line-height: 37px;min-width: 124px;padding: 0 13px;text-shadow: none;"
                                    href="#"
                                    onclick="window.location.reload(); return false;">
								<?php esc_html_e('Check again', 'zakeke'); ?>
                            </a></p>
					<?php } ?>


                    <p><a
                                class="button button-primary"
                                style="background-color: #405761;border-width: 0;box-shadow: none;border-radius: 3px;color: #fff;height: auto;padding: 3px 14px;text-align: center;white-space: normal !important;height: 37px;line-height: 37px;min-width: 124px;padding: 0 13px;text-shadow: none;"
                                href="<?php echo esc_url(self::get_zakeke_start_url()); ?>"
							<?php
							if (!empty($issues)) {
								echo ' disabled onclick="alert(\'Please fix the reported issues first\'); return false;" ';
							}
							?>
                        >
							<?php esc_html_e('Connect', 'zakeke'); ?>
                        </a></p>
                </div>
            </div>
        </div>
		<?php
	}

	private static function render_all_green() {
		?>
        <div class="wrap" style="max-width: 1200px;margin-top: 20px">
            <div style="background-color: #fff;border-left: 4px solid #405761;padding: 45px 20px 20px 30px;position: relative;overflow: hidden;">
                <div class="text">
                    <img style="width:180px;"
                         src="<?php echo esc_url(get_zakeke()->plugin_url() . '/assets/images/zakeke_logo.png'); ?>">

                    <h2 style="font-size: 24px;line-height: 29px;position: relative;">
						<?php esc_html_e('Configure your Zakeke account', 'zakeke'); ?>
                    </h2>
                    <p style="font-size: 16px;margin-bottom: 30px;position: relative;">
                        Go to your Zakeke admin to configure your products and all the settings of the designer.
                    </p>

                    <p
                    ><a
                                class="button button-primary"
                                style="background-color: #405761;border-width: 0;box-shadow: none;border-radius: 3px;color: #fff;height: auto;padding: 3px 14px;text-align: center;white-space: normal !important;height: 37px;line-height: 37px;min-width: 124px;padding: 0 13px;text-shadow: none;"
                                href="https://admin.zakeke.com/Admin/Dashboard">
							<?php esc_html_e('Go to your Zakeke admin', 'zakeke'); ?>
                        </a></p>
                </div>
            </div>
        </div>
		<?php
	}

	/**
	 * Builds the about page
	 */
	public static function get_about_page() {
		$integration = new Zakeke_Integration();

		if (( strlen($integration->get_option('username')) === 0 && strlen($integration->get_option('username')) === 0 )
		    && ( strlen($integration->get_option('client_id')) === 0 && strlen($integration->get_option('secret_key')) === 0 )) {
			self::render_connect();
		} else {
			self::render_all_green();
		}
	}
}

Zakeke_Admin_Get_Started::init();
