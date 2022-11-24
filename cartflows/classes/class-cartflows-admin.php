<?php
/**
 * CartFlows Admin.
 *
 * @package CartFlows
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Class Cartflows_Admin.
 */
class Cartflows_Admin {

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
	 * Constructor
	 */
	public function __construct() {

		$this->init_hooks();
	}

	/**
	 * Init Hooks.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function init_hooks() {

		if ( ! is_admin() ) {
			return;
		}

		/* Add lite version class to body */
		add_action( 'admin_body_class', array( $this, 'add_admin_body_class' ) );

		add_filter( 'plugin_action_links_' . CARTFLOWS_BASE, array( $this, 'add_action_links' ) );

		add_action( 'in_admin_header', array( $this, 'embed_page_header' ) );

		add_action( 'admin_init', array( $this, 'flush_rules_after_save_permalinks' ) );

		add_filter( 'post_row_actions', array( $this, 'remove_flow_actions' ), 99, 2 );

		add_action( 'wp_dashboard_setup', array( $this, 'dashboard_widget' ) );
	}

	/**
	 * Init dashboard widgets.
	 */
	public function dashboard_widget() {

		if ( current_user_can( 'manage_options' ) && '1' === get_option( 'wcf_setup_skipped', false ) && '1' !== get_option( 'wcf_setup_complete', false ) ) {
			wp_add_dashboard_widget(
				'cartFlows_setup_dashboard_widget',
				__( 'CartFlows Setup', 'cartflows' ),
				array( $this, 'status_widget' )
			);
		}
	}

	/**
	 * Show status widget.
	 */
	public function status_widget() {

		$admin_url = admin_url( 'index.php' ) . '?page=cartflow-setup&';

		$steps = array(
			'welcome'         => $admin_url . 'step=welcome',
			'page-builder'    => $admin_url . 'step=page-builder',
			'plugin-install'  => $admin_url . 'step=plugin-install',
			'global-checkout' => $admin_url . 'step=global-checkout',
			'optin'           => $admin_url . 'step=optin',
			'ready'           => $admin_url . 'step=ready',
		);

		$exit_step = get_option( 'wcf_exit_setup_step', 'welcome' );

		$incompleted_steps = array_search( $exit_step, array_keys( $steps ), true );
		$remianing_steps   = array_slice( $steps, 0, $incompleted_steps );

		$completed_tasks_count = count( $remianing_steps ) + 1;
		$tasks_count           = count( $steps );
		$button_link           = ! empty( $exit_step ) ? $steps[ $exit_step ] : '';

		$progress_percentage = ( $completed_tasks_count / $tasks_count ) * 100;
		$circle_r            = 6.5;
		$circle_dashoffset   = ( ( 100 - $progress_percentage ) / 100 ) * ( pi() * ( $circle_r * 2 ) );

		wp_enqueue_style( 'cartflows-dashboard-widget', CARTFLOWS_URL . 'admin/assets/css/admin-widget.css', array(), CARTFLOWS_VER );

		?>

		<div class="cartflows-dashboard-widget-finish-setup">
			<span class='progress-wrapper'>
				<svg class="circle-progress" width="17" height="17" version="1.1" xmlns="http://www.w3.org/2000/svg">
				<circle r="6.5" cx="10" cy="10" fill="transparent" stroke-dasharray="40.859" stroke-dashoffset="0"></circle>
				<circle class="bar" r="6.5" cx="190" cy="10" fill="transparent" stroke-dasharray="40.859" stroke-dashoffset="<?php echo esc_attr( $circle_dashoffset ); ?>" transform='rotate(-90 100 100)'></circle>
				</svg>
				<span><?php echo esc_html_e( 'Step', 'cartflows' ); ?> <?php echo esc_html( $completed_tasks_count ); ?> <?php echo esc_html_e( 'of', 'cartflows' ); ?> <?php echo esc_html( $tasks_count ); ?></span>
			</span>

			<div class="description">
				<div class="wcf-left-column">
					<p>
						<?php echo esc_html_e( 'You\'re almost there! Once you complete CartFlows setup you can start receiving orders from flows.', 'cartflows' ); ?>
					</p>
					<a href='<?php echo esc_attr( $button_link ); ?>' class='button button-primary'><?php echo esc_html_e( 'Complete Setup', 'cartflows' ); ?></a>
				</div>
				<div class="wcf-right-column">
					<img src="<?php echo CARTFLOWS_URL; ?>admin/assets/images/cartflows-home-widget.svg" />
				</div>
			</div>
		</div>
		<?php

	}

	/**
	 * Add the clone link to action list for flows row actions
	 *
	 * @param array  $actions Actions array.
	 * @param object $post Post object.
	 *
	 * @return array
	 */
	public function remove_flow_actions( $actions, $post ) {

		if ( current_user_can( 'edit_posts' ) && isset( $post ) && CARTFLOWS_FLOW_POST_TYPE === $post->post_type ) {

			if ( isset( $actions['duplicate'] ) ) { // Duplicate page plugin remove.
				unset( $actions['duplicate'] );
			}

			if ( isset( $actions['edit_as_new_draft'] ) ) { // Duplicate post plugin remove.
				unset( $actions['edit_as_new_draft'] );
			}
		}

		return $actions;
	}

	/**
	 *  After save of permalinks.
	 */
	public static function flush_rules_after_save_permalinks() {

		$has_saved_permalinks = get_option( 'cartflows_permalink_refresh' );
		if ( $has_saved_permalinks ) {
			flush_rewrite_rules();
			delete_option( 'cartflows_permalink_refresh' );
		}

	}

	/**
	 * Show action on plugin page.
	 *
	 * @param  array $links links.
	 * @return array
	 */
	public function add_action_links( $links ) {

		$default_url = add_query_arg(
			array(
				'page' => CARTFLOWS_SLUG,
				'path' => 'settings',
			),
			admin_url()
		);

		$mylinks = array(
			'<a href="' . $default_url . '">' . __( 'Settings', 'cartflows' ) . '</a>',
			'<a target="_blank" href="' . esc_url( 'https://cartflows.com/docs' ) . '">' . __( 'Docs', 'cartflows' ) . '</a>',
		);

		if ( ! _is_cartflows_pro() ) {
			array_push( $mylinks, '<a style="color: #39b54a; font-weight: 700;" target="_blank" href="' . esc_url( 'https://cartflows.com/pricing/' ) . '"> Go Pro </a>' );
		}

		return array_merge( $links, $mylinks );
	}

	/**
	 * Check is flow admin.
	 *
	 * @since 1.0.0
	 * @return boolean
	 */
	public static function is_flow_edit_admin() {

		$current_screen = get_current_screen();

		if (
			is_object( $current_screen ) &&
			isset( $current_screen->post_type ) &&
			( CARTFLOWS_FLOW_POST_TYPE === $current_screen->post_type ) &&
			isset( $current_screen->base ) &&
			( 'post' === $current_screen->base )
		) {
			return true;
		}
		return false;
	}

	/**
	 * Admin body classes.
	 *
	 * Body classes to be added to <body> tag in admin page
	 *
	 * @param String $classes body classes returned from the filter.
	 * @return String body classes to be added to <body> tag in admin page
	 */
	public function add_admin_body_class( $classes ) {

		$classes .= ' cartflows-' . CARTFLOWS_VER;

		if ( isset( $_GET['action'] ) && in_array( sanitize_text_field( wp_unslash( $_GET['action'] ) ), array( 'wcf-log', 'wcf-license' ) ) ) { //phpcs:ignore
			$classes .= ' wcf-debug-page ';
		}

		return $classes;
	}

	/**
	 * Set up a div for the header embed to render into.
	 * The initial contents here are meant as a place loader for when the PHP page initialy loads.
	 */
	public function embed_page_header() {

		if ( ! $this->show_embed_header() ) {
			return;
		}

		wp_enqueue_style( 'cartflows-admin-embed-header', CARTFLOWS_URL . 'admin/assets/css/admin-embed-header.css', array(), CARTFLOWS_VER );

		include_once CARTFLOWS_DIR . 'includes/admin/cartflows-admin-header.php';
	}

	/**
	 * Show embed header.
	 *
	 * @since 1.0.0
	 */
	public function show_embed_header() {

		$current_screen = get_current_screen();

		if (
			is_object( $current_screen ) &&
			isset( $current_screen->post_type ) &&
			( CARTFLOWS_FLOW_POST_TYPE === $current_screen->post_type ) &&
			isset( $current_screen->base ) &&
			( 'post' === $current_screen->base || 'edit' === $current_screen->base )
		) {
			return true;
		}

		return false;
	}

	/**
	 * Check allowed screen for notices.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function allowed_screen_for_notices() {

		$screen          = get_current_screen();
		$screen_id       = $screen ? $screen->id : '';
		$allowed_screens = array(
			'cartflows_page_cartflows_settings',
			'edit-cartflows_flow',
			'dashboard',
			'plugins',
		);

		if ( in_array( $screen_id, $allowed_screens, true ) ) {
			return true;
		}

		return false;
	}
}

Cartflows_Admin::get_instance();
