/**
 * AddQuicktag Script to add buttons to html-editor
 * @since    2.0.0
 * @package  AddQuicktag Plugin
 */

jQuery(document).ready(function(a){if(typeof addquicktag_tags=="undefined")return;if(typeof addquicktag_post_type=="undefined")return;if(typeof addquicktag_pt_for_js=="undefined")return;var b=Array("test","test2");a.each(addquicktag_pt_for_js,function(a,c){b.push(c)});var c=addquicktag_tags["buttons"];if(typeof c=="undefined")return;if(a.inArray("addquicktag_post_type",addquicktag_pt_for_js)){for(var d=0;d<c.length;d++){if(1==c[d][addquicktag_post_type]){if(typeof c[d].title=="undefined")c[d].title=" ";if(typeof c[d].end=="undefined")c[d].end="";if(typeof c[d].access=="undefined")c[d].access="";edButtons[edButtons.length]=new edButton(c[d].text.toLowerCase(),c[d].text,c[d].start,c[d].end,c[d].access,c[d].title)}}}})
