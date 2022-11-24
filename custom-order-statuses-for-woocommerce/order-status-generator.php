<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


// Registers custom order statuses
function woocos_register_custom_order_statuses() 
{
    $custom_order_statuses = json_decode(get_option('woocos_custom_order_statuses'));
    if(!$custom_order_statuses){
        return;
    }
    foreach ($custom_order_statuses as $order_status) {
        $slug = 'wc-' . $order_status->slug;
        register_post_status($slug, array(
            'label'                     => $order_status->title,
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop(  $order_status->title . ' (%s)', $order_status->title . ' (%s)' )
        ));
    }
}

add_action( 'init', 'woocos_register_custom_order_statuses' );

// Add order statuses to Bulk Action logic
add_filter( 'bulk_actions-edit-shop_order', 'woocos_register_bulk_actions_for_custom_statuses' ); 

function woocos_register_bulk_actions_for_custom_statuses( $bulk_actions ) {

    $custom_order_statuses = json_decode(get_option('woocos_custom_order_statuses'), true);
    foreach( $custom_order_statuses as $order_status) {
        if (!isset($order_status['bulk'])) {
            continue;
        }
        error_log($order_status['title']);
        if ($order_status['bulk'] == '1') {
            error_log($order_status['title']);
            $bulk_actions['mark_' . $order_status['slug']] = __('Change status to ', 'custom-order-statuses-for-woocommerce') . $order_status['title'];
        }
    }
    return $bulk_actions;

}


// Adds custom order statuses to order status select
function woocos_append_custom_order_statuses($order_statuses)
{
    $custom_order_statuses = json_decode(get_option('woocos_custom_order_statuses'));
    if(!$custom_order_statuses){
        return $order_statuses;
    }
    $new_order_statuses = array();
    
    foreach ( $order_statuses as $key => $status ) {
 
        $new_order_statuses[ $key ] = $status;
 
        if ( 'wc-failed' === $key ) {
            foreach ($custom_order_statuses as $custom_order_status) {
                $slug = 'wc-' . $custom_order_status->slug;
                $new_order_statuses[$slug] = _x($custom_order_status->title, 'Order status', 'woocommerce');
            }
            
        }
    }
    return $new_order_statuses;

}
add_filter( 'wc_order_statuses', 'woocos_append_custom_order_statuses', 10, 3 ); 


// Adds action, when order status is changed to custom order status
function woocos_add_custom_order_status_actions($order_id, $old_status, $new_status){
    $custom_order_statuses = json_decode(get_option('woocos_custom_order_statuses'));
    if(!$custom_order_statuses){
        return;
    }
    $order_status = null;
    foreach ($custom_order_statuses as $custom_order_status) {

        $slug = '' . $custom_order_status->slug;
        if($new_status === $slug){
            $order_status = $custom_order_status;
            delete_option('woocos_check_' . $order_id . '_' . $custom_order_status->slug);

        }
    }
    if( $order_status == null ){
        return;
    }
    do_action('woocommerce_order_status_changed_to_woocos', $order_id, $order_status, $new_status, false);

 }
 
 add_action('woocommerce_order_status_changed', 'woocos_add_custom_order_status_actions', 10, 3);