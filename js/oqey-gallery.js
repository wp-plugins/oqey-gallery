/*
	jQuery oqeyGallery v1.0 - http://www.oqeysites.com
	Copyright (c) 2011 oqeysites.com | Dorin Darii
	This plugin available for use in all personal or commercial projects under both MIT and GPL licenses.
*/

(function($){  
    $.fn.extend({
        oqeyGallery: function(options) {  
        
            //Set the default values, use comma to separate the settings 
            var defaults = {  
				oqeynext : false,
				oqeyprev : false
            }  
            
            var options =  $.extend(defaults, options);  
                        
            return this.each(function() {  
                var o = options;  
                var obj = $(this);
				var slide = obj.find('.oqeyimgdiv:eq(0)');

                // Determine the length of items here and calculate the number hidden
                var oqeyLength = obj.children().length;
                var oqeyList = obj.children();
				
				//$('.oqeyimgdiv').first()
				
				var x = obj.find('.oqeyimgdiv:eq(0)');
				var n = obj.find('.next');
				var p = obj.find('.prev');
				x.css("display", "block"); // show the first children
		
			
			    n.click(function() {
				$(slide).fadeOut( 1 );
                slide = $(slide).next();
				$(slide).fadeIn( 500 );
				});
			
			    p.click(function() {
				$(slide).fadeOut( 1 );
                slide = $(slide).prev();
				$(slide).fadeIn( 500 );
			    });
				
			function gotoSlide() { alert("am dat click pe next"); };
			
			//obj.('.prev').click(previousslide);
		
         /*	       
			
		nextslide: function() {
			alert("next was clicked");
		};
		
		previousslide: function() {
			alert("prev button");
		};
			*/	
				
				/*
				if(o.oqeynext){
				alert("next");
				var x = obj.find('.oqeyimg:eq(3)');
				alert(x);
				}
				*/
				
				
				
				//if(o.oqeynext){
				//alert("prev");
				
				//}
		
                //oqeyList.each(function(index){
		        
				
				//});

               /* 
			   
var dor = $('.archi li.first');

function next() {
	dor.fadeOut( 10 );
	if ( dor.attr('class') == 'last' )
		dor = $('.archi li.first');
	else
		dor = dor.next();
	dor.fadeIn( 500 );
}

function prev() {
	dor.fadeOut( 10 );
	if ( dor.attr('class') == 'first' )
		dor = $('.archi li.last');
	else
		dor = dor.prev();
	dor.fadeIn( 500 );
}
                // This is where I toggle the text
                jQuery("a.view").live("click", function(e){
    
                    if (jQuery(this).text()==o.showText) {
                    jQuery(this).text(o.hideText);
                }
                else {
                    jQuery(this).text(o.showText);
                }
                    jQuery('.hidden').toggle();
                    return false;
               });                
              */
			
			  
			  
            }); //sfarsit de return

        } 
    }); 
})(jQuery);

// JavaScript Document