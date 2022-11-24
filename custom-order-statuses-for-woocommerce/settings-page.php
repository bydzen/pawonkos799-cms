<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


 add_action('admin_menu', 'woocos_menu', 9999);
// Adds submenu
function woocos_menu()
{
    $page_title = esc_html__('Custom Order Statuses', 'custom-order-statuses-for-woocommerce');
   add_submenu_page( 'woocommerce', $page_title, $page_title, 'manage_options', 'woocos_settings', 'woocos_settings_page', 9999 );
}

// Renders plugin settings page
function woocos_settings_page() 
{
   ?>
   <div class="woocos-header">
        <div class="woocos-header-wrapper">
            <h1 class=""><?php esc_html_e('Custom Order Statuses', 'custom-order-statuses-for-woocommerce'); ?></h1>
        </div>
   </div>
   <div class="woocos-settings">
       <div class='woocos-created-statuses-wrapper'>
           <h3><?php esc_html_e('Created Statuses', 'custom-order-statuses-for-woocommerce'); ?></h3>
           <?php
              woocos_get_created_statuses();
           ?>
       </div>
       <div>
           <h3><?php esc_html_e('Create New Status', 'custom-order-statuses-for-woocommerce'); ?></h3>
           <form method="POST">
               <p>
                   <p><strong><?php esc_html_e('Order Status Title', 'custom-order-statuses-for-woocommerce'); ?></strong></p>
                   <p><input type="text" name='new_woocos_title' required id='new-woocos-title'></p>
               </p>
               <p>
                   <p><strong><?php esc_html_e('Order Status Slug', 'custom-order-statuses-for-woocommerce'); ?></strong></p>
                   <p>
                       <input type="text" name="new_woocos_slug" class="unedited" required id="new-woocos-slug">
                       <div id="new-woocos-slug-message" style="display:none">
                            <?php esc_html_e('Slug cannot exceed 17 characters', 'custom-order-statuses-for-woocommerce') ?>
                       </div>
                  </p>
                </p>
                <p>
                    <p><strong><?php esc_html_e('Add to "Bulk Actions"', 'custom-order-statuses-for-woocommerce'); ?></strong></p>
                    <p>
                        <input type="checkbox" name="new_woocos_bulk" id="new-woocos-bulk">
                        <label for="new_woocos_bulk"><?php esc_html_e('Add Order status to "Bulk Actions" in Orders page', 'custom-order-statuses-for-woocommerce'); ?>
                            
                        </label>
                    </p>
                </p>

               
               <input id="add-new-woocos" type='submit' class="button-secondary" value="<?php esc_attr_e('Add New Custom Order Status', 'custom-order-statuses-for-woocommerce'); ?>">
           </form>
           
       </div>
   </div>
   <?php

   if(isset($_POST)) {
       if(isset($_POST['new_woocos_title']) && isset($_POST['new_woocos_slug'])) {
        
          $title = sanitize_text_field($_POST['new_woocos_title']);
          $slug = woocos_slugify($_POST['new_woocos_slug']);
          if (isset($_POST['new_woocos_bulk'])) {
            $bulk = 1;
          } else {
            $bulk = 0;
          }
          if(strlen($slug) > 17) {
              $slug = str_split($slug, 17)[0];
          }
          $slug = sanitize_text_field($slug);

          woocos_add_new_custom_order_status($title, $slug, $bulk);
       }
   }
}


function woocos_add_new_custom_order_status_error_notice($slug)
{
    $class = 'notice notice-error is-dismissible';
    printf( '<div class="%2s"><p>' . esc_html__( 'Slug "%1s" already exists. Please change the slug to create new Status.', 'custom-order-statuses-for-woocommerce' ) . '</p></div>', esc_attr( $class ), $slug);
}

//Adds new status to database

function woocos_add_new_custom_order_status($title, $slug, $bulk) 
{
    
        woocos_create_email_template($slug);
       $new_custom_order_status = array(
           'title'             => $title,
           'slug'              => $slug,
           'bulk'              => $bulk,
           'expanded'          => null,
       );
        // define default woocommerce statuses
        $woocos_default_woocommerce_statuses = ['wc-pending', 'wc-processing', 'wc-on-hold', 'wc-completed', 'wc-cancelled', 'wc-refunded'];
        if(in_array('wc-' . $slug, $woocos_default_woocommerce_statuses)){
            woocos_add_new_custom_order_status_error_notice($slug);
            return;
        }
       if (!get_option('woocos_custom_order_statuses')) {
           $custom_order_statuses= [];
           $custom_order_statuses[$slug] = $new_custom_order_status;
           $custom_order_statuses = json_encode($custom_order_statuses);
           
           update_option('woocos_custom_order_statuses', $custom_order_statuses);
           

       } else {

           $custom_order_statuses = json_decode(get_option('woocos_custom_order_statuses'), true);
           if (!$custom_order_statuses) {
                return;
           }
           foreach($custom_order_statuses as $status){
               if($status['slug'] === $slug){
                    woocos_add_new_custom_order_status_error_notice($slug);
                   return;
               }
           }
           $custom_order_statuses[$slug] = $new_custom_order_status;
           $custom_order_statuses = json_encode($custom_order_statuses);
           update_option('woocos_custom_order_statuses', $custom_order_statuses);
       }
       
       header("Refresh:0");
}

