/**
 * AddQuicktag Script to add the listbox button to the visual-editor TinyMCE.
 *
 * @package  AddQuicktag Plugin
 * @author   Frank Bueltge <frank@bueltge.de>
 * @version  2021-04-19
 * @since    2.3.0
 */

(function ($) {

	if (typeof addquicktag_tags === 'undefined') {
		return;
	}

	if (typeof addquicktag_post_type === 'undefined') {
		return;
	}

	if (typeof addquicktag_pt_for_js === 'undefined') {
		return;
	}

	// wrong post type
	if (-1 === $.inArray(addquicktag_post_type, addquicktag_pt_for_js)) {
		return;
	}

	// break, if not an button for visual and post type
	var visual = 0,
		post_type = 0,
		i = 0;

	for (i; i < addquicktag_tags.buttons.length; i++) {
		// if not visual button in the list, return
		if (1 === parseInt(addquicktag_tags.buttons[i]['visual'])) {
			visual = addquicktag_tags.buttons[i]['visual'];
		}
		// check for active on this post type on each buttons
		if (1 === parseInt(addquicktag_tags.buttons[i][addquicktag_post_type])) {
			post_type = addquicktag_tags.buttons[i][addquicktag_post_type];
		}
	}

	if (1 !== parseInt(visual)) {
		return;
	}

	if (1 !== parseInt(post_type)) {
		return;
	}

	// Add listbox plugin to TinyMCE editor.
	tinymce.PluginManager.add('rmnlQuicktagSettings_tmce', function (editor) {

		editor.addButton('rmnlQuicktagSettings_tmce', function () {

			var tiny_tags = addquicktag_tags['buttons'],
				values = [],
				i = 0;

			for (i; i < tiny_tags.length; i++) {

				// check for active on this post type
				if (1 === parseInt(tiny_tags[i][addquicktag_post_type])) {

					// Create the list items with all custom values from settings.
					// @see http://www.tinymce.com/wiki.php/api4:class.tinymce.ui.ListBox
					if (1 === tiny_tags[i].visual) {
						console.log( tiny_tags[ i ] );
						values.push({
							text: tiny_tags[i].text,
							value: String(i),
							tooltip: tiny_tags[i].title,
							icon: 'icon dashicons dashicons-before ' + tiny_tags[i].dashicon
						});
					}
				}
			}

			return {
				type: 'listbox',
				//name      : 'align',
				icon: '',
				text: 'Quicktags',
				label: 'Select :',
				fixedWidth: true,
				onselect: function (v) {
					// For debugging purpose
					console.log(v);

					var // Set short var for the value identifier
						// v = v.control._value,
						// Change since WordPress 4.3 to new object values.
						value = v.control.settings.value,
						marked = false;

					if (typeof (
						tinymce.activeEditor.selection.getContent()
					) !== 'undefined') {
						marked = true;
					}

					if (marked === true) {

						console.log(tiny_tags);
						console.log(value);

						var content = tinymce.activeEditor.selection.getContent(),
							start_content = tinymce.activeEditor.selection.getStart().nodeName,
							all = tinymce.activeEditor.selection.getNode(),
							start = tiny_tags[value].start,
							start_tag = start.match(/[a-z]+/),
							end = tiny_tags[value].end;

						if (typeof start === 'undefined') {
							start = '';
						}

						if (typeof end === 'undefined') {
							end = '';
						}

						/**/
						// For debugging purpose
						console.log(v);
						console.log('TinyTags: ' + tiny_tags[value]);
						console.log('start_content: ' + start_content);
						console.log('start_content.nodeName: ' + tinymce.activeEditor.selection.getStart().nodeName);
						console.log('start_content.outerHMTL: ' + tinymce.activeEditor.selection.getStart().outerHMTL);
						console.log('Content: ' + content);
						console.log(all);
						console.log('Start tag: ' + start);
						console.log('Start tag, only: ' + start.match(/[a-z]+/));
						console.log('End tag: ' + end);
						//console.log(start_content.indexOf( start ));
						console.log('Search nodeName: ' + start_content.search(start));
						/**/

						// Add tag to content
						if (start.match(/[a-z]+/i) !== start_content.toLowerCase()) {
							tinymce.activeEditor.selection.setContent(
								tiny_tags[value].start + content + tiny_tags[value].end
							);
						}

						// Remove existing tag
						if (start.match(/[a-z]+/i) === start_content.toLowerCase()) {
							// Remove content with tag
							tinyMCE.activeEditor.dom.remove(
								tinymce.activeEditor.selection.getNode(
									start_content.toLowerCase()
								)
							);
							// Add content, without tag
							tinymce.activeEditor.selection.setContent(
								content
							);

						}

					}
				},
				values: values
			};
		});

	});
})(jQuery);
