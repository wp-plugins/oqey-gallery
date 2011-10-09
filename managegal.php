<?php
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'managegal.php' == basename($_SERVER['SCRIPT_FILENAME'])) die ('Please do not load this page directly. Thanks!');
global $wpdb, $current_user;
   $oqey_galls = $wpdb->prefix . "oqey_gallery";
   $oqey_images = $wpdb->prefix . "oqey_images";
   $oqey_music = $wpdb->prefix . "oqey_music";
   $oqey_music_rel = $wpdb->prefix . "oqey_music_rel";

$oqeyImagesRoot = get_option('siteurl').'/wp-content/oqey_gallery/galleries/'.oqey_getBlogFolder($wpdb->blogid);
?>
<form>
<input type="hidden" id="trget" value=""/>
<input type="hidden" id="Qtw" value=""/>
<input type="hidden" id="Qth" value=""/>
<input type="hidden" id="Qttw" value=""/>
<input type="hidden" id="Qtth" value=""/>
<input type="hidden" id="imgid" value=""/>
<input type="hidden" id="coef" value=""/>
</form>

<script type="text/javascript">
function refreshPage(){ 
    var id = jQuery('#currentGalID').val(); 
    getGalleryDetails(id); 
}
function clearUp(){ setTimeout(function () {  jQuery('#messages').fadeOut(function(){ jQuery("#messages").html("&nbsp;"); }); }, 2000); }

