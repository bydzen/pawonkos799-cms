<?php 


/**
 * Plugin Name: Custom Order Statuses for WooCommerce
 * Author: Nuggethon
 * Author URI: https://nuggethon.com
 * Description: Allows to create Custom Order Statuses for WooCommerce and send Custom Emails for them.
 * Version: 1.5.2
 * Text Domain: custom-order-statuses-for-woocommerce
 * Domain Path: /languages
 * Requires at least: 5.3
 * Tested up to: 6.0
 * Requires PHP: 5.6.40
 * Requires WC at least: 3.8.1
 * WC tested up to: 6.5
 * 
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */


 


 
add_action( 'init', 'woocos_load_textdomain' );
 
function woocos_load_textdomain() {
    load_plugin_textdomain( 'custom-order-statuses-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
}
define('WOOCOS_PLUGIN_BASE', plugin_basename(__FILE__));
include('assets.php');
include('functions.php');

require('email-manager.php');
require('order-status-generator.php');

require('ajax/bulk-change-status.php');
require('ajax/deactivation-form.php');
require('ajax/update-woocos-item.php');
require('ajax/expand-woocos-item.php');
require('ajax/remove-woocos-item.php');


require_once('settings-page.php');

register_activation_hook(__FILE__ , 'woocos_activation_functions' );

function woocos_activation_functions()
{
    woocos_remove_woocos_prefix();
    woocos_setup_email_templates();
}