<?php
$counter = 1;
$d=0;

function initoQeyGalleryPlugin(){// settings page
?>
<div class="wrap">
    <h2>oQey-Gallery settings</h2>
</div>
<?php
			if (isset($_GET['updated']) == "true"){
				echo '<div id="message" class="updated fade"><p><strong>Settings saved.</strong></p></div>';
			}elseif(isset($_GET['updated']) == "false"){
				echo '<div id="message" class="updated fade"><p>No setting was changed since last update.</p></div>';
            }
?>
<div class="wrap">
<script type="text/javascript" charset="utf-8">
jQuery(document).ready(function($) {

var numberRegex = /^[+-]?\d+(\.\d+)?([eE][+-]?\d+)?$/;

if(jQuery("#limitmax").attr('checked')){ 
jQuery(".maxon").show(); 
//jQuery("#crop_images").attr('checked', false)
//jQuery("#crop_images").attr('disabled', true);
}else{ 
jQuery(".maxon").hide(); 
//jQuery("#crop_images").removeAttr('disabled');
}	

 						
jQuery("#limitmax").click(function(){ 
if(jQuery("#limitmax").attr('checked')){ 
jQuery(".maxon").show(); 
//jQuery("#crop_images").attr('checked', false)
//jQuery("#crop_images").attr('disabled', true);
}else{ 
jQuery(".maxon").hide(); 
//jQuery("#crop_images").removeAttr('disabled');
}	
});

jQuery("#oqey_width").change(function(){
var x = jQuery("#oqey_width").val();
if(numberRegex.test(x)) {
if(parseInt(x)<200){ 
jQuery("#oqey_width").val("200"); 
var x = 200;
}

var y = jQuery("#oqey_max_width").val();
if(y==""){ jQuery("#oqey_max_width").val(x);  }

if(parseInt(y)<parseInt(x)){ 
jQuery("#oqey_max_width").val(x); 
}

}else{ alert('Only numbers allowed!!!'); } 
});

jQuery("#oqey_height").change(function(){
var x = jQuery("#oqey_height").val();

if(numberRegex.test(x)) {
if(parseInt(x)<100){ 
jQuery("#oqey_height").val("100");
var x = 100;
}

var y = jQuery("#oqey_max_height").val();
if(y==""){ jQuery("#oqey_max_height").val(x);  }

if(parseInt(y)<parseInt(x)){ 
jQuery("#oqey_max_height").val(x); 
}


}else{ alert('Only numbers allowed!!!'); } 

});

jQuery("#oqey_max_width").change(function(){
				  
var x = jQuery("#oqey_width").val();
var y = jQuery("#oqey_max_width").val();

if(numberRegex.test(y)) {
if(parseInt(y)<parseInt(x)){ jQuery("#oqey_max_width").val(x); }

}else{ alert('Only numbers allowed!!!'); } 
});

jQuery("#oqey_max_height").change(function(){
var h = jQuery("#oqey_height").val();
var x = jQuery("#oqey_max_height").val();
if(numberRegex.test(x)) {
if(parseInt(x)<parseInt(h)){ jQuery("#oqey_max_height").val(h); }
}else{ alert('Only numbers allowed!!!'); } 
});


jQuery("#oqey_thumb_width").change(function(){
var x = jQuery("#oqey_thumb_width").val();
if(numberRegex.test(x)) {
if(parseInt(x)<60){ jQuery("#oqey_thumb_width").val("60"); }
if(parseInt(x)>180){ jQuery("#oqey_thumb_width").val("180"); }
}else{ alert('Only numbers allowed!!!'); } 
});

jQuery("#oqey_thumb_height").change(function(){
var x = jQuery("#oqey_thumb_height").val();
if(numberRegex.test(x)) {
if(parseInt(x)<40){ jQuery("#oqey_thumb_height").val("40"); }
if(parseInt(x)>120){ jQuery("#oqey_thumb_height").val("120"); }
}else{ alert('Only numbers allowed!!!'); } 
});

jQuery("#oqey_effects_trans_time").change(function(){
var x = jQuery("#oqey_effects_trans_time").val();
if(numberRegex.test(x)) {
if(parseFloat(x)<0.1){ jQuery("#oqey_effects_trans_time").val("0.1"); }
if(parseFloat(x)>1.5){ jQuery("#oqey_effects_trans_time").val("1.5"); }
}else{ alert('Only numbers allowed!!!'); }  
});

jQuery("#oqey_pause_between_tran").change(function(){
var x = jQuery("#oqey_pause_between_tran").val();
if(numberRegex.test(x)) {
if(parseFloat(x)<2){ jQuery("#oqey_pause_between_tran").val("2"); }
if(parseFloat(x)>26){ jQuery("#oqey_pause_between_tran").val("26"); }
}else{ alert('Only numbers allowed!!!'); }  
});
			
    jQuery('#demo').hide();
    var f = jQuery.farbtastic('#picker');
    var p = jQuery('#picker').css('opacity', 0.25);
    var selected;
    jQuery('.colorwell')
      .each(function () { f.linkTo(this); jQuery(this).css('opacity', 0.75); })
      .focus(function() {
        if (selected) {
          jQuery(selected).css('opacity', 0.75).removeClass('colorwell-selected');
        }
        f.linkTo(this);
        p.css('opacity', 1);
        jQuery(selected = this).css('opacity', 1).addClass('colorwell-selected');
      });
  });

</script>
<div id="demo" style="color: red; font-size: 1em">Please wait, loading files...</div>

<form method="post" action="options.php">
<?php wp_nonce_field('update-options'); ?>
<table width="900" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td valign="top" align="left">
<table width="450" border="0" cellpadding="1" cellspacing="0">
  <tr valign="top">
<th width="240" height="25" align="left" valign="middle" scope="row"><div align="left">Gallery Width</div></th>
<td width="209" height="25" align="left" valign="middle"><input name="oqey_width" id="oqey_width" type="text" value="<?php echo get_option('oqey_width'); ?>" size="5"/>   pix (min. 200)</td>
</tr>
<tr valign="top">
<th height="25" align="left" valign="middle" scope="row"><div align="left">Gallery Height</div></th>
<td height="25" align="left" valign="middle"><input name="oqey_height" id="oqey_height" type="text" value="<?php echo get_option('oqey_height'); ?>" size="5"/>   pix (min. 100)</td>
</tr>
<tr valign="top">
<th height="25" align="left" valign="middle" scope="row">Limit the maximum IMAGE size</th>
<td height="25" align="left" valign="middle"><input type="checkbox" name="limitmax" id="limitmax" <?php if(get_option('limitmax')=="on"){ echo 'checked="checked"';  } ?>/></td>
</tr>
<tr valign="top" style="display:none;" class="maxon">
<th width="240" height="25" align="left" valign="middle" scope="row">Width:</th>
<td height="25" align="left" valign="middle"><input name="oqey_max_width" id="oqey_max_width" type="text" value="<?php echo get_option('oqey_max_width'); ?>" size="5"/></td>
</tr>

<tr valign="top" style="display:none;" class="maxon">
<th width="240" height="25" align="left" valign="middle" scope="row">Height:</th>
<td height="25" align="left" valign="middle"><input name="oqey_max_height" id="oqey_max_height" type="text" value="<?php echo get_option('oqey_max_height'); ?>" size="5"/></td>
</tr>

<tr valign="top">
<th height="25" align="left" valign="middle" scope="row">Crop images</th>
<td height="25" align="left" valign="middle"><input type="checkbox" name="crop_images" id="crop_images" <?php if(get_option('crop_images')=="on"){ echo 'checked="checked"';  } ?>/></td>
</tr>
<tr valign="top">
<th height="25" align="left" valign="middle" scope="row"><div align="left">Background Color</div></th>
<td height="25" align="left" valign="middle"><input type="text" name="oqey_bgcolor" class="colorwell" value="<?php echo get_option('oqey_bgcolor'); ?>" /></td>
</tr>
<tr valign="top">
<th height="25" colspan="2" align="left" valign="middle" scope="row">&nbsp;</th>
</tr>
</table>
    </td>
    <td>
    <table width="450" border="0" cellspacing="0" cellpadding="3">
  <tr>
    <td height="200"><div id="picker" style="float:left; margin-left:20px;"></div></td>
  </tr>
</table>    
    </td>
  </tr>
</table>


<table width="900" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td width="258" height="25"><strong>Thumbs width</strong></td> 
      <td width="657" height="25"><input name="oqey_thumb_width" id="oqey_thumb_width" type="text" value="<?php echo get_option('oqey_thumb_width'); ?>" size="5"/>
        pix (min. 60, max. 180)</td>
    </tr>
    <tr>
    <td width="258" height="25"><strong>Thumbs height</strong></td>
    <td width="657" height="25"><input name="oqey_thumb_height" id="oqey_thumb_height" type="text" value="<?php echo get_option('oqey_thumb_height'); ?>" size="5"/>
      pix (min. 40, max. 120)</td>
  </tr>
      <tr>
    <td width="258" height="25">&nbsp;</td>
    <td width="657" height="25">&nbsp;</td>
  </tr>
      <tr>
    <td width="258" height="25">Effect transition time:</td>
    <td width="657" height="25"><input name="oqey_effects_trans_time" id="oqey_effects_trans_time" type="text" value="<?php echo get_option('oqey_effects_trans_time'); ?>" size="5"/>
      seconds (min. 0.1, max. 1.5)</td>
  </tr>
  <tr>
    <td width="258">Pause between transitions:</td>
    <td width="657"><input name="oqey_pause_between_tran" id="oqey_pause_between_tran" type="text" value="<?php echo get_option('oqey_pause_between_tran'); ?>" size="5"/>
      seconds</td>
</tr>
<tr valign="top">
<th height="25" align="left" valign="middle" scope="row">Auto hide thumbnails</th>
<td height="25" align="left" valign="middle"><input type="checkbox" name="HideThumbs" id="HideThumbs" <?php if(get_option('HideThumbs')=="on"){ echo 'checked="checked"';  } ?>/></td>
</tr>
</table>
<p>
  <input type="hidden" name="action" value="update" />
  <input type="hidden" name="page_options" value="oqey_height,oqey_width,oqey_bgcolor,oqey_thumb_width,oqey_thumb_height,oqey_max_width,oqey_max_height,limitmax,crop_images,oqey_effects_trans_time,oqey_pause_between_tran,HideThumbs" />
</p>
<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>
</form>
</div>

<?php
}// finish setings page

