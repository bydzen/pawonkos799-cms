<?php
/**
 * Checkout post meta fields
 *
 * @package CartFlows
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
use CartflowsAdmin\AdminCore\Inc\AdminHelper;
/**
 * Meta Boxes setup
 */
class Cartflows_Checkout_Meta_Data extends Cartflows_Step_Meta_Base {


	/**
	 * Instance
	 *
	 * @var $instance
	 */
	private static $instance;


	/**
	 * Initiator
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {

		add_filter( 'cartflows_admin_checkout_step_meta_fields', array( $this, 'filter_values' ), 10, 2 );

		add_filter( 'cartflows_admin_checkout_step_meta_settings', array( $this, 'remove_store_checkout_product' ), 10, 2 );

		// Step API data.
		add_filter( 'cartflows_admin_checkout_step_data', array( $this, 'add_checkout_step_api_data' ), 10, 2 );
	}

			/**
			 * Add required data to api.
			 *
			 * @param  array $api_data data.
			 * @param  int   $step_id step id.
			 */
	public function add_checkout_step_api_data( $api_data, $step_id ) {

		$field_data                  = $this->custom_fields_data( $step_id );
		$api_data['custom_fields']   = $field_data;
		$api_data['billing_fields']  = $field_data['billing_fields'];
		$api_data['shipping_fields'] = $field_data['shipping_fields'];

		return $api_data;
	}

	/**
	 * Removes settings tab checkout_product for store checkout
	 *
	 * @param array $settings prepared settings array.
	 * @param int   $step_id current step id.
	 * @return array
	 * @since X.X.X
	 */
	public function remove_store_checkout_product( $settings, $step_id ) {
		$flow_id = absint( get_post_meta( $step_id, 'wcf-flow-id', true ) );

		if ( absint( \Cartflows_Helper::get_global_setting( '_cartflows_store_checkout' ) ) === $flow_id && ! apply_filters( 'cartflows_show_store_checkout_product_tab', false ) ) {
			unset( $settings['tabs']['checkout_products'] );
		}

		return $settings;
	}


	/**
	 * Filter checkout values
	 *
	 * @param  array $options options.
	 * @param  int   $step_id post id.
	 */
	public function filter_values( $options, $step_id ) {

		if ( isset( $options['wcf-checkout-products'] ) ) {
			// Update the product name in the option 'wcf-checkout-products'.
			$checkout_products = $options['wcf-checkout-products'];

			if ( is_array( $checkout_products ) && isset( $checkout_products[0] ) ) {

				foreach ( $checkout_products as $index => $product ) {

					$product_obj = wc_get_product( $product['product'] );
					if ( $product_obj ) {
						$checkout_products[ $index ]['name']          = $product_obj->get_name() . ' (#' . $product_obj->get_id() . ')';
						$checkout_products[ $index ]['img_url']       = get_the_post_thumbnail_url( $product['product'] );
						$checkout_products[ $index ]['regular_price'] = Cartflows_Helper::get_product_original_price( $product_obj );
					}
				}
			} else {
				$checkout_products = array();
			}

			$options['wcf-checkout-products'] = $checkout_products;
		}

		if ( isset( $options['wcf_field_order_billing'] ) ) {
			$options['wcf_field_order_billing'] = $this->get_field_settings( $step_id, 'billing', '' );
		}

		if ( isset( $options['wcf_field_order_shipping'] ) ) {
			$options['wcf_field_order_shipping'] = $this->get_field_settings( $step_id, 'shipping', '' );
		}

		return $options;
	}

	/**
	 * Page Header Tabs
	 *
	 * @param  int   $step_id Post meta.
	 * @param  array $options options.
	 */
	public function get_settings( $step_id, $options = array() ) {

		$this->step_id = $step_id;
		$this->options = $options;

		$common_tabs = $this->common_tabs();
		$add_tabs    = array(
			'checkout_products'    => array(
				'title'    => __( 'Products', 'cartflows' ),
				'id'       => 'checkout_products',
				'class'    => '',
				'icon'     => 'dashicons-format-aside',
				'priority' => 10,
			),
			'multi_order_bump'     => array(
				'title'    => __( 'Order Bumps', 'cartflows' ),
				'id'       => 'order_bumps',
				'class'    => '',
				'icon'     => 'dashicons-format-aside',
				'priority' => 20,
			),
			'checkout_form_fields' => array(
				'title'    => __( 'Form Fields', 'cartflows' ),
				'id'       => 'checkout_form_fields',
				'class'    => '',
				'icon'     => 'dashicons-format-aside',
				'priority' => 40,
			),
			'dynamic-offers'       => array(
				'title'    => __( 'Dynamic Offers', 'cartflows' ),
				'id'       => 'dynamic-offers',
				'class'    => '',
				'icon'     => 'dashicons-format-aside',
				'priority' => 50,
			),
			'settings'             => array(
				'title'    => __( 'Settings', 'cartflows' ),
				'id'       => 'settings',
				'class'    => '',
				'icon'     => 'dashicons-format-aside',
				'priority' => 60,
			),
		);

		if ( _is_cartflows_pro() && 'enable' === Cartflows_Helper::get_common_settings()['pre_checkout_offer'] ) {

			$add_tabs['checkout_offer'] = array(
				'title'    => __( 'Checkout Offer', 'cartflows' ),
				'id'       => 'checkout_offer',
				'class'    => '',
				'icon'     => 'dashicons-format-aside',
				'priority' => 30,
			);
		}

		$tabs            = array_merge( $common_tabs, $add_tabs );
		$settings        = $this->get_settings_fields( $step_id );
		$design_settings = $this->get_design_fields( $step_id );
		$options         = $this->get_data( $step_id );

		return array(
			'tabs'            => $tabs,
			'settings'        => $settings,
			'page_settings'   => $this->get_page_settings( $step_id ),
			'design_settings' => $design_settings,
		);

	}

