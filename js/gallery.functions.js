/* 
 * oQey Gallery page functions
 */

function refreshPage(){
    var id = jQuery('#currentGalID').val(); 
    getGalleryDetails(id); 
    //jQuery( '.swipebox-video' ).swipebox();
}

function clearUp(){ setTimeout(function () {  jQuery('#messages').fadeOut(function(){ jQuery("#messages").html("&nbsp;"); }); }, 2000); }

/*DELETE VIDEO*/
function deleteVideo(id){  //move to trash single image
jQuery.post( ajaxurl, { action:"oQeyVideoFromGalleryDelete", id: id },
            function(data){
                jQuery("#messages").hide().html('<p class="updated fade"> Video file was removed from this gallery. &nbsp;<\/p>').fadeIn("fast");
                jQuery('#img_li_'+id).hide(1000);
            });
}

function showImage(id, src){  //alert(decodeURIComponent(folder));  

        var i = decodeURIComponent(src);
		var img = '<img src="' + i + '" class="previewimg" style="max-height:600px; max-width:900px;" />'; 
		var $dialog = jQuery('<div id="popupdisplay"><\/div>').html(img).dialog({
			width: 900,
			height: 635,
			maxWidth: 900,
			maxHeight: 635,
			resizable: false,
			autoOpen: false,
			title: 'Preview image',
			modal: true,
			draggable: false		
		});			
		$dialog.dialog('open'); 
}

/*begin cropper*/
var xQ, yQ, wQ, hQ, Qtw, Qth, Qttw, Qtth, coef;
var status = 'start';

function showCropImage(imgid, tw, th, ttw, tth, src, src2, multiplicator){  //alert(decodeURIComponent(folder));  

        var d = new Date();
        
        var Qtw = tw; 
        var Qth = th;
        var Qttw = ttw; 
        var Qtth = tth;
        var coef = Qtw/Qth;
        
        jQuery("#Qtw").attr("value", Qtw); 
        jQuery("#Qth").attr("value", Qth); 
        jQuery("#Qttw").attr("value", ttw); 
        jQuery("#Qtth").attr("value", tth); 
        jQuery("#imgid").attr("value", imgid);
        jQuery("#coef").attr("value", coef);
        jQuery("#multiplicator").attr("value", multiplicator);
                
        status = 'start';
        
        var i = decodeURIComponent(src);
        var i2 = decodeURIComponent(src2);
        var id = Math.floor(Math.random()*7777777);

		var img = '<img src="' + i + '" height="'+Qth+'" width="'+Qtw+'" id="cropbox' + id + '" style="background-color: transparent;" \/>'; 
        var img2 = '<img src="' + i + '" id="preview' + id + '" style="display:none;" \/>'; 
        var img3 = '<img src="' + i2 + '?' + d.getTime() + '" id="actual' + id + '" \/>';  
        var bb = '<input type="button" name="update" value="update" onclick="saveNewThumb()" class="button-secondary" style="float:left; margin-left:4px;"/><p id="oqeyr' + id + '" style="float:left; margin-left:4px; width:100px;"><\/p>';       
        var x = jQuery("#trget").attr("value", id);   
        var c = '<table width="660" height="320" border="0"><tr><td rowspan="2" width="500" align="center" valign="top" >' + img + '<\/td><td width="150" height="150" align="center" valign="top"><div style="width:150px;height:100px;overflow:hidden;">' + img2 + img3 + '<\/div><\/td><\/tr><tr><td align="left" valign="top">'+ bb + '<\/td><\/tr><\/table>';
        
        
		var $dialog = jQuery('<div><\/div>').html(c).dialog({
			width: 670,
			height: 360,
			maxWidth: 700,
			maxHeight: 400,
			resizable: false,
			autoOpen: false,
			title: 'Crop image',
			modal: true,
			draggable: false		
		});			
		$dialog.dialog('open'); 
		/*
		$("#dialog-selector" ).dialog({
          open: function(event, ui) { $('#cropbox').Jcrop(); }
        }); */

        setTimeout(function(){ 
        
		 jQuery('#cropbox' + id).Jcrop({
                     onChange: showPreview,
                     onSelect: showPreview,
                     aspectRatio: 1.5
                 });
                 
        }, 3000 ); 	               
}

   function showPreview(coords){
       
       idl   = jQuery("#trget").val();
       Qttw  = 150;
       Qtth  = 100;
       Qtw   = jQuery("#Qtw").val();
       Qth   = jQuery("#Qth").val();
       imgid = jQuery("#imgid").val();
       coef  = jQuery("#coef").val();
             
        if (status != 'edit') {
			jQuery('#actual'+idl).hide();
			jQuery('#preview'+idl).show();
			status = 'edit';	
		}
             
				if (parseInt(coords.w) > 0){
				    
					var rx = (Qttw / coords.w);
					var ry = (Qtth / coords.h);

					jQuery('#preview'+idl).css({
						width: Math.round(rx * Qtw) + 'px',
						height: Math.round(ry * Qth) + 'px',
						marginLeft: '-' + Math.round(rx * coords.x) + 'px',
						marginTop: '-' + Math.round(ry * coords.y) + 'px'
					});
                    
                    xQ = coords.x;
		            yQ = coords.y;
		            wQ = coords.w;
		            hQ = coords.h;
                }
}

