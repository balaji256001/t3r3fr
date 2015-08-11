jQuery(document).ready(function($){
    var layzr = new Layzr({
        container: null,
        selector: '[data-layzr]',
        attr: 'data-layzr',
        retinaAttr: 'data-layzr-retina',
        bgAttr: 'data-layzr-bg',
        hiddenAttr: 'data-layzr-hidden',
        threshold: 0,
        callback: function(){
            console.log("Image loaded!")
        }
    });
});