//add oqey media button
function addoQeyMediaIcon($context)
{
	$qgall_upload_iframe_src = "media-upload.php?type=oqeygall&amp;post_id=$qgall_iframe_ID";
	$qgall_iframe_src = apply_filters('qgall_iframe_src', "$qgall_upload_iframe_src&amp;tab=qgall");
	$qgall_title = __('Add oQey Gallery');
	$qgall_button_src = oQeyPluginUrl().'/images/oqeyinsert.png';
    return $context.
   '<a href="'.$qgall_upload_iframe_src.'&amp;TB_iframe=true&amp;height=500&amp;width=640" class="thickbox" title="'.$qgall_title.'"><img src="'.$qgall_button_src.'" alt="'.$qgall_title.'" /></a>';
}


add_filter('media_buttons_context', 'addoQeyMediaIcon', 777);
//..............................................................//

function oQeyPluginUrl() {
	$url = get_option('siteurl') . '/wp-content/plugins/oqey-gallery';   
	return $url;
}

function oQeyPluginRepoUrl() {
	$url = get_option('siteurl') . '/wp-content/oqey_gallery';   
	return $url;
}

oqeygall_tab_content(); // start tab content

function oqeygall_tab_content()
{
    global $post_ID, $temp_ID, $wpdb;
	if($_GET['type'] == "oqeygall"){
	include ("insert_in_post.php");
    }
}

