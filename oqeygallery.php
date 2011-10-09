<?php
// oQey Gallery
// Copyright (c) 2011 oqeysites.com
// This is an add-on for WordPress
// http://wordpress.org/
//
// **********************************************************************
// This program is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
// *****************************************************************

/*
Plugin Name: oQey Gallery
Version: 0.4.8.5
Description: This is a plugin, that lets you create and manage flash photo galleries / slideshows that will work fine under iPhone / iPad and other non-flash mobile devices. The flash version supports music and skins, so you can change the way it looks like with a few simple clicks. Customizable, commercial skins are also available as well as custom photo galleries for professionals. This plugin uses built-in WP functions and a simple batch upload system.
Author: oqeysites.com
Author URI: http://oqeysites.com/
*/

if (!empty($_SERVER['SCRIPT_FILENAME']) && 'oqeygallery.php' == basename($_SERVER['SCRIPT_FILENAME'])) die ('Please do not load this page directly. Thanks!');
	
define('OQEY_ABSPATH', str_replace('\\', '/', ABSPATH) );

include("oqey-gallery-functions.php");
require_once(OQEY_ABSPATH . 'wp-admin/includes/plugin.php');
global $oqey_db_version;	
       $oqey_db_version = "0.4.9.2";
       $plugin_name = "oQey Gallery plugins";

