/**
 * AddQuicktag buttons - Playing for Gutenberg.
 */

[
    addquicktag_tags,
    addquicktag_tags['buttons'],
    addquicktag_post_type,
    addquicktag_pt_for_js
].forEach(function (element) {
    if (typeof element === 'undefined') {
        return false;
    }
});
/*
// Check if the current post type for quicktags is on the current page.
if (!addquicktag_pt_for_js.includes(addquicktag_post_type)) {
    return false;
}*/

const tags = addquicktag_tags['buttons'];
const {createElement, Fragment} = window.wp.element
const {registerFormatType, unregisterFormatType, toggleFormat} = window.wp.richText
const {__} = window.wp.i18n
const {RichTextToolbarButton, RichTextShortcut} = window.wp.editor;

for (var i = 0; i < tags.length; i++) {
    // check each tag for active on this post type
    if (1 === parseInt(tags[i][addquicktag_post_type])) {
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

        const name = html_entity_decode(tags[i].text).replace(/["\\]/gi, "").toLowerCase()
        const type = `advanced/${name}`
        // ToDo replace <> from tag
        const tagName = 'test' //tags[ i ].start.replace(/^(?=.<)(?=.>)/gi, "")
        const className = null
        const title = tags[i].title
        const character = tags[i].access
        const icon = tags[i].dashicon.replace(/dashicons-/gi, "");

console.log([{name:name, type:type, tagName:tagName, className:className, title:title, character:character, icon:icon}]);

        registerFormatType(type, {
            title,
            tagName,
            className,
            edit({isActive, value, onChange}) {
                const onToggle = () => onChange(toggleFormat(value, {type}))

                return (
                    createElement(Fragment, null,
                        createElement(RichTextShortcut, {
                            type: 'primary',
                            character,
                            onUse: onToggle
                        }),
                        createElement(RichTextToolbarButton, {
                            icon,
                            title,
                            onClick: onToggle,
                            isActive,
                            shortcutType: 'primary',
                            shortcutCharacter: character,
                            className: `toolbar-button-with-text toolbar-button__advanced-${name}`
                        })
                    )
                )
            }
        })
        /**});*/
    }
}

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
