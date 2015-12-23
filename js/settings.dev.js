/**
 * AddQuicktag Script settings page of the plugin
 *
 * @version 2015-12-23
 * @since   06/19/2014
 * @package AddQuicktag Plugin
 */

jQuery( document ).ready( function( $ ) {

	$( 'input:checkbox.toggle' ).click( function( event ) {
		var i = $( this ).attr( 'value' ),
			sel = '#rmqtb' + i + ' input:checkbox';

		if ( this.checked ) {
			// Iterate each checkbox
			$( sel ).each( function() {
				this.checked = true;
			} );
		} else {
			// Iterate each checkbox
			$( sel ).each( function() {
				this.checked = false;
			} );
		}
	} );

	$( 'table.rmnlQuicktagSettings' ).delegate( 'td', 'mouseover mouseout', function( e ) {
		var hover = 'hover';

		if ( e.type == 'mouseover' ) {
			$( this ).parent().addClass( hover );
			$( 'table.rmnlQuicktagSettings colgroup' ).eq( $( this ).index() ).addClass( hover );
		} else {
			$( this ).parent().removeClass( hover );
			$( 'table.rmnlQuicktagSettings colgroup' ).eq( $( this ).index() ).removeClass( hover );
		}
	} );

	$( 'table.rmnlCoreQuicktagSettings' ).delegate( 'td', 'mouseover mouseout', function( e ) {
		var hover = 'hover';

		if ( e.type == 'mouseover' ) {
			$( this ).parent().addClass( hover );
			$( 'table.rmnlCoreQuicktagSettings colgroup' ).eq( $( this ).index() ).addClass( hover );
		} else {
			$( this ).parent().removeClass( hover );
			$( 'table.rmnlCoreQuicktagSettings colgroup' ).eq( $( this ).index() ).removeClass( hover );
		}
	} );

	$( 'table.rmnlCodeQuicktagSettings' ).delegate( 'td', 'mouseover mouseout', function( e ) {
		var hover = 'hover';

		if ( e.type == 'mouseover' ) {
			$( this ).parent().addClass( hover );
			$( 'table.rmnlCodeQuicktagSettings colgroup' ).eq( $( this ).index() ).addClass( hover );
		} else {
			$( this ).parent().removeClass( hover );
			$( 'table.rmnlCodeQuicktagSettings colgroup' ).eq( $( this ).index() ).removeClass( hover );
		}
	} );

	//$( 'table.rmnlQuicktagSettings' ).fixMe();
} );

/**
 * Kudos to: http://codepen.io/jgx/pen/wiIGc
 */
(
	function( $ ) {
		$.fn.fixMe = function() {
			return this.each( function() {

				var $this = $( this ),
					$t_fixed, $x, $th_width;

				function init() {
					$this.wrap( '<div class="container" />' );
					$t_fixed = $this.clone();
					$t_fixed.find( "tbody" ).remove().end().addClass( "fixed" ).insertBefore( $this );
					resizeFixed();
				}

				function resizeFixed() {
					$x = 0;
					$t_fixed.find( "th" ).each( function( index ) {
						$x++;
						$th_width = $this.find( "th" ).eq( index ).outerWidth();
						// The first 3 columns are sm
						if ( $x < 4 ) {
							$th_width = $th_width - 11;
						}
						$( this ).css( "width", $th_width + "px" );
					} );
				}

				function scrollFixed() {
					var offset = $( this ).scrollTop(),
						tableOffsetTop = $this.offset().top,
						tableOffsetBottom = tableOffsetTop + $this.height() - $this.find( "thead" ).height();
					if ( offset < tableOffsetTop || offset > tableOffsetBottom ) {
						$t_fixed.hide();
					} else if ( offset >= tableOffsetTop && offset <= tableOffsetBottom && $t_fixed.is( ":hidden" ) ) {
						$t_fixed.show();
					}
				}

				$( window ).resize( resizeFixed );
				$( window ).scroll( scrollFixed );
				init();

			} );
		};
	}
)( jQuery );