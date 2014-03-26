<?php
require_once(dirname(dirname(dirname(dirname(__FILE__))))."/wp-load.php");
global $wpdb;

if( isset($_REQUEST['details']) ){
    
   $arr = explode("--", base64_decode($_REQUEST['details']) );

   if ( isset($arr[1]) ){  $auth_cookie      = $arr[1]; }
   if ( isset($arr[2]) ){  $logged_in_cookie = $arr[2]; }
   if ( isset($arr[3]) ){  $nonce            = $arr[3]; }
    
if ( is_ssl() && empty($_COOKIE[SECURE_AUTH_COOKIE]) && !empty($auth_cookie) )
	$_COOKIE[SECURE_AUTH_COOKIE] = $auth_cookie;
elseif ( empty($_COOKIE[AUTH_COOKIE]) && !empty($auth_cookie) )
	$_COOKIE[AUTH_COOKIE] = $auth_cookie;
if ( empty($_COOKIE[LOGGED_IN_COOKIE]) && !empty($logged_in_cookie) )
	$_COOKIE[LOGGED_IN_COOKIE] = $logged_in_cookie;

unset($current_user);
require_once(OQEY_ABSPATH . 'wp-admin/admin.php');
if ( !wp_verify_nonce($nonce, 'oqey-video') ) die("Access denied. Security check failed! What are you trying to do? It`s not working like that. ");
if ( !is_user_logged_in() ) die('Login failure.');
if ( !current_user_can('oQeyVideo') ) die(__('You do not have sufficient permissions to upload video files.'));

$oqey_video = $wpdb->prefix . "oqey_video";
$filespath  = OQEY_ABSPATH.'wp-content/oqey_gallery/video/'.oqey_getBlogFolder($wpdb->blogid);

if($_FILES["file"]["size"]>0){
            
    		$path = pathinfo($_FILES["file"]["name"]);			
		$ext  = array('jpg', 'flv', 'f4p', 'fpv', 'mp4', 'm4v', 'm4a', 'mov', 'mp4v', '3gp', '3g2' ); 
		
                if (in_array(strtolower($path['extension']), $ext)){	
    		
			$video_link = trim(strtolower(sanitize_title($path['filename']).".".$path['extension']));	
			$title      = trim($path['filename']);
                                
  			if(file_exists($filespath . $video_link)){ 
  				  
			           $video_link = trim(strtolower($path['filename']."_".time().".".$path['extension']));
				   $title      = trim($path['filename'])."_".time();
                                        
                        }	
				
                	        ini_set('memory_limit', '-1');
                                @move_uploaded_file($_FILES["file"]["tmp_name"],$filespath.$video_link);
                                
                                if(strtolower($path['extension'])!="jpg"){
                                    
                                    $video_link = 'wp-content/oqey_gallery/video/'.oqey_getBlogFolder($wpdb->blogid).$video_link;
                                    
                                    $wpdb->query( $wpdb->prepare( "INSERT INTO $oqey_video (post_id, oqey_parent, title, video_link, video_image, type, description, status, vorder) 
                                                                        VALUES ( %d, %d, %s, %s, %s, %s, %s, %d, %d)",
                                                                                0,
                                                                                0,
                                                                                $title,
                                                                                $video_link,
                                                                                "",
                                                                                "oqey",
                                                                                "",
                                                                                0,
                                                                                0
                                                    )
                                                    );
                                    
                                    
                                }

  
                }
                        
}else{
     die("File missing.");
}

}else{
    die("Access denied. Security check failed! What are you trying to do? It`s not working like that. ");
}
?>