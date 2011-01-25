<?php
include('../../../wp-config.php');
//include('oqey-functions.php');
global $wpdb, $current_user;
   $oqey_galls = $wpdb->prefix . "oqey_gallery";
   $oqey_images = $wpdb->prefix . "oqey_images";
   $oqey_music = $wpdb->prefix . "oqey_music";
   $oqey_music_rel = $wpdb->prefix . "oqey_music_rel";
   $oqey_skins = $wpdb->prefix . "oqey_skins";
   
$oqeyImagesRoot = get_option('siteurl')."/wp-content/oqey_gallery/galleries/";

/*create new gallery*/
if(isset($_POST["data"])){	

$resp = array();
$res = $_POST["data"];

if($res["newtitle"] != ""){	

$folder = sanitize_title($res["newtitle"]);

if(!$sql=$wpdb->get_row($wpdb->prepare("SELECT * FROM $oqey_galls WHERE title = %s",mysql_real_escape_string(stripslashes($res["newtitle"]))))){

if($sql=$wpdb->get_row($wpdb->prepare("SELECT * FROM $oqey_galls WHERE folder = %s", $folder ))){ $folder = $folder.time(); }

$add = $wpdb->query("INSERT INTO $oqey_galls (title, author, folder) VALUES ('".mysql_real_escape_string(stripslashes($res["newtitle"]))."', '".$current_user->ID."', '".$folder."' )");
$lastid = mysql_insert_id();

if($add){
   $img = str_replace('\\', '/', ABSPATH)."/wp-content/oqey_gallery/galleries/".$folder."/galimg";
   $thumb = str_replace('\\', '/', ABSPATH)."/wp-content/oqey_gallery/galleries/".$folder."/galthmb";   
   $iphone = str_replace('\\', '/', ABSPATH)."/wp-content/oqey_gallery/galleries/".$folder."/iphone";   
   wp_mkdir_p($img);
   wp_mkdir_p($thumb);
   wp_mkdir_p($iphone);
   $resp["response"] .= 'Created';
   $resp["galid"] .= $lastid;
   $resp["last_gal"] .= '<li class="li_gallery" id="gall_id_'.$lastid.'">'.mysql_real_escape_string(stripslashes($res["newtitle"])).'<br/>
                         <span><a href="#edit" onclick="getGalleryDetails(\''.$lastid.'\'); return false;">edit</a></span>
						 <span><a href="#delete" onclick="deleteGallery(\''.$lastid.'\'); return false;">delete</a></span>
						 </li>';

}else{
$resp["response"] .= 'Error, try again please';
}	
}else{
$resp["response"] .= 'Gallery already exist';
}
}else{
$resp["response"] .= 'Title missing';	
}
echo custom_json::encode($resp);
}
/*END*/

/*request all galleries*/
if(isset($_POST['allgalls'])){
$get_list = $wpdb->get_results("SELECT * FROM $oqey_galls WHERE status !=2 ORDER BY gall_order ASC, id DESC");
$r = "";
$r .= '<table width="900" border="0" cellspacing="0" cellpadding="3" id="gallerytable" class="tablesorter">
       <tbody id="sortable">';
if(!empty($get_list)){
	foreach ($get_list as $list){   
	 $r .=  '<tr id="row_'.$list->id.'">
             <td align="center" width="100" valign="middle" style="height:35px; padding-top:7px;">
			 <a href="#edit" onclick="getGalleryDetails(\''.$list->id.'\'); return false;">
			 <img src="'.oQeyPluginUrl().'/images/edit_button.png" title="Click to edit this gallery"/></a>
			 <a href="#preview" class="preview-gallery" id="'.$list->id.'"><img src="'.oQeyPluginUrl().'/images/preview_button.png" title="Click to preview this gallery"/></a>
			 </td>
             <td align="left" width="770" valign="middle"><div class="dblclick" id="gall_id_'.$list->id.'">'.$list->title.'</div></td>
             <td width="30" align="center" valign="middle" class="lasttd"><a href="#delete" onclick="deleteGallery(\''.$list->id.'\'); return false;" class="hiddenm">
			 <img src="'.oQeyPluginUrl().'/images/remove_button.png" width="24" height="24" title="Click to move to trash this gallery"/>
			 </a></td>
             </tr>';
	}
}else{ 

$r .= '<tr id="row_">
       <td align="center" width="50" style="height:35px;">&nbsp;</td>
       <td align="left" width="700" valign="middle">No galleries.</td>
       <td width="50" align="center" valign="middle" class="lasttd">&nbsp;</td>
       </tr>';
}

$r .= '</tbody>
       </table>';

echo $r;
}
/*END*/

