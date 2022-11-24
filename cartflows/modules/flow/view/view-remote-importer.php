<?php
/**
 * View Remote importer popup
 *
 * @package CartFlows
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div id="wcf-remote-step-importer" class="wcf-templates-popup-overlay">
	<div class="wcf-templates-popup-content">
		<div class="spinner"></div>
		<div class="wcf-templates-wrap wcf-templates-wrap-flows">

			<div id="wcf-remote-step-actions" class="wcf-template-header">
				<div class="wcf-template-logo-wrap">
					<span class="wcf-cartflows-logo-img">
						<span class="cartflows-logo-icon"></span>
					</span>
					<span class="wcf-cartflows-title"><?php esc_html_e( 'Steps Library', 'cartflows' ); ?></span>
				</div>
				<div class="wcf-tab-wrapper">
					<?php if ( 'other' !== $default_page_builder ) { ?>
						<div id="wcf-get-started-steps">
							<ul class="filter-links ">
								<li>
									<a href="#" class="current" data-slug="ready-templates" data-title="<?php esc_html_e( 'Ready Templates', 'cartflows' ); ?>"><?php esc_html_e( 'Ready Templates', 'cartflows' ); ?></a>
								</li>
								<li>
									<a href="#" data-slug="canvas" data-title="<?php esc_html_e( 'Create Your Own', 'cartflows' ); ?>"><?php esc_html_e( 'Create Your Own', 'cartflows' ); ?></a>
								</li>
							</ul>
						</div>
					<?php } ?>
				</div>
				<div class="wcf-popup-close-wrap">
					<span class="close-icon"><span class="wcf-cartflow-icons dashicons dashicons-no"></span></span>
				</div>
			</div>

			<!--<div class="wcf-search-form">
				<label class="screen-reader-text" for="wp-filter-search-input"><?php esc_html_e( 'Search Sites', 'cartflows' ); ?> </label>
				<input placeholder="<?php esc_html_e( 'Search Flow...', 'cartflows' ); ?>" type="text" aria-describedby="live-search-desc" class="wcf-flow-search-input">
			</div>-->

			<div id="wcf-remote-content">
				<?php if ( 'other' !== $default_page_builder ) { ?>
					<div id="wcf-ready-templates">
						<div id="wcf-remote-filters">
							<div id="wcf-page-builders"></div>
							<div id="wcf-categories"></div>
						</div>
						<div class="wcf-page-builder-notice"></div>
						<div id="wcf-remote-step-list" class="wcf-remote-list wcf-template-list-wrap"><span class="spinner is-active"></span></div>
						<div id="wcf-upcoming-page-builders" style="display: none;" class="wcf-remote-list wcf-template-list-wrap"></div>
					</div>
				<?php } ?>
				<div id="wcf-start-from-scratch" style="<?php echo ( 'other' !== $default_page_builder ) ? 'display: none;' : ''; ?>">
					<div class="inner">
						<div id="wcf-scratch-steps-categories">
							<select class="step-type-filter-links filter-links">
								<option value="" class="current"><?php echo esc_html__( 'Select Step Type', 'cartflows' ); ?></option>

								<?php foreach ( $steps as $key => $value ) { ?>
									<option class="<?php echo $key; ?>" data-slug="<?php echo $key; ?>" data-title="<?php echo $key; ?>"><?php echo $value; ?></option>
								<?php } ?>
							</select>
						</div>
						<a href="#" class="button button-primary cartflows-step-import-blank"><?php esc_html_e( 'Create Step', 'cartflows' ); ?></a>
						<?php if ( ! _is_cartflows_pro() ) { ?>
						<div class="wcf-template-notice"><p><?php echo esc_html__( 'You need a Cartflows Pro version to import Upsell / Downsell', 'cartflows' ); ?></p></div>
						<?php } ?>
						<p class="wcf-learn-how"><a href="https://cartflows.com/docs/cartflows-step-types/" target="_blank"><?php esc_html_e( 'Learn How', 'cartflows' ); ?> <i class="dashicons dashicons-external"></i></a></p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div><!-- .wcf-templates-popup-overlay -->
