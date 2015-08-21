/**
 * AddQuicktag Script to add listbox to visual-editor
 * 
 * @package  AddQuicktag Plugin
 * @author   Frank Bueltge <frank@bueltge.de>
 * @version  2015-08-21
 * @since    2.3.0
 */

jQuery(document).ready(function(t){if("undefined"!=typeof addquicktag_tags&&"undefined"!=typeof addquicktag_post_type&&"undefined"!=typeof addquicktag_pt_for_js&&-1!=t.inArray(addquicktag_post_type,addquicktag_pt_for_js)){var e=0,a=0,n=0;for(n;n<addquicktag_tags.buttons.length;n++)1===parseInt(addquicktag_tags.buttons[n].visual)&&(e=addquicktag_tags.buttons[n].visual),1===parseInt(addquicktag_tags.buttons[n][addquicktag_post_type])&&(a=addquicktag_tags.buttons[n][addquicktag_post_type]);1===parseInt(e)&&1===parseInt(a)&&tinymce.PluginManager.add("rmnlQuicktagSettings_tmce",function(t){t.addButton("rmnlQuicktagSettings_tmce",function(){var t=addquicktag_tags.buttons,e=[],a=0;for(a;a<t.length;a++)1===parseInt(t[a][addquicktag_post_type])&&1==t[a].visual&&e.push({text:t[a].text,value:String(a)});return{type:"listbox",text:"Quicktags",label:"Select :",fixedWidth:!0,onselect:function(e){var a=e.control.settings.value,n=!1;if("undefined"!=typeof tinymce.activeEditor.selection.getContent()&&(n=!0),1==n){var i=tinymce.activeEditor.selection.getContent(),o=tinymce.activeEditor.selection.getStart().nodeName,d=(tinymce.activeEditor.selection.getNode(),t[a].start),c=(d.match(/[a-z]+/),t[a].end);"undefined"==typeof d&&(d=""),"undefined"==typeof c&&(c=""),d.match(/[a-z]+/i)!=o.toLowerCase()&&tinymce.activeEditor.selection.setContent(t[a].start+i+t[a].end),d.match(/[a-z]+/i)==o.toLowerCase()&&(tinyMCE.activeEditor.dom.remove(tinymce.activeEditor.selection.getNode(o.toLowerCase())),tinymce.activeEditor.selection.setContent(i))}},values:e}})})}});