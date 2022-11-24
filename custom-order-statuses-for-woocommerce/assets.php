<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( !function_exists( 'get_current_screen' ) ) { 
    require_once ABSPATH . '/wp-admin/includes/screen.php'; 
} 

$plugin_location = plugin_dir_url( __FILE__ ) ;

$screen = get_current_screen();
if(isset($_GET['page']) && $_GET['page'] === 'woocos_settings'){
    add_action( 'admin_enqueue_scripts', 'woocos_load_custom_settings_assets' );
}
if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/wp-admin/plugins.php') !== false) {
    add_action('admin_enqueue_scripts', 'woocos_load_deactivation_assets');
}


function woocos_load_custom_settings_assets() {
    global $plugin_location;
    // Settings styles
    wp_enqueue_style('woocos-settings-style',  $plugin_location . 'assets/css/settings-page.css', array(), '394f15a65b28cb');

    // Settings scripts
    wp_enqueue_script('woocos-settings-script', $plugin_location . 'assets/js/settings-page.js', array(), '394f15a65b28ca');

    //Ajax requests
    wp_localize_script( 'woocos-settings-script', 'woocos_ajax_object', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ));
}

function woocos_load_deactivation_assets() {
    global $plugin_location;

    // Deactivation styles
    wp_enqueue_style('woocos-deactivation-style',  $plugin_location . 'assets/css/deactivation.css', array(), '394f15a65b21cb');
    // Deactivation scripts
    wp_enqueue_script('woocos-deactivation-script', $plugin_location . 'assets/js/deactivation.js', array(), '394f15a65b21cf');

    // Ajax requests
    wp_localize_script( 'woocos-deactivation-script', 'woocos_ajax_object', array( 'ajaxurl' => admin_url( 'admin-ajax.php') ) );


}