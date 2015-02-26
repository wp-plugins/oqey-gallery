<?php
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'gallcore.php' == basename($_SERVER['SCRIPT_FILENAME'])) die ('Please do not load this page directly. Thanks!');

global $oqeycounter;
$oqeycounter = 1;
 
 if(is_admin()){
    
    require ("oqey-ajax.php");
 
 }
 
add_action('delete_post','oQeyCleanGalleries');
function oQeyCleanGalleries( $postid ){
   global $wpdb;   
   
   $oqey_images = $wpdb->prefix . "oqey_images";
   
   $wpdb->query( $wpdb->prepare( "DELETE FROM $oqey_images WHERE meta_data = %d AND img_type = %s ", $postid, 'wp' ) );
    
}
 
add_action( 'widgets_init', 'oqey_load_widgets' );

function oqey_load_widgets() {
	register_widget( 'oQey_Gallery_Widget' );
}

class oQey_Gallery_Widget extends WP_Widget {

	/**
	 * Widget setup.
	 */
	function oQey_Gallery_Widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'oqeygallery', 'description' => __('Show oQey Gallery Slideshow', 'oqeygallery') );

		/* Create the widget. */
		$this->WP_Widget( 'oqey-gallery-widget', __('Oqey Widget', 'oqey-gallery'), $widget_ops ); 
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );
        
        global $wpdb,$oqeycounter;
        
        wp_enqueue_script( 'cycle2' );
        wp_enqueue_script( 'cycle2.swipe' );
        wp_enqueue_script( 'oqeyjs' );
        
        $oqey_galls  = $wpdb->prefix . "oqey_gallery";
        $oqey_skins  = $wpdb->prefix . "oqey_skins";
        $oqey_images = $wpdb->prefix . "oqey_images";
        $folder      = '';
        $autoplay    = '';

        $gal = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $oqey_galls WHERE id = %d ", absint($instance['oqeygalleryid']) ));
        if($gal){
        
        $folder = $gal->folder;
    
        if($gal->skin_id!="0"){
            
           $skin = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $oqey_skins WHERE id = %d", absint($gal->skin_id) ) );
        
        }else{
        
           $skin = $wpdb->get_row("SELECT * FROM $oqey_skins WHERE status = '1'");
        
        }
        
        if($gal->splash_only==1){ 
            
            $s = "AND id!=".$gal->splash_img; 
            
        }else{ 
            
            $s=""; 
            
        }      
        
        $all             = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $oqey_images 
                                                                        WHERE gal_id = %d 
                                                                          AND status!=2 ".$s." ORDER BY img_order ASC", absint($instance['oqeygalleryid']) 
                                                             )
                                              );
        
        $oqey_bgcolor    = get_option('oqey_bgcolor');
        $plugin_url      = oQeyPluginUrl();
        $plugin_repo_url = oQeyPluginRepoUrl();
        $autoplay        = "false";
        $nobject         = '';
        $object          = '';
        $incolums        = '';
        $oqeyblogid      = '';
        $arrowleftright  = "";
        $arrowshtml      = "";
        $backlink        = "";  
        $arrows          = "on";
        $oqeybgcss       = '';
        $autostart       = '';        
        $id              = trim($instance['oqeygalleryid']);
                      
        $title = apply_filters('widget_title', $instance['title'] );
                
        echo $before_widget;

		if ( $title )
			echo $before_title . $title . $after_title;
   
   /*images array*/     
   
    if(isset( $instance['width'] ) ){          $oqey_width     = $instance['width']; }else{          $oqey_width     = get_option('oqey_width'); }
    if(isset( $instance['height'] ) ){         $oqey_height    = $instance['height']; }else{         $oqey_height    = get_option('oqey_height'); }
    if(isset( $instance['autoplay'] ) ){       $oqey_autoplay  = $instance['autoplay']; }else{       $oqey_autoplay  = "false"; }
    if(isset( $instance['arrows'] ) ){         $arrows         = $instance['arrows']; }else{         $arrows         = 'on'; }
    if(isset( $instance['oqeywidgettext'] ) ){ $oqeywidgettext = $instance['oqeywidgettext']; }else{ $oqeywidgettext = ''; }
   
    
    /* Custom words - set arrows ON/OFF */ 
    
    if( get_option('oqey_noflash_options')=="incolums" ){  
	   
        $incolums = "on";
        $optouch  = ""; 
    }
    
    if($arrows=="off"){
        
        $arrowleftright = "";
        $arrowshtml     = "";
    
    }else{
        
        $arrowleftright = ' data-cycle-prev=".prevControl'.$oqeycounter.'" data-cycle-next=".nextControl'.$oqeycounter.'"'; 
        if($incolums!="on"){
        $arrowshtml    .= '<span class=center>';
        $arrowshtml    .= '<span class="prevControl prevControl'.$oqeycounter.'"></span>';
        $arrowshtml    .= '<span class="nextControl nextControl'.$oqeycounter.'"></span>';
        $arrowshtml    .= '</span>';
        }
    } 
    
    if( get_option('oqey_noflash_options')=="injsarr" ){ 
        
        $optouch  = "";
        $optouch  = " data-cycle-swipe=true";
	
    }
	
    //if( get_option('oqey_noflash_options')=="injsarrtouch" ){ 

      //  $optouch  = " data-cycle-swipe=true";             
    
    //}
        
    if(get_option('oqey_flash_gallery_true')){ 
            
        $pfv      = "on"; 
                        
    }else{
            
        $oqey_skins = $wpdb->prefix . "oqey_skins";
        $r          = $wpdb->get_results( "SELECT skinid FROM $oqey_skins WHERE status !='2'");  
        
        if(empty($r)){
               $pfv = "on"; 
        }else{     
               $pfv = "off"; 
        }
    }
    
    /*Border details*/
    $Border          = get_option('oqey_BorderOption');
        
    if($Border){
        
        //$oqeyBorderColor = get_option('oqey_BorderSize');
        $oqeyBorderColor = get_option('oqey_border_bgcolor');
        $oqeybgcss       = 'border:thin solid '.$oqeyBorderColor.';';
        
    }    
    /*END border*/
    
     /*Autostart details*/
            
    if($instance['autostart']==1){

        $autostart     = ' data-cycle-manual-speed="'.(get_option('oqey_effects_trans_time')*1000).'" data-cycle-timeout='.(get_option('oqey_pause_between_tran')*1000);
        $oqey_autoplay = "true";
        
    }else{
        
        $autostart     = ' data-cycle-manual-speed="'.(get_option('oqey_effects_trans_time')*1000).'" data-cycle-timeout=0';
        $oqey_autoplay = "false"; 
    }  
    /*END autostart*/
    
    $nobject .= '<div class="oqeyslider" style="background:'.$oqey_bgcolor.';'.$oqeybgcss.'width:'.$oqey_width.'px;height:'.$oqey_height.'px">';
    $nobject .= '<div class="oqey-slideshow cycle-slideshow'.$oqeycounter.'" ';
    
    if($incolums!="on"){
      
      $nobject .=' data-cycle-loader=true data-cycle-progressive="#slides'.$oqeycounter.'"'.$autostart.''.$optouch.''.$arrowleftright.' data-cycle-slides=">div,>img"';
    }
    
    $nobject .= '>';
    $nobject .= $arrowshtml;
    $d        = 0;
    $nobject2 = '';
       
   foreach($all as $i){ 
       
     if($i->img_type!="video" ){      
	   
      $image = oQeyGetImageLink($i, $gal);

	if($d<1 || get_option('oqey_noflash_options')=="incolums"){
                $nobject .= '<div style="margin:0 auto 5px auto;"><img src="'.$image['img'].'" title="'.$image['comments'].'" style="max-width:100%;"/></div>'."\n";
            }else{
                $nobject2 .='<div><img src="'.$image['img'].'" title="'.$image['comments'].'" style="max-width:100%;"/></div> --- '."\n";
         }
           
           
    }
    
    $d++;
    
    }
    
    $nobject  .= '</div>';
    
    if(get_option('oqey_noflash_options')!="incolums"){
    $nobject .='<script id="slides'.$oqeycounter.'" type="text/cycle" data-cycle-split="---">'."\n";
    $nobject .= $nobject2; 
    $nobject .='</script>'."\n";
    }
    
    $nobject .= '</div>';
    
       if($pfv!="on"){ // if Do not use Flash skins is active
       
       $object .= '<script type="text/javascript">'."\n";
       $object .= 'var flashvars'.$oqeycounter.' ={'."\n";
       $object .= 'autoplay:"'.$oqey_autoplay.'",'."\n";
       $object .= 'flashId:"'.$oqeycounter.'",'."\n";
       $object .= 'FKey:"'.trim($skin->comkey).'",'."\n";
       $object .= 'GalleryPath:"'.$plugin_url.'",'."\n";
       $object .= 'GalleryID:"'.$id.'-0",'."\n";
       $object .= 'FirstRun:"'.trim($skin->firstrun).'"'."\n";
       $object .= '};'."\n";
       $object .= 'var params'.$oqeycounter.'     = {bgcolor:"'.$oqey_bgcolor.'", allowFullScreen:"true", wMode:"transparent"};'."\n";
       $object .= 'var attributes'.$oqeycounter.' = {id: "oqeygallery'.$oqeycounter.'"};'."\n";
       $object .= 'swfobject.embedSWF("'.$plugin_repo_url.'/skins/'.$oqeyblogid.trim($skin->folder).'/'.trim($skin->folder).'.swf", "flash_gal_'.$oqeycounter.'", "'.$oqey_width.'", "'.$oqey_height.'", "8.0.0", "", flashvars'.$oqeycounter.', params'.$oqeycounter.', attributes'.$oqeycounter.');'."\n";
       $object .= '</script>'."\n";

   }

