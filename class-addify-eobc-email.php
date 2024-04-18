<?php
/**
 * Main class start.
 *
 * @package : eobc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Addify_Eobc_Email', false ) ) :

	/**
	 * Addify_Eobc_Email class start.
	 */
	class Addify_Eobc_Email extends WC_Email {

		/**
		 * Constructor for admin email.
		 */
		public function __construct() {

			$this->id             = 'Order_edited_email_to_admin';
			$this->title          = __( 'Order edited Email to admin', 'eobc-wc-email' );
			$this->customer_email = false;
			$this->description    = __( 'This email will send to admin when a user edit the order.', 'eobc-wc-email' );

			$this->template_base  = AEOBC_PLUGIN_DIR;
			$this->template_html  = 'templates/emails/html-admin-order-edited.php';
			$this->template_plain = 'templates/emails/plain/plain-admin-order-edited.php';
			$this->placeholders   = array(
				'{customer_full_name} ' => '',
				'{customer_email}'      => '',
				'{order_number}'        => '',
			);

			$this->placeholders = array();

			// Call to the  parent constructor.
			parent::__construct();

			// Other settings.
			$this->recipient = $this->get_option( 'recipient', get_option( 'admin_email' ) );
			// Trigger function  for this customer email cancelled order.
		}

		/**
		 * Get_default_subject function start
		 */
		public function get_default_subject() {
			return __( '[{site_title}]: Order order has been edited', 'addify_eobc' );
		}

		/**
		 * Get_default_heading function start.
		 */
		public function get_default_heading() {
			return __( 'Order has been edited by customer.', 'addify_eobc' );
		}
		/**
		 * Trigger function start.
		 *
		 * @param int $order_id .
		 */
		public function trigger( $order_id ) {

			$this->setup_locale();

			$order = wc_get_order( $order_id );

			$customer = $order->get_user();

			$customer_details = '';

			if ( $order ) {

				$user_login = stripslashes( $customer->user_login );

				$user_email = stripslashes( $customer->user_email );

				$this->object = $order;

				// Customer billing information details.
				$order_number       = $order->get_order_number();
				$customer_email     = $order->get_billing_email();
				$billing_first_name = $order->get_billing_first_name();
				$billing_last_name  = $order->get_billing_last_name();
				$customer_full_name = $billing_first_name . ' ' . $billing_last_name;

				$this->placeholders['{customer_full_name}'] = $customer_full_name;
				$this->placeholders['{customer_email}']     = $customer_email;
				$this->placeholders['{order_number}']       = $order_number;
			}

			if ( $this->is_enabled() && $this->get_recipient() ) {

				$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
			}

			$this->restore_locale();
		}
		/**
		 * Get_content_html start.
		 */
		public function get_content_html() {
			return wc_get_template_html(
				$this->template_html,
				array(
					'order'              => $this->object,
					'email_heading'      => $this->get_heading(),
					'email_content'      => $this->format_string( get_option( 'eobc_edit_email_field' ) ),
					'additional_content' => $this->get_additional_content(),
					'sent_to_admin'      => true,
					'plain_text'         => false,
					'email'              => $this,
				),
				'',
				$this->template_base
			);
		}
		/**
		 * Get_content_plain start.
		 */
		public function get_content_plain() {
			return wc_get_template_html(
				$this->template_html,
				array(
					'order'              => $this->object,
					'email_heading'      => $this->get_heading(),
					'email_content'      => $this->format_string( get_option( 'eobc_edit_email_field' ) ),
					'additional_content' => $this->get_additional_content(),
					'sent_to_admin'      => true,
					'plain_text'         => false,
					'email'              => $this,
				),
				'',
				$this->template_base
			);
		}
		/**
		 * Get_default_additional_content start.
		 */
		public function get_default_additional_content() {
			return __( 'Thanks for reading.', 'woocommerce' );
		}
		/**
		 * Init_form_fields start.
		 */
		public function init_form_fields() {

			/* translators: %s: placeholder */
			$placeholder_text  = sprintf( __( 'Available placeholders: %s', 'woocommerce' ), '<code>' . esc_html( implode( '</code>, <code>', array_keys( $this->placeholders ) ) ) . '</code>' );
			$this->form_fields = array(
				'enabled'            => array(
					'title'   => __( 'Enable/Disable', 'woocommerce' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable this email notification', 'woocommerce' ),
					'default' => 'yes',
				),
				// Recipient  in customer Order-edited email.
				'recipient'          => array(
					'title'       => __( 'Recipient(s)', 'woocommerce' ),
					'type'        => 'text',
					/* translators: %s: WP admin email */
					'description' => sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to %s.', 'woocommerce' ), '<code>' . esc_attr( get_option( 'admin_email' ) ) . '</code>' ),
					'placeholder' => '',
					'default'     => esc_attr( get_option( 'admin_email' ) ),
					'desc_tip'    => true,
				),
				// subject  in customer Order-edited email.
				'subject'            => array(
					'title'       => __( 'Subject', 'woocommerce' ),
					'type'        => 'text',
					'desc_tip'    => true,
					'description' => $placeholder_text,
					'placeholder' => $this->get_default_subject(),
					'default'     => '',
				),
				// heading  in customer Order-edited email.
				'heading'            => array(
					'title'       => __( 'Email heading', 'woocommerce' ),
					'type'        => 'text',
					'desc_tip'    => true,
					'description' => $placeholder_text,
					'placeholder' => $this->get_default_heading(),
					'default'     => '',
				),
				// additional content  in customer Order-edited email.
				'additional_content' => array(
					'title'       => __( 'Additional content', 'woocommerce' ),
					'description' => __( 'Text to appear below the main email content.', 'woocommerce' ) . ' ' . $placeholder_text,
					'css'         => 'width:400px; height: 75px;',
					'placeholder' => __( 'N/A', 'woocommerce' ),
					'type'        => 'textarea',
					'default'     => $this->get_default_additional_content(),
					'desc_tip'    => true,
				),
				// email type  in customer Order-edited email.
				'email_type'         => array(
					'title'       => __( 'Email type', 'woocommerce' ),
					'type'        => 'select',
					'description' => __( 'Choose which format of email to send.', 'woocommerce' ),
					'default'     => 'html',
					'class'       => 'email_type wc-enhanced-select',
					'options'     => $this->get_email_type_options(),
					'desc_tip'    => true,
				),
			);
		}
	}
endif;
