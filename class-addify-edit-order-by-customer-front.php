<?php

/**
 * Main class start.
 *
 * @package : eobc
 */

if (!defined('ABSPATH')) {
	exit;
}
/**
 * Edit order by customer main start.
 */
class Addify_Edit_Order_By_Customer_Front {
	/**
	 *  Construct function start.
	 */
	public function __construct() {
		add_filter('woocommerce_my_account_my_orders_actions', array( $this, 'add_my_account_my_orders_custom_action' ), 100, 2);
		add_action('init', array( $this, 'adfy_eobc_add_endpoints' ));
		add_filter('woocommerce_get_query_vars', array( $this, 'adfy_eobc_get_query_vars' ), 0);
		add_action('woocommerce_account_edit-order_endpoint', array( $this, 'adfy_eobc_add_endpoints_content' ));
		add_action('wp_loaded', array( $this, 'eobc_update_order_form_data' ));
		add_action('wp_enqueue_scripts', array( $this, 'adfy_eobc_front_files' ));
	}
	/**
	 *  Adfy_eobc_add_endpoints_content function start.
	 */
	public function adfy_eobc_add_endpoints_content() {
		global $post;

		$billing_all_fields  = get_option('adfy_billing_first_name_field');
		$shipping_all_fields = get_option('adfyedit_shipping_label_field');

		$shipping_method_order_status = get_option('shipping_method_order_status_field');
		$order_shipping_method        = get_option('adfy_edit_order_order_shipping_field');

		$specific_user_role    = get_option('eobc_edit_user_field');
		$user_role             = get_option('adfy_edit_user_role_field');
		$order_product         = (array) get_option('adfy_edit_order_order_product_change_field');
		$product_category      = (array) get_option('adfy_edit_product_category_change_field');
		$billing_check         = get_option('adfy_edit_order_billing_field');
		$general_edit_button   = get_option('adfyedit_order_edit_button_field_0');
		$product_check         = get_option('adfy_edit_order_order_product_field');
		$shipping_method_check = get_option('adfy_edit_order_order_shipping_checkbox_field');
		$payment_method_check  = get_option('adfy_edit_order_order_payment_checkbox_field');
		$order_popup_product   = get_option('adfy_edit_order_order_product');

		$order_id       = get_query_var('edit-order');
		$order          = wc_get_order($order_id);
		$order_status   = $order->get_status();
		$order_status   = 'wc-' . $order_status;
		$current_user   = $order->get_user();
		$curr_user_role = current($current_user->roles);
		$products       = wc_get_products($order_id);
		foreach ($products as $id) {
			$product_id = $id->get_id();
		}
		?>
		<form method="post" class="adfy_order_edit">
			<?php
			if (isset($general_edit_button) && !empty($general_edit_button)) {

				$general_flag = false;

				if (in_array('wc-' . $order->get_status(), (array) get_option('adfyedit_order_status_field'))) {
					$general_flag = true;
				} elseif (empty(get_option('adfyedit_order_status_field'))) {
					$general_flag = true;
				}

				if ($general_flag) {

					$user_match = false;

					if (in_array((string) $current_user->ID, (array) $specific_user_role, true)) {
						$user_match = true;
					}
					if (in_array((string) $curr_user_role, (array) $user_role, true)) {
						$user_match = true;
					}
					if (!$user_match) {
						return false;
					}
				}
			}

			if (isset($billing_check) && !empty($billing_check)) {

				$billing_address_flag = false;

				if (in_array('wc-' . $order->get_status(), (array) get_option('adfyedit_billing_order_status_field_a'))) {
					$billing_address_flag = true;
				} elseif (empty(get_option('adfyedit_billing_order_status_field_a'))) {
					$billing_address_flag = true;
				}

				if ($billing_address_flag) {
					?>
					<div>
						<h3><?php esc_html_e('Billing address', 'addify_eobc'); ?></h3>
						<?php

						$billing_fields = wc()->checkout()->get_checkout_fields('billing');

						foreach ($billing_fields as $field_key => $field) {

							if (!in_array($field_key, (array) $billing_all_fields, true)) {
								continue;
							}

							$function_name = 'get_' . $field_key;
							$data          = $order->$function_name();
							woocommerce_form_field($field_key, $field, $data);
						}
						?>
					</div>
				<?php
				}
			}

			$shipping_check = get_option('adfyedit_order_shipping_field');

			if (isset($shipping_check) && !empty($shipping_check)) {

				$shipping_address_flag = false;

				if (in_array('wc-' . $order->get_status(), (array) get_option('adfyedit_order_order_status_field'))) {
					$shipping_address_flag = true;
				} elseif (empty(get_option('adfyedit_order_order_status_field'))) {
					$shipping_address_flag = true;
				}

				if ($shipping_address_flag) {
					?>
					<div>

						<h3><?php esc_html_e('Shipping address', 'addify_eobc'); ?></h3>

						<?php

						$shipping_fields = wc()->checkout()->get_checkout_fields('shipping');

						foreach ($shipping_fields as $shipping_key => $shipping_field) {

							if (!in_array($shipping_key, (array) $shipping_all_fields, true)) {
								continue;
							}

							$shipping_function_name = 'get_' . $shipping_key;

							$shipping_data = $order->$shipping_function_name();
							woocommerce_form_field($shipping_key, $shipping_field, $shipping_data);
						}
						?>
					</div>
				<?php
				}
			}

			if ('product_enable' == get_option('adfy_edit_order_order_product_field')) {

				$product_flag = false;

				if (in_array('wc-' . $order->get_status(), (array) get_option('adfy_edit_order_status_field'))) {
					$product_flag = true;
				} elseif (empty(get_option('adfy_edit_order_status_field'))) {
					$product_flag = false;
				}

				if ($product_flag) {
					?>
					<h3><?php esc_html_e('Cart items', 'addify_eobc'); ?></h3>
					<table width="100%" class="popup_table">
						<tbody>
							<tr>
								<th></th>
								<th></th>
								<th> <?php echo esc_html__('Name', 'addify_eobc'); ?></th>
								<th> <?php echo esc_html__('Price', 'addify_eobc'); ?></th>
								<th> <?php echo esc_html__('Quantity', 'addify_eobc'); ?></th>
								<th> <?php echo esc_html__('Subtotal', 'addify_eobc'); ?></th>
							</tr>
							<?php
							foreach ($order->get_items() as $item) {

								$product = $item->get_product();

								$product_id = $product->is_type('variation') ? $product->get_parent_id() : $product->get_id();

								$product_match = false;

								if ('all_product_enable' === get_option('adfy_edit_order_all_product_field')) {
									$product_match = true;
								}

								if (in_array($product_id, (array) $order_product)) {

									$product_match = true;
								}

								foreach ($product_category as $cat) {

									if (!empty($cat) && has_term($cat, 'product_cat', $product_id)) {

										$product_match = true;
									}
								}

								if ($product_match) {

									$product_name = $item->get_name();
									$quantity     = $item->get_quantity();
									$subtotal     = $item->get_subtotal();
									$product      = $item->get_product();
									$active_price = $product->get_price();
									$image        = $product->get_image();
									$item_id      = $item->get_id();
									?>

									<tr>
										<td><button class="remove-order-item" data-item_id="<?php echo esc_attr($item_id); ?>">x</button></td>
										<td>
											<p class="btn2"><?php echo wp_kses_post($image); ?></p>
										</td>
										<td><?php echo esc_attr($product_name); ?></td>
										<td><?php echo esc_attr($active_price); ?></td>
										<td><input type="number" class="adfy_eobc_number" name="edit_order_quantity[<?php echo intval($item->get_id()); ?>]" min="0" value="<?php echo esc_attr($quantity); ?>"></td>
										<td><?php echo esc_attr($subtotal); ?></td>
								<?php
								}
							}
							?>
									</tr>
						</tbody>
					</table>
					<button class="add_product">Add Product(s)</button>
					<div id="myModal_popup" class="popup_modal">
						<div class="popup-modal-box">
							<div class="popup-modal-header">
								<b>
									<h3><?php esc_html_e('Add Product', 'addify_eobc'); ?><span class="close_popup">&times;</span>
								</b></h3>
							</div>
							<div class="popup-modal-content">
								<h4><?php esc_html_e('Product', 'addify_eobc'); ?></h4>
								<select class="adfy_edit_order_product" name="adfy_edit_order_product" data-placeholder="<?php esc_html_e('Select product...', 'addify_eobc'); ?>">

									<?php
									foreach ((array) $order_popup_product as $search_product_item) {

										$product = wc_get_product($search_product_item);

										if ($product) {
											?>
											<option value="<?php echo esc_attr($search_product_item); ?>" selected>
												<?php echo esc_attr($product->get_name()); ?>

											</option>
									<?php
										}
									}
									?>
								</select>
								<div class="adf_eobc_quantity">
									<h4><?php esc_html_e('Quantity', 'addify_eobc'); ?></h4>
									<input type="number" name="number_quantity" min="0" value="0">
								</div>
								<div class="adf_add_btn">
									<input type="hidden" name="order_id_hidden" class="edit_order_id_add" value="<?php echo esc_attr($order_id); ?>">
									<input type="submit" name="add_order" id="add_order" value="Add" class="modal-popup-btn">
								</div>
							</div>

						</div>
					</div>

				<?php
				}
			}

			$shipping_method_flag = false;

			if (in_array('wc-' . $order->get_status(), (array) get_option('shipping_method_order_status_field'))) {
				$shipping_method_flag = true;
			} elseif (empty(get_option('shipping_method_order_status_field'))) {
				$shipping_method_flag = false;
			}

			if ('shipping_checkbox' == get_option('adfy_edit_order_order_shipping_checkbox_field') && $shipping_method_flag) {
				?>
				<div>
					<h3><?php esc_html_e('Shipping methods', 'addify_eobc'); ?></h3>
					<?php

					$package                            = array();
					$package['destination']['country']  = $order->get_shipping_country();
					$package['destination']['state']    = $order->get_shipping_state();
					$package['destination']['postcode'] = $order->get_shipping_postcode();
					$shipping_zone                      = WC_Shipping_Zones::get_zone_matching_package($package);

					$zone = $shipping_zone->get_zone_name();

					$available_methods = $shipping_zone->get_shipping_methods();

					$chosen_method = $order->get_shipping_methods();

					if (!empty($chosen_method)) {
						$chosen_method = current($chosen_method)->get_instance_id();
					}

					$index = '';

					$shipping_methods = WC()->shipping->get_shipping_methods();

					if ($available_methods) :

						?>
						<ul id="shipping_method" class="woocommerce-shipping-methods">
							<?php
							foreach ($available_methods as $method) :
								?>
								<li>
									<?php
									if (in_array((string) $method->id, (array) $order_shipping_method)) {
										if (1 < count($available_methods)) {
											printf('<input type="radio" name="shipping_method[%1$d]" data-index="%1$d" id="shipping_method_%1$d_%2$s" value="%3$s" class="shipping_method" %4$s />', esc_attr($index), esc_attr(sanitize_title($method->instance_id)), esc_attr($method->instance_id), checked($method->instance_id, $chosen_method, false));
										} else {

											printf('<input type="hidden" name="shipping_method[%1$d]" data-index="%1$d" id="shipping_method_%1$d_%2$s" value="%3$s" class="shipping_method" />', esc_attr($index), esc_attr(sanitize_title($method->instance_id)), esc_attr($method->instance_id)); // WPCS: XSS ok.
										}
										printf('<label for="shipping_method_%1$s_%2$s">%3$s</label>', esc_attr($index), esc_attr(sanitize_title($method->instance_id)), esc_attr($method->get_title()));
										do_action('woocommerce_after_shipping_rate', $method, $index);
										?>
								</li>
						<?php
									}
								endforeach;
							?>
						</ul>
					<?php endif; ?>
				</div>
			<?php
			}

			$payment_flag = false;

			if (in_array('wc-' . $order->get_status(), (array) get_option('payment_method_order_status_field'))) {
				$payment_flag = true;
			} elseif (empty(get_option('payment_method_order_status_field'))) {
				$payment_flag = true;
			}

			if ('payment_checkbox' == get_option('adfy_edit_order_order_payment_checkbox_field') && $payment_flag) :
				?>

				<div class="move">
					<h3><?php esc_html_e('Payment methods', 'addify_eobc'); ?></h3>
					<ul class="wc_payment_methods">
						<?php

						$billing_available_gateways = WC()->payment_gateways()->get_available_payment_gateways();

						if (!empty($billing_available_gateways)) {
							foreach ($billing_available_gateways as $key => $gateway) {

								if ($order->get_payment_method() == $key) {
									$gateway->chosen = true;
								}
								if (!empty(get_option('adfy_edit_order_order_payment_field'))) {

									if (empty(get_option('adfy_edit_order_order_payment_field')) || in_array($key, (array) get_option('adfy_edit_order_order_payment_field'))) {
										wc_get_template('checkout/payment-method.php', array( 'gateway' => $gateway ));
									}
								}
							}
							if ($gateway->has_fields()) :
								echo '<div class="payment_box payment_method_' . esc_html($gateway->id) . '" ' . ( $gateway->chosen ? '' : 'style="display:none;"' ) . '>';
								$gateway->payment_fields();
								echo '</div>';
							endif;
						}
						?>
					</ul>
				</div>
			<?php
			endif;
			?>
			<?php

			if ( ( isset($allow_button) && !empty($allow_button) && !empty($billing_check) ) ||  ( isset($billing_check)  || isset($shipping_check) || !empty($shipping_check) || 'product_enable' == get_option('adfy_edit_order_order_product_field') || 'shipping_checkbox' == get_option('adfy_edit_order_order_shipping_checkbox_field') || 'payment_checkbox' == get_option('adfy_edit_order_order_payment_checkbox_field') ) ) {

				?>
				<div class="save_changes">
					<input type="hidden" name="order_id" class="edit_order_id" value="<?php echo esc_attr($order_id); ?>">
					<input type="submit" name="update_order" value="Save Changes">
				</div>
			<?php
			}
			?>
		</form>
<?php
	}