//$object .= $galtitle;
$object .= '<div id="flash_gal_'.$oqeycounter.'" style="margin: 0 auto;">'."\n";
$object .= $nobject."\n";
$object .= '</div>'."\n";
$object .= $backlink;

if($incolums!="on"){
  $object .= '<script type="text/javascript">'."\n";
  $object .= 'jQuery(document).ready(function(){'."\n";
  $object .= 'jQuery(".cycle-slideshow'.$oqeycounter.'").css("min-height", (jQuery(".cycle-slideshow'.$oqeycounter.'").width()/1.5));';
  $object .= 'jQuery(".cycle-slideshow'.$oqeycounter.' div img").css("max-height", (jQuery(".cycle-slideshow'.$oqeycounter.'").width()/1.5));';
  $object .= 'jQuery(".cycle-slideshow'.$oqeycounter.'").cycle();'."\n";
  $object .= '});'."\n";
  $object .= 'jQuery(".cycle-slideshow'.$oqeycounter.'").on("cycle-before", function( event, opts ) {'."\n";
  $object .= 'jQuery(".cycle-slideshow'.$oqeycounter.' div img").css("max-height", (jQuery(".cycle-slideshow'.$oqeycounter.'").width()/1.5));';
  $object .= '});'."\n";
  $object .= '</script>'."\n";
}
         echo $object;
         
         $oqeycounter ++;         

         echo '<div class="textwidget">'.$oqeywidgettext.'</div>';
         
          if(isset($after_widget)){
            
				echo $after_widget;
		  
          }
          
        }
}
	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance                   = $old_instance;		
		$instance['title']          = strip_tags($new_instance['title']);
                $instance['oqeygalleryid']  = (int) $new_instance['oqeygalleryid'];
		$instance['height']         = (int) $new_instance['height'];
		$instance['width']          = (int) $new_instance['width'];
                $instance['autostart']      = (bool) $new_instance['autostart'];
                $instance['oqeywidgettext'] = $new_instance['oqeywidgettext'];        
		return $instance;        
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {
        global $wpdb;
        $oqey_galls = $wpdb->prefix . "oqey_gallery";
        
        /* Set up some default widget settings. */
        $instance        = wp_parse_args( (array) $instance, array( 'title' => 'Oqey Gallery', 'oqeygalleryid' => '0', 'height' => '140', 'width' => '210') );
        $title           = esc_attr( $instance['title'] );
        $height          = esc_attr( $instance['height'] );
        $width           = esc_attr( $instance['width'] );
        $oqeywidgettext  = '';
        $autostart       = '';
        
        if(isset($instance['autostart'])){
           $autostart    = esc_attr( $instance['autostart'] );
        }
        if(isset($instance['oqeywidgettext'])){
           $oqeywidgettext  = esc_textarea($instance['oqeywidgettext']);
        }
        $gals            = $wpdb->get_results("SELECT * FROM $oqey_galls WHERE status!=2 ORDER BY id ASC ");
?>
	<p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('galleryid'); ?>"><?php _e('Select Gallery:', 'oqey-gallery'); ?></label>
            <select size="1" name="<?php echo $this->get_field_name('oqeygalleryid'); ?>" id="<?php echo $this->get_field_id('oqeygalleryid'); ?>" class="widefat">
                <option value="0" <?php if (0 == $instance['oqeygalleryid']) echo "selected='selected' "; ?> ><?php _e('All galleries', 'oqey-gallery'); ?></option>
                    <?php if($gals){
                             foreach($gals as $gal){
                                 echo '<option value="'.$gal->id.'" ';
                                 if ($gal->id == $instance['oqeygalleryid']) echo "selected='selected' ";
                                 echo '>'.trim($gal->title).'</option>'."\n\t";
                             }
                          }
                    ?>
            </select>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('width')?>"><?php _e('Width:', 'oqey-gallery'); ?></label> 
            <input id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width')?>" type="text" style="padding:3px;width:45px;" value="<?php echo $width?>" /> px
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('height')?>"><?php _e('Height:', 'oqey-gallery'); ?></label>
            <input id="<?php echo $this->get_field_id('height')?>" name="<?php echo $this->get_field_name('height')?>" type="text" style="padding:3px;width:45px;" value="<?php echo $height?>" /> px
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('autostart')?>"></label>
            <input id="<?php echo $this->get_field_id('autostart')?>" name="<?php echo $this->get_field_name('autostart')?>" type="checkbox" value="1" <?php checked(true , $autostart); ?> />
                <?php _e('Autostart','oqeygallery'); ?>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('oqeywidgettext')?>"><?php _e('Few words:', 'oqey-gallery'); ?></label>
            <textarea id="<?php echo $this->get_field_id('oqeywidgettext')?>" name="<?php echo $this->get_field_name('oqeywidgettext')?>" style="width:100%;"><?php echo trim($oqeywidgettext)?></textarea>
        </p>		
        <?php }
}
/*End Widget*/