// Hook for adding admin menus
add_action('admin_menu', 'oqey_add_pages');

// action function for above hook
function oqey_add_pages() {
	$icon = oQeyPluginUrl().'/images/oqeyadminicon.png';
    add_menu_page('oQey-Gallery plugin', 'oQey-Gallery', 8, __FILE__, 'oqey_top_page', $icon);	
	add_submenu_page(__FILE__,'oQey-Gallery plugin', 'Settings', 8, 'oQeysettings',  'initoQeyGalleryPlugin');
	//add_submenu_page(__FILE__,'oQey-Gallery plugin', 'Categories', 8, 'oQeyCategories',  'oqey_categories_page');
    add_submenu_page(__FILE__, 'Galleries', 'Galleries', 8, 'oQeyGalleries', 'oqey_galleries_page');
	add_submenu_page(__FILE__,'oQey-Gallery plugin', 'Skins', 8, 'oQeyGalleriesSkins',  'oqey_galleries_skin_page');
	add_submenu_page(__FILE__, 'Music', 'Music', 8, 'oQeyMusic', 'oqey_music_page');
	add_submenu_page(__FILE__, 'Trash', 'Trash', 8, 'oQeyTrash', 'oqey_trash_page');
	
}

function oqey_top_page() {
?>
<div class="wrap">
        	<h2>oQey-Gallery plugin</h2>
</div>
<div class="wrap">		  
          
<div class="postbox" style="width:850px; padding:10px;">
<strong>About</strong>
<p>oQey Gallery is a Plugin for Wordpress that let users create and manage flash slideshows with a non-flash version of gallery built-in for all non-flash browsers and mobile devices like iPhone / iPad / iPod etc. The flash version supports music and skins, so users can change the way it looks with a few simple clicks. Customizable, commercial skins are also available as well as custom galleries for professionals. Check this out on <a href="http://oqeysites.com/" target="_blank">oqeysites.com</a> </p>        

</div>

<div class="postbox" style="width:850px; padding:10px; height:85px;">
<div align="left"><strong>Donate</strong><p>If you really like this plugin and find it useful, help to keep this plugin free and constantly updated by clicking the donate button below.</p></div>
<div align="right">
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank"><input name="cmd" value="_donations" type="hidden"/><input name="business" value="donations@oqeysites.com" type="hidden"/><input name="lc" value="US" type="hidden"/><input name="item_name" value="oQey Gallery plugin" type="hidden"/><input name="item_number" value="Support Open Source" type="hidden"/><input name="no_note" value="0" type="hidden"/><input name="currency_code" value="USD" type="hidden"/><input name="bn" value="PP-DonationsBF:btn_donateCC_LG.gif:NonHostedGuest" type="hidden"/><input alt="PayPal - The safer, easier way to donate online!" name="submit" src="<?php echo oQeyPluginUrl(); ?>/images/btn_donate.gif" type="image"/><img src="https://www.paypal.com/en_US/i/scr/pixel.gif" alt="" width="1" border="0" height="1"/></form>
</div>
</div>

</div>
<?php
}

