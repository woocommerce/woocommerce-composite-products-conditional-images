;( function ( $, window, document, undefined ) {

	$( '.composite_data' ).on( 'wc-composite-initializing', function( event, composite ) {

		function OverlayImageApp() {

			this.initialize = function() {
				composite.actions.add_action( 'component_selection_changed', this.component_selection_changed_handler, 20, this );
			};

			this.component_selection_changed_handler = function( step ) {

				var active_scenarios       = composite.scenarios.get_active_scenarios_by_type( 'overlay_image' ),
					shaping_components     = composite.scenarios.get_scenario_shaping_components(),
					non_shaping_components = _.difference( _.pluck( composite.get_components(), 'component_id' ), shaping_components );

				/*
				 * Filter out any scenarios containing components that:
				 * - are not masked; AND
				 * - are not scenario-shaping.
				 */
				for ( var index = 0, length = non_shaping_components.length; index < length; index++ ) {
					active_scenarios = _.difference( active_scenarios, composite.scenarios.get_unmasked_scenarios( active_scenarios, non_shaping_components[ index ] ) );
				}

			};
		}

		var app = new OverlayImageApp();

		app.initialize();

	} );

} ) ( jQuery, window, document );