function deleteImage(id){  //move to trash single image
jQuery.post( ajaxurl, { action:"oQeyImageToTrash", delimgid: id },
            function(data){
			jQuery("#messages").hide().html('<p class="updated fade">Image was moved to trash. <a href="#undo" id="undoimage">undo<\/a><\/p>').fadeIn("slow");
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

function showImage(id, src){  //alert(decodeURIComponent(folder));  
        var i = decodeURIComponent(src);
		var img = '<img src="' + i + '" class="previewimg" height="600" />'; 
		var $dialog = jQuery('<div id="popupdisplay"><\/div>').html(img).dialog({
			width: 896,
			height: 595,
			maxWidth: 896,
			maxHeight: 595,
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

function showCropImage(imgid, tw, th, ttw, tth, src, src2){  //alert(decodeURIComponent(folder));  

        var d = new Date();
        
        var Qtw = tw/2; 
        var Qth = th/2;
        var Qttw = ttw; 
        var Qtth = tth;
        var coef = Qtw/Qth;
        
        jQuery("#Qtw").attr("value", Qtw); 
        jQuery("#Qth").attr("value", Qth); 
        jQuery("#Qttw").attr("value", ttw); 
        jQuery("#Qtth").attr("value", tth); 
        jQuery("#imgid").attr("value", imgid);
        jQuery("#coef").attr("value", coef);
        
        status = 'start';
        
        var i = decodeURIComponent(src);
        var i2 = decodeURIComponent(src2);
        var id = Math.floor(Math.random()*7777777);

		var img = '<img src="' + i + '?' + d.getTime() + '" height="'+Qth+'" width="'+Qtw+'" id="cropbox' + id + '" style="background-color: transparent;" \/>'; 
        var img2 = '<img src="' + i + '" id="preview' + id + '" style="display:none;" \/>'; 
        var img3 = '<img src="' + i2 + '?' + d.getTime() + '" id="actual' + id + '" \/>';  
        var bb = '<input type="button" name="update" value="update" onclick="saveNewThumb()" class="button-secondary" style="float:left; margin-left:4px;"/><p id="oqeyr' + id + '" style="float:left; margin-left:4px; width:100px;"><\/p>';       
        var x = jQuery("#trget").attr("value", id);   
        
        //var c = '<table width="660" height="320" border="0"><tr><td rowspan="2" width="500" align="center" valign="top">' + img + '</td><td width="150" height="150" align="center" valign="top"><div style="width:' + Qttw + 'px;height:'+ Qtth +'px;overflow:hidden;">' + img2 + img3 + '<\/div></td></tr><tr><td align="left" valign="top">'+ bb + '</td></tr></table>';
         var c = '<table width="660" height="320" border="0"><tr><td rowspan="2" width="500" align="center" valign="top" >' + img + '<\/td><td width="150" height="150" align="center" valign="top"><div style="width:150px;height:100px;overflow:hidden;">' + img2 + img3 + '<\/div><\/td><\/tr><tr><td align="left" valign="top">'+ bb + '<\/td><\/tr><\/table>';
                       
        //var c = img + '<div style="width:' + Qttw + 'px;height:'+ Qtth +'px;overflow:hidden;">' + img2 + img3 + '<\/div>' + bb;
        
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

                   jQuery('#cropbox' + id).Jcrop({
					onChange: showPreview,
					onSelect: showPreview,
					aspectRatio: 1.5
				});
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
             
				if (parseInt(coords.w) > 0)
				{
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
			alert("<?php _e('Select the area for the new thumbnail', 'oqeygallery'); ?>");
			return false;			
		}
		
		jQuery.ajax({
		  url: ajaxurl,
		  type : "POST",
		  data:  {x: xQ, y: yQ, w: wQ, h: hQ, Qttw: Qttw, Qtth:Qtth, action: 'createoQeyNewThumb', imgid: imgid, coef:coef},
		  cache: false,
		  success: function(data){
				var d = new Date();
				newUrl = jQuery("#img_li_"+imgid + " .img_thumbs").attr("src") + "?" + d.getTime();
				jQuery("#img_li_"+imgid + " .img_thumbs").attr("src" , newUrl);
					
					jQuery('#oqeyr' + idl).html("<?php _e('Updated', 'oqeygallery') ?>");
					jQuery('#oqeyr' + idl).css({'display':'block'});
					setTimeout(function(){ jQuery('#oqeyr' + idl).fadeOut('slow'); }, 1500);
			},
		  error: function() {
		  		    jQuery('#oqeyr' + idl).html("<?php _e('Error', 'oqeygallery') ?>");
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

function ajaxUploader(id){
		var btnUpload=jQuery('#upload');
		var status=jQuery('#status');
		new AjaxUpload(btnUpload, {
			action: '<?php echo oQeyPluginUrl(); ?>/bcupload.php?&id=' + id,
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
}

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
        
        jQuery.post( ajaxurl, { action : "oQeyNewGalleryID", newgallid: id, wpnonce: '<?php echo wp_create_nonce('oqey-upload'); ?>' },
        function(data){
	        var data = eval('(' + data + ')');
            jQuery('#content').hide().html(decodeURIComponent(data.response)).fadeIn("slow");
			jQuery('#titlul_b').show();
			jQuery('#titlul').show().html("<span style='float:left; margin-right:3px;'>Gallery title: <\/span> <span title='Double-click to rename...' class='dblclick' id='gall_id_"+ id +"'>" + decodeURIComponent(data.titlul) + "<\/span>");
						
        if(data.folderexist!=''){
			  jQuery('#error').html(decodeURIComponent(data.folderexist));
        }
			
        jQuery("#user").change(function () {
            var usrid = jQuery("#user option:selected").val();
        
        jQuery.post( ajaxurl, { action: "oQeyAddUserID", usrid: usrid, gid: id },function(data){ } );
		
        });
			
        jQuery("#view_all_galleries").click(function(){
			window.location = "<?php echo admin_url('admin.php?page=oQeyGalleries'); ?>";										 
		 });
			
			var playerVersion = swfobject.getFlashPlayerVersion();
		    if(playerVersion.major<8){	
			ajaxUploader(data.noflashinfo);
			}
			jQuery.post( ajaxurl, { action: "oQeyGetAllImages", id: data.galid },
            function(data){
			var data = eval('(' + data + ')');

            jQuery('#gallery_content').hide().html(decodeURIComponent(data.allimages)).fadeIn("slow");
 		    jQuery("#sortablegalls").selectable("disable");
    
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
 

preloadImages(decodeURIComponent(data.allimgpreload));
		

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
		jQuery("#messages").hide().html(decodeURIComponent('<p class="updated fade">' + data + ' <a href="#undo" id="undoallimages">undo<\/a><\/p>')).fadeIn("slow"); 

			jQuery("#undoallimages").click(function(){
			jQuery.post( ajaxurl, { action: "oQeyImagesFromTrash", imgallfromtrash: encodeURIComponent(names) },
            function(r){

			var data = eval('(' + r + ')');			
			jQuery.each(data.imgallfromtrash, function(i, val) {
            jQuery("#img_li_" + val).css('background-color', '#F8F8F8');
			jQuery("#selected_" + val).attr('checked', false); 
			jQuery("#img_li_" + val).show("slow");
            });

			jQuery("#messages").hide().html('<p class="updated fade">All selected images was restored.<\/p>').fadeIn("slow");
			jQuery('#selectall').attr('checked', false); 
            jQuery('#seelectmessage').text('select all');
			
			clearUp(); 	
			});	
			});
		}else{
		jQuery("#messages").hide().html(decodeURIComponent('<p class="updated fade">Please select an image<\/p>')).fadeIn("slow"); 
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
</script>
<input type="hidden" value="" id="currentGalID" name="currentGalID"  />
<div class="wrap">
  <h2>Manage galleries</h2>
  <div id="error">&nbsp;</div>
</div>

<!--<img src="<?php //echo oQeyPluginUrl().'/images/'; ?>upperbanddress.jpg" width="950" height="120" /><br /><br />-->
<div class="postbox" style="height:50px; width:900px;">

    <div style="margin-right:2px; padding-top:3px; float:right;">
    <a href="<?php echo admin_url('admin.php?page=oQeySkins&showskins=yes'); ?>"><img src="<?php echo oQeyPluginUrl().'/images/'; ?>getmoreskinsbgn.png" width="250" height="48" /></a>    
    </div>

</div>


<div class="postbox" style="width:900px;">
<div id="creator" >
Create a new gallery: 
  <input name="newtitle" id="newtitle" /> <input type="button" name="creategall" id="creategall" value="create"/>
</div>
<div id="messages" style=" float:left;">&nbsp;</div>
<div id="magic"><a href="#create" id="createfromothers">
                <img src='<?php echo oQeyPluginUrl().'/images/wizard.png'; ?>' height="25" width="25" alt="Import from existing galleries" title="Import from existing galleries"/>
                </a>
</div>
<br class="clear" />
</div>

<div class="postbox" id="createfrompostbox">
<div id="importlist">Loading content...</div>
</div>

<div class="postbox" style="width:900px; display:none;" id="titlul_b">
<div id="titlul"><div class="obis">Loading content...</div></div>
</div>
<div class="postbox" style="width:900px;">
<div id="content"><div class="obis">Loading content...</div></div>
</div>

<div class="postbox" id="musicpostbox">
<div id="musiclist"><div class="obis">Loading content...</div></div>
</div>

<div class="postbox" id="skinpostbox">
<div id="skinlist"><div class="obis">Loading content...</div></div>
</div>

<div class="galleriesbox" id="galleryarea">
<div id="gallery_content"><div class="obis">Loading content...</div></div>
<br class="clear" />
</div>

<a href="http://www.oqeysites.com" target="_blank"><img src="<?php echo oQeyPluginUrl().'/images/'; ?>galleries_banner.png"/></a>

<script type="text/javascript">
jQuery(document).ready(function($){
    
    
jQuery.loadImages([ '<?php echo oQeyPluginUrl().'/images/preview_button.png'; ?>', '<?php echo oQeyPluginUrl().'/images/remove_button.png'; ?>', '<?php echo oQeyPluginUrl().'/images/edit_button.png'; ?>', '<?php echo oQeyPluginUrl().'/images/remove_button.png'; ?>', '<?php echo oQeyPluginUrl().'/images/settings_small_button.png'; ?>', '<?php echo oQeyPluginUrl().'/images/remove_small_button.png'; ?>', '<?php echo oQeyPluginUrl().'/images/skin_button.png'; ?>', '<?php echo oQeyPluginUrl().'/images/music_button.png'; ?>', '<?php echo oQeyPluginUrl().'/images/ui-icons_222222_256x240.png'; ?>', '<?php echo oQeyPluginUrl().'/images/ui-icons_454545_256x240.png'; ?>', '<?php echo oQeyPluginUrl().'/images/ui-icons_888888_256x240.png'; ?>' ],function(){});

/*loading galleries*/ //oQeyGetAllGalleries	
jQuery.post( ajaxurl, {action: 'oQeyGetAllGalleries', allgalls: "yes" }, function(data){
    
    jQuery('#content').hide().html(data).fadeIn("slow");

		jQuery('.preview-gallery').click(function(){
		  
		var id = jQuery(this).attr("id");
		var playerVersion = swfobject.getFlashPlayerVersion();
		if(playerVersion.major<8){	
		var flash = "no";
	    }else{
		var flash = "yes";
		}
		
jQuery.post( ajaxurl, { action: 'oQeyGetPreviewGallery', previewid: id, flash: flash  }, function(r){   
    
		var $dialog = jQuery('<div style="height:620px; width:900px; position:relative; display:block;"><\/div>').html(r).dialog({
			width: 900,
			maxWidth: 900,
			maxHeight: 620,
			resizable: false,
			autoOpen: false,
			title: 'Preview gallery',
			modal: true,
			draggable: false	
		});	
		
		$dialog.dialog('open'); 
		});
		return false;
	});
   
    jQuery('.dblclick').editable( ajaxurl, {
            indicator : 'Updating...',
            tooltip   : "Double-click to rename...",
            event     : "dblclick",
            style  : "inherit",
	        width : "530px",
	        height : "15px",
            submitdata : function ( value, settings ) { return { "action": 'oQeyEditGalleryTitle' }; }
    });

   hoverGallery();
});



jQuery('#newtitle').keypress(function(e) {
        if(e.which == 13) {
            jQuery('#creategall').trigger("click");
        }
    });

/*Create new gallery*/
jQuery("#creategall").click(function(){ 
var newtitle = jQuery("#newtitle").val();

jQuery.post( ajaxurl, { action:"oQeyNewGallery", newtitle: newtitle },
 function(html){
    
         var data = eval('(' + html + ')');

         if(data.response=="Created"){	
			jQuery(".nogallery").hide();
            jQuery("#messages").hide().html(decodeURIComponent('<p class="updated fade">' + data.response + '<\/p>')).fadeIn("slow"); 
			jQuery('#newtitle').attr('value', '');
			clearDiv(); 
			getGalleryDetails(data.galid);
         }else{			
			jQuery("#messages").hide().html(decodeURIComponent('<p class="error fade">' + data.response + '<\/p>')).fadeIn("slow");  
			clearDiv(); 
         }
   });   
});
/*END new gallery*/

/*import from others galleries*/
jQuery("#createfromothers").click(function(){ 

  if(jQuery("#createfrompostbox").is(":hidden")) {
      jQuery("#createfrompostbox").slideDown("slow");
	  	
	  jQuery.post( ajaxurl, { action: "oQeyCheckForOthersGalleries", info: "all" },
            function(data){  	
            
	  		jQuery('#importlist').hide().html(data).fadeIn("slow"); 
            
            jQuery('#othersgalls').change(function() {
                
               jQuery('#otgallstitle').remove();
                
               if(jQuery(this).val()!=0){
               
                      jQuery.post( ajaxurl, { action: "oQeyCheckForOthersGalleries", info: jQuery(this).val() },
                        function(data){ 
                            jQuery('#importlist').append(data); 
                                                        
                            jQuery('#otgallstitle').change(function() {
                                
                                if(jQuery(this).val()==0){
                                     jQuery('#importnewgall').hide();
                                }else{
                                     jQuery('#importnewgall').show();
                                
                            //create new gallery from others existed
                            jQuery("#importnewgall").click(function(){      
                            jQuery.post( ajaxurl, { action:"oQeyCheckForOthersGalleries", info: "nextgencreate", gid: jQuery("#otgallstitle").val() },
                               function(html){
                                 var data = eval('(' + html + ')');

                                if(data.response=="Created"){	
			                         jQuery(".nogallery").hide();
                                     jQuery("#messages").hide().html(decodeURIComponent('<p class="updated fade">' + data.response + '<\/p>')).fadeIn("slow"); 
			                         //jQuery('#newtitle').attr('value', '');
			                         clearDiv(); 
 			                         getGalleryDetails(data.galid);
                                  }else{			
			                         jQuery("#messages").hide().html(decodeURIComponent('<p class="error fade">' + data.response + '<\/p>')).fadeIn("slow");  
			                         clearDiv(); 
                             }
                             
                            }); 
                           });
                          }
                         });
                        });
                      
               }else{
                jQuery('#otgallstitle').remove();
                jQuery('#importnewgall').remove();
               }
                                              
            });
                        
	  	});
      
      }else{
        
      jQuery("#createfrompostbox").slideUp(500);
	  setTimeout(function (){ jQuery('#importlist').html('Loading content...');  }, 1000);	  
      
    }
});
/*END import*/



function clearDiv(){ setTimeout(function () {  jQuery('#messages').fadeOut(function(){ jQuery("#messages").html("&nbsp;"); }); }, 2000); } 

	jQuery( "#detailsimg" ).dialog({
			width: 600,
			height: 400,
			maxWidth: 600,
			maxHeight: 400,
			resizable: false,
			autoOpen: false,
			modal: true,
			title: "Image details",
			draggable: false
		});
		
jQuery("#updatedetails").click(function() {            
   //var alldata = jQuery("#updateform").serialize();
   if(jQuery("#updateform #splash").is(':checked')){ var splash="on"; }else{ var splash= ""; } 
   if(jQuery("#updateform #splashexclusive").is(':checked')){ var splashexclusive="on"; }else{ var splashexclusive= ""; }
   var  imgid = encodeURIComponent( jQuery("#updateform #imgid").val() );
   var  galid = encodeURIComponent( jQuery("#updateform #galid").val() );
   var  alt = encodeURIComponent( jQuery("#updateform #alt").val() );
   var  comments = encodeURIComponent( jQuery("#updateform #comments").val() );
   var  oqey_image_link = encodeURIComponent( jQuery("#updateform #oqey_image_link").val() );
  
   jQuery.ajax({
   type: "POST",
   url:  ajaxurl,
   data: {action: "oQeyUpdateImageDetails", splash:splash, splashexclusive:splashexclusive, imgid: imgid, galid:galid, alt:alt, comments:comments, oqey_image_link:oqey_image_link },
   success: function(r){
    
    var data = eval('(' + r + ')');
    
	//var id = jQuery("#sortablegalls .imgsel").attr("id");
    jQuery("#detailsimg").dialog('close');

    
	if(data.splash=="yes"){ 	
    if( jQuery("#sortablegalls li").hasClass("imgsel") ){ 
        jQuery("#sortablegalls li").removeClass("imgsel"); 
        }
    
        jQuery("#sortablegalls li").css("border", "thin solid #C1C1C1");
	    jQuery("#img_li_" + data.id).addClass('imgsel'); 
	    jQuery("#img_li_" + data.id).css("border", "#7A82DE thin solid"); 
	
	}else{	
	    jQuery("#img_li_" + data.id).removeClass('imgsel'); 
		jQuery("#img_li_" + data.id).css("border", "#C1C1C1 thin solid");
		
	}
   }
 });
});


});
	
function showSettings(id){ 
jQuery.post( ajaxurl, { action :"oQeyImageDetails", imagedetails: id },
            function(data){
				var data = eval('(' + data + ')');
				jQuery('#detailsimg #imgid').val(id);
				var galid = jQuery('#currentGalID').val();
				jQuery('#detailsimg #galid').val(galid);
				if(data.splash=="on"){ jQuery('#detailsimg #splash').attr('checked', true); }else{ jQuery('#detailsimg #splash').attr('checked', false); }
				if(data.splashexclusive=="on"){ jQuery('#detailsimg #splashexclusive').attr('checked', true); }else{ jQuery('#detailsimg #splashexclusive').attr('checked', false); }					 
				jQuery('#detailsimg #comments').val(decodeURIComponent(data.comments));
				jQuery('#detailsimg #alt').val(decodeURIComponent(data.alt));									
			   	jQuery('#detailsimg #oqey_image_link').val(decodeURIComponent(data.link));
                jQuery( "#detailsimg" ).dialog( "open" );
});
}
</script>
<div id="detailsimg" style="display:none;">
<form id="updateform" name="updateform" action="#null">
       <input type="hidden" id="imgid" name="imgid"/>
       <input type="hidden" id="galid" name="galid"/>
    <input name="splash" id="splash" type="checkbox" class="splash"/> &nbsp; Set this image as gallery splash.
	<p><input name="splashexclusive" id="splashexclusive" class="splashexclusive" type="checkbox"/> &nbsp; Make it splash exclusive.</p>
	<p>
	Description (alt):<br/>
	<textarea name="alt" rows="2" id="alt" class="alt" style="width:550px;"></textarea>
	</p>
	<p>
	Comments:<br/>
	<textarea name="comments" rows="3" id="comments" class="comments" style="width:550px;"></textarea>
	</p>
    <p>Link <small>(ex: http://oqeysites.com), *Note: work only with custom skins.</small><br />
    <input type="text" name="oqey_image_link" id="oqey_image_link" style="width:550px;" value="" /></p>
	<p>
	<input type="button" name="updatedetails" id="updatedetails" value="save details" />
	</p>
</form>
</div>