// Renders available statuses on page load
function woocos_get_created_statuses()
{
   $custom_order_statuses = json_decode(get_option('woocos_custom_order_statuses'));
   if(!$custom_order_statuses) {
       ?>
           <div>
               <?php esc_html_e('No statuses to display', 'custom-order-statuses-for-woocommerce'); ?>
           </div>
       <?php
       return;
   }
   ?>   
    <div class="woocos-removed-message">
        <p>
            
        </p>
    </div>
   <?php
   $index = 0;
   
   foreach ($custom_order_statuses as $status) {
       ?>
           <div class='woocos-status-item' id="woocos-status-item-<?php echo esc_attr($index); ?>" data-index="<?php echo esc_attr($status->slug); ?>">
               <div class='woocos-status'>
                   <h4 style="margin: 0"><?php echo esc_html($status->title); ?></h4>
                   <div class="woocos-status-actions">
                    <button class="woocos-expand-status button"><?php if($status->expanded) { esc_html_e('Minimize', 'custom-order-statuses-for-woocommerce'); } else { esc_html_e('Expand', 'custom-order-statuses-for-woocommerce'); } ?></button>
                    <button class="remove-woocos-item button-primary"><?php esc_html_e('Remove', 'custom-order-statuses-for-woocommerce'); ?></button>
                   </div>
               </div>
               <div class='woocos-status-options <?php if($status->expanded) { ?> expanded <?php } ?>'>
                   <form method="POST" action="update_woocos_item" data-index="<?php echo esc_attr($status->slug); ?>">
                        <p></p>
                        <p>
                            <p><label for="item_<?php echo esc_attr($index); ?>_title"><strong><?php esc_html_e('Order Status Title', 'custom-order-statuses-for-woocommerce'); ?></strong></label></p>
                            <input type="text" class="woocos-title-input" required name="item_<?php echo esc_attr($index) ?>_title" value="<?php echo esc_attr($status->title); ?>">
                        </p>
                        <p>
                        <p><strong><?php esc_html_e('Order Status Slug', 'custom-order-statuses-for-woocommerce'); ?></strong></p>
                            <p>
                                <?php echo esc_html($status->slug); ?>
                            </p>
                        </p>
                        <p>
                            <p><strong><?php esc_html_e('Add to Bulk Actions', 'custom-order-statuses-for-woocommerce'); ?></strong></p>
                            <p>
                                <?php
                                    if (!isset($status->bulk) || $status->bulk == 0) {
                                        $bulk = false;
                                    } else {
                                        $bulk = true;
                                    }
                                ?>
                                <input type="checkbox" name="new_woocos_bulk" id="woocos-<?php echo esc_attr($index); ?>-bulk" class="woocos-bulk-input" <?php if ($bulk) { ?>checked<?php } ?>>
                                <label for="item_<?php echo esc_attr($index) ?>_bulk"><?php esc_html_e('Add Order status to "Bulk Actions" in Orders page', 'custom-order-statuses-for-woocommerce'); ?>
                                    
                                </label>
                            </p>
                        </p>
                        <p>
                            <?php 
                                printf(
                                    esc_html__('You can edit email template %1s here%2s.', 'custom-order-statuses-for-woocommerce'),
                                    '<a target="_blank" href="' . get_home_url() . '/wp-admin/admin.php?page=wc-settings&tab=email&section=' . esc_attr($status->slug) .'">',
                                    '</a>'
                                );
                            ?>
                        </p>
                       
                        <p>
                            <input type="submit" value="<?php esc_attr_e('Update', 'custom-order-statuses-for-woocommerce'); ?>" class="woocos-update-item button-primary disabled">
                            <div class="woocos-update-message">
                                <p>

                                </p>
                            </div>
                        </p>
                   </form>
               </div>
           </div>
       <?php
       $index++;
   }
}