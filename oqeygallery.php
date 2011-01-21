<?php
// oQey-Gallery
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
Plugin Name: oQey-Gallery
Version: 0.1
Description: oQey-Gallery plugin is the best photo gallery / slideshow solution for your custom blog. It has a built-in skinnable slideshow and supports music also.
Author: oqeysites.com
Author URI: http://oqeysites.com/
*/

if (!empty($_SERVER['SCRIPT_FILENAME']) && 'oqeygallery.php' == basename($_SERVER['SCRIPT_FILENAME']))
	die ('Please do not load this page directly. Thanks!');
	
	global $oqey_db_version;
           $oqey_db_version = "0.1";
 
function oqey_db_install() {
   global $wpdb;
   global $oqey_db_version;

	add_option("oqey_width", "900" ,'', 'yes');
	add_option("oqey_height", "600" ,'', 'yes');
	add_option("oqey_bgcolor", "#FFFFFF" ,'', 'yes');
	add_option("oqey_thumb_width", "120" ,'', 'yes');
	add_option("oqey_thumb_height", "80" ,'', 'yes');
	add_option("oqey_default_gallery_skin", "0" ,'', 'yes');
	add_option("oqey_effects_trans_time", "0.5" ,'', 'yes');
	add_option("oqey_pause_between_tran", "6" ,'', 'yes');
	

  $gal_dir_up = ABSPATH."/wp-content/oqey_gallery/galleries";   
  wp_mkdir_p ($gal_dir_up); // make the gallery folder - root

  $music_dir = ABSPATH."/wp-content/oqey_gallery/music";    
  wp_mkdir_p ($music_dir); // make the music folder - root
  

   $oqey_galls = $wpdb->prefix . "oqey_gallery";
   $oqey_images = $wpdb->prefix . "oqey_images";
   //$oqey_cats = $wpdb->prefix . "oqey_categories";
   $oqey_music = $wpdb->prefix . "oqey_music";
   $oqey_music_rel = $wpdb->prefix . "oqey_music_rel";
   $oqey_skins = $wpdb->prefix . "oqey_skins";

   if($wpdb->get_var("show tables like '$oqey_galls'") != $oqey_galls) {

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
	
	$sql2 = "CREATE TABLE `" . $oqey_images . "` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`title` tinytext NOT NULL,
		`gal_id` int(11) NOT NULL DEFAULT '0',
		`img_order` int(11) NOT NULL DEFAULT '0',
		`alt` text NOT NULL,
		`comments` text NOT NULL,
		`status` int(1) NOT NULL DEFAULT '0',
		PRIMARY KEY  (`id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";	
	 $wpdb->query($sql2);
	/* 
	$sql3 = "CREATE TABLE `" . $oqey_cats . "` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`title` tinytext NOT NULL,
		`cat_order` int(11) NOT NULL DEFAULT '0',
		`cat_status` int(1) NOT NULL DEFAULT '0',
		 PRIMARY KEY  (id)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
	$wpdb->query($sql3);	
	*/
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
 
	$sql5 = "CREATE TABLE `" . $oqey_music_rel . "` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`music_id` int(11) NOT NULL DEFAULT '0',
		`gallery_id` int(11) NOT NULL DEFAULT '0',
		`mrel_order` int(11) NOT NULL DEFAULT '0',
		PRIMARY KEY  (`id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";	
    $wpdb->query($sql5);
	
	$sql6 = "CREATE TABLE `" . $oqey_skins . "` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`name` tinytext NOT NULL,
		`description` tinytext NOT NULL,
		`comkey` varchar(255) NOT NULL DEFAULT '',
		`folder` tinytext NOT NULL,
		`status` int(1) NOT NULL DEFAULT '0',
		`commercial` varchar(3) NOT NULL DEFAULT 'no',
		`skinid` varchar(4) NOT NULL DEFAULT '',
		`firstrun` int(1) NOT NULL DEFAULT '1',
		PRIMARY KEY  (`id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";	
    $wpdb->query($sql6);	
	

   $wpdb->query("INSERT INTO $oqey_skins (name, description, folder, status, commercial, skinid) VALUES ('default', '".mysql_real_escape_string(stripslashes("That's the default gray skin that comes with the free oQey Galleries pack"))."', 'default', '1', 'no', '0001')");

	add_option("oqey_db_version", $oqey_db_version);
   }  
}

if (function_exists('register_activation_hook')) {
	register_activation_hook( __FILE__, 'oqey_db_install' );	
}
//.................................................................//	
function oqey_init_method() {   
 
   wp_enqueue_script('swfobject'); 
   wp_enqueue_script('jquery');  
   wp_enqueue_script('oqeyjs', WP_PLUGIN_URL . '/oqey-gallery/js/oqey.js');
   
   wp_register_style('oQey-front-css', WP_PLUGIN_URL . '/oqey-gallery/css/oqeystyle.css');
   wp_enqueue_style('oQey-front-css');

if(is_admin() && ($_GET['page']=='oQeysettings' || $_GET['page']=='oQeyGalleries' || $_GET['page']=='oQeyGalleriesSkins' || $_GET['page']=='oQeyMusic' || $_GET['page']=='oQeyTrash' ) ){
   wp_register_style('oQey-admin-css', WP_PLUGIN_URL . '/oqey-gallery/css/oqeyadmin.css');
   wp_enqueue_style('oQey-admin-css');
   wp_register_style('oQey-admin-pop-css', WP_PLUGIN_URL . '/oqey-gallery/css/jquery-ui.css');
   wp_enqueue_style('oQey-admin-pop-css');
   wp_enqueue_script('jquerysimplemodal', WP_PLUGIN_URL . '/oqey-gallery/js/jquery.loadimages.min.js', array('jquery'));
   wp_enqueue_script('jqueryeditable', WP_PLUGIN_URL . '/oqey-gallery/js/jquery.jeditable.js', array('jquery'));   
   wp_enqueue_script('jqueryfarbtastic', WP_PLUGIN_URL . '/oqey-gallery/js/farbtastic.js', array('jquery'));   
   wp_enqueue_script('jqueryajaxupload', WP_PLUGIN_URL . '/oqey-gallery/js/ajaxupload.js', array('jquery')); 
   wp_enqueue_script('jquery-ui-core ');
   wp_enqueue_script('jquery-ui-sortable');  
   wp_enqueue_script('jquery-ui-draggable');
   wp_enqueue_script('jquery-ui-selectable');
   wp_enqueue_script('jquery-ui-dialog');
   
   if($_GET['page']=="oQeyTrash"){ 
   wp_enqueue_script('jquery-ui-tabs'); 
   }
}
}     
 
add_action('init', 'oqey_init_method');
include("gallcore.php");
?>