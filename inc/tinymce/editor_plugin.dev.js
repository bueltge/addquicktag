/**
 * AddQuicktag Script to add listbox to visual-editor
 * @since    2.0.0
 * @package  AddQuicktag Plugin
 */

(function() {
	
	if ( typeof addquicktag_tags  == 'undefined' )
		return;
	// Load plugin specific language pack
	tinymce.PluginManager.requireLangPack('rmnlQuicktagSettings_tmce' );
	
	/*
	 * @see  http://www.tinymce.com/wiki.php/API3:class.tinymce.ui.ListBox
	 */
	tinymce.create('tinymce.plugins.AddQuicktag', {
		createControl: function(n, cm) {
			switch (n) {
				case 'rmnlQuicktagSettings_tmce':
					var tiny_tags = addquicktag_tags['buttons'];
					var i = 0;
					var rmnlQuicktagSettings_tmce_options = '';
					var mlb = cm.createListBox('rmnlQuicktagSettings_tmce', {
						title : 'Quicktags',
						onselect : function(v) {
							var selection = tinyMCE.activeEditor.selection.getContent();
							var marked = true;
							
							switch (v) {
								case 'null' :
									var marked = false;
									break;
								default :
									break;
							}
							
							if ( marked == true ) {
								if ( typeof tiny_tags[v].end == 'undefined' ) tiny_tags[v].end = '';
								tinyMCE.activeEditor.selection.setContent(
									tiny_tags[v].start + selection + tiny_tags[v].end
								);
							}
						}
					});
					
					// add values to the listbox
					if ( typeof tiny_tags !== 'undefined' ) {
						for ( i = 0; i < tiny_tags.length; i++ ) {
							if ( 1 == tiny_tags[i].visual )
								mlb.add( tiny_tags[i].text, String(i) );
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
})();