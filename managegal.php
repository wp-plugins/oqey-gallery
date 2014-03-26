<?php
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'managegal.php' == basename($_SERVER['SCRIPT_FILENAME'])) die ('Please do not load this page directly. Thanks!');
global $wpdb;
?>
<form>
<input type="hidden" id="trget" value=""/>
<input type="hidden" id="Qtw" value=""/>
<input type="hidden" id="Qth" value=""/>
<input type="hidden" id="Qttw" value=""/>
<input type="hidden" id="Qtth" value=""/>
<input type="hidden" id="imgid" value=""/>
<input type="hidden" id="coef" value=""/>
<input type="hidden" id="multiplicator" value=""/>
</form>

<script type="text/javascript">
var oqeynonce   = "<?php echo wp_create_nonce('oqey-upload')?>";
var galmainpage = "<?php echo admin_url('admin.php?page=oQeyGalleries')?>";
var actionlink  = "<?php echo oQeyPluginUrl()?>";
</script>

<?php   
   wp_enqueue_script('jquery');
   wp_enqueue_script('jquery-gallery-functions', WP_PLUGIN_URL . '/oqey-gallery/js/gallery.functions.js', array('jquery'));
?>

<input type="hidden" value="" id="currentGalID" name="currentGalID"/>
<div class="wrap">
    <h2 style="width: 930px;"><?php _e('Manage galleries', 'oqey-gallery'); ?></h2>
    <div id="error">&nbsp;</div>
</div>

<!--<img src="<?php //echo oQeyPluginUrl().'/images/'; ?>upperbanddress.jpg" width="950" height="120" /><br /><br />-->
<div class="postbox" style="height:50px; width:900px;">
    <div style="margin-right:2px; padding-top:3px; float:right;">
        <a href="<?php echo admin_url('admin.php?page=oQeySkins&showskins=yes'); ?>"><img src="<?php echo oQeyPluginUrl().'/images/'; ?>getmoreskinsbgn.png" width="250" height="48" /></a>    
    </div>
</div>

<div class="postbox" style="width:900px;">
<div id="creator">
<?php _e('Create a new gallery:', 'oqey-gallery'); ?>
  <input name="newtitle" id="newtitle" /> 
  <input class="button" type="button" name="creategall" id="creategall" value="<?php _e('Create', 'oqey-gallery'); ?>"/>
</div>
<div id="messages" style=" float:left;">&nbsp;</div>
<div id="magic">
       <a href="#create" id="createfromothers">
          <img src='<?php echo oQeyPluginUrl().'/images/wizard.png'; ?>' height="25" width="25" alt="Import from existing galleries" title="<?php _e('Import from existing galleries', 'oqey-gallery'); ?>"/>
       </a>
</div>
<br class="clear" />
</div>

<div class="postbox" id="createfrompostbox">
    <div id="importlist"><?php _e('Loading content...', 'oqey-gallery'); ?></div>
</div>

<div class="postbox" style="width:900px; display:none;" id="titlul_b">
    <div id="titlul">
        <div class="obis"><?php _e('Loading content...', 'oqey-gallery'); ?></div>
    </div>
</div>

<div class="postbox" style="width:900px;">
    <div id="content">
        <div class="obis"><?php _e('Loading content...', 'oqey-gallery'); ?></div>
    </div>
</div>

<div class="postbox" id="watermarkpostbox">
   <div id="watermarklist"><div class="obis"><?php _e('Loading content...', 'oqey-gallery'); ?></div></div>
</div>

<div class="postbox" id="musicpostbox">
    <div id="musiclist">
        <div class="obis"><?php _e('Loading content...', 'oqey-gallery'); ?>.</div>
    </div>
</div>

<div class="postbox" id="skinpostbox">
    <div id="skinlist">
        <div class="obis"><?php _e('Loading content...', 'oqey-gallery'); ?></div>
    </div>
</div>

