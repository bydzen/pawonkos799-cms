<?php
/**
 * View Flow steps
 *
 * @package CartFlows
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$default_page_builder = Cartflows_Helper::get_common_setting( 'default_page_builder' );

$steps = array(
	'landing'  => __( 'Landing', 'cartflows' ),
	'checkout' => __( 'Checkout (Woo)', 'cartflows' ),
	'thankyou' => __( 'Thank You (Woo)', 'cartflows' ),
	'upsell'   => __( 'Upsell (Woo)', 'cartflows' ),
	'downsell' => __( 'Downsell (Woo)', 'cartflows' ),
	'optin'    => __( 'Optin (Woo)', 'cartflows' ),
);
?>
<div class="wcf-flow-steps-meta-box">
	<div class="wcf-flow-settings">
		<?php do_action( 'cartflows_above_flow_steps' ); ?>
		<div class="wcf-flow-steps-wrap">
			<div class="wcf-flow-steps-container">
				<?php if ( is_array( $options['steps'] ) ) { ?>
					<?php foreach ( $options['steps'] as $index => $data ) { ?>
						<?php
						$term_slug            = '';
						$term_name            = '';
						$step_wrap_class      = '';
						$has_product_assigned = true;
						$is_global_checkout   = '';
						$common               = '';
						$data_step_id         = intval( $data['id'] );

						$control_id                  = $data_step_id;
						$note                        = '';
						$ab_test                     = false;
						$ab_test_ui                  = false;
						$ab_test_variations          = array();
						$ab_test_archived_variations = array();
						$ab_test_variations_count    = 0;
						$ab_test_args                = array();

						if ( _is_cartflows_pro() ) {

							$ab_test_ui                  = isset( $data['ab-test-ui'] ) && $data['ab-test-ui'] ? true : false;
							$ab_test                     = isset( $data['ab-test'] ) && $data['ab-test'] ? true : false;
							$ab_test_variations          = isset( $data['ab-test-variations'] ) ? $data['ab-test-variations'] : array();
							$ab_test_archived_variations = isset( $data['ab-test-archived-variations'] ) ? $data['ab-test-archived-variations'] : array();
							$ab_test_variations_count    = count( $ab_test_variations );

							if ( $ab_test_variations_count < 2 ) {
								$ab_test_ui = false;
							}
						}

						$ab_test_args = array(
							'control_id'                  => $data_step_id,
							'ab_test_variations'          => $ab_test_variations,
							'ab_test_archived_variations' => $ab_test_archived_variations,
							'ab_test_variations_count'    => $ab_test_variations_count,
						);

						if ( isset( $data['type'] ) ) {
							$term_slug = $data['type'];
							$term_name = $steps[ $data['type'] ];
						}

						if ( ! _is_cartflows_pro() && ( 'upsell' === $term_slug || 'downsell' === $term_slug ) ) {
							$step_wrap_class .= ' invalid-step';
						}

						if ( isset( $_GET['highlight-step-id'] ) ) { //phpcs:ignore

							$highlight_step_id = intval( $_GET['highlight-step-id'] ); //phpcs:ignore

							if ( $highlight_step_id === $data_step_id ) {
								$step_wrap_class .= ' wcf-new-step-highlight';
							}
						}

						if ( 'checkout' === $term_slug ) {

							$common = Cartflows_Helper::get_common_settings();

							$is_global_checkout = (int) $common['global_checkout'];

							if ( $data['id'] === $is_global_checkout ) {
								$step_wrap_class .= ' wcf-global-checkout';
							}
						}

						if ( 'upsell' === $term_slug || 'downsell' === $term_slug || 'checkout' === $term_slug ) {

							$has_product_assigned = Cartflows_Helper::has_product_assigned( $data['id'] );

							if ( ( ! $has_product_assigned ) && ( $data['id'] != $is_global_checkout ) ) {
								$step_wrap_class .= ' wcf-no-product-step';
							}
						}

						if ( $ab_test_ui ) {
							$step_wrap_class .= apply_filters( 'cartflows_ab_test_step_wrap_class', ' wcf-ab-test' );
						}

						?>
						<div class="wcf-step-wrap <?php echo $step_wrap_class; ?>" data-id="<?php echo $data['id']; ?>" data-term-slug="<?php echo esc_attr( $term_slug ); ?>">
							<?php
							do_action( 'cartflows_wcf_step_wrap_top', $data, $ab_test_ui, $ab_test, $ab_test_args );
							?>
							<?php

							if ( $ab_test_ui && is_array( $ab_test_variations ) && ! empty( $ab_test_variations ) ) {

								$var_badge_count = 0;

								foreach ( $ab_test_variations as $ab_test_variation ) {

									$inner_step_id  = $ab_test_variation['id'];
									$action_buttons = $this->get_step_action_buttons( $inner_step_id, $ab_test_ui, $ab_test_args );

									include CARTFLOWS_FLOW_DIR . 'view/view-flow-inner-step.php';
								}
							} else {

								$inner_step_id  = $data['id'];
								$action_buttons = $this->get_step_action_buttons( $inner_step_id, $ab_test_ui, $ab_test_args );

								include CARTFLOWS_FLOW_DIR . 'view/view-flow-inner-step.php';
							}

							do_action( 'cartflows_wcf_step_wrap_bottom', $data, $ab_test_ui, $ab_test, $ab_test_args );


							?>
						</div><!-- .wcf-step-wrap -->
					<?php } ?>
				<?php } ?>
			</div><!-- .wcf-flow-steps-container -->
		</div> <!-- .wcf-flow-steps-wrap -->
		<div class="wcf-flow-buttons-wrap"> <!-- .wcf-flow-buttons-wrap -->
			<?php do_action( 'cartflows_below_flow_steps' ); ?>
			<div class='wcf-add-new-step-btn-wrap'>
				<button class='wcf-trigger-popup button button-primary'>
					<?php echo esc_html__( 'Add New Step', 'cartflows' ); ?>
				</button>
			</div>
		</div><!-- .wcf-flow-buttons-wrap -->
	</div><!-- .wcf-flow-settings -->

	<?php

		require CARTFLOWS_FLOW_DIR . 'view/view-remote-importer.php';

		do_action( 'cartflows_after_flow_settings_meta' );
	?>
</div>
<?php
