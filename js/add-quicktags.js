/**
 * AddQuicktag Script to add buttons to html-editor
 * 
 * @package  AddQuicktag Plugin
 * @author   Frank Bueltge <frank@bueltge.de>
 * @version  07/11/2012
 * @since    2.0.0
 */

jQuery(document).ready(function(a){if(typeof addquicktag_tags=="undefined")return;if(typeof addquicktag_post_type=="undefined")return;if(typeof addquicktag_pt_for_js=="undefined")return;var b=addquicktag_tags["buttons"];if(typeof b=="undefined")return;if(a.inArray("addquicktag_post_type",addquicktag_pt_for_js)){for(var c=0;c<b.length;c++){if(1===parseInt(b[c][addquicktag_post_type])){if(typeof b[c].title=="undefined")b[c].title=" ";if(typeof b[c].end=="undefined")b[c].end="";if(typeof b[c].access=="undefined")b[c].access="";QTags.addButton(b[c].text.toLowerCase(),b[c].text,b[c].start,b[c].end,b[c].access,b[c].title)}}}})
