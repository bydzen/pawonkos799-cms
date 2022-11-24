<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


function expand_woocos_item_ajaxPost() {
    if(!get_option('woocos_custom_order_statuses')){
        return;
    }
    $options = json_decode(get_option('woocos_custom_order_statuses'));
    
    
    if(!isset($_POST['index'])){
        return;
    }
    $index = sanitize_text_field($_POST['index']);
    $options->$index->expanded = sanitize_text_field($_POST['expanded']);
    $options = json_encode($options);
    update_option('woocos_custom_order_statuses', $options);
    if (sanitize_text_field($_POST['expanded']) == 1) {
        esc_html_e('Minimize', 'custom-order-statuses-for-woocommerce');
    } else {
        esc_html_e('Expand', 'custom-order-statuses-for-woocommerce');
    }
}

add_action( 'wp_ajax_expand_woocos_item_ajaxPost', 'expand_woocos_item_ajaxPost' );

?>