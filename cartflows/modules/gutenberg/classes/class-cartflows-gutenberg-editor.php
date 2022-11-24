<?php
/**
 * Gutenburg Editor Compatibility.
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
 * @since 1.6.15
 */
class Cartflows_Gutenberg_Editor {

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

		add_action( 'admin_init', array( $this, 'gutenberg_editor_compatibility' ) );

	}

	/**
	 * Gutenburg editor compatibility.
	 */
	public function gutenberg_editor_compatibility() {

		if ( is_admin() && isset( $_REQUEST['action'] ) ) { //phpcs:ignore

			$current_post_id = false;

			if ( 'edit' === $_REQUEST['action'] && isset( $_GET['post'] ) ) { //phpcs:ignore
				$current_post_id = intval( $_GET['post'] ); //phpcs:ignore
			} elseif ( isset( $_REQUEST['cartflows_gb'] ) && isset( $_POST['id'] ) ){ //phpcs:ignore
				$current_post_id = intval( $_POST['id'] ); //phpcs:ignore
			}

			if ( $current_post_id ) {

				$current_post_type = get_post_type( $current_post_id );

				if ( wcf()->utils->is_step_post_type( $current_post_type ) ) {

					if ( wcf()->is_woo_active ) {

						$this->maybe_init_cart();

						/* Load woo templates from plugin */
						$cf_frontend = Cartflows_Frontend::get_instance();
						add_filter( 'woocommerce_locate_template', array( $cf_frontend, 'override_woo_template' ), 20, 3 );

						add_action( 'cartflows_gutenberg_before_checkout_shortcode', array( $this, 'before_gb_checkout_shortcode_actions' ) );

						add_action( 'cartflows_gutenberg_before_optin_shortcode', array( $this, 'before_gb_optin_shortcode_actions' ) );

					}

					do_action( 'cartflows_gutenberg_editor_compatibility', $current_post_id );
				}
			}
		}
	}

	/**
	 * Before checkout shortcode actions.
	 */
	public function maybe_init_cart() {

		wc()->frontend_includes();

		$has_cart = is_a( WC()->cart, 'WC_Cart' );

		if ( ! $has_cart ) {
			$session_class = apply_filters( 'woocommerce_session_handler', 'WC_Session_Handler' );
			WC()->session  = new $session_class();
			WC()->session->init();
			WC()->cart     = new \WC_Cart();
			WC()->customer = new \WC_Customer( get_current_user_id(), true );

		}

		/* For preview */
		add_filter( 'woocommerce_checkout_redirect_empty_cart', '__return_false' );
	}

	/**
	 * Before checkout shortcode actions.
	 *
	 * @param int $checkout_id checkout id.
	 */
	public function before_gb_checkout_shortcode_actions( $checkout_id ) {

		// Added to modify the fields labels and placeholders to display it in the preview mode.
		Cartflows_Checkout_Fields::get_instance()->checkout_field_actions();

		do_action( 'cartflows_checkout_before_shortcode', $checkout_id );
	}

	/**
	 * Before optin shortcode actions.
	 *
	 * @param int $checkout_id checkout id.
	 */
	public function before_gb_optin_shortcode_actions( $checkout_id ) {

		do_action( 'cartflows_optin_before_shortcode', $checkout_id );
	}
}

/**
 *  Kicking this off by calling 'get_instance()' method
 */
Cartflows_Gutenberg_Editor::get_instance();
