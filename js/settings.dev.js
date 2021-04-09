/**
 * AddQuicktag Script settings page of the plugin.
 *
 * @version 2021/04/08
 * @since   2014/06/19
 * @package AddQuicktag Plugin
 */

(function ($) {
	$('input:checkbox.toggle').on('click', function () {
		var i = $(this).attr('value'),
			sel = '#rmqtb' + i + ' input:checkbox';

		$(sel).prop('checked', this.checked);
	});

	$('table.rmnlQuicktagSettings').on('mouseover mouseout', 'td', function (e) {
		var hover = 'hover';

		if (e.type === 'mouseover') {
			$(this).parent().addClass(hover);
			$('table.rmnlQuicktagSettings colgroup').eq($(this).index()).addClass(hover);
		} else {
			$(this).parent().removeClass(hover);
			$('table.rmnlQuicktagSettings colgroup').eq($(this).index()).removeClass(hover);
		}
	});

	$('table.rmnlCoreQuicktagSettings').on('mouseover mouseout', 'td', function (e) {
		var hover = 'hover';

		if (e.type === 'mouseover') {
			$(this).parent().addClass(hover);
			$('table.rmnlCoreQuicktagSettings colgroup').eq($(this).index()).addClass(hover);
		} else {
			$(this).parent().removeClass(hover);
			$('table.rmnlCoreQuicktagSettings colgroup').eq($(this).index()).removeClass(hover);
		}
	});

	$('table.rmnlCodeQuicktagSettings').on('mouseover mouseout', 'td', function (e) {
		var hover = 'hover';

		if (e.type === 'mouseover') {
			$(this).parent().addClass(hover);
			$('table.rmnlCodeQuicktagSettings colgroup').eq($(this).index()).addClass(hover);
		} else {
			$(this).parent().removeClass(hover);
			$('table.rmnlCodeQuicktagSettings colgroup').eq($(this).index()).removeClass(hover);
		}
	});
})(jQuery);

/**
 * Kudos to: http://codepen.io/jgx/pen/wiIGc
 */
(function ($) {
	'use strict';
	$.fn.fixMe = function () {
		return this.each(function () {

			var $this = $(this),
				$t_fixed, $x, $th_width;

			function init() {
				$this.wrap('<div class="container" />');
				$t_fixed = $this.clone();
				$t_fixed.find("tbody").remove().end().addClass("fixed").insertBefore($this);
				resizeFixed();
			}

			function resizeFixed() {
				$x = 0;
				$t_fixed.find("th").each(function (index) {
					$x++;
					$th_width = $this.find("th").eq(index).outerWidth();
					// The first 3 columns are sm
					if ($x < 4) {
						$th_width = $th_width - 11;
					}
					$(this).css("width", $th_width + "px");
				});
			}

			function scrollFixed() {
				var offset = this.scrollTop(),
					tableOffsetTop = $this.offset().top,
					tableOffsetBottom = tableOffsetTop + $this.height() - $this.find("thead").height();
				if (offset < tableOffsetTop || offset > tableOffsetBottom) {
					$t_fixed.hide();
				} else if (offset >= tableOffsetTop && offset <= tableOffsetBottom && $t_fixed.is(":hidden")) {
					$t_fixed.show();
				}
			}

			$(window).on('resize', resizeFixed);
			$(window).on('scroll', scrollFixed);
			init();

		});
	};
})(jQuery);
