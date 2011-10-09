<?php
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'gallcore.php' == basename($_SERVER['SCRIPT_FILENAME'])) die ('Please do not load this page directly. Thanks!');
global $oqeycounter;
$d=0;
$oqeycounter = 1;

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
		$this->WP_Widget( 'oqey-gallery-widget', __('Oqey Widget', 'oqeygallery'), $widget_ops );
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );
        
        global $wpdb,$oqeycounter;
        
        $oqey_galls = $wpdb->prefix . "oqey_gallery";
        $oqey_skins = $wpdb->prefix . "oqey_skins";
        $oqey_images = $wpdb->prefix . "oqey_images";
        
        $gal = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $oqey_galls WHERE id = %d ", esc_sql($instance['oqeygalleryid']) ));
        
        $folder = $gal->folder;
    
        if($gal->skin_id!="0"){
            
           $skin = $wpdb->get_row("SELECT * FROM $oqey_skins WHERE id = '".$gal->skin_id."'");
        
        }else{
        
           $skin = $wpdb->get_row("SELECT * FROM $oqey_skins WHERE status = '1'");
        
        }
        
        if($gal->splash_only==1){ $s = "AND id!=".$gal->splash_img; }else{ $s=""; }
        
        $all = $wpdb->get_results("SELECT * FROM $oqey_images WHERE gal_id = '".esc_sql($instance['oqeygalleryid'])."' AND status!=2 ".$s." ORDER BY img_order ASC");

        $oqey_bgcolor = get_option('oqey_bgcolor');
        $plugin_url = oQeyPluginUrl();
        $plugin_repo_url = oQeyPluginRepoUrl();
        $autoplay = "false";
        if($instance['autostart']==1){ $autoplay = "true"; }else{ $autoplay="false"; }
        
        $title = apply_filters('widget_title', $instance['title'] );
                
        echo $before_widget;

		if ( $title )
			echo $before_title . $title . $after_title;
   
   /*images array*/     
    $oqey_height = $instance['height'];
    $oqey_width = $instance['width'];
       
   foreach($all as $i){ 
	  
      if($i->img_type=="nextgen"){
        
        $ipath = OQEY_ABSPATH.'/'.trim($i->img_path).'/';
        $img_type = "nextgen";
        $img_f_path = urlencode(trim($i->img_path));
       
      }else{
      
        $ipath = OQEY_ABSPATH.'wp-content/oqey_gallery/galleries/'.oqey_getBlogFolder($wpdb->blogid).$gal->folder.'/iphone/';
        $img_type = "oqey";
        $img_f_path = "";
      
      }
       
	$img_path = $ipath.trim($i->title);   
    $size = @getimagesize( $img_path );
	if ( $size ){
	   
	   list($iwidth, $iheight, $itype, $iattr)= $size;
    
    }else{
        
	   $iwidth = 900;
       $iheight = 600;
	
    }
    
      $img_holder_h = $oqey_width/1.5;
      $d = wp_constrain_dimensions($iwidth, $iheight, $oqey_width, $img_holder_h);

	  $img_full_root = get_option('siteurl').'/wp-content/plugins/oqey-gallery/oqeyimgresize.php?width='.$d[0].'&amp;new_height='.$d[1].'&amp;folder='.$gal->folder.'&amp;img='.trim($i->title).'&amp;img_type='.$img_type.'&amp;img_f_path='.$img_f_path;
	  $imgs .= '[div class="oqeyimgdiv" style="background: url('.$img_full_root.') center center no-repeat; width:'.$oqey_width.'px; height:'.$img_holder_h.'px;"][/div]';
	}	
            
         echo '<div id="oqey_image_div'.$oqeycounter.'" style="position:relative; width:'.$instance['width'].'px; height:'.$instance['height'].'; display:none; margin: 0 auto;">
               <div id="image'.$oqeycounter.'" style="height:auto; display:none;" class="oqey_images"></div>
               </div>';
         
         echo '<script type="text/javascript">
                jQuery(function($) {
                var pv = swfobject.getFlashPlayerVersion();
                oqey_e_w(pv, \''.$oqeycounter.'\', \''.$imgs.'\', \''.(get_option('oqey_pause_between_tran')*1000).'\');
               });
               </script>';
          //oqey_e(pv, {$oqeycounter}, '{$imgs}', '{$optouch}', '{$incolums}');
         
         echo '<div id="flash_gal_'.$oqeycounter.'" style="width:'.$instance['width'].'px; height:'.$instance['height'].'px; margin: 0 auto;">
               <script type="text/javascript">
                  var flashvars'.$oqeycounter.' = {
                          autoplay:"'.$autoplay.'",
                           flashId:"'.$oqeycounter.'",
		                      FKey:"'.$skin->comkey.'",
	                   GalleryPath:"'.$plugin_url.'",	
                         GalleryID:"'.$instance['oqeygalleryid'].'-0",
					      FirstRun:"'.$skin->firstrun.'"
					 };
              	var params'.$oqeycounter.' = {bgcolor:"'.get_option('oqey_bgcolor').'", allowFullScreen:"true", wMode:"transparent"};
	            var attributes'.$oqeycounter.' = {id: "oqeygallery'.$oqeycounter.'"};
               	swfobject.embedSWF("'.$plugin_repo_url.'/skins/'.$skin->folder.'/'.$skin->folder.'.swf", "flash_gal_'.$oqeycounter.'", "'.$instance['width'].'", "'.$instance['height'].'", "8.0.0", "", flashvars'.$oqeycounter.', params'.$oqeycounter.', attributes'.$oqeycounter.');
               </script> 
               
               </div>';
         $oqeycounter ++;         

          echo '<div class="textwidget">'.$instance['oqeywidgettext'].'</div>';
         
          if (isset($after_widget)) {
				echo $after_widget;
		}

}

	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['oqeygalleryid'] = (int) $new_instance['oqeygalleryid'];
		$instance['height'] = (int) $new_instance['height'];
		$instance['width'] = (int) $new_instance['width'];
        $instance['autostart'] = (bool) $new_instance['autostart'];
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
        $instance = wp_parse_args( (array) $instance, array( 'title' => 'Oqey Gallery', 'oqeygalleryid' => '0', 'height' => '140', 'width' => '210') );
        $title  = esc_attr( $instance['title'] );
		$height = esc_attr( $instance['height'] );
		$width  = esc_attr( $instance['width'] );
        $oqeywidgettext  =  esc_textarea($instance['oqeywidgettext']);   
             
		$gals = $wpdb->get_results("SELECT * FROM $oqey_galls WHERE status!=2 ORDER BY id ASC ");
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
		<p>
			<label for="<?php echo $this->get_field_id('galleryid'); ?>"><?php _e('Select Gallery:', 'oqeygallery'); ?></label>
				<select size="1" name="<?php echo $this->get_field_name('oqeygalleryid'); ?>" id="<?php echo $this->get_field_id('oqeygalleryid'); ?>" class="widefat">
					<option value="0" <?php if (0 == $instance['oqeygalleryid']) echo "selected='selected' "; ?> ><?php _e('All galleries', 'oqeygallery'); ?></option>
<?php
				if($gals) {
					foreach($gals as $gal) {
					echo '<option value="'.$gal->id.'" ';
					if ($gal->id == $instance['oqeygalleryid']) echo "selected='selected' ";
					echo '>'.trim($gal->title).'</option>'."\n\t"; 
					}
				}
?>
				</select>
		</p>
		<p><label for="<?php echo $this->get_field_id('height'); ?>"><?php _e('Height:', 'oqeygallery'); ?></label> 
        <input id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" type="text" style="padding: 3px; width: 45px;" value="<?php echo $height; ?>" /></p>
		
        <p><label for="<?php echo $this->get_field_id('width'); ?>"><?php _e('Width:', 'oqeygallery'); ?></label> 
        
        <input id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" type="text" style="padding: 3px; width: 45px;" value="<?php echo $width; ?>" /></p>
	    <p>
           <label for="<?php echo $this->get_field_id('autostart'); ?>">
		   <input id="<?php echo $this->get_field_id('autostart'); ?>" name="<?php echo $this->get_field_name('autostart'); ?>" type="checkbox" value="1" <?php checked(true , $instance['autostart']); ?> /> <?php _e('Autostart','oqeygallery'); ?>
		</p>
 	    <p>
         <label for="<?php echo $this->get_field_id('oqeywidgettext'); ?>"><?php _e('Few words:', 'oqeygallery'); ?></label> 
        <textarea id="<?php echo $this->get_field_id('oqeywidgettext'); ?>" name="<?php echo $this->get_field_name('oqeywidgettext'); ?>" style="padding: 3px; width: 100%;"><?php echo $oqeywidgettext; ?></textarea></p>
		
        
    <?php
	}
}

/*End Widget*/



/*CROP ADDON ajax*/
//create new thumb
add_action( 'wp_ajax_createoQeyNewThumb', 'createoQeyNewThumb' );
function createoQeyNewThumb() {
    global $wpdb;
    $oqey_images = $wpdb->prefix . "oqey_images";
    $simage = $wpdb->get_row("SELECT * FROM $oqey_images WHERE id ='".esc_sql($_POST['imgid'])."' AND status !=2");
    
    if( !empty($simage) ){
        
    if($simage->img_type=="nextgen"){
         $iphoneimg = OQEY_ABSPATH.'/'.trim($simage->img_path).'/'.trim($simage->title);
         $iphoneimgthmb = OQEY_ABSPATH.'/'.trim($simage->img_path).'/thumbs/thumbs_'.trim($simage->title);
    }else{
        $iphoneimg = OQEY_ABSPATH.'wp-content/oqey_gallery/galleries/'.oqey_getBlogFolder($wpdb->blogid).oqey_get_gallery_folder($simage->gal_id)."/iphone/".trim($simage->title);
        $iphoneimgthmb = OQEY_ABSPATH.'wp-content/oqey_gallery/galleries/'.oqey_getBlogFolder($wpdb->blogid).oqey_get_gallery_folder($simage->gal_id)."/galthmb/".trim($simage->title);       
    }
    
    wp_crop_image($iphoneimg, ($_POST['x']*2), ($_POST['y']*2), ($_POST['w']*2), ($_POST['h']*2), $_POST['Qttw'], $_POST['Qtth'], "",$iphoneimgthmb );   
    }
    
}
/*CROP ADDON ajax*/


