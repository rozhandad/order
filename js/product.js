
/**
 * Main custom image function start.

 * @package WOOCP
 */

jQuery(function($){

	$(".adfy_edit_chnage_role_status_field" ).select2();
	$(".adfy_edit_order_status_field").select2();
	$(".adfy_edit_order_order_payment_field" ).select2();
	$(".adfyedit_order_order_status_field" ).select2();
	$(".adfy_edit_order_order_shipping_field" ).select2();
	$(".adfyedit_billing_order_status_field_a").select2();
	$(".select_field" ).select2();
	$(".shipping_method_order_status_field").select2();
	$(".payment_method_order_status_field").select2();
	$(".adfyedit_order_status_field").select2();

	var admin_url = edit_order_by_customer.admin_url;
	var nonce     = edit_order_by_customer.nonce;
	jQuery('.adfy_edit_product_category_change_field').select2({
	});
	jQuery('.adfy_edit_order_order_product_change_field').select2({
		ajax: {
			url: admin_url, // AJAX URL is predefined in WordPress admin.
			dataType: 'json',
			type: 'POST',
			delay: 250, // Delay in ms while typing when to perform a AJAX search.
			data: function (params) {
				return {
					q: params.term, // search query
					action: 'adfy_eobc_product_search', // AJAX action for admin-ajax.php.
					nonce: nonce // AJAX nonce for admin-ajax.php.
				};
			},
			processResults: function ( data ) {
				var options = [];
				if (data ) {
					 // data is the array of arrays, and each of them contains ID and the Label of the option.
					$.each(
						data, function ( index, text ) {
							// do not forget that "index" is just auto incremented value.
							options.push({ id: text[0], text: text[1]  });
						}
						);
				}
				return {
					results: options
				};
			},
			cache: true
		},
		multiple: true,
	});
});
