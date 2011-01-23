<div class="wrap">
        <h2> Trash</h2>
<?php
include('oqey-functions.php');
global $wpdb;
   $oqey_galls = $wpdb->prefix . "oqey_gallery";
   $oqey_images = $wpdb->prefix . "oqey_images";
   $oqey_music = $wpdb->prefix . "oqey_music";
   $oqey_music_rel = $wpdb->prefix . "oqey_music_rel";
   $oqey_skins = $wpdb->prefix . "oqey_skins";

$gal_dir_musica = str_replace('\\', '/', ABSPATH)."/wp-content/oqey_gallery/music";  
$oqeyImagesRoot = get_option('siteurl')."/wp-content/oqey_gallery/galleries/";

  if(!is_dir($gal_dir_musica)){
  $eroarea = "Error creating the default gallery music folder (might be a server restrictions issue). Please create this folder manually (wp-content/oqey_gallery/music) and set permissions to 777.";
  echo '<div class="wrap"><div class="error" id="error"><p>'.$eroarea.'</p></div></div>';
  }

$get_music = $wpdb->get_results("SELECT * FROM $oqey_music WHERE status=2");
$get_skins = $wpdb->get_results("SELECT * FROM $oqey_skins WHERE status = '2' ORDER BY id DESC");
$get_g_list = $wpdb->get_results("SELECT * FROM $oqey_galls WHERE status=2 ORDER BY gall_order ASC, id DESC");
$galls = $wpdb->get_results("SELECT COUNT($oqey_images.id) as numartotal, $oqey_images.gal_id,  $oqey_galls.title, $oqey_galls.folder
                             FROM $oqey_images, $oqey_galls
						      WHERE $oqey_images.status=2 AND $oqey_images.gal_id = $oqey_galls.id
							  GROUP BY $oqey_images.gal_id");
?>
<div id="save" style="width:940px; margin-bottom:10px; margin-left:-5px;"><?php echo $mesaj; ?></div>
</div> 
<div id="tabs" style="display:none;">
	<ul id="taburile">
		<?php if(!empty($get_music)){ ?><li><a href="#tabs-1">Music</a></li><?php } ?>
        <?php if(!empty($get_skins)){ ?><li><a href="#tabs-2">Skins</a></li><?php } ?>
        <?php if(!empty($get_g_list)){ ?><li><a href="#tabs-3">Galleries</a></li><?php } ?>
        <?php if(!empty($galls)){ ?><li><a href="#tabs-4">Images</a></li><?php } ?>
	</ul>
    
<?php if(!empty($get_music)){ ?>    
<div id="tabs-1">
<div class="postbox" style="width:900px;" id="tabs-11">
<table width="900" border="0" cellspacing="0" cellpadding="3" id="musictable" class="tablesorter">
<tbody id="sortable">
<?php
	$i=1;
	$message = "Are you sure you want to delete this song?";	
	$totalFiles = count($get_music);
	if($totalFiles>0){	
	foreach ($get_music as $music){
?>
  <tr style="padding:3px;" id="row_<?php echo $music->id; ?>">
    <td align="center" width="46" style="height:35px;">
    <div id="flashm<?php echo $i; ?>">
    <script type="text/javascript">
	var flashvars = {mpath:"<?php echo get_option('siteurl').'/wp-content/oqey_gallery/music/'.$music->link;  ?>", flashId:"<?php echo $i; ?>", totalFiles:"<?php echo $totalFiles; ?>"};
	var params = {wMode:"transparent"};
	var attributes = {id: "playbtn<?php echo $i; ?>"};
	swfobject.embedSWF("<?php echo oQeyPluginUrl(); ?>/musiccs.swf", "flashm<?php echo $i; ?>", "30", "30", "8.0.0", "", flashvars, params, attributes);
    </script>
    </div>    
    </td>
    <td align="left" width="800">
    <div class="click" id="select_<?php echo $music->id; ?>"><?php echo $music->title; ?></div>    </td>
    <td width="27" align="right" valign="middle" class="lasttd">     
    <a href="#null" onClick="restoreSong('<?php echo $music->id; ?>'); return false;" class="hiddenm"><img src="<?php echo oQeyPluginUrl(); ?>/images/restore_button.png" width="24" height="24" title="Click to restore this song"/></a>
    </td>
    <td width="27" align="left" valign="middle" class="lasttd">
    <a href="#null" onclick="deleteSong('<?php echo $music->id; ?>', '<?php echo $music->link; ?>'); return false;" class="hiddenm"><img src="<?php echo oQeyPluginUrl(); ?>/images/remove_button.png" width="24" height="24" title="Click to delete permanently this song "/></a>
    </td>
  </tr>
<?php 
$i++;
} }else{ ?>
  <tr style="padding:3px;" id="row_<?php echo $music->id; ?>">
    <td colspan="4" align="center" style="height:35px;">There is no audio files for recovery.</td>
    </tr>
<?php } ?>
  </tbody>
</table>
</div>
</div>
<?php } ?>

<?php if(!empty($get_skins)){ ?>
<div id="tabs-2">
<div class="postbox" style="width:900px;" id="tabs-22">
<table width="900" border="0" cellspacing="0" cellpadding="15" class="tablesorter">
<tbody>
<?php if(!empty($get_skins)){ foreach ($get_skins as $r){ ?>
<tr id="skin_tr_<?php echo $r->id; ?>">
             <td width="170" height="120" align="center" valign="middle">
			 <img src="<?php echo oQeyPluginUrl().'/skins/'.$r->folder.'/'.$r->folder; ?>.jpg" alt="skin" width="150" height="100" style="border:#999999 solid thin; margin-left:15px;"/>
			 </td>
             <td width="630" align="left" valign="top" style="margin-left:10px; padding:5px;">
			 <h4><?php echo $r->name; ?></h4>
             <p><?php echo $r->description; ?><br/>
             Skin files location: <code>/skins/<?php echo $r->folder; ?></code>.</p>
			 <p><a href="#restore" class="restore" onclick="restoreSkin('<?php echo $r->id; ?>'); return false;">Restore</a> | <a href="#delete_this_skin" class="delete_this_skin" onclick="deleteSkinP('<?php echo $r->id; ?>', '<?php echo $r->folder; ?>'); return false;">Delete Permanently</a></p>
			 </td>
             </tr>
<?php } } ?>
</tbody>
</table>
</div>
</div>
<?php } ?>

<?php if(!empty($get_g_list)){?>
<div id="tabs-3">
<?php 
$r = "";
$r .= '<div class="postbox" style="width:900px;" id="tabs-31">
       <table width="900" border="0" cellspacing="0" cellpadding="3" id="gallerytable" class="tablesorter">
       <tbody id="sortable">';
if(!empty($get_g_list)){
	foreach ($get_g_list as $list){   
	 $r .=  '<tr id="row_'.$list->id.'">
             <td width="26" height="35" align="center" valign="middle">&nbsp;</td>
    <td width="820" height="35" align="left" valign="middle"><div id="gall_id_'.$list->id.'">'.$list->title.'</div></td>
    <td width="27" height="35" align="center" valign="middle" class="lasttd">
			 <a href="#restore" onClick="restoreGallery(\''.$list->id.'\'); return false;" class="hiddenm">
      <img src="'.oQeyPluginUrl().'/images/restore_button.png" width="24" height="24" title="Click to restore this gallery" /></a></td>
    <td width="27" align="center" valign="middle" class="lasttd"><a href="#delete" onClick="deleteGallery(\''.$list->id.'\', \''.$list->folder.'\'); return false;" class="hiddenm">
	<img src="'.oQeyPluginUrl().'/images/remove_button.png" width="24" height="24" title="Click to delete permanently this gallery" /></a></td>
  </tr>';
	}
}else{ 

$r .= '<tr id="row_">
       <td align="center" height="35" valign="middle" colspan="3">There is no galleries for recovery.</td>
       </tr>';
}

$r .= '</tbody>
       </table>
	   </div>';
echo $r;
?>

</div><?php } ?>


<?php if(!empty($galls)){ ?>
<div id="tabs-4">
<?php 
$r = "";
$r .= '<table border="0" cellspacing="0" cellpadding="3">
       <tbody>';

foreach($galls as $gal){
	$imgs = $wpdb->get_results("SELECT id, title FROM $oqey_images WHERE status=2 AND gal_id = '".$gal->gal_id."' ORDER BY img_order ASC, id DESC");
    $r .= '<tr id="row_'.$gal->gal_id.'"><td>';
	$r .= '<div class="trashdiv" id="'.$gal->gal_id.'"><a href="#expand" class="expand_images">'.$gal->title.'</a><br>';
	$r .= '<table width="800" border="0" cellspacing="0" cellpadding="0" class="tabledo">
  <tr>
    <td width="200" height="50" align="left" valign="middle">
    <div class="dodiv">
    <select name="dolist" class="dolist">
      <option value="0" selected="selected">Bulk Actions</option>
      <option value="2">Restore</option>
	  <option value="3">Delete</option>
    </select>
	<input type="button" name="doapply" class="doapply" value="Apply"/>
	<div></td>
    <td width="100" align="left" valign="middle"><input name="selectall" type="checkbox" class="selectall">&nbsp;<span class="seelectmessage">select all</span></td>
    <td width="396" align="left" valign="middle"><div class="messages">&nbsp;</div></td>
    <td width="104" align="left" valign="middle">&nbsp;</td>
  </tr>
</table>';

	$r .= '<ul class="sortablegalls">';

foreach($imgs as $img){
$folder = $gal->folder."/galthmb/";
$imgFolder = $gal->folder."/galimg/";

if($s->splash_img==$img->id){ $b=' style="border:#7A82DE thin solid;" class="imgsel"'; }
$r .= '<li id="img_li_'.$img->id.'"'.$b.'><div class="allbut" align="center">
		               <input name="'.trim($img->title).'" type="checkbox" value="'.$img->id.'" class="styled" id="selected_'.$img->id.'">
		               </div>
		               <img src="'.get_option('siteurl').'/wp-content/oqey_gallery/images/separator_line.png" width="120" height="2" align="middle"/>
		               <img src="'.$oqeyImagesRoot.$folder.trim($img->title).'" alt="image_'.$img->id.'" class="trashimages"/>
                       </li>';
}
$r .= '</ul>';
$r .= '</div> </td><tr>';
}
$r .= '</tbody></table>';
echo $r;
?>
</div>
<?php } ?>
</div>
<script type="text/javascript">
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

jQuery(function() {
	
			jQuery(".trashdiv .expand_images").click( function(){ 
			if(jQuery(this).parent().children("ul").is(":visible")){			
			jQuery(this).parent().children("ul").fadeOut("fast");
			jQuery(this).parent().children(".tabledo").fadeOut("fast");
			
			jQuery(".ui-tabs").css("width", "945px");
			}else{
			jQuery(".ui-tabs").css("width", "99%");
			jQuery(this).parent().children(".tabledo").fadeIn("slow");
			jQuery(this).parent().children("ul").fadeIn("slow");
			}
			});
			
			jQuery(".selectall").click(function(){ 
			var x = jQuery(this).parents(".trashdiv");
			var f = x.find('.styled');
			var l = x.find('.sortablegalls li'); 
            if (jQuery(this).attr('checked')){
			
			f.attr('checked', true);
            jQuery(this).parent().children('.seelectmessage').text('clear all');
            l.css('background-color', '#C0CFEF'); 
            }else{ 
            f.attr('checked', false);
            jQuery(this).parent().children('.seelectmessage').text('select all');
            l.css('background-color', '#F8F8F8'); 
            }
			});//end of click 
			
jQuery(".styled").click(function(){ 	
if (jQuery(this).attr('checked')){ jQuery(this).parent().parent().css('background-color', '#C0CFEF'); }else{ jQuery(this).parent().parent().css('background-color', '#F8F8F8'); }
});


jQuery(".doapply").click(function(){ 

var dd = jQuery(this).parent();
var id = dd.find('.dolist').val();

		var x = jQuery(this).parents(".trashdiv");
		var galid = x.attr("id");
		var f = x.find('.styled:checked');
		var l = x.find('.sortablegalls li'); 
		
       

if(id==2){

        var names = [];
        f.each(function() {
		if ( jQuery(this).parent().parent().is(':visible')){
            names.push(this.value);			
		}
			jQuery(this).parent().parent().hide("slow");	
        });

		jQuery.post("<?php echo oQeyPluginUrl(); ?>/do.php", { imgallfromtrash: names, galid:galid },
        function(data){
		if(names.length>0){
			x.find(".messages").hide().html('<p class="updated fade">All selected images was restored.<\/p>').fadeIn("slow");
			var data = eval('(' + data + ')');	
			//alert(data.statusul);
			setTimeout(function () { if(data.statusul=="no"){ jQuery("#row_"+galid).fadeOut(); } }, 1000);
		}else{
		x.find(".messages").hide().html('<p class="updated fade">Please select an image<\/p>').fadeIn("slow"); 
		clearUp(); 
		}
        });
}

if(id==3){

        var names = [];
        f.each(function() {
		if ( jQuery(this).parent().parent().is(':visible')){
            names.push(this.name);			
		}
			jQuery(this).parent().parent().hide("slow");	
        });

		jQuery.post("<?php echo oQeyPluginUrl(); ?>/do.php", { imgalldelete: names, galid: galid },
        function(data){
	
		if(names.length>0){
			
			x.find(".messages").hide().html('<p class="updated fade">All selected images was deleted permanently.<\/p>').fadeIn("slow");
			setTimeout(function () { if(data=="no"){ jQuery("#row_"+galid).fadeOut(); } }, 1000);

		}else{
		x.find(".messages").hide().html('<p class="updated fade">Please select an image<\/p>').fadeIn("slow"); 
		clearUp(); 
		}
        });
}

});//end doapply
});
function clearUp(){ setTimeout(function () {  jQuery('#save').fadeOut(function(){ jQuery("#save").html("&nbsp;"); }); }, 2000); }

function deleteSong(id, name){ 
jQuery.post("<?php echo oQeyPluginUrl(); ?>/do.php", { deletesong: id, name: name },
   function(data){
            jQuery("#row_" + id).fadeOut('slow');
			if(data=="no"){ jQuery("#tabs-11").fadeOut(); }
			jQuery("#save").hide().html('<p class="updated fade">Song was deleted.<\/p>').fadeIn("slow"); 
   });
}

function restoreSong(id){ 
jQuery.post("<?php echo oQeyPluginUrl(); ?>/do.php", { restoresong: id },
   function(data){
            jQuery("#row_" + id).fadeOut('slow');
			if(data=="no"){ jQuery("#tabs-11").fadeOut(); }
			jQuery("#save").hide().html('<p class="updated fade">Song was restored.<\/p>').fadeIn("slow"); 
   });
}

function restoreSkin(id){ 
jQuery.post("<?php echo oQeyPluginUrl(); ?>/do.php", { undoskin: id },
   function(data){
            jQuery("#skin_tr_" + id).fadeOut('slow');
			if(data=="no"){ jQuery("#tabs-22").fadeOut(); }
			jQuery("#save").hide().html('<p class="updated fade">Skin was restored.<\/p>').fadeIn("slow"); 
   });
}

function deleteSkinP(id, name){ 
jQuery.post("<?php echo oQeyPluginUrl(); ?>/do.php", { deleteskinper: id, name: name },
   function(data){
            jQuery("#skin_tr_" + id).fadeOut('slow');
			if(data=="no"){ jQuery("#tabs-22").fadeOut(); }
			jQuery("#save").hide().html('<p class="updated fade">Skin was deleted.<\/p>').fadeIn("slow"); 
   });
}

function restoreGallery(id){ 
jQuery.post("<?php echo oQeyPluginUrl(); ?>/do.php", { undogallid: id },
   function(data){
	        var data = eval('(' + data + ')');		
            jQuery("#row_" + id).fadeOut('slow');
			if(data.statusul=="no"){ jQuery("#tabs-31").fadeOut(); }
			jQuery("#save").hide().html('<p class="updated fade">Gallery was restored.<\/p>').fadeIn("slow"); 
			
   });
}

function deleteGallery(id, name){ 
jQuery.post("<?php echo oQeyPluginUrl(); ?>/do.php", { deletegall: id, name: name },
   function(data){
	        jQuery("#row_" + id).fadeOut('slow');
			if(data=="no"){ jQuery("#tabs-31").fadeOut(); }
			jQuery("#save").hide().html('<p class="updated fade">Deleted.<\/p>').fadeIn("slow"); 
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

var x = jQuery('ul#taburile li').size();
if(x>0){
jQuery('#tabs').tabs();
}else{
jQuery("#taburile").html("<li>There is nothing in the trash.<\/li>");	
}


jQuery("#tabs").fadeIn("slow");

jQuery.loadImages([ '<?php echo oQeyPluginUrl().'/images/preview_button.png'; ?>', '<?php echo oQeyPluginUrl().'/images/remove_button.png'; ?>', '<?php echo oQeyPluginUrl().'/images/edit_button.png'; ?>' ],function(){});
hoverSongs();
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