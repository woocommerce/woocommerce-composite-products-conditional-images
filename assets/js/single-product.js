;( function ( $, window, document, undefined ) {

	$( '.composite_data' ).on( 'wc-composite-initializing', function( event, composite ) {

		function OverlayImageApp() {

			this.initialize = function() {
				composite.actions.add_action( 'active_scenarios_changed', this.active_scenarios_changed_handler, 10, this );
			};

			this.active_scenarios_changed_handler = function( triggered_by ) {

				var active_scenarios = composite.scenarios.get_active_scenarios_by_type( 'overlay_image' );

				/*
				 * Filter out any scenarios containing components that:
				 * - are not masked; AND
				 * - are not scenario-shaping.
				 */
			};
		}

		var app = new OverlayImageApp();

		app.initialize();

	} );

} ) ( jQuery, window, document );
