/**
 * Main eobc function start.

 * @package WOOCP
 */

jQuery(document).ready(function($){


	$('#all_pcat').change(function () {
		if ($('#all_pcat').is(":not(:checked)")) {
			$(".adfy_edit_product_category_change_field").show(); 
			$(".adfy_edit_product_category_change_field").closest("tr").show();
			$(".adfy_edit_order_order_product_change_field").show(); 
			$(".adfy_edit_order_order_product_change_field").closest("tr").show();
		} else {
			$(".adfy_edit_product_category_change_field").hide(); 
			$(".adfy_edit_product_category_change_field").closest("tr").hide();
			$(".adfy_edit_order_order_product_change_field").hide(); 
			$(".adfy_edit_order_order_product_change_field").closest("tr").hide();
		}
	});
	if ($('#all_pcat').is(":not(:checked)")) {
		 $(".adfy_edit_product_category_change_field").show(); 
		 $(".adfy_edit_product_category_change_field").closest("tr").show();
		 $(".adfy_edit_order_order_product_change_field").show(); 
		 $(".adfy_edit_order_order_product_change_field").closest("tr").show();
	} else {
		$(".adfy_edit_product_category_change_field").hide(); 
		$(".adfy_edit_product_category_change_field").closest("tr").hide();
		$(".adfy_edit_order_order_product_change_field").hide(); 
		$(".adfy_edit_order_order_product_change_field").closest("tr").hide();
	}
});
