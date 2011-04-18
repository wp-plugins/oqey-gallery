<?php
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'managemusic.php' == basename($_SERVER['SCRIPT_FILENAME'])) die ('Please do not load this page directly. Thanks!');
global $wpdb;
$oqey_music = $wpdb->prefix . "oqey_music";

if(isset($_GET['scaner'])){
    
	$root = OQEY_ABSPATH."wp-content/oqey_gallery/music";

if(is_dir($root)){

$files = oqey_php4_scandir($root);
//$files = get_mp3_files($root);
	
global $wpdb;
$oqey_music = $wpdb->prefix . "oqey_music";
$root = $root."/";
$d=0;

foreach($files as $file){

$path = pathinfo($file);

if(!isset($path['filename'])){
    $audio_title = trim(sanitize_title(substr($path['basename'], 0,strpos($path['basename'],'.')))).".".$path['extension'];
	$music_title = substr($path['basename'], 0,strpos($path['basename'],'.'));
}else{
    $audio_title = trim(sanitize_title($path['filename']).".".$path['extension']);//denumirea imaginii
	$music_title = $path['filename'];
}

$ext = array('mp3', 'MP3'); 
if (in_array( strtolower($path['extension']), $ext) ){	

if(!$sql=$wpdb->get_row($wpdb->prepare("SELECT * FROM $oqey_music WHERE link = %s", $audio_title))){
$wpdb->query("INSERT INTO $oqey_music (link, title) VALUES ('$audio_title', '$music_title')");
rename($root.$file, $root.$audio_title);
$d++;
}
}

}
$mesaj = '<p class="updated fade">'.$d.' new audio files found.</p>';
}else{
$mesaj = '<p class="error">Error creating the default gallery music folder (might be a server restrictions issue). Please create this folder manually (wp-content/oqey_gallery/music) and set permissions to 777.</p>';
}
}
?>    
<script type="text/javascript">
function clearUp(){ setTimeout(function () { jQuery('#save').fadeOut(function(){ jQuery("#save").html("&nbsp;"); }); }, 2000); }

