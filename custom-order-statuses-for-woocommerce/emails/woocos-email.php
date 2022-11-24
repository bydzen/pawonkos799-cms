<?php 
/**
 * Custom Email
 *
 * An email sent to the admin when an order status is changed to one of the created custom order status.
 * 
 * @class       Custom_Email
 * @extends     WC_Email
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WOOCOS_Email extends WC_Email {
    
    public function __construct($order_status) {

            // Add email ID, title, description, heading, subject
            $id =  $order_status->slug;
            $this->id                   =  'woocos-' . $id;
            $this->title                = __( $order_status->title, 'custom-order-statuses-for-woocommerce' );
            $this->description          = sprintf(__( 'This email is received when an order status is changed to "%1s".', 'custom-order-statuses-for-woocommerce' ), esc_html($order_status->title));
            
            $this->heading              = sprintf(__( 'Order status for order {order_number} has been changed.',  'custom-order-statuses-for-woocommerce' ));
            $this->subject              = __( '[{site_title}] Order {order_number} - {order_date}', 'custom-order-statuses-for-woocommerce' );
            
            
            // email template path
            $this->template_html = 'emails/' . $id . '.php';
            // Triggers for this email
            add_action( 'woocos_email_notification', array( $this, 'woocos_trigger' ), 10, 2 );
            
            // Call parent constructor
            parent::__construct();
            
            // Other settings
            $this->template_base = CUSTOM_TEMPLATE_PATH;

            // Check if email should be sent to customer or to custom recipient
            if($this->get_recipient() !== '') {
                
                $this->recipient = $this->get_recipient();
            } else {
                $this->customer_email = true;
            }
    
    }
    // Collect data and send email
    function woocos_trigger( $order_id , $custom_order_status) {
        // Check if this particular email has already been sent, if yes then abort
        if(get_option('woocos_check_' . $order_id . '_' . $custom_order_status->slug)) {
            return;
        } else {
            add_option('woocos_check_' . $order_id . '_' . $custom_order_status->slug, true);
        }
        
        $email_data = get_option('woocommerce_woocos-' . $custom_order_status->slug . '_settings');
        if( $email_data['enabled'] === 'yes'){
            $send_email = true;
        }
        // validations
        if ( $order_id && $send_email ) {
            // create an object with item details like name, quantity etc.
            $this->object = $this->woocos_create_object( $order_id, $email_data);
            
            // replace the merge tags with valid data
            $key = array_search( '{product_title}', $this->find );
            if ( false !== $key ) {
                unset( $this->find[ $key ] );
                unset( $this->replace[ $key ] );
            }
            
        
            if ( $this->object->order_id ) {
                
                $this->find[]    = '{order_date}';
                $this->replace[] = date_i18n( wc_date_format(), strtotime( $this->object->order_date ) );
        
                $this->find[]    = '{order_number}';
                $this->replace[] = $this->object->order_id;
            } else {
                    
                $this->find[]    = '{order_date}';
                $this->replace[] = __( 'N/A', 'woocos-email' );
        
                $this->find[]    = '{order_number}';
                $this->replace[] = __( 'N/A', 'woocos-email' );
            }
            $recipient = $email_data['recipient'] ? $email_data['recipient'] : $this->object->billing_email;
            $subject = $email_data['subject'] ? $email_data['subject'] : $this->woocos_get_subject();

            $subject = str_replace('{site_title}',  get_bloginfo('name'), $subject);
            $subject = str_replace('{order_number}', $this->object->order_id, $subject);
            $subject = str_replace('{order_date}', date_i18n( wc_date_format(), strtotime( $this->object->order_date ) ), $subject);
            $subject = str_replace('{site_address}', wp_parse_url( home_url(), PHP_URL_HOST ), $subject);
            $subject = str_replace('{site_url}', wp_parse_url( home_url(), PHP_URL_HOST ), $subject);

            $this->send( $recipient, $subject, $this->woocos_get_content_html($email_data, $custom_order_status), $this->get_headers(), $this->woocos_get_attachments($custom_order_status, $order_id) );

        }
    }
    public function woocos_get_attachments($custom_order_status, $order_id)
    {
        $order = wc_get_order( $order_id );
        return apply_filters( 'woocommerce_email_attachments', array(), 'woocos-' . $custom_order_status->slug, $order, $this );
    }
    // Create an object with the data to be passed to the templates
    public static function woocos_create_object( $item_id , $order_status) {
    
        global $wpdb;
    
        $item_object = new stdClass();
        $item_object->order_status = $order_status;
        // order ID
        $query_order_id = "SELECT order_id FROM `". $wpdb->prefix."woocommerce_order_items`
                            WHERE order_item_id = %d";
        $get_order_id = $wpdb->get_results( $wpdb->prepare( $query_order_id, $item_id ) );
        
    
        $item_object->order_id = $item_id;
    
        $order = wc_get_order($item_id);
        // order date
        $post_data = get_post( $item_id );
        $item_object->order_date = $post_data->post_date;
    
        // product ID
        $items = $order->get_items();
        $index = 0;
        foreach ($items as $item) {
            if ($item->get_variation_id()) {
                $item_object->items[$index]['product_id'] = $item->get_variation_id();
            } else {
                $item_object->items[$index]['product_id'] = $item->get_product_id();
            }
            $item_object->items[$index]['qty'] = $item->get_quantity();
            $item_object->items[$index]['total'] = $item->get_total();
            $_product = wc_get_product($item_object->items[$index]['product_id']);
            $item_object->items[$index]['product_title'] = $_product->get_title();
            $index++;
        }

        // email adress
        $item_object->billing_email = ( version_compare( WOOCOMMERCE_VERSION, "3.0.0" ) < 0 ) ? $order->billing_email : $order->get_billing_email();
    
        // customer ID
        $item_object->customer_id = ( version_compare( WOOCOMMERCE_VERSION, "3.0.0" ) < 0 ) ? $order->user_id : $order->get_user_id();
    
        return $item_object;
    
    }
    
    // return the html content
    function woocos_get_content_html($email_data, $custom_order_status) {
        $this->template_html = 'emails/' . $custom_order_status->slug . '.php';
        $email_data = $this->object->order_status;
        $email_heading = $email_data['heading'];
            
        $email_heading = str_replace('{site_title}',  get_bloginfo('name'), $email_heading);
        $email_heading = str_replace('{order_number}', $this->object->order_id, $email_heading);
        $email_heading = str_replace('{order_date}', date_i18n( wc_date_format(), strtotime( $this->object->order_date ) ), $email_heading);
        $email_heading = str_replace('{site_address}', wp_parse_url( home_url(), PHP_URL_HOST ), $email_heading);
        $email_heading = str_replace('{site_url}', wp_parse_url( home_url(), PHP_URL_HOST ), $email_heading);

        $email_data['additional_content'] = str_replace('{site_title}',  get_bloginfo('name'), $email_data['additional_content']);
        $email_data['additional_content'] = str_replace('{order_number}', $this->object->order_id, $email_data['additional_content']);
        $email_data['additional_content'] = str_replace('{order_date}', date_i18n( wc_date_format(), strtotime( $this->object->order_date ) ), $email_data['additional_content']);
        $email_data['additional_content'] = str_replace('{site_address}', wp_parse_url( home_url(), PHP_URL_HOST ), $email_data['additional_content']);
        $email_data['additional_content'] = str_replace('{site_url}', wp_parse_url( home_url(), PHP_URL_HOST ), $email_data['additional_content']);
        
        ob_start();
        wc_get_template(
            $this->template_html,
            array(
                'email_data'            => $email_data,
                'order'                 => wc_get_order($this->object->order_id),
                'email_heading'         => $email_heading ? $email_heading : $this->get_heading(),
                'additional_content'    => $email_data['additional_content'],
                'sent_to_admin'         => false,
                'plain_text'            => false,
                'email'                 => $email_data['recipient']
            ),
            'woocommerce',
            $this->template_base
        );
        return ob_get_clean();
    }
    
    // return the subject
    function woocos_get_subject() 
    {
        $order = new WC_order( $this->object->order_id );
        return apply_filters( 'woocommerce_email_subject_' . $this->id, $this->format_string( $this->subject ), $this->object );
        
    }
    
    // return the email heading
    public function get_heading() {
        
        $order = new WC_order( $this->object->order_id );
        return apply_filters( 'woocommerce_email_heading_' . $this->id, $this->format_string( $this->heading ), $this->object );
        
    }
    
    // form fields that are displayed in WooCommerce->Settings->Emails
    function init_form_fields() {
        $placeholder_text  = sprintf( __( 'Available placeholders: %s', 'woocommerce' ), '<code>' . implode( '</code>, <code>', array_keys( $this->placeholders ) ) . '</code>' );
        $this->form_fields = array(
            'enabled' => array(
                'title' 		=> __( 'Enable/Disable', 'woocommerce' ),
                'type' 			=> 'checkbox',
                'label' 		=> __( 'Enable this email notification', 'woocommerce' ),
                'default' 		=> 'no'
            ),
            'recipient' => array(
                'title'         => __( 'Recipient(s)', 'woocommerce' ),
                'type'          => 'text',
                'description'   => sprintf(__( 'Enter recipients (comma separated) for this email. Defaults to %s.', 'woocommerce'), 'Customer' ),
                'default'       => '',
                'placeholder'   => 'Customer',
                'class'         => 'woocos-email-recipient',
                'desc_tip'      => true,
            ),
            'subject' => array(
                'title' 		=> __( 'Subject', 'woocommerce' ),
                'type' 			=> 'text',
                'description' 	=> sprintf( __( 'Available placeholders: %1s, %2s, %3s, %4s, %5s', 'custom-order-statuses-for-woocommerce' ), '{site_title}', '{site_address}', '{site_url}' , '{order_date}', '{order_number}'),
                'placeholder' 	=> $this->get_default_subject(),
                'default' 		=> '',
                'class'         => 'woocos-email-subject',
                'desc_tip'      => true,
            ),
            'heading' => array(
                'title' 		=> __( 'Email heading', 'woocommerce' ),
                'type' 			=> 'text',
                'description' 	=> sprintf( __( 'Available placeholders: %1s, %2s, %3s, %4s, %5s', 'custom-order-statuses-for-woocommerce' ), '{site_title}', '{site_address}', '{site_url}' , '{order_date}', '{order_number}' ),
                'placeholder' 	=> $this->heading,
                'desc_tip'      => true,
                'default' 		=> '',
                'class'         => 'woocos-email-heading'
            ),
            'additional_content' => array(
				'title'       => __( 'Additional content', 'woocommerce' ),
				'description' => sprintf( __( 'Text to appear below the main email content. Available placeholders: %1s, %2s, %3s, %4s, %5s', 'custom-order-statuses-for-woocommerce' ), '{site_title}', '{site_address}', '{site_url}' , '{order_date}', '{order_number}'),
				'css'         => 'width:400px; height: 75px;',
				'placeholder' => '',
				'type'        => 'textarea',
				'desc_tip'    => true,
                'class'         => 'woocos-email-additional-content'
			),
            'email_type' => array(
                'title' 		=> __( 'Email type', 'woocommerce' ),
                'type' 			=> 'select',
                'description' 	=> __( 'Choose which format of email to send.', 'woocommerce' ),
                'default' 		=> 'html',
                'class'			=> 'email_type',
                'desc_tip'      => true,
                'options'		=> array(
                    'html' 			=> __( 'HTML', 'email' ),
                ),
                'class'         => 'woocos-email-type'
            ),
        );
    }
    
}


?>