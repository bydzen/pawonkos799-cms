<?php
/**
 * CartFlows Admin Helper.
 *
 * @package CartFlows
 */

namespace CartflowsAdmin\Wizard\Inc;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class AdminHelper.
 */
class WizardHelper {

	/**
	 * Update admin settings.
	 *
	 * @param string $key key.
	 * @param bool   $value key.
	 * @param bool   $network network.
	 */
	public static function update_settings_options( $key, $value, $network = false ) {

		// Update the site-wide option since we're in the network admin.
		if ( $network && is_multisite() ) {
			update_site_option( $key, $value );
		} else {
			update_option( $key, $value );
		}

	}
}

