<?php
/**
 * View flow inner step
 *
 * @package CartFlows
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$inner_step_title = get_the_title( $inner_step_id );
$note             = get_post_meta( $inner_step_id, 'wcf-step-note', true );
?>
<div class="wcf-step">
	<div class="wcf-step-left-content">
		<span class="dashicons dashicons-menu"></span>
		<span title="<?php echo esc_attr( $inner_step_title ); ?>"><?php echo wp_trim_words( $inner_step_title, 3 ); ?></span>
		<?php
		if ( $ab_test && ! empty( $note ) ) {
			?>
			<span class="dashicons dashicons-editor-help" id="wcf-tooltip">
				<span class="wcf-ab-test-note-badge"><?php echo $note; ?></span>
			</span>
			<?php
		}


		?>
		<?php
		if ( $ab_test_ui ) {
			if ( $control_id === $inner_step_id ) {
				?>
				<span class="wcf-step-badge wcf-abtest-control-badge"><?php esc_html_e( 'Control', 'cartflows' ); ?></span>
			<?php } else { ?>
				<span class="wcf-step-badge wcf-abtest-variation-badge"><?php /* translators: %s badge count */ echo esc_html( sprintf( __( 'Variation-%s', 'cartflows' ), ++$var_badge_count ) ); ?></span>
				<?php
			}
		}
		?>

		<span class="wcf-flow-badge"><?php echo esc_html( $term_name ); ?></span>

		<?php

		if ( ( ! $has_product_assigned ) && ( $control_id !== $is_global_checkout ) ) {
			?>
			<span class="wcf-no-product-badge"><?php esc_html_e( 'No Product Assigned', 'cartflows' ); ?></span>
			<?php
		} elseif ( ( $has_product_assigned ) && ( $control_id === $is_global_checkout ) ) {
			?>
			<span class="wcf-global-checkout-badge wcf-error-badge"><?php esc_html_e( 'Store Checkout - Remove selected checkout product', 'cartflows' ); ?></span>
			<?php
		} elseif ( ( ! $has_product_assigned ) && $control_id === $is_global_checkout ) {
			?>
			<span class="wcf-global-checkout-badge"><?php esc_html_e( 'Store Checkout', 'cartflows' ); ?></span>
			<?php
		}

		?>

		<?php if ( $control_id === $inner_step_id ) { ?>
			<input type="hidden" class="wcf-steps-hidden" name="wcf-steps[]" value="<?php echo $inner_step_id; ?>">
		<?php } ?>
		<?php do_action( 'cartflows_step_left_content', $inner_step_id, $term_slug ); ?>
	</div>

<!-- popup for setting -->
		<div id="wcf-edit-dropdown" class="wcf-edit-content" >


			<?php foreach ( $action_buttons as $action_slug => $action_data ) { ?>
				<?php

				if ( 'view' === $action_slug || 'edit' === $action_slug ) {
					continue;
				}

				if ( $ab_test_ui ) {

					if ( 'ab-test' === $action_slug ) {
						continue;
					}
				} else {

					if ( isset( $action_data['ab-test'] ) ) {
						continue;
					}
				}


				$action_attr = ' ';
				if ( isset( $action_data['attr'] ) && is_array( $action_data['attr'] ) ) {
					foreach ( $action_data['attr'] as $attr_key => $attr_value ) {
						$action_attr .= $attr_key . '="' . $attr_value . '"';
					}
				}
				$style = '';
				if ( ! $action_data['show'] ) {
					$style                  = 'opacity: 0.65; cursor: not-allowed;';
					$action_data['label']  .= ' ( Pro )';
					$action_data['tooltip'] = 'Upgrade to Pro for this feature.';
					$action_data['class']  .= ' wcf-pro';
				}
				?>
			<a href="<?php echo $action_data['link']; ?>" style ="<?php echo $style; ?>" class="<?php echo $action_data['class']; ?>" title="<?php echo $action_data['tooltip']; ?>" <?php echo $action_attr; ?>>
				<span class="dashicons <?php echo $action_data['icon']; ?>"></span>
				<span class="wcf-step-act-btn-text"><?php echo $action_data['label']; ?></span>
			</a>
			<?php } ?>

		</div>

<!-- popup for setting -->

	<div class="wcf-steps-action-buttons">
		<div class="wcf-basic-action-buttons">
		<?php
		foreach ( $action_buttons as $action_slug => $action_data ) {
			if ( 'view' === $action_slug || 'edit' === $action_slug ) {
				?>
					<a href="<?php echo $action_data['link']; ?>" class="<?php echo $action_data['class']; ?>" title="<?php echo $action_data['tooltip']; ?>" <?php echo $action_attr; ?>>
						<span class="dashicons <?php echo $action_data['icon']; ?>"></span>
						<span class="wcf-step-act-btn-text"><?php echo $action_data['label']; ?></span>
					</a>
				<?php
			}
		}
		?>
		</div>
		<div class="wcf-edit-settings"></div>
	</div>
</div>
