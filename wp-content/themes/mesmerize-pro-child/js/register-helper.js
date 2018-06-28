(function ($) {
    'use strict';
    
    var toggle = function( el, hide ) {
        
        $(el).toggleClass('hide', hide);
        return $(el).hasClass('hide');
        
    };
    
    var register_form = {
        
            init: function( form ) {
                
                this.form = $(form);
                this.toggle_elements = this.toggle_elements.bind(this);
                
                if( !$('.opt-ssv_user').attr('checked') ) toggle('[for="sp_team"], [for="sp_staff"]', true);
                
                $(document).on( 'click', '.opt-ssv_user', this.toggle_elements );
                
            },
            
            toggle_elements: function() {
                
                var hide = $('.opt-ssv_user').attr('checked');
                if(hide) {
//                    this.form.trigger('reset');
                    $('[name=sp_staff]').prop( 'value', null).removeAttr('checked');
                    $('[name=sp_team]').prop( 'value', -1);
                }
                toggle('[for="sp_team"], [for="sp_staff"]', !hide);
                
            }
        
    }
    
    register_form.init( '#registerform' )
    
    
})(jQuery);