(function ($) {
    'use strict';

    $.fn.textify = function(bodyClasses, ignoredClasses) {

        var h3, hasMedia, exclude, include, selector;

        exclude = function () {

            var i, exclude, ret = '';

            for (i in bodyClasses) {
                exclude = bodyClasses[i];
                ret += `:not( ${exclude} )`;
            }
            return ret;
        }
        include = function(el, selectors) {
            var ret = [];
            selectors = selectors.replace(/\s/g,'').split(',');
            for(var i in bodyClasses) {
                include = bodyClasses[i];
                for(var j in selectors) {
                    ret.push(`${el}` + `${include}` + ' ' + selectors[j]);
                }
            }

            ret = ret.join();
            return ret;
        }

        selector = this.selector;
        $(selector + exclude() + ' h3').each( function(i) {

            var header = $('<div class="textbox-header-wrapper textbox-header-wrapper-id' + i +'"></div>'),
                content = $('<div class="textbox-content-wrapper"></div>'),
                h5 = null;
                
            h3 = $(this);

            // iterate through all the adjacent siblings no matter what, starting at H3
            $('~ *', h3).each( function(j) {

                var wrapper = $('<div class="woocommerce textbox-wrapper textbox-wrapper-id' + i + '"></div>'),
                    that = $(this);
                    
                if(that.is('h5') && $('+ h5', h3).length) { // found an H5

                    h5 = that;
                    
                } else if(that.is('p') || that.is('div')) { // Pees or the Div

                    that.addClass(`textbox-item textbox-id-${i}`);
                    
                    hasMedia = that.children('iframe, img').length;
                    if(hasMedia) {
                        that.addClass('media');
                    }
                    
                    
                    if($('+ p, + div', that).length ) { // there are more Pees to follow
                        
                        content.append(that); // keep the Pee / Div
                        
                    } else {
                        
                        h3.addClass('textbox-header textbox-id-' + i);
                        h3.before(wrapper); // remember the position of our H3 in the DOM
                        wrapper.append(h3); // move H3

                        if(h5) { // move H5
                            h5.addClass('textbox-subline textbox-id-' + i);
                            wrapper.append(h5);
                        }

                        if(!that.hasClass( ignoredClasses ) ) {
                            content.append(that);
                            that.addClass('textbox-item textbox-item-last');
                        }

                        wrapper.append(content); // move content wrapper
                        h3.wrap(header);

                        wrapper.addClass('processing').block({
                            message: 'Bitte warten',
                            overlayCSS: {
                                background: '#00000022',
                                opacity: 0.6,
                            }
                        });

                    }

                } else {
                    
                    return false;
                
                }

            });

        })

        $( document ).ready(function() {
            $('.textbox-wrapper').addClass('initialized').removeClass('processing').unblock();
            $(include(selector, 'h3 ~ h5, h3 ~ p, h3 ~ div')).css({'opacity': 1, 'visibility': 'visible'});
        });

    }

    $.fn.informify = function() {

        var selector = this.selector, h6, that, wrapper;
        
        $(selector + ' h6').each( function(i) {
            
            wrapper = $(`<div class="effect information-wrapper"></div>`);
            wrapper.addClass( `information-wrapper-id-${i}`);
            
            h6 = $(this);
            h6.before(wrapper);

            // iterate through all the adjacent siblings no matter what, starting at H6
            $('~ *', h6).each( function(j) {

                that = $(this);
                
                if(that.is('p') || that.is('div') ) { // found an Pee / Div
                    
                    var hasSiblings = $('+ p, + div', that).length; // check before we move it away
                    wrapper.append(that); // move the Pee / Div
                    
                    if( !hasSiblings ) { // the last occurence
                        
                        wrapper.prepend(h6); // move H6
                        wrapper.wrap('<div></div>'); // protect from textboxify
                        
                        return false;
                    }
                    
                } else {
                    
                    return false;
                            
                }
                
            })
            
            wrapper.prepend(h6); // h6 but no sibling found, wrap the single line header

        })
    }
    
})(jQuery)