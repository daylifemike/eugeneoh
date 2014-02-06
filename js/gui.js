(function($) {
    "use strict";

    $('head').append(document.createElement('style'));
    var stylesheet = document.styleSheets[document.styleSheets.length-1];

    var photo_prefix = 'photo';
    var photo_options = ['scale', 'crop'];
    var eyo = {
        photo : photo_options[0],
        background : '#000000',
        ribbon : '#d80000'
    };
    var gui = new dat.GUI({ autoPlace: false });
    var $gui = $('<div id="gui"></div>');
    var $body = $('body');
    var $thumbnails = $('.thumbnail');

    $body.append( $gui );
    $gui.append( gui.domElement );


    gui.add( eyo, 'photo', photo_options )
        .onFinishChange(function(value) {
            updateBody( photo_prefix, value )
        });

    gui.addColor( eyo, 'background' )
        .onChange(function(value) {
            $thumbnails.css('background', value);
        });

    gui.addColor( eyo, 'ribbon' )
        .onChange(function(value) {
            updatePseudo(value);
        });

    for (var key in eyo) {
        var prefix = '';

        switch (key) {
            case 'photo':
                prefix = photo_prefix;
                updateBody( prefix, eyo[key] );
                break;

            case 'background':
                $thumbnails.css('background', eyo[key]);
                break;
        }

        // updateBody( prefix, eyo[key] );
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


    function updatePseudo (value) {
        // FIXME: escape quotes in text
        //var $reel = $('.reel:before');
        var rule = '.reel:before { border-color: ' + value + '; border-right-color:transparent; }';
        var index = 0;

        if (stylesheet.insertRule) {
            stylesheet.insertRule(rule, index);
        }
        if (stylesheet.deleteRule && index+1 < stylesheet.cssRules.length) {
            stylesheet.deleteRule(index+1);
        }
    }

}(jQuery));