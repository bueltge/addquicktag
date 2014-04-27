/**
 * AddQuicktag Script to add listbox to visual-editor
 *
 * @package  AddQuicktag Plugin
 * @author   Frank Bueltge <frank@bueltge.de>
 * @version  04/24/2014
 * @since    2.3.0
 */

jQuery(document).ready(function ($) {

	if (typeof addquicktag_tags == 'undefined')
		return;

	if (typeof addquicktag_post_type == 'undefined')
		return;

	if (typeof addquicktag_pt_for_js == 'undefined')
		return;

	// wrong post type
	if (-1 == $.inArray(addquicktag_post_type, addquicktag_pt_for_js))
		return;

	// break, if not an button for visual and post type
	var visual = 0,
		post_type = 0,
		i = 0;

	for (i = 0; i < addquicktag_tags.buttons.length; i++) {
		// if not visual button in the list, return
		if (1 === parseInt(addquicktag_tags.buttons[i]['visual']))
			visual = addquicktag_tags.buttons[i]['visual'];
		// check for active on this post type on each buttons
		if (1 === parseInt(addquicktag_tags.buttons[i][addquicktag_post_type]))
			post_type = addquicktag_tags.buttons[i][addquicktag_post_type];
	}

	if (1 !== parseInt(visual))
		return;

	if (1 !== parseInt(post_type))
		return;

	// Add listbox plugin to TinyMCE editor
	tinymce.PluginManager.add('rmnlQuicktagSettings_tmce', function (editor) {

		editor.addButton('rmnlQuicktagSettings_tmce', function () {

			var tiny_tags = addquicktag_tags['buttons'],
				values = []
			i = 0;

			for (i = 0; i < tiny_tags.length; i++) {

				// check for active on this post type
				if (1 === parseInt(tiny_tags[i][addquicktag_post_type])) {

					if (1 == tiny_tags[i].visual) {
						values.push({text: tiny_tags[i].text, value: String(i) });
					}
				}
			}

			return {
				type      : 'listbox',
				//name: 'align',
				text      : 'Quicktags',
				label     : 'Select :',
				fixedWidth: true,
				onselect  : function (v) {
console.log(v);
					var // Set short var for the value identifier
						v = v.control._value,
						marked = true;

					// Check for marked content
					switch (v) {
						case 'null' :
							marked = false;
							break;
						default :
							break;
					}

					if (marked == true) {

						var content = tinymce.activeEditor.selection.getContent(),
							start_content = tinymce.activeEditor.selection.getStart(),
							all = tinymce.activeEditor.selection.getNode(),
							start = tiny_tags[v].start,
							end = tiny_tags[v].end;

						if (typeof end == 'undefined')
							end = '';

						console.log(tiny_tags[v]);
						console.log(start);
						console.log(start_content);
						console.log(content);
						console.log(all);
						console.log(start_content.indexOf( start ));
						console.log(start_content.search( start ));

						if ( start_content.search( start ) != -1 ) {
							tinymce.activeEditor.selection.setContent(
								tiny_tags[v].start + content + tiny_tags[v].end
							);
						}

						/*
						if ( content.search( start ) ) {
							console.log('ist drin');
							// @see: http://www.tinymce.com/forum/viewtopic.php?id=19973
							tinyMCE.activeEditor.dom.remove(
								tinyMCE.activeEditor.dom.select(
									'strong', tinyMCE.activeEditor.selection.getNode()
								)
							);
						}*/

					}
				},
				values    : values,
			};
		});

	});
});