<?php
define('WP_ADMIN', true);
require_once("../../../wp-load.php");
if(get_option("oqey_license")==""){ die(); }

if(isset($_REQUEST['loc'])){
$arr = explode("--", base64_decode($_REQUEST['loc']) );
}
if(isset($_REQUEST['id'])){
$arr = explode("--", base64_decode($_REQUEST['id']) );	
}
if( isset($_REQUEST['loc']) || isset($_REQUEST['id'])){
$gal_id = $arr[0];
$auth_cookie = $arr[1];
$logged_in_cookie = $arr[2];
$nonce = $arr[3];
}
if ( is_ssl() && empty($_COOKIE[SECURE_AUTH_COOKIE]) && !empty($auth_cookie) )
	$_COOKIE[SECURE_AUTH_COOKIE] = $auth_cookie;
elseif ( empty($_COOKIE[AUTH_COOKIE]) && !empty($auth_cookie) )
	$_COOKIE[AUTH_COOKIE] = $auth_cookie;
if ( empty($_COOKIE[LOGGED_IN_COOKIE]) && !empty($logged_in_cookie) )
	$_COOKIE[LOGGED_IN_COOKIE] = $logged_in_cookie;

unset($current_user);
global $wpdb;
require_once(OQEY_ABSPATH . 'wp-admin/admin.php');
if ( !wp_verify_nonce($nonce, 'oqey-upload') ) die("Security check");
if ( !is_user_logged_in() ) die('Login failure. -1');
$oqey_images = $wpdb->prefix . "oqey_images";
$oqey_galls = $wpdb->prefix . "oqey_gallery";

if( isset($_REQUEST['loc']) || isset($_REQUEST['id'])){
$folder = oqey_get_gallery_folder($gal_id);
$filespath = OQEY_ABSPATH.'wp-content/oqey_gallery/galleries/'.oqey_getBlogFolder($wpdb->blogid).$folder."/galimg/";
$filespaththumb= OQEY_ABSPATH.'wp-content/oqey_gallery/galleries/'.oqey_getBlogFolder($wpdb->blogid).$folder."/galthmb/";
$iphonepath= OQEY_ABSPATH.'wp-content/oqey_gallery/galleries/'.oqey_getBlogFolder($wpdb->blogid).$folder."/iphone/";
$gal_id = mysql_real_escape_string($gal_id);
}

if($_FILES['Filedata']['size']>0){
    	if($_FILES["Filedata"]["size"]>0){
    		$path = pathinfo($_FILES["Filedata"]["name"]);			
			$ext = array('jpeg', 'jpg', 'png'); 
		    if (in_array(strtolower($path['extension']), $ext)){			
			if(!isset($path['filename'])){ $name = trim(sanitize_title(substr($path['basename'], 0,strpos($path['basename'],'.')))).".".$path['extension'];
            }else{ $name = trim(sanitize_title($path['filename']).".".$path['extension']); }

  			while(1){				
				if(!file_exists($filespath.$name)){	
				$name = trim(sanitize_title($path['filename']).".".$path['extension']);
				$image_alt = $path['filename'];
				}else{		
				$name = trim(sanitize_title($path['filename'])."_".time().".".$path['extension']);
				$image_alt = $path['filename'];							
				}		
				$name = strtolower($name);
				$file = $filespath.$name; 				
				
  					@move_uploaded_file($_FILES["Filedata"]["tmp_name"],$filespath.$name);					
					oqey_img_resize( $file, $filespaththumb, $name, "100", "150" );		
					oqey_img_resize( $file, $iphonepath, $name, "640", "960" );
					//$wpdb->query( "INSERT INTO {$oqey_images} (title, gal_id, alt) VALUES ('$name ', '$gal_id', '$image_alt')");
					$wpdb->query( "INSERT INTO $oqey_images (title, gal_id, img_order, alt, comments, status) VALUES ('$name ', '$gal_id', '0', '$image_alt', '', '0')");
  					break;  		
  			}
    	}
	}
}
echo 'File uploaded';
?>