<?php
/**
 * Plugin Name: Edit Order by Customer
 *
 * Plugin URI: https://woocommerce.com/products/edit-order-by-customer/
 *
 * Description:  Allow your customers to edit specific details of your order based on the current status.
 *
 * Version:1.1.1
 *
 * Author: Addify
 *
 * Domain Path: /languages
 *
 * Author URI: https://woocommerce.com/vendor/addify/
 * License:  GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * Text Domain: addify_eobc
 *
 * Woo: 8590445:47f90cfb19c34490f0c401d03985657e
 *
 * WC requires at least: 3.0.9
 *
 * WC tested up to: 8.*.*
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Edit order by customer main class start.
 */
class Addify_Edit_Order_By_Customer_Main {
	/**
	 * Edit order by customer  main public constructor start.
	 */
	public function __construct() {

			$this->adfy_mer_plugin_global_vars_defined();
			// Edit order by customer  text domain.
			add_action( 'wp_loaded', array( $this, 'adfy_eobc_text_domain' ) );
			add_action( 'wp_ajax_adfy_eobc_front_product_search', array( $this, 'adfy_eobc_front_product_search' ) );
			add_action( 'wp_ajax_adfy_insert_product_row', array( $this, 'adfy_insert_product_row' ) );
			add_filter( 'woocommerce_email_classes', array( $this, 'eobc_include_new_email_file' ), 90, 1 );
			// Edit order by customer  Include other Files load.
		if ( is_admin() ) {
			// Edit order by customer  include Admin Class files.
			require_once AEOBC_PLUGIN_DIR . 'class-addify-edit-order-by-customer-admin.php';
		} else {
			// Edit order by customer  include Admin Class files.
			require_once AEOBC_PLUGIN_DIR . 'class-addify-edit-order-by-customer-front.php';
		}

		// HOPS compatibility
		add_action( 'before_woocommerce_init', array( $this, 'eobc_HOPS_Compatibility' ) );

		add_action( 'plugins_loaded', array( $this, 'eobc_checks' ) );
	}

	public function eobc_HOPS_Compatibility() {

		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	}

	public function eobc_checks() {

		// Check for multisite.
		if ( ! is_multisite() && ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

			add_action( 'admin_notices', array( $this, 'eobc_admin_notice' ) );
		}
	}

	public function eobc_admin_notice() {

		// Deactivate the plugin.
			deactivate_plugins( __FILE__ );

			$cstmonum_woo_check = '<div id="message" class="error">
                <p><strong>' . __( 'Edit Order by Customer plugin is inactive.', 'addify_eobc' ) . '</strong> The <a href="http://wordpress.org/extend/plugins/woocommerce/">WooCommerce plugin</a> ' . __( 'must be active for this plugin to work. Please install &amp; activate WooCommerce.', 'addify_eobc' ) . ' »</p></div>';
		echo wp_kses_post( $cstmonum_woo_check );
	}