/*Rename category title or rename*/
if(isset($_POST['gal_edit_title']) && isset($_POST['gal_edit_id'])){

$gal_id = str_replace("gall_id_", "", $_POST['gal_edit_id'] );
if(!$sql=$wpdb->get_row($wpdb->prepare("SELECT * FROM $oqey_galls WHERE title = %s AND id != %d ",mysql_real_escape_string(stripslashes(trim($_POST['gal_edit_title']))), $gal_id))){ 
$title = mysql_real_escape_string(stripslashes($_POST['gal_edit_title']));
}else{
$sql=$wpdb->get_row($wpdb->prepare("SELECT * FROM $oqey_galls WHERE title = %s AND id != %d ",mysql_real_escape_string(stripslashes(trim($_POST['gal_edit_title']))), $gal_id));
$title = $sql->title.time();	
}
$gal_update = sprintf("UPDATE $oqey_galls SET title = '%s' WHERE id = '%d' ",trim($title), $gal_id);						
$gal= mysql_query($gal_update) or die (mysql_error());
if($gal){
echo stripslashes($title);
}else{
echo '<p class="error fade">Error!</p>';
}
}
/*END edit*/

/*retrieve data for the new gallery by ID*/

if(isset($_POST['newgallid'])){

$gal = $wpdb->get_row("SELECT * FROM $oqey_galls WHERE id ='".mysql_real_escape_string($_POST['newgallid'])."'");
$aid = $wpdb->get_row("SELECT author FROM $oqey_galls WHERE id ='".mysql_real_escape_string($_POST['newgallid'])."'");

$resp = array();

$resp["response"] .= '<table width="900" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="40" align="right" valign="middle"><a href="#back_to_all_galleries" id="view_all_galleries"><img src="'.oQeyPluginUrl().'/images/back_to_list.png" title="back to all galleries" width="28" height="28" class="imgp"/></a></td>
    <td width="32" align="right" valign="middle"><a href="#add_manage_music" id="oqeymusic"><img src="'.oQeyPluginUrl().'/images/music_button.png" title="add / manage music" width="28" height="28" class="imgp"/></a></td>
    <td width="32" align="right" valign="middle"><a href="#add_manage_skins" id="oqeyskin"><img src="'.oQeyPluginUrl().'/images/skin_button.png" title="add / manage skins" width="28" height="28" class="imgp"/></a></td>

    <td width="188" align="right" valign="middle">
    <div id="dodiv" style="margin-left:10px;">
    <select name="dolist" id="dolist">
      <option value="0" selected="selected">Bulk Actions</option>
      <option value="2">Move to Trash</option>
    </select>
	<input type="button" name="doapply" id="doapply" value="Apply"/>
	</div>
    </td>
    <td width="98" align="left" valign="middle"><input name="selectall" type="checkbox" id="selectall">&nbsp;<span id="seelectmessage">select all</span></td>
    
    <td width="377" align="left" valign="middle">&nbsp;</td>
    <td width="133" height="50" align="left" valign="middle">
      <div class="uploader" id="flashuploader">
      <div id="upload">Upload</div>
      <div id="status" ></div>
      <script type="text/javascript">
	  var flashvars = {BatchUploadPath:"'.str_replace('\\', '/', ABSPATH).'wp-content/oqey_gallery/galleries/'.$gal->folder.'",
		 			   Handler:"'.oQeyPluginUrl().'/bcupload.php",
					   FTypes:"*.jpg;*.png",
					   FDescription:"Media Files"};
	  var params = {bgcolor:"#FFFFFF", allowFullScreen:"true", wMode:"transparent"};
      var attributes = {id: "flash"};
	  swfobject.embedSWF("'.oQeyPluginUrl().'/demoupload.swf", "flashuploader", "110", "30", "8.0.0", "", flashvars, params, attributes);
      </script>
    </div>
    </td>
  </tr>
</table>
';
//author: '.wp_dropdown_users("selected=".$aid->author."&echo=0").' 
$f = str_replace('\\', '/', ABSPATH).'wp-content/oqey_gallery/galleries/'.$gal->folder;
if(!is_dir($f)){
$folderexist = '<p class="error">Error creating the gallery folder (might be a server restrictions issue). Please create this folder manually (wp-content/oqey_gallery/galleries/'.$gal->folder.') and set permissions to 755.</p>';
}

$resp["galid"] .= $_POST['newgallid'];
$resp["folder"] .= $gal->folder;
$resp["folderexist"] .= $folderexist;

echo custom_json::encode($resp);
}
/*END*/

