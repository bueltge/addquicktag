import "core-js/modules/es6.regexp.replace";
import _parseInt from "@babel/runtime-corejs2/core-js/parse-int";
import "core-js/modules/web.dom.iterable";

/**
 * AddQuicktag buttons - Playing for Gutenberg.
 */
[addquicktag_tags, addquicktag_tags['buttons'], addquicktag_post_type, addquicktag_pt_for_js].forEach(function (element) {
  if (typeof element === 'undefined') {
    return false;
  }
});
/*
// Check if the current post type for quicktags is on the current page.
if (!addquicktag_pt_for_js.includes(addquicktag_post_type)) {
    return false;
}*/

var tags = addquicktag_tags['buttons'];
var _window$wp$element = window.wp.element,
    createElement = _window$wp$element.createElement,
    Fragment = _window$wp$element.Fragment;
var _window$wp$richText = window.wp.richText,
    registerFormatType = _window$wp$richText.registerFormatType,
    unregisterFormatType = _window$wp$richText.unregisterFormatType,
    toggleFormat = _window$wp$richText.toggleFormat;
var __ = window.wp.i18n.__;
var _window$wp$editor = window.wp.editor,
    RichTextToolbarButton = _window$wp$editor.RichTextToolbarButton,
    RichTextShortcut = _window$wp$editor.RichTextShortcut;

for (var i = 0; i < tags.length; i++) {
  // check each tag for active on this post type
  if (1 === _parseInt(tags[i][addquicktag_post_type])) {
    (function () {
      console.log(tags[i]);

      if (typeof tags[i].title === 'undefined') {
        tags[i].title = ' ';
      }

      if (typeof tags[i].end === 'undefined') {
        tags[i].end = '';
      }

      if (typeof tags[i].access === 'undefined') {
        tags[i].access = '';
      }

      var name = html_entity_decode(tags[i].text).replace(/["\\]/gi, "").toLowerCase();
      var type = "advanced/".concat(name); // ToDo replace <> from tag

      var tagName = 'test'; //tags[ i ].start

      var className = null;
      var title = tags[i].title;
      var character = tags[i].access;
      var icon = tags[i].dashicon.replace(/dashicons-/gi, "");
      console.log([{
        name: name,
        type: type,
        tagName: tagName,
        className: className,
        title: title,
        character: character,
        icon: icon
      }]);
      registerFormatType(type, {
        title: title,
        tagName: tagName,
        className: className,
        edit: function edit(_ref) {
          var isActive = _ref.isActive,
              value = _ref.value,
              onChange = _ref.onChange;

          var onToggle = function onToggle() {
            return onChange(toggleFormat(value, {
              type: type
            }));
          };

          return createElement(Fragment, null, createElement(RichTextShortcut, {
            type: 'primary',
            character: character,
            onUse: onToggle
          }), createElement(RichTextToolbarButton, {
            icon: icon,
            title: title,
            onClick: onToggle,
            isActive: isActive,
            shortcutType: 'primary',
            shortcutCharacter: character,
            className: "toolbar-button-with-text toolbar-button__advanced-".concat(name)
          }));
        }
      });
      /**});*/
    })();
  }
}

function html_entity_decode(str) {
  /*Firefox (and IE if the string contains no elements surrounded by angle brackets )*/
  try {
    var ta = document.createElement("textarea");
    ta.innerHTML = str;
    return ta.value;
  } catch (e) {}
  /*Internet Explorer*/


  try {
    var d = document.createElement("div");
    d.innerHTML = str.replace(/</g, "&lt;").replace(/>/g, "&gt;");

    if (typeof d.innerText !== "undefined") {
      return d.innerText;
    }
    /*Sadly this strips tags as well*/

  } catch (e) {}
}
//# sourceMappingURL=add-quicktag-gutenberg.js.map
