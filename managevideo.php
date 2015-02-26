<?php
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'managevideo.php' == basename($_SERVER['SCRIPT_FILENAME'])) die ('Please do not load this page directly. Thanks!');

global $wpdb;
$oqey_video = $wpdb->prefix . "oqey_video";

if(isset($_REQUEST['extvideoname']) && isset($_REQUEST['savevideo'])){
    
   global $wpdb;
   $oqey_video = $wpdb->prefix . "oqey_video";

   if( !empty($_REQUEST['extvideoname']) ){
                
                 $sql = $wpdb->query( $wpdb->prepare( "INSERT INTO $oqey_video (title, video_link, type) 
                                                                        VALUES ( %s, %s, %s )",
                                                                                time(),
                                                                                $_REQUEST['extvideoname'],
                                                                                'external'                                                                                                                                                                                                                            
                                                    )
                                    );
                 
   if($sql){
   
     $mesaj = '<div class="wrap">
               <div class="updated below-h2" id="message">
               <p>'.__('External video link has been registered.', 'oqey-gallery').'</p>
               </div>
               </div>';
     }else{
    
     $mesaj = '<div class="wrap">
               <div class="error fade" id="message">
               <p>'.__('Erorr!. Please try again.', 'oqey-gallery').'</p>
               </div>
               </div>';
    
     }
   }
 }

if(isset($_GET['scaner'])){

  $ext = array('flv', 'f4p', 'fpv', 'mp4', 'm4v', 'm4a', 'mov', 'mp4v', '3gp', '3g2' );
  $root = rtrim(OQEY_ABSPATH, '/');//."wp-content/oqey_gallery/video/".oqey_getBlogFolder($wpdb->blogid);
  $files = oqey_get_all_files($root, $ext );


  if(file_exists($root)){

    global $wpdb;
    $oqey_video = $wpdb->prefix . "oqey_video"; 
    $root = $root."/";
    $d=0;

    foreach($files as $file){

      $path = pathinfo($file);

        if (in_array( strtolower($path['extension']), $ext) ){	

        $video_link = trim(sanitize_title($path['filename'])).".".$path['extension'];
        $title = $path['filename'];

        $s_video_link = $path['dirname'].'/'.$video_link;
        $video_link = str_replace(OQEY_ABSPATH, "", $s_video_link);

          if(!$sql=$wpdb->get_row( $wpdb->prepare( "SELECT * FROM $oqey_video WHERE video_link = %s ", $video_link) ) ){
              
              $sql = $wpdb->query( $wpdb->prepare( "INSERT INTO $oqey_video (title, video_link, type) 
                                                                        VALUES ( %s, %s, %s )",
                                                                                $title,
                                                                                $video_link,
                                                                                'oqey'                                                                                                                                                                                                                            
                                                    )
                                                    );
              
              
              rename($file, $s_video_link);
              $d++;
          }
        }
    }

  }
   
    $mesaj = '<div class="wrap">
              <div class="updated below-h2" id="message">
              <p>'.$d."&nbsp;".__('new video files found', 'oqey-gallery').'</p>
              </div>
              </div>';
              
 }

?>

<input type="hidden" id="vid" value=""/>
      
   <div class="wrap">
    <h2 style="width: 930px;"><?php _e('Manage Video', 'oqey-gallery'); ?></h2>
  </div> 
      
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td colspan="6">     
      </td>
  </tr>
  <tr>
    <td colspan="6"> 
        <div id="save" style="width:917px; margin-bottom:10px;">
          <?php if(!empty($mesaj)){ echo $mesaj; } ?>
        </div>    
    </td>
  </tr>
  <tr>
    <td>
    
<div class="postbox" style="width:900px;">   
    <table width="900" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
    <td width="128" height="50" align="center"><?php _e('Upload video', 'oqey-gallery'); ?></td>
    <td colspan="2" align="right" valign="middle">
        <div class="uploader" id="flashuploader">
            <input type="button" name="uploadimages" id="uploadimages" value="<?php echo __('Upload', 'oqey-gallery'); ?>" class="button" style="float:left;"/>
        </div>
 
