<?php
/**
 * CartFlows Debug Log HTML.
 *
 * @package CARTFLOWS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * File.
 *
 * @file
 * Header file.
 */
require_once CARTFLOWS_DIR . 'includes/admin/cartflows-admin-header.php';
?>

<div class="wcf-debug-page-content wcf-clear">
	<div class="wcf-log__section wcf-debug-log__section">
	<?php if ( $logs ) : ?>
		<div class="log-viewer-select">
			<div class="alignrleft">
				<h2 class="log-viewer--title">CartFlows Logs</h2>
			</div>
			<div class="alignright">
				<form action="<?php	echo $form_url; ?>" method="post">
					<select name="log_file" class="wcf-log--select">
						<?php foreach ( $logs as $log_key => $log_file ) : ?>
							<?php
							$timestamp = filemtime( CARTFLOWS_LOG_DIR . $log_file );
							/* translators: %1$s: timestamp1, %2$s: timestamp2 */
							$date      = sprintf( __( '%1$s at %2$s', 'cartflows' ), date_i18n( 'F j, Y', $timestamp ), date_i18n( 'g:i a', $timestamp ) ); // phpcs:ignore
							?>
							<option value="<?php echo esc_attr( $log_key ); ?>" <?php selected( $viewed_log, $log_key ); ?>><?php echo esc_html( $log_file ); ?> (<?php echo esc_html( $date ); ?>)</option>
						<?php endforeach; ?>
					</select>
					<button type="submit" class="button wcf-view-log--button" value="<?php esc_attr_e( 'View', 'cartflows' ); ?>"><?php esc_html_e( 'View', 'cartflows' ); ?></button>
				</form>
			</div>
		</div>
		<div id="log-viewer">
			<div class="wcf-log-container">
				<pre id="wcf-log--text"><?php echo esc_html( file_get_contents( CARTFLOWS_LOG_DIR . $viewed_log_file ) );//phpcs:ignore ?></pre>
			</div>
			<?php if ( ! empty( $viewed_log_file ) ) : ?>
				<div class="wcf-log__actions">
					<a class="wcf-log--copy" href="#!" data-default="<?php esc_html_e( 'Copy log', 'cartflows' ); ?>" data-success="<?php esc_html_e( 'Copied to clipboard', 'cartflows' ); ?>"><?php esc_html_e( 'Copy log', 'cartflows' ); ?></a>
					<span>|</span>
					<a class="wcf-log--download" href="
					<?php
					echo esc_url(
						wp_nonce_url(
							add_query_arg(
								array(
									'handle'     => $viewed_log_file,
									'tab'        => 'logs',
									'btn_action' => 'download-log',
								)
							),
							'download_log'
						)
					);
					?>
					" target="_blank"><?php esc_html_e( 'Download log', 'cartflows' ); ?></a>
					<span>|</span>
					<a class="wcf-log--delete" onclick="return confirm('Are you sure to delete this log?');" href="
					<?php
					echo esc_url(
						wp_nonce_url(
							add_query_arg(
								array(
									'handle'     => $viewed_log_file,
									'tab'        => 'logs',
									'btn_action' => 'remove-log',
								)
							),
							'remove_log'
						)
					);
					?>
					"><?php esc_html_e( 'Delete log', 'cartflows' ); ?></a>
				</div>
			<?php endif; ?>

		</div>
	<?php else : ?>
		<div class="updated woocommerce-message inline"><p><?php esc_html_e( 'There are currently no logs to view.', 'cartflows' ); ?></p></div>
	<?php endif; ?>

	</div>
</div>
