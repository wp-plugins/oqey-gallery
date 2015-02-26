<?php 
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'oqey-gallery-functions.php' == basename($_SERVER['SCRIPT_FILENAME'])) die ('Please do not load this page directly. Thanks!');
	
global $wpdb;

/*Get Image link*/
function oQeyGetImageLink($i, $gal){
    global $wpdb;
    
    $image = array();
    $imgFd = oQeyPluginRepoUrl();
    
    $image['alt']      = (!empty($i->alt))?urldecode(trim($i->alt)):'';
    $image['comments'] = (!empty($i->comments))?urldecode(trim($i->comments)):'';
    $image['link']     = (!empty($i->img_link))?urlencode(trim($i->img_link)):'';
    $imgType           = (!empty($i->img_type))?trim($i->img_type):'';
    $galFolder         = (!empty($gal->folder))?trim($gal->folder):'';
    $imgTitle          = (!empty($i->title))?trim($i->title):'';
    
    switch ($imgType) {
    case 'nextgen':
        $image['img']  = get_option('siteurl').'/'.trim($i->img_path).'/'.$imgTitle;
        $image['thmb'] = get_option('siteurl').'/'.trim($i->img_path).'/thumbs/thumbs_'.$imgTitle;
        break;
    
    case 'wp':
        $image['img']  = trim($i->title);
        $image['thmb'] = wp_get_attachment_thumb_url( $i->meta_data );
        break;
    
    case 'oqey':
        $image['img']  = $imgFd.'/galleries/'.oqey_getBlogFolder($wpdb->blogid).$galFolder.'/galimg/'.$imgTitle;
        $image['thmb'] = $imgFd.'/galleries/'.oqey_getBlogFolder($wpdb->blogid).$galFolder.'/galthmb/'.$imgTitle;
        break;
    
    case 'video':
        $imgroot = OQEY_ABSPATH.trim($i->img_path);        
        if(is_file($imgroot)){          
            $image['img'] = get_option('siteurl').'/'.trim($i->img_path);       
        }else{            
            $image['img'] = oQeyPluginUrl().'/images/no-2-photo.jpg';            
        }
        break;
        
}

return $image;
    
}
/*END*/

/*Javascript libraries*/
function custom_shortcode_scripts() {
	    wp_register_script('cycle2', oQeyPluginUrl().'/js/jquery.cycle2.min.js', array('jquery'),'', true);
            wp_register_script('cycle2.swipe', oQeyPluginUrl().'/js/jquery.cycle2.swipe.min.js', array('cycle2'),'', true);
            wp_register_script('oqeyjs', oQeyPluginUrl().'/js/oqey.js', array('jquery'),'', true);
            wp_enqueue_script( 'jquery.lazyload', oQeyPluginUrl().'/js/jquery.lazyload.min.js', array('jquery'), '',true );
            //wp_enqueue_script( 'galleria.history', oQeyPluginUrl().'/js/galleria.history.min.js', array('jquery'), '',true );
          
}

add_action( 'wp_enqueue_scripts', 'custom_shortcode_scripts');

function oQeyAddJavaScriptLibraries($incolumslazy){
    
    $incolumslazy = (!empty($incolumslazy))?$incolumslazy:'';
    
    wp_enqueue_script( 'cycle2' );
    wp_enqueue_script( 'cycle2.swipe' );
    wp_enqueue_script( 'oqeyjs' );
    
    
    
    if($incolumslazy=="on"){
        wp_enqueue_script( 'jquery.lazyload' );
    }
    
    //wp_enqueue_script( 'oqeyjs' );
    
}
/*End*/

function oqey_get_gallery_folder($id){
  global $wpdb;

  $oqey_galls = $wpdb->prefix . "oqey_gallery";
  $r          = $wpdb->get_row( $wpdb->prepare( "SELECT folder FROM $oqey_galls WHERE id = %d ", absint($id) ) );

  return $r->folder;

}

function oqey_get_image_name_by_id($id){
  global $wpdb;

  $oqey_images = $wpdb->prefix . "oqey_images";
  $r           = $wpdb->get_row( $wpdb->prepare( "SELECT title FROM $oqey_images WHERE id = %d ", absint($id) ) );

  return $r->title;

}

function oqey_get_skinid_by_id($id){
  global $wpdb;

  $oqey_skins = $wpdb->prefix . "oqey_skins";
  $r          = $wpdb->get_row( $wpdb->prepare( "SELECT skinid FROM $oqey_skins WHERE id = %d ", absint($id) ));
  
  return $r->skinid;

}

function oqey_get_nextgen_title($id){
  global $wpdb;

  $nggal = $wpdb->prefix . 'ngg_gallery';
  $r     = $wpdb->get_row( $wpdb->prepare( "SELECT title FROM $nggal WHERE gid = %d ", absint($id) ) );
  
  return $r->title;

}


function oqey_getBlogFolder($id){ 
    $folder=""; 
    
    if($id==0 || $id==1){ 

        $folder = "";
        
    }else{ 
        
        $folder=$id."/"; 
        
    } 
    
    return $folder; 
    
}
    
function oqey_checkPost($post_id){ 
    global $wpdb; 
    
    $check = $wpdb->get_var( $wpdb->prepare( "SELECT post_type FROM $wpdb->posts WHERE ID = %d ", esc_sql($post_id) )); 
    
    return $check; 
    
}