function deleteSong(id){ 
jQuery.post("<?php echo oQeyPluginUrl(); ?>/do.php", { songtotrash: id },
   function(id){
            jQuery("#row_" + id).fadeOut('slow');
			jQuery("#save").hide().html('<p class="updated fade">Song was moved to trash. <a href="#undo" id="restoresong">undo<\/a><\/p>').fadeIn("slow"); 
			
			jQuery("#restoresong").click(function(){
		    jQuery.post("<?php echo oQeyPluginUrl(); ?>/do.php", { restoresong: id },
            function(data){			
			jQuery("#row_" + id).fadeIn('slow');			
			jQuery("#save").hide().html('<p class="updated fade">Song was restored.<\/p>').fadeIn("slow"); 
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
								
<?php if(get_option("oqey_license")==""){ ?>  	  
	  jQuery("#oqey_licence").dialog({ width: 896, height: 615, resizable: false, autoOpen: true, title: 'oQey Gallery License Agreement', modal: true, draggable: false });
<?php } ?>	
								
jQuery.loadImages([ '<?php echo oQeyPluginUrl().'/images/preview_button.png'; ?>', '<?php echo oQeyPluginUrl().'/images/remove_button.png'; ?>', '<?php echo oQeyPluginUrl().'/images/edit_button.png'; ?>' ],function(){});

hoverSongs();
jQuery(".click").editable("<?php echo oQeyPluginUrl(); ?>/do.php", { 
	  name: "music_edit_title",
	  id: "music_edit_id",
      indicator : 'Updating...',
      tooltip   : "Double-click to rename...",
      event     : "dblclick",
      style  : "inherit",
	  width : "80%",
	  height : "15px"
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
	function getFlashMovie(movieName) {
		var isIE = navigator.appName.indexOf("Microsoft") != -1;   return (isIE) ? window[movieName] : document[movieName];
	}
</script> 
<div class="wrap">
        <h2>Manage Music</h2>
<?php 
  $gal_dir_musica = OQEY_ABSPATH."wp-content/oqey_gallery/music";   
  if(!is_dir($gal_dir_musica)){
  $eroarea = "Error creating the default gallery music folder (might be a server restrictions issue). Please create this folder manually (wp-content/oqey_gallery/music) and set permissions to 777.";
  echo '<div class="wrap"><div class="error" id="error"><p>' . $eroarea . '</p></div></div>';
  }
  ?>

<div id="save" style="width:800px; margin-bottom:10px; margin-left:-5px;"><?php echo $mesaj; ?></div>
</div> 

<div class="postbox" style="height:50px; width:800px;">
<?php if(is_dir($gal_dir_musica)){ ?>
<table width="800" border="0" cellspacing="0" cellpadding="5">
  <tr>
    <td width="225" height="50" align="right" valign="middle">Upload a new audio file (*.mp3)</td>
    <td width="125" height="50" align="right" valign="middle">
    
<div id="flashuploader" style="width:100px; height:30px;">GET FLASH</div>	
<?php
if ( is_ssl() ){ $cookies = $_COOKIE[SECURE_AUTH_COOKIE]; }else{ $cookies = $_COOKIE[AUTH_COOKIE]; }
$datele = '7--'.$cookies.'--'.$_COOKIE[LOGGED_IN_COOKIE].'--'.wp_create_nonce('oqey-music');
?>
<script type="text/javascript">	
	var flashvars = {BatchUploadPath:"<?php echo base64_encode($datele); ?>",
					 Handler:"<?php echo oQeyPluginUrl(); ?>/bmupload.php",
					 FTypes:"*.mp3",
					 FDescription:"Media Files"};
	var params = {bgcolor:"#FFFFFF", allowFullScreen:"true", wMode:"transparent" }
	var attributes = {id: "flash"};
	swfobject.embedSWF("<?php echo oQeyPluginUrl(); ?>/demoupload.swf", "flashuploader", "110", "30", "8.0.0", "", flashvars, params, attributes);
</script>
</td>
    <td width="20" height="50">/</td>
    <td width="430" height="50" align="left"> 
    <div align="left" style="padding-top:3px;">
    <form id="scaner" name="scaner" method="post" action="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=oQeyMusic&amp;scaner=true">     
        <input type="submit" name="scanfolder" id="scanfolder" value="refresh" />              
    </form>
    </div>
    </td>
  </tr>
</table>
<?php }else{  echo '<div align="center" style="margin:15px;">Please create the gallery music folder first. After that, you will be able to upload your music.</div>'; } ?>
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
	if($i%2){ $tr_bg = "background-color:#CECECE;"; }else{ $tr_bg = "background-color:#E4E4E4;"; }
?>
  <tr style="padding:3px;" id="row_<?php echo $music->id; ?>">
    <td align="center" width="50" style="height:35px;">
    <div id="flashm<?php echo $i; ?>">
    <script type="text/javascript">
	var flashvars = {mpath:"<?php echo get_option('siteurl').'/wp-content/oqey_gallery/music/'.oqey_getBlogFolder($wpdb->blogid).$music->link;  ?>", flashId:"<?php echo $i; ?>", totalFiles:"<?php echo $totalFiles; ?>"};
	var params = {wMode:"transparent"};
	var attributes = {id: "playbtn<?php echo $i; ?>"};
	swfobject.embedSWF("<?php echo oQeyPluginUrl(); ?>/musiccs.swf", "flashm<?php echo $i; ?>", "30", "30", "8.0.0", "", flashvars, params, attributes);
    </script>
    </div>   
    </td>
    <td align="left" width="725" >
    <div class="click" id="select_<?php echo $music->id; ?>"><?php echo $music->title; ?></div>    
    </td>
    <td width="30" align="center" valign="middle" class="lasttd"> 
    <a href="#null" onClick="deleteSong('<?php echo $music->id; ?>'); return false;" class="hiddenm">
    <img src="<?php echo oQeyPluginUrl(); ?>/images/remove_button.png" width="24" height="24" title="Click to move to trash this song"/></a>
    </td>
  </tr>
<?php 
$i++;
}
}else{ ?>
    <tr style="padding:3px;">
    <td align="center" width="50" style="height:35px;">       
    </td>
    <td width="725" align="left" valign="middle" >You have no music yet. Please upload some files.</td>
    <td width="30" align="center" valign="middle" class="lasttd">    
    </td>
  </tr>    

    <?php } ?>
  </tbody>
</table>
</div>
<div class="postbox" style="width:800px;">
<div align="left" style="margin:15px;">
Notes: <br />
         * <?php echo oqey_uploadSize(); ?><br />
         * You may upload new audio files directly to your plugin audio directory via ftp.<br />
         * Your audio folder location:<b> <?php echo get_option('siteurl').'/wp-content/oqey_gallery/music/'.oqey_getBlogFolder($wpdb->blogid); ?></b>       
    </div>
</div>
<script type="text/javascript">
//uploader use this function
function refreshPage(){ window.location = "<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=oQeyMusic"; }
</script>

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