	/**
	 * Get design settings data.
	 *
	 * @param  int $step_id Post ID.
	 */
	public function get_design_fields( $step_id ) {

		$options           = $this->get_data( $step_id );
		$layout_pro_option = array();

		if ( ! _is_cartflows_pro() ) {
			$layout_pro_option = array(
				'two-step'           => __( 'Two Step (Available in higher plan) ', 'cartflows' ),
				'multistep-checkout' => __( 'Multistep Checkout (Available in higher plan) ', 'cartflows' ),
			);
		}

		$settings = array(
			'settings' => array(
				'checkout-design'          => array(
					'title'    => __( 'Checkout Design', 'cartflows' ),
					'slug'     => 'checkout_design',
					'priority' => 10,
					'fields'   => array(
						'checkout-skin'       => array(
							'type'        => 'select',
							'label'       => __( 'Checkout Skin', 'cartflows' ),
							'name'        => 'wcf-checkout-layout',
							'value'       => $options['wcf-checkout-layout'],
							'options'     => array(
								array(
									'value' => 'modern-checkout',
									'label' => esc_html__( 'Modern Checkout', 'cartflows' ),
								),
								array(
									'value' => 'modern-one-column',
									'label' => esc_html__( 'Modern One Column', 'cartflows' ),
								),
								array(
									'value' => 'multistep-checkout',
									'label' => esc_html__( 'Multistep Checkout', 'cartflows' ),
								),
								array(
									'value' => 'one-column',
									'label' => esc_html__( 'One Column', 'cartflows' ),
								),
								array(
									'value' => 'two-column',
									'label' => esc_html__( 'Two Column', 'cartflows' ),
								),
								array(
									'value' => 'two-step',
									'label' => esc_html__( 'Two Step', 'cartflows' ),
								),

							),
							'pro_options' => $layout_pro_option,
						),
						'primary-color'       => array(
							'type'  => 'color-picker',
							'name'  => 'wcf-primary-color',
							'label' => __( 'Primary Color', 'cartflows' ),
							'value' => $options['wcf-primary-color'],
						),
						'heading-font-family' => array(
							'type'  => 'font-family',
							'label' => esc_html__( 'Font Family', 'cartflows' ),
							'name'  => 'wcf-base-font-family',
							'value' => $options['wcf-base-font-family'],
						),
					),
				),

				'checkout-text-design'     => array(
					'title'    => __( 'Checkout Texts & Buttons', 'cartflows' ),
					'slug'     => 'checkout_texts_buttons',
					'priority' => 20,
					'fields'   => array(
						'advanced-options'          => array(
							'type'  => 'checkbox',
							'label' => __( 'Enable Advance Options', 'cartflows' ),
							'name'  => 'wcf-advance-options-fields',
							'value' => $options['wcf-advance-options-fields'],
						),

						'heading-heading'           => array(
							'type'       => 'heading',
							'label'      => esc_html__( 'Heading', 'cartflows' ),
							'conditions' => array(
								'fields' => array(
									array(
										'name'     => 'wcf-advance-options-fields',
										'operator' => '===',
										'value'    => 'yes',
									),
								),
							),
						),

						'heading-font-color'        => array(
							'type'       => 'color-picker',
							'label'      => __( 'Heading Color', 'cartflows' ),
							'name'       => 'wcf-heading-color',
							'value'      => $options['wcf-heading-color'],
							'conditions' => array(
								'fields' => array(
									array(
										'name'     => 'wcf-advance-options-fields',
										'operator' => '===',
										'value'    => 'yes',
									),
								),
							),
						),
						'heading-font-family'       => array(
							'type'              => 'font-family',
							'for'               => 'wcf-heading',
							'label'             => esc_html__( 'Font Family', 'cartflows' ),
							'name'              => 'wcf-heading-font-family',
							'value'             => $options['wcf-heading-font-family'],
							'font_weight_name'  => 'wcf-heading-font-weight',
							'font_weight_value' => $options['wcf-heading-font-weight'],
							'for'               => 'wcf-heading',
							'conditions'        => array(
								'fields' => array(
									array(
										'name'     => 'wcf-advance-options-fields',
										'operator' => '===',
										'value'    => 'yes',
									),
								),
							),
						),

						'heading-input-field'       => array(
							'type'       => 'heading',
							'label'      => esc_html__( 'Input Fields', 'cartflows' ),
							'conditions' => array(
								'fields' => array(
									array(
										'name'     => 'wcf-advance-options-fields',
										'operator' => '===',
										'value'    => 'yes',
									),
								),
							),
						),

						'input-field-style'         => array(
							'type'       => 'select',
							'label'      => __( 'Style', 'cartflows' ),
							'name'       => 'wcf-fields-skins',
							'value'      => 'style-one' === $options['wcf-fields-skins'] ? 'modern-label' : $options['wcf-fields-skins'],
							'options'    => array(
								array(
									'value' => 'default',
									'label' => esc_html__( 'Default', 'cartflows' ),
								),
								array(
									'value' => 'modern-label',
									'label' => esc_html__( 'Modern Labels', 'cartflows' ),
								),
							),
							'conditions' => array(
								'fields' => array(
									array(
										'name'     => 'wcf-advance-options-fields',
										'operator' => '===',
										'value'    => 'yes',
									),
								),
							),
						),
						'input-font-family'         => array(
							'type'              => 'font-family',
							'for'               => 'wcf-input',
							'label'             => esc_html__( 'Font Family', 'cartflows' ),
							'name'              => 'wcf-input-font-family',
							'value'             => $options['wcf-input-font-family'],
							'font_weight_name'  => 'wcf-input-font-weight',
							'font_weight_value' => $options['wcf-input-font-weight'],
							'for'               => 'wcf-input',
							'conditions'        => array(
								'fields' => array(
									array(
										'name'     => 'wcf-advance-options-fields',
										'operator' => '===',
										'value'    => 'yes',
									),
								),
							),
						),

						'input-size'                => array(
							'type'       => 'select',
							'label'      => __( 'Size', 'cartflows' ),
							'name'       => 'wcf-input-field-size',
							'value'      => $options['wcf-input-field-size'],
							'options'    => array(
								array(
									'value' => '33px',
									'label' => esc_html__( 'Extra Small', 'cartflows' ),
								),
								array(
									'value' => '38px',
									'label' => esc_html__( 'Small', 'cartflows' ),
								),
								array(
									'value' => '44px',
									'label' => esc_html__( 'Medium', 'cartflows' ),
								),
								array(
									'value' => '58px',
									'label' => esc_html__( 'Large', 'cartflows' ),
								),
								array(
									'value' => '68px',
									'label' => esc_html__( 'Extra Large', 'cartflows' ),
								),
								array(
									'value' => 'custom',
									'label' => esc_html__( 'Custom', 'cartflows' ),
								),
							),
							'conditions' => array(
								'fields' => array(
									array(
										'name'     => 'wcf-advance-options-fields',
										'operator' => '===',
										'value'    => 'yes',
									),
								),
							),
						),
						'input-bottom-space'        => array(
							'type'       => 'number',
							'label'      => __( 'Top Bottom Spacing', 'cartflows' ),
							'name'       => 'wcf-field-tb-padding',
							'value'      => $options['wcf-field-tb-padding'],
							'conditions' => array(
								'fields' => array(
									array(
										'name'     => 'wcf-input-field-size',
										'operator' => '===',
										'value'    => 'custom',
									),
								),
							),
						),
						'input-right-space'         => array(
							'type'       => 'number',
							'label'      => __( 'Left Right Spacing', 'cartflows' ),
							'name'       => 'wcf-field-lr-padding',
							'value'      => $options['wcf-field-lr-padding'],
							'conditions' => array(
								'fields' => array(
									array(
										'name'     => 'wcf-input-field-size',
										'operator' => '===',
										'value'    => 'custom',
									),
								),
							),
						),
						'input-text/p-color'        => array(
							'type'       => 'color-picker',
							'label'      => __( 'Text / Placeholder Color', 'cartflows' ),
							'name'       => 'wcf-field-color',
							'value'      => $options['wcf-field-color'],
							'conditions' => array(
								'fields' => array(
									array(
										'name'     => 'wcf-advance-options-fields',
										'operator' => '===',
										'value'    => 'yes',
									),
								),
							),
						),
						'input-bg-color'            => array(
							'type'       => 'color-picker',
							'label'      => __( 'Background Color', 'cartflows' ),
							'name'       => 'wcf-field-bg-color',
							'value'      => $options['wcf-field-bg-color'],
							'conditions' => array(
								'fields' => array(
									array(
										'name'     => 'wcf-advance-options-fields',
										'operator' => '===',
										'value'    => 'yes',
									),
								),
							),
						),
						'input-border-color'        => array(
							'type'       => 'color-picker',
							'label'      => __( 'Border Color', 'cartflows' ),
							'name'       => 'wcf-field-border-color',
							'value'      => $options['wcf-field-border-color'],
							'conditions' => array(
								'fields' => array(
									array(
										'name'     => 'wcf-advance-options-fields',
										'operator' => '===',
										'value'    => 'yes',
									),
								),
							),
						),
						'input-label-color'         => array(
							'type'       => 'color-picker',
							'label'      => __( 'Label Color', 'cartflows' ),
							'name'       => 'wcf-field-label-color',
							'value'      => $options['wcf-field-label-color'],
							'conditions' => array(
								'fields' => array(
									array(
										'name'     => 'wcf-advance-options-fields',
										'operator' => '===',
										'value'    => 'yes',
									),
								),
							),
						),

						'button-heading'            => array(
							'type'       => 'heading',
							'label'      => esc_html__( 'Buttons', 'cartflows' ),
							'conditions' => array(
								'fields' => array(
									array(
										'name'     => 'wcf-advance-options-fields',
										'operator' => '===',
										'value'    => 'yes',
									),
								),
							),
						),
						'button-font-family'        => array(
							'type'              => 'font-family',
							'for'               => 'wcf-button',
							'label'             => esc_html__( 'Font Family', 'cartflows' ),
							'name'              => 'wcf-button-font-family',
							'value'             => $options['wcf-button-font-family'],
							'font_weight_name'  => 'wcf-button-font-weight',
							'font_weight_value' => $options['wcf-button-font-weight'],
							'for'               => 'wcf-button',
							'conditions'        => array(
								'fields' => array(
									array(
										'name'     => 'wcf-advance-options-fields',
										'operator' => '===',
										'value'    => 'yes',
									),
								),
							),
						),

						'buttom-font-size'          => array(
							'type'       => 'select',
							'label'      => __( 'Size', 'cartflows' ),
							'name'       => 'wcf-input-button-size',
							'value'      => $options['wcf-input-button-size'],
							'options'    => array(
								array(
									'value' => '33px',
									'label' => esc_html__( 'Extra Small', 'cartflows' ),
								),
								array(
									'value' => '38px',
									'label' => esc_html__( 'Small', 'cartflows' ),
								),
								array(
									'value' => '44px',
									'label' => esc_html__( 'Medium', 'cartflows' ),
								),
								array(
									'value' => '58px',
									'label' => esc_html__( 'Large', 'cartflows' ),
								),
								array(
									'value' => '68px',
									'label' => esc_html__( 'Extra Large', 'cartflows' ),
								),
								array(
									'value' => 'custom',
									'label' => esc_html__( 'Custom', 'cartflows' ),
								),
							),
							'conditions' => array(
								'fields' => array(
									array(
										'name'     => 'wcf-advance-options-fields',
										'operator' => '===',
										'value'    => 'yes',
									),
								),
							),
						),
						'buttom-top-space'          => array(
							'type'       => 'number',
							'label'      => __( 'Top Bottom Spacing', 'cartflows' ),
							'name'       => 'wcf-submit-tb-padding',
							'value'      => $options['wcf-submit-tb-padding'],
							'conditions' => array(
								'fields' => array(
									array(
										'name'     => 'wcf-input-button-size',
										'operator' => '===',
										'value'    => 'custom',
									),
									array(
										'name'     => 'wcf-advance-options-fields',
										'operator' => '===',
										'value'    => 'yes',
									),
								),
							),
						),
						'buttom-left-space'         => array(
							'type'       => 'number',
							'label'      => __( 'Left Right Spacing', 'cartflows' ),
							'name'       => 'wcf-submit-lr-padding',
							'value'      => $options['wcf-submit-lr-padding'],
							'conditions' => array(
								'fields' => array(
									array(
										'name'     => 'wcf-input-button-size',
										'operator' => '===',
										'value'    => 'custom',
									),
									array(
										'name'     => 'wcf-advance-options-fields',
										'operator' => '===',
										'value'    => 'yes',
									),
								),
							),
						),
						'buttom-text-color'         => array(
							'type'       => 'color-picker',
							'label'      => __( 'Text Color', 'cartflows' ),
							'name'       => 'wcf-submit-color',
							'value'      => $options['wcf-submit-color'],
							'conditions' => array(
								'fields' => array(
									array(
										'name'     => 'wcf-advance-options-fields',
										'operator' => '===',
										'value'    => 'yes',
									),
								),
							),
						),
						'buttom-text-hover-color'   => array(
							'type'       => 'color-picker',
							'label'      => __( 'Text Hover Color', 'cartflows' ),
							'name'       => 'wcf-submit-hover-color',
							'value'      => $options['wcf-submit-hover-color'],
							'conditions' => array(
								'fields' => array(
									array(
										'name'     => 'wcf-advance-options-fields',
										'operator' => '===',
										'value'    => 'yes',
									),
								),
							),
						),
						'buttom-bg-color'           => array(
							'type'       => 'color-picker',
							'label'      => __( 'Background Color', 'cartflows' ),
							'name'       => 'wcf-submit-bg-color',
							'value'      => $options['wcf-submit-bg-color'],
							'conditions' => array(
								'fields' => array(
									array(
										'name'     => 'wcf-advance-options-fields',
										'operator' => '===',
										'value'    => 'yes',
									),
								),
							),
						),
						'buttom-bg-hover-color'     => array(
							'type'       => 'color-picker',
							'label'      => __( 'Background Hover Color', 'cartflows' ),
							'name'       => 'wcf-submit-bg-hover-color',
							'value'      => $options['wcf-submit-bg-hover-color'],
							'conditions' => array(
								'fields' => array(
									array(
										'name'     => 'wcf-advance-options-fields',
										'operator' => '===',
										'value'    => 'yes',
									),
								),
							),
						),
						'buttom-border-color'       => array(
							'type'       => 'color-picker',
							'label'      => __( 'Border Color', 'cartflows' ),
							'name'       => 'wcf-submit-border-color',
							'value'      => $options['wcf-submit-border-color'],
							'conditions' => array(
								'fields' => array(
									array(
										'name'     => 'wcf-advance-options-fields',
										'operator' => '===',
										'value'    => 'yes',
									),
								),
							),
						),
						'buttom-border-hover-color' => array(
							'type'       => 'color-picker',
							'label'      => __( 'Border Hover Color', 'cartflows' ),
							'name'       => 'wcf-submit-border-hover-color',
							'value'      => $options['wcf-submit-border-hover-color'],
							'conditions' => array(
								'fields' => array(
									array(
										'name'     => 'wcf-advance-options-fields',
										'operator' => '===',
										'value'    => 'yes',
									),
								),
							),
						),

						'section-heading'           => array(
							'type'       => 'heading',
							'label'      => esc_html__( 'Sections', 'cartflows' ),
							'conditions' => array(
								'fields' => array(
									array(
										'name'     => 'wcf-advance-options-fields',
										'operator' => '===',
										'value'    => 'yes',
									),
								),
							),
						),

						'highlighted-area'          => array(
							'type'       => 'color-picker',
							'label'      => __( 'Highlight Area Background Color', 'cartflows' ),
							'name'       => 'wcf-hl-bg-color',
							'value'      => $options['wcf-hl-bg-color'],
							'conditions' => array(
								'fields' => array(
									array(
										'name'     => 'wcf-advance-options-fields',
										'operator' => '===',
										'value'    => 'yes',
									),
								),
							),
						),

					),
				),

				'checkout-two-step-design' => array(
					'title'      => __( 'Two-Step Design', 'cartflows' ),
					'slug'       => 'two_step_design',
					'priority'   => 40,
					'fields'     => ! _is_cartflows_pro() ? array(
						'product-option' => array(
							'type'    => 'pro-notice',
							'feature' => 'Two Step',
						),
					) : '',
					'conditions' => array(
						'relation' => 'and',
						'fields'   => array(
							array(
								'name'     => 'wcf-checkout-layout',
								'operator' => '===',
								'value'    => 'two-step',
							),
						),
					),
				),

				'product-option-design'    => array(
					'title'    => __( 'Product Options', 'cartflows' ),
					'slug'     => 'product_options',
					'priority' => 50,
					'fields'   => ! _is_cartflows_pro() ? array(
						'product-option' => array(
							'type'    => 'pro-notice',
							'feature' => 'Product Options',
						),
					) : '',
				),

				'checkout-offer-design'    => array(
					'title'    => __( 'Checkout Offer', 'cartflows' ),
					'slug'     => 'checkout_offer',
					'priority' => 70,
					'fields'   => ! _is_cartflows_pro() ? array(
						'product-option' => array(
							'type'    => 'pro-notice',
							'feature' => 'Checkout Offer',
						),
					)

						: '',
				),
			),
		);

		// @todo Remove this code after v1.7.4 update.
		// Start.

		if ( in_array( get_option( 'wcf_order_bump_migrated', false ), array( 'no', 'processing' ), true ) ) {
			$settings['settings']['order-bump-design'] = array(
				'title'    => __( 'Order Bump', 'cartflows' ),
				'priority' => 70,
				'fields'   => ! _is_cartflows_pro() ? array(
					'order-bump' => array(
						'type'    => 'pro-notice',
						'feature' => 'Order Bump',
					),
				)

					: array(),
			);
		}
		// End.

		$settings = apply_filters( 'cartflows_admin_checkout_design_fields', $settings, $options );
		return $settings;
	}