function oqey_uploadSize(){
	$upload_size_unit = $max_upload_size =  wp_max_upload_size();
	$sizes = array( 'KB', 'MB', 'GB' );
	for ( $u = -1; $upload_size_unit > 1024 && $u < count( $sizes ) - 1; $u++ )
		$upload_size_unit /= 1024;
	if ( $u < 0 ) {
		$upload_size_unit = 0;
		$u = 0;
	} else {
		$upload_size_unit = (int) $upload_size_unit;
	}   

    printf( __( 'Maximum upload file size: %d%s' , 'oqey-gallery'), $upload_size_unit, $sizes[$u] );
}

function oQeyGetNumericUploadSize(){
	$upload_size_unit = $max_upload_size =  wp_max_upload_size();
	$sizes = array( 'KB', 'MB', 'GB' );
	for ( $u = -1; $upload_size_unit > 1024 && $u < count( $sizes ) - 1; $u++ )
		$upload_size_unit /= 1024;
	if ( $u < 0 ) {
		$upload_size_unit = 0;
		$u = 0;
	} else {
		$upload_size_unit = (int) $upload_size_unit;
	}   

    return $upload_size_unit.$sizes[$u];
}

function oqey_rm($fileglob)
{
    if (is_string($fileglob)) {
        if (is_file($fileglob)) {
            return unlink($fileglob);
        } else if (is_dir($fileglob)) {
            $ok = oqey_rm("$fileglob/*");
            if (! $ok) {
                return false;
            }
            return rmdir($fileglob);
        } else {
            $matching = glob($fileglob);
            if ($matching === false) {
               // trigger_error(sprintf('No files match supplied glob %s', $fileglob), E_USER_WARNING);
                return false;
            }       
            $rcs = array_map('oqey_rm', $matching);
            if (in_array(false, $rcs)) {
                return false;
            }
        }       
    } else if (is_array($fileglob)) {
        $rcs = array_map('oqey_rm', $fileglob);
        if (in_array(false, $rcs)) {
            return false;
        }
    } else {
        trigger_error('Param #1 must be filename or glob pattern, or array of filenames or glob patterns', E_USER_ERROR);
        return false;
    }
    return true;
}

function oqey_scanSkins( $outerDir, $type, $filters = array()){
    
    $dirs      = array_diff( scandir( $outerDir ), array_merge( Array( ".", ".." ), $filters ) );
    $dir_array = Array();
    
    foreach( $dirs as $d ){
	if($type=="1"){  if(is_dir($outerDir."/".$d)){ $dir_array[] = $d; } }else{ $dir_array[] = $d; }
	}		
    return $dir_array;
} 


function oqey_get_all_images($content){
  $m = '';
  preg_match_all('/\[oqeygallery id=([0-9+])\s/', $content, $m);
  
  if(!empty($m[0])){
  $content = preg_replace('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', '', $content);
  }
  return $content;
}

/*VIDEO*/

  function oqey_get_all_files($root_dir, $allow_extensions, $all_data=array()){
    // only include files with these extensions
    $allow_extensions = $allow_extensions;//array("php", "html");
    // make any specific files you wish to be excluded
    $ignore_files = array("index.php", "index.html", "wp-config.php");
    $ignore_regex = '/^_/';
    // skip these directories
    $ignore_dirs = array(".", "..", "images", "dev", "lib", "data", "osh", "fiq", "google", "stats", "_db_backups", "maps", "php_uploads", "test", "plugins", "themes", "wp-admin", "wp-includes", "galthmb", "galimg", "iphone", "thumbs", "upgrade", "skins");

    // run through content of root directory
    $dir_content = scandir($root_dir);
    foreach($dir_content as $key => $content){
        
      $path = $root_dir.'/'.$content;
      
      if(is_file($path) && is_readable($path)){
        // skip ignored files
        if(!in_array($content, $ignore_files))
        {
          if (preg_match($ignore_regex,$content) == 0)
          {
            $content_chunks = explode(".",$content);
            $ext = $content_chunks[count($content_chunks) - 1];
            // only include files with desired extensions
            if (in_array($ext, $allow_extensions))
            {
                // save file name with path
                $all_data[] = $path;   
            }
          }
        }
      }elseif(is_dir($path) && is_readable($path)){
        // skip any ignored dirs
        if(!in_array($content, $ignore_dirs)){
          // recursive callback to open new directory
          $all_data = oqey_get_all_files($path, $allow_extensions, $all_data);
        }
        
      }
    } // end foreach
    return $all_data;
  } // end get_files()
  
  
function identifyVideoService ( $url )
{
    $url = preg_replace('#\#.*$#', '', trim($url));

    $services_regexp = array(
        "#^\w+\.(?P<format>[a-zA-Z0-9]{2,5})#"                     => 'local',
        '#vimeo\.com\/(?P<id>[0-9]*)[\/\?]?#i'                     => 'vimeo',
        '#youtube\.[a-z]{0,5}/.*[\?&]?v(?:\/|=)?(?P<id>[^&]*)#i'   => 'youtube'
    );

    foreach ( $services_regexp as $pattern => $service ) {
        if ( preg_match ( $pattern, $url, $matches ) ) {
            return ( $service === 'local' ) ? $matches['format']  : $service;
        }
    }

    return false;

}

?>