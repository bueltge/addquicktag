/**
 * AddQuicktag Script settings page of the plugin
 * @since    08/02/2012
 * @package  AddQuicktag Plugin
 */

jQuery( document ).ready( function( $ ) {
	
	$( 'input:checkbox.toggle' ).click( function( event ) {
		var i = $(this).attr('value');
		console.log(i);
		
		if ( this.checked ) {
			// Iterate each checkbox
			$('#rmqtb' + i +' input:checkbox').each(function() {
				this.checked = true;
			});
		} else {
			// Iterate each checkbox
			$('#rmqtb' + i +' input:checkbox').each(function() {
				this.checked = false;
			});
		}
	});

});
