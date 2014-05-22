/**
 * AddQuicktag Script settings page of the plugin
 * @since    05/22/2014
 * @package  AddQuicktag Plugin
 */

jQuery(document).ready(function(e){e("input:checkbox.toggle").click(function(){var t=e(this).attr("value"),c="#rmqtb"+t+" input:checkbox";e(c).each(this.checked?function(){this.checked=!0}:function(){this.checked=!1})}),e("table.rmnlQuicktagSettings").delegate("td","mouseover mouseout",function(t){var c="hover";"mouseover"==t.type?(e(this).parent().addClass(c),e("colgroup").eq(e(this).index()).addClass(c)):(e(this).parent().removeClass(c),e("colgroup").eq(e(this).index()).removeClass(c))})});