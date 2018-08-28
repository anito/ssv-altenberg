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
            
            $(window).on('scroll', function(e) {

                var cl = '.scroll-top',
                    height = $(window).height(),
                    scrollTop = $(window).scrollTop();

                if( scrollTop > height ) {
                    div_handler( cl );
                } else {
                    $( cl ).addClass( 'dimmed' );
                }

                e.preventDefault();

            });

            var div_handler = function( cl ) {
                
                var str = cl,
                    tmpl,
                    el;
                
                el  = $( cl );
                
                if( !el.length ) {
                    
                    $('body').attr('id', 'the-top');

                    str = cl.replace(/(^\.|\.)/g, " ");
                    tmpl = `<div class="${str}"><a href="#the-top" class="scroll-top-inner fa icon fa-angle-up"></a></div>`;
                    
                    $('body').append( $(tmpl) );
                    
                    add_animate_scroll();
                }

                el.removeClass( 'dimmed' );
            };

        }
	
    add_animate_scroll();
    add_check_scroll();
    

})(jQuery)
