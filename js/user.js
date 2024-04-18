/**
 * Main sus function start.

 * @package SUS
 */

jQuery(document).ready(function($){

	var admin_url = edit_order_by_customer.admin_url;
	var nonce     = edit_order_by_customer.nonce;
	jQuery('.eobc_edit_user_field').select2({
		ajax: {
			url: ajaxurl, // AJAX URL is predefined in WordPress admin.
			dataType: 'json',
			type: 'POST',
			delay: 250, // Delay in ms while typing when to perform a AJAX search.
			data: function (params) {
				return {
					q: params.term, // search query
					action: 'eobc_user_search', // AJAX action for admin-ajax.php.
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
