<?php
/**
 * Main class start.
 *
 * @package : eobc
 */

do_action( 'woocommerce_email_header', $email_heading, $email );
?>
<p>
	<?php

	$content = wpautop( wptexturize( $email_content ) );

		echo wp_kses_post( $content );
	?>
</p>
<?php

do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

/*
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

/*
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

if ( $additional_content ) {
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
}

do_action( 'woocommerce_email_footer', $email );
