<?php


// Custom order status email manager

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WOOCOS_Email_Manager {

     public function __construct()
     {
         // template path
	    define( 'CUSTOM_TEMPLATE_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/templates/' );
        add_action( 'woocommerce_order_status_changed_to_woocos', array( $this, 'woocos_trigger_email_action' ), 10, 4 );
        add_filter( 'woocommerce_email_classes', array( &$this, 'woocos_init_emails' ) );

		
        // Email Actions - Triggers
	    $email_actions = array(
		    'woocos_item_email',
	    );

	    foreach ( $email_actions as $action ) {
	        add_action( $action, array( 'WC_Emails', 'send_transactional_email' ), 10, 10 );
	    }
		
	    add_filter( 'woocommerce_template_directory', array( $this, 'woocos_template_directory' ), 10, 2 );
     }

	 // Adds emails to woocommerce email list
     
	public function woocos_init_emails( $emails ) {
        $custom_order_statuses = json_decode(get_option('woocos_custom_order_statuses'));
		if(!$custom_order_statuses){
			return $emails;
		}
        include_once( 'emails/woocos-email.php' );
        foreach ($custom_order_statuses as $order_status) {
            if ( ! isset( $emails[ $order_status->slug ] )) {
				$emails[  $order_status->slug ] = new WOOCOS_Email($order_status);
            }
        }
	    return $emails;
	}
	
	// When custom order status is selected in edit order page, triggers email

	public function woocos_trigger_email_action( $order_id, $custom_order_status, $new_status, $posted ) {
		if ($custom_order_status->slug !== $new_status) {
			return;
		}
	    if ( isset( $order_id ) && 0 != $order_id ) {
			
	        $wc_emails = WC_Emails::instance();
			global $wp_filter;
			foreach ($wp_filter['woocommerce_email_header']->callbacks as $key => $array) {
				if (sizeOf($array) > 1) {
					$this->sanitize_email_template($wc_emails);
					break;
				}
			}
    		do_action( 'woocos_email_notification', $order_id, $custom_order_status );
	    
	    }
	}

	
	public function sanitize_email_template($class) 
	{
		remove_action('woocommerce_email_header', array($class, 'email_header'));
		remove_action('woocommerce_email_footer', array($class, 'email_footer'));
		remove_action( 'woocommerce_email_order_details', array( $class, 'order_downloads' ), 10, 4 );
		remove_action( 'woocommerce_email_order_details', array( $class, 'order_details' ), 10, 4 );
		remove_action( 'woocommerce_email_order_meta', array( $class, 'order_meta' ), 10, 3 );
		remove_action( 'woocommerce_email_customer_details', array( $class, 'customer_details' ), 10, 3 );
		remove_action( 'woocommerce_email_customer_details', array( $class, 'email_addresses' ), 20, 3 );
	}

	// Returns directory for email template
	public function woocos_template_directory( $directory, $template ) {
	   // ensure the directory name is correct
	    if ( false !== strpos( $template, 'woocos' ) ) {
	      return 'woocos-email';
	    }
	
	    return $directory;
	}
    
}


new WOOCOS_Email_Manager();