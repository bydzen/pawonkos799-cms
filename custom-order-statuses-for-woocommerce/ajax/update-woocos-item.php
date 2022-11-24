<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function update_woocos_item_ajaxPost() {
    if(!get_option('woocos_custom_order_statuses')){
        return;
    }
    $options = json_decode(get_option('woocos_custom_order_statuses'));
    if(!isset($_POST['index'])){
        return;
    }
    $index = sanitize_text_field($_POST['index']);
    if($options->$index->slug !== $_POST['index']){
        echo 'An error occured while updating status.';
        return;
    }
    $options->$index->title = sanitize_text_field($_POST['new_title']);
    $options->$index->bulk = sanitize_text_field($_POST['new_bulk']);
    
    $options = json_encode($options);
    update_option('woocos_custom_order_statuses', $options);
    printf(
        esc_html__('"%1s" status updated successfully.', 'custom-order-statuses-for-woocommerce'),
        esc_html($_POST['new_title'])
    );
}

add_action( 'wp_ajax_update_woocos_item_ajaxPost', 'update_woocos_item_ajaxPost' );

?>