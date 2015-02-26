<?php
// oQey Gallery
// Copyright (c) 2015 oqeysites.com
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
Version: 1.0.8
Description: oQey Gallery is a plugin for managing images and video, creating photo slideshows with music, photo & video galleries that works fine under iPhone / iPad and other mobile devices. Flash version of the slideshow is automatically replaced by the HTML5 | Java slideshow on a non-flash device. Flash gallery supports customizable skins, so you can change the way it looks with a few clicks using the skin options tool. Commercial skins are also available as well as custom built photo / video galleries and slideshows for professionals. This plugin uses built-in WP functions and a simple batch upload system.
Author: oqeysites.com
Author URI: http://oqeysites.com/
*/
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'oqeygallery.php' == basename($_SERVER['SCRIPT_FILENAME'])) die ('Please do not load this page directly. Thanks!');
	
if (!defined('OQEY_ABSPATH')) {

    define('OQEY_ABSPATH', str_replace('\\', '/', ABSPATH) ); //oqey path

}

require("oqey-gallery-functions.php");
require_once(OQEY_ABSPATH . 'wp-admin/includes/plugin.php');
global $oqey_db_version;	
       $oqey_db_version = "1.0.8";

function oqey_db_install(){
   global $wpdb, $oqey_db_version;

	if ( !current_user_can('activate_plugins') ) 
		return;

	$perm = get_role('administrator');	
	$perm->add_cap('oQeySettings');
	$perm->add_cap('oQeyGalleries');
        $perm->add_cap('oQeyVideo');	
	$perm->add_cap('oQeySkins');
	$perm->add_cap('oQeyMusic');
	$perm->add_cap('oQeyTrash');
        $perm->add_cap('oQeyRoles'); 
        $perm->add_cap('oqey-gallery');

        add_option("oqey_width", "100" ,'', 'no');
        add_option("oqey_width_details", "pr" ,'', 'no');
        add_option("oqey_height", "450" ,'', 'no');
	add_option("oqey_bgcolor", "none" ,'', 'no');
	add_option("oqey_thumb_width", "120" ,'', 'no');
	add_option("oqey_thumb_height", "80" ,'', 'no');
	add_option("oqey_default_gallery_skin", "0" ,'', 'no');
	add_option("oqey_effects_trans_time", "0.5" ,'', 'no');
	add_option("oqey_pause_between_tran", "6" ,'', 'no');
	add_option("oqey_LoopOption", "on" ,'', 'no');
	add_option("oqey_gall_title_no", "on" ,'', 'no');
	add_option("oqey_backlinks", "on" ,'', 'no');
	add_option("oqey_BorderOption", "on" ,'', 'no');
	add_option("oqey_AutostartOption", "" ,'', 'no');
	add_option("oqey_CaptionsOption", "" ,'', 'no');
	add_option("oqey_options", "TM" ,'', 'no');
	add_option("oqey_noflash_options", "injsarr" ,'', 'no');	
	add_option("oqey_gall_title_no", "on" ,'', 'no');
	add_option("oqey_border_bgcolor", "none" ,'', 'no');
	add_option("oqey_effect_transition_type", "fade" ,'', 'no');
        add_option("oqey_effect_transition_type", "fade" ,'', 'no');
        add_option("oqey_show_captions_under_photos", "on" ,'', 'no');
        add_option("oqey_gallery_version", "1.0");
        add_option("oqey_db_version", $oqey_db_version);
        //add_option("oqey_upload_width", "1920" ,'', 'no');
        //add_option("oqey_upload_height", "1280" ,'', 'no');
        //add_option("oqey_upload_quality", "90" ,'', 'no');
        
        /*added in v 1.0.3*/
        add_option("oqey_wp_native_run", "on" ,'', 'no');
        

        $gal_dir_up = oQeyPluginRepoPath().'/galleries/'.oqey_getBlogFolder($wpdb->blogid); 
        wp_mkdir_p ($gal_dir_up); // create gallery folder - root
        
        $music_dir  = oQeyPluginRepoPath().'/music/'.oqey_getBlogFolder($wpdb->blogid);  
        wp_mkdir_p ($music_dir); // create music folder - root
        
        $skins_dir  = oQeyPluginRepoPath().'/skins/'.oqey_getBlogFolder($wpdb->blogid); 
        wp_mkdir_p ($skins_dir); // create skins folder - root
  
        $oqey_galls     = $wpdb->prefix . "oqey_gallery";
        $oqey_images    = $wpdb->prefix . "oqey_images";
        $oqey_music     = $wpdb->prefix . "oqey_music";
        $oqey_music_rel = $wpdb->prefix . "oqey_music_rel";
        $oqey_skins     = $wpdb->prefix . "oqey_skins";
        $oqey_video     = $wpdb->prefix . "oqey_video";
   
   if(!$wpdb->get_var("SHOW TABLES LIKE '$oqey_galls'")){
    
    add_option("oqey_flash_gallery_true", "on" ,'', 'no');//add this option if on the fisrt install
    
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
                `wtmrk_status` varchar(255) NOT NULL DEFAULT 'default',
                `permalink` varchar(255) NOT NULL DEFAULT 'default',
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
                `video_id` int(11) NOT NULL DEFAULT '0',  
                `meta_data` longtext NOT NULL DEFAULT '',     
		PRIMARY KEY  (`id`)
                ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";	
	 $wpdb->query($sql2);
    }
    
    if(!$wpdb->get_var("SHOW TABLES LIKE '$oqey_music'")){
    $sql4 = "CREATE TABLE `" . $oqey_music . "` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`link` varchar(255) NOT NULL DEFAULT '',
                `path` varchar(255) NOT NULL DEFAULT '',
		`title` tinytext NOT NULL,
		`artist` varchar(255) NOT NULL DEFAULT '',
		`music_order` int(11) NOT NULL DEFAULT '0',
		`status` int(1) NOT NULL DEFAULT '0', 
                `type` varchar(55) NOT NULL DEFAULT '',       
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

    if(!$wpdb->get_var("SHOW TABLES LIKE '$oqey_video'")){
     $sql7 = "CREATE TABLE `" . $oqey_video . "` (
		`id` int NOT NULL AUTO_INCREMENT,
		`post_id` int(11) NOT NULL DEFAULT '0',
		`oqey_parent` int(11) NOT NULL DEFAULT '0',
		`title` varchar(255) NOT NULL DEFAULT '',
		`video_link` varchar(255) NOT NULL DEFAULT '',
                `video_image` varchar(255) NOT NULL DEFAULT '',
                `type` varchar(55) NOT NULL DEFAULT '',
                `description` text NOT NULL DEFAULT '',
                `status` int(1) NOT NULL DEFAULT '0',
                `vorder` int(1) NOT NULL DEFAULT '0',
                PRIMARY KEY  (`id`)
                ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
     $wpdb->query($sql7);
    }
   
}

if (function_exists('register_activation_hook')){
	register_activation_hook( __FILE__, 'oqey_db_install' );
}

/*Upgrades*/
function oqey_check_upgrade(){}

//.................................................................//	
function oqey_no_skins_installed(){
      echo '<div class="error fade" style="background-color:#ff8c7a;width:887px;"><p>';
      printf(__('oQey Gallery Plugin didn`t detect any active slideshow skins. Would you like to install one? If so, please click <a href="%s">here</a>.', 'oqey-gallery'), admin_url('admin.php?page=oQeySkins&showskins=yes') );
      echo '</p></div>';
}

function oqey_init_method() { 
   global $wpdb;
   
   $oqey_dir = basename(dirname(__FILE__));
   load_plugin_textdomain('oqey-gallery', false, $oqey_dir . '/languages'); 
   //oqey_check_upgrade();//make update if need to do

   wp_enqueue_script('swfobject'); 
   wp_enqueue_script('jquery');  
   
   wp_register_style('oQey-front-css', oQeyPluginUrl() . '/css/oqeystyle.css');
   wp_enqueue_style('oQey-front-css');
   
   if(isset($_GET['page'])){
   if(is_admin() && 
           ($_GET['page']=='oqey-gallery' ||           
            $_GET['page']=='oQeysettings' || 
            $_GET['page']=='oQeyGalleries' || 
            $_GET['page']=='oQeySkins' || 
            $_GET['page']=='oQeyMusic' || 
            $_GET['page']=='oQeyTrash' || 
            $_GET['page']=='oQeyVideo' ) ){

      wp_register_style('oQey-admin-css', oQeyPluginUrl() . '/css/oqeyadmin.css');
      wp_enqueue_style('oQey-admin-css');
      wp_register_style('oQey-admin-pop-css', oQeyPluginUrl() . '/css/jquery-ui.css');
      wp_enqueue_style('oQey-admin-pop-css');
      wp_enqueue_script('jquerysimplemodal', oQeyPluginUrl() . '/js/jquery.loadimages.min.js', array('jquery'),"", true);
      wp_enqueue_script('jqueryeditable', oQeyPluginUrl() . '/js/jquery.jeditable.js', array('jquery') ,"", true);   
     
    if($_GET['page']=='oQeyGalleries'){
     
      /*Admin if no skins is installed*/
      $oqey_skins = $wpdb->prefix . "oqey_skins";
      $r          = $wpdb->get_results( "SELECT skinid FROM $oqey_skins WHERE status !='2'"); 
      if(empty($r)){ 
        add_action( 'admin_notices', 'oqey_no_skins_installed');
      } 
     /*END*/
      
      
      
      wp_register_style('swipebox', oQeyPluginUrl() . '/css/swipebox.css');
      wp_enqueue_style('swipebox');
      wp_enqueue_script('swipebox', oQeyPluginUrl() . '/js/swipebox.js', array('jquery') ,"", true);
   
    }
    
    if(     $_GET['page']=='oQeyGalleries' || 
            $_GET['page']=='oQeySkins' || 
            $_GET['page']=='oQeyMusic' || 
            $_GET['page']=='oQeyVideo' ){
        
        if(function_exists( 'wp_enqueue_media' )){
            
            wp_enqueue_media(); 
            
        }else{
            
            wp_enqueue_style('thickbox');
            wp_enqueue_script('media-upload');
            wp_enqueue_script('thickbox');
            
        }
      
      }
    
    if(     $_GET['page']=='oQeysettings' || $_GET['page']=='oQeySkins'  ){
        
             wp_enqueue_script('iris');
             wp_enqueue_script('numeric', oQeyPluginUrl() . '/js/jquery.numeric.min.js', array('jquery') ,"", true);
           
       }       
    
           
    wp_enqueue_script('jquery-ui-core ');
    wp_enqueue_script('jquery-ui-sortable');  
    wp_enqueue_script('jquery-ui-draggable');
    wp_enqueue_script('jquery-ui-selectable');
    wp_enqueue_script('jquery-ui-dialog');
   
   if (is_plugin_active('oqey-photo-cropper/oqeycropper.php') && $_GET['page']=='oQeyGalleries'){
       
    wp_enqueue_script('jcrop'); 
    wp_register_style('oQey-admin-jCrop', WP_PLUGIN_URL . '/oqey-photo-cropper/css/jquery.jcrop.css');
    wp_enqueue_style('oQey-admin-jCrop');
    
   }elseif(is_plugin_active('oqey-add-ons/oqeyaddons.php')){
    
    wp_enqueue_script('jcrop');
    wp_register_style('oQey-admin-jCrop', WP_PLUGIN_URL . '/oqey-gallery/css/jquery.jcrop.css');
    wp_enqueue_style('oQey-admin-jCrop');
    
   }
   
   if($_GET['page']=="oQeyGalleries"){ 
    
     if(!function_exists("gd_info")){ add_action( 'admin_notices', 'oqey_gd_error'); }
 
   }
   
   if($_GET['page']=="oQeyTrash"){ 
     wp_enqueue_script('jquery-ui-tabs'); 
   }
  
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

add_filter( 'admin_footer_text', 'oqey_admin_footer_text' );
/**
 * Modify the footer text inside of the WordPress admin area.
 *
 * @since 1.0.0
 *
 * @param string $text The default footer text
 * @return string $text Amended footer text
 */
function oqey_admin_footer_text($text) {
 global $oqey_db_version;
 
	return sprintf( __( 'Your using oQey Gallery %s , <a href="%s" title="Click here to purchase a skin!">Click here to purchase a skin!</a>' ), $oqey_db_version, admin_url('admin.php?page=oQeySkins&showskins=yes') ).' | '.$text;
 
}

/* reactivate the plugin for multisite subdomains*/
function oqey_init_method_gallery_multisite(){
global $wpdb;

$oqey_galls = $wpdb->prefix . "oqey_gallery";
if($wpdb->get_var("show tables like '$oqey_galls'") != $oqey_galls ){ 
    oqey_db_install(); 
    
}

}
/*end*/ 

 if ( is_multisite() && is_admin() ) {
     
     add_action('init', 'oqey_init_method_gallery_multisite');
 
 } 
 
function oqey_gd_error(){
       echo '<div class="error fade" style="background-color:#E36464;">
             <p>'.__( 'Attention! Graphic Library missing. oQey Gallery requires GD library installed in order to run properly. Please install this php extension!', 'oqey-gallery' ).'</p></div>';
}

function oqey_php_version(){
     echo '<div class="error fade" style="background-color:#E36464;">
           <p>';
     printf(__('Attention! Your server php version is: %d oQey Gallery requires php version 5.2+ in order to run properly. Please upgrade your server!', 'oqey-gallery'), phpversion() );
     echo '</p></div>';
}
if( version_compare( '5.2', phpversion(), '>' ) ){ add_action( 'admin_notices', 'oqey_php_version'); }

function oqey_safe_mode(){
   if(ini_get('safe_mode')){
   
      echo '<div class="error fade" style="background-color:#E36464;">
            <p>'.__( 'Attention! Your server safe mode is: ON. oQey Gallery requires safe mode to be OFF in order to run properly. Please set your server safe mode option!', 'oqey-gallery' ).'
            </p></div>';
   }
}

if( version_compare( '5.3', phpversion(), '>' ) ){ add_action( 'admin_notices', 'oqey_safe_mode'); }

require("gallcore.php");


if(get_option('oqey_wp_native_run')=="on"){
    $tagnn = 'gallery';
    remove_shortcode( $tagnn );
    add_shortcode( 'gallery', 'AddoQeyGalleryToNativeWpGallery' );
}


function AddoQeyGalleryToNativeWpGallery($atts){
   global $oqeycounter, $wpdb, $post;
  
   $oqey_skins  = $wpdb->prefix . "oqey_skins";
     
   if (is_feed()) {

     //return AddoQeyGalleryToFeed($atts);

   }else{

   $allids               = $atts['ids'];   
   //$oqey_BorderSize  = get_option('oqey_BorderSize');
   $oqey_bgcolor     = get_option('oqey_bgcolor');
   $plugin_url       = oQeyPluginUrl();
   $oqey_plugin_url  = oQeyPluginRepoUrl();
   $imgs             = '';
   $nobject          = '';
   $object           = '';
   $incolums         = '';
   $incolumslazy     = '';
   $oqeyblogid       = '';
   $arrowleftright   = "";
   $arrowshtml       = "";
   $arrows           = "on";
   $wdetails         = '';
   
   $skinoptionsrecorded = "false";
   
   if(isset( $atts['width'] ) ){    
       
       $oqey_width = $atts['width']; 
       
   }else{   
       
       $wdetails   = (get_option('oqey_width_details')=='pr')?'%':'';
       $oqey_width = get_option('oqey_width').$wdetails; 
       
   }
   if(isset( $atts['height'] ) ){   $oqey_height   = $atts['height']; }else{    $oqey_height   = get_option('oqey_height'); }
   if(isset( $atts['autoplay'] ) ){ $oqey_autoplay = $atts['autoplay']; }else{  $oqey_autoplay = "false"; }
   if(isset( $atts['arrows'] ) ){   $arrows        = $atts['arrows']; }else{    $arrows        = 'on'; }
  

      /*get default skin*/         
      $skin      = $wpdb->get_row("SELECT * FROM $oqey_skins WHERE status = '1'"); 
      $options   = "oqey_skin_options_".$skin->folder; 
      $all       = json_decode(get_option($options));
         
      if(!empty($all)){
            
            $skinoptionsrecorded = "true";
        
      }      
      
      $link = oQeyPluginRepoPath().'/skins/'.oqey_getBlogFolder($wpdb->blogid).$skin->folder.'/'.$skin->folder.'.swf';
      
      if(!is_file($link)){
        
         $skin    = $wpdb->get_row("SELECT * FROM $oqey_skins WHERE status != '2' LIMIT 0,1"); 
         $options = "oqey_skin_options_".$skin->folder; 
         $all     = json_decode(get_option($options));
         
         if(!empty($all)){
            
            $skinoptionsrecorded = "true";
         
         }         
      }
	
        $gal_title = "";
        
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
    if( get_option('oqey_noflash_options')=="injsarr" ){ 
        
        $optouch  = " data-cycle-swipe=true"; 
	
    }
    
    if( get_option('oqey_noflash_options')=="incolums" ){  
	   
	   $incolums = "on";
           $optouch  = ""; 
        }
    
    if( get_option('oqey_noflash_options')=="incolumslazy"  ){
        
        $incolumslazy = "on";
        $incolums     = "on";
        
    }
    
    if($arrows=="off"){
        
        $arrowleftright = "";
        $arrowshtml     = "";
    
    }else{
        
        $arrowleftright = ' data-cycle-prev=".prevControl'.$oqeycounter.'" data-cycle-next=".nextControl'.$oqeycounter.'"'; 
        
        if($incolums!="on"){
        $arrowshtml    .= '<span class=center><span class="prevControl prevControl'.$oqeycounter.'"></span><span class="nextControl nextControl'.$oqeycounter.'"></span></span>';
        }
        
    } 
    
    oQeyAddJavaScriptLibraries($incolumslazy); // load script for javascript slideshow
   
        
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
    $Border    = get_option('oqey_BorderOption');
    $oqeybgcss = '';
    
    if($Border){
        
        //$oqeyBorderColor = get_option('oqey_BorderSize');
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
      
      $nobject .='data-cycle-loader=true data-cycle-progressive="#slides'.$oqeycounter.'"'.$autostart.''.$optouch.''.$arrowleftright.''.$effecttr.' data-cycle-slides=">div,>img"';
    }
    
    $nobject .= '>';
    $nobject .= $arrowshtml;
    $d        = 0;
    $nobject2 = "";
    
    //$gimg     = get_option('siteurl').'/'.trim($nggpath).'/'; 
    
    $data    = explode("-", $allids);
    $ids     = explode(",", $data[0]);
    $bgimage = wp_get_attachment_url($ids[0]); //splash image 

    foreach($ids as $id) { 
       
       $imgurl   = wp_get_attachment_url($id);
       //$thumburl = wp_get_attachment_thumb_url( $id );
       $p        = get_post($id);
       
       $srcdata = ($incolumslazy=="on")?"data-original":"src";
	
       if($d<1 || $incolums=="on"){
              $nobject .= '<div class="oqeyincolumn"><img class="lazy'.$oqeycounter.'" '.$srcdata.'="'.$imgurl.'" alt="'.trim($p->post_excerpt).'"/></div>'."\n";
       }else{
              $nobject2 .='<div><img src="'.$imgurl.'" title="'.trim($p->post_excerpt).'" style="max-width:100%;"/></div> --- '."\n";
       }
                                                                 
      if(get_option('oqey_show_captions_under_photos')=="on" && get_option('oqey_noflash_options')=="incolums" ){
                
              $nobject .= '<p class="oqey_p_comments">'.trim($p->post_excerpt)."</p>";
                
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
    
    $backlink = "";  
    
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
       $object .= 'GalleryID:"'.$allids.'-'.$post->ID.'",'."\n";
       $object .= 'FirstRun:"'.trim($skin->firstrun).'"'."\n";
       $object .= '};'."\n";
       $object .= 'var params'.$oqeycounter.'     = {bgcolor:"'.$oqey_bgcolor.'", allowFullScreen:"true", wMode:"transparent"};'."\n";
       $object .= 'var attributes'.$oqeycounter.' = {id: "oqeygallery'.$oqeycounter.'"};'."\n";
       $object .= 'swfobject.embedSWF("'.$oqey_plugin_url.'/skins/'.$oqeyblogid.trim($skin->folder).'/'.trim($skin->folder).'.swf", "flash_gal_'.$oqeycounter.'", "'.$oqey_width.'", "'.$oqey_height.'", "8.0.0", "", flashvars'.$oqeycounter.', params'.$oqeycounter.', attributes'.$oqeycounter.');'."\n";
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
}

?>