/**
 * AddQuicktag Script to add buttons to html-editor
 * @since    2.0.0
 * @package  AddQuicktag Plugin
 */

jQuery(document).ready(function(a){if(typeof addquicktag_tags=="undefined")return;var b=addquicktag_tags["buttons"];if(typeof b!=="undefined"){for(var c=0;c<b.length;c++){if(typeof b[c].title=="undefined")b[c].title=" ";if(typeof b[c].end=="undefined")b[c].end="";if(typeof b[c].access=="undefined")b[c].access="";edButtons[edButtons.length]=new edButton(b[c].text.toLowerCase(),b[c].text,b[c].start,b[c].end,b[c].access,b[c].title)}}})
