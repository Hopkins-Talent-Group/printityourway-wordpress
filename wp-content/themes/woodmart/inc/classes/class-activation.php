<?php
/**
 * Activate theme.
 *
 * @package xts
 */

namespace XTS;

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

/**
 * Activate theme.
 */
class Activation {
	private $_api             = null;
	private $_notices         = null;

	function __construct() {
		$this->_api     = Registry::getInstance()->api;
		$this->_notices = Registry::getInstance()->notices;

		$this->process_form();
	}

	/**
	 * License page template.
	 *
	 * @return void
	 */
	public function form() {
		?>
		<div class="xts-box xts-license xts-theme-style">
			<div class="xts-box-header">
				<h3>
					<?php esc_html_e( 'Theme license', 'woodmart' ); ?>
				</h3>
			</div>

			<div class="xts-box-content">
				<div class="xts-row">
					<div class="xts-col-12 xts-col-xl-5 xts-license-img">
						<img src="<?php echo esc_url( WOODMART_ASSETS_IMAGES . '/dashboard/license.svg' ); ?>" alt="license banner">
					</div>

					<div class="xts-col-12 xts-col-xl-7 xts-license-content">

						<?php $this->_notices->show_msgs(); ?>

						<?php if ( woodmart_is_license_activated() ) : ?>
							<div class="xts-activated-message">
								<p class="xts-licanse-setup-label">
									<?php echo esc_html__( 'Thank you for activating the theme. After activation, you will be able to receive automatic updates via', 'woodmart' ); ?> <strong><?php esc_html_e( 'Appearance → Themes', 'woodmart' ); ?></strong> <?php esc_html_e( 'or', 'woodmart' ); ?> <strong><?php esc_html_e( 'Dashboard → Updates', 'woodmart' ); ?></strong>. <?php esc_html_e( 'Once the theme installation is complete, you can also deactivate this domain on the', 'woodmart' ); ?> <strong><?php esc_html_e( 'Theme License', 'woodmart' ); ?></strong> <?php esc_html_e( 'page if you plan to transfer your website to a different domain or server.', 'woodmart' ); ?>
								</p>
								<p class="xts-licanse-dashboard-label">
									<?php
										printf(
											'%s <a href="' . esc_url( admin_url( 'themes.php' ) ) . '">%s</a> %s <a href="' . esc_url( admin_url( 'update-core.php?force-check=1' ) ) . '">%s</a>.%s',
											esc_html__( 'Thank you for activation. Now you are able to get automatic updates for our theme via', 'woodmart' ),
											esc_html__( 'Appearance -> Themes', 'woodmart' ),
											esc_html__( 'or via', 'woodmart' ),
											esc_html__( 'Dashboard -> Updates', 'woodmart' ),
											esc_html__( ' You can click this button to deactivate your license code from this domain if you are going to transfer your website to some other domain or server.', 'woodmart' )
										);
									?>
								</p>
								<?php if ( get_option( 'woodmart_dev_domain', false ) ) : ?>
									<p class="xts-dev-license-label">
										<?php echo esc_html__( '* Activated on development website.', 'woodmart' ); ?>
									</p>
								<?php endif; ?>

								<form action="" class="xts-form xts-activation-form" method="post">
									<?php wp_nonce_field( 'xts-license-deactivation' ); ?>
									<input type="hidden" name="purchase-code-deactivate" value="1"/>
									<div class="xts-license-btn xts-deactivate-btn xts-i-close">
										<input class="xts-btn xts-color-warning" type="submit" value="<?php esc_attr_e( 'Deactivate theme', 'woodmart' ); ?>" />
									</div>
								</form>
							</div>
						<?php else : ?>
							<?php if ( ! woodmart_get_opt( 'white_label' ) ) : ?>
								<p class="xts-license-label">
									<?php esc_html_e( 'Activate your purchase code for this domain to enable the automatic updates feature.', 'woodmart' ); ?>

									<a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-" target="_blank"><span class="xts-hint"></span><?php esc_attr_e( 'Where is my code?', 'woodmart' ); ?></a>
								</p>
							<?php endif; ?>
							<form action="" class="xts-form xts-activation-form" method="post">
								<?php wp_nonce_field( 'xts-license-activation' ); ?>

								<div class="xts-activation-form-inner">
									<input type="text" name="purchase-code" placeholder="<?php esc_attr_e( 'Example: 1e71cs5f-13d9-41e8-a140-2cff01d96afb', 'woodmart' ); ?>" id="purchase-code" required>
									<?php if ( woodmart_is_license_activated() ) : ?>
										<span>
										<?php esc_html_e( 'Activated', 'woodmart' ); ?>
									</span>
									<?php else : ?>
										<span>
										<?php esc_html_e( 'Not activated', 'woodmart' ); ?>
									</span>
									<?php endif; ?>
								</div>

								<div class="xts-dev-domain-agree">
									<label for="xts-dev-domain-label">
										<input id="xts-dev-domain-label" type="checkbox" name="xts-dev-domain" <?php checked( isset( $_REQUEST['xts-dev-domain'] ) && $_REQUEST['xts-dev-domain'], '1' ); // phpcs:ignore ?> value="1">
										<?php esc_html_e( 'Development domain', 'woodmart' ); ?>
									</label>

									<div class="xts-hint">
										<div class="xts-tooltip xts-top xts-top-left">
											<?php esc_html_e( 'You are allowed to use our theme only on one domain if you purchased a regular license. But we give you an ability to activate our theme to turn on auto updates on two domains: for the development website and for your production (live) website.', 'woodmart' ); ?>
										</div>
									</div>
								</div>

								<div class="xts-activation-form-agree">
									<label for="agree_stored" class="agree-label" >
										<input type="checkbox" name="agree_stored" id="agree_stored" required>
										<?php if ( ! woodmart_get_opt( 'white_label' ) ) : ?>
											<?php esc_html_e( 'I agree that my purchase code and user data will be processed by xtemos.com', 'woodmart' ); ?>
										<?php else : ?>
											<?php esc_html_e( 'I agree that my purchase code and user data will be processed by developers.', 'woodmart' ); ?>
										<?php endif; ?>
									</label>

									<div class="xts-hint">
										<div class="xts-tooltip xts-top xts-top-left">
											<?php esc_html_e( 'To activate the theme and access product support, please register your Envato purchase code on our website. This code, along with your support expiration date and user information, will be securely processed. Registration is required for us to provide you with product support and other customer services.', 'woodmart' ); ?>
										</div>
									</div>
								</div>

								<div class="xts-license-btn xts-activate-btn xts-i-key">
									<input class="xts-btn xts-color-primary" name="woodmart-purchase-code" type="submit" value="<?php esc_attr_e( 'Activate theme', 'woodmart' ); ?>" />
								</div>

								<div class="xts-note">
									<?php
										echo wp_kses(
											__(
												'<span>Note:</span> if you need to check all your active domains or you want to remove some of them you should visit <a href="https://xtemos.com/" target="_blank">our website</a> and check the activation list in your account.',
												'woodmart'
											),
											woodmart_get_allowed_html()
										);
									?>
								</div>
							</form>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Process activate theme.
	 *
	 * @return void
	 */
	public function process_form() {
		if ( isset( $_POST['purchase-code-deactivate'] ) ) {
			check_admin_referer( 'xts-license-deactivation' );
			$this->deactivate();
			$this->_notices->add_success( 'Theme license is successfully deactivated.' );
			return;
		}

		if ( isset( $_POST['woodmart-purchase-code'] ) && ( empty( $_POST['agree_stored'] ) ) ) {
			$this->_notices->add_error( 'You must agree to store your purchase code and user data by xtemos.com' );
			return;
		}

		if ( empty( $_POST['purchase-code'] ) ) {
			return;
		}
		check_admin_referer( 'xts-license-activation' );

		$code = sanitize_text_field( $_POST['purchase-code'] );
		$dev  = (int) ( isset( $_POST['xts-dev-domain'] ) && $_POST['xts-dev-domain'] ); // phpcs:ignore

		$response = $this->_api->call(
			'activate?key=' . $code,
			array(
				'domain' => get_site_url(),
				'theme'  => WOODMART_SLUG,
				'dev'    => $dev,
			),
			'post'
		);

		if ( isset( $_GET['xtemos_debug'] ) ) {
			ar( $response );
		}

		if ( is_wp_error( $response ) ) {
			$this->_notices->add_error( 'The API server can\'t be reached. Please, contact your hosting provider to check the connectivity with our xtemos.com server. If you need further help, please, contact our support center too.' );
			return;
		}

		$data = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( isset( $data['errors'] ) ) {
			$this->_notices->add_error( $data['errors'] );
			return;
		}

		if ( ( isset( $data['code'] ) && 'rest_forbidden' === $data['code'] ) || empty( $data['verified'] ) ) {
			$this->_notices->add_error( 'The purchase code is invalid. <a target="_blank" href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-">Where can I get my purchase code?</a>' );
			return;
		}

		$this->activate( $code, $data['token'], $dev );

		$this->_notices->add_success( 'The license is verified and theme is activated successfully. Auto updates function is enabled.' );
	}

	/**
	 * Activate theme.
	 *
	 * @param string $purchase Theme token.
	 * @param string $token Purchase code.
	 * @param int    $dev Is developer activation? Set 1 or 0.
	 *
	 * @return void
	 */
	public function activate( $purchase, $token, $dev ) {
		update_option( 'woodmart_token', $token );
		update_option( 'woodmart_is_activated', true );
		update_option( 'woodmart_dev_domain', $dev );
	}

	/**
	 * Deactivated theme.
	 *
	 * @return void
	 */
	public function deactivate() {
		$this->_api->call( 'deactivate/?token=' . get_option( 'woodmart_token' ) );

		delete_option( 'woodmart_token' );
		delete_option( 'woodmart_is_activated' );
		delete_option( 'woodmart-update-time' );
		delete_option( 'woodmart-update-info' );
		delete_option( 'woodmart_dev_domain' );
	}
}
