jQuery(document).ready(function ($) {

    if ( $('.wrap h1').length ) {
        $('.layout-switcher').appendTo('.wrap h1:first');
    }
    else if ( $('.wrap h2').length ) {
        $('.layout-switcher').appendTo('.wrap h2:first');
    }
    else if ( $('.wrap div').length ) {
        $('.layout-switcher').appendTo('.wrap div:first');
    }
});