function saveNewThumb(){
    
    	if ( (wQ == 0) || (hQ == 0) || (wQ == undefined) || (hQ == undefined) ) {
			alert("Select the area for the new thumbnail");
			return false;			
		}
        
        var multiplicator = jQuery("#multiplicator").val();
		
		jQuery.ajax({
		  url: ajaxurl,
		  type : "POST",
		  data:  {x: xQ, y: yQ, w: wQ, h: hQ, Qttw: Qttw, Qtth:Qtth, action: 'createoQeyNewThumb', imgid: imgid, coef:coef, multiplicator: multiplicator },
		  cache: false,
		  success: function(data){
				var d = new Date();
				newUrl = jQuery("#img_li_"+imgid + " .img_thumbs").attr("src") + "?" + d.getTime();
				jQuery("#img_li_"+imgid + " .img_thumbs").attr("src" , newUrl);
					
					jQuery('#oqeyr' + idl).html("Updated");
					jQuery('#oqeyr' + idl).css({'display':'block'});
					setTimeout(function(){ jQuery('#oqeyr' + idl).fadeOut('slow'); }, 1500);
			},
		  error: function() {
		  		    jQuery('#oqeyr' + idl).html("Error");
					jQuery('#oqeyr' + idl).css({'display':'block'});
					setTimeout(function(){ jQuery('#oqeyr' + idl).fadeOut('slow'); }, 1500);
		    }
		});
}
/*end cropper*/


function updateThumbs(){ //get the last uploaded image
//under development
}

function preloadImages(img){
var images=img.split(",");
jQuery.loadImages(images,function(){});
}


function viewAll(){ 
  if (jQuery("#gallscontent > li").is(":hidden") ) {
      jQuery("#gallscontent > li").fadeIn(2000);
  }  
  jQuery(".allgalls").html("&nbsp;");
}

