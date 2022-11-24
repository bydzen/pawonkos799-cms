<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Widgets loader for Cartflows.
 *
 * @package Cartflows
 * */

defined( 'ABSPATH' ) || exit;

/**
 * Set up Widgets Loader class
 */
class Cartflows_Widgets_Loader {

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
	 * Setup actions and filters.
	 *
	 * @since 1.6.15
	 */
	private function __construct() {

		// Register category.
		add_action( 'elementor/elements/categories_registered', array( $this, 'register_widget_category' ) );

		// Register widgets.
		add_action( 'elementor/widgets/widgets_registered', array( $this, 'register_widgets' ) );

		add_action( 'elementor/editor/after_enqueue_styles', array( $this, 'admin_enqueue_styles' ) );
	}

	/**
	 * Enqueue admin scripts
	 *
	 * @since 1.6.15
	 * @param string $hook Current page hook.
	 * @access public
	 */
	public function admin_enqueue_styles( $hook ) {

		// Register the icons styles.
		wp_register_style(
			'cartflows-elementor-icons-style',
			CARTFLOWS_URL . 'assets/elementor-assets/css/style.css',
			array(),
			CARTFLOWS_VER
		);

		wp_enqueue_style( 'cartflows-elementor-icons-style' );
	}

	/**
	 * Returns Script array.
	 *
	 * @return array()
	 * @since 1.6.15
	 */
	public static function get_widget_list() {

		$widget_list = array(
			'checkout-form',
			'order-details-form',
			'next-step-button',
			'optin-form',
		);

		return $widget_list;
	}


	/**
	 * Include Widgets files
	 *
	 * Load widgets files
	 *
	 * @since 1.6.15
	 * @access public
	 */
	public function include_widgets_files() {

		/* Required files */
		require_once CARTFLOWS_DIR . 'modules/elementor/classes/class-cartflows-elementor-editor.php';

		$widget_list = $this->get_widget_list();

		if ( ! empty( $widget_list ) ) {
			foreach ( $widget_list as $handle => $data ) {
				$file_path = CARTFLOWS_DIR . 'modules/elementor/widgets/class-cartflows-el-' . $data . '.php';
				if ( file_exists( $file_path ) ) {
					require_once $file_path;
				}
			}
		}

		// Emqueue the widgets style.
		wp_enqueue_style( 'cartflows-elementor-style', CARTFLOWS_URL . 'modules/elementor/widgets-css/frontend.css', array(), CARTFLOWS_VER );

	}

	/**
	 * Register Category
	 *
	 * @since 1.6.15
	 * @param object $this_cat class.
	 */
	public function register_widget_category( $this_cat ) {
		$category = __( 'Cartflows', 'cartflows' );

		$this_cat->add_category(
			'cartflows-widgets',
			array(
				'title' => $category,
				'icon'  => 'eicon-font',
			)
		);

		return $this_cat;
	}

	/**
	 * Register Widgets
	 *
	 * Register new Elementor widgets.
	 *
	 * @since 1.6.15
	 * @access public
	 */
	public function register_widgets() {

		global $post;

		if ( ! isset( $post ) ) {
			return;
		}

		$post_type = $post->post_type;

		$step_type = get_post_meta( $post->ID, 'wcf-step-type', true );

		if ( 'cartflows_step' === $post_type && class_exists( '\Elementor\Plugin' ) ) {

			$widget_manager = \Elementor\Plugin::$instance->widgets_manager;

			$widget_list = $this->get_widget_list();

			// Its is now safe to include Widgets files.
			$this->include_widgets_files();

			foreach ( $widget_list as $widget ) {

				$widget_name = str_replace( '-', ' ', $widget );

				$class_name = 'Cartflows_' . str_replace( ' ', '_', ucwords( $widget_name ) );

				if ( $class_name::is_enable( $step_type ) ) {
					$widget_manager->register_widget_type( new $class_name() );
				}
			}
		}
	}

}

/**
 * Initiate the class.
 */
Cartflows_Widgets_Loader::get_instance();