<?php  
  $ulocation = oQeyPluginRepoPath().'/video/'.oqey_getBlogFolder($wpdb->blogid);
  if(!is_dir($ulocation)){
    wp_mkdir_p($ulocation);
  }
?> 
  </td>
    <!--<td width="26" valign="middle"> /</td>-->
    <td valign="middle">
    <div style="padding-top: 3px;" align="left">
    
    <form id="scaner" name="scaner" method="post" action="<?php echo admin_url('admin.php?page=oQeyVideo&scaner=true'); ?>">     
        <input class="button" name="scanfolder" id="scanfolder" value="<?php _e('Magic Scan', 'oqey-gallery'); ?>" type="submit"/>              
    </form>
    
    </div>    
    
    </td>
    <td width="257" valign="middle"></td>
    <td width="128" valign="middle">&nbsp;</td>
    <td width="64" valign="middle">&nbsp;</td>
    <td width="65" valign="middle"> 
        <a href="#add_video" class="videolink" id="videolink">
          <img src="<?php echo oQeyPluginUrl().'/images/exlinkvideo.png'; ?>" title="<?php _e('add an external video file', 'oqey-gallery'); ?>" />
        </a>
    </td>
    </tr>
    </table>
</div>

<div class="postbox" id="externalvideo" style="width:900px; display:none;">

