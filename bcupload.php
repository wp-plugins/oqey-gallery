<?php
require_once(dirname(dirname(dirname(dirname(__FILE__))))."/wp-load.php");
include(dirname(__FILE__)."/thumbnail.class.php");
global $wpdb;

function oqey_wtmk_position($w){
    
    switch ($w) {
    case "TL":
        return array( 'TOP', 'LEFT' );
        break;
    case "TM":
        return array( 'TOP', 'CENTER' );
        break;
    case "TR":
        return array( 'TOP', 'RIGHT' );
        break;
    case "ML":
        return array( 'CENTER', 'LEFT' );
        break;
    case "MM":
        return array( 'CENTER', 'CENTER' );
        break;
    case "MR":
        return array( 'CENTER', 'RIGHT' );
        break;
    case "BL":
        return array( 'BOTTOM', 'LEFT' );
        break;
    case "BM":
        return array( 'BOTTOM', 'CENTER' );
        break;
    case "BR":
        return array( 'BOTTOM', 'RIGHT' );
        break;
  } 
}

function oqey_img_process($from, $to, $name, $height, $width, $wt, $id){
    global $wpdb;
    $oqey_galls  = $wpdb->prefix . "oqey_gallery";
      
    $to          = $to.$name;
    $thumb       = new Thumbnail($from);    	 
    $thumb->size($width,$height);
    $thumb->quality=90;                      //default 75 , only for JPG format
        
    if(is_plugin_active('oqey-add-ons/oqeyaddons.php') && $wt){
      
      $d           = $wpdb->get_row($wpdb->prepare("SELECT * FROM $oqey_galls WHERE id = %d ", absint($id) ));
    
      if($d->wtmrk_status=="default"){ 
        
         $settings = json_decode(get_option("oqey_addons_watermark_settings"));    
         $pos      = oqey_wtmk_position($settings->oqey_addons_watermark);
        
       }else{
        
         $settings = json_decode($d->wtmrk_status);      
         $pos      = oqey_wtmk_position($settings->oqey_addons_watermark); 
         
       }
      
      $thumb->output_format = 'JPG'; 
      $thumb->img_watermark = OQEY_ABSPATH.'wp-content/oqey_gallery/watermark/'.oqey_getBlogFolder($wpdb->blogid).'watermark.png';	    // [OPTIONAL] set watermark source file, only PNG format [RECOMENDED ONLY WITH GD 2 ]
      $thumb->img_watermark_Valing = $pos[0];   	    // [OPTIONAL] set watermark vertical position, TOP | CENTER | BOTTOM
      $thumb->img_watermark_Haling = $pos[1]; 
      //$thumb->txt_watermark='Watermark text';
      $thumb->txt_watermark_Hmargin = $settings->oqey_W_X_margin;          // [OPTIONAL] set watermark text horizonatal margin in pixels
      $thumb->txt_watermark_Vmargin = $settings->oqey_W_Y_margin;           // [OPTIONAL] set watermark text vertical margin in pixels
    }
    
      $thumb->memory_limit = '128M';
      $thumb->max_execution_time = '60';  
      $thumb->process();   				        // generate image
      //$thumb->show();						        // show your thumbnail, or    
      $thumb->save($to);			    // save your thumbnail to file, or
}

$gal_id           = '';
$auth_cookie      = '';
$logged_in_cookie = '';
$nonce            = '';