function oQeyPluginUrl() {
	$url = WP_PLUGIN_URL.'/oqey-gallery';   
	return $url;
}

function oQeyPluginRepoUrl() {
	$url = WP_CONTENT_URL.'/oqey_gallery';   
	return $url;
}

function oQeyPluginRepoPath() {
	$path = WP_CONTENT_DIR . '/oqey_gallery';   
	return $path;
}

add_action('admin_menu', 'oqey_add_pages');


function oqey_gallery_add_upload_tab( $tabs ) {
    $newtab = array( 'tab_slug' => 'Insert oQey Gallery' );
    return array_merge( $tabs, $newtab );
}
add_filter( 'media_upload_tabs', 'oqey_gallery_add_upload_tab' );


function oqey_gallery_media_upload() {
   global $wpdb;
   
   //if(!$_SESSION){ session_start(); }
   $oqey_galls  = $wpdb->prefix . "oqey_gallery";
   $oqey_images = $wpdb->prefix . "oqey_images";
   $gals        = $wpdb->get_results("SELECT * FROM $oqey_galls WHERE status!=2 ORDER BY id DESC");
  
   _e('*just click on picture to add the gallery', 'oqey-gallery');
   ?>
<style>
#oqey-attachmentsq{ overflow-y: scroll; width: 100%; }
.attachmentq {
	position: relative;
	float: left;
	margin: 10px 10px 0 0;
	color: #464646;
	cursor: pointer;
	list-style: none;
	text-align: center;
	-webkit-user-select: none;
	-moz-user-select: none;
	-ms-user-select: none;
	user-select: none;
	width: 150px;
        height:101px;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
}

.attachmentq thumbnail{
overflow: hidden;
position: absolute;
top: 0;
right: 0;
bottom: 0;
left: 0;
opacity: 1;
-webkit-transition: opacity .1s;
transition: opacity .1s; }
.attachmentq .thumbnail img {
	top: 0;
	left: 0;
        max-width: 150px;
        max-height: 100px;
}
.attachmentq .thumbnail .centered {
	width: 100%;
	height: 100%;
	-webkit-transform: translate( 50%, 50% );
	-ms-transform: translate(50%,50%); /* Fails with spaces?? Weird! */
	transform: translate( 50%, 50% );
}
.attachmentq .thumbnail .centered img {
	-webkit-transform: translate( -50%, -50% );
	-ms-transform: translate(-50%,-50%);
	transform: translate( -50%, -50% );
}
.attachmentq .filename {
	position: absolute;
	left: 0;
	right: 0;
	bottom: 0;
	overflow: hidden;
	max-height: 100%;
	word-wrap: break-word;
	text-align: center;
	font-weight: bold;
	background: rgba( 255, 255, 255, 0.8 );
	-webkit-box-shadow: inset 0 0 0 1px rgba( 0, 0, 0, 0.15 );
	box-shadow: inset 0 0 0 1px rgba( 0, 0, 0, 0.15 );
}
.attachment-preview {
	position: relative;
	-webkit-box-shadow:
		inset 0 0 15px rgba( 0, 0, 0, 0.1 ),
		inset 0 0 0 1px rgba( 0, 0, 0, 0.05 );
	box-shadow:
		inset 0 0 15px rgba( 0, 0, 0, 0.1 ),
		inset 0 0 0 1px rgba( 0, 0, 0, 0.05 );
	background: #eee;
	cursor: pointer;
}
   </style>
   
<?php
   echo '<div id="oqey-attachments">';
   
   if(!empty($gals)){
       
       foreach ($gals as $i){
           
           $images = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $oqey_images WHERE gal_id= %d AND status!=2", absint($i->id) ));
           
           if(!empty($images)){
               
               if($i->splash_img!=0){
                   
                   $img = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $oqey_images WHERE id= %d ", absint($i->splash_img) ) ); 
                   
                   if(empty($img)){
                       $img = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $oqey_images WHERE gal_id = %d AND status!= %d AND img_type != %s ORDER BY img_order ASC LIMIT 0,1 ", absint($i->id), "2", "video" ) );
                   }
                   
                }else{
                    
                    $img = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $oqey_images WHERE gal_id = %d AND status!= %d AND img_type != %s ORDER BY img_order ASC LIMIT 0,1 ", absint($i->id), "2", "video" ) );
      
                }
                
                $image = oQeyGetImageLink($img, $i);
                $thmb = (!empty($image['thmb']))?$image['thmb']:'';

                  echo '<div onclick="InsertOqeyGalleryInContent(\''.$i->id.'\')" class="attachmentq">';
		  echo '<div class="thumbnail">';
                  echo '<img src="'.$thmb.'" class="icon">';
                  echo '<div class="filename">';
                  echo '<div>'.trim($i->title).'</div>';
                  echo '</div>';
		  echo '</div>';
                  echo '</div>';
                                    
                }
                                
         }
         
    }else{ 
        
        echo '<div>';
        _e('There is no galleries found. Please create a gallery first.', 'oqey-gallery');
        echo '</div>'; 
        
    }

    echo '</div>';
?>
<script type="text/javascript">
function InsertOqeyGalleryInContent(id){
var win = window.dialogArguments || opener || parent || top;
var html = "[oqeygallery id=" + id + "]";
win.send_to_editor(html);
}
</script>
<?php
}
add_action( 'media_upload_tab_slug', 'oqey_gallery_media_upload' );

