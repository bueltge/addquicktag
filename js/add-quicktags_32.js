/**
 * AddQuicktag Script to add buttons to html-editor for WordPress version smaller 3.3
 * @since    2.0.0
 * @package  AddQuicktag Plugin
 */

jQuery(document).ready(function(a){if(typeof addquicktag_tags=="undefined")return;var b=addquicktag_tags["buttons"];var c=document.getElementById("ed_toolbar");if(typeof b!=="undefined"&&c){var d;for(var e=0;e<b.length;e++){if(typeof b[e].title=="undefined")b[e].title=" ";if(typeof b[e].end=="undefined")b[e].end="";if(typeof b[e].access=="undefined")b[e].access="";wpaqNr=edButtons.length;edButtons[wpaqNr]=new edButton(b[e].text.toLowerCase(),b[e].text,b[e].start,b[e].end,b[e].access,b[e].title);d=c.lastChild;while(d.nodeType!=1){d=d.previousSibling}d=d.cloneNode(true);d.id="ed_"+wpaqNr;d._idx=wpaqNr;d.value=b[e].text;d.title=b[e].title;d.onclick=function(){edInsertTag(edCanvas,this._idx);return false};c.appendChild(d)}}})