/*START galleries ajax*/

/*Check for others galleries*/
add_action( 'wp_ajax_oQeyCheckForOthersGalleries', 'oQeyCheckForOthersGalleries' );
function oQeyCheckForOthersGalleries(){
    global $wpdb, $current_user;
    $nggpic = $wpdb->prefix . 'ngg_pictures';
	$nggal = $wpdb->prefix . 'ngg_gallery';
    $oqey_galls = $wpdb->prefix . "oqey_gallery";
    $oqey_images = $wpdb->prefix . "oqey_images";
    $resp = array();
    
    if ( !current_user_can('oQeyGalleries') ) die(__('You do not have sufficient permissions to do this.'));
    
    if (isset($_POST['info'])) {
	$do = $_POST['info'];
    }
	switch ($do) {
		case 'all':
		 
         if( $wpdb->get_var( "SHOW TABLES LIKE '$nggpic'" ) && $wpdb->get_var( "SHOW TABLES LIKE '$nggal'" ) ) { 
         echo "From: &nbsp;";
         echo '<select name="othersgalls" id="othersgalls">';
         echo '<option value="0" selected="selected">Select</option>';
         echo '<option value="nextgen">nextgen</option>';
         echo '</select>';
         }else{ echo "Importable galleries not found."; }
         //echo '<div id="others_info"></div>';
         
        break;
		case 'nextgen':
        $all = $wpdb->get_results("SELECT * FROM $nggal");
        //echo "<pre>". print_r($all, true)."</pre>";
         echo "&nbsp;";
         echo '<select name="otgallstitle" id="otgallstitle">';
         echo '<option value="0" selected="selected">Select a gallery</option>';
         
         foreach($all as $g){
         echo '<option value="'.$g->gid.'">'.$g->title.'</option>';
         }
         echo '</select>';
         echo "<input id='importnewgall' class='importnewgall' type='button' style='display:none;' value='Create'/>";
		break;
        
        case 'nextgencreate':
        $info = $wpdb->get_row("SELECT * FROM $nggal WHERE gid = '".$_POST['gid']."'");
        $folder = sanitize_title($info->title);
        $newtitle = esc_sql(stripslashes_deep(trim(urldecode($info->title))));

        if($sql=$wpdb->get_row($wpdb->prepare("SELECT * FROM $oqey_galls WHERE title = %s",$newtitle ))){ $newtitle = $newtitle.time(); }

        if($sql=$wpdb->get_row($wpdb->prepare("SELECT * FROM $oqey_galls WHERE folder = %s", $folder ))){ $folder = $folder.time(); }

        $add = $wpdb->query("INSERT INTO $oqey_galls (title, post_id, author, folder) VALUES ('".$newtitle."', '".$info->pageid."', '".$info->author."', '".$folder."' )");
        $lastid = mysql_insert_id();
        
        $resp["galid"] = $lastid;
        
        if($add){ 
            
            $img = OQEY_ABSPATH.'wp-content/oqey_gallery/galleries/'.oqey_getBlogFolder($wpdb->blogid).$folder.'/galimg';
            $thumb = OQEY_ABSPATH.'wp-content/oqey_gallery/galleries/'.oqey_getBlogFolder($wpdb->blogid).$folder.'/galthmb';   
            $iphone = OQEY_ABSPATH.'wp-content/oqey_gallery/galleries/'.oqey_getBlogFolder($wpdb->blogid).$folder.'/iphone';   
            wp_mkdir_p($img);
            wp_mkdir_p($thumb);
            wp_mkdir_p($iphone);            
            
            $imgs = $wpdb->get_results("SELECT * FROM $nggpic WHERE galleryid = '".$_POST['gid']."'");
            
            //echo "<pre>". print_r($imgs, true)."</pre>";
            //pid 	image_slug 	post_id 	galleryid 	filename 	description 	alttext 	imagedate 	exclude 	sortorder 	meta_data
            if(!empty($imgs)){
            foreach($imgs as $i){
            $wpdb->query("INSERT INTO $oqey_images (title, gal_id, img_order, alt, comments, status, img_link, img_path, img_type) VALUES ('".$i->filename."', '".$lastid."', '".$i->sortorder."', '".stripslashes_deep($i->alttext)."', '".stripslashes_deep($i->description)."', '0', '', '".$info->path."', 'nextgen')");
            }
            }
            
            $resp["response"]="Created";
                        
            echo json_encode($resp);
        }
		break;
			
		default:
        echo "There is no others galleries available at the moment.";
    }
    die();
}
/*END checking*/

/*Get all galleries*/
add_action( 'wp_ajax_oQeyGetAllGalleries', 'oQeyGetAllGalleries' );
function oQeyGetAllGalleries(){
    global $wpdb, $current_user;
    $oqey_galls = $wpdb->prefix . "oqey_gallery";
    
    if ( !current_user_can('oQeyGalleries') ) die(__('You do not have sufficient permissions to do this.'));
    
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
     $r .= '</tbody></table>';
     echo $r;
     die();
}
/*END all galleries list*/

/*Preview gallery*/
add_action( 'wp_ajax_oQeyGetPreviewGallery', 'oQeyGetPreviewGallery' );
function oQeyGetPreviewGallery(){
    global $wpdb, $current_user;
    
    $oqey_galls = $wpdb->prefix . "oqey_gallery";
    $oqey_images = $wpdb->prefix . "oqey_images";
    $oqey_skins = $wpdb->prefix . "oqey_skins";
    
    if ( !current_user_can('oQeyGalleries') ) die(__('You do not have sufficient permissions to do this.'));

    $galleryID = esc_sql($_POST['previewid']);
    
    $gal = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $oqey_galls WHERE id = %d ", $galleryID ) );
    
    $folder = $gal->folder;
    
    if($gal->skin_id!="0"){
        
       $skin = $wpdb->get_row("SELECT * FROM $oqey_skins WHERE id = '".$gal->skin_id."'");
    
    }else{
    
       $skin = $wpdb->get_row("SELECT * FROM $oqey_skins WHERE status = '1'");
    
    }
    
    $oqey_height = get_option('oqey_height');
    $oqey_width = get_option('oqey_width');
    $oqey_bgcolor = get_option('oqey_bgcolor');
    $plugin_url = oQeyPluginUrl();
    $plugin_repo_url = oQeyPluginRepoUrl();
    $show = "yes";

  	$get_bg_img = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $oqey_galls WHERE id = %d ", $galleryID ));			  
    $allimgs = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $oqey_images WHERE gal_id = %d ORDER BY img_order ASC", $galleryID ));

    $out .= '<div align="center" class="nofimg">';
     
    if(!empty($allimgs)){
         
         foreach($allimgs as $i){ 
            
             if($i->img_type=="nextgen"){
                $thumb = get_option('siteurl').'/'.trim($i->img_path).'/thumbs/thumbs_';
             }else{
                $thumb = get_option('siteurl').'/wp-content/oqey_gallery/galleries/'.oqey_getBlogFolder($wpdb->blogid).$folder.'/galthmb/';       
             }  
         
             $out .= '<span class="noflashimg"><img src="'.$thumb.$i->title.'" alt="'.$i->alt.'"/></span>';
         }
         
      }else{
             echo '<span style="margin:10px; height:300px;">No images. Please upload some pictures.</span>';
             $show = "no";
      }
      $out .= '</div>';		  
      
      if($show=="yes"){
        
      if($_POST['flash']=="no" || empty($skin) ){
         echo '<div style="float:left;">'.$out.'</div>';
      }else{
         
         echo '<object height="600" width="896">
               <param value="#ffffff" name="bgcolor">
               <param value="true" name="allowFullScreen">
               <param name="movie" value="'.$plugin_repo_url.'/skins/'.$skin->folder.'/'.$skin->folder.'.swf">
               <param name="FlashVars" value="flashId='.$skin->skinid.'&amp;FKey='.$skin->comkey.'&amp;GalleryPath='.$plugin_url.'&amp;GalleryID='.$galleryID.'&amp;FirstRun='.$skin->firstrun.'"><embed src="'.$plugin_repo_url.'/skins/'.$skin->folder.'/'.$skin->folder.'.swf" bgcolor="#ffffff" FlashVars="flashId='.$skin->skinid.'&amp;FKey='.$skin->comkey.'&amp;GalleryPath='.$plugin_url.'&amp;GalleryID='.$galleryID.'&amp;FirstRun='.$skin->firstrun.'" width="896" height="600" wmode="transparent" allowFullScreen="true">
               </embed>
               </object>';
      }
      
     }
     die();
}
/*END Preview gallery*/


/*Edit gallery title*/
add_action( 'wp_ajax_oQeyEditGalleryTitle', 'oQeyEditGalleryTitle' );
function oQeyEditGalleryTitle(){
    global $wpdb, $current_user;
    $oqey_galls = $wpdb->prefix . "oqey_gallery";
    if ( !current_user_can('oQeyGalleries') ) die(__('You do not have sufficient permissions to do this.'));

    $gal_id = str_replace("gall_id_", "", $_POST['id'] );
    $gal_id = esc_sql($gal_id);
    
    $newtitle = stripslashes_deep(trim(urldecode($_POST["value"])));

    if(!$sql=$wpdb->get_row($wpdb->prepare("SELECT * FROM $oqey_galls WHERE title = %s AND id != %d ",$newtitle, $gal_id))){ 
        $title = $newtitle;
    }else{
        $sql=$wpdb->get_row($wpdb->prepare("SELECT * FROM $oqey_galls WHERE title = %s AND id != %d ",$newtitle, $gal_id));
        $title = trim($sql->title.time());	
    }
 
    $gal = $wpdb->query( $wpdb->prepare("UPDATE $oqey_galls SET title = %s WHERE id= %d ", $title, $gal_id) );
    echo stripslashes_deep($title);
    
    die();
}
/*END Edit gallery title*/

