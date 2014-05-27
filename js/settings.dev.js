/**
 * AddQuicktag Script settings page of the plugin
 * @since    05/22/2014
 * @package  AddQuicktag Plugin
 */

jQuery( document ).ready( function( $ ) {
	
	$( 'input:checkbox.toggle' ).click( function( event ) {
		var i = $( this ).attr( 'value' ),
		    sel = '#rmqtb' + i +' input:checkbox';
		
		if ( this.checked ) {
			// Iterate each checkbox
			$( sel ).each( function() {
				this.checked = true;
			});
		} else {
			// Iterate each checkbox
			$( sel ).each( function() {
				this.checked = false;
			} );
		}
	} );

	$( 'table.rmnlQuicktagSettings' ).delegate( 'td','mouseover mouseout', function(e) {
		var hover = 'hover';
		
		if ( e.type == 'mouseover' ) {
			$( this ).parent().addClass( hover );
			$( 'colgroup' ).eq( $( this ).index() ).addClass( hover );
		} else {
			$( this ).parent().removeClass( hover );
			$( 'colgroup' ).eq( $( this ).index() ).removeClass( hover );
		}
	} );

} );
