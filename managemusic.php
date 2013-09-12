<?php
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'managemusic.php' == basename($_SERVER['SCRIPT_FILENAME'])) die ('Please do not load this page directly. Thanks!');
global $wpdb;
$oqey_music = $wpdb->prefix . "oqey_music";

if(isset($_GET['scaner'])){
 
  $ext        = array('mp3');
  $root       = rtrim(OQEY_ABSPATH, '/');
  $files      = oqey_get_all_files($root, $ext );
  $oqey_music = $wpdb->prefix . "oqey_music";
  $root       = $root."/";
  $d          = 0;
  $site_url   = get_option('siteurl').'/';

  foreach($files as $file){
    
    $path        = pathinfo($file);   
    $pre_link    = str_replace(OQEY_ABSPATH, $site_url, $path['dirname']);    
    $audio_file  = trim(sanitize_title($path['filename']).".".$path['extension']);//new song title
    $audio_link  = $pre_link."/".$audio_file;
    $audio_path  = $path['dirname']."/".$audio_file;
    $music_title = $path['filename'];    
    $ext         = array('mp3', 'MP3'); 
    
    if ( in_array( strtolower($path['extension']), $ext) ){	
  
       if(!$sql=$wpdb->get_row( $wpdb->prepare("SELECT * FROM $oqey_music WHERE link = %s ", $audio_link) ) ){
      
         $wpdb->query( $wpdb->prepare( "INSERT INTO $oqey_music (link, path, title, type) 
                                                         VALUES ( %s, %s, %s, %s)", 
                                                                 $audio_link,
                                                                 $audio_path,
                                                                 $music_title,
                                                                 "other"
                                                         )
                                                         );
         rename($file, $audio_path);
         $d++;
    
       }
    
    }

  }

    $mesaj = '<div class="wrap">
                 <div class="updated below-h2" id="message">
                   <p>'.$d.'&nbsp;'.__('new audio files found.', 'oqey-gallery').'</p>
                 </div>
               </div>';
  
}
?>
<script type="text/javascript">
function clearUp(){ 
    setTimeout(function (){ 
        jQuery('#save').fadeOut(function(){ 
            jQuery("#save").html("&nbsp;");
        }); 
    }, 3000); 
}

function deleteSong(id){ 
jQuery.post(ajaxurl, { action:"oQeySongToTrash", songtotrash: id },
     function(id){
         jQuery("#row_" + id).fadeOut('slow');
         jQuery("#save").hide().html('<div class="wrap"><div class="updated below-h2" id="message"><p><?php _e('Song was moved to trash', 'oqey-gallery'); ?> <a href="#undo" id="restoresong">undo<\/a><\/p><\/div><\/div>').fadeIn('slow');
   
         jQuery("#restoresong").click(function(){
             jQuery.post(ajaxurl, { action:"oQeySongFromTrash", restoresong: id },
                        function(data){			
			   jQuery("#row_" + id).fadeIn('slow');			
			   jQuery("#save").hide().html('<div class="wrap"><div class="updated below-h2" id="message"><p><?php _e('Song was restored', 'oqey-gallery'); ?><\/p><\/div><\/div>').fadeIn('slow');
			
			});
            clearUp();
            });
   });
}

function hoverSongs(){
jQuery("#sortable tr").hover(
  function () {
  jQuery(this).children(".lasttd").children(".hiddenm").addClass("visiblem"); 
  },
  function () {
  jQuery(this).children(".lasttd").children(".hiddenm").removeClass("visiblem").addClass("hiddenm");
  }
);
}

jQuery(document).ready(function($) {
								
  jQuery.loadImages([ '<?php echo oQeyPluginUrl().'/images/preview_button.png'; ?>', 
                      '<?php echo oQeyPluginUrl().'/images/remove_button.png'; ?>', 
                      '<?php echo oQeyPluginUrl().'/images/edit_button.png'; ?>' ],function(){});

hoverSongs();
jQuery(".click").editable(ajaxurl, { 
	       name: "music_edit_title",
	         id: "music_edit_id",
          indicator: '<?php _e('Updating...', 'oqey-gallery'); ?>',
            tooltip: "<?php _e('Double-click to rename...', 'oqey-gallery'); ?>",
              event: "dblclick",
              style: "inherit",
	      width: "80%",
	     height: "15px",
         submitdata: function ( value, settings ) { return { "action": 'oQeySongRename' }; }
      
  });

});
	var name=1;
	var firstplay = true;
	function checkActivePlayer(newname) {
		if (!firstplay) {
        	getFlashMovie("playbtn" + name).sendIDToFlash(newname);
			name = newname;
		} else {
			name = newname;
			firstplay = false;
		}
	}
	function getFlashMovie(movieName){ 
            var isIE = navigator.appName.indexOf("Microsoft") != -1; 
            return (isIE) ? window[movieName] : document[movieName]; 
        }
</script> 

<div class="wrap">
    <h2 style="width: 830px;"><?php _e('Manage Music', 'oqey-gallery'); ?></h2>
    
<?php 
  $gal_dir_musica = OQEY_ABSPATH."wp-content/oqey_gallery/music";   
  if(!is_dir($gal_dir_musica)){
    
    $eroarea = __('Error creating the default gallery music folder (might be a server restrictions issue). Please create this folder manually (wp-content/oqey_gallery/music) and set permissions to 777.', 'oqey-gallery');
    echo '<div class="wrap"><div class="error" id="error"><p>' . $eroarea . '</p></div></div>';
  
  }
?>

<div id="save" style="width:817px; margin-bottom:10px;">
    <?php if(!empty($mesaj)){ echo $mesaj; } ?>
</div>

</div> 

