/**
 * AddQuicktag Script to add listbox to visual-editor
 * 
 * @package  AddQuicktag Plugin
 * @author   Frank Bueltge <frank@bueltge.de>
 * @version  05/22/2014
 * @since    2.3.0
 */

jQuery(document).ready(function(t){if("undefined"!=typeof addquicktag_tags&&"undefined"!=typeof addquicktag_post_type&&"undefined"!=typeof addquicktag_pt_for_js&&-1!=t.inArray(addquicktag_post_type,addquicktag_pt_for_js)){var e=0,a=0,i=0;for(i=0;i<addquicktag_tags.buttons.length;i++)1===parseInt(addquicktag_tags.buttons[i].visual)&&(e=addquicktag_tags.buttons[i].visual),1===parseInt(addquicktag_tags.buttons[i][addquicktag_post_type])&&(a=addquicktag_tags.buttons[i][addquicktag_post_type]);1===parseInt(e)&&1===parseInt(a)&&tinymce.PluginManager.add("rmnlQuicktagSettings_tmce",function(t){t.addButton("rmnlQuicktagSettings_tmce",function(){var t=addquicktag_tags.buttons,e=[],a=0;for(a=0;a<t.length;a++)1===parseInt(t[a][addquicktag_post_type])&&1==t[a].visual&&e.push({text:t[a].text,value:String(a)});return{type:"listbox",text:"Quicktags",label:"Select :",fixedWidth:!0,onselect:function(e){var e=e.control._value,a=!1;if("undefined"!=typeof tinymce.activeEditor.selection.getContent()&&(a=!0),1==a){var i=tinymce.activeEditor.selection.getContent(),n=tinymce.activeEditor.selection.getStart().nodeName,d=(tinymce.activeEditor.selection.getNode(),t[e].start),o=(d.match(/[a-z]+/),t[e].end);"undefined"==typeof o&&(o=""),d.match(/[a-z]+/i)!=n.toLowerCase()&&tinymce.activeEditor.selection.setContent(t[e].start+i+t[e].end),d.match(/[a-z]+/i)==n.toLowerCase()&&(tinyMCE.activeEditor.dom.remove(tinymce.activeEditor.selection.getNode(n.toLowerCase())),tinymce.activeEditor.selection.setContent(i))}},values:e}})})}});