	/**
	 * Get dummy settings for product options.
	 */
	public function get_dummy_settings_for_product_options() {
		return array(
			'wcf-enable-product-options'    => array(
				'type'  => 'checkbox',
				'label' => __( 'Enable Product Options', 'cartflows' ),
			),
			'wcf-get-product-option-fields' => array(
				'type'  => 'product-options',
				'label' => __( 'Enable Product Options', 'cartflows' ),
			),

			'wcf-product-options'           => array(
				'type'    => 'radio',
				'label'   => __( 'Product Options Conditions', 'cartflows' ),
				'options' => array(
					array(
						'value' => 'force-all',
						'label' => __( 'Restrict user to purchase all products', 'cartflows' ),
					),
					array(
						'value' => 'single-selection',
						'label' => __( 'Let user select one product from all options', 'cartflows' ),
					),
					array(
						'value' => 'multiple-selection',
						'label' => __( 'Let user select multiple products from all options', 'cartflows' ),
					),
				),
			),
			'enable-variation'              => array(
				'type'  => 'checkbox',
				'label' => __( 'Enable Variations', 'cartflows' ),
			),
			'wcf-product-variation-options' => array(
				'type'        => 'radio',
				'label'       => '',
				'child_class' => 'wcf-child-field',
				'options'     => array(
					array(
						'value' => 'inline',
						'label' => __( 'Show variations inline', 'cartflows' ),
					),
					array(
						'value' => 'popup',
						'label' => __( 'Show variations in popup', 'cartflows' ),
					),
				),
			),
			'wcf-enable-product-quantity'   => array(
				'type'  => 'checkbox',
				'label' => __( 'Enable Quantity', 'cartflows' ),
			),

		);
	}

