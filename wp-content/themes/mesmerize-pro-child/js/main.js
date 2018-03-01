(function ($) {
    'use strict';
    
    var $overlay = $('<div>', {
        'class': 'js--overlay'
    }).appendTo('body'),
            isOpen = false,
            openClass = 'is--open',
            closableClass = 'is--closable',
            events = ['click', 'touchstart', 'MSPointerDown'].join('.overlay') + '.overlay',
            openOverlay = function (options) {
                if (isOpen) {
                    updateOverlay(options);
                    return;
                }
                isOpen = true;
                $overlay.addClass(openClass);
                if (options && options.closeOnClick !== false) {
                    $overlay.addClass(closableClass);
                }
                $overlay.on(events, $.proxy(onOverlayClick, this, options));
            },
            closeOverlay = function () {
                if (!isOpen) {
                    return;
                }
                isOpen = false;
                $overlay.removeClass(openClass + ' ' + closableClass);
                $overlay.off(events);
            },
            onOverlayClick = function (options) {
                if (options) {
                    if (typeof options.onClick === 'function') {
                        options.onClick.call($overlay);
                    }
                    if (options.closeOnClick === false) {
                        return;
                    }
                    if (typeof options.onClose === 'function' && options.onClose.call($overlay) === false) {
                        return;
                    }
                }
                closeOverlay();
            },
            updateOverlay = function (options) {
//            $overlay.toggleClass(closableClass, options.closeOnClick !== false);
                $overlay.off(events);
//            $overlay.on(events, $.proxy(onOverlayClick, this, options));
            };
    $overlay.on('mousewheel DOMMouseScroll', function (event) {
        event.preventDefault();
    });
    $.overlay = {
        open: openOverlay,
        close: closeOverlay,
        isOpen: function () {
            return isOpen;
        },
        getElement: function () {
            return $overlay;
        }
    };
})(jQuery);

(function ($) {

    
    var add_fb_div = function () {
        $('body').prepend('<div id="fb-root"></div>');
    }

    var add_background_image = function () {
        $('body').addClass('');
    }

    //pimp search aws field
    var add_aws_badge_el = function () {
        $('.aws-search-form').prepend('<span class="icon"><i class="fa fa-search"></i></span>');
    };

    //add disclaimer to MagicToolbox Container
    var disclaimer = "Abbildung kann ähnlich sein. Änderungen und Irrtümer vorbehalten";
    var add_image_disclaimer = function () {
        $('.MagicToolboxContainer').prepend('<span class="disclaimer">' + disclaimer + '</span>');
    }

    //add cart sidebar behaviour
    var add_mini_cart = function () {
        var sidebar = $('.cart-sidebar'),
            overlay = $('.js--overlay');
    
        $(document).on('click', '.mini-cart', function (e) {
            e.preventDefault();

            sidebar.addClass('is--open');
            $.overlay.open();
            return false;

        });
        $(overlay).on('click', function (e) {
            $.overlay.close();
            sidebar.removeClass('is--open');
            return false;
        });
//            $(document.body).on('added_to_cart', function(e, fragments, cart_hash, button) {
//                    e.preventDefault();
//                    $('.mini-cart').click()
//            });
        $(document.body).on('quantity_updated', function (e) {
            e.preventDefault();
            $('.mini-cart').click();
        });
        $(document.body).on('updated_wc_div', function (e) {
            e.preventDefault();
            $('.mini-cart').click();
        });
        $(document).on('click', '.continue-shopping', function (e) {
            e.preventDefault();

            sidebar.removeClass('is--open');
            $.overlay.close();
            return false;

        });

    };

    //add readmore behaviour
    var add_readmore = function () {
        $('p').readmore({
            speed: 75,
            moreLink: '<a href="#">[mehr lesen]</a>',
            lessLink: '<a href="#">[weniger lesen]</a>'
        });
    };

    // jQuery.blockUI defaults
    var add_jQuery_blocking = function () {
        $.blockUI.defaults.css = {
            message: "Hi"
        };
    };
    
    var add_open_links_in_new_window_icon = function() {
            var new_window, array=[];
            $('.document-gallery').each( function() {
                new_window = $(this).data('shortcode')['new_window'];
                if( new_window ) array.push($(this));
            })
            
            $(array).each(function() {
                $(this).find('.document-icon').addClass('new-window');
            })
        }
        
    
    // depends on utils.js in child-theme/js
    var add_boxify = function() {
        
        var bodyClasses = [
            '.woocommerce-checkout',
            '.woocommerce-multistep-checkout',
            '.woocommerce-cart',
            '.blog'
        ],
        
        ignoreClasses = [
            'page-link'
        ]
        
        $('body').informify();
        $('body').textify(bodyClasses, ignoreClasses);
        
    }

    add_fb_div();
    add_background_image();
    add_aws_badge_el();
    add_mini_cart();
    add_image_disclaimer();
    add_jQuery_blocking();
    add_open_links_in_new_window_icon();
    add_boxify()
//        add_readmore();
    

})(jQuery)
