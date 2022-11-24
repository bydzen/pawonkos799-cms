<?php
/**
 * Elementor Editor Compatibility.
 *
 * @package CartFlows
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Checkout Markup
 *
 * @since 1.0.0
 */
class Cartflows_Elementor_Editor {

	/**
	 * Member Variable
	 *
	 * @var object instance
	 */
	private static $instance;

	/**
	 *  Initiator
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 *  Constructor
	 */
	public function __construct() {

		$this->elementor_editor_compatibility();
	}

	/**
	 * Elementor editor compatibility.
	 */
	public function elementor_editor_compatibility() {

		if ( ! empty( $_REQUEST['action'] ) && is_admin() ) { //phpcs:ignore

			$current_post_id = false;
			$elementor_ajax  = false;

			if ( 'elementor' === $_REQUEST['action'] && isset( $_GET['post'] ) && ! empty( $_GET['post'] ) ) { //phpcs:ignore
				$current_post_id   = intval( $_GET['post'] ); //phpcs:ignore
			}

			if ( wp_doing_ajax() && 'elementor_ajax' === $_REQUEST['action'] && isset( $_REQUEST['editor_post_id'] ) && ! empty( $_REQUEST['editor_post_id'] ) ) { //phpcs:ignore
				$current_post_id = intval( $_REQUEST['editor_post_id'] ); //phpcs:ignore
				$elementor_ajax  = true;
			}

			if ( $current_post_id ) { //phpcs:ignore

				$current_post_type = get_post_type( $current_post_id );

				if ( wcf()->utils->is_step_post_type( $current_post_type ) ) {

					$cf_frontend = Cartflows_Frontend::get_instance();

					/* Load woo templates from plugin */
					add_filter( 'woocommerce_locate_template', array( $cf_frontend, 'override_woo_template' ), 20, 3 );

					do_action( 'cartflows_elementor_editor_compatibility', $current_post_id, $elementor_ajax );
				}
			}

			/* Compatibility without condition, just to add actions */
			if ( 'elementor' === $_REQUEST['action'] || 'elementor_ajax' === $_REQUEST['action'] ) { //phpcs:ignore

				add_action( 'cartflows_elementor_before_checkout_shortcode', array( $this, 'before_checkout_shortcode_actions' ) );
				add_action( 'cartflows_elementor_before_optin_shortcode', array( $this, 'before_optin_shortcode_actions' ) );

				/* Thank you filters */
				add_filter( 'cartflows_show_demo_order_details', '__return_true' );
			}
		}
	}

	/**
	 * Before checkout shortcode actions.
	 *
	 * @param int $checkout_id checkout id.
	 */
	public function before_checkout_shortcode_actions( $checkout_id ) {

		// Added to modify the fields labels and placeholders to display it in the preview mode.
		Cartflows_Checkout_Fields::get_instance()->checkout_field_actions();

		do_action( 'cartflows_checkout_before_shortcode', $checkout_id );
	}

	/**
	 * Before optin shortcode actions.
	 *
	 * @param int $checkout_id checkout id.
	 */
	public function before_optin_shortcode_actions( $checkout_id ) {

		do_action( 'cartflows_optin_before_shortcode', $checkout_id );
	}
}

/**
 *  Kicking this off by calling 'get_instance()' method
 */
Cartflows_Elementor_Editor::get_instance();
