<?php
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'manageskins.php' == basename($_SERVER['SCRIPT_FILENAME'])) die ('Please do not load this page directly. Thanks!');
	 
include('oqey-functions.php');
global $wpdb, $current_user;
   $oqey_galls = $wpdb->prefix . "oqey_gallery";
   $oqey_images = $wpdb->prefix . "oqey_images";
   $oqey_music = $wpdb->prefix . "oqey_music";
   $oqey_music_rel = $wpdb->prefix . "oqey_music_rel";   
   $oqey_skins = $wpdb->prefix . "oqey_skins";

if(isset($_GET['new_skin'])) {
$up = sprintf("UPDATE $oqey_skins SET status ='%d' WHERE status ='1' ", "0");
$s = mysql_query($up) or die (mysql_error());

$skin = sprintf("UPDATE $oqey_skins SET status ='%d' WHERE id = '%d' ", "1",mysql_real_escape_string($_GET['new_skin'])  );	
$s = mysql_query($skin) or die (mysql_error());

$mesaj = '<p class="updated fade">New skin was set.</p>';
}


if(isset($_GET['scaner'])){

$root = str_replace('\\', '/', ABSPATH)."wp-content/plugins/oqey-gallery/skins/";
$skins = scanSkins($root,"1");
$d=0;

foreach($skins as $skin){

if(!$sql=$wpdb->get_row($wpdb->prepare("SELECT * FROM $oqey_skins WHERE folder = %s", $skin))){
$myFile = str_replace('\\', '/', ABSPATH)."wp-content/plugins/oqey-gallery/skins/".$skin."/details.php";
$fh = fopen($myFile, 'r');
$theData = fread($fh, filesize($myFile));
fclose($fh);

$all = preg_match_all('/\[([^]]+)]/i', $theData, $matches);

$name = str_replace("Name:", "", $matches[1][0]);
$description = str_replace("Description:", "", $matches[1][3]);
$commercial = str_replace("Commercial:", "", $matches[1][4]);
$skinid = str_replace("Skinid:", "", $matches[1][5]);

//print_r($matches);

$in = $wpdb->query("INSERT INTO $oqey_skins (name, description, folder, commercial, skinid) VALUES ('".trim(mysql_real_escape_string(stripslashes($name)))."', '".trim(mysql_real_escape_string(stripslashes($description)))."', '".trim($skin)."', '".trim($commercial)."', '".trim($skinid)."')");
$d++;
}
}

$mesaj = '<p class="updated fade">'.$d.' new skins found.</p>';
}
?>
<script type="text/javascript">
function deleteCat(id){ 
jQuery.post("<?php echo oQeyPluginUrl(); ?>/do.php", { deletecat: id },
   function(id){
            jQuery("#cat_id_s_" + id).fadeOut('slow');
   });
}

function refreshPage(){
	window.location = "<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=oQeyGalleriesSkins&scaner=true";
}

</script>
<div class="wrap" style="width:900px;">
  <h2>Manage Skins</h2>
  
  <div id="save" style="width:900px; margin-bottom:10px; margin-left:-5px;"><?php echo $mesaj; ?></div>
</div>
<div class="postbox" style="height:50px; width:900px;">

<table width="900" border="0" cellspacing="0" cellpadding="5">
  <tr>
    <td width="185" height="50" align="right" valign="middle">Upload a new skin (*.zip)</td>
    <td width="125" height="50" align="right" valign="middle">
    
<div id="flashuploader" style="width:100px; height:30px;"><a href="http://www.macromedia.com/go/getflashplayer" target="_blank" class="style4">GET FLASH </a></div>	   
      <script type="text/javascript">
	var flashvars = {BatchUploadPath:"<?php echo str_replace('\\', '/', ABSPATH); ?>wp-content/plugins/oqey-gallery/skins/",
					 Handler:"<?php echo oQeyPluginUrl(); ?>/btupload.php",
					 FTypes:"*.zip",
					 FDescription:"Media Files"};
	var params = {bgcolor:"#FFFFFF", allowFullScreen:"true", wMode:"transparent"};
	var attributes = {id: "flash"};
	swfobject.embedSWF("<?php echo oQeyPluginUrl(); ?>/demoupload.swf", "flashuploader", "110", "30", "8.0.0", "", flashvars, params, attributes);
    </script>
