<?php
/**
 * CartFlows Flows ajax actions.
 *
 * @package CartFlows
 */

namespace CartflowsAdmin\AdminCore\Ajax;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use CartflowsAdmin\AdminCore\Ajax\AjaxBase;
use CartflowsAdmin\AdminCore\Inc\AdminHelper;

/**
 * Class Flows.
 */
class HomePage extends AjaxBase {

	/**
	 * Instance
	 *
	 * @access private
	 * @var object Class object.
	 * @since 1.0.0
	 */
	private static $instance;

	/**
	 * Initiator
	 *
	 * @since 1.0.0
	 * @return object initialized object of class.
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Register_ajax_events.
	 *
	 * @return void
	 */
	public function register_ajax_events() {

		$ajax_events = array(
			'hide_welcome_page_boxes',
			'get_welcome_box_option',
		);

		$this->init_ajax_events( $ajax_events );
	}

	/**
	 * Save settings.
	 *
	 * @return void
	 */
	public function get_welcome_box_option() {

		$response_data = array( 'messsage' => $this->get_error_msg( 'permission' ) );

		if ( ! current_user_can( 'cartflows_manage_flows_steps' ) ) {
			wp_send_json_error( $response_data );
		}

		/**
		 * Nonce verification
		 */
		if ( ! check_ajax_referer( 'cartflows_get_welcome_box_option', 'security', false ) ) {
			$response_data = array( 'messsage' => $this->get_error_msg( 'nonce' ) );
			wp_send_json_error( $response_data );
		}

		if ( empty( $_POST ) ) {
			$response_data = array( 'messsage' => __( 'No post data found!', 'cartflows' ) );
			wp_send_json_error( $response_data );
		}

		if ( isset( $_POST ) ) {

			$setting_tab = isset( $_POST['wcf_box_id'] ) ? sanitize_text_field( wp_unslash( $_POST['wcf_box_id'] ) ) : '';

			$boxes_settings = AdminHelper::get_admin_settings_option( $setting_tab );

			$response_data = array(
				'is_hidden' => $boxes_settings,
			);
		}

		wp_send_json_success( $response_data );
	}

	/**
	 * Save settings.
	 *
	 * @return void
	 */
	public function hide_welcome_page_boxes() {

		$response_data = array( 'messsage' => $this->get_error_msg( 'permission' ) );

		if ( ! current_user_can( 'cartflows_manage_flows_steps' ) ) {
			wp_send_json_error( $response_data );
		}

		/**
		 * Nonce verification
		 */
		if ( ! check_ajax_referer( 'cartflows_hide_welcome_page_boxes', 'security', false ) ) {
			$response_data = array( 'messsage' => $this->get_error_msg( 'nonce' ) );
			wp_send_json_error( $response_data );
		}

		if ( empty( $_POST ) ) {
			$response_data = array( 'messsage' => __( 'No post data found!', 'cartflows' ) );
			wp_send_json_error( $response_data );
		}

		if ( isset( $_POST ) ) {

			$setting_tab = json_decode( isset( $_POST['cartflows_box_to_hide'] ) ? sanitize_text_field( wp_unslash( $_POST['cartflows_box_to_hide'] ) ) : '', true );

			switch ( $setting_tab['id'] ) {

				case 'welcome_box':
					$this->hide_show_home_boxes();
					break;

				default:
					$this->hide_show_home_boxes();

			}
		}

		$response_data = array(
			'messsage'  => __( 'Successfully hidden!', 'cartflows' ),
			'is_hidden' => 'yes',
		);
		wp_send_json_success( $response_data );
	}

	/**
	 * Save settings.
	 *
	 * @return void
	 */
	public function hide_show_home_boxes() {

		$new_settings = array();

		if ( isset( $_POST['cartflows_box_to_hide'] ) ) { //phpcs:ignore
			// Loop through the input and sanitize each of the values.
			$new_settings = $this->sanitize_form_inputs( json_decode( wp_unslash( $_POST['cartflows_box_to_hide'] ), true ) ); //phpcs:ignore
		}

		$this->update_admin_settings_option( 'cartflows_hide_welcome_box', $new_settings['hide'], false );
	}



	/**
	 * Update admin settings.
	 *
	 * @param string $key key.
	 * @param bool   $value key.
	 * @param bool   $network network.
	 */
	public function update_admin_settings_option( $key, $value, $network = false ) {

		// Update the site-wide option since we're in the network admin.
		if ( $network && is_multisite() ) {
			update_site_option( $key, $value );
		} else {
			update_option( $key, $value );
		}

	}

	/**
	 * Save settings.
	 *
	 * @param array $input_settings settimg data.
	 */
	public function sanitize_form_inputs( $input_settings = array() ) {
		$new_settings = array();
		foreach ( $input_settings as $key => $val ) {

			if ( is_array( $val ) ) {
				foreach ( $val as $k => $v ) {
					$new_settings[ $key ][ $k ] = ( isset( $val[ $k ] ) ) ? sanitize_text_field( $v ) : '';
				}
			} else {
				$new_settings[ $key ] = ( isset( $input_settings[ $key ] ) ) ? sanitize_text_field( $val ) : '';
			}
		}
		return $new_settings;
	}
}
