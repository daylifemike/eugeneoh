(function($) {
    "use strict";

    var badge_prefix = 'reel';
    var badge_options = ['ribbon', 'band', 'cover', 'curl'];
    var hover_prefix = 'caption';
    var hover_options = ['slide', 'fade', 'partial-fade', 'zoom', 'zoomslide'];
    var eyo = {
        badge : badge_options[0],
        hover : hover_options[0]
    };
    var gui = new dat.GUI({ autoPlace: false });
    var $gui = $('<div id="gui"></div>');
    var $body = $('body');

    $body.append( $gui );
    $gui.append( gui.domElement );


    gui.add( eyo, 'badge', badge_options )
        .onFinishChange(function(value) {
            updateBody( badge_prefix, value )
        });

    gui.add( eyo, 'hover', hover_options )
        .onFinishChange(function(value) {
            updateBody( hover_prefix, value )
        });


    for (var key in eyo) {
        var prefix = '';

        switch (key) {
            case 'badge':
                prefix = badge_prefix;
                break;

            case 'hover':
                prefix = hover_prefix;
                break;
        }

        updateBody( prefix, eyo[key] );
    }


    function updateBody (key, value) {
        var re = new RegExp( key + "-[\\w-]+" );
        var classes = $body.attr('class') || '';
        var matches = classes.match(re);

        if (matches) {
            $body.removeClass( matches[0] );
        }

        $body.addClass( key + '-' + value );
    }

}(jQuery));