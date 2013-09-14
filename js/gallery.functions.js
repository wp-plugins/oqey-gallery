/* 
 * Gallery page functions
 */

function refreshPage(){
    var id = jQuery('#currentGalID').val(); 
    getGalleryDetails(id); 
}

function clearUp(){ setTimeout(function () {  jQuery('#messages').fadeOut(function(){ jQuery("#messages").html("&nbsp;"); }); }, 2000); }

function deleteImage(id){  //move to trash single image
jQuery.post( ajaxurl, { action:"oQeyImageToTrash", delimgid: id },
            function(data){
                jQuery("#messages").hide().html('<p class="updated fade">Image was moved to trash.&nbsp;<a href="#undo" id="undoimage">undo<\/a> &nbsp;<\/p>').fadeIn("slow");
            jQuery('#img_li_'+id).hide(1000);
            
			jQuery("#undoimage").click(function(){            
			jQuery.post( ajaxurl, { action:"oQeyImageFromTrash", undoimgid: id },
              function(r){
                
                jQuery("#messages").hide().html('<p class="updated fade">' + r + '<\/p>').fadeIn("slow");
			    jQuery('#img_li_'+id).show(1000);
			  });								
			});	
            	
      });
}



/*DELETE VIDEO*/
function deleteVideo(id){  //move to trash single image
jQuery.post( ajaxurl, { action:"oQeyVideoFromGalleryDelete", id: id },
            function(data){
			jQuery("#messages").hide().html('<p class="updated fade"> Video file was removed from this gallery. &nbsp;<\/p>').fadeIn("slow");
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
			 
 		     idl = jQuery("#trget").val();
             Qttw = 150; //jQuery("#Qttw").val();
             Qtth = 100;//jQuery("#Qtth").val();
             Qtw = jQuery("#Qtw").val();
             Qth = jQuery("#Qth").val();
             imgid = jQuery("#imgid").val();
             coef = jQuery("#coef").val();
             
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

/*
function ajaxUploader(id){
		var btnUpload=jQuery('#upload');
		var status=jQuery('#status');
		new AjaxUpload(btnUpload, {
			action: actionlink + '/bcupload.php?&id=' + id,
			name: 'Filedata',
			onSubmit: function(file, ext){
				  if (! (ext && /^(jpg|png|jpeg)$/.test(ext))){ 
                    // extension is not allowed 
					status.text('Only JPG and PNG files are allowed');
					return false;
				}
				status.text('Uploading...');
			},
			onComplete: function(file, response){
				status.text('');
				status.text(response);
				refreshPage();
		}
	});
} */

function viewAll(){ 
  if (jQuery("#gallscontent > li").is(":hidden") ) {
      jQuery("#gallscontent > li").fadeIn(2000);
  }  
  jQuery(".allgalls").html("&nbsp;");
}

function getGalleryDetails(id){	
	    
            jQuery('#gallerytable').fadeOut("slow");
            jQuery("#createfrompostbox").fadeOut("slow");
            jQuery('#galleryarea').fadeIn("slow");
            jQuery('#currentGalID').attr('value', id);			
        
        jQuery.post( ajaxurl, { action : "oQeyNewGalleryID", newgallid: id, wpnonce: oqeynonce },
        function(data){
            
	        var data = eval('(' + data + ')');
                jQuery('#content').hide().html(decodeURIComponent(data.response)).fadeIn("slow");
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
            
            /*
            uploader.init();            
            uploader.bind('FilesAdded', function(up, files) {
                
                uploader.start();
                return false;
        
            });*/
            
			
	    //var playerVersion = swfobject.getFlashPlayerVersion();
	    //if(playerVersion.major<8){	
		//ajaxUploader(data.noflashinfo);
	    //}
	
            jQuery.post( ajaxurl, { action: "oQeyGetAllImages", id: data.galid },
            function(data){
                
                var data = eval('(' + data + ')');
                jQuery('#gallery_content').hide().html(decodeURIComponent(data.allimages)).fadeIn("slow");
                //jQuery("#sortablegalls").selectable("disable");
    
    /*
    jQuery(function() {
		var allimgs = jQuery('#sortablegalls').sortable('serialize'); 
        alert(allimgs);
	    
        jQuery.post( ajaxurl, { action: "oQeyOrderAllImages", orderallimgs: allimgs, galleryid: id }, function(data){ alert(data); });
        
    	jQuery( "#sortablegalls" ).disableSelection();
        
            
	});
    */
            
    jQuery("#sortablegalls").sortable({
    create: function(event, ui) { 
        var allimgs = jQuery('#sortablegalls').sortable('serialize'); 
        jQuery.post( ajaxurl, { action: "oQeyOrderAllImages", orderallimgs: allimgs, galleryid: id }, function(data){});
        },
    update: function(){	//onupdate update the image order		
	
    		var allimgs = jQuery('#sortablegalls').sortable('serialize');						
	
    jQuery.post( ajaxurl, { action: "oQeyOrderAllImages", orderallimgs: allimgs, galleryid: id },
            function(data){});
            
       }   
    });
 
    jQuery(".dblclick").editable( ajaxurl, { 
    indicator : 'Updating...',
    tooltip   : "Double-click to rename...",
    event     : "dblclick",
    style  : "inherit",
    width : "670px",
    height : "15px",
    submitdata : function ( value, settings ) { return { "action": 'oQeyEditGalleryTitle' }; }
   });
 
//preloadImages(decodeURIComponent(data.allimgpreload));
		

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
			jQuery('#musiclist').hide().html(data).fadeIn("slow"); 
			
                        jQuery("#sortablemuzon").sortable({
                            revert: true 
                        });
			
			jQuery("#savemusic").click(function(){
                            var selectedmusic = jQuery('#musicselect').serializeArray();
                            var id = jQuery('#currentGalID').val();
                            
        jQuery.post( ajaxurl, { action:"oQeySaveMusicOrder", selectedmusic: selectedmusic, mgalleryid: id },
            function(data){
                jQuery("#messages").hide().html('<p class="updated fade">' + data + '<\/p>').fadeIn("slow"); 
                clearUp();  
                }
			);

           });
		});

    } else {
      jQuery("#musicpostbox").slideUp(500);
	  setTimeout(function () {  jQuery('#musiclist').html('<div class="obis">Loading content...<\/div>');  }, 1000);	  
    }
});


jQuery("#oqeyskin").click(function(){ 

  if (jQuery("#skinpostbox").is(":hidden")) {
      jQuery("#skinpostbox").slideDown("slow");
	  
	  var id = jQuery('#currentGalID').val();	
	  jQuery.post( ajaxurl, { action: "oQeyGetAllSkins", skin_gall_id: id },
            function(data){  			
			
			jQuery('#skinlist').hide().html(data).fadeIn("slow"); 

			jQuery(".activate_skin").click(function(){			 
			 var skinid = jQuery(this).attr("id");			 
			jQuery.post( ajaxurl, { action:"oQeySetNewSkin", skinid: skinid, skin_gallery_id: id },
            function(data){ jQuery("#messages").hide().html('<p class="updated fade">' + data + '<\/p>').fadeIn("slow"); clearUp();
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
    
    //var id = jQuery(this).parent(".videoimg").attr("id");
    
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
			
          jQuery('#watermarklist').hide().html(data).fadeIn("slow"); 

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
		jQuery("#messages").hide().html(decodeURIComponent('<p class="updated fade">' + data + ' <a href="#undo" id="undoallimages">undo&nbsp;<\/a><\/p>')).fadeIn("slow"); 

			jQuery("#undoallimages").click(function(){
			jQuery.post( ajaxurl, { action: "oQeyImagesFromTrash", imgallfromtrash: encodeURIComponent(names) },
            function(r){

			var data = eval('(' + r + ')');			
			jQuery.each(data.imgallfromtrash, function(i, val) {
            jQuery("#img_li_" + val).css('background-color', '#F8F8F8');
			jQuery("#selected_" + val).attr('checked', false); 
			jQuery("#img_li_" + val).show("slow");
            });

			jQuery("#messages").hide().html('<p class="updated fade">All selected images was restored.&nbsp;<\/p>').fadeIn("slow");
			jQuery('#selectall').attr('checked', false); 
            jQuery('#seelectmessage').text('select all');
			
			clearUp(); 	
			});	
			});
		}else{
		jQuery("#messages").hide().html(decodeURIComponent('<p class="updated fade">Please select an image&nbsp;<\/p>')).fadeIn("slow"); 
		clearUp(); 
		}		
		
        });
       }
       });

        });	
        
    });	
}

function deleteGallery(id){ 
            jQuery.post( ajaxurl, { action:"oQeyGalleryToTrash", movetotrashgall: id },
            function(data){
            jQuery('#row_' + id).fadeOut("slow");					
			jQuery("#messages").hide().html('<p class="updated fade">' + data + ' <a href="#undo" id="undogallery">undo<\/a><\/p>').fadeIn("slow");
			
			jQuery("#undogallery").click(function(){
			jQuery.post( ajaxurl, { action: "oQeyGalleryFromTrash", undogallid: id },
            function(r){
			var data = eval('(' + r + ')');		
			jQuery("#messages").hide().html('<p class="updated fade">' + decodeURIComponent(data.mesaj) + '<\/p>').fadeIn("slow");
			jQuery('#row_'+id).fadeIn("slow");
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