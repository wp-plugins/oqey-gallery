<?php
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'gallcore.php' == basename($_SERVER['SCRIPT_FILENAME'])) die ('Please do not load this page directly. Thanks!');
$counter = 1;
$d=0;

function addoQeyMediaIcon($context){
	$qgall_upload_iframe_src = "media-upload.php?type=oqeygall&amp;post_id=$qgall_iframe_ID";
	$qgall_iframe_src = apply_filters('qgall_iframe_src', "$qgall_upload_iframe_src&amp;tab=qgall");
	$qgall_title = __('Add oQey Gallery');
	$qgall_button_src = oQeyPluginUrl().'/images/oqeyinsert.png';
    return $context.'<a href="'.$qgall_upload_iframe_src.'&amp;TB_iframe=true&amp;height=500&amp;width=640" class="thickbox" title="'.$qgall_title.'"><img src="'.$qgall_button_src.'" alt="'.$qgall_title.'" /></a>';
}
add_filter('media_buttons_context', 'addoQeyMediaIcon');

function oQeyPluginUrl() {
	$url = get_option('siteurl') . '/wp-content/plugins/oqey-gallery';   
	return $url;
}

function oQeyPluginRepoUrl() {
	$url = get_option('siteurl') . '/wp-content/oqey_gallery';   
	return $url;
}
oqeygall_tab_content(); // start tab content

function oqeygall_tab_content(){
    global $post_ID, $temp_ID, $wpdb;
	if($_GET['type'] == "oqeygall"){ include ("insert_in_post.php"); }
}

add_action('admin_menu', 'oqey_add_pages');
function oqey_add_pages() {
	$icon = oQeyPluginUrl().'/images/oqeyadminicon.png';
    add_menu_page('oQey Gallery plugin', 'oQey Gallery', 8, __FILE__, 'oqey_top_page', $icon);	  
    
	add_submenu_page(__FILE__,'oQey Gallery plugin', 'Settings', 'oQeySettings', 'oQeysettings',  'oqey_settings_page');
	//add_submenu_page(__FILE__,'oQey-Gallery plugin', 'Categories', 8, 'oQeyCategories',  'oqey_categories_page');
    add_submenu_page(__FILE__, 'Galleries', 'Galleries', 'oQeyGalleries', 'oQeyGalleries', 'oqey_galleries_page');
	add_submenu_page(__FILE__,'Skins', 'Skins', 'oQeySkins', 'oQeySkins',  'oqey_galleries_skin_page');
	add_submenu_page(__FILE__, 'Music', 'Music', 'oQeyMusic', 'oQeyMusic', 'oqey_music_page');
    add_submenu_page(__FILE__, 'Roles', 'Roles', 'oQeyRoles', 'oQeyRoles', 'oqey_roles_page');
	add_submenu_page(__FILE__, 'Trash', 'Trash', 'oQeyTrash', 'oQeyTrash', 'oqey_trash_page');	

}


function oqey_top_page() {
?>
<div class="wrap">
        	<h2>oQey Gallery plugin</h2>
</div>
<div class="wrap">		  
          
<div class="postbox" style="width:850px; padding:10px;">
<strong>About</strong>
<p>oQey Gallery is a Plugin for Wordpress that let users create and manage flash slideshows with a non-flash version of gallery built-in for all non-flash browsers and mobile devices like iPhone / iPad / iPod etc. The flash version supports music and skins, so users can change the way it looks with a few simple clicks. Customizable, commercial skins are also available as well as custom galleries for professionals. Check this out on <a href="http://oqeysites.com/" target="_blank">oqeysites.com</a></p>
<a href="http://oqeysites.com"><img style="border: none;" src="<?php echo oQeyPluginUrl(); ?>/images/oqeybanner.jpg" /></a>
</div>

<div class="postbox" style="width:850px; padding:10px; height:85px;">
<div align="left"><strong>Donate</strong><p>If you really like this plugin and find it useful, help to keep this plugin free and constantly updated by clicking the donate button below.</p></div>
<div align="right">
<form action="https://www.paypal.com/cgi-bin/webscr" method="post"><input type="hidden" name="cmd" value="_s-xclick"><input type="hidden" name="hosted_button_id" value="3ZV8CCFYAUYKJ"><input alt="PayPal - The safer, easier way to donate online!" name="submit" src="<?php echo oQeyPluginUrl(); ?>/images/btn_donate.gif" type="image"/><img src="https://www.paypal.com/en_US/i/scr/pixel.gif" alt="" width="1" border="0" height="1"/></form>
</div>
</div>

<div class="postbox" style="width:850px; padding:10px;">
	<strong>Original WP Themes</strong>
	<p>If you are interested to buy an original WP theme, oQey Sites recommends the following themes. They have a cool 1 click auto install feature and excellent after care support services. Check it out!</p>
    <div align="right"><a href="https://www.e-junkie.com/ecom/gb.php?ii=850762&c=ib&aff=155824&cl=136641" target="ejejcsingle"><img style="border:none;" src="<?php echo oQeyPluginUrl(); ?>/images/themes.jpg" /></a>
    </div>
</div>

</div>
<?php
}

