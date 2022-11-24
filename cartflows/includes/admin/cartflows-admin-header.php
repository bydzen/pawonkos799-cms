<?php
/**
 * CartFlows Admin Header while displaying log.
 *
 * @package CartFlows
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wcf-menu-page-header">
	<div class="wcf-container wcf-flex">
		<div class="wcf-title">
			<span class="screen-reader-text"><?php echo esc_attr( CARTFLOWS_NAME ); ?></span>
			<img class="wcf-logo" alt="" src="<?php echo esc_attr( CARTFLOWS_URL ) . 'assets/images/cartflows-logo.svg'; ?>" />
		</div>
		<div class="wcf-top-links">
		<a target="_blank" class="wcf-top-links__item" title="Knowledge Base" href="//cartflows.com/docs/"><span class="dashicons dashicons-book"></span></a>
		<a target="_blank" class="wcf-top-links__item" title="Community" href="//youtube.com/c/CartFlows/"><span class="dashicons dashicons-youtube"></span></a>
		<a target="_blank" class="wcf-top-links__item" title="Support" href="//cartflows.com/contact/"><span class="dashicons dashicons-sos"></span></a>
		</div>
	</div>
</div>
