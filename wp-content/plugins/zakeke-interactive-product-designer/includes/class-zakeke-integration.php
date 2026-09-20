<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Zakeke_Integration' ) ) :

	/**
	 * Zakeke_Integration Class.
	 */
	class Zakeke_Integration extends WC_Integration {
		public $debug                 = 'no';
		public $show_cart_sides       = 'yes';
		public $show_custom_thumbnail = 'yes';
		public $show_price_in_cart    = 'yes';
		public $show_all_sides        = 'no';
		public $share_return_to_product_page = 'yes';
		public $username;
		public $password;
		public $client_id;
		public $secret_key;

		public $force_customization;

		public $hide_price;

		/**
		 * Zakeke Integration Constructor.
		 */
		public function __construct() {
			$this->id                 = 'zakeke';
			$this->method_title       = __( 'Zakeke Interactive Product Designer', 'zakeke' );
			$this->method_description = __( 'Integrate Zakeke into WooCommerce. These credentials will be used to allow the integration of Zakeke with your store. By entering your Zakeke credentials your store will be able to communicate with the Zakeke API. Please refer to the <a href="https://zakeke.zendesk.com/hc/en-us">Zakeke documentation</a> if you are unsure how to proceed',
				'zakeke' );

			// Load the settings.
			$this->init_form_fields();
			$this->init_settings();

			// Define user set variables.
			$this->username              = $this->get_option( 'username' );
			$this->password              = $this->get_option( 'password' );
			$this->client_id             = $this->get_option( 'client_id' );
			$this->secret_key            = $this->get_option( 'secret_key' );
			$this->force_customization   = $this->get_option( 'force_customization' );
			$this->hide_price            = $this->get_option( 'hide_price' );
			$this->debug                 = $this->get_option( 'debug' );
			$this->show_cart_sides       = $this->get_option( 'show_cart_sides' );
			$this->show_custom_thumbnail = $this->get_option( 'show_custom_thumbnail' );
			$this->show_price_in_cart    = $this->get_option( 'show_price_in_cart' );
			$this->show_all_sides        = $this->get_option( 'show_all_sides' );
			$this->share_return_to_product_page = $this->get_option( 'share_return_to_product_page', 'yes' );

			// Actions.
			add_action( 'woocommerce_update_options_integration_' . $this->id,
				array( $this, 'process_admin_options' ) );
		}

		/**
		 * Processes and saves options.
		 * If there the Zakeke credentials are invalid, no setting will be saved and an error is show.
		 *
		 * @return bool was anything saved?
		 */
		public function process_admin_options() {
			$this->init_settings();

			$post_data = $this->get_post_data();
			$username  = $this->get_field_value( 'username', array(), $post_data );

			if ( ! empty( $username ) ) {
				$password = $this->get_field_value( 'password', array(), $post_data );

				$webservice = new Zakeke_Webservice();
				if ( ! $webservice->are_valid_credentials( $username, $password ) ) {
					$this->add_error( __( 'Incorrect Zakeke credentials', 'zakeke' ) );
					WC_Admin_Settings::add_error( esc_html__( 'Incorrect Zakeke credentials', 'zakeke' ) );
					return false;
				}
			}

			$client_id  = $this->get_field_value( 'client_id', array(), $post_data );
			$secret_key = $this->get_field_value( 'secret_key', array(), $post_data );
			if ( ! empty( $client_id ) && ! empty( $secret_key ) ) {
				$webservice = new Zakeke_Webservice();
				if ( ! $webservice->are_valid_api_keys( $client_id, $secret_key ) ) {
					$this->add_error( __( 'Incorrect Zakeke API keys', 'zakeke' ) );
					WC_Admin_Settings::add_error( esc_html__( 'Incorrect Zakeke API keys', 'zakeke' ) );
					return false;
				}
			}

			return parent::process_admin_options();
		}

		/**
		 * Initialize integration settings form fields.
		 */
		public function init_form_fields() {
			$this->form_fields = array(
				'username'            => array(
					'title'       => __( 'Zakeke username', 'zakeke' ),
					'type'        => 'text',
					'description' => __( 'Your Zakeke account username. Need a Zakeke account? <a href="https://portal.zakeke.com/Admin/Register" target="_blank">Click here to get one</a>',
						'zakeke' )
				),
				'password'            => array(
					'title'       => __( 'Zakeke password', 'zakeke' ),
					'type'        => 'password',
					'description' => __( 'Your Zakeke account password', 'zakeke' ),
					'custom_attributes' => array(
						'autocomplete' => 'new-password'
					)
				),
				'client_id'          => array(
					'title'       => __( 'Zakeke API client id', 'zakeke' ),
					'type'        => 'text',
					'description' => __( 'Your Zakeke API client id', 'zakeke' )
				),
				'secret_key'         => array(
					'title'       => __( 'Zakeke API secret key', 'zakeke' ),
					'type'        => 'text',
					'description' => __( 'Your Zakeke API secret key', 'zakeke' )
				),
				'force_customization' => array(
					'title'       => __( 'Force product customization', 'zakeke' ),
					'type'        => 'checkbox',
					'default'     => 'yes',
					'description' => __( 'Replace the "Add to cart" button with the "Customize" button for customizable products',
						'zakeke' )
				),
				'show_cart_sides' => array(
					'title'       => __( 'Show the list of customized sides in the cart', 'zakeke' ),
					'type'        => 'checkbox',
					'default'     => 'yes',
					'description' => __( 'Customized products will also show the list of customized sides in the cart',
						'zakeke' )
				),
				'show_custom_thumbnail' => array(
					'title'       => __( 'Show the customized image in the cart thumbnail', 'zakeke' ),
					'type'        => 'checkbox',
					'default'     => 'yes',
					'description' => __( 'Show the customized product image in the cart thumbnail',
						'zakeke' )
				),
				'hide_price' => array(
					'title'       => __( 'Manage prices outside Zakeke', 'zakeke' ),
					'type'        => 'checkbox',
					'default'     => 'no',
					'description' => __( 'Check when your products price is handled outside Zakeke',
						'zakeke' )
				),
				'show_price_in_cart' => array(
					'title'       => __( 'Show the "Customizaton Price" property in the cart', 'zakeke' ),
					'type'        => 'checkbox',
					'default'     => 'yes',
					'description' => __( 'Check to show the price due to the customization in the cart',
						'zakeke' )
				),
				'show_all_sides' => array(
					'title'       => __( 'Show the all the product sides in the confirmation mail and checkout', 'zakeke' ),
					'type'        => 'checkbox',
					'default'     => 'no',
					'description' => __( 'Check to show don\'t limit the number of the sides to show to the first two', 'zakeke' )
				),
				'share_return_to_product_page' => array(
					'title'       => __( 'The Zakeke share url return to the product page', 'zakeke' ),
					'type'        => 'checkbox',
					'default'     => 'yes',
					'description' => __( 'Check to show the product page first when visiting a url shared by Zakeke', 'zakeke' )
				),
				'debug' => array(
					'title'       => __( 'Debug Log', 'zakeke' ),
					'type'        => 'checkbox',
					'label'       => __( 'Enable logging', 'zakeke' ),
					'default'     => 'no',
					'description' => __( 'Log events such as API requests', 'zakeke' ),
				)
			);
		}
	}

endif;
