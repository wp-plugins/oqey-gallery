<?php
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'gallcore.php' == basename($_SERVER['SCRIPT_FILENAME'])) die ('Please do not load this page directly. Thanks!');

global $oqeycounter;
$oqeycounter = 1;
 
 if(is_admin()){
    
    require ("oqey-ajax.php");
 
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
        
        //print_r($gal);
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
    
    if($arrows=="off"){
        
        $arrowleftright = "";
        $arrowshtml     = "";
    
    }else{
        
        $arrowleftright = ' data-cycle-prev=".prevControl'.$oqeycounter.'" data-cycle-next=".nextControl'.$oqeycounter.'"'; 
        $arrowshtml    .= '<span class=center>';
        $arrowshtml    .= '<span class="prevControl prevControl'.$oqeycounter.'"></span>';
        $arrowshtml    .= '<span class="nextControl nextControl'.$oqeycounter.'"></span>';
        $arrowshtml    .= '</span>';
    } 
    
    if( get_option('oqey_noflash_options')=="injsarr" ){ 
        
        $optouch  = ""; 	
	
    }
	
    if( get_option('oqey_noflash_options')=="injsarrtouch" ){ 

        $optouch  = " data-cycle-swipe=true";             
    
    }
    
    if( get_option('oqey_noflash_options')=="incolums" ){  
	   
        $incolums = "on";
        $optouch  = ""; 
    }
        
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
	   
      if($i->img_type=="nextgen"){
        
        $ipath      = OQEY_ABSPATH.'/'.trim($i->img_path).'/';
        $img_type   = "nextgen";
        $img_f_path = urlencode(trim($i->img_path));
        $imglink    = get_option('siteurl').'/'.trim($i->img_path)."/".trim($i->title);
       
      }else{
      
        $ipath      = OQEY_ABSPATH.'wp-content/oqey_gallery/galleries/'.oqey_getBlogFolder($wpdb->blogid).$gal->folder.'/galimg/';
        $img_type   = "oqey";
        $img_f_path = "";
        $imglink    = $plugin_repo_url.'/galleries/'.oqey_getBlogFolder($wpdb->blogid).$gal->folder.'/galimg/'.trim($i->title);
      
      }

	if($d<1 || get_option('oqey_noflash_options')=="incolums"){
                $nobject .= '<div style="margin:0 auto 5px auto;"><img src="'.$imglink.'" title="'.trim($i->comments).'" style="max-width:100%;"/></div>'."\n";
            }else{
                $nobject2 .='<div><img src="'.$imglink.'" title="'.trim($i->comments).'" style="max-width:100%;"/></div> --- '."\n";
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
 
function addoQeyMediaIcon($context){
    global $post_ID;    
    
	$qgall_upload_iframe_src = "media-upload.php?type=oqeygallery&amp;post_id=".$post_ID;
	$qgall_iframe_src        = apply_filters('qgall_iframe_src', $qgall_upload_iframe_src."&amp;tab=oqeygallery");
	$title                   = __('Add oQey Gallery');
	$qgall_button_src        = oQeyPluginUrl().'/images/oqeyinsert.png';
        
        return $context.'<a href="'.$qgall_upload_iframe_src.'&amp;TB_iframe=1&amp;height=500&amp;width=640" class="thickbox" id="add_oqeygallery" title="'.$title.'"><img src="'.$qgall_button_src.'" alt="'.$title.'" /></a>';
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

function oQeyPluginRepoPath() {
	$path = OQEY_ABSPATH . 'wp-content/oqey_gallery';   
	return $path;
}

function oqeygall_tab_content(){
   global $post_ID, $temp_ID, $wpdb;
    if(isset($_GET['type'])){
	if($_GET['type'] == "oqeygallery"){ include ("insert_in_post.php"); }
    }
}

add_action('admin_init', 'oqeygall_tab_content');

add_action('admin_menu', 'oqey_add_pages');

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
    <h2 style="width: 900px;"><?php _e('oQey Gallery plugin', 'oqey-gallery'); ?>
    <div style="margin-left:250px; float:right; width: 200px; height: 20px;">
     <div id="fb-root"></div>
     <div class="fb-like" data-href="http://www.facebook.com/oqeysites" data-send="false" data-layout="button_count" data-width="100" data-show-faces="false" data-font="tahoma"></div>
     <div class="fb-send" data-href="http://oqeysites.com"></div>
    </div>
    </h2>
</div>

<div class="metabox-holder has-right-sidebar">
										
<div class="has-sidebar sm-padded">
	<div id="post-body-content" class="has-sidebar-content">
	  <div class="meta-box-sortabless">
        
       <div class="postbox" style="width:870px;">
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
                echo urldecode($body);
            }
           }
        ?>
        </p>
       </div>
      </div>
      
      <div class="postbox" style="width:870px;">
          <h3 class="hndle"><span>WP Themes and Site templates</span></h3>
          <div class="inside" style="font-size:13px;text-align:justify;"> 
              <p>If you are interested to buy an original WP theme, oQey Sites recommends the following themes. Check it out!</p>
              <a href="http://themeforest.net/?ref=oqeysites" target="_blank"><img style="border:none;" src="<?php echo oQeyPluginUrl(); ?>/images/tf_728x90_v5.gif" width="728" height="90" /></a>
          </div>
      </div>

      <div class="postbox" style="width:870px;">
          <h3 class="hndle"><span><?php _e('Donate', 'oqey-gallery'); ?></span></h3> 
          <div class="inside" style="font-size:13px;padding:10px auto;text-align:justify;">
              <div align="left"><p>If you really like this plugin and find it useful, help to keep this plugin free and constantly updated by clicking the donate button below.</p></div>
              <div align="right">
                  <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank"><input type="hidden" name="cmd" value="_s-xclick"/><input type="hidden" name="hosted_button_id" value="3ZV8CCFYAUYKJ"/><input alt="PayPal - The safer, easier way to donate online!" name="submit" src="<?php echo oQeyPluginUrl(); ?>/images/btn_donate.gif" type="image"/><img src="https://www.paypal.com/en_US/i/scr/pixel.gif" alt="" width="1" border="0" height="1"/></form>
              </div>
          </div>
      </div>
        
      <div class="postbox" style="width:870px;">
          <h3 class="hndle"><span><?php _e('About', 'oqey-gallery'); ?></span></h3>
          <div class="inside" style="font-size:13px; padding:10px auto; text-align:justify;">
              <p>oQey Gallery is a premium grade plugin for managing images and video, creating photo slideshows with music, photo &amp; video galleries that works fine under iPhone / iPad and other mobile devices. Flash version of the slideshow is automatically replaced by the HTML5 | Java slideshow on a non-flash device. Flash gallery supports customizable skins, so you can change the way it looks with a few clicks using the skin options tool. Commercial skins are also available as well as custom built photo / video galleries and slideshows for professionals. This plugin uses built-in WP functions and a simple batch upload system. 
                   Check this out on <a href="http://oqeysites.com/" target="_blank">oqeysites.com</a></p>
              <p><a href="http://oqeysites.com"><img style="border: none;" src="<?php echo WP_PLUGIN_URL; ?>/oqey-gallery/images/oqeybanner.jpg" /></a></p>
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
            
            $gimg = get_option('siteurl').'/'.trim($i->img_path).'/';
        
           }else{
        
            $gimg = get_option('siteurl').'/wp-content/oqey_gallery/galleries/'.oqey_getBlogFolder($wpdb->blogid).$gal->folder.'/galimg/';
        
           } 
	 
          $imgs .= '<p><img src="'.$gimg.trim($i->title).'" alt="'.urlencode(trim($i->alt)).'" style="margin-top:3px;"/></p>'; 
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
   
   oQeyAddJavaScriptLibraries(); // load script for javascript slideshow
   
   if (is_feed()) {

     return AddoQeyGalleryToFeed($atts);

   }else{
 
   $oqey_galls       = $wpdb->prefix . "oqey_gallery";
   $oqey_images      = $wpdb->prefix . "oqey_images";
   $oqey_skins       = $wpdb->prefix . "oqey_skins";
   $plugin_url       = oQeyPluginUrl();
   $plugin_repo_url  = oQeyPluginRepoUrl();
   $oqey_bgcolor     = get_option('oqey_bgcolor');
   $imgs             = '';
   $nobject          = '';
   $object           = '';
   $incolums         = '';
   $oqeyblogid       = '';
   $arrowleftright   = "";
   $arrowshtml       = "";
   $backlink         = "";  
   $arrows           = "on";
   $id               = "";
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
   
   if(isset( $atts['height'] ) ){ //set gallery height
       
       $oqey_height = $atts['height']; 
       
   }else{ 
       
       $oqey_height = get_option('oqey_height'); 
       
   }
   
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
   
 
   $id = str_replace(":", "", $atts['id']); 
   
   if(empty($id)){        
   
       $id = str_replace(":", "", $atts[0] );     
      
   }
   
   $id            = esc_sql(trim($id)); 

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
      
      $link       = OQEY_ABSPATH . 'wp-content/oqey_gallery/skins/'.oqey_getBlogFolder($wpdb->blogid).$skin->folder.'/'.$skin->folder.'.swf';
      
      if(!is_file($link)){
        
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
      
      $all       = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $oqey_images WHERE gal_id = %d AND status!=2 ".$s." ORDER BY img_order ASC", $id  ));
      $gimg      = get_option('siteurl').'/wp-content/oqey_gallery/galleries/'.oqey_getBlogFolder($wpdb->blogid).$gal->folder.'/galimg/';
      $isCrawler = getUserNow($_SERVER['HTTP_USER_AGENT']); // check if is a crawler

      if ($isCrawler){    
           
        $imgs = "<p align='center'>".urldecode($gal_title)."</p>";
    
        foreach($all as $i){ 
            
          if($i->img_type=="nextgen"){
        
           $gimg = get_option('siteurl').'/'.trim($i->img_path).'/';
        
          }
         
           if($i->img_type!="video"){
          
             $imgs .= '<p style="margin-left:auto; margin-right:auto;display:block;text-align:center;">
                         <img src="'.$gimg.trim($i->title).'" alt="Photo '.urldecode(trim($i->alt)).'" style="margin:1px auto;height:auto;max-width:100%;"/>
                       </p>'; 
             
             if(get_option('oqey_show_captions_under_photos')=="on"){
        	
		if(!empty($i->comments)){ $comments = ' | '.trim(urldecode($i->comments)); }
			       
                $imgs .= '<p class="oqey_p_comments">'.trim(urldecode($i->alt)).$comments."</p>";
                
             }
          
           }
           
        } 
        
        return $imgs; 
        
    }else{	
	
        $galtitle = "";
        
	if(get_option('oqey_gall_title_no')=="on"){
	   
   	    $galtitle = '<div style="margin:0 auto;width:100%;text-align:center;">'.urldecode($gal_title).'</div>';
                   
        }else{
            
            $galtitle = "";
           
        }
	
    if( get_option('oqey_noflash_options')=="incolums" ){
        
        $top_margin = 'margin-top:3px;';
        
    }else{
        
        $top_margin = '';
        
    }
    
    /* Custom words - set arrows ON/OFF */ 
    if($arrows=="off"){
        
        $arrowleftright = "";
        $arrowshtml     = "";
    
    }else{
        
        $arrowleftright = ' data-cycle-prev=".prevControl'.$oqeycounter.'" data-cycle-next=".nextControl'.$oqeycounter.'"'; 
        $arrowshtml    .= '<span class=center>';
        $arrowshtml    .= '<span class="prevControl prevControl'.$oqeycounter.'"></span>';
        $arrowshtml    .= '<span class="nextControl nextControl'.$oqeycounter.'"></span>';
        $arrowshtml    .= '</span>';
    } 
    
    if( get_option('oqey_noflash_options')=="injsarr" ){ 
        
        $optouch  = ""; 	
	
    }
	
    if( get_option('oqey_noflash_options')=="injsarrtouch" ){ 

        $optouch  = " data-cycle-swipe=true";             
    
    }
    
    if( get_option('oqey_noflash_options')=="incolums" ){  
	   
	$incolums = "on";
        $optouch  = ""; 
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
        	
	foreach($all as $i){ 
           
              if($i->img_type!="video" ){
                  
                  if($i->img_type=="nextgen"){
                      
                      $ipath      = OQEY_ABSPATH.'/'.trim($i->img_path).'/';
                      $img_type   = "nextgen";
                      $img_f_path = urlencode(trim($i->img_path));
                      $imglink    = get_option('siteurl').'/'.trim($i->img_path)."/".trim($i->title);
                                            
                  }else{
                      
                      $ipath      = OQEY_ABSPATH.'wp-content/oqey_gallery/galleries/'.oqey_getBlogFolder($wpdb->blogid).$gal->folder.'/galimg/';
                      $img_type   = "oqey";
                      $img_f_path = "";
                      $imglink    = $plugin_repo_url.'/galleries/'.oqey_getBlogFolder($wpdb->blogid).$gal->folder.'/galimg/'.trim($i->title);
                                            
                  }
                  
                  if($d<1 || get_option('oqey_noflash_options')=="incolums"){
                      $nobject .= '<div style="margin:0 auto 5px auto;"><img src="'.$imglink.'" title="'.trim($i->comments).'" style="max-width:100%;"/></div>'."\n";
                  }else{
                      $nobject2 .='<div><img src="'.$imglink.'" title="'.trim($i->comments).'" style="max-width:100%;"/></div> --- '."\n";
                  }
                  
                  if(get_option('oqey_show_captions_under_photos')=="on" && get_option('oqey_noflash_options')=="incolums" ){                
                      $nobject .= '<p class="oqey_p_comments">'.trim($i->comments).'</p>';                
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
    
    if(get_option("oqey_backlinks")=="on"){ 
        $backlink = '<div style="text-align:center;margin:0 auto;width:50%;">Created with <a href="http://oqeysites.com" target="_blank">oQey Gallery</a></div>'; 
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
       $object .= 'swfobject.embedSWF("'.$plugin_repo_url.'/skins/'.$oqeyblogid.trim($skin->folder).'/'.trim($skin->folder).'.swf", "flash_gal_'.$oqeycounter.'", "'.$oqey_width.'", "'.$oqey_height.'", "8.0.0", "", flashvars'.$oqeycounter.', params'.$oqeycounter.', attributes'.$oqeycounter.');'."\n";
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
  //$object .= 'jQuery(".oqey-slideshow").click(function(){ jQuery(".cycle-slideshow'.$oqeycounter.'").cycle("pause"); }) ';
  //$object .= 'jQuery(".oqey-slideshow").click(function(){ jQuery(".cycle-slideshow'.$oqeycounter.'").cycle("next"); })';
  $object .= '</script>'."\n";
}

$oqeycounter ++;
return $object;
}
}//end crawler check
}
}
?>