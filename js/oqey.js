var oqeyname  = 1;
var firstplay = true;

function checkActivePlayer(newname) {
    
    if (!firstplay) {
        
        getFlashMovie("oqeygallery" + oqeyname).sendIDToFlash(newname);
        oqeyname  = newname;
    
    } else {
        
        oqeyname  = newname;
        firstplay = false;
    
    }
}
    
function getFlashMovie(movieName) {
    var isIE = navigator.appName.indexOf("Microsoft") != -1;
    return (isIE) ? window[movieName] : document[movieName];
}


var htmlPlayer = document.getElementsByTagName("video");
function pausePlayer(){
    for(var i=0;i<htmlPlayer.length;i++){
        htmlPlayer[i].pause();
    }
}

jQuery(document).ready(function($){  
    
   jQuery(".oqey-slideshow").css('background-image', 'none');
    
});

jQuery(window).resize( function(){
    var height = jQuery(".oqey-slideshow").height();
    
    jQuery(".oqey-slideshow div img").css("max-height", height);

}); 

function SetGalleryHeight(galleryId){
    var wgal = jQuery("#" + galleryId).width();
    jQuery("#" + galleryId).css("height", Math.round(wgal/1.5)+50 ); //+50
}


function IfIsIe () {
  var myNav = navigator.userAgent.toLowerCase();
  return (myNav.indexOf('msie') != -1) ? parseInt(myNav.split('msie')[1]) : false;
}

/*
function oQeyPlayPauseAudio(audionId){
    
    jQuery.each(jQuery('audio'), function () {
    this.pause();
    });

}*/


/**
 * jQuery Plugin to obtain touch gestures from iPhone, iPod Touch and iPad, should also work with Android mobile phones (not tested yet!)
 * Common usage: wipe images (left and right to show the previous or next image)
 * 
 * @author Andreas Waltl, netCU Internetagentur (http://www.netcu.de)
 * @version 1.1.1 (9th December 2010) - fix bug (older IE's had problems)
 * @version 1.1 (1st September 2010) - support wipe up and wipe down
 * @version 1.0 (15th July 2010)
 */
(function($){$.fn.touchwipe=function(settings){var config={min_move_x:20,min_move_y:20,wipeLeft:function(){},wipeRight:function(){},wipeUp:function(){},wipeDown:function(){},preventDefaultEvents:true};if(settings)$.extend(config,settings);this.each(function(){var startX;var startY;var isMoving=false;function cancelTouch(){this.removeEventListener('touchmove',onTouchMove);startX=null;isMoving=false}function onTouchMove(e){if(config.preventDefaultEvents){e.preventDefault()}if(isMoving){var x=e.touches[0].pageX;var y=e.touches[0].pageY;var dx=startX-x;var dy=startY-y;if(Math.abs(dx)>=config.min_move_x){cancelTouch();if(dx>0){config.wipeLeft()}else{config.wipeRight()}}else if(Math.abs(dy)>=config.min_move_y){cancelTouch();if(dy>0){config.wipeDown()}else{config.wipeUp()}}}}function onTouchStart(e){if(e.touches.length==1){startX=e.touches[0].pageX;startY=e.touches[0].pageY;isMoving=true;this.addEventListener('touchmove',onTouchMove,false)}}if('ontouchstart'in document.documentElement){this.addEventListener('touchstart',onTouchStart,false)}});return this}})(jQuery);