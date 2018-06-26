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
                
                this.toggle_elements();
                
                $(document).on( 'click', '.opt-user', this.toggle_elements );
                
            },
            
            toggle_elements: function() {
                
                var hide = $('.opt-user').attr('checked');
                var hidden = toggle('[for="sp_team"], [for="sp_staff"]', !hide);
                if(hidden) {
                    this.form.trigger('reset');
                }
                
            }
        
    }
    
    register_form.init( '#registerform' )
    
    
})(jQuery);