	/**
	 *  Eobc_update_order_data function start.
	 */
	public function eobc_update_order_form_data() {
		global $post;

		$update_nonce_button = isset($_POST['nonce']) && '' !== $_POST['nonce'] ? sanitize_text_field(wp_unslash($_POST['nonce'])) : 0;
		if (isset($_POST['q']) && '' !== $_POST['q']) {
			if (!wp_verify_nonce($update_nonce_button, 'af_edit_order_nonce')) {
				die('Failed ajax security check!');
			}
			$pro = sanitize_text_field(wp_unslash($_POST['q']));
		} else {
			$pro = '';
		}
		if (isset($_POST['order_id'])) {
			$order_id = sanitize_text_field(wp_unslash($_POST['order_id']));
			$order    = wc_get_order($order_id);
		}

		if (isset($_POST['update_order'])) {

			$billing_fields_validate = wc()->checkout()->get_checkout_fields('billing');

			foreach ($billing_fields_validate as $billing_field_key_validate => $billing_field_validate) {

				if ($billing_field_validate['required'] && isset($_POST[ $billing_field_key_validate ]) && empty($_POST[ $billing_field_key_validate ])) {
					wc_add_notice('Billing ' . $billing_field_validate['label'] . ' field is Required!', 'error');
				}
			}

			$shipping_fields_validate = wc()->checkout()->get_checkout_fields('shipping');

			foreach ($shipping_fields_validate as $shipping_field_key_validate => $shipping_field_validate) {

				if ($shipping_field_validate['required'] && isset($_POST[ $shipping_field_key_validate ]) && empty($_POST[ $shipping_field_key_validate ])) {
					wc_add_notice('Shipping ' . $shipping_field_validate['label'] . ' field is Required!', 'error');
				}
			}

			$calculate_tax_for = array(
				'country'  => $order->get_shipping_country(),
				'state'    => $order->get_shipping_state(),
				'postcode' => $order->get_shipping_postcode(),
				'city'     => $order->get_shipping_city(),
			);

			foreach ($order->get_items() as $item_id => $item) {

				if (isset($_POST['edit_order_quantity'][ $item->get_id() ])) {

					$product = $item->get_product();
					$product_id = $item->get_product_id();

					$update_args = array(
						'order_item_id'       => $item->get_id(),
						'order_item_qty'      => intval($_POST['edit_order_quantity'][ $item->get_id() ]),
						'order_item_subtotal' => intval($_POST['edit_order_quantity'][ $item->get_id() ]) * $product->get_price(),
					);

					if ($update_args['order_item_qty'] > 0) {



						$old_quantity = $item->get_quantity();
						$new_quantity = intval($_POST['edit_order_quantity'][ $item->get_id() ]);


						// Calculate the difference in quantity
						$quantity_diff = $new_quantity - $old_quantity;



						if ($quantity_diff < 0) {


							wc_update_product_stock($product_id, -$quantity_diff, 'increase');
						} else {
							wc_update_product_stock($product_id, $quantity_diff, 'decrease');
						}

						// Update the product stock


						$item->set_quantity($update_args['order_item_qty']);
						$item->set_subtotal($update_args['order_item_subtotal']);

						$item->calculate_taxes($calculate_tax_for);

						$item->set_total($item->get_subtotal() + $item->get_total_tax());

						$item->save();
					} else {
						$old_quantity = $item->get_quantity();
						$new_quantity = intval($_POST['edit_order_quantity'][ $item->get_id() ]);
						$quantity_diff = $new_quantity - $old_quantity;
						if ($quantity_diff < 0) {


							wc_update_product_stock($product_id, -$quantity_diff, 'increase');
						} else {
							wc_update_product_stock($product_id, $quantity_diff, 'decrease');
						}
						$order->remove_item($item->get_id());
					}
				}
			}

			$alll = get_option('eobc_edit_email_field');

			$eobc_user_role       = wp_get_current_user();
			$eobc_curr_user_email = $eobc_user_role->user_email;
			$current_user_name    = $eobc_user_role->user_login;

			$billing_fields_form = wc()->checkout()->get_checkout_fields('billing');

			foreach ($billing_fields_form as $billing_field_key => $billing_field) {

				if (isset($_POST[ $billing_field_key ])) {
					call_user_func_array(array( $order, 'set_' . $billing_field_key ), array( sanitize_text_field(wp_unslash($_POST[ $billing_field_key ])) ));
				}
			}

			$shipping_fields_form = wc()->checkout()->get_checkout_fields('shipping');

			foreach ($shipping_fields_form as $shipping_field_key => $field) {

				if (isset($_POST[ $shipping_field_key ])) {
					call_user_func_array(array( $order, 'set_' . $shipping_field_key ), array( sanitize_text_field(wp_unslash($_POST[ $shipping_field_key ])) ));
				}
			}

			if (isset($_POST['payment_method'])) {

				$payment_method = sanitize_text_field(wp_unslash($_POST['payment_method']));

				$_available_gateways = WC()->payment_gateways()->get_available_payment_gateways();

				if (isset($_available_gateways[ $payment_method ])) {

					$order->set_payment_method($_available_gateways[ $payment_method ]);
				}

				if ('bacs' || 'cod' || 'cheque' === $payment_method) {

					$order->set_status('on-hold');
				} else {

					$order->set_status('pending');
				}
			}

			if (isset($_POST['shipping_method'])) {

				$shipping_nonce = isset($_POST['nonce']) && '' !== $_POST['nonce'] ? sanitize_text_field(wp_unslash($_POST['nonce'])) : 0;
				if (isset($_POST['q']) && '' !== $_POST['q']) {
					if (!wp_verify_nonce($shipping_nonce, 'af_edit_order_nonce')) {
						die('Failed ajax security check!');
					}
					$pro = sanitize_text_field(wp_unslash($_POST['q']));
				} else {
					$pro = '';
				}
				if (isset($_POST['shipping_method'])) {

					$new_method_id = current(sanitize_meta('', wp_unslash($_POST['shipping_method']), ''));

					foreach ($order->get_items('shipping') as $item_id => $item) {

						$shipping_zone = WC_Shipping_Zones::get_zone_by('instance_id', $item->get_instance_id());

						$shipping_methods = $shipping_zone->get_shipping_methods();

						foreach ($shipping_methods as $instance_id => $shipping_method) {

							if ($shipping_method->is_enabled() && $instance_id == $new_method_id) {

								$item->set_method_title($shipping_method->get_title());
								$item->set_method_id($shipping_method->get_rate_id());
								$item->set_instance_id($shipping_method->get_instance_id());

								if (isset($shipping_method->cost)) {
									$item->set_total($shipping_method->cost);
								}

								$item->calculate_taxes($calculate_tax_for);

								$item->save();
								break;
							}
						}
					}
				}
			}

			$order->calculate_taxes($calculate_tax_for);
			$order->calculate_totals();
			$order->save();

			$name  = $current_user_name;
			$email = $eobc_curr_user_email;

			// php mailer variables.
			$to = get_option('woocommerce_email_from_address');
			if (isset($_POST['order_id'])) {
				$order_id         = sanitize_text_field(wp_unslash($_POST['order_id']));
				$eobc_order       = wc_get_order($order_id);
				$eobc_customer_id = $eobc_order->get_customer_id();

				wc()->mailer()->emails['adfy_eobc_email_template']->trigger($order_id);
			}
		}
	}
	/**
	 *  Add_my_account_my_orders_custom_action function start.
	 *
	 * @param array $actions .
	 *
	 * @param array $order .
	 */
	public function add_my_account_my_orders_custom_action( $actions, $order ) {

		$edit_button       = get_option('adfyedit_order_edit_button_field_0');
		$specific_user     = get_option('eobc_edit_user_field');
		$button_user_role  = get_option('adfy_edit_user_role_field');
		$current_user      = $order->get_user();
		$current_user_role = current($current_user->roles);

		$edit_button_user_role = false;

		if (in_array((string) $current_user->ID, (array) $specific_user, true)) {
			$edit_button_user_role = true;
		}
		if (in_array((string) $current_user_role, (array) $button_user_role, true)) {
			$edit_button_user_role = true;
		}

		if (!empty($edit_button) && $edit_button_user_role) {

			$general_flag_edit = false;

			if (in_array('wc-' . $order->get_status(), (array) get_option('adfyedit_order_status_field'))) {
				$general_flag_edit = true;
			} elseif (empty(get_option('adfyedit_order_status_field'))) {
				$general_flag_edit = true;
			}

			if ($general_flag_edit) {

				$actions['edit-order'] = array(
					'name' => __('Edit Order', 'addify_eobc'),
					'url'  => wc_get_endpoint_url('edit-order', $order->get_id()),

				);
			}
		}
		return $actions;
	}
	/**
	 * Register new endpoint to use inside My Account page.
	 */
	public function adfy_eobc_add_endpoints() {
		add_rewrite_endpoint('edit-order', EP_ALL, true);
		flush_rewrite_rules();
	}
	/**
	 * Adfy_eobc_get_query_vars var.
	 *
	 * @param array $vars .
	 * @return array
	 */
	public function adfy_eobc_get_query_vars( $vars ) {
		$vars[' edit-order'] = 'edit-order';
		return $vars;
	}
	/**
	 * File function start.
	 */
	public function adfy_eobc_front_files() {
		wp_enqueue_style('hide_css', plugins_url('css\hide.css', __FILE__), false, '1.0');
		wp_enqueue_script('select2_js_front', plugins_url('js/select2.js', __FILE__), array( 'jquery' ), '1.0', $in_footer = false);
		wp_enqueue_style('select2_css_front', plugins_url('css/select2.css', __FILE__), false, '1.0');
		wp_enqueue_script('cart_popup', plugins_url('js/popup.js', __FILE__), array( 'jquery' ), '1.0.7', $in_footer = false);
		$order_nonce_front = array(
			'admin_url' => admin_url('admin-ajax.php'),
			'nonce'     => wp_create_nonce('af_edit_order_nonce'),
		);
		wp_localize_script('cart_popup', 'edit_order_by_customer', $order_nonce_front);
	}
}
new Addify_Edit_Order_By_Customer_Front();