function oqey_db_install(){
    global $wpdb, $oqey_db_version, $wp_filesystem, $wp_roles;

	if ( !current_user_can('activate_plugins') ) 
		return;

	$perm = get_role('administrator');	
	$perm->add_cap('oQeySettings');
	$perm->add_cap('oQeyGalleries');	
	$perm->add_cap('oQeySkins');
	$perm->add_cap('oQeyMusic');
	$perm->add_cap('oQeyTrash');
    $perm->add_cap('oQeyRoles');
  
	add_option("oqey_width", "900" ,'', 'no');
	add_option("oqey_height", "600" ,'', 'no');
	add_option("oqey_bgcolor", "#ffffff" ,'', 'no');
	add_option("oqey_thumb_width", "120" ,'', 'no');
	add_option("oqey_thumb_height", "80" ,'', 'no');
	add_option("oqey_default_gallery_skin", "0" ,'', 'no');
	add_option("oqey_effects_trans_time", "0.5" ,'', 'no');
	add_option("oqey_pause_between_tran", "6" ,'', 'no');
	add_option("oqey_LoopOption", "on" ,'', 'no');
	add_option("oqey_flash_gallery_true", "on" ,'', 'no');
	add_option("oqey_backlinks", "on" ,'', 'no');
	add_option("oqey_BorderOption", "on" ,'', 'no');
	add_option("oqey_AutostartOption", "" ,'', 'no');
	add_option("oqey_CaptionsOption", "" ,'', 'no');
	add_option("oqey_options", "TM" ,'', 'no');
	add_option("oqey_noflash_options", "injsarr" ,'', 'no');	
	add_option("oqey_gall_title_no", "on" ,'', 'no');
	add_option("oqey_border_bgcolor", "#000000" ,'', 'no');
	add_option("oqey_effect_transition_type", "fade" ,'', 'no');	
    add_option("oqey_gallery_version", "0.4.8.5");
    add_option("oqey_db_version", $oqey_db_version);
			
  $gal_dir_up = OQEY_ABSPATH.'wp-content/oqey_gallery/galleries/'.oqey_getBlogFolder($wpdb->blogid);   
  wp_mkdir_p ($gal_dir_up); // make the gallery folder - root

  $music_dir = OQEY_ABSPATH.'wp-content/oqey_gallery/music/'.oqey_getBlogFolder($wpdb->blogid);    
  wp_mkdir_p ($music_dir); // make the music folder - root
  
  $skins_dir = OQEY_ABSPATH.'wp-content/oqey_gallery/skins/'.oqey_getBlogFolder($wpdb->blogid); 
  wp_mkdir_p ($skins_dir); // make the music folder - root
  
   $oqey_galls = $wpdb->prefix . "oqey_gallery";
   $oqey_images = $wpdb->prefix . "oqey_images";
   $oqey_music = $wpdb->prefix . "oqey_music";
   $oqey_music_rel = $wpdb->prefix . "oqey_music_rel";
   $oqey_skins = $wpdb->prefix . "oqey_skins";

   if(!$wpdb->get_var("SHOW TABLES LIKE '$oqey_galls'")){
	$sql = "CREATE TABLE `" . $oqey_galls . "` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`title` tinytext NOT NULL,
		`splash_img` int(11) NOT NULL DEFAULT '0',
		`splash_only` int(11) NOT NULL DEFAULT '0',
		`post_id` int(11) NOT NULL DEFAULT '0',
		`gall_order` int(11) NOT NULL DEFAULT '0',
		`status` int(1) NOT NULL DEFAULT '0',
		`author` int(11) NOT NULL DEFAULT '0',  
		`folder` varchar(255) NOT NULL DEFAULT '',
		`skin_id` int(11) NOT NULL DEFAULT '0',
		PRIMARY KEY  (`id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";	
	 $wpdb->query($sql);
	}
    
    if(!$wpdb->get_var("SHOW TABLES LIKE '$oqey_images'")){
	$sql2 = "CREATE TABLE `" . $oqey_images . "` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`title` tinytext NOT NULL,
		`gal_id` int(11) NOT NULL DEFAULT '0',
		`img_order` int(11) NOT NULL DEFAULT '0',
		`alt` text NOT NULL,
		`comments` text NOT NULL,
		`status` int(1) NOT NULL DEFAULT '0',
        `img_link` text NOT NULL DEFAULT '',
        `img_path` text NOT NULL DEFAULT '',
        `img_type` varchar(255) NOT NULL DEFAULT 'oqey',        
		PRIMARY KEY  (`id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";	
	 $wpdb->query($sql2);
    }
    
    if(!$wpdb->get_var("SHOW TABLES LIKE '$oqey_music'")){
    $sql4 = "CREATE TABLE `" . $oqey_music . "` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`link` varchar(255) NOT NULL DEFAULT '',
		`title` tinytext NOT NULL,
		`artist` varchar(255) NOT NULL DEFAULT '',
		`music_order` int(11) NOT NULL DEFAULT '0',
		`status` int(1) NOT NULL DEFAULT '0',
		PRIMARY KEY  (`id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";	
    $wpdb->query($sql4);	
    }
    
    if(!$wpdb->get_var("SHOW TABLES LIKE '$oqey_music_rel'")){
	$sql5 = "CREATE TABLE `" . $oqey_music_rel . "` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`music_id` int(11) NOT NULL DEFAULT '0',
		`gallery_id` int(11) NOT NULL DEFAULT '0',
		`mrel_order` int(11) NOT NULL DEFAULT '0',
		PRIMARY KEY  (`id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";	
    $wpdb->query($sql5);
	}
    
    if(!$wpdb->get_var("SHOW TABLES LIKE '$oqey_skins'")){
	$sql6 = "CREATE TABLE `" . $oqey_skins . "` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`name` tinytext NOT NULL,
		`description` tinytext NOT NULL,
		`comkey` varchar(255) NOT NULL DEFAULT '',
		`folder` tinytext NOT NULL,
		`status` int(1) NOT NULL DEFAULT '0',
		`commercial` varchar(3) NOT NULL DEFAULT 'no',
		`skinid` varchar(55) NOT NULL DEFAULT '',
		`firstrun` int(1) NOT NULL DEFAULT '1',
		PRIMARY KEY  (`id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";	
    $wpdb->query($sql6);	
    }
   
   $installed_oqey_ver = get_option( "oqey_db_version" );
}

if (function_exists('register_activation_hook')){
	register_activation_hook( __FILE__, 'oqey_db_install' );
}

/*Upgrade database if the DB version is lower than 0.4.7*/
function oqey_check_upgrade(){
    global $wpdb, $oqey_db_version, $wp_roles;
     $oqey_images = $wpdb->prefix . "oqey_images";
     $oqey_skins = $wpdb->prefix . "oqey_skins";
       
     $installed_oqey_ver = get_option( "oqey_db_version" );
          
    if (version_compare($installed_oqey_ver, '0.4.6', '<')){
        
	 $perm = get_role('administrator');	
	 $perm->add_cap('oQeySettings');
	 $perm->add_cap('oQeyGalleries');	
	 $perm->add_cap('oQeySkins');
	 $perm->add_cap('oQeyMusic');
	 $perm->add_cap('oQeyTrash');
     $perm->add_cap('oQeyRoles');
	
    }
   
    if (version_compare($installed_oqey_ver, '0.4.7', '<')){     
	
	 add_option("oqey_effect_transition_type", "fade" ,'', 'yes');//default effect transition - fade
	
     $wpdb->query("ALTER TABLE $oqey_images ADD img_link TEXT NOT NULL DEFAULT '' AFTER status");
     $wpdb->query("ALTER TABLE $oqey_images ADD img_path TEXT NOT NULL DEFAULT '' AFTER img_link");
     $wpdb->query("ALTER TABLE $oqey_images ADD img_type varchar(255) NOT NULL DEFAULT 'oqey' AFTER img_path");
	}
    
    if (version_compare($installed_oqey_ver, '0.4.9.1', '<')){     

      $wpdb->query("ALTER TABLE $oqey_skins MODIFY skinid varchar(55)");

	}
    
    update_option( "oqey_db_version", $oqey_db_version );        
}

//.................................................................//	
function oqey_init_method() { 
    
   oqey_check_upgrade();//make update if need to do

   wp_enqueue_script('swfobject'); 
   wp_enqueue_script('jquery');  
   
   //wp_enqueue_script('applejs', WP_PLUGIN_URL . '/oqey-gallery/js/oqey-js-drag-iphone.js', array('jquery')); 
   //wp_enqueue_script('oqeyjs', WP_PLUGIN_URL . '/oqey-gallery/js/oqey.js', array('jquery')); 
   
   //wp_enqueue_script('oqeygalleryjs', WP_PLUGIN_URL . '/oqey-gallery/js/oqey-gallery.js');   
   wp_register_style('oQey-front-css', WP_PLUGIN_URL . '/oqey-gallery/css/oqeystyle.css');
   wp_enqueue_style('oQey-front-css');
   
if(is_admin() && ($_GET['page']=='oQeysettings' || $_GET['page']=='oQeyGalleries' || $_GET['page']=='oQeySkins' || $_GET['page']=='oQeyMusic' || $_GET['page']=='oQeyTrash' ) ){
   wp_register_style('oQey-admin-css', WP_PLUGIN_URL . '/oqey-gallery/css/oqeyadmin.css');
   wp_enqueue_style('oQey-admin-css');
   wp_register_style('oQey-admin-pop-css', WP_PLUGIN_URL . '/oqey-gallery/css/jquery-ui.css');
   wp_enqueue_style('oQey-admin-pop-css');
   wp_enqueue_script('jquerysimplemodal', WP_PLUGIN_URL . '/oqey-gallery/js/jquery.loadimages.min.js', array('jquery'));
   wp_enqueue_script('jqueryeditable', WP_PLUGIN_URL . '/oqey-gallery/js/jquery.jeditable.js', array('jquery'));   
   wp_enqueue_script('jqueryfarbtastic', WP_PLUGIN_URL . '/oqey-gallery/js/farbtastic.js', array('jquery'));   
   
   if($_GET['page']=='oQeyGalleries'){
   wp_enqueue_script('jqueryajaxupload', WP_PLUGIN_URL . '/oqey-gallery/js/ajaxupload.js', array('jquery')); 
   }
   wp_enqueue_script('jquery-ui-core ');
   wp_enqueue_script('jquery-ui-sortable');  
   wp_enqueue_script('jquery-ui-draggable');
   wp_enqueue_script('jquery-ui-selectable');
   wp_enqueue_script('jquery-ui-dialog');
   
   if (is_plugin_active('oqey-photo-cropper/oqeycropper.php') && $_GET['page']=='oQeyGalleries'){
    wp_enqueue_script('jqueryoqeyjCrop', WP_PLUGIN_URL . '/oqey-photo-cropper/js/jquery.jcrop.js', array('jquery')); 
    //wp_enqueue_script('jqueryoqeycropper', WP_PLUGIN_URL . '/oqey-photo-cropper/js/oqey.cropper.js', array('jquery'));
    wp_register_style('oQey-admin-jCrop', WP_PLUGIN_URL . '/oqey-photo-cropper/css/jquery.jcrop.css');
    wp_enqueue_style('oQey-admin-jCrop');
   }elseif(is_plugin_active('oqey-addons/oqeyaddons.php')){
    
    wp_enqueue_script('jqueryoqeyjCrop', WP_PLUGIN_URL . '/oqey-addons/js/jquery.jcrop.js', array('jquery')); 
    //wp_enqueue_script('jqueryoqeycropper', WP_PLUGIN_URL . '/oqey-photo-cropper/js/oqey.cropper.js', array('jquery'));
    wp_register_style('oQey-admin-jCrop', WP_PLUGIN_URL . '/oqey-addons/css/jquery.jcrop.css');
    wp_enqueue_style('oQey-admin-jCrop');
    
   }
   
   if($_GET['page']=="oQeyTrash"){ 
   wp_enqueue_script('jquery-ui-tabs'); 
  }
 }
}  
 
add_action('init', 'oqey_init_method');


function oqey_admin_custom_css_head(){
    
   $css = WP_PLUGIN_URL . '/oqey-gallery/css/oqeyadmin-ie.css';
   echo '<!--[if IE]><link rel="stylesheet" type="text/css" href="'.$css.'" /><![endif]-->';
   echo "\n";

}

add_action('admin_head', 'oqey_admin_custom_css_head');

/* reactivate the plugin for multisite subdomains*/
function oqey_init_method_gallery_multisite(){
global $wpdb;
$oqey_galls = $wpdb->prefix . "oqey_gallery";
if($wpdb->get_var("show tables like '$oqey_galls'") != $oqey_galls ){ oqey_db_install(); }
}
/*end*/ 
add_action('init', 'oqey_init_method_gallery_multisite');

function oqey_php_version(){
echo '<div class="error fade" style="background-color:#E36464;">
      <p>Attention! Your server php version is: '.phpversion().'. oQey Gallery requires php version 5.2+ in order to run properly. Please upgrade your server!</p></div>';
}
if( version_compare( '5.2', phpversion(), '>' ) ){ add_action( 'admin_notices', 'oqey_php_version'); }


function oqey_safe_mode(){
if(ini_get('safe_mode')){
echo '<div class="error fade" style="background-color:#E36464;">
      <p>Attention! Your server safe mode is: ON. oQey Gallery requires safe mode to be OFF in order to run properly. Please set your server safe mode option!</p></div>';
}
}
if( version_compare( '5.3', phpversion(), '>' ) ){ add_action( 'admin_notices', 'oqey_safe_mode'); }

include("gallcore.php");
?>