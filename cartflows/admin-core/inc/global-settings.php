<?php
/**
 * CartFlows Global Data.
 *
 * @package CartFlows
 */

namespace CartflowsAdmin\AdminCore\Inc;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class flowMeta.
 */
class GlobalSettings {


	/**
	 * Get flow meta options.
	 */
	public static function get_global_settings_fields() {
		global $wp_roles;

		$origin = get_site_url();

		// Get all user roles.
		$all_roles_tmp = $wp_roles->roles;
		$all_roles     = array();

		foreach ( $all_roles_tmp as $role_name => $dispaly_name ) {
			if ( 'administrator' !== $role_name ) {
				$all_roles[ $role_name ] = array(
					'role_name' => $dispaly_name['name'],
					'fields'    => array(
						'roles-structure' => array(
							'type'    => 'radio',
							'name'    => '_cartflows_roles[' . $role_name . ']',
							'options' => array(
								array(
									'value' => 'no_access',
									'label' => __( 'No Access', 'cartflows' ),
								),
								array(
									'value' => 'access_to_cartflows',
									'label' => __( 'Full Access', 'cartflows' ),
									'desc'  => __( 'A full access to all settings.', 'cartflows' ),
								),
								array(
									'value' => 'access_to_flows_and_step',
									'label' => __( 'Limited Access', 'cartflows' ),
									'desc'  => __( 'Can create/edit/delete/import flows and steps only.', 'cartflows' ),
								),
							),
						),
					),
				);
			}
		}

		$settings = array(
			'general'                => array(
				'title'  => __( 'General ', 'cartflows' ),
				'fields' => array(
					'page_builder'  => array(
						'type'    => 'select',
						'name'    => '_cartflows_common[default_page_builder]',
						'label'   => __( 'Show Ready Templates for', 'cartflows' ),
						/* translators: %1$s: link html start, %2$s: link html end*/
						'desc'    => sprintf( __( 'CartFlows offers flow templates that can be imported in one click. These templates are available in few different page builders. Please choose your preferred page builder from the list so you will only see templates that are made using that page builder. %1$sLearn More >>%2$s', 'cartflows' ), '<a href="https://cartflows.com/docs/import-cartflows-templates-for-flows-steps/" target="_blank">', '</a>' ),

						'options' => array(
							array(
								'value' => 'elementor',
								'label' => __( 'Elementor', 'cartflows' ),
							),
							array(
								'value' => 'beaver-builder',
								'label' => __( 'Beaver Builder', 'cartflows' ),
							),
							array(
								'value' => 'divi',
								'label' => __( 'Divi', 'cartflows' ),
							),
							array(
								'value' => 'gutenberg',
								'label' => __( 'Gutenberg', 'cartflows' ),
							),
							array(
								'value' => 'other',
								'label' => __( 'Other', 'cartflows' ),
							),
						),

					),
					'search_engine' => array(
						'type'     => 'checkbox',
						'name'     => '_cartflows_common[disallow_indexing]',
						'label'    => __( 'Disallow search engine from indexing flows.', 'cartflows' ),
						'backComp' => true,
					),
				),
			),
			'global-checkout'        => array(
				'title'  => __( 'Store Checkout', 'cartflows' ),
				'fields' => array(
					'override_global_checkout' => array(
						'type'     => 'checkbox',
						'backComp' => true,
						'name'     => '_cartflows_common[override_global_checkout]',
						'label'    => __( 'Override Store Checkout', 'cartflows' ),
						/* translators: %1$1s: link html start, %2$12: link html end*/
						'desc'     => sprintf( __( 'For more information about the Store Checkout settings please %1$sClick here%2$s.', 'cartflows' ), '<a href="https://cartflows.com/docs/global-checkout/" target="_blank">', '</a>' ),
					),
				),
			),
			'permalink'              => array(
				'title'  => __( 'Permalinks', 'cartflows' ),
				'fields' => array(
					'perma-structure' => array(
						'type'    => 'radio',
						'name'    => '_cartflows_permalink[permalink_structure]',
						'options' => array(
							array(
								'value' => '',
								'label' => __( 'Default Permalinks', 'cartflows' ),
								'desc'  => __( 'Default WordPress Permalink', 'cartflows' ),
							),
							array(
								'value' => '/cartflows_flow/%flowname%/cartflows_step',
								'label' => __( 'Flow and Step Slug', 'cartflows' ),
								'desc'  => $origin . '/' . CARTFLOWS_FLOW_PERMALINK_SLUG . '/%flowname%/' . CARTFLOWS_STEP_PERMALINK_SLUG . '/%stepname%/',
							),
							array(
								'value' => '/cartflows_flow/%flowname%',
								'label' => __( 'Flow Slug', 'cartflows' ),
								'desc'  => $origin . '/' . CARTFLOWS_FLOW_PERMALINK_SLUG . '/%flowname%/%stepname%/',
							),
							array(
								'value' => '/%flowname%/cartflows_step',
								'label' => __( 'Step Slug', 'cartflows' ),
								'desc'  => $origin . '/%flowname%/' . CARTFLOWS_STEP_PERMALINK_SLUG . '/%stepname%/',
							),
						),
					),
					'perma-heading'   => array(
						'type'  => 'heading',
						'label' => __( 'Post Type Permalink Base', 'cartflows' ),
					),
					'perma-step-base' => array(
						'type'  => 'text',
						'label' => __( 'Step Base', 'cartflows' ),
						'name'  => '_cartflows_permalink[permalink]',
						'class' => 'input-field',
					),
					'perma-flow-base' => array(
						'type'  => 'text',
						'label' => __( 'Flow Base', 'cartflows' ),
						'name'  => '_cartflows_permalink[permalink_flow_base]',
						'class' => 'input-field',
					),
					'perma-doc'       => array(
						'type'    => 'doc',
						/* translators: %1$s: link html start, %2$s: link html end*/
						'content' => sprintf( __( 'For more information about the CartFlows Permalink settings please %1$sClick here.%2$s', 'cartflows' ), '<a href="https://cartflows.com/docs/cartflows-permalink-settings/" target="_blank">', '</a>' ),
					),
				),
			),
			'facebook-pixel'         => array(
				'title'  => __( 'FaceBook Pixel', 'cartflows' ),
				'fields' => array(
					'enable-fb-pixel'               => array(
						'type'     => 'checkbox',
						'label'    => __( 'Enable Facebook Pixel Tracking For CartFlows Pages', 'cartflows' ),
						'name'     => '_cartflows_facebook[facebook_pixel_tracking]',
						'backComp' => true,
					),
					'enable-fb-pixel-for-site'      => array(
						'type'       => 'checkbox',
						'label'      => __( 'Enable Facebook Pixel Tracking For the whole site', 'cartflows' ),
						'name'       => '_cartflows_facebook[facebook_pixel_tracking_for_site]',
						'desc'       => __( 'If checked, page view and view content event will also be triggered for other pages/posts of site.', 'cartflows' ),
						'conditions' => array(
							'fields' => array(
								array(
									'name'     => '_cartflows_facebook[facebook_pixel_tracking]',
									'operator' => '===',
									'value'    => 'enable',
								),
							),
						),
						'backComp'   => true,
					),
					'pixel-id'                      => array(
						'type'       => 'text',
						'label'      => __( 'Enter Facebook pixel ID', 'cartflows' ),
						'name'       => '_cartflows_facebook[facebook_pixel_id]',
						'class'      => 'input-field',
						'conditions' => array(
							'fields' => array(
								array(
									'name'     => '_cartflows_facebook[facebook_pixel_tracking]',
									'operator' => '===',
									'value'    => 'enable',
								),
							),
						),
					),
					'pixel-event-heading'           => array(
						'type'       => 'heading',
						'label'      => __( 'Facebook Pixel Events', 'cartflows' ),
						'conditions' => array(
							'fields' => array(
								array(
									'name'     => '_cartflows_facebook[facebook_pixel_tracking]',
									'operator' => '===',
									'value'    => 'enable',
								),
							),
						),
					),

					'pixel-event-view-content'      => array(
						'type'       => 'checkbox',
						'label'      => __( 'View Content', 'cartflows' ),
						'name'       => '_cartflows_facebook[facebook_pixel_view_content]',
						'conditions' => array(
							'fields' => array(
								array(
									'name'     => '_cartflows_facebook[facebook_pixel_tracking]',
									'operator' => '===',
									'value'    => 'enable',
								),
							),
						),
						'backComp'   => true,
					),

					'pixel-event-ini-checkout'      => array(
						'type'       => 'checkbox',
						'label'      => __( 'Initiate Checkout', 'cartflows' ),
						'name'       => '_cartflows_facebook[facebook_pixel_initiate_checkout]',
						'conditions' => array(
							'fields' => array(
								array(
									'name'     => '_cartflows_facebook[facebook_pixel_tracking]',
									'operator' => '===',
									'value'    => 'enable',
								),
							),
						),
						'backComp'   => true,
					),

					'pixel-event-payment-info'      => array(
						'type'       => 'checkbox',
						'label'      => __( 'Add Payment Info', 'cartflows' ),
						'name'       => '_cartflows_facebook[facebook_pixel_add_payment_info]',
						'conditions' => array(
							'fields' => array(
								array(
									'name'     => '_cartflows_facebook[facebook_pixel_tracking]',
									'operator' => '===',
									'value'    => 'enable',
								),
							),
						),
						'backComp'   => true,
					),

					'pixel-event-purchase-complete' => array(
						'type'       => 'checkbox',
						'label'      => __( 'Purchase Complete', 'cartflows' ),
						'name'       => '_cartflows_facebook[facebook_pixel_purchase_complete]',
						'conditions' => array(
							'fields' => array(
								array(
									'name'     => '_cartflows_facebook[facebook_pixel_tracking]',
									'operator' => '===',
									'value'    => 'enable',
								),
							),
						),
						'backComp'   => true,
					),

					'pixel-event-lead-info'         => array(
						'type'       => 'checkbox',
						'label'      => __( 'Optin Lead', 'cartflows' ),
						'name'       => '_cartflows_facebook[facebook_pixel_optin_lead]',
						'conditions' => array(
							'fields' => array(
								array(
									'name'     => '_cartflows_facebook[facebook_pixel_tracking]',
									'operator' => '===',
									'value'    => 'enable',
								),
							),
						),
						'backComp'   => true,
						'tooltip'    => __( 'Optin Lead event will be trigger for optin page.', 'cartflows' ),
					),

					'pixel-not-work-doc'            => array(
						'type'       => 'doc',
						'label'      => '',
						'name'       => 'pixel-not-work-doc',
						/* translators: %1$1s: link html start, %2$12: link html end*/
						'content'    => sprintf( __( 'Facebook Pixel not working correctly? %1$1s Click here %2$2s to know more.', 'cartflows' ), '<a href="https://cartflows.com/docs/facebook-pixel-support/" target="_blank">', '</a>' ),
						'conditions' => array(
							'fields' => array(
								array(
									'name'     => '_cartflows_facebook[facebook_pixel_tracking]',
									'operator' => '===',
									'value'    => 'enable',
								),
							),
						),
					),
				),
			),
			'ga-analytics'           => array(
				'title'  => __( 'Google Analytics', 'cartflows' ),
				'fields' => array(
					'enable-ga-analytics'          => array(
						'type'     => 'checkbox',
						'label'    => __( 'Enable Google Analytics Tracking For CartFlows Pages', 'cartflows' ),
						'name'     => '_cartflows_google_analytics[enable_google_analytics]',
						'backComp' => true,
					),
					'enable-ga-analytics-for-site' => array(
						'type'       => 'checkbox',
						'label'      => __( 'Enable Google Analytics Tracking For the whole site', 'cartflows' ),
						'name'       => '_cartflows_google_analytics[enable_google_analytics_for_site]',
						'desc'       => __( 'If checked, page view event will also be triggered for other pages/posts of site.', 'cartflows' ),
						'conditions' => array(
							'fields' => array(
								array(
									'name'     => '_cartflows_google_analytics[enable_google_analytics]',
									'operator' => '===',
									'value'    => 'enable',
								),
							),
						),
						'backComp'   => true,
					),
					'ga-id'                        => array(
						'type'       => 'text',
						'label'      => __( 'Enter Google Analytics ID', 'cartflows' ),
						'name'       => '_cartflows_google_analytics[google_analytics_id]',
						'class'      => 'input-field',
						/* translators: %1$1s: link html start, %2$12: link html end*/
						'desc'       => sprintf( __( 'Log into your %1$1s google analytics account %2$2s to find your ID. e.g. G-XXXXX or UA-XXXXX-X', 'cartflows' ), '<a href="https://analytics.google.com/" target="_blank">', '</a>' ),
						'conditions' => array(
							'fields' => array(
								array(
									'name'     => '_cartflows_google_analytics[enable_google_analytics]',
									'operator' => '===',
									'value'    => 'enable',
								),
							),
						),
					),
					'ga-event-heading'             => array(
						'type'       => 'heading',
						'label'      => __( 'Google Analytics Events', 'cartflows' ),
						'conditions' => array(
							'fields' => array(
								array(
									'name'     => '_cartflows_google_analytics[enable_google_analytics]',
									'operator' => '===',
									'value'    => 'enable',
								),
							),
						),
					),
					'ga-event-ini-checkout'        => array(
						'type'       => 'checkbox',
						'label'      => __( 'Begin Checkout', 'cartflows' ),
						'name'       => '_cartflows_google_analytics[enable_begin_checkout]',
						'conditions' => array(
							'fields' => array(
								array(
									'name'     => '_cartflows_google_analytics[enable_google_analytics]',
									'operator' => '===',
									'value'    => 'enable',
								),
							),
						),
						'backComp'   => true,
					),

					'ga-event-add-to-cart'         => array(
						'type'       => 'checkbox',
						'label'      => __( 'Add To Cart', 'cartflows' ),
						'name'       => '_cartflows_google_analytics[enable_add_to_cart]',
						'conditions' => array(
							'fields' => array(
								array(
									'name'     => '_cartflows_google_analytics[enable_google_analytics]',
									'operator' => '===',
									'value'    => 'enable',
								),
							),
						),
						'backComp'   => true,
					),
					'ga-event-payment-info'        => array(
						'type'       => 'checkbox',
						'label'      => __( 'Add Payment Info', 'cartflows' ),
						'name'       => '_cartflows_google_analytics[enable_add_payment_info]',
						'conditions' => array(
							'fields' => array(
								array(
									'name'     => '_cartflows_google_analytics[enable_google_analytics]',
									'operator' => '===',
									'value'    => 'enable',
								),
							),
						),
						'backComp'   => true,
					),
					'ga-event-purchase-complete'   => array(
						'type'       => 'checkbox',
						'label'      => __( 'Purchase', 'cartflows' ),
						'name'       => '_cartflows_google_analytics[enable_purchase_event]',
						'conditions' => array(
							'fields' => array(
								array(
									'name'     => '_cartflows_google_analytics[enable_google_analytics]',
									'operator' => '===',
									'value'    => 'enable',
								),
							),
						),
						'backComp'   => true,
					),

					'ga-event-lead-info'           => array(
						'type'       => 'checkbox',
						'label'      => __( 'Optin Lead', 'cartflows' ),
						'name'       => '_cartflows_google_analytics[enable_optin_lead]',
						'conditions' => array(
							'fields' => array(
								array(
									'name'     => '_cartflows_google_analytics[enable_google_analytics]',
									'operator' => '===',
									'value'    => 'enable',
								),
							),
						),
						'backComp'   => true,
						'tooltip'    => __( 'Optin Lead event will be trigger for optin page.', 'cartflows' ),
					),

					'ga-not-work-doc'              => array(
						'type'       => 'doc',
						'label'      => '',
						'name'       => 'ga-not-work-doc',
						/* translators: %1$1s: link html start, %2$12: link html end*/
						'content'    => sprintf( __( 'Google Analytics not working correctly? %1$1s Click here %2$2s to know more.', 'cartflows' ), '<a href="https://cartflows.com/docs/troubleshooting-google-analytics-tracking-issues/" target="_blank">', '</a>' ),
						'conditions' => array(
							'fields' => array(
								array(
									'name'     => '_cartflows_google_analytics[enable_google_analytics]',
									'operator' => '===',
									'value'    => 'enable',
								),
							),
						),
					),
				),
			),
			'g-address-autocomplete' => array(
				'title'  => __( 'Google Address Autocomplete', 'cartflows' ),
				'fields' => array(
					'g-api-key' => array(
						'type'      => 'password',
						'label'     => __( 'Enter Google Map API key', 'cartflows' ),
						'name'      => '_cartflows_google_auto_address[google_map_api_key]',
						'class'     => 'input-field',
						'icon'      => 'dashicons dashicons-visibility',
						'afterIcon' => 'dashicons dashicons-hidden',
						'iconclick' => 'show_field_value',
						/* translators: %1$1s: link html start, %2$12: link html end*/
						'desc'      => sprintf( __( 'Check this %1$1s article %2$2s to setup and find an API key.', 'cartflows' ), '<a href="https://cartflows.com/docs/enabling-google-address-autocompletes/" target="_blank">', '</a>' ),
					),
				),
			),
			'other-settings'         => array(
				'title'  => __( 'Other', 'cartflows' ),
				'fields' => array(
					'weekly-report-heading'      => array(
						'type'  => 'heading',
						'label' => __( 'Store Revenue Report Emails', 'cartflows' ),
					),
					'enable_weekly_emails'       => array(
						'type'     => 'checkbox',
						'name'     => 'cartflows_stats_report_emails',
						'label'    => __( 'Enable sending CartFlows analytics report emails.', 'cartflows' ),
						'backComp' => true,
						/* translators: %1$1s: link html start, %2$12: link html end*/
						'desc'     => __( 'If enabled, you will receive the weekly report emails of your store for the revenue stats generated by CartFlows.', 'cartflows' ),
					),
					'email_id_for_weekly_emails' => array(
						'type'       => 'textarea',
						'rows'       => 2,
						'cols'       => 38,
						'name'       => 'cartflows_stats_report_email_ids',
						'label'      => __( 'Email Adddress', 'cartflows' ),
						'desc'       => __( 'Email address to receive the weekly sales report emails. For multiple emails, add each email address per line.', 'cartflows' ),
						'conditions' => array(
							'fields' => array(
								array(
									'name'     => 'cartflows_stats_report_emails',
									'operator' => '===',
									'value'    => 'enable',
								),
							),
						),
					),
					'seperator'                  => array(
						'type' => 'separator',
					),
					'delete_data'                => array(
						'type'     => 'checkbox',
						'name'     => 'cartflows_delete_plugin_data',
						'label'    => __( 'Delete plugin data on plugin deletion', 'cartflows' ),
						'backComp' => true,
						'notice'   => array(
							'type'    => 'prompt',
							'check'   => 'delete',
							'message' => __( 'Are you sure? Do you want to delete plugin data while deleting the plugin? Type "DELETE" to confirm!', 'cartflows' ),
						),
						/* translators: %1$1s: link html start, %2$12: link html end*/
						'desc'     => sprintf( __( 'This option will delete all the CartFlows options data on plugin deletion. If you enable this and deletes the plugin, you can\'t restore your saved data. To learn more, %1$1s Click here %2$2s.', 'cartflows' ), '<a href="https://cartflows.com/docs/delete-plugin-data-while-uninstalling-plugin/" target="_blank">', '</a>' ),
					),
				),
			),
			'user-role-management'   => array(
				'title' => __( 'User Role Manager', 'cartflows' ),
				'roles' => (
					$all_roles
				),
			),
		);

		$settings = apply_filters( 'cartflows_admin_global_settings_data', $settings );

		return $settings;
	}
}