/*get all images By gallery ID*/
if(isset($_POST['allimages'])){
$resp = array();

$images = $wpdb->get_results("SELECT * FROM $oqey_images WHERE gal_id ='".mysql_real_escape_string($_POST['allimages'])."' AND status !=2 ORDER BY img_order ASC, id DESC");
$folder = getFolder($_POST['allimages'])."/galthmb/";
$imgFolder = getFolder($_POST['allimages'])."/galimg/";

$s = $wpdb->get_row("SELECT splash_img FROM $oqey_galls WHERE id ='".mysql_real_escape_string($_POST['allimages'])."'");

if(count($images)>0){
$resp["allimages"] .=  '<div class="allimages">
                        <ul id="sortablegalls">';
$i=0;
$preload = array(); 
foreach($images as $img){
if($s->splash_img==$img->id){ $b=' style="border:#7A82DE thin solid;" class="imgsel"'; }
$resp["allimages"] .=  '<li id="img_li_'.$img->id.'"'.$b.'><div class="allbut" align="center">
                       <a href="#delete" onclick="deleteImage(\''.$img->id.'\'); return false;"><img src="'.oQeyPluginUrl().'/images/remove_small_button.png" width="14" height="14" alt="move to trash" title="move to trash"/></a>  		  
		               <a href="#image_details" onclick="showSettings(\''.$img->id.'\'); return false;"><img src="'.oQeyPluginUrl().'/images/settings_small_button.png" width="14" height="14" alt="image details" title="image details"/></a>
		               <a href="#image" onclick="showImage(\''.$img->id.'\', \''.urlencode(trim($oqeyImagesRoot.$imgFolder.$img->title)).'\'); return false;"><img src="'.oQeyPluginUrl().'/images/details_small_button.png" width="14" height="14" alt="image" title="preview image"/></a>
		               <input name="selected" type="checkbox" value="'.$img->id.'" class="styled" id="selected_'.$img->id.'">
		               </div>
		               <img src="'.oQeyPluginUrl().'/images/separator_line.png" width="120" height="2" align="middle"/>
		               <img src="'.$oqeyImagesRoot.$folder.trim($img->title).'" alt="image_'.$img->id.'" />
                       </li>';
$b = "";
$preload[$i] = $oqeyImagesRoot.$imgFolder.trim($img->title);
$i++;
}
$resp["allimages"] .=  '</ul></div>';
}else{
$resp["allimages"] .=  '<div class="allimages">';
$resp["allimages"] .=  'No images. Please upload some pictures.';	
$resp["allimages"] .=  '</div>';
}

$resp["allimgpreload"] = $preload;

echo custom_json::encode($resp);
}
/*END*/

/*Song move to trash*/
if(isset($_POST['songtotrash']) ){
$song_trash = sprintf("UPDATE $oqey_music SET status = '%d' WHERE id = '%d' ", "2", mysql_real_escape_string($_POST['songtotrash']) );						
$song = mysql_query($song_trash) or die (mysql_error());
if($song){
echo trim($_POST['songtotrash']);
}
}
/*End song trash*/

/*Song move from trash*/
if(isset($_POST['restoresong']) ){
$song_trash = sprintf("UPDATE $oqey_music SET status = '%d' WHERE id = '%d' ", "0", mysql_real_escape_string($_POST['restoresong']) );						
$song = mysql_query($song_trash) or die (mysql_error());
if($song){
    if(!$sql=$wpdb->get_row($wpdb->prepare("SELECT id FROM $oqey_music WHERE status = '%d' ", "2" ))){ $e="no"; }
echo $e;
//echo trim($_POST['restoresong']);
}
}
/*End song trash*/

/*DELETE song*/
if(isset($_POST['deletesong'])){
$d = $wpdb->query("DELETE FROM $oqey_music WHERE id = '".mysql_real_escape_string($_POST['deletesong'])."' ");
$r = $wpdb->query("DELETE FROM $oqey_music_rel WHERE music_id = '".mysql_real_escape_string($_POST['deletesong'])."' ");

if($d){
 if(is_file(str_replace('\\', '/', ABSPATH)."wp-content/oqey_gallery/music/".$_POST['name'])){    
	$i = unlink(str_replace('\\', '/', ABSPATH)."wp-content/oqey_gallery/music/".$_POST['name']);
   }
    if(!$sql=$wpdb->get_row($wpdb->prepare("SELECT id FROM $oqey_music WHERE status = '%d' ", "2" ))){ $e="no"; }
echo $e;
//echo trim($_POST['deletesong']);
}
}
/*End delete song*/


/*gallery move to trash*/
if(isset($_POST['movetotrashgall']) ){
$gal_trash = sprintf("UPDATE $oqey_galls SET status = '%d' WHERE id = '%d' ", "2", mysql_real_escape_string($_POST['movetotrashgall']) );						
$gal = mysql_query($gal_trash) or die (mysql_error());
if($gal){
echo "Gallery was moved to trash.";
}
}
/*End gallery trash*/

/*gallery restore from trash undo*/
if(isset($_POST['undogallid']) ){
$resp = array();
$gal_trash = sprintf("UPDATE $oqey_galls SET status = '%d' WHERE id = '%d' ", "0", mysql_real_escape_string($_POST['undogallid']) );						
$gal = mysql_query($gal_trash) or die (mysql_error());

if($gal){
if(!$sql=$wpdb->get_row($wpdb->prepare("SELECT id FROM $oqey_galls WHERE status = '%d'  ", "2" ))){ $e="no"; }
//echo $e;

$resp["statusul"] = $e;
$resp["mesaj"] = "Gallery was restored.";
echo custom_json::encode($resp);
	
//echo "Gallery was restored.";
}
}
/*End gallery restore*/


