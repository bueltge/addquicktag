/**
 * AddQuicktag Script to add listbox to visual-editor
 * 
 * @package  AddQuicktag Plugin
 * @author   Frank Bueltge <frank@bueltge.de>
 * @version  03/13/2014
 * @since    2.0.0
 */

jQuery( document ).ready( function( $ ) {
	
	if ( typeof addquicktag_tags  == 'undefined' )
		return;
	
	if ( typeof addquicktag_post_type == 'undefined' )
		return;
		
	if ( typeof addquicktag_pt_for_js == 'undefined' )
		return;
	
	// wrong post type
	if ( -1 == $.inArray( addquicktag_post_type, addquicktag_pt_for_js ) )
		return;
	
	// break, if not an button for visual and post type
	var visual    = 0,
	    post_type = 0,
        i = 0;
	
	for ( i = 0; i < addquicktag_tags.buttons.length; i++ ) {
		// if not visual button in the list, return
		if ( 1 === parseInt( addquicktag_tags.buttons[i]['visual'] ) )
			visual = addquicktag_tags.buttons[i]['visual'];
		// check for active on this post type on each buttons
		if ( 1 === parseInt( addquicktag_tags.buttons[i][addquicktag_post_type] ) )
			post_type = addquicktag_tags.buttons[i][addquicktag_post_type];
	}
	
	if ( 1 !== parseInt( visual ) )
		return;
	
	if ( 1 !== parseInt( post_type ) )
		return;

	// Add listbox plugin to TinyMCE editor
	tinymce.PluginManager.add( 'rmnlQuicktagSettings_tmce', function( editor ) {

		editor.addButton('rmnlQuicktagSettings_tmce', function() {

			var tiny_tags = addquicktag_tags['buttons'],
			    values = []
				i = 0;

			for ( i = 0; i < tiny_tags.length; i++ ) {

				// check for active on this post type
				if ( 1 === parseInt( tiny_tags[i][addquicktag_post_type] ) ) {

					if ( 1 == tiny_tags[i].visual ) {
						values.push( {text: tiny_tags[i].text, value: String(i) } );
					}
				}
			}

			return {
				type: 'listbox',
				//name: 'align',
				text: 'Quicktags',
				label: 'Select :',
				fixedWidth: true,
				onselect: function(v) {
					var selection = tinymce.activeEditor.selection.getContent(),
						marked = true,
						// Set short var for the value identifier
						var v = v.control._value;

					// Check for marked content
					switch (v) {
						case 'null' :
							marked = false;
							break;
						default :
							break;
					}

					if ( marked == true ) {

						if ( typeof tiny_tags[v].end !== 'undefined' )
							tiny_tags[v].end = '';

						tinymce.activeEditor.selection.setContent(
							tiny_tags[v].start + selection + tiny_tags[v].end
						);

					}
				},
				values: values,
			};
		});

	});
});