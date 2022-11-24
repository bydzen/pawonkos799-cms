<?php
/**
 * CartFlows flow Meta Helper.
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
class FlowMeta {


	/**
	 * Get flow meta options.
	 *
	 * @param int $flow_id flow id.
	 */
	public static function get_meta_settings( $flow_id ) {

		$settings      = self::get_settings_fields( $flow_id );
		$settings_data = array(
			'settings' => $settings,
		);
		return $settings_data;
	}


	/**
	 * Page Header Tabs.
	 *
	 * @param int $flow_id id.
	 */
	public static function get_settings_fields( $flow_id ) {

		$settings = array(
			'general'       => array(
				'title'    => __( 'General ', 'cartflows' ),
				'slug'     => 'general',
				'fields'   => array(
					'flow_slug'     => array(
						'type'  => 'text',
						'name'  => 'post_name',
						'label' => __( 'Flow Slug', 'cartflows' ),
						'value' => get_post_field( 'post_name', $flow_id ),
					),
					'flow_indexing' => array(
						'type'    => 'select',
						'name'    => 'wcf-flow-indexing',
						'label'   => __( 'Disallow flow indexing', 'cartflows' ),
						'tooltip' => __( 'It will overwrite the global setting option. To use the global option, set it to default.', 'cartflows' ),
						'options' => array(
							array(
								'value' => '',
								'label' => __( 'Default', 'cartflows' ),
							),
							array(
								'value' => 'disallow',
								'label' => __( 'Yes', 'cartflows' ),
							),
							array(
								'value' => 'allow',
								'label' => __( 'No', 'cartflows' ),
							),
						),
						'value'   => get_post_meta( $flow_id, 'wcf-flow-indexing', true ),
					),
				),
				'priority' => 10,
			),
			'sandbox'       => array(
				'title'    => __( 'Sandbox', 'cartflows' ),
				'slug'     => 'sandbox',
				'fields'   => array(
					'sandbox_mode' => array(
						'type'  => 'checkbox',
						'label' => __( 'Enable Test Mode', 'cartflows' ),
						'name'  => 'wcf-testing',
						'value' => get_post_meta( $flow_id, 'wcf-testing', true ),
						'desc'  => __( 'Test mode will add random products in your flow if products are not selected in checkout settings, so you can preview it easily while testing.', 'cartflows' ),
					),
				),
				'priority' => 30,
			),
			'analytics'     => array(
				'title'      => __( 'Analytics', 'cartflows' ),
				'slug'       => 'analytics',
				'fields'     => array(
					'analtics_option' => array(
						'type'  => 'checkbox',
						'label' => __( 'Enable Flow Analytics', 'cartflows' ),
						'name'  => 'wcf-enable-analytics',
						'value' => get_post_meta( $flow_id, 'wcf-enable-analytics', true ),
						'desc'  => __( 'Analytics offers data that helps you understand how your flows are performing.', 'cartflows' ),
					),
				),
				'visibility' => ! ( _is_cartflows_pro() && is_wcf_pro_plan() ) ? 'hidden' : '',
				'priority'   => 20,
			),
			'custom-script' => array(
				'title'    => __( 'Custom Script', 'cartflows' ),
				'slug'     => 'custom_script',
				'fields'   => array(
					'script_option' => array(
						'type'    => 'textarea',
						'label'   => __( 'Custom Script', 'cartflows' ),
						'name'    => 'wcf-flow-custom-script',
						'value'   => get_post_meta( $flow_id, 'wcf-flow-custom-script', true ),
						'tooltip' => __( 'This scustom script will execute on all steps of this flow.', 'cartflows' ),
					),
				),
				'priority' => 40,
			),
		);

		return $settings;
	}
}
