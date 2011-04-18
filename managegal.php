<?php
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'managegal.php' == basename($_SERVER['SCRIPT_FILENAME'])) die ('Please do not load this page directly. Thanks!');
global $wpdb, $current_user;
   $oqey_galls = $wpdb->prefix . "oqey_gallery";
   $oqey_images = $wpdb->prefix . "oqey_images";
   $oqey_music = $wpdb->prefix . "oqey_music";
   $oqey_music_rel = $wpdb->prefix . "oqey_music_rel";  
      
?>
<script type="text/javascript">
function refreshPage(){ var id = jQuery('#currentGalID').val(); getGalleryDetails(id); }
function clearUp(){ setTimeout(function () {  jQuery('#messages').fadeOut(function(){ jQuery("#messages").html("&nbsp;"); }); }, 2000); }

function deleteImage(id){  //move to trash single image
jQuery.post( "<?php echo oQeyPluginUrl(); ?>/do.php", { delimgid: id },
            function(data){
			jQuery("#messages").hide().html('<p class="updated fade">Image was moved to trash. <a href="#undo" id="undoimage">undo<\/a><\/p>').fadeIn("slow");
            jQuery('#img_li_'+id).hide(1000);
			jQuery("#undoimage").click(function(){
			jQuery.post( "<?php echo oQeyPluginUrl(); ?>/do.php", { undoimgid: id },
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
			jQuery('#galleryarea').fadeIn("slow");
			jQuery('#currentGalID').attr('value', id);			
	        jQuery.post( "<?php echo oQeyPluginUrl(); ?>/do.php", { newgallid: id, wpnonce: '<?php echo wp_create_nonce('oqey-upload'); ?>' },
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
			jQuery.post( "<?php echo oQeyPluginUrl(); ?>/do.php", { usrid: usrid, gid: id },function(data){ } );
			});
			
			jQuery("#view_all_galleries").click(function(){
			window.location = "<?php echo get_option("siteurl"); ?>/wp-admin/admin.php?page=oQeyGalleries";										 
		    });
			
			var playerVersion = swfobject.getFlashPlayerVersion();
		    if(playerVersion.major<8){	
			ajaxUploader(data.noflashinfo);
			}
			jQuery.post( "<?php echo oQeyPluginUrl(); ?>/do.php", { allimages: data.galid },
            function(data){
			var data = eval('(' + data + ')');

            jQuery('#gallery_content').hide().html(decodeURIComponent(data.allimages)).fadeIn("slow");
 		    jQuery("#sortablegalls").selectable("disable");
            
			jQuery("#sortablegalls").sortable({ update: function(){	//onupdate update the image order		
			var allimgs = jQuery('#sortablegalls').sortable('serialize');						
			jQuery.post( "<?php echo oQeyPluginUrl(); ?>/do.php", { orderallimgs: allimgs, galleryid: id },
            function(data){}); } });
 
    jQuery(".dblclick").editable( "<?php echo oQeyPluginUrl(); ?>/do.php", { 
			name: "gal_edit_title",
			id: "gal_edit_id",
            indicator : 'Updating...',
            tooltip   : "Double-click to rename...",
            event     : "dblclick",
            style  : "inherit",
	        width : "670px",
	        height : "15px"
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
	  jQuery.post( "<?php echo oQeyPluginUrl(); ?>/do.php", { music_gall_id: id },
            function(data){  			
			jQuery('#musiclist').hide().html(data).fadeIn("slow"); 
			jQuery("#sortablemuzon").sortable({ 
 	         revert: true 
 		    });
			
			jQuery("#savemusic").click(function(){
            var selectedmusic = jQuery('#musicselect').serializeArray();
			var id = jQuery('#currentGalID').val();	
            jQuery.post( "<?php echo oQeyPluginUrl(); ?>/do.php", { selectedmusic: selectedmusic, mgalleryid: id },
            function(data){ jQuery("#messages").hide().html('<p class="updated fade">' + data + '<\/p>').fadeIn("slow"); clearUp();  }
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
	  jQuery.post( "<?php echo oQeyPluginUrl(); ?>/do.php", { skin_gall_id: id },
            function(data){  			
			
			jQuery('#skinlist').hide().html(data).fadeIn("slow"); 

			jQuery(".activate_skin").click(function(){			 
			 var skinid = jQuery(this).attr("id");			 
			jQuery.post( "<?php echo oQeyPluginUrl(); ?>/do.php", { skinid: skinid, skin_gallery_id: id },
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
jQuery.post( "<?php echo oQeyPluginUrl(); ?>/do.php", { orderallimgs: allimgs, galleryid: gid, imgreverse: "yes" },
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
		
        jQuery.post( "<?php echo oQeyPluginUrl(); ?>/do.php", { imgalltotrash: encodeURIComponent(names) },
        function(data){
		if(names.length>0){
		jQuery("#messages").hide().html(decodeURIComponent('<p class="updated fade">' + data + ' <a href="#undo" id="undoallimages">undo<\/a><\/p>')).fadeIn("slow"); 

			jQuery("#undoallimages").click(function(){
			jQuery.post( "<?php echo oQeyPluginUrl(); ?>/do.php", { imgallfromtrash: encodeURIComponent(names) },
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
            jQuery.post( "<?php echo oQeyPluginUrl(); ?>/do.php", { movetotrashgall: id },
            function(data){
            jQuery('#row_' + id).fadeOut("slow");					
			jQuery("#messages").hide().html('<p class="updated fade">' + data + ' <a href="#undo" id="undogallery">undo<\/a><\/p>').fadeIn("slow");
			
			jQuery("#undogallery").click(function(){
			jQuery.post( "<?php echo oQeyPluginUrl(); ?>/do.php", { undogallid: id },
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

<div class="postbox" style="width:900px;">
<div id="creator">
Create a new gallery: 
  <input name="newtitle" id="newtitle" /> <input type="button" name="creategall" id="creategall" value="create"/>
</div>
<div id="messages">&nbsp;</div>
<br class="clear" />
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

<script type="text/javascript">
jQuery(document).ready(function($){
jQuery.loadImages([ '<?php echo oQeyPluginUrl().'/images/preview_button.png'; ?>', '<?php echo oQeyPluginUrl().'/images/remove_button.png'; ?>', '<?php echo oQeyPluginUrl().'/images/edit_button.png'; ?>', '<?php echo oQeyPluginUrl().'/images/remove_button.png'; ?>', '<?php echo oQeyPluginUrl().'/images/settings_small_button.png'; ?>', '<?php echo oQeyPluginUrl().'/images/remove_small_button.png'; ?>', '<?php echo oQeyPluginUrl().'/images/skin_button.png'; ?>', '<?php echo oQeyPluginUrl().'/images/music_button.png'; ?>', '<?php echo oQeyPluginUrl().'/images/ui-icons_222222_256x240.png'; ?>', '<?php echo oQeyPluginUrl().'/images/ui-icons_454545_256x240.png'; ?>', '<?php echo oQeyPluginUrl().'/images/ui-icons_888888_256x240.png'; ?>' ],function(){});

<?php if(get_option("oqey_license")==""){ ?>  	  
	  jQuery("#oqey_licence").dialog({ width: 896, height: 615, resizable: false, autoOpen: true, title: 'oQey Gallery License Agreement', modal: true, draggable: false });
<?php } ?>	


/*loading galleries*/	
jQuery.post( "<?php echo oQeyPluginUrl(); ?>/do.php", { allgalls: "yes" },
   function(data){
        jQuery('#content').hide().html(data).fadeIn("slow");

		jQuery('.preview-gallery').click(function(){
		var id = jQuery(this).attr("id");
		var playerVersion = swfobject.getFlashPlayerVersion();
		if(playerVersion.major<8){	
		var flash = "no";
	    }else{
		var flash = "yes";
		}
		
jQuery.post( "<?php echo oQeyPluginUrl(); ?>/do.php", { previewid: id, flash: flash  },
   function(r){       
		var $dialog = jQuery('<div style="height:590px; width:896px; position:relative; display:block;"><\/div>').html(r).dialog({
			width: 896,
			maxWidth: 896,
			maxHeight: 590,
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
	        jQuery(".dblclick").editable( "<?php echo oQeyPluginUrl(); ?>/do.php", { 
			name: "gal_edit_title",
			id: "gal_edit_id",
            indicator : 'Updating...',
            tooltip   : "Double-click to rename...",
            event     : "dblclick",
            style  : "inherit",
	        width : "530px",
	        height : "15px"
   });
   hoverGallery();
});

jQuery('#newtitle').keypress(function(e) {
        if(e.which == 13) {
            jQuery('#creategall').trigger("click");
        }
    });

jQuery("#creategall").click(function(){ 
var newtitle = jQuery("#newtitle").val();

jQuery.post( "<?php echo oQeyPluginUrl(); ?>/do.php", { newtitle: newtitle },
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
})

function clearDiv(){ setTimeout(function () {  jQuery('#messages').fadeOut(function(){ jQuery("#messages").html("&nbsp;"); }); }, 2000); } });
jQuery(function() {
	jQuery( "#detailsimg" ).dialog({
			width: 600,
			height: 350,
			maxWidth: 600,
			maxHeight: 350,
			resizable: false,
			autoOpen: false,
			modal: true,
			title: "Image details",
			draggable: false	
		});
		
jQuery( "#updatedetails" ).click(function() {            
   var alldata = jQuery("#updateform").serialize();
   jQuery.ajax({
   type: "POST",
   url:  "<?php echo oQeyPluginUrl(); ?>/do.php",
   data: alldata,
   success: function(data){
	var id = jQuery("#sortablegalls .imgsel").attr("id");
    jQuery("#detailsimg").dialog('close');
	
	if(!isNaN(data)){ 	
    if( jQuery("#sortablegalls li").hasClass("imgsel") ) { jQuery("#sortablegalls li").removeClass("imgsel"); } 
    jQuery("#sortablegalls li").css("border", "thin solid #C1C1C1");
	jQuery("#img_li_" + data).addClass('imgsel'); 
	jQuery("#img_li_" + data).css("border", "#7A82DE thin solid"); 
	
	}else{	
	    jQuery(id).addClass('imgsel'); 
		jQuery(id).css("border", "#7A82DE thin solid");
		
	}
   }
 });
});
});
	
function showSettings(id){ 
jQuery.post( "<?php echo oQeyPluginUrl(); ?>/do.php", { imagedetails: id },
            function(data){
				var data = eval('(' + data + ')');
				jQuery('#detailsimg #imgid').val(id);
				var galid = jQuery('#currentGalID').val();
				jQuery('#detailsimg #galid').val(galid);
				if(data.splash=="on"){ jQuery('#detailsimg #splash').attr('checked', true); }else{ jQuery('#detailsimg #splash').attr('checked', false); }
				if(data.splashexclusive=="on"){ jQuery('#detailsimg #splashexclusive').attr('checked', true); }else{ jQuery('#detailsimg #splashexclusive').attr('checked', false); }					 
				jQuery('#detailsimg #comments').val(decodeURIComponent(data.comments));
				jQuery('#detailsimg #alt').val(decodeURIComponent(data.alt));									
			   	jQuery( "#detailsimg" ).dialog( "open" );
});
}
</script>
<div id="detailsimg" style="display:none;">
<form id="updateform" name="updateform" action="#null">
       <input type="hidden" id="imgid" name="imgid"/>
       <input type="hidden" id="galid" name="galid"/>
    <p><input name="splash" id="splash" type="checkbox" class="splash"/> &nbsp; Set this image as gallery splash.</p>
	<p><input name="splashexclusive" id="splashexclusive" class="splashexclusive" type="checkbox"/> &nbsp; Make it splash exclusive.</p>
	<p>
	Description (alt):<br/>
	<textarea name="alt" rows="2" id="alt" class="alt" style="width:550px;"></textarea>
	</p>
	<p>
	Comments:<br/>
	<textarea name="comments" rows="3" id="comments" class="comments" style="width:550px;"></textarea>
	</p>
	<p>
	<input type="button" name="updatedetails" id="updatedetails" value="save details" />
	</p>
</form>
</div>

<div id="oqey_licence" style="display:none; margin:15px;">
<div style="overflow-y: auto; height:475px; border:#999 thin solid; padding:5px; text-align:justify;">
<?php echo OQEY_LICENSE_TEXT; ?> 
</div>
<div style="margin:10px; text-align:center; vertical-align: middle;">
<form method="post" action="options.php">
<?php wp_nonce_field('update-options'); ?>
<table width="400" border="0" cellspacing="0" cellpadding="0" align="center" id="license" style="margin-left:260px;">
<tr valign="top">
<td width="25" height="25" align="left" valign="middle" scope="row">
<input type="checkbox" name="oqey_license" id="oqey_license" <?php if(get_option('oqey_license')=="on"){ echo 'checked="checked"';  } ?> /></td>
<td width="875" height="25" align="left" valign="middle" scope="row">I agree with the terms of this License Agreement</td>
</tr>
</table>
<input type="hidden" name="action" value="update" /><input type="hidden" name="page_options" value="oqey_license" />
<input type="submit" class="button-primary" style="width:50px; margin-top:5px;" value="<?php _e('Ok') ?>" />
</form>
</div>
</div>