function oqey_add_pages() {
    $icon  = oQeyPluginUrl().'/images/oqeygallery.png';
    $oqeym = plugin_basename( dirname(__FILE__));
    
    add_menu_page('oQey Gallery plugin', 'oQey Gallery', 'oqey-gallery', $oqeym, 'oqey_top_page', $icon);	  
    add_submenu_page($oqeym,'oQey Gallery plugin', __('Settings', 'oqey-gallery'), 'oQeySettings', 'oQeysettings',  'oqey_settings_page');
    add_submenu_page($oqeym, 'Galleries', __('Galleries', 'oqey-gallery'), 'oQeyGalleries', 'oQeyGalleries', 'oqey_galleries_page');    
    add_submenu_page($oqeym, 'Video', __('Video', 'oqey-gallery'), 'oQeyVideo', 'oQeyVideo', 'oqey_video_page');    
    add_submenu_page($oqeym, 'Skins', __('Skins', 'oqey-gallery'), 'oQeySkins', 'oQeySkins', 'oqey_galleries_skin_page');
    add_submenu_page($oqeym, 'Music', __('Music', 'oqey-gallery'), 'oQeyMusic', 'oQeyMusic', 'oqey_music_page');
    add_submenu_page($oqeym, 'Roles', __('Roles', 'oqey-gallery'), 'oQeyRoles', 'oQeyRoles', 'oqey_roles_page');
    add_submenu_page($oqeym, 'Trash', __('Trash', 'oqey-gallery'), 'oQeyTrash', 'oQeyTrash', 'oqey_trash_page');	
}