function oqey_galleries_page(){ include("managegal.php"); }
function oqey_galleries_skin_page(){ include("manageskins.php"); }
function oqey_music_page(){ include("managemusic.php"); }
function oqey_trash_page(){ include("managetrash.php"); }
function oqey_roles_page(){ include("manageroles.php"); }
function oqey_settings_page(){ include("managesettings.php"); }

function gallery_embed($content){
if (is_feed()) {
add_shortcode( 'oqeygallery', 'add_oqey_gallery_feed' );
}else{
add_shortcode( 'oqeygallery', 'add_gallery' );
}
return $content;
}

function add_oqey_gallery_feed($content){
$content = " [See the post to view the galleries] ";
return $content;
}

function oqey_gallery_front_head(){
   
   $applejs = WP_PLUGIN_URL . '/oqey-gallery/js/oqey-js-drag-iphone.js';
   echo '<script type="text/javascript" src="' . $applejs . '"></script>';
   echo "\n";
   
   $oqeyjs = WP_PLUGIN_URL . '/oqey-gallery/js/oqey.js';
   echo '<script type="text/javascript" src="' . $oqeyjs . '"></script>';
   echo "\n";
}

add_action('wp_head', 'oqey_gallery_front_head');

function getUserNow($userAgent) {
    $crawlers = 'Google|msnbot|Rambler|Yahoo|AbachoBOT|accoona|' .
    'AcioRobot|ASPSeek|CocoCrawler|Dumbot|FAST-WebCrawler|' .
    'GeonaBot|Gigabot|Lycos|MSRBOT|Scooter|AltaVista|IDBot|eStyle|Scrubby|yandex';
    $isCrawler = (preg_match("/$crawlers/i", $userAgent) > 0);
    return $isCrawler;
}

function oQeycrawlerFood($t){
	$t = str_replace("oqeyimg", "oQey Photo Gallery Plugin for WordPress -", urldecode($t) );
	$t = $t."<p align='center'><a href='http://oqeysites.com'>oQey Photo Gallery Plugin for WordPress</a></p>";
	return $t;
}

add_shortcode( 'oqeygallery', 'add_gallery' );

