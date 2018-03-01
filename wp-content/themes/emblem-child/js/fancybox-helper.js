/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

( function( $ ) {
		
		jQuery('.metaslider' ).each( function( galleryIndex ) {
				
				jQuery('.slides img[class*="slide-"]', $(this) ).each( function() {
				
				
						if( 1 > $(this).find('a').length ) {

								var c, caption, regex, subst, inner = $( this ), outer;

								src_big = function( src ) {

										regex = /(.*)(-\d{1,}x\d{1,})(.)(jpg|jpeg|png|gif)/g;
										subst = '$1$3$4';

										return src.replace(regex, subst);

								}
								
								caption = $(this).parents( '[class*="slide-"]').find('.caption' ).text();
								outer = '<a href="' + src_big( this.src ) + '" data-fancybox="gallery-' + galleryIndex + '" data-caption="' + caption  + '">';
//							
								inner.wrap( outer );

						};
						
				});
			
		});
		
} )(jQuery)
