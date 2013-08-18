/**
 * AddQuicktag Script to add buttons to html-editor for WordPress version smaller 3.3
 * @since    2.0.0
 * @package  AddQuicktag Plugin
 */

jQuery(document).ready(function($){if(typeof addquicktag_tags=="undefined")return;var tags=addquicktag_tags["buttons"];var wpaqToolbar=document.getElementById("ed_toolbar");if(typeof tags!=="undefined"&&wpaqToolbar){var wpaqBut;for(var i=0;i<tags.length;i++){if(typeof tags[i].title=="undefined")tags[i].title=" ";if(typeof tags[i].end=="undefined")tags[i].end="";if(typeof tags[i].access=="undefined")tags[i].access="";wpaqNr=edButtons.length;edButtons[wpaqNr]=new edButton(tags[i].text.toLowerCase(),
tags[i].text,tags[i].start,tags[i].end,tags[i].access,tags[i].title);wpaqBut=wpaqToolbar.lastChild;while(wpaqBut.nodeType!=1)wpaqBut=wpaqBut.previousSibling;wpaqBut=wpaqBut.cloneNode(true);wpaqBut.id="ed_"+wpaqNr;wpaqBut._idx=wpaqNr;wpaqBut.value=tags[i].text;wpaqBut.title=tags[i].title;wpaqBut.onclick=function(){edInsertTag(edCanvas,this._idx);return false;};wpaqToolbar.appendChild(wpaqBut);}}});