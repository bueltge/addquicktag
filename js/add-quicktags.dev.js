/**
 * AddQuicktag Script to add buttons to html-editor
 * @since    2.0.0
 * @package  AddQuicktag Plugin
 */

jQuery( document ).ready( function( $ ) {
	
	if ( typeof addquicktag_tags  == 'undefined' )
		return;
	
	var tags = addquicktag_tags['buttons'];
	
	if ( typeof tags !== 'undefined' ) {
		
		for ( var i = 0; i < tags.length; i++ ) {
			if ( typeof tags[i].title  == 'undefined' ) tags[i].title = ' ';
			if ( typeof tags[i].end    == 'undefined' ) tags[i].end = '';
			if ( typeof tags[i].access == 'undefined' ) tags[i].access = '';
			
			edButtons[edButtons.length] = new edButton(
				// id, display, tagStart, tagEnd, access_key, title
				tags[i].text.toLowerCase(), tags[i].text, tags[i].start, tags[i].end, tags[i].access, tags[i].title
			);
		}
		
	} // end if tags
	
});