</td>
    <td width="20" height="50">/</td>
    <td width="570" height="50" align="left"> 
    <div align="left" style="padding-top:3px;">
    <form id="scaner" name="scaner" method="post" action="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=oQeyGalleriesSkins&amp;scaner=true">     
        <input type="submit" name="scan" id="scan" value="refresh" />              
    </form>  
    </div>
    </td>
  </tr>
</table>



</div>

<div class="postbox" style="width:900px;">
<div id="currentskin">
<?php
global $wpdb; 
$r = $wpdb->get_row("SELECT * FROM $oqey_skins WHERE status ='1' LIMIT 0,1");
if($r->commercial=="yes"){ $comm = " - Commercial skin"; }else{ $comm = " - Free skin"; }
echo '
<div id="current-theme">
<h4>Default skin</h4>
<img src="'.oQeyPluginUrl().'/skins/'.$r->folder.'/'.$r->folder.'.jpg" alt="Current theme preview" width="150" height="100" />
<h4>'.$r->name.$comm.'</h4>
<p>'.$r->description.'</p>
<p>Skin files location: <code>/skins/'.$r->folder.'</code>.</p>
</div>';
?>
</div>
<br class="clear" />
</div>

<div class="postbox" style="width:900px;">
<div id="content"><div class="obis">Loading content...</div></div>
</div>

<div class="postbox" style="width:900px;">
<div align="left" style="margin:15px;">
Notes: <br />
         *<?php echo uploadSize(); ?><br />
         *You may upload new skin files directly to your plugin directory via ftp.<br />
         *Your skins folder location:<b><?php echo get_option('siteurl').'/wp-content/plugins/oqey-gallery/skins/'; ?></b>       
    </div>
</div>
<script type="text/javascript">
jQuery(document).ready(function($){
jQuery.loadImages([ '<?php echo oQeyPluginUrl().'/images/preview_button.png'; ?>', '<?php echo oQeyPluginUrl().'/images/remove_button.png'; ?>', '<?php echo oQeyPluginUrl().'/images/edit_button.png'; ?>' ],function(){});

function clearDiv(){ setTimeout(function () {  jQuery('#messages').fadeOut(function(){ jQuery("#messages").html("&nbsp;"); }); }, 3000); }

jQuery.post("<?php echo oQeyPluginUrl(); ?>/do.php", { allskins: "yes" },
   function(data){
            jQuery("#content").hide().html(data).fadeIn('slow');
						
			jQuery(".set_as_default").click(function(){
			var id = jQuery(this).attr("id");
			window.location = "<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=oQeyGalleriesSkins&new_skin=" + id;			
			});
			
			jQuery(".savekey").click(function(){			
			var id = jQuery(this).attr("id");
			var comkey = jQuery("#key" + id).val();
			
			jQuery.post("<?php echo oQeyPluginUrl(); ?>/do.php", { savekey: id, comkey: comkey },
            function(r){
			jQuery("#save").hide().html('<p class="updated fade">' + r + '<\/p>').fadeIn("slow");
			});
			});

			jQuery(".discount_code").click(function(){			
			var d = jQuery(this).val("");			
			});
			
			jQuery(".buy_this_skin").click(function(){			
			
			jQuery(this).parent("form").submit();
			
			});
			

			jQuery(".delete_this_skin").click(function(){
			var id = jQuery(this).attr("id");
			
			jQuery.post("<?php echo oQeyPluginUrl(); ?>/do.php", { movetotrashskin: id },
            function(data){ 
			jQuery('#skink_tr_'+id).fadeOut('slow');
	        jQuery("#save").hide().html('<p class="updated fade">Skin was moved to trash. <a href="#undo" id="undoskin">undo<\/a><\/p>').fadeIn('slow');
			
			jQuery("#undoskin").click(function(){
			jQuery.post("<?php echo oQeyPluginUrl(); ?>/do.php", { undoskin: id },
            function(r){
			jQuery("#save").hide().html('<p class="updated fade">' + r + '<\/p>').fadeIn("slow");
			jQuery('#skink_tr_'+id).fadeIn('slow');
			});			
			});
			
			});
		});
   });
});
</script>