function getGalleryDetails(id){	
	    
        jQuery('#gallerytable').fadeOut("fast");
        jQuery("#createfrompostbox").fadeOut("fast");
        jQuery('#galleryarea').fadeIn("fast");
        jQuery('#currentGalID').attr('value', id);			
        
        jQuery.post( ajaxurl, { action : "oQeyNewGalleryID", newgallid: id, wpnonce: oqeynonce },
        function(data){
            
	        var data = eval('(' + data + ')');
                jQuery('#content').hide().html(decodeURIComponent(data.response)).fadeIn("fast");
                jQuery('#titlul_b').show();
                jQuery('#titlul').show().html("<span style='float:left; margin-right:3px;'>Gallery title: <\/span> <span title='Double-click to rename...' class='dblclick' id='gall_id_"+ id +"'>" + decodeURIComponent(data.titlul) + "<\/span>");
			
            if(data.folderexist!=''){
                jQuery('#error').html(decodeURIComponent(data.folderexist));
            }
            
            /*
            jQuery("#user").change(function () {
                var usrid = jQuery("#user option:selected").val();
                jQuery.post( ajaxurl, { action: "oQeyAddUserID", usrid: usrid, gid: id },function(data){ } );
            });*/
			
            jQuery("#view_all_galleries").click(function(){
                window.location = galmainpage;	
            });
            
         
	
            jQuery.post( ajaxurl, { action: "oQeyGetAllImages", id: data.galid },
            function(data){
                
                var data = eval('(' + data + ')');
                jQuery('#gallery_content').hide().html(decodeURIComponent(data.allimages)).fadeIn("fast");
                
                jQuery("#sortablegalls").sortable({
                    create: function(event, ui) { 
                        var allimgs = jQuery('#sortablegalls').sortable('serialize');
                        jQuery.post( ajaxurl, { action: "oQeyOrderAllImages", orderallimgs: allimgs, galleryid: id }, function(data){
                            
                         jQuery( '.swipebox' ).swipebox({
             afterOpen: function(){
                 if (jQuery.browser.msie  && parseInt(jQuery.browser.version, 10) === 8){
                      jQuery('#swipebox-bottom-bar').css('bottom','0px');
                      jQuery('#swipebox-top-bar').css('top','0px');
                     }
             }
         });
                            
                        });
                    },
                    update: function(){	//onupdate update the image order
                        var allimgs = jQuery('#sortablegalls').sortable('serialize');
                        jQuery.post( ajaxurl, { action: "oQeyOrderAllImages", orderallimgs: allimgs, galleryid: id },
                        function(data){ 
                        
                         jQuery( '.swipebox' ).swipebox({
             afterOpen: function(){
                 if (jQuery.browser.msie  && parseInt(jQuery.browser.version, 10) === 8){
                      jQuery('#swipebox-bottom-bar').css('bottom','0px');
                      jQuery('#swipebox-top-bar').css('top','0px');
                     }
             }
         });
                     });
                    }   
                });
                
                jQuery(".deleteimage").click(function(e){
                    
                    e.preventDefault();
                    
                    var id = jQuery(this).parents('li').attr('id').replace('img_li_', '');
                    
                    jQuery.post(
                            ajaxurl, 
                            { action:"oQeyImageToTrash", delimgid: id },
                            function(data){
                                jQuery("#messages").hide().html('<p class="updated fade">Image was moved to trash.&nbsp;<a href="#undo" id="undoimage">undo<\/a> &nbsp;<\/p>').fadeIn("fast");
                                jQuery('#img_li_'+id).hide(400);
                                jQuery('#img_li_'+id).children('a').removeClass('swipebox').addClass("hidedswipebox");
                                
                                jQuery("#undoimage").click(function(){ 
                                    
                                    jQuery.post( ajaxurl, { action:"oQeyImageFromTrash", undoimgid: id },
                                    
                                    function(r){
                                        jQuery("#messages").hide().html('<p class="updated fade">' + r + '<\/p>').fadeIn("fast");
                                        jQuery('#img_li_'+id).show(400);
                                        jQuery('#img_li_'+id).children('a').removeClass('hidedswipebox').addClass("swipebox");
                                    });
                                });
                            });
                        });
                        
                  jQuery(".deletevideo").click(function(e){
                    
                    e.preventDefault();
                    
                    var id = jQuery(this).parents('li').attr('id').replace('img_li_', '');
                    
                    jQuery.post( ajaxurl, { action:"oQeyVideoFromGalleryDelete", id: id },
                    function(data){
                        jQuery("#messages").hide().html('<p class="updated fade"> Video file was removed from this gallery. &nbsp;<\/p>').fadeIn("fast");
                        jQuery('#img_li_'+id).hide(400);
                        jQuery('#img_li_'+id).children('a').removeClass('swipebox').addClass("hidedswipebox");
                    });
          
                  });
                        
                
            jQuery(".showimagesettings").click(function(e){
                
                e.preventDefault();
     
               var id = jQuery(this).parents('li').attr('id').replace('img_li_', '');
                    
            jQuery.post( ajaxurl, { action :"oQeyImageDetails", imagedetails: id },
            function(data){                
                var data = eval('(' + data + ')');				
		var galid = jQuery('#currentGalID').val();
                jQuery('#detailsimg #imgid').val(id);
                jQuery('#detailsimg #galid').val(galid);
                
                if(data.type=="video"){

                    jQuery('#detailsimg #p_splash').hide();
                    jQuery('#detailsimg #p_splashexclusive').hide()
                    jQuery('#detailsimg #alt').css("height", "80");
                    jQuery('#detailsimg #comments').css("height", "100");
                    
                }else{
                    
                    jQuery('#detailsimg #p_splash').show();
                    jQuery('#detailsimg #p_splashexclusive').show()
                    jQuery('#detailsimg #splash').attr('checked', false); 
                    jQuery('#detailsimg #splashexclusive').attr('checked', false); 
                    jQuery('#detailsimg #alt').css("height", "60");
                    jQuery('#detailsimg #comments').css("height", "75");
                    
                }
                
                if(data.type!="video"){
				
                   if(data.splash=="on"){ 
                    
                       jQuery('#detailsimg #splash').attr('checked', true);
                   
                   }else{ 
                       
                       jQuery('#detailsimg #splash').attr('checked', false); 
                   
                   }
				
                   if(data.splashexclusive=="on"){ 
                    
                      jQuery('#detailsimg #splashexclusive').attr('checked', true); 
                   
                   }else{ 
                      
                      jQuery('#detailsimg #splashexclusive').attr('checked', false); 
                   
                   }
                   
                }					 
				
                   jQuery('#detailsimg #comments').val(decodeURIComponent(data.comments));
                   jQuery('#detailsimg #alt').val(decodeURIComponent(data.alt));
                   jQuery('#detailsimg #oqey_image_link').val(decodeURIComponent(data.link));
                   jQuery( "#detailsimg" ).dialog( "open" );
            
            });

 });
                
    jQuery( '.swipebox' ).swipebox({
             afterOpen: function(){
                 if (jQuery.browser.msie  && parseInt(jQuery.browser.version, 10) === 8){
                      jQuery('#swipebox-bottom-bar').css('bottom','0px');
                      jQuery('#swipebox-top-bar').css('top','0px');
                     }
             }
         });
    
    
    //jQuery( '.swipebox-video' ).swipebox();
    
    jQuery("#add_pps_gallery_button").fadeIn(500);
 
    jQuery(".dblclick").editable( ajaxurl, { 
    indicator  : 'Updating...',
    tooltip    : "Double-click to rename...",
    event      : "dblclick",
    style      : "inherit",
    width      : "670px",
    height     : "20px",
    submitdata : function ( value, settings ) { return { "action": 'oQeyEditGalleryTitle' }; }
   });

jQuery(".styled").click(function(){ 	
if (jQuery(this).attr('checked')){ jQuery(this).parent().parent().css('background-color', '#C0CFEF'); }else{ jQuery(this).parent().parent().css('background-color', '#F8F8F8'); }
        var names = [];
        jQuery('#sortablegalls input:checked').each(function() {
            names.push(this.id);
        });
});

jQuery("#oqeymusic").click(function(){ 

  if (jQuery("#musicpostbox").is(":hidden")) {
      jQuery("#musicpostbox").slideDown("slow");
	  
	  var id = jQuery('#currentGalID').val();	
	  jQuery.post( ajaxurl, { action: "oQeyOrderAndSelectMusic", music_gall_id: id },
            function(data){  			
			jQuery('#musiclist').hide().html(data).fadeIn("fast");			
                        jQuery("#sortablemuzon").sortable({ revert: true });
                        
			jQuery("#savemusic").click(function(){
                            
                            var selectedmusic = jQuery('#musicselect').serializeArray();
                            var id = jQuery('#currentGalID').val(); 
                            
                            jQuery.post( 
                                    ajaxurl, 
                                    { action:"oQeySaveMusicOrder", selectedmusic: selectedmusic, mgalleryid: id },
                                    function(data){
                                        jQuery("#messages").hide().html('<p class="updated fade">' + data + '<\/p>').fadeIn("fast"); 
                                        clearUp();  
                                    }
                                );
                                
                        });
                    });

    }else{
        
                            jQuery("#musicpostbox").slideUp(500);
                            setTimeout(function () {  
                                jQuery('#musiclist').html('<div class="obis">Loading content...<\/div>');  
                            }, 1000);	  
    }
});


jQuery("#oqeyskin").click(function(){ 

  if (jQuery("#skinpostbox").is(":hidden")) {
      jQuery("#skinpostbox").slideDown("slow");
	  
	  var id = jQuery('#currentGalID').val();	
	  jQuery.post( ajaxurl, { action: "oQeyGetAllSkins", skin_gall_id: id },
            function(data){  			
			
			jQuery('#skinlist').hide().html(data).fadeIn("fast"); 

			jQuery(".activate_skin").click(function(){			 
			 var skinid = jQuery(this).attr("id");			 
			jQuery.post( ajaxurl, { action:"oQeySetNewSkin", skinid: skinid, skin_gallery_id: id },
            function(data){ jQuery("#messages").hide().html('<p class="updated fade">' + data + '<\/p>').fadeIn("fast"); clearUp();
			jQuery("#skinpostbox").slideUp(500);
	        setTimeout(function () {  jQuery('#skinlist').html('<div class="obis">Loading content...<\/div>');  }, 1000);
			}
			);
        });
    });

    } else {
      jQuery("#skinpostbox").slideUp(500);
	  setTimeout(function () {  jQuery('#skinlist').html('<div class="obis">Loading content...<\/div>');  }, 1000);	  
    }

});


/*ADD VIDEO FILES TO GALLERY*/
  jQuery("#addvideofile img").click(function(){ 
    
    var id = jQuery('#currentGalID').val();
    
          jQuery.ajax({
              url: ajaxurl,
              type : "POST",
              data:  {action: 'oQeyGetAllVideoFiles', galid: id},
              cache: false,
              success: function(data){ 
                  jQuery("#video_content").html(data);
                  jQuery("#oqeyvideomanagement").dialog({ width: 900, height: 600, resizable: false, autoOpen: true, title: 'Add video', modal: true, draggable: false });
              },
              error: function() {
                  jQuery("#save").html('<div class="updated fade" id="message"><p>There is an error, please try again.<\/p><\/div>');
              }
          });
      });


/*END VIDEO ADD*/

jQuery("#watermark-settings img").click(function(){ //watermark management
   
   if (jQuery("#watermarkpostbox").is(":hidden")){ 
    
    jQuery("#watermarkpostbox").slideDown("slow");
	  
	  var id = jQuery('#currentGalID').val();	
        
	  jQuery.post( ajaxurl, { action: "oQeyGetWatermarkSettings", id: id },
          function(data){  			
			
          jQuery('#watermarklist').hide().html(data).fadeIn("fast"); 

            jQuery("#oqey_wtm input").change(function(){
                
               if(jQuery(this).val()!="0d"){ 
                 jQuery("#customtw").prop("checked", true);
               }
                
                //jQuery("#customtw").prop("checked", true);
                //jQuery(".myCheckbox").prop("checked", false);

            
              var id = jQuery('#currentGalID').val();
              var data = jQuery("#oqey_wtm").serialize();
		 
			  jQuery.post( ajaxurl, { action:"oQeySaveWatermarkSettings", id: id, data: data },
                function(data){});            
              });
     });

    }else{
        
      jQuery("#watermarkpostbox").slideUp("slow");
	  setTimeout(function () {  jQuery('#watermarklist').html('<div class="obis">Loading content...<\/div>');  }, 1000);	  
    
    }
    
});


jQuery("#selectall").click(function(){ 
    
     if (jQuery(this).attr('checked')){ 
         
         jQuery('#sortablegalls input').attr('checked', true); 
         jQuery('#seelectmessage').text('clear all');
         jQuery("#sortablegalls li").css('background-color', '#C0CFEF'); 
    
    }else{
         
         jQuery('#sortablegalls input').attr('checked', false); 
         jQuery('#seelectmessage').text('select all');
         jQuery("#sortablegalls li").css('background-color', '#F8F8F8'); 
         
    }	
});

jQuery("#doapply").click(function(){ 
var id = jQuery('#dolist').val();

if(id==3){

jQuery('#sortablegalls input').attr('checked', false); 
jQuery('#seelectmessage').text('select all');
jQuery("#sortablegalls li").css('background-color', '#F8F8F8'); 
jQuery('#selectall').attr('checked', false); 

var gid = jQuery('#currentGalID').val();

var allimgs = jQuery('#sortablegalls').sortable('serialize');	
jQuery.post( ajaxurl, { action: "oQeyOrderAllImages", orderallimgs: allimgs, galleryid: gid, imgreverse: "yes" },
function(data){
    getGalleryDetails(gid);
    jQuery("#add_pps_gallery_button").fadeIn(500);
});	
	
}

if(id==2){

var names = [];

jQuery('#sortablegalls input:checked').each(function() {
    
    if ( jQuery(this).parent().parent().is(':visible')){
        
        names.push(this.value);		
    
    }
    
    jQuery(this).parent().parent().hide("slow");

});
		
        jQuery.post( ajaxurl, { action: "oQeyImagesToTrash", imgalltotrash: encodeURIComponent(names) },
        function(data){
            
            if(names.length>0){
                
                jQuery("#messages").hide().html(decodeURIComponent('<p class="updated fade">' + data + ' <a href="#undo" id="undoallimages">undo&nbsp;<\/a><\/p>')).fadeIn("fast"); 
         
                jQuery("#undoallimages").click(function(){
                    
                    jQuery.post( ajaxurl, { action: "oQeyImagesFromTrash", imgallfromtrash: encodeURIComponent(names) },
                        function(r){
                            
                            var data = eval('(' + r + ')');
                            
                            jQuery.each(data.imgallfromtrash, function(i, val) {
                                
                                jQuery("#img_li_" + val).css('background-color', '#F8F8F8');
                                jQuery("#selected_" + val).attr('checked', false); 
                                jQuery("#img_li_" + val).show("slow");
                            });
                            
                            jQuery("#messages").hide().html('<p class="updated fade">All selected images was restored.&nbsp;<\/p>').fadeIn("fast");
                            jQuery('#selectall').attr('checked', false);
                            jQuery('#seelectmessage').text('select all');
                            clearUp(); 
                        });
                    });
                    
		}else{
                    
                    jQuery("#messages").hide().html(decodeURIComponent('<p class="updated fade">Please select an image&nbsp;<\/p>')).fadeIn("fast"); 
                    clearUp(); 
		}
            
            });
        
        }
    
    });

});

});	
    
    return true;
    
}

function deleteGallery(id){ 
            jQuery.post( ajaxurl, { action:"oQeyGalleryToTrash", movetotrashgall: id },
            function(data){
            jQuery('#row_' + id).fadeOut("slow");					
			jQuery("#messages").hide().html('<p class="updated fade">' + data + ' <a href="#undo" id="undogallery">undo<\/a>&nbsp; <\/p>').fadeIn("fast");
			
			jQuery("#undogallery").click(function(){
			jQuery.post( ajaxurl, { action: "oQeyGalleryFromTrash", undogallid: id },
            function(r){
			var data = eval('(' + r + ')');		
			jQuery("#messages").hide().html('<p class="updated fade">' + decodeURIComponent(data.mesaj) + '<\/p>').fadeIn("fast");
			jQuery('#row_'+id).fadeIn("fast");
			});			
			});	
         });
}

function openPreview(){
    jQuery("#dialog").dialog( {autoOpen: false});
}

function hoverGallery(){
    jQuery("#sortable tr").hover(
            function () {
                jQuery(this).children(".lasttd").children(".hiddenm").addClass("visiblem"); 
            },
            function () {
                jQuery(this).children(".lasttd").children(".hiddenm").removeClass("visiblem").addClass("hiddenm");
            }
        );
}