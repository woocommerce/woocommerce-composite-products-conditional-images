;( function( $ ) {

	$( function( $ ) {

		var $scenarios_panel             = $( '#bto_scenario_data' ),
			conditional_image_frame_data = {
				image_frame: false,
				$button:     false
			};

		/*
		 * Handle events in Scenarios panel.
		 */

		// Update scenario DOM elements, menu order and toolbar state.
		$scenarios_panel

			// Enable/Disable Scenario Action.
			.on( 'change', 'input.scenario_action_overlay_image', function() {

				var $this      = $( this ),
					$container = $this.closest( '.scenario_action_overlay_image_group' ),
					$content   = $container.find( '.action_conditional_images' );

				if ( $this.is( ':checked' ) ) {
					$content.show();
				} else {
					$content.hide();
				}

			} )

			// Set Image.
			.on( 'click', '.upload_conditional_image_button', function( e ) {

				conditional_image_frame_data.$button = $( this );

				e.preventDefault();

				// If the media frame already exists, reopen it.
				if ( conditional_image_frame_data.image_frame ) {

					conditional_image_frame_data.image_frame.open();

				} else {

					// Create the media frame.
					conditional_image_frame_data.image_frame = wp.media( {

						// Set the title of the modal.
						title: wc_cp_ci_admin_params.i18n_choose_component_image,
						button: {
							text: wc_cp_ci_admin_params.i18n_set_component_image
						},
						states: [
							new wp.media.controller.Library( {
								title: wc_cp_ci_admin_params.i18n_choose_component_image,
								filterable: 'all'
							} )
						]
					} );

					// When an image is selected, run a callback.
					conditional_image_frame_data.image_frame.on( 'select', function () {

						var attachment = conditional_image_frame_data.image_frame.state().get( 'selection' ).first().toJSON(),
							url        = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;

						conditional_image_frame_data.$button.addClass( 'has_image' );
						conditional_image_frame_data.$button.closest( '.action_conditional_images' ).find( '.remove_conditional_image_button' ).addClass( 'has_image' );
						conditional_image_frame_data.$button.find( 'input' ).val( attachment.id ).change();
						conditional_image_frame_data.$button.find( 'img' ).eq( 0 ).attr( 'src', url );
					} );

					// Finally, open the modal.
					conditional_image_frame_data.image_frame.open();
				}

			} )

			// Remove Image.
			.on( 'click', '.remove_conditional_image_button', function( e ) {

				var $button         = $( this ),
					$option_wrapper = $button.closest( '.action_conditional_images' ),
					$upload_button  = $option_wrapper.find( '.upload_conditional_image_button' );

				e.preventDefault();

				$upload_button.removeClass( 'has_image' );
				$button.removeClass( 'has_image' );
				$option_wrapper.find( 'input' ).val( '' ).change();
				$upload_button.find( 'img' ).eq( 0 ).attr( 'src', wc_composite_admin_params.wc_placeholder_img_src );

			} );

	} );

} ) ( jQuery );
