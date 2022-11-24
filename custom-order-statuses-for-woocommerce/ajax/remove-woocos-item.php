<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


function remove_woocos_item_ajaxPost() {
    if(!get_option('woocos_custom_order_statuses')){
        return;
    }
    if(!isset($_POST)){
        return;
    }
    
    if(!isset($_POST['index'])){
        return;
    }
    $options = json_decode(get_option('woocos_custom_order_statuses'), true);
    $index = sanitize_text_field($_POST['index']);
    $slug = $options[$index]['slug'];
    $orders = wc_get_orders(array(
        'status' => 'wc-' . $slug,
    ));
    
    if(!empty($orders)){
        $orders_list = '';
        foreach($orders as $order) {
            
            $orders_list = $orders_list . '<a href="'. get_edit_post_link($order->id) . '" target="_blank">#';
            $orders_list = $orders_list . $order->id;
            $orders_list = $orders_list . '</a> ';
        }
        printf(
            esc_html__('Impossible to remove"%1s" status, orders %2s are using this status. Please change them before removing.', 'custom-order-statuses-for-woocommerce'),
            esc_html($_POST['title']),
            wp_kses_post($orders_list)
        );
        echo 'Break';
        return;
    };  
    delete_option('woocommerce_woocos-' . $slug . '_settings');
    if (sizeOf($options) === 1) {
        delete_option('woocos_custom_order_statuses');
    } else {
        unset($options[$index]);
        $options = json_encode($options);
        update_option('woocos_custom_order_statuses', $options);
    }
    woocos_remove_email_template($slug);
    printf(esc_html__('"%1s" status has been removed', 'custom-order-statuses-for-woocommerce'), esc_html($_POST['title']));
    return;
}

add_action( 'wp_ajax_remove_woocos_item_ajaxPost', 'remove_woocos_item_ajaxPost' );

?>