/*CREATE new gallery*/
add_action( 'wp_ajax_oQeyNewGallery', 'oQeyNewGallery' );
function oQeyNewGallery(){
    global $wpdb, $current_user;
    $oqey_galls = $wpdb->prefix . "oqey_gallery";
    
if ( !current_user_can('oQeyGalleries') ) die(__('You do not have sufficient permissions to do this.'));

$resp = array();

if($_POST["newtitle"] != ""){	

$folder = sanitize_title(urldecode($_POST["newtitle"]));
$newtitle = esc_sql(stripslashes_deep(trim(urldecode($_POST["newtitle"]))));

if(!$sql=$wpdb->get_row($wpdb->prepare("SELECT * FROM $oqey_galls WHERE title = %s",$newtitle ))){

if($sql=$wpdb->get_row($wpdb->prepare("SELECT * FROM $oqey_galls WHERE folder = %s", $folder ))){ $folder = $folder.time(); }

$add = $wpdb->query("INSERT INTO $oqey_galls (title, author, folder) VALUES ('".$newtitle."', '".$current_user->ID."', '".$folder."' )");
$lastid = mysql_insert_id();

if($add){
   $img = OQEY_ABSPATH.'wp-content/oqey_gallery/galleries/'.oqey_getBlogFolder($wpdb->blogid).$folder.'/galimg';
   $thumb = OQEY_ABSPATH.'wp-content/oqey_gallery/galleries/'.oqey_getBlogFolder($wpdb->blogid).$folder.'/galthmb';   
   $iphone = OQEY_ABSPATH.'wp-content/oqey_gallery/galleries/'.oqey_getBlogFolder($wpdb->blogid).$folder.'/iphone';   
   wp_mkdir_p($img);
   wp_mkdir_p($thumb);
   wp_mkdir_p($iphone);
   $resp["response"] .= 'Created';
   $resp["galid"] .= $lastid;
   $resp["last_gal"] .= '<li class="li_gallery" id="gall_id_'.$lastid.'">'.$newtitle.'<br/>
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
echo json_encode($resp);

die();
}
/*END to CREATE new gallery*/


add_action( 'wp_ajax_oQeyNewGalleryID', 'oQeyNewGalleryID' );
function oQeyNewGalleryID(){
    global $wpdb, $current_user;
    $oqey_galls = $wpdb->prefix . "oqey_gallery";
    
    if ( !current_user_can('oQeyGalleries') ) die(__('You do not have sufficient permissions to do this.'));

$gal = $wpdb->get_row("SELECT * FROM $oqey_galls WHERE id ='".esc_sql($_POST['newgallid'])."'");

$resp = array();

if ( is_ssl() ){
	$cookies = $_COOKIE[SECURE_AUTH_COOKIE];
    }else{
	$cookies = $_COOKIE[AUTH_COOKIE];
}

$datele = $gal->id.'--'.$cookies.'--'.$_COOKIE[LOGGED_IN_COOKIE].'--'.$_POST['wpnonce'];

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
	  <option value="3">Reverse Order</option>
    </select>
	<input type="button" name="doapply" id="doapply" value="Apply"/>
	</div>
    </td>
    <td width="98" align="left" valign="middle"><input name="selectall" type="checkbox" id="selectall">&nbsp;<span id="seelectmessage">select all</span></td>
    
    <td width="150" align="center" valign="middle">Gallery ID: '.(int)$_POST['newgallid'].' </td>
    <td width="227 " align="center" valign="middle">Shortcode: <span style="background-color:#CCC;">[oqeygallery id='.(int)$_POST['newgallid'].'] </span></td>
    <td width="133" height="50" align="left" valign="middle">
      <div class="uploader" id="flashuploader">
      <div id="upload">Upload</div>
      <div id="status" ></div>
      <script type="text/javascript">
	  var flashvars = {BatchUploadPath:"'.base64_encode($datele).'",
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
</table>';
//author: '.wp_dropdown_users("selected=".$aid->author."&echo=0").' 
$f = OQEY_ABSPATH.'wp-content/oqey_gallery/galleries/'.oqey_getBlogFolder($wpdb->blogid).$gal->folder;
if(!is_dir($f)){
$folderexist = '<p class="error">Error creating the gallery folder (might be a server restrictions issue). Please create this folder manually (wp-content/oqey_gallery/galleries/'.oqey_getBlogFolder($wpdb->blogid).$gal->folder.') and set permissions to 755.</p>';
}

$resp["galid"] .= $_POST['newgallid'];
$resp["titlul"] .= $gal->title;
$resp["noflashinfo"] = base64_encode($datele);
$resp["folder"] .= $gal->folder;
$resp["folderexist"] .= $folderexist;

echo json_encode($resp);

die();
}


add_action( 'wp_ajax_oQeyAddUserID', 'oQeyAddUserID' );
function oQeyAddUserID(){
    global $wpdb, $current_user;
    $oqey_galls = $wpdb->prefix . "oqey_gallery"; 
    if ( !current_user_can('oQeyGalleries') ) die(__('You do not have sufficient permissions to do this.'));
    $gal = $wpdb->query( $wpdb->prepare("UPDATE $oqey_galls SET author = '%d' WHERE id='%d'", $_POST['usrid'], $_POST['gid']) );
    die();
}

/*Show all gallery images*/
add_action( 'wp_ajax_oQeyGetAllImages', 'oQeyGetAllImages' );
function oQeyGetAllImages(){
   global $wpdb, $current_user;
   $oqey_galls = $wpdb->prefix . "oqey_gallery";
   $oqey_images = $wpdb->prefix . "oqey_images";
    
   $oqeyImagesRoot = get_option('siteurl').'/wp-content/oqey_gallery/galleries/'.oqey_getBlogFolder($wpdb->blogid);
    
   if ( !current_user_can('oQeyGalleries') ) die(__('You do not have sufficient permissions to do this.'));
    
   $resp = array();

   $images = $wpdb->get_results("SELECT * FROM $oqey_images WHERE gal_id ='".esc_sql($_POST['id'])."' AND status !=2 ORDER BY img_order ASC, id DESC");
   $folder = oqey_get_gallery_folder($_POST['id'])."/galthmb/";
   $imgFolder = oqey_get_gallery_folder($_POST['id'])."/galimg/";
   $cropFolder = oqey_get_gallery_folder($_POST['id'])."/iphone/";

   $s = $wpdb->get_row("SELECT splash_img FROM $oqey_galls WHERE id ='".esc_sql($_POST['id'])."'");

   if(!empty($images)){
   $resp["allimages"] .=  '<div class="allimages">
                           <ul id="sortablegalls">';
   $i=0;
   $preload = array(); 
   foreach($images as $img){
   if($s->splash_img==$img->id){ $b=' style="border:#7A82DE thin solid;" class="imgsel"'; }

   /*cropper process*/
   $croper="";
   
   
   if($img->img_type=="nextgen"){
     $fullimg = get_option('siteurl').'/'.$img->img_path.'/thumbs/thumbs_'.trim($img->title);
     $previewimg = get_option('siteurl').'/'.trim($img->img_path).'/'.trim($img->title);
     
     }else{
     $fullimg = $oqeyImagesRoot.$folder.trim($img->title);
     $previewimg = $oqeyImagesRoot.$imgFolder.trim($img->title);
    }
    
   
   if ( current_user_can('oQeyCropper') ){ 
   if (is_plugin_active('oqey-photo-cropper/oqeycropper.php') || is_plugin_active('oqey-addons/oqeyaddons.php') ){
   
   if($img->img_type=="nextgen"){
     $iphoneimg = OQEY_ABSPATH.'/'.$img->img_path.'/'.trim($img->title);
     $iphoneimgthumb = OQEY_ABSPATH.'/'.$img->img_path.'/thumbs/thumbs_'.trim($img->title);
     
     $cropimage = get_option('siteurl').'/'.$img->img_path.'/'.trim($img->title);
     $cropimagethmb = get_option('siteurl').'/'.$img->img_path.'/thumbs/thumbs_'.trim($img->title);   
     
   }else{
     $iphoneimg = OQEY_ABSPATH.'wp-content/oqey_gallery/galleries/'.oqey_getBlogFolder($wpdb->blogid).$cropFolder.trim($img->title);
     $iphoneimgthumb = OQEY_ABSPATH.'wp-content/oqey_gallery/galleries/'.oqey_getBlogFolder($wpdb->blogid).$folder.trim($img->title);
     
     $cropimage = $oqeyImagesRoot.$cropFolder.$img->title;
     $cropimagethmb = $oqeyImagesRoot.$folder.$img->title;
   }
   list($twidth, $theight) = @getimagesize($iphoneimg);
   list($ttwidth, $ttheight) = @getimagesize($iphoneimgthumb);
    
   $croper ='<a href="#image" onclick="showCropImage(\''.$img->id.'\', \''.$twidth.'\', \''.$theight.'\',\''.$ttwidth.'\', \''.$ttheight.'\', \''.urlencode(trim($cropimage)).'\', \''.urlencode(trim($cropimagethmb)).'\'); return false;">
             <img src="'.oQeyPluginUrl().'/images/cropperbutton.png" width="14" height="14" alt="image" title="crop image"/></a>';    
    }
   }/*END with croper icon*/


   $resp["allimages"] .= '
                       <li id="img_li_'.$img->id.'"'.$b.'><div class="allbut" align="center">
                       <a href="#delete" onclick="deleteImage(\''.$img->id.'\'); return false;"><img src="'.oQeyPluginUrl().'/images/remove_small_button.png" width="14" height="14" alt="move to trash" title="move to trash"/></a>  		  
		               <a href="#image_details" onclick="showSettings(\''.$img->id.'\'); return false;"><img src="'.oQeyPluginUrl().'/images/settings_small_button.png" width="14" height="14" alt="image details" title="image details"/></a>
		               <a href="#image" onclick="showImage(\''.$img->id.'\', \''.urlencode(trim($previewimg)).'\'); return false;"><img src="'.oQeyPluginUrl().'/images/details_small_button.png" width="14" height="14" alt="image" title="preview image"/></a>
                       '.$croper.'
		               <input name="selected" type="checkbox" value="'.$img->id.'" class="styled" id="selected_'.$img->id.'">
		               </div>
					   <img src="'.oQeyPluginUrl().'/images/separator_line.png" width="120" height="2" align="middle" class="img_thumbs_top_line"/>
		               <img src="'.$fullimg.'?'.time().'" alt="image_'.$img->id.'" class="img_thumbs" />
                       </li>';
    $b = "";
    $preload[$i] = $previewimg;
    $i++;
    }
      $resp["allimages"] .=  '</ul></div>';
    }else{
      $resp["allimages"] .=  '<div class="allimages">';
      $resp["allimages"] .=  'No images. Please upload some pictures.';	
      $resp["allimages"] .=  '</div>';
    }

      $resp["allimgpreload"] = $preload;
      
      echo json_encode($resp);
      
      die();
}
/*END to show all gallery images*/

add_action( 'wp_ajax_oQeyOrderAllImages', 'oQeyOrderAllImages' );
function oQeyOrderAllImages(){
   global $wpdb, $current_user;
   $oqey_images = $wpdb->prefix . "oqey_images";
   
   if ( !current_user_can('oQeyGalleries') ) die(__('You do not have sufficient permissions to do this.'));
    
   $r = str_replace("img_li[]=", "", $_POST['orderallimgs']);
   $gal_img_id = esc_sql($_POST['galleryid']);
   $r =  explode("&", $r);
   if(isset($_REQUEST['imgreverse']) && $_REQUEST['imgreverse']=="yes"){
      $r = array_reverse($r);
   }
   
   foreach ($r as $position => $item){
      $uimgs = $wpdb->query( $wpdb->prepare("UPDATE $oqey_images SET img_order = '%d' WHERE id = '%d' AND gal_id = '%d'", $position, $item, $gal_img_id) );
   }
   die();
}


add_action( 'wp_ajax_oQeyOrderAndSelectMusic', 'oQeyOrderAndSelectMusic' );
function oQeyOrderAndSelectMusic(){
   global $wpdb, $current_user;
   $oqey_music = $wpdb->prefix . "oqey_music";
   $oqey_music_rel = $wpdb->prefix . "oqey_music_rel";

$gal_id = esc_sql($_POST['music_gall_id']);

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
	echo '<li><input type="checkbox" checked="checked" value="'.$music->music_id.'" name="check_music_'.$music->music_id.'" id="check_music_'.$music->music_id.'"> '.$music->title.'</li>';	
	$interogare .= "id !='".$music->music_id."' AND ";
}
$get_music_list_all = $wpdb->get_results("SELECT * FROM $oqey_music WHERE ".$interogare." status !=2");

foreach ($get_music_list_all as $musicall){
	echo '<li><input type="checkbox" value="'.$musicall->id.'" name="check_music_'.$musicall->id.'" id="check_music_'.$musicall->id.'"> '.$musicall->title.'</li>';
}

echo '</ul>
      </form>      
	  </div>
	  <input type="button" name="savemusic" id="savemusic" value="Save changes" class="savemusic" style=" margin-left:20px; margin-bottom:10px;"/>
';

die();
}



add_action( 'wp_ajax_oQeySaveMusicOrder', 'oQeySaveMusicOrder' );
function oQeySaveMusicOrder(){
   global $wpdb, $current_user;
   $oqey_music_rel = $wpdb->prefix . "oqey_music_rel";
    
    if ( !current_user_can('oQeyGalleries') ) die(__('You do not have sufficient permissions to do this.'));
    
  $delete_music = $wpdb->query("DELETE FROM $oqey_music_rel WHERE gallery_id = '".esc_sql($_POST['mgalleryid'])."'");
  $i=0;

  if(!empty($_POST['selectedmusic'])){
    
   foreach ($_POST['selectedmusic'] as $l => $d){
   
   $min = $wpdb->query("INSERT INTO $oqey_music_rel (music_id, gallery_id, mrel_order) VALUES ('".$d['value']."', '".esc_sql($_POST['mgalleryid'])."', '".$i."')");
   $i++;
   
   }//end foreach
  }
  
   echo "Changes are saved.";
   
   die();
}


add_action( 'wp_ajax_oQeyGetAllSkins', 'oQeyGetAllSkins' );
function oQeyGetAllSkins(){
   global $wpdb, $current_user;
   
   $oqey_galls = $wpdb->prefix . "oqey_gallery";
   $oqey_skins = $wpdb->prefix . "oqey_skins";
    
    //if ( !current_user_can('oQeySkins') ) die(__('You do not have sufficient permissions to do this.'));

   $s = $wpdb->get_row("SELECT skin_id, status FROM $oqey_galls WHERE id = '".esc_sql($_POST['skin_gall_id'])." ' ");

   if($s->skin_id !=0 && $s->status!=2){

      $r = $wpdb->get_row("SELECT * FROM $oqey_skins WHERE id = '".$s->skin_id."'");

   }else{
      
      $r = $wpdb->get_row("SELECT * FROM $oqey_skins WHERE status = '1'");

   }

if(!empty($r)){
 
echo '<table width="900" border="0" cellspacing="0" cellpadding="0" id="currentskintable">
      <thead>
        <tr>
          <th colspan="2" align="left" valign="middle" style="padding:15px;">Current skin</th>
        </tr>
      </thead>
      <tbody id="sortable">
	    <tr id="skink_tr_'.$r->id.'">
             <td width="180" height="120" align="center" valign="middle">
			   <div align="center"><img src="'.oQeyPluginRepoUrl().'/skins/'.$r->folder.'/'.$r->folder.'.jpg" alt="skin" width="150" height="100" style="border:#999999 solid thin;"/>
	           </div></td>
               <td width="720" align="left" valign="top">
               <p align="left" style="padding:5px;"><b>'.urldecode($r->name).'</b><br/>'.urldecode($r->description).'<br/>
               Skin files location: <code>/skins/'.$r->folder.'</code>.
               </p> 
	          </td>       
        </tr>
      </tbody>
      </table>';
}else{
    
   echo '<table width="900" border="0" cellspacing="0" cellpadding="0" id="currentskintable">
          <thead>
            <tr>
             <th align="left" valign="middle" style="padding:15px;">Current skin</th>
            </tr>
           </thead>
          <tbody id="sortable">
	       <tr>
             <td width="180" height="120" align="center" valign="middle">
			 <p>No skins found.</p> 
             </td>       
           </tr>
          </tbody>
         </table>';
}

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
	         <p align="left" style="padding:5px;"><b>'.urldecode($r->name).'</b><br/>'.urldecode($r->description).'<br/>
             Skin files location: <code>/skins/'.$r->folder.'</code>.<br/><br/>
			 <a href="#activate_skin" class="activate_skin" id="'.$r->id.'">Activate this skin</a>
        </p> 
	    </td>       
        </tr>';
	}
}else{ 
echo '<tr id="skink_tr_'.$r->id.'"><td align="center" height="30" valign="middle">No available skins.</td></tr>';
}

echo '</tbody>
      </table>
	  ';
      
      die();
}



add_action( 'wp_ajax_oQeySetNewSkin', 'oQeySetNewSkin' );
function oQeySetNewSkin(){
    global $wpdb, $current_user;
    $oqey_galls = $wpdb->prefix . "oqey_gallery";   
    $s = $wpdb->query( $wpdb->prepare("UPDATE $oqey_galls SET skin_id = '%d' WHERE id = '%d'", $_POST['skinid'], $_POST['skin_gallery_id']) ); 
    echo "New skin was set.";
    die();
}

/*Move all selected images to trash*/
add_action( 'wp_ajax_oQeyImagesToTrash', 'oQeyImagesToTrash' );
function oQeyImagesToTrash(){
   global $wpdb, $current_user;
   $oqey_images = $wpdb->prefix . "oqey_images";
   if ( !current_user_can('oQeyGalleries') ) die(__('You do not have sufficient permissions to do this.'));
    
   $i=0;
   $all = explode(",", urldecode($_POST['imgalltotrash']));

   foreach ($all as $id){
     $img = $wpdb->query( $wpdb->prepare( "UPDATE $oqey_images SET status = '%d' WHERE id = %d ", 2, $id ) ); 
     $i++;
   }
   
   if($i>0){
      echo "All selected images was moved to trash.";
   }else{
      echo "Please select an image.";
   }
   die();
}
/*END to Move all selected images to trash*/

add_action( 'wp_ajax_oQeyImagesFromTrash', 'oQeyImagesFromTrash' );
function oQeyImagesFromTrash(){
   global $wpdb, $current_user;
   $oqey_images = $wpdb->prefix . "oqey_images";
   if ( !current_user_can('oQeyGalleries') ) die(__('You do not have sufficient permissions to do this.'));
    
   $resp = array();
   $galid = str_replace("trashdiv", "", $_POST['galid']);
   $all = explode(",", urldecode($_POST['imgallfromtrash']));

   $i=0;
   foreach ($all as $id){
      $img = $wpdb->query( $wpdb->prepare("UPDATE $oqey_images SET status = '%d' WHERE id = '%d'", 0, $id) ); 
      $i++;
   }
   if($i>0){
   if(!$sql=$wpdb->get_row($wpdb->prepare("SELECT id FROM $oqey_images WHERE gal_id = '%d' AND status = '%d'", esc_sql($galid), "2" ))){ $e="no"; }
   $resp["statusul"] = $e;
   $resp["imgallfromtrash"] = $all;//$_POST['imgallfromtrash'];
   echo json_encode($resp);
   }
   die();
}

/*Gallery to trash*/
add_action( 'wp_ajax_oQeyGalleryToTrash', 'oQeyGalleryToTrash' );
function oQeyGalleryToTrash(){
   global $wpdb, $current_user;
   $oqey_galls = $wpdb->prefix . "oqey_gallery";
   if ( !current_user_can('oQeyGalleries') ) die(__('You do not have sufficient permissions to do this.'));
   $img = $wpdb->query( $wpdb->prepare("UPDATE $oqey_galls SET status = '%d' WHERE id = '%d'", 2, $_POST['movetotrashgall']) ); 
   echo "Gallery was moved to trash.";
   die();
}
/*End Gallery to trash*/

/*Gallery from trash*/
add_action( 'wp_ajax_oQeyGalleryFromTrash', 'oQeyGalleryFromTrash' );
function oQeyGalleryFromTrash(){
   global $wpdb, $current_user;
   $oqey_galls = $wpdb->prefix . "oqey_gallery";    
   if ( !current_user_can('oQeyGalleries') ) die(__('You do not have sufficient permissions to do this.'));
    
   $resp = array();
   $gal = $wpdb->query( $wpdb->prepare("UPDATE $oqey_galls SET status = '%d' WHERE id = '%d'", 0, $_POST['undogallid']) ); 

   if($gal){
   if(!$sql=$wpdb->get_row($wpdb->prepare("SELECT id FROM $oqey_galls WHERE status = '%d' ", "2" ))){ $e="no"; }
   $resp["statusul"] = $e;
   $resp["mesaj"] = "Gallery was restored.";
   echo json_encode($resp);	
   }
   die();
}
/*END Gallery from trash*/

/*Image to trash*/
add_action( 'wp_ajax_oQeyImageToTrash', 'oQeyImageToTrash' );
function oQeyImageToTrash(){
   global $wpdb, $current_user;
   $oqey_images = $wpdb->prefix . "oqey_images";
   if ( !current_user_can('oQeyGalleries') ) die(__('You do not have sufficient permissions to do this.'));
   $img = $wpdb->query( $wpdb->prepare("UPDATE $oqey_images SET status = '%d' WHERE id = '%d'", 2, $_POST['delimgid']) ); 
   echo 'Image was moved to trash.';
   die();
}
/*END Image to trash*/

/*Image from trash*/
add_action( 'wp_ajax_oQeyImageFromTrash', 'oQeyImageFromTrash' );
function oQeyImageFromTrash(){
   global $wpdb, $current_user;
   $oqey_images = $wpdb->prefix . "oqey_images";
   
   if ( !current_user_can('oQeyGalleries') ) die(__('You do not have sufficient permissions to do this.'));
   $img = $wpdb->query( $wpdb->prepare("UPDATE $oqey_images SET status = '%d' WHERE id='%d'", 0 , $_POST['undoimgid']) );
   echo 'Image was restored.';
   die();
}
/*END Image from trash*/

/*GET image details, alt, comments, link etc*/
add_action( 'wp_ajax_oQeyImageDetails', 'oQeyImageDetails' );
function oQeyImageDetails(){
   global $wpdb, $current_user;
   $oqey_galls = $wpdb->prefix . "oqey_gallery";
   $oqey_images = $wpdb->prefix . "oqey_images";	
    
   if ( !current_user_can('oQeyGalleries') ) die(__('You do not have sufficient permissions to do this.'));    

   $resp = array();
   $splash = 'off';
   $splashexclusive = 'off';
   
   $image = $wpdb->get_row("SELECT * FROM $oqey_images WHERE id ='".esc_sql($_POST['imagedetails'])."'");

   if($sql=$wpdb->get_row($wpdb->prepare("SELECT splash_img FROM $oqey_galls WHERE id = '%d' AND splash_img = %s",$image->gal_id, esc_sql($_POST['imagedetails'])  ))){
   $splash = 'on';

   $s = $wpdb->get_row("SELECT splash_only FROM $oqey_galls WHERE id ='".$image->gal_id."'");
   if($s->splash_only=="1"){ $splashexclusive = 'on'; }
   }

   $resp['alt'] .= $image->alt;
   $resp['comments'] .= $image->comments;
   $resp['splash'] .= $splash;
   $resp['splashexclusive'] .= $splashexclusive;
   $resp['link'] .= $image->img_link;

   echo json_encode($resp);
   die();
}
/*END image details, alt, comments, link etc*/

/*Update image details alt, etc*/
add_action( 'wp_ajax_oQeyUpdateImageDetails', 'oQeyUpdateImageDetails' );
function oQeyUpdateImageDetails(){
   global $wpdb, $current_user;
   $oqey_galls = $wpdb->prefix . "oqey_gallery";
   $oqey_images = $wpdb->prefix . "oqey_images";	
   if ( !current_user_can('oQeyGalleries') ) die(__('You do not have sufficient permissions to do this.'));
   
   $resp = array();

   $alt_up = $wpdb->query( $wpdb->prepare("UPDATE $oqey_images SET alt = %s, comments = %s, img_link = %s WHERE id = %d ", stripslashes_deep(urldecode($_POST['alt'])), stripslashes_deep(urldecode($_POST['comments'])), stripslashes_deep(urldecode($_POST['oqey_image_link'])), esc_sql($_POST['imgid']) ) ); 

   $m = "not";
   $c = $wpdb->get_row( $wpdb->prepare( "SELECT splash_img FROM $oqey_galls WHERE id = %d ", esc_sql($_POST['galid']) ) );

   if($c->splash_img==$_POST['imgid']){
   $splashk = $wpdb->query( $wpdb->prepare("UPDATE $oqey_galls SET splash_img = %d, splash_only = %d WHERE id = %d ", 0, 0, esc_sql($_POST['galid']) ));
   }

   if(!empty($_POST['splash']) && !empty($_POST['splashexclusive']) ){
    
   $splashk = $wpdb->query( $wpdb->prepare("UPDATE $oqey_galls SET splash_img = %d, splash_only = %d WHERE id = %d ", esc_sql($_POST['imgid']), 1, esc_sql($_POST['galid']) ));
   $m = "yes";
   
   }elseif(!empty($_POST['splash'])){	 
   $splashk = $wpdb->query( $wpdb->prepare("UPDATE $oqey_galls SET splash_img = %d, splash_only = %d WHERE id = %d ", esc_sql($_POST['imgid']), 0, esc_sql($_POST['galid']) ));   
   $m = "yes";
   }
   
   $resp['splash'] .= $m;
   $resp['id'] .= $_POST['imgid'];

   echo json_encode($resp);
   die();
}
/*END image details update*/

/*END galleries ajax*/

/*START skins ajax*/

/*Get more skins from oqesites.com*/
add_action( 'wp_ajax_oQeyGetNewSkins', 'oQeyGetNewSkins' );
function oQeyGetNewSkins(){
global $wpdb, $current_user;
   $oqey_skins = $wpdb->prefix . "oqey_skins";
   if ( !current_user_can('oQeySkins') ) die(__('You do not have sufficient permissions to do this.'));
	
   $xml = @simplexml_load_file('http://oqeysites.com/skinsxml/skins.xml');    

    if(!empty($xml)){

       echo '<table class="tablesorter" border="0" cellpadding="15" cellspacing="0" width="900">
             <tbody id="sortable">';
	   $e = 0;	 
	   foreach($xml as $x){

       if(!$sql = $wpdb->get_row( $wpdb->prepare( "SELECT id FROM $oqey_skins WHERE skinid = %s ", mysql_real_escape_string($x->skinid) ))){		   
	   
       $install = '<a href="#install_skin" class="install_skin" id="'.$x->archivename.'" >Install</a>';
       if($x->price!="" && $x->commercial=="yes"){ $price = 'Skin price: $'.urldecode($x->price).'<br/>'; }else{ $discount_code = "<br/>"; }
	   if($x->commercial=="yes"){ $info = ' - Commercial skin'; }else{ $info = ' - Free skin'; }
       $preview = '<a href="#preview_skin" class="preview_skin" id="'.$x->archivename.'" title="Preview '.urldecode($x->title).'" >Preview</a>';
       
	   echo '<tr>
             <td align="center" valign="middle" width="170" height="120">
			 <img src="http://oqeysites.com/skinsxml/'.$x->archivename.'/'.$x->archivename.'.jpg" alt="skin" style="border: thin solid rgb(153, 153, 153); margin-left: 15px;" width="150" height="100">
			 </td>
             <td style="margin-left: 10px; padding: 5px;" align="left" valign="top" width="730">
             <h4>'.urldecode($x->title).$info.'</h4>
             <p>'.urldecode($x->description).'
             <br/>'.$price.'  </p>           
			 <p>'.$install.' | '.$preview.'</p>
			 </td></tr>';
             $price = "";
             $e ++;
	    }         
	  }
      if($e==0){ echo '<tr><td valign="middle" style="height:30px; padding-left: 20px;">All available skins are already installed...</tr></td>'; }
      echo '</tbody></table>';	
            
	}else{
	  echo "<a href='http://oqeysites.com/category/wp-photo-gallery-skins/'>View more skins...</a>";	
	}    
    die();
}
/*END Get more skins from oqesites.com*/


/*install new skin*/
add_action( 'wp_ajax_oQeyInstallNewSkins', 'oQeyInstallNewSkins' );
function oQeyInstallNewSkins(){
    global $wpdb, $current_user;
    
    if ( !current_user_can('oQeySkins') ) die(__('You do not have sufficient permissions to do this.'));
	if ( !wp_verify_nonce($_POST['nonce'], 'oqey-install-skin') ) die("Security check");
	
    //include(OQEY_ABSPATH."wp-admin/includes/file.php");

    $url = "http://oqeysites.com/skinsxml/archives/".$_POST['install_new_skin'].".zip";
    $arhiva = download_url($url);
    $arhiva = str_replace("\\", "/", $arhiva);

      if( class_exists('ZipArchive') ){
			$zip = new ZipArchive;
	        $zip_file = $arhiva;
            $zip->open($zip_file);
	        $zip_extract = OQEY_ABSPATH.'wp-content/oqey_gallery/skins/'.oqey_getBlogFolder($wpdb->blogid);
            $zip->extractTo($zip_extract);
            $zip->close();
		}else{
			require_once(OQEY_ABSPATH . 'wp-admin/includes/class-pclzip.php');			
			$archive = new PclZip($arhiva);
            $list = $archive->extract($skins_dir);
            if ($list == 0) {
            die("ERROR : '".$archive->errorInfo(true)."'");
            }
		}

    if(unlink($arhiva)){ echo "ok"; }
    die();	
}
/*END install new skin*/

/*preview skin*/
add_action( 'wp_ajax_oQeyPreviewNewSkin', 'oQeyPreviewNewSkin' );
function oQeyPreviewNewSkin(){
   global $wpdb, $current_user;
   if ( !current_user_can('oQeySkins') ) die(__('You do not have sufficient permissions to do this.'));
   
   echo '
   <object height="598" width="896">
   <param value="#ffffff" name="bgcolor">
   <param value="true" name="allowFullScreen">
   <param name="movie" value="http://oqeysites.com/skinsxml/'.$_POST['get_the_preview'].'/'.$_POST['get_the_preview'].'.swf">
   <param name="FlashVars" value="flashId=fid&amp;FKey='.$_POST['comkey'].'&amp;GalleryPath=http://oqeysites.com/skinsxml/&amp;GalleryID=1&amp;FirstRun=0">
   <embed src="http://oqeysites.com/skinsxml/'.$_POST['get_the_preview'].'/'.$_POST['get_the_preview'].'.swf" bgcolor="#ffffff" FlashVars="flashId=fid&amp;FKey='.$_POST['comkey'].'&amp;GalleryPath=http://oqeysites.com/skinsxml/&amp;GalleryID=1&amp;FirstRun=0" width="896" height="598" wmode="transparent" allowFullScreen="true"></embed>
   </object>';
   die(); 
}
/*END preview skin*/

/*get all skins*/
add_action( 'wp_ajax_oQeyGetAllInstalledSkins', 'oQeyGetAllInstalledSkins' );
function oQeyGetAllInstalledSkins(){
   global $wpdb, $current_user;
   $oqey_skins = $wpdb->prefix . "oqey_skins";
   if ( !current_user_can('oQeySkins') ) die(__('You do not have sufficient permissions to do this.'));

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
			 <h4>'.urldecode($r->name).$comm.'</h4>
             '.urldecode($r->description).'<br/>
             Skin files location: <code>/skins/'.$r->folder.'</code>.
			 <p><a href="#set_as_default" class="set_as_default" id="'.$r->id.'">Set as default</a> | <a href="#delete_this_skin" class="delete_this_skin" id="'.$r->id.'">Move to trash</a></p>
             </td>
             <td width="270" align="left" valign="top" style="margin-left:10px; padding:5px;">';
		
		if($r->commercial=="yes"){
		
        if($r->firstrun==0){ 
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
              <input type="hidden" name="s" value="'.$r->skinid.'" />
              <input type="hidden" name="skinfolder" value="'.$r->folder.'" />';
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
        
        echo '</tbody></table>';
        die();
}
/*END get all skins*/

/*Save skin key*/
add_action( 'wp_ajax_oQeySaveSkinKey', 'oQeySaveSkinKey' );
function oQeySaveSkinKey(){
   global $wpdb, $current_user;
   $oqey_skins = $wpdb->prefix . "oqey_skins";
   
   if ( !current_user_can('oQeySkins') ) die(__('You do not have sufficient permissions to do this.'));
  
   $upd = $wpdb->query( $wpdb->prepare("UPDATE $oqey_skins SET comkey = %s WHERE id = %d ", esc_sql($_POST['comkey']), esc_sql($_POST['savekey']) )); 
   
   if($upd){
      echo 'Skin key was saved.';
   }
   die();
}
/*END save key*/

/*single skin move to trash*/
add_action( 'wp_ajax_oQeySkinToTrash', 'oQeySkinToTrash' );
function oQeySkinToTrash(){
   global $wpdb, $current_user;
   $oqey_skins = $wpdb->prefix . "oqey_skins";  
   if ( !current_user_can('oQeySkins') ) die(__('You do not have sufficient permissions to do this.'));
    
   $s = $wpdb->query( $wpdb->prepare("UPDATE $oqey_skins SET status = %d WHERE id = %d ", 2, esc_sql($_POST['movetotrashskin']) ));
   echo 'Skin was moved to trash.';
   die();
}
/*End skin to trash*/

/*single skin move to trash*/
add_action( 'wp_ajax_oQeySkinFromTrash', 'oQeySkinFromTrash' );
function oQeySkinFromTrash(){
   global $wpdb, $current_user;
   $oqey_skins = $wpdb->prefix . "oqey_skins";
   if ( !current_user_can('oQeySkins') ) die(__('You do not have sufficient permissions to do this.'));
    
   $resp = array();
   $s = $wpdb->query( $wpdb->prepare("UPDATE $oqey_skins SET status = %d WHERE id = %d", 0, esc_sql($_POST['undoskin']) ));

   if($s){
   if(!$sql=$wpdb->get_row($wpdb->prepare("SELECT id FROM $oqey_skins WHERE status = %d ", "2" ))){ $e="no"; }
   $resp["statusul"] = $e;
   $resp["raspuns"] = 'Skin was restored.';
   echo json_encode($resp);
   }
   die();
}
/*End skin to trash*/

/*END skins ajax*/

/*MUSIC*/

/*Song move to trash*/
add_action( 'wp_ajax_oQeySongToTrash', 'oQeySongToTrash' );
function oQeySongToTrash(){
   global $wpdb, $current_user;
   $oqey_music = $wpdb->prefix . "oqey_music";
   if ( !current_user_can('oQeyMusic') ) die(__('You do not have sufficient permissions to do this.'));
   
   $s = $wpdb->query( $wpdb->prepare( "UPDATE $oqey_music SET status = '%d' WHERE id = '%d' ", 2, esc_sql($_POST['songtotrash']) ));
   echo trim($_POST['songtotrash']);
   die();
}
/*End song to trash*/

/*Song move from trash*/
add_action( 'wp_ajax_oQeySongFromTrash', 'oQeySongFromTrash' );
function oQeySongFromTrash(){
   global $wpdb, $current_user;
   $oqey_music = $wpdb->prefix . "oqey_music";
   if ( !current_user_can('oQeyMusic') ) die(__('You do not have sufficient permissions to do this.'));
   
   $s = $wpdb->query( $wpdb->prepare("UPDATE $oqey_music SET status = '%d' WHERE id = %d ", 0, esc_sql($_POST['restoresong']) ));
   if(!$sql=$wpdb->get_row($wpdb->prepare("SELECT id FROM $oqey_music WHERE status = %d ", 2 ))){ $e="no"; }
   echo $e;
   die();
}
/*End song from trash*/


/*Rename songs titles*/
add_action( 'wp_ajax_oQeySongRename', 'oQeySongRename' );
function oQeySongRename(){
   global $wpdb, $current_user;
   $oqey_music = $wpdb->prefix . "oqey_music";
   
   if ( !current_user_can('oQeyMusic') ) die(__('You do not have sufficient permissions to do this.'));

   $id = str_replace("select_", "", $_POST['music_edit_id'] );
   $newtitle = esc_sql(stripslashes_deep(trim(urldecode($_POST["music_edit_title"]))));

   if(!$sql=$wpdb->get_row($wpdb->prepare("SELECT * FROM $oqey_music WHERE title = %s AND id != %d ",$newtitle, esc_sql($id) ))){ 
    
      $title = $newtitle;
   
   }else{
   
      $sql=$wpdb->get_row($wpdb->prepare("SELECT * FROM $oqey_music WHERE title = %s AND id != %d ",$newtitle, esc_sql($id) ));
      $title = trim($sql->title.time());	
   
   }
  
   $m = $wpdb->query( $wpdb->prepare("UPDATE $oqey_music SET title = '%s' WHERE id = '%d' ", $title, esc_sql($id) ));
   echo stripslashes_deep($title);
   die();
}
/*END music rename*/
/*END MUSIC*/

/*START TRASH*/

/*DELETE gallery images permanently*/
add_action( 'wp_ajax_oQeyImagePermDelete', 'oQeyImagePermDelete' );
function oQeyImagePermDelete(){
   global $wpdb, $current_user;
   $oqey_images = $wpdb->prefix . "oqey_images";
   if ( !current_user_can('oQeyTrash') ) die(__('You do not have sufficient permissions to do this.'));
    
   $e ="";
   $galid = str_replace("trashdiv", "", $_POST['galid']);
   $folder = oqey_get_gallery_folder($galid);

   $all = explode(",", urldecode($_POST['imgalldelete']));

   foreach ($all as $title){
    
   $d = $wpdb->query( $wpdb->prepare( "DELETE FROM $oqey_images WHERE gal_id = %d AND title = %s ", esc_sql($galid), esc_sql($title) ) );

   if(is_file(OQEY_ABSPATH.'wp-content/oqey_gallery/galleries/'.oqey_getBlogFolder($wpdb->blogid).$folder.'/galimg/'.trim($title)) ){    
      	$i = unlink(OQEY_ABSPATH.'wp-content/oqey_gallery/galleries/'.oqey_getBlogFolder($wpdb->blogid).$folder.'/galimg/'.trim($title));
	    $t = unlink(OQEY_ABSPATH.'wp-content/oqey_gallery/galleries/'.oqey_getBlogFolder($wpdb->blogid).$folder.'/galthmb/'.trim($title));
	    $t = unlink(OQEY_ABSPATH.'wp-content/oqey_gallery/galleries/'.oqey_getBlogFolder($wpdb->blogid).$folder.'/iphone/'.trim($title));
    }
   }

   if(!$sql=$wpdb->get_row($wpdb->prepare("SELECT id FROM $oqey_images WHERE gal_id = '%d' AND status = %d  ", esc_sql($galid), "2" ))){ $e="no"; }
   echo $e;
   die();
}
/*End delete gallery images*/

/*DELETE song*/
add_action( 'wp_ajax_oQeySongPermDelete', 'oQeySongPermDelete' );
function oQeySongPermDelete(){
   global $wpdb, $current_user;
   $oqey_music = $wpdb->prefix . "oqey_music";
   $oqey_music_rel = $wpdb->prefix . "oqey_music_rel";
   if ( !current_user_can('oQeyTrash') ) die(__('You do not have sufficient permissions to do this.'));
   
   $d = $wpdb->query( $wpdb->prepare( "DELETE FROM $oqey_music WHERE id = %d ", esc_sql($_POST['deletesong']) ));
   $r = $wpdb->query( $wpdb->prepare( "DELETE FROM $oqey_music_rel WHERE music_id = %d ", esc_sql($_POST['deletesong']) ) );

   if($d){
     if(is_file(OQEY_ABSPATH."wp-content/oqey_gallery/music/".oqey_getBlogFolder($wpdb->blogid).trim($_POST['name']))){    
	    $i = unlink(OQEY_ABSPATH."wp-content/oqey_gallery/music/".oqey_getBlogFolder($wpdb->blogid).trim($_POST['name']));
   }
   if(!$sql=$wpdb->get_row($wpdb->prepare("SELECT id FROM $oqey_music WHERE status = %d ", 2 ))){ $e="no"; }
   echo $e;
   }
   
   die();
}
/*End delete song*/

/*DELETE skin*/
add_action( 'wp_ajax_oQeySkinPermDelete', 'oQeySkinPermDelete' );
function oQeySkinPermDelete(){
   global $wpdb, $current_user;
   $oqey_galls = $wpdb->prefix . "oqey_gallery";
   $oqey_skins = $wpdb->prefix . "oqey_skins";
   
   if ( !current_user_can('oQeyTrash') ) die(__('You do not have sufficient permissions to do this.'));
    
   $d = $wpdb->query( $wpdb->prepare( "DELETE FROM $oqey_skins WHERE id = %d ", esc_sql($_POST['deleteskinper']) ) );
   $u = $wpdb->query( $wpdb->prepare( "UPDATE $oqey_galls SET skin_id = %d WHERE skin_id = %d ", 0, esc_sql($_POST['deleteskinper']) ) );

   if($d){
      if(is_dir(OQEY_ABSPATH."wp-content/oqey_gallery/skins/".oqey_getBlogFolder($wpdb->blogid).esc_sql($_POST['name'])) ){   
         $dir = OQEY_ABSPATH."wp-content/oqey_gallery/skins/".oqey_getBlogFolder($wpdb->blogid).esc_sql($_POST['name']);
         $do = oqey_rm($dir);
      if($do){ 
         if(!$sql=$wpdb->get_row($wpdb->prepare("SELECT id FROM $oqey_skins WHERE status = %d ", "2" ))){ $e="no"; }
         echo $e;
      }
     } 
    }
    die();
}
/*End delete song*/

/*DELETE gallery permanently*/
add_action( 'wp_ajax_oQeyGalleryPermDelete', 'oQeyGalleryPermDelete' );
function oQeyGalleryPermDelete(){
   global $wpdb, $current_user;
   $oqey_galls = $wpdb->prefix . "oqey_gallery";
   $oqey_music_rel = $wpdb->prefix . "oqey_music_rel";
   $oqey_images = $wpdb->prefix . "oqey_images";
   
   if ( !current_user_can('oQeyTrash') ) die(__('You do not have sufficient permissions to do this.'));
    
   $e="";
   $d = $wpdb->query( $wpdb->prepare("DELETE FROM $oqey_galls WHERE id = %d ", esc_sql($_POST['deletegall']) ) );
   $r = $wpdb->query( $wpdb->prepare("DELETE FROM $oqey_music_rel WHERE gallery_id = %d ", esc_sql($_POST['deletegall']) ) );
   $d = $wpdb->query( $wpdb->prepare("DELETE FROM $oqey_images WHERE gal_id = %d ", esc_sql($_POST['deletegall']) ) );   

   if($d){
      if(is_dir(OQEY_ABSPATH.'wp-content/oqey_gallery/galleries/'.fj_getBlogFolder($wpdb->blogid).esc_sql($_POST['name'])) ){   
         $dir = OQEY_ABSPATH.'wp-content/oqey_gallery/galleries/'.fj_getBlogFolder($wpdb->blogid).esc_sql($_POST['name']);
         $do = fj_rm($dir);
      if($do){ 
      if(!$sql=$wpdb->get_row($wpdb->prepare("SELECT id FROM $oqey_galls WHERE status = '%d' ", "2" ))){ $e="no"; }
          echo $e;
      }
     }  
    }
   die();
}
/*End delete gallery*/

/*END TRASH*/



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
	$icon = oQeyPluginUrl().'/images/oqeygallery.png';
    $oqeym = plugin_basename( dirname(__FILE__));
    
    add_menu_page('oQey Gallery plugin', 'oQey Gallery', 8, $oqeym, 'oqey_top_page', $icon);	  
    //add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
    
	add_submenu_page($oqeym,'oQey Gallery plugin', 'Settings', 'oQeySettings', 'oQeysettings',  'oqey_settings_page');
	//add_submenu_page(__FILE__,'oQey-Gallery plugin', 'Categories', 8, 'oQeyCategories',  'oqey_categories_page');
    add_submenu_page($oqeym, 'Galleries', 'Galleries', 'oQeyGalleries', 'oQeyGalleries', 'oqey_galleries_page');
	add_submenu_page($oqeym,'Skins', 'Skins', 'oQeySkins', 'oQeySkins',  'oqey_galleries_skin_page');
	add_submenu_page($oqeym, 'Music', 'Music', 'oQeyMusic', 'oQeyMusic', 'oqey_music_page');
    add_submenu_page($oqeym, 'Roles', 'Roles', 'oQeyRoles', 'oQeyRoles', 'oqey_roles_page');
	add_submenu_page($oqeym, 'Trash', 'Trash', 'oQeyTrash', 'oQeyTrash', 'oqey_trash_page');	
    
    //add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );

}


function oqey_top_page() {
?>
<div class="wrap">
        	<h2>oQey Gallery plugin</h2>
</div>

<div class="metabox-holder has-right-sidebar">
<!--
<div class="inner-sidebar" style="margin-right:30px;">
 <div id="side-sortables" class="meta-box-sortabless ui-sortable" style="position: relative;">
  <div id="sm_pnres" class="postbox" >
				<h3 class="hndle"><span>LINKS</span></h3>
				<div class="inside">
				  <p>&nbsp;</p>
				  <p>few links</a>
			      </p>
				</div>
			</div>
</div>
</div>

-->
										
<div class="has-sidebar sm-padded">
	<div id="post-body-content" class="has-sidebar-content">
		<div class="meta-box-sortabless">
        
<div class="postbox" style="width:870px;">
				<h3 class="hndle"><span>About</span></h3>

				<div class="inside" style="font-size:13px; padding:10px; text-align:justify;">
                <p>oQey Gallery is a Plugin for Wordpress that let users create and manage flash slideshows with a non-flash version of gallery built-in for all non-flash browsers and mobile devices like iPhone / iPad / iPod etc. The flash version supports music and skins, so users can change the way it looks with a few simple clicks. Customizable, commercial skins are also available as well as custom galleries for professionals. Check this out on <a href="http://oqeysites.com/" target="_blank">oqeysites.com</a></p>
<a href="http://oqeysites.com"><img style="border: none;" src="<?php echo WP_PLUGIN_URL; ?>/oqey-gallery/images/oqeybanner.jpg" /></a>
                </div>

</div>

<div class="postbox" style="width:870px;">
				<h3 class="hndle"><span>Donate</span></h3>
                
                <div class="inside" style="font-size:13px; padding:10px; text-align:justify;"> 
                <div align="left"><p>If you really like this plugin and find it useful, help to keep this plugin free and constantly updated by clicking the donate button below.</p></div>
                <div align="right">
                <form action="https://www.paypal.com/cgi-bin/webscr" method="post"><input type="hidden" name="cmd" value="_s-xclick"><input type="hidden" name="hosted_button_id" value="3ZV8CCFYAUYKJ"><input alt="PayPal - The safer, easier way to donate online!" name="submit" src="<?php echo oQeyPluginUrl(); ?>/images/btn_donate.gif" type="image"/><img src="https://www.paypal.com/en_US/i/scr/pixel.gif" alt="" width="1" border="0" height="1"/></form>
                </div>
                </div>
</div>


<div class="postbox" style="width:870px;">
				<h3 class="hndle"><span>Original WP Themes</span></h3>
                
                <div class="inside" style="font-size:13px; padding:10px; text-align:justify;"> 
	            <p>If you are interested to buy an original WP theme, oQey Sites recommends the following themes. They have a cool 1 click auto install feature and excellent after care support services. Check it out!</p>
                <div align="right"><a href="https://www.e-junkie.com/ecom/gb.php?ii=850762&c=ib&aff=155824&cl=136641" target="ejejcsingle"><img style="border:none;" src="<?php echo oQeyPluginUrl(); ?>/images/themes.jpg" /></a>
                </div>
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


//add_filter('the_content', 'gallery_embed');

//function gallery_embed($content){
  if (is_feed()) {
    
    add_shortcode( 'oqeygallery', 'add_oqey_gallery_feed' );

   }else{
    
    add_shortcode( 'oqeygallery', 'add_gallery' );
 
   }
   
 //  return $content;
//}

function add_oqey_gallery_feed($atts){
    global $oqeycounter, $post_ID, $wpdb, $post;
    
    $id = esc_sql( $atts['id'] );
    
    $oqey_galls = $wpdb->prefix . "oqey_gallery";
    $oqey_images = $wpdb->prefix . "oqey_images";
    $oqey_skins = $wpdb->prefix . "oqey_skins";

    $gal = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $oqey_galls WHERE id = %d AND status !='2'", $id ));
    if($gal){
       $folder = $gal->folder;
       $gal_title = urlencode($gal->title);

       if($gal->skin_id!="0"){ 
          $skin = $wpdb->get_row("SELECT * FROM $oqey_skins WHERE id = '".$gal->skin_id."'");
       }else{ 
          $skin = $wpdb->get_row("SELECT * FROM $oqey_skins WHERE status = '1'"); 
       }
       
       if($gal->splash_only==1){ $s = "AND id!=".$gal->splash_img; }else{ $s=""; }
       
       $all = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $oqey_images WHERE gal_id = %d AND status!=2 ".$s." ORDER BY img_order ASC", $id  ));
       
       $imgs .= '<span class="all_images">';	
    
        foreach($all as $i){ 
            
        if($i->img_type=="nextgen"){
            
          $gimg = get_option('siteurl').'/'.trim($i->img_path).'/';
        
        }else{
        
          $gimg = get_option('siteurl').'/wp-content/oqey_gallery/galleries/'.oqey_getBlogFolder($wpdb->blogid).$gal->folder.'/iphone/';
        
        }
        
        $imgs .= '<span><img src="'.$gimg.trim($i->title).'" alt="'.urlencode(trim($i->alt)).'" style="margin-top:3px;"/></span>'; 
        
        } 
        $imgs .= '</span>';
      }
       return $imgs;
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
    'GeonaBot|Gigabot|Lycos|MSRBOT|Scooter|AltaVista|IDBot|eStyle|Scrubby|yandex|facebook';
    $isCrawler = (preg_match("/$crawlers/i", $userAgent) > 0);
    return $isCrawler;
}

function oQeycrawlerFood($t){
	$t = str_replace("oqeyimg", "oQey Photo Gallery Plugin for WordPress -", urldecode($t) );
	$t = $t."<p align='center'><a href='http://oqeysites.com'>oQey Photo Gallery Plugin for WordPress</a></p>";
	return $t;
}

//add_shortcode( 'oqeygallery', 'add_gallery' );

function add_gallery($atts){
global $oqeycounter, $post_ID, $wpdb, $post, $wp_query;

if($atts['width']!=""){ $oqey_width = $atts['width']; }else{ $oqey_width = get_option('oqey_width'); }
if($atts['height']!=""){ $oqey_height = $atts['height']; }else{ $oqey_height = get_option('oqey_height'); }
if($atts['autoplay']!=""){ $oqey_autoplay = $atts['autoplay']; }else{ $oqey_autoplay = "false"; }

 
   $id = str_replace(":", "", $atts['id']); 
   $id = esc_sql( $id );
   
   $oqey_galls = $wpdb->prefix . "oqey_gallery";
   $oqey_images = $wpdb->prefix . "oqey_images";
   $oqey_skins = $wpdb->prefix . "oqey_skins";
   
   $oqey_BorderSize = get_option('oqey_BorderSize');
   $oqey_bgcolor = get_option('oqey_bgcolor');
   $plugin_url_qu = oQeyPluginUrl();
   $plugin_repo_url = oQeyPluginRepoUrl();

   $gal = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $oqey_galls WHERE id = %d AND status !='2'", $id ) );

   if($gal){
      
      $folder = $gal->folder;
      $gal_title = urlencode($gal->title);

      if($gal->skin_id!="0"){ 
        
         $skin = $wpdb->get_row("SELECT * FROM $oqey_skins WHERE id = '".$gal->skin_id."'");
         
      }else{ 
         
         $skin = $wpdb->get_row("SELECT * FROM $oqey_skins WHERE status = '1'"); 
      
      }

      if($gal->splash_only==1){ 
        
          $s = "AND id!=".$gal->splash_img; 
      
      }else{ 
        
          $s=""; 
      
      }
      
      $all = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $oqey_images WHERE gal_id = %d AND status!=2 ".$s." ORDER BY img_order ASC", $id  ));

      
      define('IBROWSER', preg_match('~(iPad|iPod|iPhone)~si', $_SERVER['HTTP_USER_AGENT']));
      if(IBROWSER){

           $gimg = get_option('siteurl').'/wp-content/oqey_gallery/galleries/'.oqey_getBlogFolder($wpdb->blogid).$gal->folder.'/iphone/';

      }else{

           $gimg = get_option('siteurl').'/wp-content/oqey_gallery/galleries/'.oqey_getBlogFolder($wpdb->blogid).$gal->folder.'/galimg/';	

      }

      $isCrawler = getUserNow($_SERVER['HTTP_USER_AGENT']); // check if is a crowler

      if ($isCrawler || (is_plugin_active('wptouch/wptouch.php') && IBROWSER)){
    
        if ($isCrawler){
           $imgs = "<p align='center'>".$gal_title."</p>";
        }else{ 
            if(is_plugin_active('wptouch/wptouch.php') && IBROWSER){
               if(get_option('oqey_gall_title_no')=="on"){
   	              $imgs = '<div style="margin-left:auto; margin-right:auto; width:100%; text-align:center;">'.urldecode($gal_title).'</div>';
	           } 
            }
        }
    
	    
        $imgs .= '<span class="all_images">';	
    
        foreach($all as $i){ 
            
        if($i->img_type=="nextgen"){
         $gimg = get_option('siteurl').'/'.trim($i->img_path).'/';
        }
        
        $imgs .= '<img src="'.$gimg.trim($i->title).'" alt="oqeyimg '.urlencode(trim($i->alt)).'" width="'.$oqey_width.'" style="margin-top:3px;"/>'; 
        
        } 
        $imgs .= '</span>'; 
        
        if ($isCrawler){ 
            
             return oQeycrawlerFood($imgs); 
             
           }else{ 
            
             return $imgs; 
           
           }
        
    }else{	
	
	if(get_option('oqey_gall_title_no')=="on"){
	   
   	   $galtitle = '<div style="margin-left:auto; margin-right:auto; width:100%; text-align:center;">'.urldecode($gal_title).'</div>';
	
    }else{ 
        
        $galtitle =""; 
    
    }
	
    
    $allimgs = array();
	
	foreach($all as $i){ 
	  
      if($i->img_type=="nextgen"){
      
          $ipath = OQEY_ABSPATH.'/'.trim($i->img_path).'/';
          $img_type = "nextgen";
          $img_f_path = urlencode(trim($i->img_path));
      
      }else{
        
          $ipath = OQEY_ABSPATH.'wp-content/oqey_gallery/galleries/'.oqey_getBlogFolder($wpdb->blogid).$gal->folder.'/iphone/';
          $img_type = "oqey";
          $img_f_path = "";
      
      }
       
	$img_path = $ipath.trim($i->title);   
    $size = @getimagesize( $img_path );
    
	if ( $size ){
	
       list($iwidth, $iheight, $itype, $iattr)= $size;
    
    }else{
	
       $iwidth = 900;
       $iheight = 600;
	
    }
    
       $img_holder_h = $oqey_width/1.5;
       $d = wp_constrain_dimensions($iwidth, $iheight, $oqey_width, $img_holder_h);

 	   $img_full_root = get_option('siteurl').'/wp-content/plugins/oqey-gallery/oqeyimgresize.php?width='.$d[0].'&amp;new_height='.$d[1].'&amp;folder='.$gal->folder.'&amp;img='.trim($i->title).'&amp;img_type='.$img_type.'&amp;img_f_path='.$img_f_path;
	   $imgs .= '[div class="oqeyimgdiv" style="background: url('.$img_full_root.') center center no-repeat; width:'.$oqey_width.'px; height:'.$img_holder_h.'px; margin-top:3px;"][/div]';
	
	}	
	if(get_option("oqey_backlinks")=="on"){ 
	
       $oqeybacklink = '<div style="font-size:11px; margin-left:auto; margin-right:auto; width:100%; text-align:center; font-family:Arial, Helvetica, sans-serif">powered by <a href="http://oqeysites.com" target="_blank">oQeySites</a></div>'; 
	
    }	
	
	if( get_option('oqey_noflash_options')=="incolums" ){  
	   
	   $incolums = "on";
	   $optouch = "off"; 
       $custom_height = "auto";
	
    }
	
    if( get_option('oqey_noflash_options')=="injsarr" ){ 
	
       $incolums = "off"; 
	   $optouch = "off"; 	
       $custom_height = $img_holder_h."px";
	
    }
	
    if( get_option('oqey_noflash_options')=="injsarrtouch" ){ 
	
       $incolums = "off"; 
	   $optouch = "on";                    
	   $custom_height = $img_holder_h."px";
    
    }
	
	$margleft = $oqey_width - 53;
    $margin_top = $img_holder_h/2-20;
    
    if(get_option('oqey_flash_gallery_true')){ $pfv = "on"; }else{ $pfv = "off"; }
    
ob_start();	
print <<< SWF
<div id="oqey_image_div{$oqeycounter}" style="position:relative; width:{$oqey_width}px; height:{$custom_height}; display:none; margin: 0 auto;">

<div style="position:absolute; left:0px; top:{$margin_top}px; z-index:99999;" class="gall_links">
<a id="prev{$oqeycounter}" href="#back" style="text-decoration:none;">
<button style="background: url({$plugin_url_qu}/images/larrow.png) center center no-repeat; width:53px; height:101px; border:none;">&nbsp;</button></a>
</div>

<div style="position:absolute; left:{$margleft}px; top:{$margin_top}px; z-index:99999;" class="gall_links">
<a id="next{$oqeycounter}" href="#next" style="text-decoration:none;">
<button style="background: url({$plugin_url_qu}/images/rarrow.png) center center no-repeat; width:53px; height:101px; border:none;">&nbsp;</button></a>
</div>

{$galtitle}

<div id="image{$oqeycounter}" style="height:auto; display:none;" class="oqey_images"></div>

{$oqeybacklink}

</div>

<script type="text/javascript">
    var flashvars{$oqeycounter} = {
                          autoplay:"{$oqey_autoplay}",
                           flashId:"{$oqeycounter}",
		                      FKey:"{$skin->comkey}",
	                   GalleryPath:"{$plugin_url_qu}",	
                         GalleryID:"{$id}-{$post->ID}",
					      FirstRun:"{$skin->firstrun}"
					 };
	var params{$oqeycounter} = {bgcolor:"{$oqey_bgcolor}", allowFullScreen:"true", wMode:"transparent"};
	var attributes{$oqeycounter} = {id: "oqeygallery{$oqeycounter}"};
	swfobject.embedSWF("{$plugin_repo_url}/skins/{$skin->folder}/{$skin->folder}.swf", "flash_gal_{$oqeycounter}", "{$oqey_width}", "{$oqey_height}", "8.0.0", "", flashvars{$oqeycounter}, params{$oqeycounter}, attributes{$oqeycounter});
</script> 

<div id="flash_gal_{$oqeycounter}" style="width:{$oqey_width}px; min-width:{$oqey_width}px; min-height:{$oqey_height}px; height:{$oqey_height}px; margin: 0 auto;">
<script type="text/javascript">
jQuery(function($) {
        var pv = swfobject.getFlashPlayerVersion();
        oqey_e(pv, {$oqeycounter}, '{$imgs}', '{$optouch}', '{$incolums}', '{$pfv}');      
});
</script>
</div>
SWF;
$output = ob_get_contents();
ob_end_clean();
$oqeycounter ++;
return $output;
}
}//end crawler check

}
?>