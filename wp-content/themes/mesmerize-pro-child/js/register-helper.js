(function ($) {
    'use strict';
    
    var toggle = function( el, hide ) {
        
        $(el).toggleClass('hide', hide);
        
    };
    
    var register_form = {
        
            init: function( form ) {
                
                this.form = $(form);
                this.toggle_elements = this.toggle_elements.bind(this);
                
                
                console.log($('.opt-user').attr('checked'));
                
                this.toggle_elements();
                
                $(document).on( 'click', '.opt-user', this.toggle_elements );
                
            },
            
            toggle_elements: function() {
                
                var hide = $('.opt-user').attr('checked');
                console.log(hide);
                toggle('[for="sp_team"], [for="sp_staff"]', !hide);
                
            }
        
    }
    
    register_form.init( '#registerform' )
    
    
})(jQuery);