<?php

/**
 * Main class start.
 *
 * @package : eobc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Edit order by customer main start.
 */
class Addify_Edit_Order_By_Customer_Admin {
	/**
	 *  Function __construct function start.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'adfy_edit_order_by_customer_submenu' ) );
		add_action( 'admin_init', array( $this, 'adfy_edit_order_by_customer_settings' ) );
		add_action( 'wp_ajax_adfy_eobc_product_search', array( $this, 'adfy_eobc_product_search' ) );
		add_action( 'wp_ajax_eobc_user_search', array( $this, 'eobc_user_search' ) );
		add_action( 'wp_ajax_remove_item_from_order', array( $this, 'product_remove' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'adfy_eobc_files' ) );
	}
	/**
	 * Main adfy_edit_order_by_customer_submenu function start.
	 */
	public function adfy_edit_order_by_customer_submenu() {

		add_submenu_page(
			'woocommerce', // parent slug.
			'Edit Order By Customer', // Page title.
			esc_html__( 'Edit Order By Customer', ' addify_eobc' ), // Title.
			'manage_options', // Capability.
			'Eobc_settings', // slug.
			array( $this, 'create_edit_order_by_customer_setting_page' )
		);
	}
	/**
	 * Main create_edit_order_by_customer_setting_page start.
	 */
	public function create_edit_order_by_customer_setting_page() {
		global $active_tab;
		if ( isset( $_GET['tab'] ) ) {
			$active_tab = sanitize_text_field( wp_unslash( $_GET['tab'] ) );
		} else {
			$active_tab = 'general';
		}
		?>
		<div class="wrap">
			<!-- Title above Tabs  -->
			<h2> <?php echo esc_html__( 'Edit order by customer Settings', 'addify_eobc' ); ?></h2>
			<?php settings_errors(); ?>
			<h2 class="nav-tab-wrapper">
				<!-- General Setting Tab -->
				<a href="?post_type=Eobc_settings&page=Eobc_settings&tab=general" class="nav-tab  <?php echo esc_attr( $active_tab ) === 'general' ? ' nav-tab-active' : ''; ?>"> <?php esc_html_e( 'General Settings', 'addify_eobc' ); ?> </a>
				<a href="?post_type=Eobc_settings&page=Eobc_settings&tab=settings" class="nav-tab  <?php echo esc_attr( $active_tab ) === 'settings' ? ' nav-tab-active' : ''; ?>"> <?php esc_html_e( 'Billing Address Settings', 'addify_eobc' ); ?> </a>

				<a href="?post_type=Eobc_settings&page=Eobc_settings&tab=settings1" class="nav-tab  <?php echo esc_attr( $active_tab ) === 'settings1' ? ' nav-tab-active' : ''; ?>"> <?php esc_html_e( 'Shipping Address Settings', 'addify_eobc' ); ?> </a>

				<a href="?post_type=Eobc_settings&page=Eobc_settings&tab=settings3" class="nav-tab  <?php echo esc_attr( $active_tab ) === 'settings3' ? ' nav-tab-active' : ''; ?>"> <?php esc_html_e( 'Product Settings', 'addify_eobc' ); ?> </a>

				<a href="?post_type=Eobc_settings&page=Eobc_settings&tab=settings4" class="nav-tab  <?php echo esc_attr( $active_tab ) === 'settings4' ? ' nav-tab-active' : ''; ?>"> <?php esc_html_e( 'Shipping Method Settings', 'addify_eobc' ); ?> </a>

				<a href="?post_type=Eobc_settings&page=Eobc_settings&tab=settings5" class="nav-tab  <?php echo esc_attr( $active_tab ) === 'settings5' ? ' nav-tab-active' : ''; ?>"> <?php esc_html_e( 'Payment Method Settings', 'addify_eobc' ); ?> </a>

			</h2>
			<form method="post" action="options.php">
				<?php
				if ( isset( $_POST['_wpnonce'] ) ) {
					$nonce = sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) );
					if ( ! wp_verify_nonce( $nonce, '_wpnonce' ) ) {
						die( 'Failed Security check' );
					}
				}
				// General Setting Tab.
				if ( 'general' === $active_tab ) {

					settings_fields( 'adfy_edit_order_general_style_setting' );
					do_settings_sections( 'adfy_edit_order_general_settings_page' );
				} elseif ( 'settings' === $active_tab ) {

					settings_fields( 'adfy_edit_order_by_customer_style_setting' );
					do_settings_sections( 'adfy_edit_order_by_customer_settings_page' );
				} elseif ( 'settings1' === $active_tab ) {

					settings_fields( 'adfy_edit_shipping_style_setting' );
					do_settings_sections( 'adfy_edit_shipping_settings_page' );
				} elseif ( 'settings3' === $active_tab ) {

					settings_fields( 'adfy_edit_product_style_setting' );
					do_settings_sections( 'adfy_edit_product_settings_page' );
				} elseif ( 'settings4' === $active_tab ) {

					settings_fields( 'adfy_edit_shipping_method_style_setting' );
					do_settings_sections( 'adfy_edit_shipping_method_settings_page' );
				} elseif ( 'settings5' === $active_tab ) {

					settings_fields( 'adfy_edit_payment_method_style_setting' );
					do_settings_sections( 'adfy_edit_payment_method_settings_page' );
				}

				echo '<div class= "div2">';
				submit_button( esc_html__( 'Save Settings', 'addify_eobc' ), 'primary' );
				echo '</div>';
				?>
			</form>

		<?php
	}
	/**
	 * Main adfy_edit_order_by_customer_settings start.
	 */
	public function adfy_edit_order_by_customer_settings() {

		add_settings_section(
			'adfy_edit_order_billing_section_1',
			'',
			array( $this, 'edit_order_settings_section_1' ),
			'adfy_edit_order_by_customer_settings_page'
		);
		add_settings_field(
			'adfy_edit_order_billing_field',
			esc_html__( 'Enable ', ' addify_eobc' ),
			array( $this, 'edit_order_billing_setting_field_callback' ),
			'adfy_edit_order_by_customer_settings_page',
			'adfy_edit_order_billing_section_1'
		);
		register_setting(
			'adfy_edit_order_by_customer_style_setting',
			'adfy_edit_order_billing_field'
		);

		add_settings_section(
			'adfyedit_order_edit_button_section_0',
			'',
			array( $this, 'edit_order_settings_section_0' ),
			'adfy_edit_order_general_settings_page'
		);
		add_settings_field(
			'adfyedit_order_edit_button_field_0',
			esc_html__( 'Enable Edit Button ', ' addify_eobc' ),
			array( $this, 'edit_order_edit_button_setting_field_callback' ),
			'adfy_edit_order_general_settings_page',
			'adfyedit_order_edit_button_section_0'
		);
		register_setting(
			'adfy_edit_order_general_style_setting',
			'adfyedit_order_edit_button_field_0'
		);

		add_settings_section(
			'adfyedit_order_status_section',
			'',
			array( $this, 'edit_order_settings_section' ),
			'adfy_edit_order_general_settings_page'
		);
		add_settings_field(
			'adfyedit_order_status_field',
			esc_html__( 'Order Status ', ' addify_eobc' ),
			array( $this, 'new_edit_order_status_setting_field_callback' ),
			'adfy_edit_order_general_settings_page',
			'adfyedit_order_status_section'
		);
		register_setting(
			'adfy_edit_order_general_style_setting',
			'adfyedit_order_status_field'
		);

		add_settings_section(
			'adfyedit_billing_order_status_section_a',
			'',
			array( $this, 'edit_order_settings_section_a' ),
			'adfy_edit_order_by_customer_settings_page'
		);
		add_settings_field(
			'adfyedit_billing_order_status_field_a',
			esc_html__( 'Order Status ', ' addify_eobc' ),
			array( $this, 'edit_billing_order_status_setting_field_callback' ),
			'adfy_edit_order_by_customer_settings_page',
			'adfyedit_billing_order_status_section_a'
		);
		register_setting(
			'adfy_edit_order_by_customer_style_setting',
			'adfyedit_billing_order_status_field_a'
		);

		add_settings_section(
			'adfy_billing_first_name_section',
			'',
			array( $this, 'billing_label_name_section' ),
			'adfy_edit_order_by_customer_settings_page'
		);
		add_settings_field(
			'adfy_billing_first_name_field',
			esc_html__( 'Billing Address', ' addify_eobc' ),
			array( $this, 'edit_billing_label_setting_field_callback' ),
			'adfy_edit_order_by_customer_settings_page',
			'adfy_billing_first_name_section'
		);
		register_setting(
			'adfy_edit_order_by_customer_style_setting',
			'adfy_billing_first_name_field'
		);

		add_settings_section(
			'adfy_edit_order_shipping_section',
			'',
			array( $this, 'edit_order_settings_section_2' ),
			'adfy_edit_shipping_settings_page'
		);
		add_settings_field(
			'adfyedit_order_shipping_field',
			esc_html__( 'Enable ', ' addify_eobc' ),
			array( $this, 'edit_order_shipping_setting_field_callback' ),
			'adfy_edit_shipping_settings_page',
			'adfy_edit_order_shipping_section'
		);
		register_setting(
			'adfy_edit_shipping_style_setting',
			'adfyedit_order_shipping_field'
		);

		add_settings_section(
			'adfy_edit_user_role_section',
			'',
			array( $this, 'edit_order_settings_section_11' ),
			'adfy_edit_order_general_settings_page'
		);
		add_settings_field(
			'adfy_edit_user_role_field',
			esc_html__( 'Select User Role ', ' addify_eobc' ),
			array( $this, 'edit_user_role_setting_field_callback' ),
			'adfy_edit_order_general_settings_page',
			'adfy_edit_user_role_section'
		);
		register_setting(
			'adfy_edit_order_general_style_setting',
			'adfy_edit_user_role_field'
		);

		add_settings_section(
			'adfy_edit_user_section',
			'',
			array( $this, 'edit_order_settings_section_13' ),
			'adfy_edit_order_general_settings_page'
		);
		add_settings_field(
			'eobc_edit_user_field',
			esc_html__( 'Select Specific User ', ' addify_eobc' ),
			array( $this, 'edit_all_users_setting_field_callback' ),
			'adfy_edit_order_general_settings_page',
			'adfy_edit_user_section'
		);
		register_setting(
			'adfy_edit_order_general_style_setting',
			'eobc_edit_user_field'
		);

		add_settings_section(
			'adfy_edit_order_section',
			'',
			array( $this, 'edit_order_settings_section_email' ),
			'adfy_edit_order_general_settings_page'
		);
		add_settings_field(
			'eobc_edit_email_field',
			esc_html__( 'Email Text', ' addify_eobc' ),
			array( $this, 'edit_edit_order_setting_field_callback' ),
			'adfy_edit_order_general_settings_page',
			'adfy_edit_order_section'
		);
		register_setting(
			'adfy_edit_order_general_style_setting',
			'eobc_edit_email_field'
		);

		add_settings_section(
			'adfy_edit_order_order_product_section',
			'',
			array( $this, 'edit_order_settings_section_5' ),
			'adfy_edit_product_settings_page'
		);
		add_settings_field(
			'adfy_edit_order_order_product_field',
			esc_html__( 'Enable ', ' addify_eobc' ),
			array( $this, 'edit_order_order_product_setting_field_callback' ),
			'adfy_edit_product_settings_page',
			'adfy_edit_order_order_product_section'
		);
		register_setting(
			'adfy_edit_product_style_setting',
			'adfy_edit_order_order_product_field'
		);

		add_settings_section(
			'adfy_edit_order_status_section_c',
			'',
			array( $this, 'edit_order_settings_section_c' ),
			'adfy_edit_product_settings_page'
		);
		add_settings_field(
			'adfy_edit_order_status_field',
			esc_html__( 'Order Status ', ' addify_eobc' ),
			array( $this, 'edit_order_status_setting_field_callback' ),
			'adfy_edit_product_settings_page',
			'adfy_edit_order_status_section_c'
		);
		register_setting(
			'adfy_edit_product_style_setting',
			'adfy_edit_order_status_field'
		);

		add_settings_section(
			'adfy_edit_order_all_product_section_c',
			'',
			array( $this, 'edit_order_all_product_settings_section_c' ),
			'adfy_edit_product_settings_page'
		);
		add_settings_field(
			'adfy_edit_order_all_product_field',
			esc_html__( 'All Products', ' addify_eobc' ),
			array( $this, 'edit_order_all_product_setting_callback' ),
			'adfy_edit_product_settings_page',
			'adfy_edit_order_all_product_section_c'
		);
		register_setting(
			'adfy_edit_product_style_setting',
			'adfy_edit_order_all_product_field'
		);

		add_settings_section(
			'adfy_edit_order_order_product_change_section',
			'',
			array( $this, 'edit_order_settings_section_6' ),
			'adfy_edit_product_settings_page'
		);
		add_settings_field(
			'adfy_edit_order_order_product_change_field',
			esc_html__( 'Choose product ', ' addify_eobc' ),
			array( $this, 'edit_order_order_product_change_setting_field_callback' ),
			'adfy_edit_product_settings_page',
			'adfy_edit_order_order_product_change_section'
		);
		register_setting(
			'adfy_edit_product_style_setting',
			'adfy_edit_order_order_product_change_field'
		);

		add_settings_section(
			'adfy_edit_product_category_change_section',
			'',
			array( $this, 'edit_order_settings_section_14' ),
			'adfy_edit_product_settings_page'
		);
		add_settings_field(
			'adfy_edit_product_category_change_field',
			esc_html__( 'Choose product category', ' addify_eobc' ),
			array( $this, 'edit_product_category_change_setting_field_callback' ),
			'adfy_edit_product_settings_page',
			'adfy_edit_product_category_change_section'
		);
		register_setting(
			'adfy_edit_product_style_setting',
			'adfy_edit_product_category_change_field'
		);

		add_settings_section(
			'adfy_edit_order_order_status_section',
			'',
			array( $this, 'edit_order_settings_section_4' ),
			'adfy_edit_shipping_settings_page'
		);
		add_settings_field(
			'adfyedit_order_order_status_field',
			esc_html__( 'Choose Order Status ', ' addify_eobc' ),
			array( $this, 'edit_order_order_status_setting_field_callback' ),
			'adfy_edit_shipping_settings_page',
			'adfy_edit_order_order_status_section'
		);
		register_setting(
			'adfy_edit_shipping_style_setting',
			'adfyedit_order_order_status_field'
		);

		add_settings_section(
			'adfy_edit_shipping_label_section',
			'',
			array( $this, 'edit_shipping_label_settings_section' ),
			'adfy_edit_shipping_settings_page'
		);
		add_settings_field(
			'adfyedit_shipping_label_field',
			esc_html__( 'Shipping Address ', ' addify_eobc' ),
			array( $this, 'edit_shipping_label_setting_field_callback' ),
			'adfy_edit_shipping_settings_page',
			'adfy_edit_shipping_label_section'
		);
		register_setting(
			'adfy_edit_shipping_style_setting',
			'adfyedit_shipping_label_field'
		);

		add_settings_section(
			'adfy_edit_order_order_shipping_checkbox_section',
			'',
			array( $this, 'edit_order_settings_section_7' ),
			'adfy_edit_shipping_method_settings_page'
		);
		add_settings_field(
			'adfy_edit_order_order_shipping_checkbox_field',
			esc_html__( 'Enable ', ' addify_eobc' ),
			array( $this, 'edit_order_order_shipping_checkbox_setting_field_callback' ),
			'adfy_edit_shipping_method_settings_page',
			'adfy_edit_order_order_shipping_checkbox_section'
		);
		register_setting(
			'adfy_edit_shipping_method_style_setting',
			'adfy_edit_order_order_shipping_checkbox_field'
		);

		add_settings_section(
			'shipping_method_order_status_section',
			'',
			array( $this, 'edit_order_settings_section_g' ),
			'adfy_edit_shipping_method_settings_page'
		);
		add_settings_field(
			'shipping_method_order_status_field',
			esc_html__( 'Order Status', ' addify_eobc' ),
			array( $this, 'shipping_method_order_status_setting_field_callback' ),
			'adfy_edit_shipping_method_settings_page',
			'shipping_method_order_status_section'
		);
		register_setting(
			'adfy_edit_shipping_method_style_setting',
			'shipping_method_order_status_field'
		);

		add_settings_section(
			'adfy_edit_order_order_shipping_section',
			'',
			array( $this, 'edit_order_settings_section_8' ),
			'adfy_edit_shipping_method_settings_page'
		);
		add_settings_field(
			'adfy_edit_order_order_shipping_field',
			esc_html__( 'Choose Shipping Method ', ' addify_eobc' ),
			array( $this, 'edit_order_order_shipping_setting_field_callback' ),
			'adfy_edit_shipping_method_settings_page',
			'adfy_edit_order_order_shipping_section'
		);
		register_setting(
			'adfy_edit_shipping_method_style_setting',
			'adfy_edit_order_order_shipping_field'
		);

		add_settings_section(
			'adfy_edit_order_order_payment_checkbox_section',
			'',
			array( $this, 'edit_order_settings_section_9' ),
			'adfy_edit_payment_method_settings_page'
		);
		add_settings_field(
			'adfy_edit_order_order_payment_checkbox_field',
			esc_html__( 'Enable ', ' addify_eobc' ),
			array( $this, 'edit_order_order_payment_checkbox_setting_field_callback' ),
			'adfy_edit_payment_method_settings_page',
			'adfy_edit_order_order_payment_checkbox_section'
		);
		register_setting(
			'adfy_edit_payment_method_style_setting',
			'adfy_edit_order_order_payment_checkbox_field'
		);

		add_settings_section(
			'payment_method_order_status_setting_checkbox_section',
			'',
			array( $this, 'edit_order_settings_section_j' ),
			'adfy_edit_payment_method_settings_page'
		);
		add_settings_field(
			'payment_method_order_status_field',
			esc_html__( 'Order Status', ' addify_eobc' ),
			array( $this, 'payment_method_order_status_setting_field_callback' ),
			'adfy_edit_payment_method_settings_page',
			'payment_method_order_status_setting_checkbox_section'
		);
		register_setting(
			'adfy_edit_payment_method_style_setting',
			'payment_method_order_status_field'
		);

		add_settings_section(
			'adfy_edit_order_order_payment_section',
			'',
			array( $this, 'edit_order_settings_section_10' ),
			'adfy_edit_payment_method_settings_page'
		);
		add_settings_field(
			'adfy_edit_order_order_payment_field',
			esc_html__( 'Choose Payment Method ', ' addify_eobc' ),
			array( $this, 'edit_order_order_payment_setting_field_callback' ),
			'adfy_edit_payment_method_settings_page',
			'adfy_edit_order_order_payment_section'
		);
		register_setting(
			'adfy_edit_payment_method_style_setting',
			'adfy_edit_order_order_payment_field'
		);
	}
	/**
	 * Main class start.
	 */
	public function edit_edit_order_setting_field_callback() {
		?>
			<?php
			if ( ! get_option( 'eobc_edit_email_field' ) ) :
				update_option( 'eobc_edit_email_field', '<p>' . esc_html__( 'Hello your customer {customer_full_name} email: {customer_email} edited his order {order_number}.', 'addify_eobc' ) . '</p>' );
				?>
			<?php endif ?>
			<div class="ps_enable_cust_email">
				<?php
				$eobc_content_email   = stripslashes( get_option( 'eobc_edit_email_field' ) );
				$eobc_editor_id_email = 'eobc_edit_email_field';
				$eobc_settings_email  = array(
					'wpautop'       => false,
					'media_buttons' => false,
					'tinymce'       => true,
					'textarea_rows' => 5,
					'quicktags'     => array( 'buttons' => 'em,strong,link' ),

				);
				wp_editor( $eobc_content_email, $eobc_editor_id_email, $eobc_settings_email );
				?>
			</div>
			<p><?php echo esc_html__( 'Use {customer_full_name} for customer name,use {customer_email} for customer email and use {order_number} for order id for sending email to admin.', 'addify_eobc' ); ?></p>
			<?php
	}
		/**
		 * Main class start.
		 */
	public function edit_order_settings_section_email() {
	}
		/**
		 * Main edit_all_users_setting_field_callback start.
		 */
	public function edit_billing_label_setting_field_callback() {
		global $post;
		$billing = wc()->checkout()->get_checkout_fields( 'billing' );

		$save_billing = (array) get_option( 'adfy_billing_first_name_field' );

		if ( $billing ) {

			foreach ( $billing as $field_key => $field ) {
				$field_label = $field['label'];

				?>
					<input type="checkbox" name="adfy_billing_first_name_field[]" id="adfy_enable_billing_field" value="<?php echo esc_attr( $field_key ); ?>" 
																																   <?php
																																	if ( in_array( $field_key, $save_billing ) ) {
																																		echo 'checked';
																																	}
																																	?>
																																								>
					<?php echo esc_html( $field['label'] ); ?>
					<br>
				<?php
			}
			?>
				<p><?php echo esc_html__( 'Select billing address that you want to change in order.', 'addify_eobc' ); ?></p>
			<?php
		} else {
			return;
		}
	}
		/**
		 * Main edit_order_settings_section start.
		 */
	public function billing_label_name_section() {
	}
		/**
		 * Main edit_all_users_setting_field_callback start.
		 */
	public function edit_all_users_setting_field_callback() {
		global $post;
		$users = (array) get_option( 'eobc_edit_user_field' );
		?>
			<select class="eobc_edit_user_field" name="eobc_edit_user_field[]" id="specific" multiple style="width: 50%; ">
				?>
			<?php
			foreach ( (array) $users as $search_users ) {
				$user = get_user_by( 'id', $search_users );
				if ( ! is_object( $user ) ) {
					continue;
				}
				?>
					<option value="<?php echo intval( $search_users ); ?>" selected="selected"><?php echo esc_attr( $user->display_name . ' ( ' . $user->user_email . ')' ); ?></option>
				<?php
			}
			?>
			</select>
			<p><?php echo esc_html__( 'Select specific user role which you want to show edit order button.', 'addify_eobc' ); ?></p>
			<?php
	}
		/**
		 * Main edit_order_settings_section start.
		 */
	public function edit_order_settings_section_13() {
	}
		/**
		 * Main edit_user_role_setting_field_callback start.
		 */
	public function edit_user_role_setting_field_callback() {
		global $post, $wp_roles;

		$user_role_switch = (array) get_option( 'adfy_edit_user_role_field' );
		$roles            = $wp_roles->get_names();
		foreach ( $roles as $key => $new_user_role ) {
			?>
				<div>
					<input type="checkbox" name="adfy_edit_user_role_field[]" value="<?php echo esc_attr( $key ); ?>" <?php echo in_array( (string) $key, (array) $user_role_switch, true ) ? 'checked' : ''; ?> />
				<?php wp_nonce_field( 'checkbox_nonce_action', 'checkbox_fields_nonce' ); ?>
				<?php echo esc_attr( $new_user_role ); ?>
				</div>
			<?php
		}
		?>
			<p><?php echo esc_html__( 'Select user role which you want to show edit order button.', 'addify_eobc' ); ?></p>
		<?php
	}
		/**
		 * Main edit_order_settings_section start.
		 */
	public function edit_order_settings_section_11() {
	}
		/**
		 * Main edit_order_status_setting_field_callback start.
		 */
	public function edit_order_status_setting_field_callback() {

		global $post;
		$get_status  = get_option( 'adfy_edit_order_status_field' );
		$user_status = (array) get_option( 'adfy_edit_order_status_field' );
		?>
			<select id="adfy_edit_order_status_field" class="adfy_edit_order_status_field" name="adfy_edit_order_status_field[]" multiple style="width: 50%;">
			<?php
			foreach ( wc_get_order_statuses() as $valr_status  => $order_status ) :
				if ( 'Pending payment' == $order_status || 'On hold' == $order_status ) {
					?>
						<option value="<?php echo esc_attr( $valr_status ); ?>" <?php echo in_array( $valr_status, $user_status, true ) ? esc_attr( 'selected' ) : ''; ?>>
						<?php echo esc_attr( $order_status ); ?>
						</option>
					<?php
				}
				endforeach;
			?>
			</select>
			<br><label><i><?php echo esc_html__( 'Select order status that you want to change in order. ', ' addify_eobc' ); ?></i></label>

		<?php
	}
		/**
		 * Main edit_order_settings_section_c start.
		 */
	public function edit_order_settings_section_c() {
	}
		/**
		 * Main edit_order_order_payment_setting_field_callback start.
		 *
		 * @param init $args .
		 */
	public function edit_order_order_payment_setting_field_callback( $args ) {
		global $post;
		$payment_gateways_obj     = new WC_Payment_Gateways();
		$enabled_payment_gateways = $payment_gateways_obj->payment_gateways();

		?>
			<div id="accordion" class="accordion">
				<div>
					<p>
					<table class="addify-table-optoin">
						<tbody>
							<tr class="addify-option-field">
								<td>
								<?php
								$saved_options = (array) get_option( 'adfy_edit_order_order_payment_field' );

								foreach ( $enabled_payment_gateways as $key1 => $payment_gateway ) {

									?>
										<input type="checkbox" name="adfy_edit_order_order_payment_field[]" id="afrfq_enable_name_field" value="<?php echo esc_attr( $key1 ); ?>" 
																																						   <?php
																																							if ( in_array( $key1, $saved_options ) ) {
																																								echo 'checked';
																																							}
																																							?>
																																													>
										<?php echo esc_html( $payment_gateway->title ); ?>
										<br>
									<?php
								}
								?>
								</td>
							</tr>
						</tbody>
					</table>
					</p>
				</div>
			</div>

			<p><?php echo esc_html__( 'Select payment method which you want to show in edit order view page.', 'addify_eobc' ); ?></p>
		<?php
	}
		/**
		 * Main edit_order_settings_section_10 start.
		 */
	public function edit_order_settings_section_10() {
	}
		/**
		 * Main edit_order_order_payment_checkbox_setting_field_callback start.
		 */
	public function edit_order_order_payment_checkbox_setting_field_callback() {
		global $post;
		?>
			<input type="checkbox" name="adfy_edit_order_order_payment_checkbox_field" id="checbox8" value="payment_checkbox" 
			<?php
			if ( get_option( 'adfy_edit_order_order_payment_checkbox_field' ) === 'payment_checkbox' ) {
				echo 'checked';
			}
			?>
																																>
			<p for="checbox8"> <?php esc_html_e( 'Checkbox for enable payment method settings.', 'addify_eobc' ); ?></p>

		<?php
	}
		/**
		 * Main edit_order_settings_section_9 start.
		 */
	public function edit_order_settings_section_9() {
	}
		/**
		 * Main edit_order_order_shipping_setting_field_callback start.
		 *
		 * @param int $args .
		 */
	public function edit_order_order_shipping_setting_field_callback( $args ) {
		?>
			<div class="afpvu_accordian">
			<?php
			$shipping_methods = WC()->shipping->get_shipping_methods();
			?>
				<table class="addify-table-optoin">
					<tbody>
						<tr class="addify-option-field">
							<td>
							<?php
							$saved_options = (array) get_option( 'adfy_edit_order_order_shipping_field' );

							foreach ( $shipping_methods as $key2 => $shipping_method ) {
								?>
									<input type="checkbox" name="adfy_edit_order_order_shipping_field[]" id="adfy_enable_name_field" value="<?php echo esc_attr( $key2 ); ?>" 
																																					   <?php
																																						if ( in_array( $key2, $saved_options ) ) {
																																							echo 'checked';
																																						}
																																						?>
																																												>
									<?php echo esc_html( $shipping_method->method_title ); ?>
									<br>
								<?php
							}
							?>
							</td>
						</tr>
					</tbody>
				</table>
				</p>
			</div>
		</div>
		<p><?php echo esc_html__( 'Select shipping method which you want to show in edit order view page.', 'addify_eobc' ); ?></p>
		<?php
	}
		/**
		 * Main edit_order_settings_section_8 start.
		 */
	public function edit_order_settings_section_8() {
	}
		/**
		 * Main edit_order_by_customer_setting_field_callback start.
		 */
	public function edit_order_order_shipping_checkbox_setting_field_callback() {
		global $post;
		?>
		<input type="checkbox" name="adfy_edit_order_order_shipping_checkbox_field" id="checbox6" value="shipping_checkbox" 
		<?php
		if ( get_option( 'adfy_edit_order_order_shipping_checkbox_field' ) === 'shipping_checkbox' ) {
			echo 'checked';
		}
		?>
																															>
		<p for="checbox6"> <?php esc_html_e( 'Checkbox for enable shipping method settings.', 'addify_eobc' ); ?></p>

		<?php
	}
		/**
		 * Main edit_order_settings_section_7 start.
		 */
	public function edit_order_settings_section_7() {
	}
		/**
		 * Main edit_order_order_product_change_setting_field_callback start.
		 */
	public function edit_order_order_product_change_setting_field_callback() {
		global $post;
		$new_for_search_products_change = (array) get_option( 'adfy_edit_order_order_product_change_field' );
		?>
		<select class="adfy_edit_order_order_product_change_field" name="adfy_edit_order_order_product_change_field[]" multiple style="width: 50%;">
			<span class="suggestion">Search all products. </span>
		<?php
		foreach ( (array) $new_for_search_products_change as $search_product_item ) {

			$product = wc_get_product( $search_product_item );
			if ( $product ) {
				?>
					<option value="<?php echo esc_attr( $search_product_item ); ?>" selected>
					<?php echo esc_attr( $product->get_name() ); ?>

					</option>
				<?php
			}
		}
		?>
		</select>
		<p> <?php esc_html_e( 'Choose specific product that you want to edit in order.', 'addify_eobc' ); ?></p>
		<?php
	}
		/**
		 * Main edit_order_settings_section_6 start.
		 */
	public function edit_order_settings_section_6() {
	}
		/**
		 * Main edit_product_category_change_setting_field_callback start.
		 */
	public function edit_product_category_change_setting_field_callback() {
		global $post;

		$order_product_category = get_terms( 'product_cat' );
		$selected_categories    = (array) get_option( 'adfy_edit_product_category_change_field' );
		?>
		<select name="adfy_edit_product_category_change_field[]" id="adfy_edit_product_category_change_field" class="adfy_edit_product_category_change_field" multiple style="width: 50%;">;
		<?php
		foreach ( $order_product_category  as $selct_order_product_category ) {
			?>
				<option value="<?php echo esc_html( $selct_order_product_category->term_id, 'addify_eobc' ); ?>" 
										  <?php
											if ( in_array( (string) $selct_order_product_category->term_id, (array) $selected_categories, true ) ) {
												echo 'selected';
											}
											?>
																													>
					<?php echo esc_html( $selct_order_product_category->name, 'addify_eobc' ); ?>
				</option>
			<?php

		}
		?>
		</select>
		<p> <?php esc_html_e( 'Choose specific product category that you want to edit in order.', 'addify_eobc' ); ?></p>
		<?php
	}
		/**
		 * Main edit_order_settings_section_14 start.
		 */
	public function edit_order_settings_section_14() {
	}
		/**
		 * Main edit_order_order_product_setting_field_callback start.
		 */
	public function edit_order_order_product_setting_field_callback() {
		global $post;
		?>
		<input type="checkbox" name="adfy_edit_order_order_product_field" id="checbox5" value="product_enable" 
		<?php
		if ( get_option( 'adfy_edit_order_order_product_field' ) === 'product_enable' ) {
			echo 'checked';
		}
		?>
																												>
		<p for="checbox5"> <?php esc_html_e( 'Checkbox for enable product settings.', 'addify_eobc' ); ?></p>

		<?php
	}
		/**
		 * Main edit_order_settings_section_5 start.
		 */
	public function edit_order_settings_section_5() {
	}
		/**
		 * Main edit_order_all_product_setting_callback start.
		 */
	public function edit_order_all_product_setting_callback() {
		global $post;
		?>
		<input type="checkbox" name="adfy_edit_order_all_product_field" id="all_pcat" value="all_product_enable" 
		<?php
		if ( get_option( 'adfy_edit_order_all_product_field' ) === 'all_product_enable' ) {
			echo 'checked';
		}
		?>
																													>
		<p for="all_pcat"> <?php esc_html_e( 'Checkbox for enable all product settings.', 'addify_eobc' ); ?></p>

		<?php
	}
		/**
		 * Main edit_order_all_product_settings_section_c start.
		 */
	public function edit_order_all_product_settings_section_c() {
	}
		/**
		 * Main edit_shipping_label_setting_field_callback start.
		 */
	public function edit_shipping_label_setting_field_callback() {
		global $post;
		$shipping = wc()->checkout()->get_checkout_fields( 'shipping' );

		$save_billing = (array) get_option( 'adfyedit_shipping_label_field' );

		if ( $shipping ) {

			foreach ( $shipping as $field_key => $field ) {
				$field_label = $field['label'];
				?>
				<input type="checkbox" name="adfyedit_shipping_label_field[]" id="adfy_enable_shipping_field" value="<?php echo esc_attr( $field_key ); ?>" 
																																<?php
																																if ( in_array( $field_key, $save_billing ) ) {
																																	echo 'checked';
																																}
																																?>
																																							>
				<?php echo esc_html( $field['label'] ); ?>
				<br>
				<?php
			}
			?>
			<p><?php esc_html_e( 'Select shipping address that you want to change in order.', 'addify_eobc' ); ?></p>
			<?php
		} else {
			return;
		}
	}
		/**
		 * Main edit_order_settings_section_5 start.
		 */
	public function edit_shipping_label_settings_section() {
	}
		/**
		 * Main edit_order_order_status_setting_field_callback start.
		 */
	public function edit_order_order_status_setting_field_callback() {

		global $post;
		$get_status  = get_option( 'adfyedit_order_order_status_field' );
		$user_status = (array) get_option( 'adfyedit_order_order_status_field' );
		?>
		<select id="adfyedit_order_order_status_field" class="adfyedit_order_order_status_field" name="adfyedit_order_order_status_field[]" multiple style="width: 50%;">
		<?php
		foreach ( wc_get_order_statuses() as $valr_status  => $namer_status ) :
			?>
				<option value="<?php echo esc_attr( $valr_status ); ?>" <?php echo in_array( $valr_status, $user_status, true ) ? esc_attr( 'selected' ) : ''; ?>>
				<?php echo esc_attr( $namer_status ); ?>
				</option>
			<?php
			endforeach;
		?>
		</select>
		<br><label><i><?php echo esc_html__( 'Select order status that you want to change in order(leave empty for all). ', ' addify_eobc' ); ?></i></label>

		<?php
	}
		/**
		 * Main edit_order_settings_section_4 start.
		 */
	public function edit_order_settings_section_4() {
	}
		/**
		 * Main shipping_method_order_status_setting_field_callback start.
		 */
	public function shipping_method_order_status_setting_field_callback() {

		global $post;
		$get_status  = get_option( 'shipping_method_order_status_field' );
		$user_status = (array) get_option( 'shipping_method_order_status_field' );
		?>

		<select id="shipping_method_order_status_field" class="shipping_method_order_status_field" name="shipping_method_order_status_field[]" multiple style="width: 50%;">
		<?php
		foreach ( wc_get_order_statuses() as $value_status  => $shipping_status ) :
			if ( 'Pending payment' == $shipping_status || 'On hold' == $shipping_status ) {

				?>
					<option value="<?php echo esc_attr( $value_status ); ?>" <?php echo in_array( $value_status, $user_status, true ) ? esc_attr( 'selected' ) : ''; ?>>
					<?php echo esc_attr( $shipping_status ); ?>
					</option>
				<?php
			}
			endforeach;
		?>
		</select>
		<br><label><i><?php echo esc_html__( 'Select order status that you want to change in order. ', ' addify_eobc' ); ?></i></label>
		<?php
	}
		/**
		 * Main edit_order_settings_section_g start.
		 */
	public function edit_order_settings_section_g() {
	}
		/**
		 * Main edit_order_edit_button_setting_field_callback start.
		 */
	public function edit_order_edit_button_setting_field_callback() {
		?>
		<input type="checkbox" name="adfyedit_order_edit_button_field_0" id="checbox0" value="edit" 
		<?php
		if ( get_option( 'adfyedit_order_edit_button_field_0' ) === 'edit' ) {
			echo 'checked';
		}
		?>
																									>
		<p for="checbox0"> <?php esc_html_e( 'Checkbox for enable edit button settings.', 'addify_eobc' ); ?></p>
		<?php
	}
		/**
		 * Main edit_order_settings_section_0 start.
		 */
	public function edit_order_settings_section_0() {
	}
		/**
		 * Main new_edit_order_status_setting_field_callback start.
		 */
	public function new_edit_order_status_setting_field_callback() {

		global $post;
		$get_status  = get_option( 'adfyedit_order_status_field' );
		$user_status = (array) get_option( 'adfyedit_order_status_field' );
		?>
		<select id="adfyedit_order_status_field" class="adfyedit_order_status_field" name="adfyedit_order_status_field[]" multiple style="width: 50%;">
		<?php
		foreach ( wc_get_order_statuses() as $val_status  => $name_order_status ) :
			?>
				<option value="<?php echo esc_attr( $val_status ); ?>" <?php echo in_array( $val_status, $user_status, true ) ? esc_attr( 'selected' ) : ''; ?>>
				<?php echo esc_attr( $name_order_status ); ?>
				</option>
			<?php
			endforeach;
		?>
		</select>
		<br><label><i><?php echo esc_html__( 'Select order status that you want to change in order(leave empty for all). ', ' addify_eobc' ); ?></i></label>

		<?php
	}
		/**
		 * Main edit_order_settings_section start.
		 */
	public function edit_order_settings_section() {
	}
		/**
		 * Main edit_order_by_customer_setting_field_callback start.
		 */
	public function edit_billing_order_status_setting_field_callback() {

		global $post;
		$get_status  = get_option( 'adfyedit_billing_order_status_field_a' );
		$user_status = (array) get_option( 'adfyedit_billing_order_status_field_a' );
		?>
		<select id="adfyedit_billing_order_status_field_a" class="adfyedit_billing_order_status_field_a" name="adfyedit_billing_order_status_field_a[]" multiple style="width: 50%;">
		<?php
		foreach ( wc_get_order_statuses() as $valr_status  => $name_status ) :
			?>
				<option value="<?php echo esc_attr( $valr_status ); ?>" <?php echo in_array( $valr_status, $user_status, true ) ? esc_attr( 'selected' ) : ''; ?>>
				<?php echo esc_attr( $name_status ); ?>
				</option>
			<?php
			endforeach;
		?>
		</select>
		<br><label><i><?php echo esc_html__( 'Select order status that you want to change in order(leave empty for all). ', ' addify_eobc' ); ?></i></label>

		<?php
	}
		/**
		 * Main edit_order_settings_section start.
		 */
	public function edit_order_settings_section_a() {
	}
		/**
		 * Main edit_order_billing_setting_field_callback start.
		 */
	public function edit_order_billing_setting_field_callback() {
		?>
		<input type="checkbox" name="adfy_edit_order_billing_field" id="checbox1" value="billing" 
		<?php
		if ( get_option( 'adfy_edit_order_billing_field' ) === 'billing' ) {
			echo 'checked';
		}
		?>
																									>
		<p for="checbox1"> <?php esc_html_e( 'Checkbox for enable edit billing address settings.', 'addify_eobc' ); ?></p>
		<?php
	}
		/**
		 * Main edit_order_settings_section start.
		 */
	public function edit_order_settings_section_1() {
	}
		/**
		 * Main edit_order_shipping_setting_field_callback start.
		 */
	public function edit_order_shipping_setting_field_callback() {
		?>
		<input type="checkbox" name="adfyedit_order_shipping_field" id="checbox2" value="shipping" 
		<?php
		if ( get_option( 'adfyedit_order_shipping_field' ) === 'shipping' ) {
			echo 'checked';
		}
		?>
																									>
		<p for="checbox2"> <?php esc_html_e( 'Checkbox for enable edit shipping address settings.', 'addify_eobc' ); ?></p>
		<?php
	}
		/**
		 * Main edit_order_settings_section start.
		 */
	public function edit_order_settings_section_2() {
	}
		/**
		 * Main edit_order_by_customer_setting_field_callback start.
		 */
	public function payment_method_order_status_setting_field_callback() {
		global $post;
		$get_status  = get_option( 'payment_method_order_status_field' );
		$user_status = (array) get_option( 'payment_method_order_status_field' );
		?>
		<select id="payment_method_order_status_field" class="payment_method_order_status_field" name="payment_method_order_status_field[]" multiple style="width: 50%;">
		<?php
		foreach ( wc_get_order_statuses() as $valr_status  => $name_status ) :
			?>
				<option value="<?php echo esc_attr( $valr_status ); ?>" <?php echo in_array( $valr_status, $user_status, true ) ? esc_attr( 'selected' ) : ''; ?>>
				<?php echo esc_attr( $name_status ); ?>
				</option>
			<?php
			endforeach;
		?>
		</select>
		<br><label><i><?php echo esc_html__( 'Select order status that you want to change in order(leave empty for all). ', ' addify_eobc' ); ?></i></label>

		<?php
	}
		/**
		 * Main edit_order_settings_section start.
		 */
	public function edit_order_settings_section_j() {
	}
		/**
		 * Product remove function start.
		 */
	public function product_remove() {

		global $woocommerce, $post;
		$item_nonce = isset( $_POST['nonce'] ) && '' !== $_POST['nonce'] ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : 0;
		if ( isset( $_POST['q'] ) && '' !== $_POST['q'] ) {
			if ( ! wp_verify_nonce( $item_nonce, 'edit_order_url' ) ) {
				die( 'Failed ajax security check!' );
			}
			$item_new = sanitize_text_field( wp_unslash( $_POST['q'] ) );
		} else {
			$item_new = '';
		}
		if ( isset( $_POST['order_item_id'] ) ) {
			$item_id = sanitize_text_field( wp_unslash( $_POST['order_item_id'] ) );
			if ( isset( $_POST['order_id'] ) ) {
				$order_id = sanitize_text_field( wp_unslash( $_POST['order_id'] ) );

				$order = wc_get_order( $order_id );

				$removed_item = $order->get_item( $item_id );

				// Retrieve the product ID and quantity from the removed item
				$product_id = $removed_item['product_id'];
				$quantity = $removed_item['quantity'];

				// Load the product

				$product = wc_get_product( $product_id );

				if ( $product ) {
					// Get the current stock quantity
					$current_stock = $product->get_stock_quantity();
					// Update the stock quantity (add back the quantity of the removed item)
					$new_stock = $current_stock + $quantity;
					$product->set_stock_quantity( $new_stock );
					$product->save();
				}

				$order->remove_item( $item_id );

				$order->save();
			}
		}
	}

		/**
		 * New  ka_apply_for_product_search function start.
		 */
	public function eobc_user_search() {
		$user_nonce = isset( $_POST['nonce'] ) && '' !== $_POST['nonce'] ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : 0;
		if ( isset( $_POST['q'] ) && '' !== $_POST['q'] ) {
			if ( ! wp_verify_nonce( $user_nonce, 'edit_order_url' ) ) {
				die( 'Failed ajax security check!' );
			}
			$pro = sanitize_text_field( wp_unslash( $_POST['q'] ) );
		} else {
			$pro = '';
		}
		$user_data_array  = array();
		$users_result     = new WP_User_Query(
			array(
				'search'         => '*' . esc_attr( $pro ) . '*',
				'search_columns' => array(
					'user_login',
					'user_nicename',
					'user_email',
					'user_url',
				),
			)
		);
		$eobc_users_found = $users_result->get_results();
		if ( ! empty( $eobc_users_found ) ) {
			foreach ( $eobc_users_found as $found ) {
				$title             = $found->display_name . '(' . $found->user_email . ')';
				$user_data_array[] = array( $found->ID, $title ); // array( User ID, User name and email ).
			}
		}
		echo wp_json_encode( $user_data_array );
		die();
	}
		/**
		 * New  adfy_eobc_product_search function start.
		 */
	public function adfy_eobc_product_search() {
		$nonce = isset( $_POST['nonce'] ) && '' !== $_POST['nonce'] ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : 0;
		if ( isset( $_POST['q'] ) && '' !== $_POST['q'] ) {
			if ( ! wp_verify_nonce( $nonce, 'edit_order_url' ) ) {
				die( 'Failed ajax security check!' );
			}
			$new = sanitize_text_field( wp_unslash( $_POST['q'] ) );
		} else {
			$new = '';
		}
		$eobc_data_array = array();
		$args            = array(
			'post_type'   => array( 'product' ),
			'post_status' => 'publish',
			'numberposts' => -1,
			's'           => $new,
		);
		$pros            = get_posts( $args );
		if ( ! empty( $pros ) ) {
			foreach ( $pros as $proo ) {
				$title             = ( mb_strlen( $proo->post_title ) > 50 ) ? mb_substr( $proo->post_title, 0, 49 ) . '...' : $proo->post_title;
				$eobc_data_array[] = array( $proo->ID, $title ); // array( Post ID, Post Title ).
			}
		}
		echo wp_json_encode( $eobc_data_array );
		die();
	}
		/**
		 * File function start.
		 */
	public function adfy_eobc_files() {

		wp_enqueue_script( 'product_js', plugins_url( 'js/product.js', __FILE__ ), true, '1.0', $in_footer = false );
		wp_enqueue_script( 'select_js', plugins_url( 'js/select2.js', __FILE__ ), true, '1.0', $in_footer = false );
		wp_enqueue_script( 'user_js', plugins_url( 'js/user.js', __FILE__ ), true, '1.0', $in_footer = false );
		wp_enqueue_script( 'hide_js', plugins_url( 'js/hide_catp.js', __FILE__ ), true, '1.0', $in_footer = false );
		wp_enqueue_style( 'select_css', plugins_url( 'css/select2.css', __FILE__ ), false, '1.0' );
		wp_enqueue_style( 'hide_css', plugins_url( 'css/hide.css', __FILE__ ), false, '1.0' );

		$eobc_nonce = array(
			'admin_url' => admin_url( 'admin-ajax.php' ),
			'nonce'     => wp_create_nonce( 'edit_order_url' ),
		);
		wp_localize_script( 'select_js', 'edit_order_by_customer', $eobc_nonce );
	}
}
	new Addify_Edit_Order_By_Customer_Admin();