	/**
	 * Get dummy settings for product options.
	 */
	public function get_dummy_settings_for_coupon() {
		return array(
			'coupon'     => array(
				'type'        => 'coupon',
				'label'       => __( 'Select Coupon', 'cartflows' ),
				'placeholder' => __( 'Type to search for a coupon...', 'cartflows' ),
				'multiple'    => false,
				'allow_clear' => true,
			),
			'coupon-doc' => array(
				'type'    => 'doc',
				/* translators: %1$1s: link html start, %2$12: link html end*/
				'content' => sprintf( __( 'For more information about the CartFlows coupon please %1$1s Click here.%2$2s', 'cartflows' ), '<a href="https://cartflows.com/docs/enable-coupons-on-cartflows-page/" target="_blank">', '</a>' ),
			),
		);
	}

	/**
	 * Get page settings.
	 *
	 * @param int $step_id Step ID.
	 */
	public function get_page_settings( $step_id ) {

		$options = $this->get_data( $step_id );

		$settings = array(
			'settings' => array(
				'product'         => array(
					'title'    => __( 'Product', 'cartflows' ),
					'priority' => 20,
					'fields'   => array(
						'wcf-checkout-products' => array(
							'type'                   => 'product-repeater',
							'fieldtype'              => 'product',
							'name'                   => 'wcf-checkout-products',
							'value'                  => array(),
							'label'                  => __( 'Select Product', 'cartflows' ),
							'placeholder'            => __( 'Search for a product...', 'cartflows' ),
							'multiple'               => false,
							'allow_clear'            => true,
							'allowed_product_types'  => array(),
							'excluded_product_types' => array( 'grouped' ),
							'include_product_types'  => array( 'braintree-subscription, braintree-variable-subscription' ),
						),
						'checkout-product-doc'  => array(
							'type'    => 'doc',
							/* translators: %1$1s: link html start, %2$12: link html end*/
							'content' => sprintf( __( 'For more information about the checkout product settings please %1$1s Click here.%2$2s', 'cartflows' ), '<a href="https://cartflows.com/docs/set-product-quantity-and-discount/" target="_blank">', '</a>' ),
						),
					),
				),

				'coupon'          => array(
					'title'    => __( 'Coupon', 'cartflows' ),
					'priority' => 30,
					'fields'   => _is_cartflows_pro() && 'Activated' === _is_cartflows_pro_license_activated() ? '' : $this->get_dummy_settings_for_coupon(),

				),
				// Product Options.
				'product-options' => array(
					'title'    => __( 'Product Options', 'cartflows' ),
					'priority' => 40,
					'fields'   => _is_cartflows_pro() && 'Activated' === _is_cartflows_pro_license_activated() ? '' : $this->get_dummy_settings_for_product_options(),
				),

				// checkout offer.
				'checkout-offer'  => array(
					'title'    => __( 'Checkout Offer', 'cartflows' ),
					'priority' => 60,
					'fields'   => ! _is_cartflows_pro() ? array(
						'checkout-offer' => array(
							'type'    => 'pro-notice',
							'feature' => 'Checkout Offer',
						),
					)
						: '',
				),

			),
		);

		// @todo Remove this code after v1.7.4 update.
		// Start.

		// Order Bump.
		$settings['settings']['order-bump'] = array(
			'title'    => __( 'Order Bump', 'cartflows' ),
			'priority' => 50,
			'fields'   => ! _is_cartflows_pro() ? array(
				'order-Bump' => array(
					'type'    => 'pro-notice',
					'feature' => 'Order Bump',
				),
			)

				: '',
		);

		// End.

		$settings = apply_filters( 'cartflows_admin_checkout_meta_fields', $settings, $step_id, $options );

		return $settings;
	}
	/**
	 * Get settings data.
	 *
	 * @param  int $step_id Post ID.
	 */
	public function get_settings_fields( $step_id ) {

		$options = $this->get_data( $step_id );

		$autoaddress_setting_url = admin_url( 'admin.php?page=' . CARTFLOWS_SLUG . '&path=settings#auto_fields' );

		$settings = array(
			'settings' => array(
				'shortcodes'        => array(
					'title'    => __( 'Shortcodes', 'cartflows' ),
					'slug'     => 'shortcodes',
					'priority' => 30,
					'fields'   => array(
						'checkout-shortcode' => array(
							'type'     => 'text',
							'name'     => 'checkout-shortcode',
							'label'    => __( 'CartFlows Checkout', 'cartflows' ),
							'value'    => '[cartflows_checkout]',
							'help'     => __( 'Add this shortcode to your checkout page', 'cartflows' ),
							'readonly' => true,
						),
					),
				),
				'general'           => array(
					'title'    => __( 'General', 'cartflows' ),
					'slug'     => 'general',
					'priority' => 20,
					'fields'   => array(
						'slug'      => array(
							'type'  => 'text',
							'name'  => 'post_name',
							'label' => __( 'Step Slug', 'cartflows' ),
							'value' => get_post_field( 'post_name', $step_id ),
						),
						'step-note' => array(
							'type'  => 'textarea',
							'name'  => 'wcf-step-note',
							'label' => __( 'Step Note', 'cartflows' ),
							'value' => get_post_meta( $step_id, 'wcf-step-note', true ),
							'rows'  => 2,
							'cols'  => 38,
						),
					),
				),

				// Checkout Fields.
				'checkout-settings' => array(
					'title'    => __( 'Checkout Settings', 'cartflows' ),
					'slug'     => 'checkout_setting',
					'priority' => 10,
					'fields'   => array(
						'checkout-strings-heading'       => array(
							'type'  => 'heading',
							'label' => esc_html__( 'Checkout Texts', 'cartflows' ),
						),

						'wcf-billing-details-text'       => array(
							'type'        => 'text',
							'label'       => __( 'Billing Details', 'cartflows' ),
							'name'        => 'wcf-checkout-billing-details-text',
							'placeholder' => __( 'Billing details', 'cartflows' ),
						),
						'wcf-shipping-details-text'      => array(
							'type'        => 'text',
							'label'       => __( 'Shipping Details', 'cartflows' ),
							'name'        => 'wcf-checkout-shipping-details-text',
							'placeholder' => __( 'Ship to a different address?', 'cartflows' ),
						),
						'wcf-your-order-text'            => array(
							'type'        => 'text',
							'label'       => __( 'Your Order', 'cartflows' ),
							'name'        => 'wcf-checkout-your-order-text',
							'placeholder' => __( 'Your order', 'cartflows' ),
						),
						'wcf-customer-info-text'         => array(
							'type'        => 'text',
							'label'       => __( 'Customer Information', 'cartflows' ),
							'name'        => 'wcf-checkout-customer-info-text',
							'placeholder' => __( 'Customer information', 'cartflows' ),
							'tooltip'     => __( 'This heading will be displayed only on modern checkout style.', 'cartflows' ),
						),
						'wcf-payment-text'               => array(
							'type'        => 'text',
							'label'       => __( 'Payment', 'cartflows' ),
							'name'        => 'wcf-checkout-payment-text',
							'placeholder' => __( 'Payment', 'cartflows' ),
							'tooltip'     => __( 'This heading will be displayed only on modern checkout style.', 'cartflows' ),
						),
						'wcf-enable-validation-text'     => array(
							'type'    => 'checkbox',
							'label'   => __( 'Enable Field validation error message', 'cartflows' ),
							'name'    => 'wcf-enable-checkout-field-validation-text',
							'value'   => $options['wcf-enable-checkout-field-validation-text'],
							'tooltip' => __( 'This is the error message appended to the field name to form a error message.', 'cartflows' ),
						),
						'wcf-field-validation-text'      => array(
							'type'        => 'text',
							'label'       => __( 'Validation error message', 'cartflows' ),
							'name'        => 'wcf-checkout-field-validation-text',
							'placeholder' => __( 'is required', 'cartflows' ),
							'conditions'  => array(
								'fields' => array(
									array(
										'name'     => 'wcf-enable-checkout-field-validation-text',
										'operator' => '===',
										'value'    => 'yes',
									),
								),
							),
						),
						'checkout-button-heading'        => array(
							'type'  => 'heading',
							'label' => esc_html__( 'Place Order Button', 'cartflows' ),
						),
						'wcf-place-order-button-text'    => array(
							'type'        => 'text',
							'label'       => __( 'Place Order Button Text', 'cartflows' ),
							'name'        => 'wcf-checkout-place-order-button-text',
							'value'       => $options['wcf-checkout-place-order-button-text'],
							'placeholder' => __( 'Place Order', 'cartflows' ),
							'tooltip'     => __( 'It will change the Place Order Button text on checkout page.', 'cartflows' ),
						),

						'wcf-place-order-button-icon'    => array(
							'type'    => 'checkbox',
							'label'   => __( 'Enable Lock Icon', 'cartflows' ),
							'name'    => 'wcf-checkout-place-order-button-lock',
							'value'   => $options['wcf-checkout-place-order-button-lock'],
							'tooltip' => __( 'This will show the lock icon on the place order button on checkout page.', 'cartflows' ),
						),

						'wcf-place-order-price-display'  => array(
							'type'    => 'checkbox',
							'label'   => __( 'Enable Price Display', 'cartflows' ),
							'name'    => 'wcf-checkout-place-order-button-price-display',
							'value'   => $options['wcf-checkout-place-order-button-price-display'],
							'tooltip' => __( 'This will show the cart total from the place order button.', 'cartflows' ),
						),

						'checkout-other-heading'         => array(
							'type'  => 'heading',
							'label' => __( 'Others', 'cartflows' ),
						),

						'wcf-show-prod-img-order-review' => array(
							'type'    => 'checkbox',
							'label'   => __( 'Display product images', 'cartflows' ),
							'name'    => 'wcf-order-review-show-product-images',
							'value'   => $options['wcf-order-review-show-product-images'],
							'tooltip' => __( 'Enabling this option will display the product\'s images in the order review section.', 'cartflows' ),
						),

						'wcf-edit-cart'                  => array(
							'type'    => 'checkbox',
							'label'   => __( 'Enable cart editing on checkout', 'cartflows' ),
							'name'    => 'wcf-remove-product-field',
							'value'   => $options['wcf-remove-product-field'],
							'tooltip' => __( 'Users will able to remove products from the checkout page.', 'cartflows' ),
						),

						'wcf-google-autoaddress'         => array(
							'type'  => 'checkbox',
							'label' => __( 'Enable Google Address Autocomplete', 'cartflows' ),
							'name'  => 'wcf-google-autoaddress',
							/* translators: %1$s: link html start, %2$s: link html end*/
							'desc'  => sprintf( __( 'Before enabling this option, make sure that you have added API key in %1$1sGoogle Address Autocomplete Settings.%1$1s', 'cartflows' ), '<a href="' . $autoaddress_setting_url . '" target="_blank">', '</a>' ),
						),

						'wcf-animate-browser-tab'        => ! _is_cartflows_pro() ? array(
							'type'    => 'pro-notice',
							'feature' => 'Animate Browser Tab',
						) : '',

					),
				),

				'custom-scripts'    => array(
					'title'    => __( 'Custom Script', 'cartflows' ),
					'slug'     => 'custom_script',
					'priority' => 40,
					'fields'   => array(

						'wcf-checkout-custom-script' => array(
							'type'  => 'textarea',
							'label' => __( 'Custom Script', 'cartflows' ),
							'name'  => 'wcf-custom-script',
							'value' => $options['wcf-custom-script'],
						),
					),
				),
			),
		);

		return apply_filters( 'cartflows_admin_checkout_settings_fields', $settings );
	}

