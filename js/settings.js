/**
 * AddQuicktag Script settings page of the plugin
 * @since    08/02/2012
 * @package  AddQuicktag Plugin
 */

jQuery(document).ready(function(a){a("input:checkbox.toggle").click(function(b){var c=a(this).attr("value");if(this.checked){a("#rmqtb"+c+" input:checkbox").each(function(){this.checked=true})}else{a("#rmqtb"+c+" input:checkbox").each(function(){this.checked=false})}})})