<div class="postbox" style="height:50px; width:800px;">
<?php if(is_dir($gal_dir_musica)){ ?>
<table width="800" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="200" height="50" align="right"><?php _e('Upload a new audio file', 'oqey-gallery'); ?> (*.mp3)</td>
    <td width="125" height="50" align="right">
        
        <div class="uploader" id="flashuploader">
            <input type="button" name="uploadimages" id="uploadimages" value="<?php echo __('Upload', 'oqey-gallery'); ?>" class="button" style="float:left;"/>
            <img src="<?php echo oQeyPluginUrl(). '/images/indicator.gif'; ?>" id="inicon"/>     
        </div>	
<?php
if ( is_ssl() ){ $cookies = $_COOKIE[SECURE_AUTH_COOKIE]; }else{ $cookies = $_COOKIE[AUTH_COOKIE]; }
$datele = '7--'.$cookies.'--'.$_COOKIE[LOGGED_IN_COOKIE].'--'.wp_create_nonce('oqey-music');
?>

<script type="text/javascript">
var uploader = new plupload.Uploader({
runtimes : "gears,html5,flash,silverlight,browserplus",
browse_button : "uploadimages",
container: "flashuploader",
max_file_size : "<?php echo oQeyGetNumericUploadSize(); ?>",
url : "<?php echo oQeyPluginUrl().'/bmupload.php';?>",
flash_swf_url : "<?php echo oQeyPluginUrl().'/js/plupload.flash.swf';?>",
silverlight_xap_url : "<?php echo oQeyPluginUrl().'/js/plupload.silverlight.xap';?>",
filters : [{title : "Video files", extensions : "mp3,MP3"}],
multipart_params : {
        "details" : "<?=base64_encode($datele)?>"
    }
});
uploader.init();
uploader.bind("FilesAdded", function(up, files) {
uploader.start();
return false;
});
uploader.bind("UploadProgress", function(up, file) {
document.getElementById("inicon").style.display="block";
})
uploader.bind("UploadComplete", function(up) {
refreshPage();
document.getElementById("inicon").style.display="none";
});
</script>

</td>
    <td width="20" height="50">/</td>
    <td width="455" height="50" align="left"> 
    <div align="left" style="padding-top:3px;">
    <form id="scaner" name="scaner" method="post" action="<?php echo admin_url('admin.php?page=oQeyMusic&scaner=true'); ?>">     
        <input class="button" type="submit" name="scanfolder" id="scanfolder" value="<?php _e('Magic scan', 'oqey-gallery'); ?>" />              
    </form>
    </div>
    </td>
  </tr>
</table>
<?php }else{  
    
         echo '<div align="center" style="margin:15px;">';
         _e('Please create the gallery music folder first. After that, you will be able to upload your music.', 'oqey-gallery');
         echo '</div>';
         
       } ?>
</div>

<div class="postbox" style="width:800px;">
<table width="800" border="0" cellspacing="0" cellpadding="3" id="musictable" class="tablesorter">
<tbody id="sortable">
<?php
   global $wpdb;
   $oqey_music = $wpdb->prefix . "oqey_music";
   $i=1;

     $get_music = $wpdb->get_results("SELECT * FROM $oqey_music WHERE status !=2");
	  
     $totalFiles = count($get_music);
	 
     if($get_music){
        
        foreach ($get_music as $music){
?>
  <tr style="padding:3px;" id="row_<?=$music->id?>">
    <td align="center" width="50" style="height:35px;">
    <div id="flashm<?php echo $i; ?>">
    <script type="text/javascript">
	var flashvars  = {mpath:"<?=trim($music->link)?>", flashId:"<?=$i?>", totalFiles:"<?=$totalFiles?>"};
	var params     = {wMode:"transparent"};
	var attributes = {id: "playbtn<?php echo $i; ?>"};
	swfobject.embedSWF("<?php echo oQeyPluginUrl(); ?>/musiccs.swf", "flashm<?php echo $i; ?>", "30", "30", "8.0.0", "", flashvars, params, attributes);
    </script>
    </div>   
    </td>
    <td align="left" width="725" >
    <div class="click" id="select_<?php echo $music->id; ?>"><?php echo $music->title; ?></div>    
    </td>
    <td width="30" align="center" valign="middle" class="lasttd"> 
    <a href="#null" onclick="deleteSong('<?=$music->id?>'); return false;" class="hiddenm">
    <img src="<?php echo oQeyPluginUrl(); ?>/images/remove_button.png" width="24" height="24" title="<?php _e('Click to move to trash this song', 'oqey-gallery'); ?>"/></a>
    </td>
  </tr>
<?php 
$i++;
}
}else{ ?>
    <tr style="padding:3px;">
    <td align="center" width="50" style="height:35px;">       
    </td>
    <td width="725" align="left" valign="middle" ><?php _e('You have no music yet. Please upload some files.', 'oqey-gallery'); ?></td>
    <td width="30" align="center" valign="middle" class="lasttd">    
    </td>
  </tr>    

    <?php } ?>
  </tbody>
</table>
</div>
<div class="postbox" style="width:800px;">
<div align="left" style="margin:15px;">
<?php _e('Notes', 'oqey-gallery'); ?>: <br />
         * <?php echo oqey_uploadSize(); ?><br />
         * <?php _e('You may upload new audio files directly to your plugin audio directory via ftp.', 'oqey-gallery'); ?><br />
         * <?php _e('Your audio folder location:', 'oqey-gallery'); ?><b> <?php echo get_option('siteurl').'/wp-content/oqey_gallery/music/'.oqey_getBlogFolder($wpdb->blogid); ?></b>       
    </div>
</div>
<script type="text/javascript">function refreshPage(){ window.location = "<?php echo admin_url('admin.php?page=oQeyMusic'); ?>"; }</script>