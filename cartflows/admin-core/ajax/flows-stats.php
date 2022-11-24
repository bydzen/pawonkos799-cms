<?php
/**
 * CartFlows Flows Stats ajax actions.
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
class FlowsStats extends AjaxBase {

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
	 * Register ajax events.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function register_ajax_events() {

		$ajax_events = array(
			'get_all_flows_stats',
		);

		$this->init_ajax_events( $ajax_events );
	}


	/**
	 * Get all Flows Stats.
	 */
	public function get_all_flows_stats() {

		$response_data = array( 'messsage' => $this->get_error_msg( 'permission' ) );

		if ( ! current_user_can( 'cartflows_manage_flows_steps' ) ) {
			wp_send_json_error( $response_data );
		}

		/**
		 * Nonce verification
		 */
		if ( ! check_ajax_referer( 'cartflows_get_all_flows_stats', 'security', false ) ) {
			$response_data = array( 'messsage' => $this->get_error_msg( 'nonce' ) );
			wp_send_json_error( $response_data );
		}

		$start_date = filter_input( INPUT_POST, 'date_from', FILTER_SANITIZE_STRING );
		$end_date   = filter_input( INPUT_POST, 'date_to', FILTER_SANITIZE_STRING );

		$total_flow_revenue = AdminHelper::get_earnings( $start_date, $end_date );

		$response = array(
			'flow_stats' => $total_flow_revenue,
		);

		wp_send_json_success( $response );

	}
}