function oqey_galleries_page(){ include("managegal.php"); }
function oqey_galleries_skin_page(){ include("manageskins.php"); }
function oqey_music_page(){ include("managemusic.php"); }
function oqey_trash_page(){ include("managetrash.php"); }
//...........front......................//

//add_filter('the_content', 'gallery_embed');

//.........................................................
function gallery_embed($content){//show gallery in content

if (is_feed()) {
$content = preg_replace_callback( "/\[oqeygallery:([^]]+)]/i", "add_oqey_gallery_feed", $content );
}else{
$content = preg_replace_callback( "/\[oqeygallery:([^]]+)]/i", "add_gallery", $content );
}
return $content;
}

function add_oqey_gallery_feed($content){
$content = " [See the post to view the galleries] ";
return $content;
}

function getUserNow($userAgent) {
    $crawlers = 'Google|msnbot|Rambler|Yahoo|AbachoBOT|accoona|' .
    'AcioRobot|ASPSeek|CocoCrawler|Dumbot|FAST-WebCrawler|' .
    'GeonaBot|Gigabot|Lycos|MSRBOT|Scooter|AltaVista|IDBot|eStyle|Scrubby|yandex';
    $isCrawler = (preg_match("/$crawlers/i", $userAgent) > 0);
    return $isCrawler;
}

add_shortcode( 'oqeygallery', 'add_gallery' );