	/**
	 * Constructor of class AF_Abandoned_Cart_Admin.
	 *
	 * @param int $emails .
	 */
	public function eobc_include_new_email_file( $emails ) {
		require_once 'class-addify-eobc-email.php';
		// create an instance of file and set in a unique index of emails array.
		$emails['adfy_eobc_email_template'] = new Addify_Eobc_Email();
		return $emails;
	}
	/**
	 * Adfy_insert_product_row function start.
	 */
	public function adfy_insert_product_row() {

		if ( isset( $_POST['nonce'] ) && ! empty( $_POST['nonce'] ) ) {

			$nonce = sanitize_text_field( wp_unslash( $_POST['nonce'] ) );
		} else {
			$nonce = 0;
		}

		if ( ! wp_verify_nonce( $nonce, 'af_edit_order_nonce' ) ) {

			die( 'Failed ajax security check!' );
		}

		$product_id = isset( $_POST['product_id'] ) ? intval( wp_unslash( $_POST['product_id'] ) ) : 0;

		$quantity = isset( $_POST['quantity'] ) ? intval( wp_unslash( $_POST['quantity'] ) ) : 0;
		$order_id = isset( $_POST['order_id'] ) ? intval( wp_unslash( $_POST['order_id'] ) ) : 0;

		$form_data['product_id'] = $product_id;

		$form_data['quantity'] = $quantity;

		$product = wc_get_product( $product_id );

		$product->get_image();

		$stock_status = $product->get_stock_status();
		if ( 0 === $quantity ) {
			wp_send_json(
				array(
					'success' => false,
					/* translators: %s: Product name */
					'message' => sprintf( __( ' For “%s” You must enter 1 quantity.', 'addify_eobc' ), $product->get_name() ),
				)
			);
		} elseif ( $quantity < 0 ) {
			wp_send_json(
				array(
					'success' => false,
					/* translators: %s: Product name */
					'message' => sprintf( __( ' For “%s” You must enter positive number for quantity.', 'addify_eobc' ), $product->get_name() ),
				)
			);
		}

		if ( $product->is_in_stock() ) {

			if ( ! $product->has_enough_stock( $quantity ) ) {

				wp_send_json(
					array(
						'success' => false,
						/* translators: %s: Product name */
						'message' => sprintf( __( ' “%1$s” has not enough stock. You can add only %2$s quantity.', 'addify_eobc' ), $product->get_name(), $product->get_stock_quantity() ),
					)
				);
			}
		} else {

			wp_send_json(
				array(
					'success' => false,
					/* translators: %s: Product name */
					'message' => sprintf( __( ' “%s” is out of stock.', 'addify_eobc' ), $product->get_name() ),
				)
			);
		}

		$order = wc_get_order( $order_id );

		foreach ( $order->get_items() as $item_id => $item ) {

			if ( 'variation' == $product->get_type() && $form_data['product_id'] == $item->get_variation_id() ) {
				$order->remove_item( $item->get_id() );
				$quantity  = $item->get_quantity();
				$quantity += $form_data['quantity'];
				break;
			}

			if ( $form_data['product_id'] == $item->get_product_id() ) {

				$quantity  = $item->get_quantity();
				$quantity += $form_data['quantity'];
				$order->remove_item( $item->get_id() );
				break;
			}
		}

		$order->add_product( $product, $quantity );

		$order->calculate_totals();

		if ( $product ) {
			$new_stock = $product->get_stock_quantity() - $quantity;
			$product->set_stock_quantity( $new_stock );
			$product->save();
		}

		if ( $order->save() ) {

			ob_start();
			include AEOBC_PLUGIN_DIR . 'cart-table.php';
			$cart_table = ob_get_clean();

			wp_send_json(
				array(
					'success'    => true,
					'cart-table' => $cart_table,
				)
			);

		} else {

			wp_send_json(
				array(
					'success' => false,
					/* translators: %s: Product name */
					'message' => sprintf( __( 'Cart table is not update for “%s”.', 'addify_eobc' ), $product->get_name() ),
				)
			);
		}
	}
	/**
	 *  Mer_plugin_global_vars_defined.
	 */
	public function adfy_mer_plugin_global_vars_defined() {
		if ( ! defined( 'AEOBC_URL' ) ) {
			define( 'AEOBC_URL', plugin_dir_url( __FILE__ ) );
		}
		if ( ! defined( 'AEOBC_BASENAME' ) ) {
			define( 'AEOBC_BASENAME', plugin_basename( __FILE__ ) );
		}
		if ( ! defined( 'AEOBC_PLUGIN_DIR' ) ) {
			define( 'AEOBC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}
	}
	/**
	 * Text domain upload function start.
	 */
	public function adfy_eobc_text_domain() {
		if ( function_exists( 'load_plugin_textdomain' ) ) {
				load_plugin_textdomain( 'addify_eobc', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}
	}
	/**
	 * New  adfy_eobc_front_product_search function start.
	 */
	public function adfy_eobc_front_product_search() {
			$nonce = isset( $_POST['nonce'] ) && '' !== $_POST['nonce'] ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : 0;
		if ( isset( $_POST['q'] ) && '' !== $_POST['q'] ) {
			if ( ! wp_verify_nonce( $nonce, 'af_edit_order_nonce' ) ) {
				die( 'Failed ajax security check!' );
			}
				$new = sanitize_text_field( wp_unslash( $_POST['q'] ) );
		} else {
						$new = '';
		}

		$order_slected_roduct          = (array) get_option( 'adfy_edit_order_order_product_change_field' );
		$order_slected_roduct_catogery = (array) get_option( 'adfy_edit_product_category_change_field' );
		$all_product                   = get_option( 'adfy_edit_order_all_product_field' );

		if ( ! empty( $all_product ) ) {

			$data_array = array();
			$arg        = array(
				'post_type'   => array( 'product_variation', 'product' ),
				'post_status' => 'publish',
				's'           => $new,
				'orderby'     => 'relevance',
				'numberposts' => 50,
			);
			$pros_data  = get_posts( $arg );

			if ( ! empty( $pros_data ) ) {

				foreach ( $pros_data as $pro_arr ) {

					$product = wc_get_product( $pro_arr->ID );

					if ( ! $product || $product->is_type( 'variable' ) ) {
						continue;
					}

					$title = $product->get_name();

					$data_array[] = array( $product->get_id(), $title ); // array( Post ID, Post Title ).
				}
			}
			wp_send_json( $data_array );

		} else {

			$args = array(
				'post_type'   => array( 'product_variation', 'product' ),
				'post_status' => 'publish',
				's'           => $new,
				'orderby'     => 'relevance',
				'numberposts' => 50,
				'tax_query'   => array(
					array(
						'taxonomy' => 'product_cat',
						'field'    => 'term_id',
						'terms'    => $order_slected_roduct_catogery,
					),
				),
			);

			$query = new WP_Query( $args );

			$cat = get_posts( $args );

			$data_array = array();
			$args       = array(
				'post_type'   => array( 'product_variation', 'product' ),
				'post_status' => 'publish',
				's'           => $new,
				'orderby'     => 'relevance',
				'numberposts' => 50,
				'post__in'    => $order_slected_roduct,
			);
			$pros       = get_posts( $args );

			$combine_arr = array();
			$combine_arr = array_merge( $cat, $pros );

			if ( ! empty( $combine_arr ) ) {

				foreach ( $combine_arr as $pro_post ) {

					$product = wc_get_product( $pro_post->ID );

					if ( ! $product || $product->is_type( 'variable' ) ) {

						$children = $product->get_children();

						foreach ( $children as $child_id ) {

							$variation = wc_get_product( $child_id );

							$variation_attributes = $variation->get_variation_attributes();

							if ( in_array( 'any', $variation_attributes ) ) {
								continue;
							}

							$data_array[] = array( $variation->get_id(), $variation->get_name() );
						}

						continue;
					}

					$title = $product->get_name();

					$data_array[] = array( $product->get_id(), $title ); // array( Post ID, Post Title ).
				}
			}
			wp_send_json( $data_array );
		}
	}
}

new Addify_Edit_Order_By_Customer_Main();
