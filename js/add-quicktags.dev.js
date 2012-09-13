/**
 * AddQuicktag Script to add buttons to html-editor
 * 
 * @package  AddQuicktag Plugin
 * @author   Frank Bueltge <frank@bueltge.de>
 * @version  07/11/2012
 * @since    2.0.0
 */

jQuery( document ).ready( function( $ ) {
	
	if ( typeof addquicktag_tags == 'undefined' )
		return;
	
	if ( typeof addquicktag_post_type == 'undefined' )
		return;
		
	if ( typeof addquicktag_pt_for_js == 'undefined' )
		return;
	
	var tags = addquicktag_tags['buttons'];
	if ( typeof tags == 'undefined' )
		return;
	
	// check post type
	if ( $.inArray( "addquicktag_post_type", addquicktag_pt_for_js ) ) {
		
		for ( var i = 0; i < tags.length; i++ ) {
			// check for active on this post type
			if ( 1 === parseInt( tags[i][addquicktag_post_type] ) ) {
				
				if ( typeof tags[i].title  == 'undefined' ) tags[i].title = ' ';
				if ( typeof tags[i].end    == 'undefined' ) tags[i].end = '';
				if ( typeof tags[i].access == 'undefined' ) tags[i].access = '';
				
				 /**
				 * @param id string required Button HTML ID
				 * @param display string required Button's value="..."
				 * @param arg1 string || function required Either a starting tag to be inserted like "<span>" or a callback that is executed when the button is clicked.
				 * @param arg2 string optional Ending tag like "</span>"
				 * @param access_key string optional Access key for the button.
				 * @param title string optional Button's title="..."
				 * @param priority int optional Number representing the desired position of the button in the toolbar. 1 - 9 = first, 11 - 19 = second, 21 - 29 = third, etc.
				 * @param instance string optional Limit the button to a specific instance of Quicktags, add to all instances if not present.
				 */
				QTags.addButton(
					tags[i].text.toLowerCase(),
					tags[i].text,
					tags[i].start,
					tags[i].end,
					tags[i].access,
					tags[i].title
				);
				/* old function
				edButtons[edButtons.length] = new edButton(
					// id, display, tagStart, tagEnd, access_key, title
					tags[i].text.toLowerCase(),
					tags[i].text,
					tags[i].start,
					tags[i].end,
					tags[i].access,
					tags[i].title
				);
				*/
			}
		}
		
	} // end check post type
	
});