	/**
	 * Get data.
	 *
	 * @param  int $step_id Post ID.
	 */
	public function get_data( $step_id ) {

		$optin_data = array();

		// Stored data.
		$stored_meta = get_post_meta( $step_id );

		// Default.
		$default_data = self::get_meta_option( $step_id );

		// Set stored and override defaults.
		foreach ( $default_data as $key => $value ) {
			if ( array_key_exists( $key, $stored_meta ) ) {
				$optin_data[ $key ] = ( isset( $stored_meta[ $key ][0] ) ) ? maybe_unserialize( $stored_meta[ $key ][0] ) : '';
			} else {
				$optin_data[ $key ] = ( isset( $default_data[ $key ]['default'] ) ) ? $default_data[ $key ]['default'] : '';
			}
		}

		return $optin_data;

	}

	/**
	 * Get meta.
	 *
	 * @param int $post_id Post ID.
	 */
	public static function get_meta_option( $post_id ) {

		$meta_option = wcf()->options->get_checkout_fields( $post_id );

		return $meta_option;

	}

	/**
	 * Get name.
	 *
	 * @param int $id Product ID.
	 */
	public static function get_name( $id ) {

		$product_object = wc_get_product( $id );

		$formatted_name = '';

		if ( is_object( $formatted_name ) ) {
			$formatted_name = rawurldecode( $product_object->get_formatted_name() );
		}
		return $formatted_name;

	}