if(isset($_REQUEST['details'])){
    
   $arr = explode("--", base64_decode($_REQUEST['details']) );
   
   if ( isset($arr[0]) ){  $gal_id           = $arr[0]; }
   if ( isset($arr[1]) ){  $auth_cookie      = $arr[1]; }
   if ( isset($arr[2]) ){  $logged_in_cookie = $arr[2]; }
   if ( isset($arr[3]) ){  $nonce            = $arr[3]; }

  if ( is_ssl() && empty($_COOKIE[SECURE_AUTH_COOKIE]) && !empty($auth_cookie) )
	$_COOKIE[SECURE_AUTH_COOKIE] = $auth_cookie;
  elseif ( empty($_COOKIE[AUTH_COOKIE]) && !empty($auth_cookie) )
	$_COOKIE[AUTH_COOKIE] = $auth_cookie;
  if ( empty($_COOKIE[LOGGED_IN_COOKIE]) && !empty($logged_in_cookie) )
	$_COOKIE[LOGGED_IN_COOKIE] = $logged_in_cookie;

  require_once(OQEY_ABSPATH . 'wp-admin/admin.php');
  if ( !wp_verify_nonce($nonce, 'oqey-upload') ) die("Access denied. Security check failed! What are you trying to do? It`s not working like that. ");
  if ( !is_user_logged_in() ) die('Login failure. You must be logged in.');
  if ( !current_user_can('oQeyGalleries') ) die(__('You do not have sufficient permissions to upload files.'));

  $oqey_images = $wpdb->prefix . "oqey_images";

 if( isset($_REQUEST['details']) ){
  
  $folder         = oqey_get_gallery_folder($gal_id);
  $filespath      = OQEY_ABSPATH.'wp-content/oqey_gallery/galleries/'.oqey_getBlogFolder($wpdb->blogid).$folder."/galimg/";
  $filespaththumb = OQEY_ABSPATH.'wp-content/oqey_gallery/galleries/'.oqey_getBlogFolder($wpdb->blogid).$folder."/galthmb/";
  $gal_id         = absint($gal_id);

 }
 
 
if($_FILES['file']['size']>0){
    
    	if($_FILES["file"]["size"]>0){

            $path = pathinfo($_FILES["file"]["name"]);	
            $ext  = array('jpeg', 'jpg', 'png'); 
            
            if (in_array(strtolower($path['extension']), $ext)){
                
                if(!isset($path['filename'])){ 
                    
                    $name = trim(sanitize_title(substr($path['basename'], 0,strpos($path['basename'],'.')))).".".$path['extension'];
                    $name = ltrim($name, "_");	
                    
                }else{ 
                    
                    $name = ltrim(trim(sanitize_title($path['filename']).".".$path['extension'])); 
                    $name = ltrim($name, "_");	
                   
                }

              while(1){	
                  
                  if(!file_exists($filespath.$name)){
                      
                      $name      = trim(sanitize_title($path['filename']).".".$path['extension']);
                      $image_alt = $path['filename'];
                      
                  }else{	
                      
                      $name      = trim(sanitize_title($path['filename'])."_".time().".".$path['extension']);
                      $image_alt = $path['filename'];		
                      
                  }		
				
                $name = trim(strtolower($name));
                $name = ltrim($name, "_");
                $file = $filespath.$name; 
                ini_set('memory_limit', '-1');
                $exif = wp_read_image_metadata($_FILES["file"]["tmp_name"]); 
                oqey_img_process( $_FILES["file"]["tmp_name"], $filespaththumb, $name, "100", "150", false, $gal_id);	
                    
                    if( is_plugin_active('oqey-add-ons/oqeyaddons.php') ){
                        
                        oqey_img_process( $_FILES["file"]["tmp_name"], $filespath, $name, "1200", "1800", true, $gal_id);                         
                    
                    }else{
                        
                        @move_uploaded_file($_FILES["file"]["tmp_name"],$file);
                                            
                    } 
                    
                    //@move_uploaded_file($_FILES["file"]["tmp_name"],$file);
                                        
                    if(empty($exif)){
                        
                        $exif = "default";
                        
                    }                          
           
                      $wpdb->query( $wpdb->prepare( "INSERT INTO $oqey_images (title, gal_id, img_order, alt, comments, status, img_link, img_path, img_type, video_id, meta_data) 
                                                                       VALUES (%s, %d, %d, %s, %s, %d, %s, %s, %s, %d, %s)",
                                                                       $name,
                                                                       $gal_id,
                                                                       '0',
                                                                       $image_alt,
                                                                       $image_alt,
                                                                       '0',
                                                                       '',
                                                                       '',
                                                                       'oqey',
                                                                       '0',
                                                                       json_encode($exif)                                                                     
                                                  ) 
                                                  );
                      

                      
                      break;  	
          
  			}
    	}
	}
}

}else{
    die("Access denied. Security check failed! What are you trying to do? It`s not working like that. ");
}
?>