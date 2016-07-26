jQuery(document).ready(function($){


    var fixedHeader = $(wbHeaderFixed.fixed_class),
        mode = wbHeaderFixed.modality,
        breakpoint = wbHeaderFixed.breakpoint;

    var styleBefore = {
        'padding-top' : wbHeaderFixed.padding_before+'px',
        'padding-bottom': wbHeaderFixed.padding_before+'px',
        'background-color': wbHeaderFixed.color_before
    };

    var styleAfter = {
        'padding-top' : wbHeaderFixed.padding_after+'px',
        'padding-bottom': wbHeaderFixed.padding_after+'px',
        'background-color': wbHeaderFixed.color_after
    };

    $( window ).scroll(function() {
        console.log($(document).scrollTop());
    });

    switch(mode) {

        case 'beginning':

            fixedHeader.addClass('fixed-header-component');
            enterFixed();
            // now we can change the css checking if the position of the window is before or after the breakpoint specified by the user
            $( window ).scroll(function() {
                enterFixed();
            });

            break;


        case 'scrollUp':

            // da dove partiamo
            var initialScroll = $(document).scrollTop(),
                headerHeight = fixedHeader.outerHeight(),
                sensitiveness = 15; // voglio filtrare per i movimenti decisi
            $( window ).scroll(function() {
                enterScrollUp(headerHeight, sensitiveness);
            });

            break;


        case 'breakpoint':

            // First apply the correct padding and background
            fixedHeader.css(styleBefore).addClass('margin-top-animation');
            // at the document ready apply the correct class
            enterAfter();

            // then update the classes on window scroll
            $( window ).scroll(function() {
                enterAfter();
            });
    }


    function enterScrollUp(headerHeight, sensitiveness) {
        var currentScroll = $(this).scrollTop(), // lo scroll attuale
            delta = currentScroll - initialScroll; // la differenza fra lo scroll attuale e il punto di partenza
        if (Math.abs(delta) > sensitiveness) {
            if (delta > 0) { // se il delta è maggiore di 0 stiamo andando giù
                // fixedHeader.removeClass('fixed-header-component');
                fixedHeader.css('margin-top', headerHeight * -1);
            } else if (delta < 0) { // altrimenti stiamo andando su
                fixedHeader.addClass('fixed-header-component').css('margin-top', 0);
            }
        }
        // anyhow just reset margin and class if we are at the top
        if (currentScroll<=headerHeight) {
            fixedHeader.css({
                'margin-top': currentScroll,
                'z-index': 2,
                'position': 'relative'
            }).removeClass('fixed-header-component');
            $('#main-wrapper').css({
                'margin-top': $(this).scrollTop() * -1,
                'z-index': 1
            });
        }

        initialScroll = currentScroll; // a ogni scroll dobbiamo aggiornare la posizione iniziale attualizzandola con la posizione corrente
    }


    function enterFixed() {
        var scroll = $(document).scrollTop();
        if (scroll > breakpoint) {
            fixedHeader.css(styleAfter);
        } else {
            fixedHeader.css(styleBefore);
        }
    }


    function enterAfter() {
        var scroll = $(document).scrollTop(),
            headerHeight = fixedHeader.outerHeight();

        if (scroll < breakpoint && scroll <= headerHeight) {
            fixedHeader.removeClass('fixed-header-component');
            fixedHeader.css('margin-top', 0).css(styleBefore);
        } else if (scroll < breakpoint && scroll > headerHeight) {
            // fixedHeader.removeClass('fixed-header-component');
            fixedHeader.css('margin-top', headerHeight*-1);
        } else {
            fixedHeader.addClass('fixed-header-component').css('margin-top', 0).css(styleAfter);
        }
    }
});