		/**
		 * Add custom meta fields
		 *
		 * @param int $post_id post id.
		 */
	public function custom_fields_data( $post_id ) {

		$billing_fields  = $this->get_field_settings( $post_id, 'billing', '' );
		$shipping_fields = $this->get_field_settings( $post_id, 'shipping', '' );

		$custom_fields = array(
			'extra_fields'    => array(
				'fields' => array(

					'enable-coupon-field'       => array(
						'type'  => 'checkbox',
						'label' => __( 'Enable Coupon Field', 'cartflows' ),
						'name'  => 'wcf-show-coupon-field',
					),
					'collapse-coupon-field'     => array(
						'type'        => 'checkbox',
						'label'       => __( 'Collapsible Coupon Field', 'cartflows' ),
						'name'        => 'wcf-optimize-coupon-field',
						'child_class' => 'wcf-cfe-child',
						'conditions'  => array(
							'fields' => array(
								array(
									'name'     => 'wcf-show-coupon-field',
									'operator' => '===',
									'value'    => 'yes',
								),
							),
						),
					),
					'enable-additional-field'   => array(
						'type'  => 'checkbox',
						'label' => __( 'Enable Additional Field', 'cartflows' ),
						'name'  => 'wcf-checkout-additional-fields',
					),
					'collapse-additional-field' => array(
						'type'        => 'checkbox',
						'label'       => __( 'Collapsible Additional Field', 'cartflows' ),
						'name'        => 'wcf-optimize-order-note-field',
						'child_class' => 'wcf-cfe-child',
						'conditions'  => array(
							'fields' => array(
								array(
									'name'     => 'wcf-checkout-additional-fields',
									'operator' => '===',
									'value'    => 'yes',
								),
							),
						),
					),
					'shipping-field'            => array(
						'type'  => 'checkbox',
						'label' => __( 'Enable Ship To Different Address', 'cartflows' ),
						'name'  => 'wcf-shipto-diff-addr-fields',
					),
					'custom-field-editor'       => array(
						'type'  => 'checkbox',
						'label' => __( 'Enable Custom Field Editor', 'cartflows' ),
						'name'  => 'wcf-custom-checkout-fields',
					),
				),
			),
			'billing_fields'  => array(
				'fields' => $billing_fields,
			),
			'shipping_fields' => array(
				'fields' => $shipping_fields,
			),
		);

		$custom_fields = apply_filters( 'cartflows_admin_checkout_editor_settings', $custom_fields );

		return $custom_fields;
	}

