/**
 * AddQuicktag Script to add buttons to html-editor
 * 
 * @package  AddQuicktag Plugin
 * @author   Frank Bueltge <frank@bueltge.de>
 * @version  05/22/2014
 * @since    2.0.0
 */

jQuery(document).ready(function(t){if("undefined"!=typeof addquicktag_tags&&"undefined"!=typeof addquicktag_post_type&&"undefined"!=typeof addquicktag_pt_for_js){var e=addquicktag_tags.buttons;if("undefined"!=typeof e&&t.inArray("addquicktag_post_type",addquicktag_pt_for_js))for(var d=0;d<e.length;d++)1===parseInt(e[d][addquicktag_post_type])&&("undefined"==typeof e[d].title&&(e[d].title=" "),"undefined"==typeof e[d].end&&(e[d].end=""),"undefined"==typeof e[d].access&&(e[d].access=""),QTags.addButton(e[d].text.toLowerCase(),e[d].text,e[d].start,e[d].end,e[d].access,e[d].title))}});