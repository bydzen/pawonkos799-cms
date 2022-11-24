<?php 

// Add plugin action links

add_filter('plugin_action_links_' . WOOCOS_PLUGIN_BASE, 'woocos_plugin_action_links');

function woocos_plugin_action_links( $links ) {
    
    $settings_link = sprintf( '<a href="%1$s">%2$s</a>', admin_url( 'admin.php?page=woocos_settings'), esc_html__( 'Settings', 'custom-order-statuses-for-woocommerce' ) );
    array_unshift($links, $settings_link);

    // $links['donate'] = sprintf( '<a href="https://www.paypal.com/donate/?hosted_button_id=SJBDN6KJHV2RY" target="_blank"><strong>%1$s</strong></a>', esc_html__( 'Donate', 'custom-order-statuses-for-woocommerce' ) );

    return $links;
}

add_filter('plugin_row_meta', 'woocos_plugin_row_meta', 10, 2);
function woocos_plugin_row_meta($plugin_meta, $plugin_file) {
    if (WOOCOS_PLUGIN_BASE === $plugin_file) {
        $row_meta = [
            'donate' => '<a href="https://www.paypal.com/donate/?hosted_button_id=SJBDN6KJHV2RY" target="_blank"><strong>' . esc_html__( 'Donate', 'custom-order-statuses-for-woocommerce' ) . '</strong></a>',
        ];
        
        $plugin_meta = array_merge($plugin_meta, $row_meta);
    }
    return $plugin_meta;
}

//Creates slug from string


function woocos_slugify($text, string $divider = '-')
{
    // replace non letter or digits by divider
    $text = preg_replace('~[^\pL\d]+~u', $divider, $text);

    // transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);

    // trim
    $text = trim($text, $divider);

    // remove duplicate divider
    $text = preg_replace('~-+~', $divider, $text);

    // lowercase
    $text = strtolower($text);

    if (empty($text)) {
      return 'n-a';
    }

    return $text;
}

function woocos_create_email_template($slug)
{
    $default_email_template_contents = file_get_contents( dirname(__FILE__) . '/templates/emails/default/woocos-email-template.php' );
    
    $new_email_template = dirname(__FILE__) . '/templates/emails/' . $slug . '.php';
    if (file_exists($new_email_template)) {
        return;
    } else {
        $new_email_template_file = fopen($new_email_template, "w");
        fwrite($new_email_template_file, $default_email_template_contents);
        fclose($new_email_template_file);
    }
}

function woocos_remove_email_template($slug)
{
    $email_template = dirname(__FILE__) . '/templates/emails/' . $slug . '.php';
    if(file_exists($email_template)) {
        unlink($email_template);
    }
}


function woocos_setup_email_templates()
{
    $woocos_options = json_decode(get_option('woocos_custom_order_statuses'), true);
    if (!empty($woocos_options) ) {
        foreach ( $woocos_options as $key => $option ) {
            $slug = $option['slug'];
            woocos_create_email_template($slug);
        }
    }
}

function woocos_remove_woocos_prefix()
{
    
    $woocos_options =  json_decode(get_option('woocos_custom_order_statuses'), true);

    if (!empty($woocos_options)) {
        foreach(wp_load_alloptions() as $option => $key) {
            foreach ( $woocos_options as $woocos_key => $woocos_option ) {
                if (strpos($option, 'woocommerce_' . $woocos_option['slug'] . '_settings') !== false) {
                    $option_data = get_option($option, array());
                    $option_name = str_replace('woocommerce_', 'woocommerce_woocos-', $option);
                   
                    update_option($option_name, $option_data);
                    delete_option($option);
                }
            }
        }
        foreach ( $woocos_options as $key => $option ) {
            $slug = 'woocos-' . $option['slug'];
            $args = array(
                'status'    => $slug,
                'limit'     => -1,
            );
            $orders = wc_get_orders($args);
            if ( !empty( $orders ) ) {
                foreach ( $orders as $order ) {
                    if ( $order->get_status() == $slug) {
                        $temp_slug = 'wc-' . $option['slug'];
                        $args = array(
                            'ID'            => $order->get_id(),
                            'post_status'   => $temp_slug
                        );
                        wp_update_post($args);
                    }
                }
            }
        }
    }
}

if ( in_array('print-invoices-packing-slip-labels-for-woocommerce/print-invoices-packing-slip-labels-for-woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))) ) {
    add_filter('wf_pklist_alter_invoice_attachment_mail_type','wf_pklist_alter_invoice_attachments',10,4);
}
function wf_pklist_alter_invoice_attachments($attach_to_mail_for, $order_id, $email_class_id, $order)
{
    $custom_order_statuses = json_decode(get_option('woocos_custom_order_statuses'), true);
    if (!empty($custom_order_statuses)) {
        foreach ( $custom_order_statuses as $order_status ) {
            $attach_to_mail_for[] = 'woocos-' . $order_status['slug'];
        }
    }
    return $attach_to_mail_for;
}

?>