		/**
		 * Add custom meta fields
		 *
		 * @param string $post_id post id.
		 * @param array  $fields fields.
		 * @param array  $new_fields new fields.
		 */
	public function get_field_settings( $post_id, $fields, $new_fields ) {

		if ( 'billing' === $fields ) {
			$get_ordered_fields = wcf()->options->get_checkout_meta_value( $post_id, 'wcf_field_order_billing' );
		} else {
			$get_ordered_fields = wcf()->options->get_checkout_meta_value( $post_id, 'wcf_field_order_shipping' );
		}

		if ( isset( $get_ordered_fields ) && ! empty( $get_ordered_fields ) ) {
			$data_array = $get_ordered_fields;

		} else {
			$data_array = Cartflows_Helper::get_checkout_fields( $fields, $post_id );
		}

		if ( isset( $new_fields ) && ! empty( $new_fields ) && is_array( $new_fields ) ) {
			$data_array = $new_fields;
		}
		$field_args = array();

		foreach ( $data_array as $key => $value ) {

			$field_args = $this->prepare_field_arguments( $key, $value, $post_id, $fields );

			foreach ( $field_args as $arg_key => $arg_val ) {

				if ( ! in_array( $arg_key, $value, true ) ) {

					$data_array[ $key ][ $arg_key ] = $arg_val;
				}
			}

			if ( 'billing' === $fields ) {
				$name = 'wcf_field_order_billing[' . $key . ']';
			} else {
				$name = 'wcf_field_order_shipping[' . $key . ']';
			}

			$is_checkbox     = false;
			$is_require      = false;
			$is_radiobutton  = false;
			$is_select       = false;
			$display         = 'none';
			$is_custom_field = false;

			$type = $field_args['type'];

			if ( 'checkbox' == $field_args['type'] ) {
				$is_checkbox = true;
			}

			if ( 'yes' == $field_args['required'] ) {
				$is_require = true;
			}

			if ( 'yes' == $field_args['optimized'] ) {
				$is_optimized = true;
			}

			if ( 'select' == $field_args['type'] ) {
				$is_select = true;
				$display   = 'block';
			}

			if ( 'radio' == $field_args['type'] ) {
				$is_radiobutton = true;
			}

			if ( isset( $field_args['custom'] ) && true === (bool) $field_args['custom'] ) {
				$is_custom_field = true;
			}

			$data_array[ $key ]['field_options'] = array(
				'enable-field'  => array(
					'type'  => 'checkbox',
					'label' => __( 'Enable Field', 'cartflows' ),
					'name'  => $name . '[enabled]',
					'value' => $field_args['enabled'],
				),
				'select-width'  => array(
					'type'    => 'select',
					'label'   => __( 'Field Width', 'cartflows' ),
					'name'    => $name . '[width]',
					'value'   => $field_args['width'],
					'options' => array(
						array(
							'value' => '33',
							'label' => esc_html__( '33%', 'cartflows' ),
						),
						array(
							'value' => '50',
							'label' => esc_html__( '50%', 'cartflows' ),
						),
						array(
							'value' => '100',
							'label' => esc_html__( '100%', 'cartflows' ),
						),
					),

				),
				'field-label'   => array(
					'type'  => 'text',
					'label' => __( 'Field Label', 'cartflows' ),
					'name'  => $name . '[label]',
					'value' => $field_args['label'],
				),
				'field-name'    => array(
					'type'     => 'text',
					'label'    => __( 'Field ID', 'cartflows' ),
					'name'     => $name . '[key]',
					'value'    => $field_args['key'],
					'readonly' => true,
					'tooltip'  => __( 'Copy this field id to use in Order Custom Field rule of dynamic offers.', 'cartflows' ),
				),

				'field-default' => $is_checkbox ?
					array(
						'type'    => 'select',
						'label'   => __( 'Default', 'cartflows' ),
						'name'    => $name . '[default]',
						'value'   => $field_args['default'],
						'options' => array(
							array(
								'value' => '1',
								'label' => esc_html__( 'Checked', 'cartflows' ),
							),
							array(
								'value' => '0',
								'label' => esc_html__( 'Un-Checked', 'cartflows' ),
							),
						),
					) :

					array(
						'type'  => 'text',
						'label' => __( 'Default', 'cartflows' ),
						'name'  => $name . '[default]',
						'value' => $field_args['default'],
					),
			);

			if ( $is_select || $is_radiobutton ) {

				$data_array[ $key ]['field_options']['select-options'] = array(
					'type'  => 'text',
					'label' => __( 'Options', 'cartflows' ),
					'name'  => $name . '[options]',
					'value' => $field_args['options'],
				);
			}

			if ( ! in_array( $type, array( 'checkbox', 'select', 'radio' ), true ) ) {
				$data_array[ $key ]['field_options']['field-placeholder'] = array(
					'type'  => 'text',
					'label' => __( 'Placeholder', 'cartflows' ),
					'name'  => $name . '[placeholder]',
					'value' => $field_args['placeholder'],
				);
			}

			if ( $is_custom_field ) {
				$data_array[ $key ]['field_options']['show-in-email'] = array(
					'type'  => 'checkbox',
					'label' => __( 'Show In Email', 'cartflows' ),
					'name'  => $name . '[show_in_email]',
					'value' => $field_args['show_in_email'],
				);
			}

			$data_array[ $key ]['field_options']['required-field'] = array(
				'type'  => 'checkbox',
				'label' => __( 'Required', 'cartflows' ),
				'name'  => $name . '[required]',
				'value' => $field_args['required'],
			);

			$data_array[ $key ]['field_options']['collapsed-field'] = array(
				'type'  => 'checkbox',
				'label' => __( 'Collapsible', 'cartflows' ),
				'name'  => $name . '[optimized]',
				'value' => $field_args['optimized'],
			);
		}

		return $data_array;
	}

