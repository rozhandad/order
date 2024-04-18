<?php
/**
 * Cart table in my account page.
 *
 * @package cart table.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<table width="100%" class="popup_table">
	<tbody>
		<tr>
		<th></th>
		<th></th>
		<th> <?php echo esc_html__( 'Name', 'addify_eobc' ); ?></th>
		<th> <?php echo esc_html__( 'Price', 'addify_eobc' ); ?></th>
		<th> <?php echo esc_html__( 'Quantity', 'addify_eobc' ); ?></th>
		<th> <?php echo esc_html__( 'Subtotal', 'addify_eobc' ); ?></th>
	</tr>
	<?php

	foreach ( $order->get_items() as $item ) {

		$product = $item->get_product();

		$product_id = $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id();

		$product_match = false;

		if ( 'all_product_enable' === get_option( 'adfy_edit_order_all_product_field' ) ) {
			$product_match = true;
		}

		if ( in_array( $product_id, (array) get_option( 'adfy_edit_order_order_product_change_field' ) ) ) {

			$product_match = true;
		}

		foreach ( (array) get_option( 'adfy_edit_product_category_change_field' ) as $cate ) {

			if ( ! empty( $cate ) && has_term( $cate, 'product_cat', $product_id ) ) {

				$product_match = true;
			}
		}

		if ( $product_match ) {

			$product_name = $item->get_name();
			$quantity     = $item->get_quantity();
			$subtotal     = $item->get_subtotal();
			$product      = $item->get_product();
			$active_price = $product->get_price();
			$image        = $product->get_image();
			$item_id      = $item->get_id();

			?>
			<tr>
				<td><button  class="remove-order-item" data-item_id="<?php echo esc_attr( $item_id ); ?>">x</button></td>
				<td ><p class="btn2"><?php echo wp_kses_post( $image ); ?></p></td>
				<td><?php echo esc_attr( $product_name ); ?></td>
				<td><?php echo esc_attr( $active_price ); ?></td>
				<td><input type="number" class="number" name="edit_order_quantity[<?php echo intval( $item->get_id() ); ?>]" value="<?php echo esc_attr( $quantity ); ?>"></td>
				<td><?php echo esc_attr( $subtotal ); ?></td>
			</tr>
			<?php
		}
	}
	?>

</tbody>
</table>
