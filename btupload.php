<?php
require_once(dirname(dirname(dirname(dirname(__FILE__))))."/wp-load.php");
global $wpdb;

if(isset($_REQUEST['details'])){
    
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
if ( !wp_verify_nonce($nonce, 'oqey-skins') ) die("Access denied. Security check failed! What are you trying to do? It`s not working like that.");
if ( !is_user_logged_in() ) die('Login failure.');
if ( !current_user_can('oQeySkins') ) die(__('You do not have sufficient permissions to upload files.'));


if($_FILES['file']['size']>0){
            
    		$path        = pathinfo($_FILES["file"]["name"]);			
    		$ext         = array('zip', 'ZIP'); 
                $zip_extract = OQEY_ABSPATH."wp-content/oqey_gallery/skins/".oqey_getBlogFolder($wpdb->blogid);
                
		if(in_array( strtolower($path['extension']), $ext)){        
                        
                        if ( class_exists('ZipArchive') ){
			   
                            $zip      = new ZipArchive;
	                    $zip_file = $_FILES["file"]["tmp_name"];
                            $zip->open($zip_file);
                            $zip->extractTo($zip_extract);
                            $zip->close();                            
                            
                        }else{
                            
                            require_once(OQEY_ABSPATH . 'wp-admin/includes/class-pclzip.php');	
                            $zip_file = $_FILES["file"]["tmp_name"];
                            $archive = new PclZip($zip_file);
                            $list = $archive->extract($zip_extract);
                            if ($list == 0) {
                                die("ERROR : '".$archive->errorInfo(true)."'");
                                }
                        }

    	}

}else{
     die("File missing.");
}

}else{
    die("Access denied. Security check failed! What are you trying to do? It`s not working like that. ");
}
?>