<div class="galleriesbox" id="galleryarea">
    <div id="gallery_content"><div class="obis"><?php _e('Loading content...', 'oqey-gallery'); ?></div></div>
    <br class="clear" />
</div>
<!--
<div class="postbox" style="width:900px;">
    <div align="left" style="margin:15px;">
        <?php //_e('Notes', 'oqey-gallery'); ?>: <br />
        * <?php //echo oqey_uploadSize(); ?><br />
    </div>
</div> -->
<!--<a href="http://www.oqeysites.com" target="_blank"><img src="<?php //echo oQeyPluginUrl().'/images/'; ?>galleries_banner.png"/></a>-->

<script type="text/javascript">
jQuery(document).ready(function($){    
    
jQuery.loadImages([ '<?php echo oQeyPluginUrl().'/images/preview_button.png'; ?>', 
                    '<?php echo oQeyPluginUrl().'/images/remove_button.png'; ?>', 
                    '<?php echo oQeyPluginUrl().'/images/edit_button.png'; ?>', 
                    '<?php echo oQeyPluginUrl().'/images/remove_button.png'; ?>', 
                    '<?php echo oQeyPluginUrl().'/images/settings_small_button.png'; ?>', 
                    '<?php echo oQeyPluginUrl().'/images/remove_small_button.png'; ?>', 
                    '<?php echo oQeyPluginUrl().'/images/skin_button.png'; ?>', 
                    '<?php echo oQeyPluginUrl().'/images/music_button.png'; ?>', 
                    '<?php echo oQeyPluginUrl().'/images/ui-icons_222222_256x240.png'; ?>', 
                    '<?php echo oQeyPluginUrl().'/images/ui-icons_454545_256x240.png'; ?>', 
                    '<?php echo oQeyPluginUrl().'/images/ui-icons_888888_256x240.png'; ?>' ],function(){});

/*loading galleries*/ //oQeyGetAllGalleries	
jQuery.post( ajaxurl, {action: 'oQeyGetAllGalleries', allgalls: "yes" }, function(data){
    
    jQuery('#content').hide().html(data).fadeIn("slow");
    
        jQuery('.preview-gallery').click(function(){
		  
		var id            = jQuery(this).attr("id");
		var playerVersion = swfobject.getFlashPlayerVersion();
                
		if(playerVersion.major<8){	
                    
                    var flash = "no";
                
                  }else{
                      
                    var flash = "yes";
                }
		
             jQuery.post( ajaxurl, { action: 'oQeyGetPreviewGallery', previewid: id, flash: flash  }, function(r){   
    
		var $dialog = jQuery('<div style="height:650px; width:896px; position:relative; display:block;"><\/div>').html(r).dialog({
			width: 896,
			maxWidth: 896,
			maxHeight: 650,
			resizable: false,
			autoOpen: false,
			title: '<?php _e('Preview gallery', 'oqey-gallery'); ?>',
			modal: true,
			draggable: false	
		});	
		
		$dialog.dialog('open'); 
		});
		return false;
	});
   
    jQuery('.dblclick').editable( ajaxurl, {
            indicator : '<?php _e('Updating...', 'oqey-gallery'); ?>',
            tooltip   : "<?php _e('Double-click to rename...', 'oqey-gallery'); ?>",
            event     : "dblclick",
            width     : "500px",
            height    : "20px",
            submitdata: function ( value, settings ) { return { "action": 'oQeyEditGalleryTitle' }; }
    });
    
    jQuery("a[href='#editoqeygallery']").click(function(){
        
        var id = jQuery(this).attr("rel");  
        getGalleryDetails(id);
        
    });
    
   hoverGallery();
});

jQuery('#newtitle').keypress(function(e){
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
          
          jQuery('#othersgalls').change(function(){
                
             jQuery('#otgallstitle').remove();
                
               if(jQuery(this).val()!=0){
               
                 jQuery.post( ajaxurl, { action: "oQeyCheckForOthersGalleries", info: jQuery(this).val() },
                 function(data){ 
                     jQuery('#importlist').append(data);
                     jQuery('#otgallstitle').change(function(){
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
	  setTimeout(function (){ jQuery('#importlist').html('<?php _e('Loading content...', 'oqey-gallery'); ?>');  }, 1000);	  
      
    }
});
/*END import*/

function clearDiv(){ setTimeout(function () {  jQuery('#messages').fadeOut(function(){ jQuery("#messages").html("&nbsp;"); }); }, 2000); } 

	jQuery( "#detailsimg" ).dialog({
			width: 600,
			height: 420,
			maxWidth: 600,
			maxHeight: 420,
			resizable: false,
			autoOpen: false,
			modal: true,
			title: "Details",
			draggable: false
		});
		
jQuery("#updatedetails").click(function(){            

   if(jQuery("#updateform #splash").is(':checked')){ var splash="on"; }else{ var splash= ""; } 
   if(jQuery("#updateform #splashexclusive").is(':checked')){ var splashexclusive="on"; }else{ var splashexclusive= ""; }
   
   var  imgid           = encodeURIComponent( jQuery("#updateform #imgid").val() );
   var  galid           = encodeURIComponent( jQuery("#updateform #galid").val() );
   var  alt             = encodeURIComponent( jQuery("#updateform #alt").val() );
   var  comments        = encodeURIComponent( jQuery("#updateform #comments").val() );
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

}

/*Add selected video*/
function SaveVideoImage(){
    
   var alldata = jQuery("#videoslist").serialize();
   var galid   = jQuery('#currentGalID').val();
    
   jQuery.ajax({
       type: "POST",
       url:  ajaxurl,
       data: {action: "oQeyAddVideoToGallery", data: alldata, galid: galid },
       success: function(r){

            getGalleryDetails(galid);
            jQuery("#oqeyvideomanagement").dialog('close');
   
       }
   });

}
</script>

<div id="detailsimg" style="display:none;">

  <form id="updateform" name="updateform" action="#null">

    <input type="hidden" id="imgid" name="imgid"/>
    <input type="hidden" id="galid" name="galid"/>
    
    <p id="p_splash"><input name="splash" id="splash" type="checkbox" class="splash"/> &nbsp; <?php _e('Set this photo as a gallery splash.', 'oqey-gallery'); ?></p>
	<p id="p_splashexclusive"><input name="splashexclusive" id="splashexclusive" class="splashexclusive" type="checkbox"/> &nbsp; <?php _e('Make it splash exclusive.', 'oqey-gallery'); ?></p>    
	<p>
	<?php _e('Description', 'oqey-gallery'); ?> (alt):<br/>
	<textarea name="alt" id="alt" class="alt" style="height:60px; width:550px;"></textarea>
	</p>
    
	<p>
	<?php _e('Comments', 'oqey-gallery'); ?> :<br/>
	<textarea name="comments" id="comments" class="comments" style="height:75px; width:550px;"></textarea>
	</p>
    
    <p>Link <small>(ex: http://oqeysites.com), * <?php _e('Note: will work with supported commercial skins only.', 'oqey-gallery'); ?></small><br />
    <input type="text" name="oqey_image_link" id="oqey_image_link" style="width:550px;" value="" /></p>
	<p>
	<input class="button" type="button" name="updatedetails" id="updatedetails" value="save details" />
	</p>
  
  </form>

</div>

<div id="oqeyvideomanagement" style="display:none; margin:10px;">
<div style="overflow-y:auto;overflow-x: hidden;height:483px;border:#999 thin solid;padding:5px;background:#ccc;border-radius:3px;" id="video_content"></div>
<div style="margin:10px;text-align:center;vertical-align: middle;">
<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="oqey_license" />
<input class="button" type="button" class="button-primary" style="width:100px; margin-top:5px;" value="<?php _e('Add video', 'oqey-gallery'); ?>" onclick="SaveVideoImage(); return false;" />
</div>
</div>