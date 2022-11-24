
<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}



?>


<?php do_action( 'woocommerce_email_header', $email_heading ); ?>


<?php 

        do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );
        do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );
        if ($email_data['additional_content'] !== '') {
                echo wp_kses_post( wpautop( wptexturize( $email_data['additional_content'] ) ) );

        }

?>


<?php do_action( 'woocommerce_email_footer' ); ?>