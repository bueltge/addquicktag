/**
 * AddQuicktag Script to add listbox to visual-editor
 * 
 * @package  AddQuicktag Plugin
 * @author   Frank Bueltge <frank@bueltge.de>
 * @version  07/11/2012
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
	var visual    = 0;
	var post_type = 0;
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
	
	// Load plugin specific language pack
	tinymce.PluginManager.requireLangPack( 'rmnlQuicktagSettings_tmce' );
	
	/*
	 * @see  http://www.tinymce.com/wiki.php/API3:class.tinymce.ui.ListBox
	 */
	tinymce.create('tinymce.plugins.AddQuicktag', {
		createControl: function(n, cm) {
			switch (n) {
				case 'rmnlQuicktagSettings_tmce':
					var tiny_tags = addquicktag_tags['buttons'],
						i = 0,
						rmnlQuicktagSettings_tmce_options = '',
						mlb = cm.createListBox('rmnlQuicktagSettings_tmce', {
						title : 'Quicktags',
						onselect : function(v) {
							var selection = tinyMCE.activeEditor.selection.getContent(),
								marked = true;
							
							switch (v) {
								case 'null' :
									marked = false;
									break;
								default :
									break;
							}
							
							if ( marked == true ) {
								
								if ( typeof tiny_tags[v].end == 'undefined' )
									tiny_tags[v].end = '';
								
								tinyMCE.activeEditor.selection.setContent(
									tiny_tags[v].start + selection + tiny_tags[v].end
								);
							}
						}
					});
					
					// add values to the listbox
					if ( typeof tiny_tags !== 'undefined' ) {
						for ( i = 0; i < tiny_tags.length; i++ ) {
							
							// check for active on this post type
							if ( 1 === parseInt( tiny_tags[i][addquicktag_post_type] ) ) {
							
								if ( 1 == tiny_tags[i].visual )
									mlb.add( tiny_tags[i].text, String(i) );
							}
						}
					} else {
						mlb.add('rmnlQuicktagSettings_tmce.addquicktag_select_error', 'null');
					}
					
					// Return the new listbox instance
					return mlb;
				break;
			}
			return null;
		},
		
		getInfo : function() {
			return {
				longname :  'AddQuicktag Plugin for TinyMCE in WordPress',
				author :    'Frank Bueltge',
				authorurl : 'http://bueltge.de/',
				infourl :   'http://wordpress.org/extend/plugins/addquicktag/',
				version :   tinymce.majorVersion + "." + tinymce.minorVersion
			};
		} 		
	});
	
	// Register plugin
	tinymce.PluginManager.add( 'rmnlQuicktagSettings_tmce', tinymce.plugins.AddQuicktag );
});