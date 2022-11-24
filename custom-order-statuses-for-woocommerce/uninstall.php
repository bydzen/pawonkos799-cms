<?php 

if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

foreach (wp_load_alloptions() as $option => $key) {
    if(strpos($option, 'woocos_check_') !== false) {
        delete_option($option);
    }
    if(strpos($option, 'woocommerce_woocos-') !== false){
        delete_option($option);
    }
    if(strpos($option, 'woocos_custom_order_statuses') !== false){
        delete_option($option);
    }
}