<form id="addextvideofile" name="addextvideofile" method="post" action="<?php echo admin_url('admin.php?page=oQeyVideo'); ?>">
<table width="880" border="0" cellspacing="0" cellpadding="0" style="margin:10px;">
  <tr>
    <td height="26" colspan="3">&nbsp;<?php _e('Add an external video link', 'oqey-gallery'); ?> (http://domain.com/my-video-file.flv)</td>
    </tr>
  <tr>
    <td width="614"><input name="extvideoname" type="text" id="extvideoname" style="width:600px;"/></td>
    <td width="266" colspan="2"><input class="button" type="submit" name="savevideo" id="savevideo" value="<?php _e('Add video', 'oqey-gallery'); ?>" /></td>
    </tr>
</table>
</form>
</div>

    </td>
  </tr>
  <tr>
    <td height="1" colspan="6"></td>
  </tr>
  <tr>
    <td colspan="6">

<form action="#" name="formvideo" id="formvideo">
<div class="postbox" style="width:900px;">
<table width="900" border="0" cellspacing="0" cellpadding="0" id="musictable" class="tablesorter">
  <tbody id="sortable">
    <?php
	$i=1;
	$videos = $wpdb->get_results("SELECT * FROM $oqey_video WHERE status !=2 ORDER BY ID DESC");
    
    if(!empty($videos)){
        
	foreach ($videos as $video){
            
            if(is_file(OQEY_ABSPATH.trim($video->video_link))){ 
    ?>
    <tr style="width:900px;" id="row_<?php echo $video->id; ?>">
    <td width="170" align="center" valign="middle" style="padding-right:10px; padding-top:10px; padding-bottom:10px; padding-left:0px;">
    <div style="position: relative; width:150px;" class="videoimg" id="<?php echo $video->id; ?>">
    <?php	
    $img_file = $video->video_image;
    $imgroot  = OQEY_ABSPATH.trim($img_file);
    
    if(!empty($img_file) && is_file($imgroot)){ 
        
        echo '<img src="'.get_option('siteurl').'/'.$img_file.'" style="height:100px; max-width:150px; position: relative; z-index: 50;" class="videoimage" title="Edit photo" />';
        echo '<div style="top: 0px; left: 0px; position: absolute; width:150px; height:100px; z-index: 100; display:none;" class="vedit">';
        echo '<img src="'.oQeyPluginUrl().'/images/with-photo.png" height="100" width="150" title="'.__('Edit photo', 'oqey-gallery').'" /></div>';
    
    }else{
     
        echo '<img src="'.oQeyPluginUrl().'/images/no-photo.jpg" alt="video_img" class="videoimage" style="height:100px; max-width:150px; position: relative; z-index: 50;" title="Add photo" />';
        echo '<div style="top: 0px; left: 0px; position: absolute; width:150px; height:100px; z-index: 1; display:none;" class="2vedit">';
        echo '<img src="'.oQeyPluginUrl().'/images/with-photo.png" height="100" width="150" title="'.__('Edit photo', 'oqey-gallery').'" /></div>';
	
    }
    
?>
    </div>
    </td>
    <td width="635" style="padding-top:10px; vertical-align:top; text-align:left; line-height:1.5;" >
        
        <table width="592" border="0" cellspacing="0" cellpadding="0">
        <tr>
         <td width="90" style="height: 22px; vertical-align: middle;"><?php _e('Title', 'oqey-gallery'); ?>:</td>
         <td width="502"><span class="dblclick" id="video_title_<?php echo $video->id; ?>"><?php echo $video->title; ?></span></td>
        </tr>
        <tr>
         <td style="height: 17px; vertical-align: middle;"><?php //_e('Shortcode', 'oqey-gallery'); ?></td>
         <td><!--<span style="background-color: rgb(204, 204, 204); width:auto;">[oqeyvideo id=<?php //echo $video->id; ?>]</span> --></td>
        </tr>
        <tr>
         <td valign="top"><?php _e('Description', 'oqey-gallery'); ?>:</td>
         <td><span class="dblclickdesc" id="video_desc_<?php echo $video->id; ?>"><?php echo $video->description; ?></span></td>
       </tr>
       </table>    
    </td>
    <td width="39" align="center" valign="middle" class="lasttd"> 
      <!--<a href="#default" class="hiddenm"><?php //echo $defaultimg; ?></a>    -->
    </td>
    <td width="32" align="center" valign="middle" class="lasttd"> 
      <a href="#null" onclick="VideoDelete('<?php echo $video->id; ?>', '<?php echo $video->video_link; ?>'); return false;" class="hiddenm">
      <img src="<?php echo oQeyPluginUrl(); ?>/images/remove_button.png" width="22" height="22" title="<?php _e('Move to trash this video', 'oqey-gallery'); ?>"/></a>    
    </td>
  </tr>
  <?php $i++; //hiddenm
    
        }else{
           
           $wpdb->query( $wpdb->prepare("DELETE FROM $oqey_video WHERE id = %d ", absint($video->id) ) );
           $wpdb->query( $wpdb->prepare("DELETE FROM $oqey_images WHERE video_id = %d ", absint($video->id) ) );
        }
  
  
    }
  }else{
    
    echo '<tr><td style="padding:10px;">'.__('There is no video files.', 'oqey-gallery').'</td></tr>';
    
  } 
  
  ?>
  </table>
 </div>
</form>

<div class="postbox" style="width:900px;">
<div align="left" style="margin:15px;">
<?php _e('Notes', 'oqey-gallery'); ?>: <br />
         * <?php echo oqey_uploadSize(); ?><br />
         * <?php _e('You may upload new video files directly to your plugin video directory via ftp', 'oqey-gallery'); ?>.<br />
         * <?php _e('Your video folder location', 'oqey-gallery'); ?>:<b> <?php echo oQeyPluginRepoUrl().'/video/'.oqey_getBlogFolder($wpdb->blogid); ?></b>    
  </div>
</div> 

</td>
  </tr>
</table>
<script type="text/javascript">

//delete video file
function VideoDelete(id, title){
        
        jQuery.post( ajaxurl, { action :"oQeyTrashVideoFile", id: id },
            function(data){                
                
                var data = eval('(' + data + ')');
                
                jQuery("#save").hide().html('<div class="wrap"><div class="updated fade" id="message"><p>' + data.info + ' <a href="#undo" id="undovideo"><?php _e('undo', 'oqey-gallery'); ?><\/p><\/div><\/div>').fadeIn('slow');
                jQuery("#row_" + id + "").hide("slow");
                   
              if(data.defvid!=0 && data.defvid!=null){
           
              var d    = new Date();
              var src1 = "<?php echo oQeyPluginUrl(); ?>/images/checked.png?" + d.getMilliseconds();
              var src2 = "<?php echo oQeyPluginUrl(); ?>/images/unchecked.png?" + d.getMilliseconds();              
              var f    = jQuery("#sortable").find(".defaultvideo");  
                            
              jQuery(f).attr("src", src2 );
              jQuery(f).attr("title", "Set as default video" );
              jQuery(f).removeClass("defaultvideo").addClass("setdefaultvideo");
              
              jQuery('#v' + data.defvid + '').attr("src", src1 );       
              jQuery('#v' + data.defvid + '').attr("title", "Default video" );
              jQuery('#v' + data.defvid + '').removeClass("setdefaultvideo").addClass("defaultvideo");
              
            }
                
           
                jQuery("#undovideo").click(function(){ 
                    
                     jQuery.post( ajaxurl, { action :"oQeyFromTrashVideoFile", id: id },
                            function(data){         
                                var data = eval('(' + data + ')');
                                jQuery("#row_" + id).show("slow");
                                jQuery("#save").hide().html('<div class="wrap"><div class="updated fade" id="message"><p>' + data.mesaj + '<\/p><\/div><\/div>').fadeIn('slow');
                                clearUp();
   
                            });
                }); 
                
   });    
}

//scan video files
function VideoScan(){
  window.location = "<?php echo admin_url('admin.php?page=oQeyVideo&scaner=true'); ?>";
}

//clear response div 
function clearUp(){ 
    setTimeout(function (){ 
        jQuery('#save').fadeOut(function(){ 
            jQuery("#save").html("&nbsp;");
        }); 
    }, 3000); 
}

//uploader use this function
function refreshPage(){
    window.location = "<?php echo admin_url('admin.php?page=oQeyVideo'); ?>";
}

function SaveVideoImage(){
    
    
    var img    = jQuery("#all_images").find(".selectedimg");    
    var imgurl = jQuery(img).attr("src");
    var id     = jQuery("#vid").val();
    
    jQuery("#all_images").dialog('close'); 
    
    jQuery.post( ajaxurl, { action :"oQeySaveVideoImg", imgurl: imgurl, id: id },
      function(data){  
          
          var imgsrc  = jQuery("#row_" + id).find("img.videoimage");
          var editimg = jQuery("#row_" + id).find(".2vedit");  
              
          jQuery(editimg).removeClass("2vedit").addClass("vedit");
          
          var fullsrc = "<?php echo get_option("siteurl")."/"; ?>" + imgurl;
          var imgsrc  = jQuery(imgsrc).attr("src", imgurl);  
        
          jQuery("#all_images").dialog('close');  
          
          
   jQuery(".vedit").click(function(){ 
       
        var id = jQuery(this).parent(".videoimg").attr("id");
        jQuery("#vid").attr("value", id);
        jQuery.ajax({
		  url    : ajaxurl,
		  type   : "POST",
		  data   : {action: 'oQeyScanForImagesForVideo', id: id},
		  cache  : false,
		  success: function(data){			
                      
                           jQuery("#video_content").html(data);
                           jQuery("#all_images").dialog({ width: 860, height: 600, resizable: false, autoOpen: true, title: '<?php _e('Images', 'oqey-gallery'); ?>', modal: true, draggable: false });
            
                           jQuery(".videoimage").click(function(){
                               
                                  jQuery('#all_images div').each(function(){
                                      
                                        jQuery(this).css("background", "#ccc");
                                        jQuery(this).children().removeClass("selectedimg");
                                  
                                  });
                                  
                           jQuery(this).parent().css("background", "#AECFF0");
                           jQuery(this).addClass("selectedimg");
                       }); 
                       
                       },
          	   error: function(){
                           jQuery("#save").hide().html('<div class="wrap"><div class="updated fade" id="message"><p><?php _e('There is an error, please try again.', 'oqey-gallery'); ?><\/p><\/div><\/div>').fadeIn('slow');
                           clearUp();
		    }
                    }); 
                    
    });
             
   });  
    
}

jQuery(document).ready(function($){  
    
    
    var ds = ds || {};
	var media;
	ds.media = media = {
		buttonId: '#uploadimages',
		//detailsTemplate: '#attachment-details-tmpl',
                init: function() {
                    
			jQuery( media.buttonId ).on( 'click', function( e ) {
				e.preventDefault();
				media.frame().open();
			});                        
		},
                frame: function() {
			if ( this._frame )
				return this._frame;
			this._frame = wp.media( {
				title: 'Select Video Files',
				multiple: true,
				library: {
					type: 'video'
				}
			} );
			this._frame.on( 'ready', this.ready );
			this._frame.state( 'library' ).on( 'select', this.select );
                        this._frame.on( 'close', this.close );
			return this._frame;
		},
		ready: function() {
			//jQuery( '.media-modal' ).addClass( 'no-sidebar smaller' );
		},
		select: function() {
			var settings = wp.media.view.settings,
			selection = this.get( 'selection' );
			jQuery( '.added' ).remove();
                        
                        
		},
		showAttachmentDetails: function( attachment ) {
                    jQuery('#empty-album-text').hide();
                    var imgId = attachment.get( 'id' );
                    var sizes = attachment.get( 'sizes' );
                    var burl  = attachment.get( 'url' );
                    
                   
                    
                    //dotest(imgId);
            
		},
		close: function() { //onclose	
                       VideoScan();
              }
		
	}
        
        jQuery( media.init );
        
	
jQuery("#sortable tr").hover(
  function () {
  jQuery(this).children(".lasttd").children(".hiddenm").addClass("visiblem"); 
  },
  function () {
  jQuery(this).children(".lasttd").children(".hiddenm").removeClass("visiblem").addClass("hiddenm");
  }
);


jQuery(".videoimg").mouseover(
  function () {
    jQuery(this).css('cursor', 'hand');
    jQuery(this).children(".vedit").show(); 
});
  
jQuery(".videoimg").mouseout(function(){
    jQuery(".vedit").hide(); 
});


jQuery(".vedit").click(function(){ 
    
          var id = jQuery(this).parent(".videoimg").attr("id");
          jQuery("#vid").attr("value", id);
    
          jQuery.ajax({
               url: ajaxurl,
              type: "POST",
              data:  {action: 'oQeyScanForImagesForVideo', id: id},
                       cache: false,
           success: function(data){
               
                    jQuery("#video_content").html(data);
                    jQuery("#all_images").dialog({ width: 860, height: 600, resizable: false, autoOpen: true, title: 'Images', modal: true, draggable: false });   
                    
                    jQuery(".videoimage").click(function(){  
                         
                           jQuery('#all_images div').each(function() {
                               
                                  jQuery(this).css("background", "#ccc");
                                  jQuery(this).children().removeClass("selectedimg");
                              });
        
                           jQuery(this).parent().css("background", "#AECFF0");
                           jQuery(this).addClass("selectedimg");
                       }); 
                       },
             error: function() { 

                    jQuery("#save").hide().html('<div class="wrap"><div class="updated fade" id="message"><p><?php _e('There is an error, please try again.', 'oqey-gallery'); ?><\/p><\/div><\/div>').fadeIn('slow');
                    clearUp();
                    
		    }
		});    
});


 jQuery(".videoimage").click(function(){ //scan server for images 

        var id = jQuery(this).parent(".videoimg").attr("id");        
        jQuery("#vid").attr("value", id);
        
        jQuery.ajax({
		  url    : ajaxurl,
		  type   : "POST",
		  data   : {action: 'oQeyScanForImagesForVideo', id: id},
		  cache  : false,
		  success: function(data){
                      
                           jQuery("#video_content").html(data);
                           jQuery("#all_images").dialog({ width: 860, height: 600, resizable: false, autoOpen: true, title: 'Images', modal: true, draggable: false });
            
                           jQuery(".videoimage").click(function(){     
                               
                                  jQuery('#all_images div').each(function(){
                                      
                                         jQuery(this).css("background", "#ccc");
                                         jQuery(this).children().removeClass("selectedimg");
                                  });
        
                           jQuery(this).parent().css("background", "#AECFF0");
                           jQuery(this).addClass("selectedimg");
                       });
                       },
		   error: function(){
                          
                           jQuery("#save").hide().html('<div class="wrap"><div class="updated fade" id="message"><p><?php _e('There is an error, please try again.', 'oqey-gallery'); ?><\/p><\/div><\/div>').fadeIn('slow');
                           clearUp();

                       }
                       
        });
                       
}); 
     
   jQuery(".dblclick").editable( ajaxurl, { 
            indicator  : '<?php _e('Updating...', 'oqey-gallery'); ?>',
            tooltip    : "<?php _e('Double-click to rename...', 'oqey-gallery'); ?>",
            event      : "dblclick",
            style      : "inherit",
	        width  : "470px",
	        height : "20px",
            submitdata : function ( value, settings ) { return { "action": 'oQeyEditVideoTitle' }; }
   });
     
     jQuery(".dblclickdesc").editable( ajaxurl, { 
            indicator  : '<?php _e('Updating...', 'oqey-gallery'); ?>',
            type       : 'textarea',
            tooltip    : "<?php _e('Double-click to rename...', 'oqey-gallery'); ?>",
            placeholder: "<?php _e('Double-click to add description...', 'oqey-gallery'); ?>",
            event      : "dblclick",
            style      : "inherit",
	        width  : "470px",
	        height : "40px",
            submit     : 'Save',
            submitdata : function ( value, settings ) { return { "action": 'oQeyEditVideoDescription' }; }
   });
   
   /*import from others galleries*/
jQuery("#videolink").click(function(){ 

  if(jQuery("#externalvideo").is(":hidden")){
      
            jQuery("#externalvideo").slideDown("slow");
        
        }else{
            
            jQuery("#externalvideo").slideUp(500);  
        
        }
});
/*END import*/   
});
</script>

<div id="all_images" style="display:none; margin:10px;">
    <div style="overflow-y:auto;overflow-x:hidden;height:483px;border:#999 thin solid;padding:5px;text-align:center;" id="video_content"></div>
    <div style="margin:10px;text-align:center;vertical-align: middle;">
        <input type="hidden" name="action" value="update" />
        <input type="button" class="button-primary button" style="width:50px;margin-top:5px;" value="<?php _e('Save', 'oqey-gallery') ?>" onclick="SaveVideoImage(); return false;" />
    </div>
</div>

<br class="clear"/>
<div style="border-width:1px;border-style:solid;line-height:1;-moz-border-radius:6px;-khtml-border-radius:6px;-webkit-border-radius:6px;border-radius:6px;width:880px;padding:10px;border-color:#dfdfdf;text-align:justify;">
  <?php _e('Use this section to add videos to your website. We strongly recommend to first convert your videos to *.mp4 video using this free utility - Miro Video Converter', 'oqey-gallery') ?>  <a href="http://www.mirovideoconverter.com/" target="_blank">http://www.mirovideoconverter.com</a> 
  <?php _e('Converting your video content to mp4 will make it accessible from both flash enabled browsers and html5 devices. It is also recommended to upload video content via ftp when uploading files of a size above your server maximum upload limit.', 'oqey-gallery') ?>
</div>

<div class="qr">
    <img src="<?php echo oQeyPluginUrl(); ?>/images/subscribe.png" width="120" >    
</div>