/**
 * AddQuicktag Script settings page of the plugin
 * @since    06/16/2014
 * @package  AddQuicktag Plugin
 */

jQuery(document).ready(function(e){e("input:checkbox.toggle").click(function(){var t=e(this).attr("value"),s="#rmqtb"+t+" input:checkbox";e(s).each(this.checked?function(){this.checked=!0}:function(){this.checked=!1})}),e("table.rmnlQuicktagSettings").delegate("td","mouseover mouseout",function(t){var s="hover";"mouseover"==t.type?(e(this).parent().addClass(s),e("table.rmnlQuicktagSettings colgroup").eq(e(this).index()).addClass(s)):(e(this).parent().removeClass(s),e("table.rmnlQuicktagSettings colgroup").eq(e(this).index()).removeClass(s))}),e("table.rmnlCoreQuicktagSettings").delegate("td","mouseover mouseout",function(t){var s="hover";"mouseover"==t.type?(e(this).parent().addClass(s),e("table.rmnlCoreQuicktagSettings colgroup").eq(e(this).index()).addClass(s)):(e(this).parent().removeClass(s),e("table.rmnlCoreQuicktagSettings colgroup").eq(e(this).index()).removeClass(s))})});