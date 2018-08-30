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
            
            var timer_id;
            
            $('body').attr('id', 'the-top');
            
            $(window).on('scroll', function(e) {

                var cl = '.scroll-top',
                    height = $(window).height(),
                    scrollTop = $(window).scrollTop();

                if( scrollTop > height ) {
                    div_handler( cl );
                    
                    $( cl ).removeClass( 'dimmed' ).addClass( 'active' );
                    
                    clearTimeout( timer_id );
                    timer_id = setTimeout( function() {
                        $( cl ).addClass( 'dimmed' );
                    }, 4000);
                    
                } else {
                    $( cl ).addClass( 'away' ).removeClass( 'active' );
                    
                    if( timer_id ) {
                        clearTimeout(timer_id);
                        timer_id = null;
                    }
                }

                e.preventDefault();

            });

            var div_handler = function( cl ) {
                
                var str = cl,
                    tmpl,
                    el;
                
                el  = $( cl );
                
                if( !el.length ) {
                    

                    str = cl.replace(/(^\.|\.)/g, " ");
                    tmpl = `<div class="${str} away"><a href="#the-top" class="scroll-top-inner fa icon fa-angle-up"></a></div>`;
                    
                    $('body').append( $(tmpl) );
                    
                    add_animate_scroll();
                }

                el.removeClass( 'away' );
            };

        }
	
    add_animate_scroll();
    add_check_scroll();
    

})(jQuery)
