/**
 * AddQuicktag Script to add buttons to html-editor
 *
 * @package  AddQuicktag Plugin
 * @author   Frank Bueltge <frank@bueltge.de>
 * @version  2021-04-09
 * @since    2.0.0
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

	var tags = addquicktag_tags['buttons'];
	if (typeof tags === 'undefined') {
		return;
	}

	/**
	 * Decode html strings.
	 *
	 * @param str
	 * @returns {string}
	 */
	function html_entity_decode(str) {
		/*Firefox (and IE if the string contains no elements surrounded by angle brackets )*/
		try {
			var ta = document.createElement("textarea");
			ta.innerHTML = str;
			return ta.value;
		} catch (e) {
		}

		/*Internet Explorer*/
		try {
			var d = document.createElement("div");
			d.innerHTML = str.replace(/</g, "&lt;").replace(/>/g, "&gt;");
			if (typeof d.innerText !== "undefined") {
				return d.innerText;
			}
			/*Sadly this strips tags as well*/
		} catch (e) {
		}
	}

	/**
	 * Window for input.
	 *
	 * @param e
	 * @param c
	 * @param ed
	 */
	function qt_callback_input_window(e, c, ed) {

		var prmt = prompt('Enter Tag Name');

		if (prmt === null) {
			return;
		}

		this.tagStart = '[tag]' + prmt + '[/tag]';

		QTags.TagButton.prototype.callback.call(this, e, c, ed);
	}

	/**
	 * Get string from selection.
	 *
	 * @param canvas
	 * @returns {string|*}
	 */
	function get_selected_text(canvas) { // "canvas" is what they call the textarea of the editor
		canvas.focus();

		if (document.selection) { // IE
			return document.selection.createRange().text;
		}
		return canvas.value.substring(canvas.selectionStart, canvas.selectionEnd);
	}

	// check post type
	if ($.inArray("addquicktag_post_type", addquicktag_pt_for_js)) {

		for (var i = 0; i < tags.length; i++) {
			// check for active on this post type
			if (1 === parseInt(tags[i][addquicktag_post_type])) {
				//console.log(tags[i]);
				if (typeof tags[i].title === 'undefined') {
					tags[i].title = ' ';
				}
				if (typeof tags[i].end === 'undefined') {
					tags[i].end = '';
				}
				if (typeof tags[i].access === 'undefined') {
					tags[i].access = '';
				}

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
					html_entity_decode(tags[i].text).replace(/["\\]/gi, "").toLowerCase(),
					tags[i].text,
					tags[i].start,
					tags[i].end,
					tags[i].access,
					tags[i].title.replace(/["\\]/gi, "")
				);

				/**
				 * @TODO New idea for multiple edit windows
				 // for edit window
				 QTags.addButton(
				 tags[i].text.toLowerCase(),
				 tags[i].text,
				 qt_callback_input_window,
				 tags[i].end,
				 tags[i].access,
				 tags[i].title
				 );
				 */
			}
		}

	} // end check post type

	// Check the Code buttons, if inside the json
	var code_buttons = addquicktag_tags['code_buttons'];

	// Fallback, if WP core don't set the var
	if (typeof typenow === 'undefined') {
		typenow = '';
	}

	// IF no code buttons was active
	if (typeof code_buttons === 'undefined') {
		return;
	}

	// Fallback for no htmlentities settings
	if (typeof code_buttons.htmlentities === 'undefined') {
		code_buttons.htmlentities = 0;
	}

	// Fallback for no pre settings
	if (typeof code_buttons.pre === 'undefined') {
		code_buttons.pre = 0;
	}

	// if the htmlentities settings is active for each post type (var typenow from WP core)
	if (code_buttons.htmlentities[typenow] === 1) {
		/**
		 * ideas for code buttons and optional window with input possibility
		 *
		 * @see @see http://bililite.com/blog/2012/08/20/custom-buttons-in-the-wordpress-html-editor/
		 */
		QTags.addButton('toHTML', 'HTML Entities', function (el, canvas) {
			QTags.insertContent(
				get_selected_text(canvas).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
			);
		}, 'Encode HTML Entities');

		QTags.addButton('fromHTML', 'Decode HTML', function (el, canvas) {
			QTags.insertContent(
				get_selected_text(canvas).replace(/&amp;/g, '&').replace(/&lt;/g, '<').replace(/&gt;/g, '>')
			);
		}, 'Decode HTML Entities');
	}

	// if the pre settings is active for each post type (var typenow from WP core)
	if (code_buttons.pre[typenow] === 1) {
		var code_languages = ['html', 'javascript', 'css', 'bash', 'php', 'vb'];
		// Insert before the code button
		edButtons[109] = {
			html: function (id_prefix) {
				return '<select id="' + id_prefix + 'code_language" class="language-select">' +
					'<option>blank</option>' + // include a blank option
					'<option>' + code_languages.join('</option><option>') + '</option>' +
					'</select>';
			}
		};
		$('body').on('change', 'select.language-select', function () {
			var lang = $(this).val();
			// 110 is the code qt-tag from core, wp-includes/js/quicktags.js
			edButtons[110].tagStart = lang ? '<code class="language-' + lang + '">' : '<code>';
		});

		// Add pre button for preformatted text
		QTags.addButton('qt_pre', 'pre', '<pre>', '</pre>', '', 'Preformatted text', '108');
	}

})(jQuery);

var decodeEntities = (
	function () {
		// this prevents any overhead from creating the object each time
		var element = document.createElement('div');

		function decodeHTMLEntities(str) {
			if (str && typeof str === 'string') {
				// strip script/html tags
				str = str.replace(/<script[^>]*>([\S\s]*?)<\/script>/gmi, '');
				str = str.replace(/<\/?\w(?:[^"'>]|"[^"]*"|'[^']*')*>/gmi, '');
				element.innerHTML = str;
				str = element.textContent;
				element.textContent = '';
			}

			return str;
		}

		return decodeHTMLEntities;
	}
)();
