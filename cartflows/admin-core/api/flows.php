<?php
/**
 * CartFlows Flows Query.
 *
 * @package CartFlows
 */

namespace CartflowsAdmin\AdminCore\Api;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use CartflowsAdmin\AdminCore\Api\ApiBase;

/**
 * Class Admin_Query.
 */
class Flows extends ApiBase {

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = '/admin/flows/';

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
	 * Init Hooks.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function register_routes() {

		// Eg. http://example.local/wp-json/cartflows/v1/admin/flows/.
		$namespace = $this->get_api_namespace();

		register_rest_route(
			$namespace,
			$this->rest_base,
			array(
				array(
					'methods'             => 'POST', // WP_REST_Server::READABLE.
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
					'args'                => array(), // get_collection_params may use.
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Get items
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response
	 */
	public function get_items( $request ) {

		$post_status = 'any';
		$post_count  = 10;

		$args = array(
			'post_type'   => CARTFLOWS_FLOW_POST_TYPE,
			'post_status' => $post_status,
			'orderby'     => 'ID',
		);

		// checking if store checkout is available and removing it from the list of flows.
		$store_checkout_id = intval( \Cartflows_Helper::get_global_setting( '_cartflows_store_checkout' ) );
		if ( 0 !== $store_checkout_id ) {
			$args['post__not_in'] = array( $store_checkout_id );
		}

		if ( isset( $_REQUEST['paged'] ) ) { //phpcs:ignore
			$args['paged'] = absint( $_REQUEST['paged'] ); //phpcs:ignore
		}

		if ( 'any' === $post_status ) {

			if ( isset( $_REQUEST['s'] ) ) { //phpcs:ignore
				$args['s'] = sanitize_text_field( $_REQUEST['s'] ); //phpcs:ignore
			}

			if ( isset( $_REQUEST['post_status'] ) ) { //phpcs:ignore
				if ( 'active' === $_REQUEST['post_status'] ) { //phpcs:ignore
					$args['post_status'] = 'publish';
				} elseif ( 'inactive' === $_REQUEST['post_status'] ) { // phpcs:ignore
					$args['post_status'] = 'draft';
				} else {
					$args['post_status'] = sanitize_text_field( wp_unslash( $_REQUEST['post_status'] ) ); // phpcs:ignore
				}
			}
		}

		if ( ! empty( $post_count ) ) {
			$args['posts_per_page'] = $post_count;
		}

		$result = new \WP_Query( $args );

		$data = array(
			'items'      => array(),
			'pagination' => array(),
		);

		if ( $result->have_posts() ) {
			while ( $result->have_posts() ) {
				$result->the_post();

				global $post;

				$post_data = (array) $post;

				// Modify the date Format just to display it.
				$post_data['post_modified'] = date_format( date_create( $post_data['post_modified'] ), 'yy/m/d' );
				$post_data['post_status']   = ucwords( $post_data['post_status'] );

				$view   = get_permalink( $post->ID );
				$edit   = admin_url( 'admin.php?page=cartflows&action=wcf-edit-flow&flow_id=' . $post->ID );
				$delete = '#';
				$clone  = '#';
				$export = '#';

				$post_data['actions'] = array(
					'view'      => array(
						'action' => 'edit',
						'class'  => '',
						'attr'   => array( 'target' => '_blank' ),
						'text'   => __( 'View', 'cartflows' ),
						'link'   => $view,

					),
					'edit'      => array(
						'action' => 'edit',
						'class'  => '',
						'attr'   => array(),
						'text'   => __( 'Edit', 'cartflows' ),
						'link'   => $edit,

					),
					'duplicate' => array(
						'action' => 'clone',
						'attr'   => array(),
						'class'  => '',
						'text'   => __( 'Clone', 'cartflows' ),
						'link'   => $clone,
					),
					'export'    => array(
						'action' => 'export',
						'attr'   => array(),
						'class'  => '',
						'text'   => __( 'Export', 'cartflows' ),
						'link'   => $export,
					),
					'delete'    => array(
						'action' => 'delete',
						'attr'   => array(),
						'class'  => '',
						'text'   => __( 'Delete', 'cartflows' ),
						'link'   => $delete,
					),
				);

				$data['items'][] = $post_data;
			}
		}

		$data['found_posts'] = $result->found_posts;
		$data['post_status'] = isset( $post_data['post_status'] ) ? $post_data['post_status'] : $args['post_status'];

		$data['active_flows_count'] = intval( wp_count_posts( CARTFLOWS_FLOW_POST_TYPE )->publish );
		$data['trash_flows_count']  = intval( wp_count_posts( CARTFLOWS_FLOW_POST_TYPE )->trash );
		$data['draft_flows_count']  = intval( wp_count_posts( CARTFLOWS_FLOW_POST_TYPE )->draft );

		$data['pagination'] = array(
			'found_posts' => $result->found_posts,
			'paged'       => $result->query['paged'],
			'max_pages'   => $result->max_num_pages,
		);

		// Reducing count of active_flows_count if store checkout is set.
		if ( 0 !== $store_checkout_id ) {
			$data['active_flows_count']--;
		}

		wp_reset_postdata();

		$data['status'] = true;

		if ( ! $result->have_posts() ) {
			$data['status'] = false;
			$response       = new \WP_REST_Response( $data );
			$response->set_status( 200 );
			return $response;
		}

		$response = new \WP_REST_Response( $data );
		$response->set_status( 200 );

		return $response;
	}

	/**
	 * Check whether a given request has permission to read notes.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function get_items_permissions_check( $request ) {

		if ( ! current_user_can( 'cartflows_manage_flows_steps' ) ) {
			return new \WP_Error( 'cartflows_rest_cannot_view', __( 'Sorry, you cannot list resources.', 'cartflows' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}
}
