/**
 * AddQuicktag Script to add buttons to html-editor
 * @since    2.0.0
 * @package  AddQuicktag Plugin
 */

jQuery( document ).ready( function( $ ) {
	
	if ( typeof addquicktag_tags  == 'undefined' )
		return;
	
	if ( typeof addquicktag_post_type == 'undefined' )
		return;
		
	if ( typeof addquicktag_pt_for_js == 'undefined' )
		return;
	
	var pt_for_js = Array('test', 'test2');
	$.each( addquicktag_pt_for_js, function( i, obj ) {
		pt_for_js.push(
			obj
		);
	});
	
	var tags = addquicktag_tags['buttons'];
	
	if ( typeof tags  == 'undefined' )
		return;
	// check post type
	if ( $.inArray( "addquicktag_post_type", addquicktag_pt_for_js ) ) {
		
		for ( var i = 0; i < tags.length; i++ ) {
			// check for active on this post type
			if ( 1 == tags[i][addquicktag_post_type] ) {
				
				if ( typeof tags[i].title  == 'undefined' ) tags[i].title = ' ';
				if ( typeof tags[i].end    == 'undefined' ) tags[i].end = '';
				if ( typeof tags[i].access == 'undefined' ) tags[i].access = '';
				
				edButtons[edButtons.length] = new edButton(
					// id, display, tagStart, tagEnd, access_key, title
					tags[i].text.toLowerCase(),
					tags[i].text,
					tags[i].start,
					tags[i].end,
					tags[i].access,
					tags[i].title
				);
			}
		}
		
	} // end check post type
	
});
