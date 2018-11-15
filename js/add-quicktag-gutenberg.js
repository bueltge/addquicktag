import "core-js/modules/es6.function.name";
import "core-js/modules/web.dom.iterable";

/**
 * AddQuicktag buttons - Playing for Gutenberg.
 */
[addquicktag_tags, addquicktag_post_type, addquicktag_pt_for_js].forEach(function (element) {
  if (typeof element === 'undefined') {
    return false;
  }
});
console.log('Test Gutenberg in AddQuicktag: all var are existent');
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
[{
  name: 'heart',
  tagName: 'hearttag',
  className: 'heart',
  title: 'Heart',
  character: '.',
  icon: 'heart'
}, {
  name: 'sub',
  tagName: 'subtest',
  className: null,
  title: 'Subscript',
  character: ',',
  icon: 'hammer'
}].forEach(function (_ref) {
  var name = _ref.name,
      tagName = _ref.tagName,
      className = _ref.className,
      title = _ref.title,
      character = _ref.character,
      icon = _ref.icon;
  var type = "advanced/".concat(name);
  registerFormatType(type, {
    title: title,
    tagName: tagName,
    className: className,
    edit: function edit(_ref2) {
      var isActive = _ref2.isActive,
          value = _ref2.value,
          onChange = _ref2.onChange;

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
});
//# sourceMappingURL=add-quicktag-gutenberg.js.map
