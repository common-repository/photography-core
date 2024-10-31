jQuery( document ).ready( function() {
	if( ! jQuery( '#the-list' ).find( 'tr:first-child' ).hasClass( 'no-items' ) ) {
		jQuery( '#the-list' ).sortable({
			placeholder: "pcore-drag-drop-tax-placeholder",
			axis: "y",
			start: function(event, ui) {
				var height = jQuery( ui.item[0] ).css( 'height' );
				jQuery( '.pcore-drag-drop-tax-placeholder' ).css( 'height', height );
			},
			update: function( event, ui ) {
				jQuery( ui.item[0] ).find( 'input[type="checkbox"]' ).hide().after( '<img src="' + simple_taxonomy_ordering_data.preloader_url + '" class="pcore-simple-taxonomy-preloader" />' );
				var updated_array = [];
				jQuery( '#the-list' ).find( 'tr.ui-sortable-handle' ).each( function() {
					var tax_id = jQuery( this ).attr( 'id' ).replace( 'tag-', '' );
					updated_array.push( [ tax_id, jQuery( this ).index() ] );
				});
				var data = {
					'action': 'update_taxonomy_order',
					'updated_array': updated_array 
				};
				jQuery.post( simple_taxonomy_ordering_data.ajax_url, data, function( response ) {
					jQuery( '.pcore-simple-taxonomy-preloader' ).remove();
					jQuery( ui.item[0] ).find( 'input[type="checkbox"]' ).show();
				});
			}
		});
	}

});