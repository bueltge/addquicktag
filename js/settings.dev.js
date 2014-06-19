/**
 * AddQuicktag Script settings page of the plugin
 * @since    06/19/2014
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
			$( 'table.rmnlQuicktagSettings colgroup' ).eq( $( this ).index() ).addClass( hover );
		} else {
			$( this ).parent().removeClass( hover );
			$( 'table.rmnlQuicktagSettings colgroup' ).eq( $( this ).index() ).removeClass( hover );
		}
	} );

	$( 'table.rmnlCoreQuicktagSettings' ).delegate( 'td','mouseover mouseout', function(e) {
		var hover = 'hover';

		if ( e.type == 'mouseover' ) {
			$( this ).parent().addClass( hover );
			$( 'table.rmnlCoreQuicktagSettings colgroup' ).eq( $( this ).index() ).addClass( hover );
		} else {
			$( this ).parent().removeClass( hover );
			$( 'table.rmnlCoreQuicktagSettings colgroup' ).eq( $( this ).index() ).removeClass( hover );
		}
	} );

	$( 'table.rmnlCodeQuicktagSettings' ).delegate( 'td','mouseover mouseout', function(e) {
		var hover = 'hover';

		if ( e.type == 'mouseover' ) {
			$( this ).parent().addClass( hover );
			$( 'table.rmnlCodeQuicktagSettings colgroup' ).eq( $( this ).index() ).addClass( hover );
		} else {
			$( this ).parent().removeClass( hover );
			$( 'table.rmnlCodeQuicktagSettings colgroup' ).eq( $( this ).index() ).removeClass( hover );
		}
	} );

} );