function add_gallery($atts){// insert flash gallery in content
global $counter, $post_ID, $wpdb;

foreach($atts as $a){ 

   $id = str_replace(":", "", $a);

   $oqey_galls = $wpdb->prefix . "oqey_gallery";
   $oqey_images = $wpdb->prefix . "oqey_images";
   //$oqey_music = $wpdb->prefix . "oqey_music";
   //$oqey_music_rel = $wpdb->prefix . "oqey_music_rel";
   $oqey_skins = $wpdb->prefix . "oqey_skins";
   
//$id = mysql_real_escape_string($gasite[1]);
$oqey_BorderSize = get_option('oqey_BorderSize');
$oqey_height = get_option('oqey_height');
$oqey_width = get_option('oqey_width');
$oqey_bgcolor = get_option('oqey_bgcolor');
$plugin_url_qu = oQeyPluginUrl();
$plugin_repo_url = oQeyPluginRepoUrl();

$gal = $wpdb->get_row("SELECT * FROM $oqey_galls WHERE id ='".$id."' AND status !='2'");

if($gal){ // if gallery still exist

$folder = $gal->folder;

if($gal->skin_id!="0"){
$skin = $wpdb->get_row("SELECT * FROM $oqey_skins WHERE id = '".$gal->skin_id."'");
}else{
$skin = $wpdb->get_row("SELECT * FROM $oqey_skins WHERE status = '1'");
}

if($gal->splash_only==1){ $s = "AND id!=".$gal->splash_img; }else{ $s=""; }

           $all = $wpdb->get_results("SELECT *
                                        FROM $oqey_images
                                       WHERE gal_id = '".$id."'
									     AND status!=2
									         ".$s."
									ORDER BY img_order ASC
									 ");
/*
	  //verifica daca este muzica setata la galerie
	 $m = $wpdb->get_results("SELECT * FROM $oqey_music_rel WHERE gallery_id = '".$id."' " );
	 if(count($m)>0){  
	  $fsong = $wpdb->get_row("SELECT link FROM $oqey_music WHERE id ='".$m[0]->music_id."'");
      $song = get_option('siteurl').'/wp-content/oqey_gallery/music/'.$fsong->link;
	 }
*/
define('IBROWSER', preg_match('~(iPad|iPod|iPhone)~si', $_SERVER['HTTP_USER_AGENT'])); //check for apple devices
if(IBROWSER){
$gimg = get_option('siteurl').'/wp-content/oqey_gallery/galleries/'.$gal->folder.'/iphone/';
}else{
$gimg = get_option('siteurl').'/wp-content/oqey_gallery/galleries/'.$gal->folder.'/galimg/';	
}

$imgs = '<span class="all_images">';

foreach($all as $i){ 
$imgs .= '<span class"single_img"><img src="'.$gimg.trim($i->title).'" alt="'.trim($i->alt).'"/><\/span>';
}		 
$imgs .= '<\/span>'; 

$isCrawler = getUserNow($_SERVER['HTTP_USER_AGENT']); // check if is a crowler

if ($isCrawler) {
	echo str_replace("\\", "", $imgs);
    }else{

ob_start();	
print <<< SWF
<span id="image{$counter}" style="display:none;"></span>

<script type="text/javascript">
    var flashvars{$counter} = {
		             flashId:"{$counter}",
		             FKey:"{$skin->comkey}",
	                 GalleryPath:"{$plugin_url_qu}",	
					 GalleryID:"{$id}",
					 FirstRun:"{$skin->firstrun}"
					 };
	var params{$counter} = {bgcolor:"{$oqey_bgcolor}", allowFullScreen:"true", wMode:"transparent"};
	var attributes{$counter} = {id: "oqeygallery{$counter}"};
	swfobject.embedSWF("{$plugin_repo_url}/skins/{$skin->folder}/{$skin->folder}.swf", "flash_gal_{$counter}", "{$oqey_width}", "{$oqey_height}", "8.0.0", "", flashvars{$counter}, params{$counter}, attributes{$counter});
</script> 
<span id="flash_gal_{$counter}" style="min-height:{$oqey_height}px; height:{$oqey_height}px; margin-bottom:5px; text-align:center;">

<script type="text/javascript">
jQuery(function($) {	
		var playerVersion = swfobject.getFlashPlayerVersion();
		if(playerVersion.major<8){	
		jQuery.noConflict();
		jQuery("#image{$counter}").show().html(decodeURIComponent('{$imgs}'.replace(/\+/g, '%20')));	
		jQuery("#image{$counter} img").lazyload();
	    }
});
</script>
</span>&nbsp;
SWF;
	$output = ob_get_contents();
	ob_end_clean();
$counter ++;

	return $output;
   }
}//end crowler check
}
}
?>