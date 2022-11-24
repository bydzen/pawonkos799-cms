<?php

if ( ! defined ('ABSPATH') ) {
    exit;
}

function woocos_setup_deactivation_form_ajaxPost(){
    if(!isset($_POST)){
        return;
    }
    // Check whether deactivation form is needed

    $options = json_decode(get_option('woocos_custom_order_statuses'), true);
    $woocos_default_woocommerce_statuses = ['wc-pending', 'wc-processing', 'wc-on-hold', 'wc-completed', 'wc-cancelled', 'wc-refunded'];
    $found_orders = [];
    if ( empty( $options ) ) {
        return;
    }
    foreach ( $options as $key => $option ) {
        $slug = 'wc-' . $option['slug'];
        if(in_array($slug, $woocos_default_woocommerce_statuses)) {
            continue;
        }
        $orders = wc_get_orders(array(
            'status'    => $slug
        ));
        if ( !empty( $orders ) ) {
            foreach ( $orders as $order ){
                array_push( $found_orders, $order->id );
            }
        }
    }
    if ( ! empty( $found_orders ) ) {

    ?>

        <div class="woocos-deactivation-popup hidden">
            <div class="overlay"></div>
            <div class="form">
                <div class="form-header">
                    <div class="col-10">
                        <h3>Safe deactivation</h3>
                    </div>
                    <div class="col-2">
                        <span class="close-woocos-popup">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><!--! Font Awesome Pro 6.1.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M310.6 361.4c12.5 12.5 12.5 32.75 0 45.25C304.4 412.9 296.2 416 288 416s-16.38-3.125-22.62-9.375L160 301.3L54.63 406.6C48.38 412.9 40.19 416 32 416S15.63 412.9 9.375 406.6c-12.5-12.5-12.5-32.75 0-45.25l105.4-105.4L9.375 150.6c-12.5-12.5-12.5-32.75 0-45.25s32.75-12.5 45.25 0L160 210.8l105.4-105.4c12.5-12.5 32.75-12.5 45.25 0s12.5 32.75 0 45.25l-105.4 105.4L310.6 361.4z"/></svg>
                        </span>
                    </div>
                </div>
                <div class="form-body">
                    <p>Orders 
                        <?php
                            $more_orders = false;
                            foreach ( $found_orders as $key => $found_order ) {
                                if($key === 2) {
                                    echo '<a href="#show-more-orders" class="woocos-show-more-orders" title="See all">...</a>';
                                    echo '<span class="more-orders hidden">';
                                    $more_orders = true;
                                }
                                echo '<a href="' . esc_attr(get_edit_post_link($found_order)) . '" target="_blank">';
                                echo '#' . esc_html($found_order);
                                if ($key + 1 !== sizeOf($found_orders)) {
                                    echo '</a>, ';
                                } else {
                                    echo '</a> ';
                                }
                            }
                            if ( $more_orders ) {
                                echo '</span>';
                            }
                        ?>
                        have a Custom Order Status set as their current status. If you deactivate the plugin without changing the status to one of Woocommerce defaults, the order will become missing. You can either change statuses separatelly for each order, or bulk change them all.
                    </p>
                    <p>
                        <strong>P.S.</strong> If you reactivate the plugin without deleting it, the orders will reappear, so you can safely skip this step if you are deactivating it temporarily.
                    </p>
                    <div class="bulk-change-section">
                        <h4>Bulk change</h4>
                        <p>Change status for all orders to:</p>
                        <?php
                            $order_statuses = wc_get_order_statuses();
                            $woocos_keys = [];
                            foreach ( $order_statuses as $key => $order_status ) {
                                if (!in_array($key, $woocos_default_woocommerce_statuses)){
                                    array_push($woocos_keys, $key);
                                }
                            }
                            foreach($woocos_keys as $key){
                                unset($order_statuses[$key]);
                            }
                        ?>
                        
                        <select name="default-order-statuses" id="bulk-change" placeholder="select">
                            <option value="" disabled selected hidden>Select Status</option>
                            <?php 
                                foreach($order_statuses as $key => $status) {
                            ?>
                            
                                <option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($status); ?></option>

                            <?php 
                                }
                            ?>  
                        </select>
                        <p>
                            <button class="button-secondary disabled" id="bulk-change-button">
                                Bulk Change
                            </button>
                        </p>
                        <p id="bulk-change-message">

                        </p>
                    </div>
                </div>
                <div class="form-footer">
                    <a href="<?php echo esc_attr($_POST['deactivation_link']); ?>">
                        <button class="button-secondary" id="skip-safe-deactivation">
                            Skip & Deactivate
                        </button>
                    </a>
                    <a href="<?php echo esc_attr($_POST['deactivation_link']); ?>">
                        <button class="button-primary disabled" id="initiate-safe-deactivation">
                            Deactivate Safely
                        </button>
                    </a>
                    
                </div>
            </div>
        </div>'

    <?php
    
    }

}

add_action( 'wp_ajax_woocos_setup_deactivation_form_ajaxPost', 'woocos_setup_deactivation_form_ajaxPost' );