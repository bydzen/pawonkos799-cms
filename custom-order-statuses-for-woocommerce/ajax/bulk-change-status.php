<?php

if ( ! defined ('ABSPATH') ) {
    exit;
}

function woocos_bulk_change_orders_ajaxPost(){
    if(!isset($_POST)){
        return;
    } 
    // define default woocommerce statuses
    $new_status = sanitize_text_field($_POST['status']);
    $woocos_default_woocommerce_statuses = ['wc-pending', 'wc-processing', 'wc-on-hold', 'wc-completed', 'wc-cancelled', 'wc-refunded'];

    if( ! in_array( $new_status, $woocos_default_woocommerce_statuses ) ) {
        echo 'Error. Selected status not found in WooCommerce default statuses.';
        return;
    }

    $options = json_decode(get_option('woocos_custom_order_statuses'), true);
    $found_orders = [];
    if ( empty( $options ) ) {
        return;
    }
    foreach ( $options as $key => $option ) {
        $slug = 'wc-' . $option['slug'];
        if(in_array($slug, $woocos_default_woocommerce_statuses)) {
            continue;
        }
        $orders = wc_get_orders(array(
            'status'    => $slug
        ));
        if ( !empty( $orders ) ) {
            foreach ( $orders as $order ){
                array_push( $found_orders, $order->id );
            }
        }
    }
    $new_status = str_replace('wc-', '', $new_status);
    foreach ( $found_orders as $order_id ) {
        $order = new WC_Order($order_id);
        $order->update_status($new_status);
    }
    echo 'Orders ';
    $more_orders = false;
    foreach($found_orders as $key => $order_id){
        if ( $key === 2 ){
            echo '<a href="#show-more-orders" class="woocos-show-more-orders" title="See all">...</a>';
            echo '<span class="more-orders hidden">';
            $more_orders = true;
        }
        echo '<a href="' . esc_attr(get_edit_post_link($order_id)) . '" target="_blank">';
        echo '#' . esc_html($order_id);
        if ($key + 1 !== sizeOf($order_id)) {
            echo '</a>, ';
        } else {
            echo '</a> ';
        }
    }
    if ( $more_orders ) {
        echo '</span>';
    }

    echo ' have been changed successfully';
}

add_action( 'wp_ajax_woocos_bulk_change_orders_ajaxPost', 'woocos_bulk_change_orders_ajaxPost' );