		/**
		 * Fetch default width of checkout fields by key.
		 *
		 * @param string $checkout_field_key field key.
		 * @return int
		 */
	public function get_default_checkout_field_width( $checkout_field_key ) {

		$default_width = 100;
		switch ( $checkout_field_key ) {
			case 'billing_first_name':
			case 'billing_last_name':
			case 'billing_address_1':
			case 'billing_address_2':
			case 'shipping_first_name':
			case 'shipping_last_name':
			case 'shipping_address_1':
			case 'shipping_address_2':
				$default_width = 50;
				break;

			case 'billing_city':
			case 'billing_state':
			case 'billing_postcode':
			case 'shipping_city':
			case 'shipping_state':
			case 'shipping_postcode':
				$default_width = 33;
				break;

			default:
				$default_width = 100;
				break;
		}

		return $default_width;
	}


	/**
	 * Prepare HTML data for billing and shipping fields.
	 *
	 * @param string  $field checkout field key.
	 * @param array   $field_data checkout field object.
	 * @param integer $post_id chcekout post id.
	 * @param string  $type checkout field type.
	 * @return array
	 */
	public function prepare_field_arguments( $field, $field_data, $post_id, $type ) {

		if ( isset( $field_data['label'] ) ) {
			$field_name = $field_data['label'];
		} elseif ( 'shipping_address_2' == $field || 'billing_address_2' == $field ) {
			$field_name = 'Street address line 2';
		}

		if ( isset( $field_data['width'] ) ) {
			$width = $field_data['width'];
		} else {
			$width = get_post_meta( $post_id, 'wcf-field-width_' . $field, true );
			if ( ! $width ) {
				$width = $this->get_default_checkout_field_width( $field );
			}
		}

		if ( isset( $field_data['enabled'] ) ) {
			$is_enabled = true === $field_data['enabled'] ? 'yes' : 'no';
		} else {
			$is_enabled = get_post_meta( $post_id, 'wcf-' . $field, true );

			if ( '' === $is_enabled ) {
				$is_enabled = 'yes';
			}
		}

		$field_args = array(
			'type'          => ( isset( $field_data['type'] ) && ! empty( $field_data['type'] ) ) ? $field_data['type'] : '',
			'label'         => $field_name,
			'key'           => $field,
			'name'          => 'wcf-' . $field,
			'placeholder'   => isset( $field_data['placeholder'] ) ? $field_data['placeholder'] : '',
			'width'         => $width,
			'enabled'       => $is_enabled,
			'after'         => 'Enable',
			'custom'        => isset( $field_data['custom'] ) ? $field_data['custom'] : false,
			'section'       => $type,
			'default'       => isset( $field_data['default'] ) ? $field_data['default'] : '',
			'show_in_email' => ( isset( $field_data['show_in_email'] ) && true == $field_data['show_in_email'] ) ? 'yes' : 'no',
			'required'      => ( isset( $field_data['required'] ) && true == $field_data['required'] ) ? 'yes' : 'no',
			'optimized'     => ( isset( $field_data['optimized'] ) && true == $field_data['optimized'] ) ? 'yes' : 'no',
			'options'       => ( isset( $field_data['options'] ) && ! empty( $field_data['options'] ) ) ? implode( '|', $field_data['options'] ) : '',
		);

		if ( 'shipping' === $type ) {
			if ( isset( $field_data['custom'] ) && $field_data['custom'] ) {
				$field_args['after_html']  = '<span class="wcf-cpf-actions" data-type="shipping" data-key="' . $field . '"> | ';
				$field_args['after_html'] .= '<a class="wcf-pro-custom-field-remove"><span class="dashicons dashicons-trash"></span></a>';
				$field_args['after_html'] .= '</span>';
			}
		}

		if ( 'billing' === $type ) {
			if ( isset( $field_data['custom'] ) && $field_data['custom'] ) {
				$field_args['after_html']  = '<span class="wcf-cpf-actions" data-type="billing" data-key="' . $field . '">';
				$field_args['after_html'] .= '<a class="wcf-pro-custom-field-remove wp-ui-text-notification">' . __( 'Remove', 'cartflows' ) . '</a>';
				$field_args['after_html'] .= '</span>';
			}
		}

		return $field_args;
	}


}

/**
 * Kicking this off by calling 'get_instance()' method.
 */
Cartflows_Checkout_Meta_Data::get_instance();

