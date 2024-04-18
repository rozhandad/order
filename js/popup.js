
/**
 * Main custom image function start.

 * @package WOOCP
 */
var popupmodal = document.getElementById("myModal_popup");

jQuery(document).ready(function($){
	 var admin_url = edit_order_by_customer.admin_url;
	 var nonce     = edit_order_by_customer.nonce;

	jQuery(document).on('click', '.remove-order-item', function(){
				var order_item_id  = jQuery(this).attr("data-item_id");
				var current_button = $(this);

				current_button.closest('tr').css( 'opacity', '0.4' );
				jQuery.ajax({
					type: 'POST',
					dataType: 'json',
					url: admin_url,
					data: {
						action: "remove_item_from_order", 
						order_item_id: order_item_id,
						order_id : jQuery('input.edit_order_id').val()
					},success: function(data){

						current_button.closest('tr').remove();
					}
				});
				return false;
	});

	jQuery('.add_product').on('click', function(e){
		e.preventDefault();

		// Get the modal
		

		popupmodal.style.display = "block";

	});

	var popupmodal = document.getElementById("myModal_popup");

	jQuery('.close_popup').on('click', function(e){
		e.preventDefault();
		// Get the modal.
		popupmodal.style.display = "none";

	});

	// When the user clicks anywhere outside of the modal, close it
	window.onclick = function(event) {
		if (event.target == popupmodal) {
			popupmodal.style.display = "none";
		}
	}

	jQuery('.adfy_edit_order_product').select2({
		ajax: {
			url: admin_url, // AJAX URL is predefined in WordPress admin.
			dataType: 'json',
			type: 'POST',
			delay: 250, // Delay in ms while typing when to perform a AJAX search.
			data: function (params) {
				return {
					q: params.term, // search query
					action: 'adfy_eobc_front_product_search', // AJAX action for admin-ajax.php.
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
			cache: false
		},
		multiple: false,
		placeholder: 'Choose Products',
		minimumInputLength: 3 // the minimum of symbols to input before perform a search.
	});

	$('div#myModal_popup input#add_order').click( function(event){

		event.preventDefault();

		$.ajax({

			url: admin_url,
			type: 'POST',
			data: {
				action     : 'adfy_insert_product_row',
				nonce      : nonce,
				order_id   : jQuery('input.edit_order_id_add').val(),
				product_id : jQuery('div#myModal_popup select.adfy_edit_order_product').val(),
				quantity   : jQuery('div#myModal_popup input[name="number_quantity"]').val(),
			},
			success: function (response) {

				if ( response['success'] ) {

					$('div#myModal_popup').hide();
					jQuery('input.edit_order_id_add').removeClass('loading');
					jQuery('input.edit_order_id_add').css('opacity', '1');
					$('.popup_table').replaceWith( response['cart-table'] );

				} else {
					
					$('div.adf_add_btn').before("<p class='af-backbone-message'>" + response['message'] + "</p>");
				}
			},
			error: function (response) {
				jQuery(this).removeClass('loading');
				console.log( response );    
			}
		});
	});

});
