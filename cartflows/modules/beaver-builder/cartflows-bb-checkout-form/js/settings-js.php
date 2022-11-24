(function($) {

	var $wrapper = $('.fl-node-<?php echo $cf_module->node; //phpcs:ignore ?> .cartflows-bb__checkout-form' );

	var $offer_wrap = $('body').find( '#wcf-pre-checkout-offer-modal' );

	var settings_data = $wrapper.data( 'settings-data' );

	var is_offer_enable = settings_data.enable_checkout_offer;
	var enable_product_options = settings_data.enable_product_options;
	<!--var enable_order_bump = settings_data.enable_order_bump;-->

	var form = $('.fl-builder-settings');

	var checkout_settings = {
		offer_title: [ settings_data.title_text, '.wcf-content-modal-title h1' ],
		offer_subtitle: [ settings_data.subtitle_text, '.wcf-content-modal-sub-title span' ],
		offer_product_name: [ settings_data.product_name, '.wcf-pre-checkout-offer-product-title h1' ],
		offer_product_desc: [ settings_data.product_desc, '.wcf-pre-checkout-offer-desc span' ],
		offer_accept_button : [ settings_data.accept_button_text, '.wcf-pre-checkout-offer-btn-action.wcf-pre-checkout-add-cart-btn button' ],
		offer_skip_button : [ settings_data.skip_button_text, '.wcf-pre-checkout-offer-btn-action.wcf-pre-checkout-skip-btn .wcf-pre-checkout-skip' ]
	};

	if( 'yes' === is_offer_enable ) {

		$.each( checkout_settings, function( key, value ) {

			var $control_name = value[0];
			var $selector = value[1];
			if( '' !== $control_name ) {
				$offer_wrap.find( $selector ).html( $control_name );
			}
		});

		form.find( "#fl-field-pre_checkout_enable_preview .fl-field-label" ).show();
		form.find( "#fl-field-pre_checkout_enable_preview select" ).show();
		form.find( "#fl-field-pre_checkout_enable_preview .fl-field-description" ).hide();

		form.find( "#fl-field-checkout_offer_subtitle_text" ).show();
		form.find( "#fl-field-checkout_offer_product_name" ).show();
		form.find( "#fl-field-checkout_offer_product_desc" ).show();
		form.find( "#fl-field-checkout_offer_accept_button_text" ).show();
		form.find( "#fl-field-checkout_offer_title_text" ).show();
		form.find( "#fl-field-checkout_offer_skip_button_text" ).show();

		form.find('#fl-builder-settings-section-pre_checkout_offer_style').show();

	} else {

		form.find( "#fl-field-pre_checkout_enable_preview .fl-field-label" ).hide();
		form.find( "#fl-field-pre_checkout_enable_preview select" ).hide();
		form.find( "#fl-field-pre_checkout_enable_preview .fl-field-description" ).show();

		form.find( "#fl-field-checkout_offer_subtitle_text" ).hide();
		form.find( "#fl-field-checkout_offer_product_name" ).hide();
		form.find( "#fl-field-checkout_offer_product_desc" ).hide();
		form.find( "#fl-field-checkout_offer_title_text" ).hide();
		form.find( "#fl-field-checkout_offer_accept_button_text" ).hide();
		form.find( "#fl-field-checkout_offer_skip_button_text" ).hide();

		form.find('#fl-builder-settings-section-pre_checkout_offer_style').hide();

	}

	if( 'yes' === enable_product_options ) {

		form.find( "#fl-field-product_options_position .fl-field-label" ).show();
		form.find( "#fl-field-product_options_position select" ).show();
		form.find( "#fl-field-product_options_position .fl-field-description" ).hide();

		form.find( "#fl-field-product_options_skin" ).show();
		form.find( "#fl-field-product_options_images" ).show();
		form.find( "#fl-field-product_option_section_title_text" ).show();

		form.find('#fl-builder-settings-section-product_style').show();

	} else {

		form.find( "#fl-field-product_options_position .fl-field-label" ).hide();
		form.find( "#fl-field-product_options_position select" ).hide();
		form.find( "#fl-field-product_options_position .fl-field-description" ).show();

		form.find( "#fl-field-product_options_skin" ).hide();
		form.find( "#fl-field-product_options_images" ).hide();
		form.find( "#fl-field-product_option_section_title_text" ).hide();

		form.find('#fl-builder-settings-section-product_style').hide();
	}

	<!--if( 'yes' === enable_order_bump ) {

		// Option for description.
		form.find( "#fl-field-order_bump_position .fl-field-label" ).show();
		form.find( "#fl-field-order_bump_position select" ).show();
		form.find( "#fl-field-order_bump_position .fl-field-description" ).hide();

		form.find( "#fl-field-order_bump_checkbox_label" ).show();
		form.find( "#fl-field-order_bump_highlight_text" ).show();
		form.find( "#fl-field-order_bump_product_description" ).show();
		form.find( "#fl-field-order_bump_checkbox_arrow" ).show();
		form.find( "#fl-field-order_bump_checkbox_arrow_animation" ).show();

		form.find('#fl-builder-settings-section-order_bump_style').show();
	} else {

		form.find( "#fl-field-order_bump_position .fl-field-label" ).hide();
		form.find( "#fl-field-order_bump_position select" ).hide();
		form.find( "#fl-field-order_bump_position .fl-field-description" ).show();

		form.find( "#fl-field-order_bump_checkbox_label" ).hide();
		form.find( "#fl-field-order_bump_highlight_text" ).hide();
		form.find( "#fl-field-order_bump_product_description" ).hide();
		form.find( "#fl-field-order_bump_checkbox_arrow" ).hide();
		form.find( "#fl-field-order_bump_checkbox_arrow_animation" ).hide();

		form.find('#fl-builder-settings-section-order_bump_style').hide();
	}-->

})(jQuery);
