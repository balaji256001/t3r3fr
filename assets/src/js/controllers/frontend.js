module.exports = Backbone.Model.extend({
    initialize: function() {
        "use strict";
        console.log("It'frontend time!");
        this.do_stuff(jQuery);
    },
    do_stuff: function($){
        "use strict";
        /*
         * Bootstrapping html elements
         */
        $('input[type=text]').addClass('form-control');
        $('input[type=select]').addClass('form-control');
        $('input[type=email]').addClass('form-control');
        $('input[type=tel]').addClass('form-control');
        $('textarea').addClass('form-control');
        $('select').addClass('form-control');
        $('input[type=submit]').addClass('btn btn-primary');
        $('button[type=submit]').addClass('btn btn-primary');
        // Gravity Form
        $('.gform_button').addClass('btn btn-primary btn-lg').removeClass('gform_button button');
        $('.validation_error').addClass('alert alert-danger').removeClass('validation_error');
        $('.gform_confirmation_wrapper').addClass('alert alert-success').removeClass('gform_confirmation_wrapper');
        // Tables
        $('table').addClass('table');
        /*
         * These will make any element that has data-wbShow\wbHide="<selector>" act has visibily toggle for <selector>
         */
        $('[data-wbShow]').on('click', function() {
            var itemToShow = $($(this).attr("data-trgShow"));
            if (itemToShow.hasClass('modal')) {
                $('.modal').each(function(index) {
                    $(this).modal("hide");
                });
                itemToShow.modal("show");
            } else {
                itemToShow.show();
            }
        });
        $('[data-wbHide]').on('click', function() {
            var itemToShow = $($(this).attr("data-trgHide"));
            if (itemToShow.hasClass('modal')) {
                itemToShow.modal("hide");
            } else {
                itemToShow.hide();
            }
        });
        /*
         * INIT CONTACT FORM
         */
        var ContactFormView = require("../views/contactForm.js"),
            ContactFormModel = require("./contactForm.js"),
            $contactForm = $("[data-contactForm]");
        //Init search windows
        if ($contactForm.length > 0) {
            var contactWindow = new ContactFormView({
                model: new ContactFormModel(),
                el: $contactForm
            });
        }
        /*
         * MOBILE ACTIONS
         */
        if (wbData.isMobile) {
            var fs = require("FastClick");
            //swipe = require("TouchSwipe");
            //http://getbootstrap.com/getting-started/#support
            if (navigator.userAgent.match(/IEMobile\/10\.0/)) {
                var msViewportStyle = document.createElement('style')
                msViewportStyle.appendChild(
                    document.createTextNode(
                        '@-ms-viewport{width:auto!important}'
                    )
                );
                document.querySelector('head').appendChild(msViewportStyle);
            }
            fs.FastClick.attach(document.body);
            /*$("body").swipe({
                swipeRight: function(event, direction, distance, duration, fingerCount) {
                    if ($(".navbar-mobile-collapse").css('right') == '0px') {
                        $('button.navbar-toggle').trigger('click');
                    }
                },
                swipeLeft: function(event, direction, distance, duration, fingerCount) {
                    if ($(".navbar-mobile-collapse").css('right') == '0px') {
                        $('button.navbar-toggle').trigger('click');
                    }
                }
            });*/
            //Disable for Metaslider
            $(".metaslider").addClass("noSwipe");
        }
        /*
         * WOOCOMMERCE
         */
        $('.woocommerce a.button').addClass('btn');
        $('.woocommerce a.add_to_cart_button').removeClass('btn-primary');
        $('.woocommerce .single_add_to_cart_button').removeClass('btn-primary');
        $('.woocommerce a.add_to_cart_button').addClass('btn-success');
        $('.woocommerce .single_add_to_cart_button').addClass('btn-success');
        $('.woocommerce a.button').removeClass('button');
        $('.woocommerce table.cart').addClass('table-striped');
        $('.woocommerce table.cart td.actions input.button').addClass('btn');
        $('.woocommerce table.cart td.actions input.button').addClass('btn-default');
        $('.woocommerce table.cart td.actions input.button').removeClass('button');
        $('.wc-proceed-to-checkout a').addClass('btn btn-lg btn-primary');
        //Enabling tab navigation
        /*$("[role=tablist] li a").each(function() {
         var self = this;
         $(this).on("click", function(e) {
         e.preventDefault();
         self.tab("show");
         });
         });*/
        $(".nav-tabs li:first-child").addClass("active");
        $(".tab-content .tab-pane:first-child").addClass("active");

        $( document.body ).on( 'updated_checkout', function(){
            $('.woocommerce-checkout .woocommerce-checkout-review-order-table').addClass('table');
            $('.woocommerce-checkout-payment input[type=submit]').addClass('btn btn-lg btn-primary');
        });
    }
});
