<?php
/**
 * CartFlows Mobile Order Review Table for Modern Checkout.
 *
 * @package cartflows
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$is_coupon_enabled = Cartflows_Checkout_Markup::get_instance()->is_custom_coupon_field_enabled();
$visibility_class  = '';

// Add display class only if the filter is true. By defauled summary box should be closed.
if ( ! apply_filters( 'cartflows_show_mobile_order_summary_collapsed', true ) ) {
	$visibility_class = 'wcf-show';
}

?>

<!-- Mobile responsive order review template -->
<div class="wcf-collapsed-order-review-section <?php echo $visibility_class; ?>">
	<div class='wcf-order-review-toggle'>
		<div class='wcf-order-review-toggle-button-wrap'>
			<span class='wcf-order-review-toggle-text'><?php echo Cartflows_Checkout_Markup::get_instance()->get_order_review_toggle_texts(); ?></span>
			<span class='wcf-order-review-toggle-button dashicons dashicons-arrow-down-alt2'></span>
			<span class='wcf-order-review-toggle-button dashicons dashicons-arrow-up-alt2'></span>
		</div>
		<div class='wcf-order-review-total'><?php echo wp_strip_all_tags( WC()->cart->get_total() ); ?></div>
	</div>

	<div class="wcf-cartflows-review-order-wrapper">
		<?php Cartflows_Checkout_Markup::get_instance()->wcf_order_review(); ?>

		<?php if ( $is_coupon_enabled ) : ?>
			<!-- Order review coupon field -->
			<div class="wcf-custom-coupon-field" id="wcf_custom_coupon_field_order_review">
				<div class="wcf-coupon-col-1">
					<span>
						<input type="text" name="coupon_code" class="input-text wcf-coupon-code-input" placeholder="<?php esc_attr_e( 'Coupon Code', 'cartflows' ); ?>" id="order_review_coupon_code" value="">
					</span>
				</div>
				<div class="wcf-coupon-col-2">
					<span>
						<button type="button" class="button wcf-submit-coupon wcf-btn-small" name="apply_coupon" value="Apply"><?php esc_html_e( 'Apply', 'cartflows' ); ?></button>
					</span>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>
<?php