function add_gallery($atts){
global $counter, $post_ID, $wpdb;
if($atts['width']!=""){ $oqey_width = $atts['width']; }else{ $oqey_width = get_option('oqey_width'); }
if($atts['height']!=""){ $oqey_height = $atts['height']; }else{ $oqey_height = get_option('oqey_height'); }

if($atts['autoplay']!=""){ $oqey_autoplay = $atts['autoplay']; }else{ $oqey_autoplay = "false"; }

foreach($atts as $a){    
   $id = str_replace(":", "", $atts['id']);
   $oqey_galls = $wpdb->prefix . "oqey_gallery";
   $oqey_images = $wpdb->prefix . "oqey_images";
   $oqey_skins = $wpdb->prefix . "oqey_skins";
   
$oqey_BorderSize = get_option('oqey_BorderSize');
$oqey_bgcolor = get_option('oqey_bgcolor');
$plugin_url_qu = oQeyPluginUrl();
$plugin_repo_url = oQeyPluginRepoUrl();

$gal = $wpdb->get_row("SELECT * FROM $oqey_galls WHERE id ='".$id."' AND status !='2'");

if($gal){
$folder = $gal->folder;
$gal_title = urlencode($gal->title);

if($gal->skin_id!="0"){ $skin = $wpdb->get_row("SELECT * FROM $oqey_skins WHERE id = '".$gal->skin_id."'");
}else{ $skin = $wpdb->get_row("SELECT * FROM $oqey_skins WHERE status = '1'"); }

if($gal->splash_only==1){ $s = "AND id!=".$gal->splash_img; }else{ $s=""; }
$all = $wpdb->get_results("SELECT * FROM $oqey_images WHERE gal_id = '".$id."' AND status!=2 ".$s." ORDER BY img_order ASC");

define('IBROWSER', preg_match('~(iPad|iPod|iPhone)~si', $_SERVER['HTTP_USER_AGENT']));
if(IBROWSER){
$gimg = get_option('siteurl').'/wp-content/oqey_gallery/galleries/'.oqey_getBlogFolder($wpdb->blogid).$gal->folder.'/iphone/';
}else{
$gimg = get_option('siteurl').'/wp-content/oqey_gallery/galleries/'.oqey_getBlogFolder($wpdb->blogid).$gal->folder.'/galimg/';	
}

$isCrawler = getUserNow($_SERVER['HTTP_USER_AGENT']); // check if is a crowler

if ($isCrawler){
	$imgs = "<p align='center'>".$gal_title."</p>";
    $imgs .= '<span class="all_images">';	foreach($all as $i){   $imgs .= '<img src="'.$gimg.trim($i->title).'" alt="oqeyimg '.urlencode(trim($i->alt)).'"/>'; } $imgs .= '</span>'; echo oQeycrawlerFood($imgs);
}else{	
    $ipath = OQEY_ABSPATH.'wp-content/oqey_gallery/galleries/'.oqey_getBlogFolder($wpdb->blogid).$gal->folder.'/iphone/';
	
	if(get_option('oqey_gall_title_no')=="on"){
	$galtitle = '<div style="margin-left:auto; margin-right:auto; width:100%; text-align:center;">'.urldecode($gal_title).'</div>';
	}else{ $galtitle =""; }
	//$imgs .= '[span class="all_images"]'; 
	$allimgs = array();
	
	foreach($all as $i){ 
	$img_path = $ipath.trim($i->title);
    list($iwidth, $iheight, $itype, $iattr)= getimagesize($img_path);
	$coeficient = $iwidth/$iheight;	
	if($coeficient>1){
	$new_height = round($oqey_width/$coeficient);
	}else{
	$new_height = round($oqey_width*$coeficient);
	}
	//echo ($iwidth/$iheight);
	$img_full_root = get_option('siteurl').'/wp-content/plugins/oqey-gallery/oqeyimgresize.php?width='.$oqey_width.'&amp;new_height='.$new_height.'&amp;folder='.$gal->folder.'&amp;img='.trim($i->title);
	
	//$allimgs[] = $img_full_root;
	
	$imgs .= '[div class="oqeyimgdiv" style="background: url('.$img_full_root.') center no-repeat; width:'.$oqey_width.'px; height:'.$new_height.'px; margin-top:3px;"][/div]';
	
	//$imgs .= '[img src="'.get_option('siteurl').'/wp-content/plugins/oqey-gallery/oqeyimgresize.php?width='.$oqey_width.'&amp;folder='.$gal->folder.'&amp;img='.trim($i->title).'" alt="'.urlencode(trim($i->alt)).'"/]'; 
	}	
	//$imgs .= '[a href="#prev" class="prev"]prev[/a][a href="#next" class="next"]next[/a]'; 
	//$imgs .= '[/span]';

	if(get_option("oqey_backlinks")=="on"){ 
	$oqeybacklink = '<div style="font-size:11px; margin-left:auto; margin-right:auto; width:100%; text-align:center;">powered by &copy; <a href="http://oqeysites.com" target="_blank">oQeySites</a></div>'; 
	}	
	
	if( get_option('oqey_noflash_options')=="incolums" ){  
	$incolums = "on";
	$optouch = "off"; 
	}
	if( get_option('oqey_noflash_options')=="injsarr" ){ 
	$incolums = "off"; 
	$optouch = "off"; 	
	}
	if( get_option('oqey_noflash_options')=="injsarrtouch" ){ 
	$incolums = "off"; 
	$optouch = "on";                    
	}
	
	$margleft = $oqey_width - 70;
ob_start();	
print <<< SWF
<div id="oqey_image_div{$counter}" style="position:relative; width:{$oqey_width}px; display:none;">
<div style="position:absolute; left:0px; top:45%; z-index:99999;" class="gall_links">
<a id="prev{$counter}" href="#" style="text-decoration:none;"><img src="{$plugin_url_qu}/images/larrow.png" style="border:none;" alt="" /></a></div>
<div style="position:absolute; left:{$margleft}px; top:45%; z-index:99999;" class="gall_links">
<a id="next{$counter}" href="#" style="text-decoration:none;"><img src="{$plugin_url_qu}/images/rarrow.png" style="border:none;" alt="" /></a></div>
{$galtitle}
<div id="image{$counter}" style="height:auto; display:none;" class="oqey_images"></div>
{$oqeybacklink}
</div>
<script type="text/javascript">
    var flashvars{$counter} = {
                     autoplay:"{$oqey_autoplay}",
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
		var pv = swfobject.getFlashPlayerVersion();
		oqey_e(pv, {$counter}, '{$imgs}', '{$optouch}', '{$incolums}');
});
</script>
</span>&nbsp;
SWF;
$output = ob_get_contents();
ob_end_clean();
$counter ++;
return $output;
}
}//end crawler check
}
}
?>