function oqey_top_page(){
?>
<div class="wrap">
    <h2 style="width: 900px;"><?php _e('oQey Gallery plugin', 'oqey-gallery'); ?></h2>
</div>

<div class="metabox-holder has-right-sidebar">
										
<div class="has-sidebar sm-padded">
	<div id="post-body-content" class="has-sidebar-content">
	  <div class="meta-box-sortabless">
              
              <script type="text/javascript" src="//s3.amazonaws.com/downloads.mailchimp.com/js/signup-forms/popup/embed.js" data-dojo-config="usePlainJson: true, isDebug: false"></script><script type="text/javascript">require(["mojo/signup-forms/Loader"], function(L) { L.start({"baseUrl":"mc.us5.list-manage.com","uuid":"bbcd6e12a998117c48004dd73","lid":"57829f2a4b"}) })</script>
              
<div class="postbox">
    <h3 class="hndle"><span><?php _e('Subscribe and win', 'oqey-gallery'); ?></span></h3>
       <!-- Begin MailChimp Signup Form -->

<style type="text/css">
#mc_embed_signup{background:#fff; clear:left; font:12px Helvetica,Arial,sans-serif;  width:400px;}
#mc_embed_signup form {display:block; position:relative; text-align:left; padding:10px 0 10px 3%}
#mc_embed_signup h2 {font-weight:bold; padding:0; margin:15px 0; font-size:1.4em;}
#mc_embed_signup input {border:1px solid #999; -webkit-appearance:none;}
#mc_embed_signup input[type=checkbox]{-webkit-appearance:checkbox;}
#mc_embed_signup input[type=radio]{-webkit-appearance:radio;}
#mc_embed_signup input:focus {border-color:#333;}
#mc_embed_signup .button {clear:both; background-color: #bd4b4b; border: 0 none; border-radius:4px; color: #FFFFFF; cursor: pointer; display: inline-block; font-size:15px; font-weight: bold; height: 32px; line-height: 32px; margin: 0 5px 10px 0; padding: 0 22px; text-align: center; text-decoration: none; vertical-align: top; white-space: nowrap; width: auto;}
#mc_embed_signup .button:hover {background-color:#cf5858;}
#mc_embed_signup .small-meta {font-size: 11px;}
#mc_embed_signup .nowrap {white-space:nowrap;}

#mc_embed_signup .mc-field-group {clear:left; position:relative; width:96%; padding-bottom:3%; min-height:50px;}
#mc_embed_signup .size1of2 {clear:none; float:left; display:inline-block; width:46%; margin-right:4%;}
* html #mc_embed_signup .size1of2 {margin-right:2%; /* Fix for IE6 double margins. */}
#mc_embed_signup .mc-field-group label {display:block; margin-bottom:3px;}
#mc_embed_signup .mc-field-group input {display:block; width:100%; padding:8px 0; text-indent:2%;}
#mc_embed_signup .mc-field-group select {display:inline-block; width:99%; padding:5px 0; margin-bottom:2px;}

#mc_embed_signup .datefield, #mc_embed_signup .phonefield-us{padding:5px 0;}
#mc_embed_signup .datefield input, #mc_embed_signup .phonefield-us input{display:inline; width:60px; margin:0 2px; letter-spacing:1px; text-align:center; padding:5px 0 2px 0;}
#mc_embed_signup .phonefield-us .phonearea input, #mc_embed_signup .phonefield-us .phonedetail1 input{width:40px;}
#mc_embed_signup .datefield .monthfield input, #mc_embed_signup .datefield .dayfield input{width:30px;}
#mc_embed_signup .datefield label, #mc_embed_signup .phonefield-us label{display:none;}

#mc_embed_signup .indicates-required {text-align:right; font-size:11px; margin-right:4%;}
#mc_embed_signup .asterisk {color:#c60; font-size:200%;}
#mc_embed_signup .mc-field-group .asterisk {position:absolute; top:25px; right:10px;}        
#mc_embed_signup .clear {clear:both;}

#mc_embed_signup .mc-field-group.input-group ul {margin:0; padding:5px 0; list-style:none;}
#mc_embed_signup .mc-field-group.input-group ul li {display:block; padding:3px 0; margin:0;}
#mc_embed_signup .mc-field-group.input-group label {display:inline;}
#mc_embed_signup .mc-field-group.input-group input {display:inline; width:auto; border:none;}

#mc_embed_signup div#mce-responses {float:left; top:-1.4em; padding:0em .5em 0em .5em; overflow:hidden; width:90%;margin: 0 5%; clear: both;}
#mc_embed_signup div.response {margin:1em 0; padding:1em .5em .5em 0; font-weight:bold; float:left; top:-1.5em; z-index:1; width:80%;}
#mc_embed_signup #mce-error-response {display:none;}
#mc_embed_signup #mce-success-response {color:#529214; display:none;}
#mc_embed_signup label.error {display:block; float:none; width:auto; margin-left:1.05em; text-align:left; padding:.5em 0;}

#mc-embedded-subscribe {clear:both; width:auto; display:block; margin:1em 0 1em 5%;}
#mc_embed_signup #num-subscribers {font-size:1.1em;}
#mc_embed_signup #num-subscribers span {padding:.5em; border:1px solid #ccc; margin-right:.5em; font-weight:bold;}
</style>
<div id="mc_embed_signup">
<form action="//oqeysites.us5.list-manage.com/subscribe/post?u=bbcd6e12a998117c48004dd73&amp;id=57829f2a4b" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
    <div id="mc_embed_signup_scroll">
	<div><b>Subscribe and get a guaranteed discount</b></div><br>
<div class="mc-field-group">
	<label for="mce-NAME">Your name </label>
	<input type="text" value="" name="NAME" class="" id="mce-NAME">
</div>
<div class="mc-field-group">
	<label for="mce-EMAIL">Email Address </label>
	<input type="email" value="" name="EMAIL" class="required email" id="mce-EMAIL">
</div>
	<div id="mce-responses" class="clear">
		<div class="response" id="mce-error-response" style="display:none"></div>
		<div class="response" id="mce-success-response" style="display:none"></div>
	</div>    <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
    <div style="position: absolute; left: -5000px;"><input type="text" name="b_bbcd6e12a998117c48004dd73_57829f2a4b" tabindex="-1" value=""></div>
    <div class="clear"><input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="button"></div>
    </div>
</form>
</div>
<script type='text/javascript' src='//s3.amazonaws.com/downloads.mailchimp.com/js/mc-validate.js'></script><script type='text/javascript'>(function($) {window.fnames = new Array(); window.ftypes = new Array();fnames[2]='NAME';ftypes[2]='text';fnames[0]='EMAIL';ftypes[0]='email';}(jQuery));var $mcj = jQuery.noConflict(true);</script>
<!--End mc_embed_signup-->

</div>
       
      <div class="postbox">
          <h3 class="hndle"><span><?php _e('Donate', 'oqey-gallery'); ?></span></h3> 
          <div class="inside" style="font-size:13px;padding:10px auto;text-align:justify;">
              <div align="left"><p>If you really like this plugin and find it useful, help to keep this plugin free and constantly updated by clicking the donate button below.</p></div>
              <div align="right">
                  <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank"><input type="hidden" name="cmd" value="_s-xclick"/><input type="hidden" name="hosted_button_id" value="3ZV8CCFYAUYKJ"/><input alt="PayPal - The safer, easier way to donate online!" name="submit" src="<?php echo oQeyPluginUrl(); ?>/images/btn_donate.gif" type="image"/><img src="https://www.paypal.com/en_US/i/scr/pixel.gif" alt="" width="1" border="0" height="1"/></form>
              </div>
          </div>
      </div>
              
       <div class="postbox">
        <h3 class="hndle"><span><?php _e('News', 'oqey-gallery'); ?></span></h3>
	    <div class="inside" style="font-size:13px; padding:10px auto; text-align:justify;">        
        <p>
        <?php               
        $url = "http://oqeysites.com/updater/get-oqey-gallery-news.php";         	
        $response = wp_remote_post( $url, array(
	    'method' => 'POST',
	    'timeout' => 45,
	    'redirection' => 5,
	    'httpversion' => '1.0',
	    'blocking' => true,
	    'headers' => array('User-Agent' => 'oQeySitesNewsBot'),
	    'body' => array( 'domainurl' => get_option('siteurl') )
        )
        );        
        $code = (int) wp_remote_retrieve_response_code( $response );
        $body = wp_remote_retrieve_body( $response );
        if( $code == 200 ){
            if($body!=""){
                echo wpautop(urldecode($body));
            }
           }
        ?>
        </p>
       </div>
      </div>
        
      <div class="postbox">
          <h3 class="hndle"><span><?php _e('About', 'oqey-gallery'); ?></span></h3>
          <div class="inside" style="font-size:13px; padding:10px auto; text-align:justify;">
              <p>oQey Gallery is a premium grade plugin for managing images and video, creating photo slideshows with music, photo &amp; video galleries that works fine under iPhone / iPad and other mobile devices. Flash version of the slideshow is automatically replaced by the HTML5 | Java slideshow on a non-flash device. Flash gallery supports customizable skins, so you can change the way it looks with a few clicks using the skin options tool. Commercial skins are also available as well as custom built photo / video galleries and slideshows for professionals. This plugin uses built-in WP functions and a simple batch upload system. 
                   Check this out on <a href="http://oqeysites.com/" target="_blank">oqeysites.com</a></p>
              <p><a href="http://oqeysites.com"><img style="border: none;" src="<?php echo WP_PLUGIN_URL; ?>/oqey-gallery/images/oqeybanner.jpg" /></a></p>
          </div>
      </div>
              
      <div class="postbox">
          <h3 class="hndle"><span>WP Themes and Site templates</span></h3>
          <div class="inside" style="font-size:13px;text-align:justify;"> 
              <p>If you are interested to buy an original WP theme, oQey Sites recommends the following themes. Check it out!</p>
              <a href="http://themeforest.net/user/oqeysites/portfolio?ref=oqeysites" target="_blank"><img style="border:none;" src="<?php echo oQeyPluginUrl(); ?>/images/tf_728x90_v5.gif" width="728" height="90" /></a>
          </div>
      </div>
              
    </div>
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
function oqey_video_page(){ include("managevideo.php"); }

/*oqeygallery shortcode*/
add_shortcode( 'oqeygallery', 'AddoQeyGallery' );
add_shortcode( 'qgallery', 'AddoQeyGallery' );

function AddoQeyGalleryToFeed($atts){
    global $wpdb;
    
    $id          = esc_sql( $atts['id'] );    
    $oqey_galls  = $wpdb->prefix . "oqey_gallery";
    $oqey_images = $wpdb->prefix . "oqey_images";
    $oqey_skins  = $wpdb->prefix . "oqey_skins";
    $gal         = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $oqey_galls WHERE id = %d AND status !='2'", $id ));
    
    if($gal){
 
       if($gal->skin_id!="0"){ 
          $skin = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $oqey_skins WHERE id = %d", $gal->skin_id ));
       }else{ 
          $skin = $wpdb->get_row("SELECT * FROM $oqey_skins WHERE status = '1'"); 
       }
       
       if($gal->splash_only==1){ $s = "AND id!=".$gal->splash_img; }else{ $s=""; }
       
       $all = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $oqey_images WHERE gal_id = %d AND status!=2 ".$s." ORDER BY img_order ASC", $id  ));
       
       $imgs .= '<span class="all_images">';	
    
        foreach($all as $i){
	 
	 if($i->img_type!="video"){
            
          if($i->img_type=="nextgen"){
            
            $gimg = get_option('siteurl').'/'.trim($i->img_path).'/'.trim($i->title);
        
           }elseif($i->img_type=="wp"){
            
            $gimg = trim($i->title);
        
           }else{
        
            $gimg = oQeyPluginRepoUrl().'/galleries/'.oqey_getBlogFolder($wpdb->blogid).$gal->folder.'/galimg/'.trim($i->title);
        
           } 
	 
          $imgs .= '<p><img src="'.$gimg.'" alt="'.urlencode(trim($i->alt)).'" style="margin-top:3px;"/></p>'; 
         }
        } 
        $imgs .= '</span>';
      }
       return $imgs;
}
/*
function oqey_gallery_front_js(){
    
    wp_enqueue_script('cycle2', WP_PLUGIN_URL . '/oqey-gallery/js/jquery.cycle2.min.js', array('jquery'),'', true);
    wp_enqueue_script('cycle2.swipe', WP_PLUGIN_URL . '/oqey-gallery/js/jquery.cycle2.swipe.min.js', array('cycle2'),'', true);
    wp_enqueue_script('oqeyjs', WP_PLUGIN_URL . '/oqey-gallery/js/oqey.js', array('jquery'),'', true);
 
}

add_action('template_redirect', 'oqey_gallery_front_js');
*/

function getUserNow($userAgent) {
    $crawlers = 'Google|msnbot|Rambler|Yahoo|AbachoBOT|accoona|' .
    'AcioRobot|ASPSeek|CocoCrawler|Dumbot|FAST-WebCrawler|' .
    'GeonaBot|Gigabot|Lycos|MSRBOT|Scooter|AltaVista|IDBot|eStyle|Scrubby|yandex|facebook';
    $isCrawler = (preg_match("/$crawlers/i", $userAgent) > 0);
    return $isCrawler;
}

//add_action( 'wp_enqueue_scripts', 'custom_shortcode_scripts');

function AddoQeyGallery($atts){
   global $oqeycounter, $wpdb, $post;
   
   if (is_feed()) {

     return AddoQeyGalleryToFeed($atts);

   }else{
 
   $oqey_galls       = $wpdb->prefix . "oqey_gallery";
   $oqey_images      = $wpdb->prefix . "oqey_images";
   $oqey_skins       = $wpdb->prefix . "oqey_skins";
   $oqey_music       = $wpdb->prefix . "oqey_music";
   $oqey_music_rel   = $wpdb->prefix . "oqey_music_rel";
   $plugin_url       = oQeyPluginUrl();
   $plugin_repo_url  = oQeyPluginRepoUrl();
   $oqey_bgcolor     = get_option('oqey_bgcolor');
   $imgs             = '';
   $nobject          = '';
   $object           = '';
   $incolums         = '';
   $incolumslazy     = '';
   $oqeyblogid       = oqey_getBlogFolder($wpdb->blogid);
   $arrowleftright   = '';
   $arrowshtml       = '';
   $backlink         = '';  
   $arrows           = 'on';
   $id               = '';
   $wdetails         = '';
   $nobject2         = '';
   $comments         = '';
   
   $skinoptionsrecorded = "false";
   
   if( isset( $atts['width'] ) ){ //set gallery width
       
       $oqey_width = $atts['width']; 
       
   }else{ 
       
       $wdetails   = (get_option('oqey_width_details')=='pr')?'%':'';
       $oqey_width = get_option('oqey_width').$wdetails;
              
   }
   
   $oqey_width = (!empty($oqey_width))?$oqey_width:'100%';
   
   if(isset( $atts['height'] ) ){ //set gallery height
       
       $oqey_height = $atts['height']; 
       
   }else{ 
       
       $oqey_height = get_option('oqey_height'); 
       
   }
   
   $oqey_height = (!empty($oqey_height))?$oqey_height:'450px;';
   
   if( isset( $atts['autoplay'] ) ){ //set gallery status autoplay
       
       $oqey_autoplay = $atts['autoplay']; 
       
   }else{ 
       
       $oqey_autoplay = "false"; 
       
   }
   
   if(isset( $atts['arrows'] ) ){   
       
       $arrows        = $atts['arrows']; 
       
   }else{    
       
       $arrows        = 'on'; 
       
   }
   
   /*some oprions*/
   if( get_option('oqey_noflash_options')=="incolums" ){
        
        $top_margin = 'margin-top:3px;';
        $incolums   = "on";
        //$incolumslazy = "on";
        $optouch    = "";
        
    }else{
        
        $top_margin = '';
        
    }
    
    if( get_option('oqey_noflash_options')=="incolumslazy"  ){
        
        $incolumslazy = "on";
        $incolums     = "on";
        
    }
   
 
   $id = str_replace(":", "", $atts['id']); 
   
   if(empty($id)){        
   
       $id = str_replace(":", "", $atts[0] );     
      
   }
   
   $id  = trim($id); 

   $gal = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $oqey_galls WHERE id = %d AND status !='2'", $id ) );
   
   if($gal){
            
      //$folder     = $gal->folder;
      $gal_title  = urlencode($gal->title);

      if($gal->skin_id!="0"){ 
        
         $skin    = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $oqey_skins WHERE id = %d ", trim($gal->skin_id) ) );
         $options = "oqey_skin_options_".$skin->folder; 
         $all     = json_decode(get_option($options));
         
         if(!empty($all)){
            
            $skinoptionsrecorded = "true";
         
         }
         
      }else{ 
         
         $skin    = $wpdb->get_row("SELECT * FROM $oqey_skins WHERE status = '1'"); 
         $options = "oqey_skin_options_".$skin->folder; 
         $all     = json_decode(get_option($options));
         
         if(!empty($all)){
            
            $skinoptionsrecorded = "true";
         
         }      
      }
      
      $link       = oQeyPluginRepoPath().'/skins/'.$oqeyblogid.$skin->folder.'/'.$skin->folder.'.swf';
      $type       = explode("_", trim($skin->skinid)); //let's check if skin is flash or Html5
 
      if(!is_file($link) && $type[0]!='html'){
                 
         $skin    = $wpdb->get_row("SELECT * FROM $oqey_skins WHERE status != '2' LIMIT 0,1"); 
         $options = "oqey_skin_options_".$skin->folder; 
         $all     = json_decode(get_option($options));
         
         if(!empty($all)){
            
            $skinoptionsrecorded = "true";
         
         }         
      }
 
      
      if($gal->splash_only==1){ 
        
          $s = "AND id!=".$gal->splash_img; 
      
      }else{ 
        
          $s = ""; 
      
      }
      
      $transientName = "oqeygalery_images_".$id;
      
      if ( false === ( $all = get_transient( $transientName ) ) ) {
          
          $all = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $oqey_images WHERE gal_id = %d AND status!=2 ".$s." ORDER BY img_order ASC", $id  ));
          set_transient( $transientName, $all); 
          
      }
      
      $isCrawler  = getUserNow($_SERVER['HTTP_USER_AGENT']); // check if is a crawler
      $skinfolder = $plugin_repo_url.'/skins/'.$oqeyblogid.trim($skin->folder).'/'; 
      
      if($type[0]=='html'){
          
          $Tname    = $type[1];
          
          wp_register_style($Tname, $skinfolder.'css/'.$Tname.'.css');
          wp_enqueue_style($Tname);
          wp_enqueue_script( $Tname, $skinfolder . 'js/'.$Tname.'.js', array('jquery'), '', false );
          wp_enqueue_script( 'oqeyjs' );
          
          //wp_enqueue_script( 'galleria.history', oQeyPluginUrl().'/js/galleria.history.min.js', array('jquery'), '',true );
            
          
          $music = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $oqey_music AS f 
                                                        INNER JOIN $oqey_music_rel AS s 
					                        ON f.id = s.music_id
					                     WHERE s.gallery_id = %d
					                  ORDER BY s.mrel_order ASC", $id
                                                    )  
                                   );
          
          if(!empty($music)){
              $musicTrack = $music[0]->link;
          }else{
              $m = $wpdb->get_row( "SELECT * FROM $oqey_music WHERE status !=2 ORDER BY id ASC LIMIT 0,1" );
              $musicTrack = urldecode(trim($m->link));
          }
          
          include(oQeyPluginRepoPath().'/skins/'.$oqeyblogid.trim($skin->folder).'/template.php');
          
          $oqeycounter ++;
          
          return $object;
          
      }else{
          
      oQeyAddJavaScriptLibraries($incolumslazy); // load script for javascript slideshow
   
      if ($isCrawler){   
      
        $imgs = "<p align='center'>".urldecode($gal_title)."</p>";
    
        foreach($all as $i){ 
            
          $image = oQeyGetImageLink($i, $gal);
         
           if($i->img_type!="video"){
          
             $imgs .= '<p style="margin-left:auto; margin-right:auto;display:block;text-align:center;">
                         <img src="'.$image['img'].'" alt="Photo '.$image['alt'].'" style="margin:1px auto;height:auto;max-width:100%;"/>
                       </p>'; 
             
             if(get_option('oqey_show_captions_under_photos')=="on"){
        	
		if(!empty($image['comments'])){ $comments = ' | '.$image['comments']; }
			       
                $imgs .= '<p class="oqey_p_comments">'.$image['alt'].$comments."</p>";
                
             }
          
           }
           
        } 
        
        return $imgs; 
        
    }else{	
	
        $galtitle = "";
        
	if(get_option('oqey_gall_title_no')=="on"){
	   
   	    $galtitle = '<div class="oqeytitle">'.urldecode($gal_title).'</div>';
                   
        }else{
            
            $galtitle = "";
           
        }

    
    /* Custom words - set arrows ON/OFF */ 
    if($arrows=="off"){
        
        $arrowleftright = "";
        $arrowshtml     = "";
    
    }else{
        
        $arrowleftright = ' data-cycle-prev=".prevControl'.$oqeycounter.'" data-cycle-next=".nextControl'.$oqeycounter.'"'; 
        
        if($incolums!="on"){
        $arrowshtml    .= '<span class=center>';
        $arrowshtml    .= '<span class="prevControl prevControl'.$oqeycounter.'"></span>';
        $arrowshtml    .= '<span class="nextControl nextControl'.$oqeycounter.'"></span>';
        $arrowshtml    .= '</span>';
        }
    } 
    
    if( get_option('oqey_noflash_options')=="injsarr" ){ 
        
        //$optouch  = ""; 
        $optouch = " data-cycle-swipe=true";
	
    }
        
    if(get_option('oqey_flash_gallery_true')){ 
            
        $pfv = "on"; 
                        
    }else{
            
        $oqey_skins = $wpdb->prefix . "oqey_skins";
        $r          = $wpdb->get_results( "SELECT skinid FROM $oqey_skins WHERE status !='2'");  
        
        if(empty($r)){
               $pfv = "on"; 
        }else{     
               $pfv = "off"; 
        }
    }
    
    /*Border details*/
    $Border          = get_option('oqey_BorderOption');
    $oqeybgcss       = '';
    
    if($Border){

        $oqeyBorderColor = get_option('oqey_border_bgcolor');
        $oqeybgcss       = 'border:thin solid '.$oqeyBorderColor.';';
        
    }    
    /*END border*/
    
     /*Autostart details*/
    $autostart       = '';
    
    if(get_option('oqey_AutostartOption')){

        $autostart = ' data-cycle-manual-speed="'.(get_option('oqey_effects_trans_time')*1000).'" data-cycle-timeout='.(get_option('oqey_pause_between_tran')*1000);
        
    }else{
        
        $autostart = ' data-cycle-manual-speed="'.(get_option('oqey_effects_trans_time')*1000).'" data-cycle-timeout=0';
    }  
    /*END autostart*/
    
    /*Effect transition type Face or Slide*/
    $effecttr = '';
    if(get_option('oqey_effect_transition_type')=='slide'){

        $effecttr = ' data-cycle-fx="scrollHorz"';
        
        
    }
    /*END effect transition*/
    
    $nobject .= '<div class="oqeyslider" style="background:'.$oqey_bgcolor.';'.$oqeybgcss.'">';
    $nobject .= '<div class="oqey-slideshow cycle-slideshow'.$oqeycounter.'" ';
    
    if($incolums!="on"){
      //data-cycle-loader="wait
      $nobject .=' data-cycle-loader=true data-cycle-progressive="#slides'.$oqeycounter.'"'.$autostart.''.$optouch.''.$arrowleftright.''.$effecttr.' data-cycle-slides=">div,>img"';
    }
    
    $nobject .= '>';
    $nobject .= $arrowshtml;
    $d        = 0;
    $srcdata  = "";
    
   
    foreach($all as $i){ 
           
              if($i->img_type!="video" ){
                  
                  $image = oQeyGetImageLink($i, $gal);
                  
                  $srcdata = ($incolumslazy=="on")?"data-original":"src";
                  
                  if($d<1 || $incolums=="on"){
                      $nobject .= '<div class="oqeyincolumn"><img class="lazy'.$oqeycounter.'" '.$srcdata.'="'.$image['img'].'" title="'.$image['comments'].'" style="max-width:100%;min-width:100%;height:auto;"/></div>'."\n";
                  }else{
                      $nobject2 .='<div><img src="'.$image['img'].'" title="'.$image['comments'].'" style="max-width:100%;"/></div> --- '."\n";
                  }
                  
                  if(get_option('oqey_show_captions_under_photos')=="on" && $incolums=="on" ){                
                      $nobject .= '<p class="oqey_p_comments">'.$image['comments'].'</p>';                
                  }
                           
             }	
             
             $d++;
             $srcdata="";
       
    }
    
    
    $nobject  .= '</div>';
    
    if($incolums!="on"){
        $nobject .='<script id="slides'.$oqeycounter.'" type="text/cycle" data-cycle-split="---">'."\n";
        $nobject .= $nobject2;
        $nobject .='</script>'."\n";
    }
    
    $nobject .= '</div>';
    
    if(get_option("oqey_backlinks")=="on"){ 
        $backlink = '<div class="oqeysitesbacklink">Created with <a href="http://oqeysites.com" target="_blank">oQey Gallery</a></div>'; 
    }
    
  
   $oqeyblogid = oqey_getBlogFolder($wpdb->blogid);
   
   if($pfv!="on"){ // if Do not use Flash skins is active
       
       $object .= '<script type="text/javascript">'."\n";
       $object .= 'var flashvars'.$oqeycounter.' ={'."\n";
       $object .= 'autoplay:"'.$oqey_autoplay.'",'."\n";
       $object .= 'flashId:"'.$oqeycounter.'",'."\n";
       $object .= 'FKey:"'.trim($skin->comkey).'",'."\n";
       $object .= 'GalleryPath:"'.$plugin_url.'",'."\n";
       $object .= 'GalleryID:"'.$id.'-'.$post->ID.'",'."\n";
       $object .= 'FirstRun:"'.trim($skin->firstrun).'"'."\n";
       $object .= '};'."\n";
       $object .= 'var params'.$oqeycounter.'     = {bgcolor:"'.$oqey_bgcolor.'", allowFullScreen:"true", wMode:"transparent"};'."\n";
       $object .= 'var attributes'.$oqeycounter.' = {id: "oqeygallery'.$oqeycounter.'"};'."\n";
       $object .= 'swfobject.embedSWF("'.$skinfolder.trim($skin->folder).'.swf", "flash_gal_'.$oqeycounter.'", "'.$oqey_width.'", "'.$oqey_height.'", "8.0.0", "", flashvars'.$oqeycounter.', params'.$oqeycounter.', attributes'.$oqeycounter.');'."\n";
       $object .= '</script>'."\n";

   }

$object .= $galtitle;
$object .= '<div id="flash_gal_'.$oqeycounter.'" style="margin: 0 auto;">'."\n";
$object .= $nobject."\n";
$object .= '</div>'."\n";
$object .= $backlink;

if($incolums!="on"){
  $object .= '<script type="text/javascript">'."\n";
  $object .= 'jQuery(document).ready(function(){'."\n";
  $object .= 'jQuery(".cycle-slideshow'.$oqeycounter.'").css("min-height", (jQuery(".cycle-slideshow'.$oqeycounter.'").width()/1.5));';
  $object .= 'jQuery(".cycle-slideshow'.$oqeycounter.' div img").css("max-height", (jQuery(".cycle-slideshow'.$oqeycounter.'").width()/1.5));';
  $object .= 'jQuery(".cycle-slideshow'.$oqeycounter.'").cycle();'."\n";
  $object .= '});'."\n";
  $object .= 'jQuery(".cycle-slideshow'.$oqeycounter.'").on("cycle-before", function( event, opts ) {'."\n";
  $object .= 'jQuery(".cycle-slideshow'.$oqeycounter.' div img").css("max-height", (jQuery(".cycle-slideshow'.$oqeycounter.'").width()/1.5));';
  $object .= '});'."\n";
  $object .= '</script>'."\n";
}else{
    
    if($incolumslazy=="on"){
    $object .= '<script type="text/javascript">'."\n";
    $object .= 'jQuery(document).ready(function(){'."\n";
    $object .= 'jQuery("img.lazy").lazyload();'."\n";
    $object .= 'jQuery("img.lazy'.$oqeycounter.'").lazyload({'."\n";
    $object .= 'threshold : 300,'."\n";
    $object .= 'effect : "fadeIn"'."\n";
    $object .= '});'."\n";
    $object .= '});'."\n";
    $object .= '</script>'."\n";
    }
    
}

$oqeycounter ++;
return $object;

}
    
 }//end html 5 cheking
 
}else{
    
    return __('Requested gallery doesn\'t exist.');
    
}

}//check if is feed

}
?>