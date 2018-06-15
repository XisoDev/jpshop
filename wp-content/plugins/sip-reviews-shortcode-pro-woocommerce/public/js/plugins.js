// Avoid `console` errors in browsers that lack a console.
(function() {
    var method;
    var noop = function () {};
    var methods = [
        'assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'error',
        'exception', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log',
        'markTimeline', 'profile', 'profileEnd', 'table', 'time', 'timeEnd',
        'timeStamp', 'trace', 'warn'
    ];
    var length = methods.length;
    var console = (window.console = window.console || {});

    while (length--) {
        method = methods[length];

        // Only stub undefined methods.
        if (!console[method]) {
            console[method] = noop;
        }
    }
}());

// Place any jQuery/helper plugins in here.
/*Input placeholder*/
function placeholder() {
    var input = document.createElement('input');
    if (('placeholder' in input) == false) {
        $('[placeholder]').focus(function () {
            var i = $(this);
            if (i.val() == i.attr('placeholder')) {
                i.val('').removeClass('placeholder');
                if (i.hasClass('password')) {
                    i.removeClass('password');
                    this.type = 'password';
                }
            }
        }).blur(function () {
            var i = $(this);
            if (i.val() == '' || i.val() == i.attr('placeholder')) {
                if (this.type == 'password') {
                    i.addClass('password');
                    this.type = 'text';
                }
                i.addClass('placeholder').val(i.attr('placeholder'));
            }
        }).blur().parents('form').submit(function () {
            $(this).find('[placeholder]').each(function () {
                var i = $(this);
                if (i.val() == i.attr('placeholder'))
                    i.val('');
            })
        });
    }
	
	$('[type="text"], [type="password"], textarea, select.form-select').focus(function() {
		var input = $(this);
		input.parents('[data-type="focus"]').addClass('focus');
	}).blur(function() {
		var input = $(this);
		input.parents('[data-type="focus"]').removeClass('focus');
	});
}


(function( $ ) {
    'use strict';  
    
    jQuery(function($) {
        $('.example').barrating({theme: 'fontawesome-stars'});
    });

    jQuery(document).ready(function($) {
        $("img.avatar").addClass("thumb");
        $('.sip-star-rating').each(function () {
            //alert($(this).text());
            var value = $(this).text();
            $('.rating-readonly-'+value).barrating({theme: 'fontawesome-stars', readonly:true, initialRating: value });
        });
    });

    jQuery( function( $ ) { 
        $( 'body' )
            .on( 'click', '.sip-respond p.stars a', function() {
                var $star = $( this ),
                $rating = $( this ).closest( '.sip-respond' ).find( '#rating' );
                $rating.val( $star.text() );
                $star.siblings( 'a' ).removeClass( 'active' );
                $star.addClass( 'active' );
                $("p.stars").addClass("selected");
                return false;
            })
    });

    jQuery('.example').barrating('show', {
        onSelect:function(value, text) {
            //your code goes here.
            $('#div-to-be-revealed').toggleClass('invisible')
            $('.show').slideDown();
        }
    });

})( jQuery );

/*End*/