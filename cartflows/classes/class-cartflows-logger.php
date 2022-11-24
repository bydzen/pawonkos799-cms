<?php
/**
 * Logger.
 *
 * @package CartFlows
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Initialization
 *
 * @since 1.0.0
 */
class Cartflows_Logger {


	/**
	 * Member Variable
	 *
	 * @var instance
	 */
	private static $instance;

	/**
	 * Member Variable
	 *
	 * @var logger
	 */
	public $logger;

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

		/* Load WC Logger */
		add_action( 'init', array( $this, 'init_wc_logger' ), 99 );
	}

	/**
	 * Init Logger.
	 *
	 * @since 1.0.0
	 */
	public function init_wc_logger() {
		if ( class_exists( 'CartFlows_WC_Logger' ) ) {
			$this->logger = new CartFlows_WC_Logger();
		}
	}

	/**
	 * Enable log.
	 *
	 * @since 1.7.2
	 */
	public function is_log_enable() {
		return apply_filters( 'cartflows_enable_log', 'enable' );
	}

	/**
	 * Write log
	 *
	 * @param string $message log message.
	 * @param string $level type of log.
	 * @since 1.0.0
	 */
	public function log( $message, $level = 'info' ) {

		if ( 'enable' === $this->is_log_enable() &&
			is_a( $this->logger, 'CartFlows_WC_Logger' ) &&
			did_action( 'plugins_loaded' )
		) {

			$this->logger->log( $level, $message, array( 'source' => 'cartflows' ) );
		}
	}

	/**
	 * Write log
	 *
	 * @param string $message log message.
	 * @param string $level type of log.
	 * @since 1.0.0
	 */
	public function import_log( $message, $level = 'info' ) {

		if ( 'enable' === $this->is_log_enable() && defined( 'WP_DEBUG' ) &&
			WP_DEBUG &&
			is_a( $this->logger, 'CartFlows_WC_Logger' ) &&
			did_action( 'plugins_loaded' )
		) {

			$this->logger->log( $level, $message, array( 'source' => 'cartflows-import' ) );
		}
	}

	/**
	 * Migration log
	 *
	 * @param string $message migration message.
	 * @param string $level type of log.
	 * @since 1.7.0
	 */
	public function migration_log( $message, $level = 'info' ) {

		if ( 'enable' === $this->is_log_enable() && defined( 'WP_DEBUG' ) &&
			WP_DEBUG &&
			is_a( $this->logger, 'CartFlows_WC_Logger' ) &&
			did_action( 'plugins_loaded' )
		) {

			$this->logger->log( $level, $message, array( 'source' => 'cartflows-migration' ) );
		}
	}

	/**
	 * Sync log
	 *
	 * @param string $message log message.
	 * @param string $level type of log.
	 * @since 1.0.0
	 */
	public function sync_log( $message, $level = 'info' ) {

		if ( 'enable' === $this->is_log_enable() && defined( 'WP_DEBUG' ) &&
			WP_DEBUG &&
			is_a( $this->logger, 'CartFlows_WC_Logger' ) &&
			did_action( 'plugins_loaded' )
		) {

			$this->logger->log( $level, $message, array( 'source' => 'cartflows-sync' ) );
		}
	}
}

/**
 *  Kicking this off by calling 'get_instance()' method
 */
Cartflows_Logger::get_instance();
