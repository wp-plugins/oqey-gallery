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
 