/*single image move to trash*/
if(isset($_POST['delimgid']) ){
$img_trash = sprintf("UPDATE $oqey_images SET status = '%d' WHERE id = '%d' ", "2", mysql_real_escape_string($_POST['delimgid']) );						
$img = mysql_query($img_trash) or die (mysql_error());
if($img){
echo 'Image was moved to trash.';
}
}
/*End images trash*/

/*single image move to trash*/
if(isset($_POST['undoimgid']) ){
$img_trash = sprintf("UPDATE $oqey_images SET status = '%d' WHERE id = '%d' ", "0", mysql_real_escape_string($_POST['undoimgid']) );						
$img = mysql_query($img_trash) or die (mysql_error());
if($img){
echo 'Image was restored.';
}
}
/*End images trash*/


/*all selected images move to trash*/
if(isset($_POST['imgalltotrash']) ){
$i=0;
foreach ($_POST['imgalltotrash'] as $id){
$img_trash = sprintf("UPDATE $oqey_images SET status = '%d' WHERE id = %d ", "2", mysql_real_escape_string($id));						
$img = mysql_query($img_trash) or die (mysql_error());
$i++;
}
if($i>0){
echo "All selected images was moved to trash.";
}else{
echo "Please select an image.";
}
}

if(isset($_POST['imgallfromtrash']) ){
$resp = array();
$i=0;
foreach ($_POST['imgallfromtrash'] as $id){
$img_trash = sprintf("UPDATE $oqey_images SET status = '%d' WHERE id = %d ", "0", mysql_real_escape_string($id));						
$img = mysql_query($img_trash) or die (mysql_error());
$i++;
}
if($i>0){

//echo "All selected images was restored.";
if(!$sql=$wpdb->get_row($wpdb->prepare("SELECT id FROM $oqey_images WHERE gal_id = '%d' AND status = '%d'  ",mysql_real_escape_string($_POST['galid']), "2" ))){ $e="no"; }
$resp["statusul"] = $e;
$resp["imgallfromtrash"] = $_POST['imgallfromtrash'];
echo custom_json::encode($resp);
}
}
/*End all selected images trash*/

/*update gallery author*/
if(isset($_POST['usrid']) ){
$up = sprintf("UPDATE $oqey_galls SET author = '%d' WHERE id = '%d' ", mysql_real_escape_string($_POST['usrid']), mysql_real_escape_string($_POST['gid']) );						
$author_up = mysql_query($up) or die (mysql_error());
}
/*End update gall author*/


