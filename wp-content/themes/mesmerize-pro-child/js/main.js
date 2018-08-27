(function ($) {
    'use strict';
    
    var add_animate_scroll = function() {
            
        $('a[href^=#]').on('click', function(e){

            var href = $(this).attr('href');

            if(typeof $(href).offset() == 'undefined') return;

            $('html, body').animate({
                scrollTop:$(href).offset().top
            },'slow');

            e.preventDefault();

        });

    };
    
    var add_check_scroll = function() {
            
            $('body').attr('id', 'the-top');
            
            $(window).on('scroll', function(e) {

                var cl = '.scroll-wrapper',
                    height = $(window).height(),
                    scrollTop = $(window).scrollTop();

                if( scrollTop > height ) {
                    make_div( cl );
                } else {
                    $( cl ).addClass( 'hide' );
                }

                e.preventDefault();

            });

            var make_div = function( cl ) {
                
                var str = cl,
                    tmpl,
                    el;
                
                el  = $( cl );
                str = cl.replace(/(^\.|\.)/g, " ");
                tmpl = `<div class="${str}"><a href="#the-top" class="scroll-top fa icon fa-chevron-up"></a></div>`;
                
                if( !el.length ) {
                    $('body').append( $(tmpl) );
                    add_animate_scroll();
                }

                el.removeClass( 'hide' );
            };

        }
	
    add_animate_scroll();
    add_check_scroll();
    

})(jQuery)