/*Get the previw*/
if(isset($_POST['previewid'])){

$galleryID = mysql_real_escape_string($_POST['previewid']);

$gal = $wpdb->get_row("SELECT * FROM $oqey_galls WHERE id ='".$galleryID."'");
$folder = $gal->folder;

if($gal->skin_id!="0"){
$skin = $wpdb->get_row("SELECT * FROM $oqey_skins WHERE id = '".$gal->skin_id."'");
}else{
$skin = $wpdb->get_row("SELECT * FROM $oqey_skins WHERE status = '1'");
}

$ruta_img_bg = get_option('siteurl').'/wp-content/oqey_gallery/galleries/'.$folder.'/galimg/';
$thumb_root = get_option('siteurl').'/wp-content/oqey_gallery/galleries/'.$folder.'/galthmb/';

$oqey_height = get_option('oqey_height');
$oqey_width = get_option('oqey_width');
$oqey_bgcolor = get_option('oqey_bgcolor');
$plugin_url = oQeyPluginUrl();
$plugin_repo_url = oQeyPluginRepoUrl();
$show = "yes";
  	 $get_bg_img = $wpdb->get_row("SELECT *
								     FROM $oqey_galls 
							        WHERE id ='".mysql_real_escape_string($galleryID)."'
                                  ");			  

$extract_images = $wpdb->get_results("SELECT *
                                        FROM $oqey_images
                                       WHERE gal_id = '".mysql_real_escape_string($galleryID)."'
									ORDER BY img_order ASC
									 ");
$totalimg = count($extract_images);
$outputIMG .= '<div align="center" class="nofimg">';
if($totalimg>0){
foreach($extract_images as $images){ 
$outputIMG .= '<span class="noflashimg"><img src="'.$thumb_root.$images->title.'" alt="'.$images->alt.'"/></span>';
}
}else{
echo '<span style="margin:10px;">No images. Please upload some pictures.</span>';
$show = "no";
}
$outputIMG .= '</div>';		  
      if($get_bg_img->splash_img !=0){
	 	$get_bg_img2 = $wpdb->get_row("SELECT *
                                        FROM $oqey_images
							           WHERE oqey_img_id ='".$get_bg_img->oqey_thumb_img."'  ");
									   
	    if(count($get_bg_img2)==0){		
		$get_bg_img2 = $wpdb->get_row("SELECT *
                                         FROM $oqey_images
							            WHERE gal_id ='".mysql_real_escape_string($galleryID)."'
							    	 ORDER BY img_order ASC
							    	    LIMIT 0,1 ");		
		}		
	  }else{
	    $get_bg_img2 = $wpdb->get_row("SELECT *
                                         FROM $oqey_images
							            WHERE gal_id ='".mysql_real_escape_string($galleryID)."'
							    	 ORDER BY img_order ASC
							    	    LIMIT 0,1 ");  	
	  } 
	  
      $bg_images = $ruta_img_bg.$get_bg_img2->title;

if($show=="yes"){
if($_POST['flash']=="no"){
echo '<div style="float:left;">'.$outputIMG.'</div>';
}else{
echo '
<object height="600" width="896">
<param value="#ffffff" name="bgcolor">
<param value="true" name="allowFullScreen">
<param name="movie" value="'.$plugin_repo_url.'/skins/'.$skin->folder.'/'.$skin->folder.'.swf">
<param name="FlashVars" value="flashId='.$skin->skinid.'&amp;FKey='.$skin->comkey.'&amp;GalleryPath='.$plugin_url.'&amp;GalleryID='.$galleryID.'&amp;FirstRun='.$skin->firstrun.'">
<embed src="'.$plugin_repo_url.'/skins/'.$skin->folder.'/'.$skin->folder.'.swf" bgcolor="#ffffff" FlashVars="flashId='.$skin->skinid.'&amp;FKey='.$skin->comkey.'&amp;GalleryPath='.$plugin_url.'&amp;GalleryID='.$galleryID.'&amp;FirstRun='.$skin->firstrun.'" width="896" height="600" wmode="transparent" allowFullScreen="true"></embed>
</object>
';
}
}

}
/*END preview*/


/*Images order*/
if(isset($_POST['orderallimgs']) && isset($_POST['galleryid']) ){	
$r = str_replace("img_li[]=", "", $_POST['orderallimgs']);
$gal_img_id = mysql_real_escape_string($_POST['galleryid']);
$r =  explode("&", $r);
foreach ($r as $position => $item){
$images_update = sprintf("UPDATE $oqey_images
                             SET img_order = '%d'
						   WHERE id = '%s'
						     AND gal_id = '%s'
					   ", $position, 
						  $item,
						  $gal_img_id
						);						
$update_images = mysql_query($images_update) or die (mysql_error());
}
}
/*END images order*/

/*Images details*/
if(isset($_POST['imagedetails'])){	

$resp = array();
$splash = 'off';
$splashexclusive = 'off';

$image = $wpdb->get_row("SELECT * FROM $oqey_images WHERE id ='".mysql_real_escape_string($_POST['imagedetails'])."'");

if($sql=$wpdb->get_row($wpdb->prepare("SELECT splash_img FROM $oqey_galls WHERE id = '%d' AND splash_img = %s",$image->gal_id, mysql_real_escape_string($_POST['imagedetails'])  ))){

$splash = 'on';

$s = $wpdb->get_row("SELECT splash_only FROM $oqey_galls WHERE id ='".$image->gal_id."'");
if($s->splash_only=="1"){ $splashexclusive = 'on'; }
}

$resp['alt'] .= $image->alt;
$resp['comments'] .= $image->comments;
$resp['splash'] .= $splash;
$resp['splashexclusive'] .= $splashexclusive;

echo custom_json::encode($resp);
}
/*END images details*/


//imgid: id, galid: galid, splash: splash, splashex: splashex, alt: alt, comments: comments

/*Update image details*/
if(isset($_POST['imgid']) && isset($_POST['galid']) && isset($_POST['alt']) && isset($_POST['comments'])){	

$up = sprintf("UPDATE $oqey_images
                              SET alt = '%s', comments = '%s'
					        WHERE id = '%d'
					      ", mysql_real_escape_string(stripslashes(urldecode($_POST['alt']))),
						     mysql_real_escape_string(stripslashes(urldecode($_POST['comments']))),
					         mysql_real_escape_string($_POST['imgid'])
				          );	
$alt_up = mysql_query($up) or die (mysql_error()); // image_alt

$m = "not";
$c = $wpdb->get_row("SELECT splash_img FROM $oqey_galls WHERE id ='".mysql_real_escape_string($_POST['galid'])."'");

if($c->splash_img==$_POST['imgid']){
$splash = sprintf("UPDATE $oqey_galls SET splash_img ='%d',splash_only = '%d' WHERE id = '%d'
			      ", 0, 0, mysql_real_escape_string($_POST['galid'])  );	
$splashk = mysql_query($splash) or die (mysql_error());
}

if(isset($_POST['splash']) && isset($_POST['splashexclusive']) ){

$splash = sprintf("UPDATE $oqey_galls SET splash_img ='%d',splash_only = '%d' WHERE id = '%d'
			      ", mysql_real_escape_string($_POST['imgid']), 1, mysql_real_escape_string($_POST['galid']) );						
$splashk = mysql_query($splash) or die (mysql_error());
$m = $_POST['imgid'];
}elseif(isset($_POST['splash'])){	
	
$splash = sprintf("UPDATE $oqey_galls SET splash_img ='%d',splash_only = '%d' WHERE id = '%d'
			      ", mysql_real_escape_string($_POST['imgid']), 0, mysql_real_escape_string($_POST['galid']) );						
$splashk = mysql_query($splash) or die (mysql_error());
$m = $_POST['imgid'];
}
echo $m;
}
/*END update image details*/

/*Sa inceapa muzica , - Parazitii - */

/*get gallery music and the entire list*/
if(isset($_POST['music_gall_id']) ){  

$gal_id = mysql_real_escape_string($_POST['music_gall_id']);

$get_music_list = $wpdb->get_results("SELECT * 
								       FROM $oqey_music AS f 
							     INNER JOIN $oqey_music_rel AS s 
								 	     ON f.id = s.music_id
									  WHERE s.gallery_id = '$gal_id'
								   ORDER BY s.mrel_order ASC
										" ); 
				 
echo '<h4 style="padding-left:10px; margin-left:10px;">Select music</h4>
      <div style="muzica" id="muzica">
      <form action="#" name="musicselect" id="musicselect">
	  <ul id="sortablemuzon" name="sortablemuzon">';	
$interogare = "";
foreach ($get_music_list as $music){
	echo '<li><input type="checkbox" checked="checked" value="'.$music->music_id.'" name="check_music_'.$music->music_id.'" id="check_music_'.$music->music_id.'">'.$music->title.'</li>';	
	$interogare .= "id !='".$music->music_id."' AND ";
}
$get_music_list_all = $wpdb->get_results("SELECT * FROM $oqey_music WHERE ".$interogare." status !=2");

foreach ($get_music_list_all as $musicall){
	echo '<li><input type="checkbox" value="'.$musicall->id.'" name="check_music_'.$musicall->id.'" id="check_music_'.$musicall->id.'">'.$musicall->title.'</li>';
}

echo '</ul>
      </form>      
	  </div>
	  <input type="button" name="savemusic" id="savemusic" value="Save changes" class="savemusic" style=" margin-left:20px; margin-bottom:10px;"/>
';
}

/*save the music in order*/
if(isset($_POST['mgalleryid'])){

$all = count($_POST['selectedmusic']);

$delete_music = $wpdb->query("DELETE FROM $oqey_music_rel WHERE gallery_id = '".mysql_real_escape_string($_POST['mgalleryid'])."'");

$i=0;
if($all>0){
foreach ($_POST['selectedmusic'] as $l => $d){
$min = $wpdb->query("INSERT INTO $oqey_music_rel (music_id, gallery_id, mrel_order) VALUES ('".$d['value']."', '".mysql_real_escape_string($_POST['mgalleryid'])."', '$i')");
$i++;
}//end foreach
}
echo "All changes was saved";
}
/*end save music order */


/*get all skins*/
if(isset($_POST['allskins'])){

$get_list = $wpdb->get_results("SELECT * FROM $oqey_skins WHERE status !=2 AND status !=1 ORDER BY id DESC");
echo '<table width="900" border="0" cellspacing="0" cellpadding="15" id="musictable" class="tablesorter">
      <thead><tr>
      <th colspan="3" align="left" valign="middle" style="padding:15px;">Available skins</th>
      </tr></thead>
      <tbody id="sortable">';


if(!empty($get_list)){
	
	foreach ($get_list as $r){  
	
	if($r->commercial=="yes"){ $comm = " - Commercial skin"; }else{ $comm = " - Free skin"; }
	
       echo '<tr id="skink_tr_'.$r->id.'">
             <td width="170" height="120" align="center" valign="middle">
			 <img src="'.oQeyPluginRepoUrl().'/skins/'.$r->folder.'/'.$r->folder.'.jpg" alt="skin" width="150" height="100" style="border:#999999 solid thin; margin-left:15px;" >
			 </td>
             <td width="460" align="left" valign="top" style="margin-left:10px; padding:5px;">
			 <h4>'.$r->name.$comm.'</h4>
             '.$r->description.'<br/>
             Skin files location: <code>/skins/'.$r->folder.'</code>.
			 <p><a href="#set_as_default" class="set_as_default" id="'.$r->id.'">Set as default</a> | <a href="#delete_this_skin" class="delete_this_skin" id="'.$r->id.'">Move to trash</a></p>
             
</td>
             <td width="270" align="left" valign="top" style="margin-left:10px; padding:5px;">';
		
		if($r->commercial=="yes"){
		if($r->firstrun==0){ 
		
		echo '<div><p>
		Commercial key:<br/>
                 <input name="key" class="key" value="'.$r->comkey.'" id="key'.$r->id.'" size="35" style="background-color:#CCC;" readonly="readonly"/>
			  </p></div>';
		
		}else{

		echo '<div>
               <p>
			   Commercial key:<br/>
                 <input name="comkey" class="comkey" type="text" value="'.$r->comkey.'" id="key'.$r->id.'" style="background-color:#CCC; width:210px;" />
				 <input type="button" name="savekey" class="savekey" id="'.$r->id.'" value="save" style="background-color:#CCC; width:43px;">
               </p>
               <p>
<form action="http://oqeysites.com/paypal/oqeypaypal.php" name="buyskin" method="post">
<input type="hidden" name="oqey" value="qwe1qw5e4cw8c7fv8h7" />
<input type="hidden" name="website" value="'.urlencode(get_option('siteurl')).'" />
<input type="hidden" name="s" value="'.$r->skinid.'" />';
echo '<input type="text" name="d" value="discount code" class="discount_code" style="background-color:#CCC; width:259px;"/>'; 
echo '<a href="#buy_this_skin" class="buy_this_skin"><img src="'.oQeyPluginUrl().'/images/btn_buynowcc_lg.gif" style="margin-top:8px;" /></a>
</form>
			   </p>
              </div>';
		}
		}
	  echo '</td></tr>';
	}
}else{ 
echo '<tr id="skink_tr_"><td height="30" align="center" valign="middle">No available skins.</td></tr>';
}

echo '</tbody>
      </table>';

}
/*END get all skins*/



/*Save skin key*/

if(isset($_POST['savekey'])){

$u = sprintf("UPDATE $oqey_skins SET comkey = '%s' WHERE id = '%d' ", mysql_real_escape_string($_POST['comkey']), mysql_real_escape_string($_POST['savekey']) );						
$upd= mysql_query($u) or die (mysql_error());
if($upd){
echo 'Skin key was saved.';
}

}

/*END save key*/



/*single skin move to trash*/
if(isset($_POST['movetotrashskin']) ){
$skin = sprintf("UPDATE $oqey_skins SET status = '%d' WHERE id = '%d' ", "2", mysql_real_escape_string($_POST['movetotrashskin']) );						
$s = mysql_query($skin) or die (mysql_error());
if($s){
echo 'Skin was moved to trash.';
}
}
/*End skin to trash*/



/*single skin move to trash*/
if(isset($_POST['undoskin']) ){
$skin = sprintf("UPDATE $oqey_skins SET status = '%d' WHERE id = '%d' ", "0", mysql_real_escape_string($_POST['undoskin']) );						
$s = mysql_query($skin) or die (mysql_error());
if($s){
    if(!$sql=$wpdb->get_row($wpdb->prepare("SELECT id FROM $oqey_skins WHERE status = '%d' ", "2" ))){ $e="no"; }
echo $e;
//echo 'Skin was restored.';
}
}
/*End skin to trash*/

/*DELETE skin*/
if(isset($_POST['deleteskinper'])){
$d = $wpdb->query("DELETE FROM $oqey_skins WHERE id = '".mysql_real_escape_string($_POST['deleteskinper'])."' ");
$u = sprintf("UPDATE $oqey_galls SET skin_id = '%d' WHERE skin_id = '%d' ", 0, mysql_real_escape_string($_POST['deleteskinper']));						
$upd= mysql_query($u) or die (mysql_error());

if($d){
 if(is_dir(str_replace('\\', '/', ABSPATH)."wp-content/oqey_gallery/skins/".mysql_real_escape_string($_POST['name'])) ){   
 $dir = str_replace('\\', '/', ABSPATH)."wp-content/oqey_gallery/skins/".mysql_real_escape_string($_POST['name']);
 $do = rm($dir);
if($do){ 
if(!$sql=$wpdb->get_row($wpdb->prepare("SELECT id FROM $oqey_skins WHERE status = '%d' ", "2" ))){ $e="no"; }
echo $e;
//echo 'Skin was deleted.'; 
}
//else{ echo 'Error. Skin wasn`t deleted.'; }
} 

}
}
/*End delete song*/



/*Get the current skin */
if(isset($_POST['skin_gall_id']) ){

$s = $wpdb->get_row("SELECT skin_id, status FROM $oqey_galls WHERE id = '".mysql_real_escape_string($_POST['skin_gall_id'])." ' ");

//$s->skin_id
if($s->skin_id !=0 && $s->status!=2){
$r = $wpdb->get_row("SELECT * FROM $oqey_skins WHERE id = '".$s->skin_id."'");
}else{
$r = $wpdb->get_row("SELECT * FROM $oqey_skins WHERE status = '1'");
}

echo '<table width="900" border="0" cellspacing="0" cellpadding="0" id="currentskintable">
      <thead><tr>
      <th colspan="2" align="left" valign="middle" style="padding:15px;">Current skin</th>
      </tr></thead>
      <tbody id="sortable">
	  <tr id="skink_tr_'.$r->id.'">
             <td width="180" height="120" align="center" valign="middle">
			   <div align="center"><img src="'.oQeyPluginRepoUrl().'/skins/'.$r->folder.'/'.$r->folder.'.jpg" alt="skin" width="150" height="100" style="border:#999999 solid thin;"/>
	           </div></td>
 <td width="720" align="left" valign="top">
	    <p align="left" style="padding:5px;"><b>'.$r->name.'</b><br/>'.$r->description.'<br/>
             Skin files location: <code>/skins/'.$r->folder.'</code>.
        </p> 
	    </td>       
        </tr>
</tbody>
</table>';


$get_list = $wpdb->get_results("SELECT * FROM $oqey_skins WHERE id !='".$r->id."' AND status != '2' ORDER BY id DESC");
echo '<table width="900" border="0" cellspacing="0" cellpadding="0" id="skintable" class="tablesorter">
      <thead><tr>
      <th colspan="2" align="left" valign="middle" style="padding:15px;">Available skins</th>
      </tr></thead>
      <tbody id="sortable">';

if(!empty($get_list)){
	foreach ($get_list as $r){   
       echo '<tr id="skink_tr_'.$r->id.'">
             <td width="180" height="120" align="center" valign="middle">
			   <div align="center"><img src="'.oQeyPluginRepoUrl().'/skins/'.$r->folder.'/'.$r->folder.'.jpg" alt="skin" width="150" height="100" style="border:#999999 solid thin;"/>
	           </div></td>
 <td width="720" align="left" valign="top">
	    <p align="left" style="padding:5px;"><b>'.$r->name.'</b><br/>'.$r->description.'<br/>
             Skin files location: <code>/skins/'.$r->folder.'</code>.<br/><br/>
			 <a href="#activate_skin" class="activate_skin" id="'.$r->id.'">Activate this skin</a>
        </p> 
	    </td>       
        </tr>';
	}
}else{ 
echo '<tr id="skink_tr_'.$r->id.'">
             <td align="center" height="30" valign="middle">No available skins.</td>
             </tr>';
}

echo '</tbody>
      </table>
	  ';
}
/*End current skin*/

/*single skin move to trash*/
if(isset($_POST['skin_gallery_id'])){
$skin = sprintf("UPDATE $oqey_galls SET skin_id = '%d' WHERE id = '%d' ", mysql_real_escape_string($_POST['skinid']), mysql_real_escape_string($_POST['skin_gallery_id']) );						
$s = mysql_query($skin) or die (mysql_error());
if($s){
echo "New skin was set.";
}
}
/*End skin to trash*/

/*DELETE gallery permanently*/
if(isset($_POST['deletegall'])){
$e="";
$d = $wpdb->query("DELETE FROM $oqey_galls WHERE id = '".mysql_real_escape_string($_POST['deletegall'])."' ");
$r = $wpdb->query("DELETE FROM $oqey_music_rel WHERE gallery_id = '".mysql_real_escape_string($_POST['deletegall'])."' ");

if($d){
 if(is_dir(str_replace('\\', '/', ABSPATH)."wp-content/oqey_gallery/galleries/".mysql_real_escape_string($_POST['name'])) ){   
 $dir = str_replace('\\', '/', ABSPATH)."wp-content/oqey_gallery/galleries/".mysql_real_escape_string($_POST['name']);
 $do = rm($dir);
if($do){ 
if(!$sql=$wpdb->get_row($wpdb->prepare("SELECT id FROM $oqey_galls WHERE status = '%d' ", "2" ))){ $e="no"; }
echo $e;
//echo 'Gallery was deleted.'; 
}
//else{ echo 'Error. Gallery wasn`t deleted.'; }
} 
}
}
/*End delete gallery*/

/*DELETE gallery images permanently*/
if(isset($_POST['imgalldelete'])){
$e ="";
$folder = getFolder($_POST['galid']);

foreach ($_POST['imgalldelete'] as $title){

$d = $wpdb->query("DELETE FROM $oqey_images WHERE gal_id = '".mysql_real_escape_string($_POST['galid'])."' AND title = '".mysql_real_escape_string($title)."' ");

 if(is_file(str_replace('\\', '/', ABSPATH)."wp-content/oqey_gallery/galleries/".$folder."/galimg/".$title)){    
	$i = unlink(str_replace('\\', '/', ABSPATH)."wp-content/oqey_gallery/galleries/".$folder."/galimg/".$title);
	$t = unlink(str_replace('\\', '/', ABSPATH)."wp-content/oqey_gallery/galleries/".$folder."/galthmb/".$title);
	$t = unlink(str_replace('\\', '/', ABSPATH)."wp-content/oqey_gallery/galleries/".$folder."/iphone/".$title);
   }					
}

if(!$sql=$wpdb->get_row($wpdb->prepare("SELECT id FROM $oqey_images WHERE gal_id = '%d' AND status = '%d'  ",mysql_real_escape_string($_POST['galid']), "2" ))){ $e="no"; }
echo $e;
}
/*End delete gallery images*/



/*first run test*/
if(isset($_GET['firstrun'])){

echo "ok";

}
/*end first run*/

/*save skin key*/
if(isset($_GET['set_skin_key']) && $_GET['pass']=="oqey"){

//mail("dariimd@gmail.com", "ok", "test ok - ".$_GET['set_skin_key']);

}
/*end*/
?>