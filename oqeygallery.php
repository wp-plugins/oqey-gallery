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
Version: 0.4.6
Description: oQey Gallery plugin is the best photo gallery / slideshow solution for your custom blog. It has a built-in skinnable slideshow and supports music also.
Author: oqeysites.com
Author URI: http://oqeysites.com/
*/

if (!empty($_SERVER['SCRIPT_FILENAME']) && 'oqeygallery.php' == basename($_SERVER['SCRIPT_FILENAME']))
	die ('Please do not load this page directly. Thanks!');
	
define('OQEY_ABSPATH', str_replace('\\', '/', ABSPATH) );
define('OQEY_LICENSE_TEXT', '
	<p>oQey Gallery License</p>
<p>Preamble</p>
<p>oQey Gallery photo slideshow plugin for WordPress is distributed under the GNU General Public
  License. However, please note that oQey Gallery skins and Adobe Flash Player (*.swf) files 
  included in the plugin and (or) available for download from oQeySites website are distributed under 
  the oQey Gallery Non-Commercial License. Commercial oQey Gallery skins for the oQey Gallery 
  photo slideshow plugin are distributed under the terms of the oQey Gallery Commercial Professional 
License.</p>
<p>GNU General Public License</p>
<p>1. (c) 2011 by oQeySites<br />
  2. All php files included in the oQey Gallery photo slideshow plugin package are subject to the terms 
  of the GNU GENERAL PUBLIC LICENSE as published by the FREE SOFTWARE 
  FOUNDATION, either version 0.1 or any later versions, and can be modified and/or redistributed 
  pursuant to the terms contained therein.<br />
  3. For a copy of the GNU GENERAL PUBLIC LICENSE write to the Free Software Foundation, 
  Inc., 51 Franklin St, Suite 500, Boston, MA 02110-1335 USA, or visit their website at 
  http://www.fsf.org/. You can also view the text of the GNU GENERAL PUBLIC LICENSE 
  version 2 here http://wordpress.org/about/gpl/.<br />
  4. Programs herein are distributed in the hope that they will be useful, but WITHOUT 
  WARRANTY OF ANY KIND, EITHER EXPRESSED OR IMPLIED, INCLUDING, BUT NOT 
  LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR 
  A PARTICULAR PURPOSE. THE ENTIRE RISK AS TO THE QUALITY AND 
  PERFORMANCE OF THE PROGRAM IS WITH YOU. SHOULD THE PROGRAM PROVE 
  DEFECTIVE, YOU ASSUME THE COST OF ALL NECESSARY SERVICING, REPAIR OR 
  CORRECTION. Please see the GNU General Public License for more details.</p>
<p>oQey Gallery Non-Commercial License</p>
<p>1. Definitions</p>
<p> * &quot;oQey Gallery skin&quot; means a collection of files included in a folder or a zip archive that 
  makes it a product, which can be used by oQey Gallery photo slideshow plugin for 
  WordPress in order to change the way it looks / works and the functionality of the oQey <br />
  Gallery photo slideshow plugin for WordPress. oQey Gallery skins can be distributed under 
  oQey Gallery Non-Commercial License or under oQey Gallery Commercial License. <br />
  * &quot;Redistribution&quot; means to make available to the public the original or copies of the software product, as appropriate, through making it available for download or any other known method of a data transfer. </p>
<p>2. License</p>
<p>This Product is provided to you free of charge for any use other than any commercial use, including 
  redistribution for a fee. Adobe Flash Source (*.fla) files are not included in the oQey Gallery plugin 
  for WordPress package and will not be provided in any other editable format. Adobe Flash Source 
  (*.fla) and Adobe Flash Player (*.swf) files remain the property of oQeySites.
  Non-commercial redistribution of the Product oQey Gallery plugin is allowed, however, you cannot 
  change the name of the product, oQey Gallery, and you shall distribute the package in its entirety, 
  including the document titled documentation.txt.
  Before commercial redistribution of the oQey Gallery plugin for WordPress and before changing the 
  name of the Product, you shall obtain a written permission of the copyright holders, whose contact 
  information can be found here http://oqeysites.com/contact/. Before commercial redistribution of the oQey Gallery skin and before changing the name of this product, you shall obtain a written permission of the copyright holders, whose contact information can be found here http://oqeysites.com/contact/. A Copyright notice and a link back to the oQey Sites website (http://oqeysites.com) is appreciated but not obligatory.</p>
<p>DISCLAIMER OF WARRANTY</p>
<p>THERE IS NO WARRANTY FOR THE PRODUCT, TO THE EXTENT PERMITTED BY 
  APPLICABLE LAW. EXCEPT WHEN OTHERWISE STATED IN WRITING, THE 
  COPYRIGHT HOLDERS AND/OR OTHER PARTIES PROVIDE THE PRODUCT &quot;AS IS&quot;  WITHOUT WARRANTY OF ANY KIND, EITHER EXPRESSED OR IMPLIED, 
  INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF 
  MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE. THE ENTIRE RISK 
  AS TO THE QUALITY AND PERFORMANCE OF THE PRODUCT IS WITH YOU. SHOULD 
  THE PRODUCT PROVE DEFECTIVE, YOU ASSUME THE COST OF ALL NECESSARY 
  SERVICING, REPAIR OR CORRECTION.<br />
  LIMITATION OF LIABILITY
  IN NO EVENT, UNLESS REQUIRED BY APPLICABLE LAW, OR AGREED TO IN 
  WRITING, WILL ANY COPYRIGHT HOLDER, OR ANY OTHER PARTY WHO MODIFIES 
  AND/OR CONVEYS THE PRODUCT AS PERMITTED ABOVE, BE LIABLE TO YOU FOR 
  DAMAGES, INCLUDING ANY GENERAL, SPECIAL, INCIDENTAL OR 
  CONSEQUENTIAL DAMAGES ARISING OUT OF THE USE OR INABILITY TO USE THE 
  PRODUCT (INCLUDING BUT NOT LIMITED TO LOSS OF DATA OR DATA BEING 
  RENDERED INACCURATE OR LOSSES SUSTAINED BY YOU OR THIRD PARTIES OR A 
  FAILURE OF THE PRODUCT TO OPERATE WITH ANY OTHER PROGRAMS), EVEN IF <br />
  SUCH HOLDER OR OTHER PARTY HAS BEEN ADVISED OF THE POSSIBILITY OF 
SUCH DAMAGES.</p>
	');

include("oqey-gallery-functions.php");
global $oqey_db_version;	
       $oqey_db_version = "0.4";

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
  
	add_option("oqey_width", "900" ,'', 'yes');
	add_option("oqey_height", "600" ,'', 'yes');
	add_option("oqey_bgcolor", "#ffffff" ,'', 'yes');
	add_option("oqey_thumb_width", "120" ,'', 'yes');
	add_option("oqey_thumb_height", "80" ,'', 'yes');
	add_option("oqey_default_gallery_skin", "0" ,'', 'yes');
	add_option("oqey_effects_trans_time", "0.5" ,'', 'yes');
	add_option("oqey_pause_between_tran", "6" ,'', 'yes');
	add_option("oqey_LoopOption", "on" ,'', 'yes');
	add_option("oqey_license", "" ,'', 'yes');
	add_option("oqey_backlinks", "on" ,'', 'yes');
	add_option("oqey_BorderOption", "on" ,'', 'yes');
	add_option("oqey_AutostartOption", "" ,'', 'yes');
	add_option("oqey_CaptionsOption", "" ,'', 'yes');
	add_option("oqey_options", "TM" ,'', 'yes');
	add_option("oqey_noflash_options", "incolums" ,'', 'yes');	
	add_option("oqey_gall_title_no", "on" ,'', 'yes');
	add_option("oqey_border_bgcolor", "#000000" ,'', 'yes');
    
    add_option("oqey_gallery_version", "0.4.6");
    add_option("oqey_db_version", $oqey_db_version);
			
  $gal_dir_up = OQEY_ABSPATH.'wp-content/oqey_gallery/galleries/'.oqey_getBlogFolder($wpdb->blogid);   
  wp_mkdir_p ($gal_dir_up); // make the gallery folder - root

  $music_dir = OQEY_ABSPATH.'wp-content/oqey_gallery/music/'.oqey_getBlogFolder($wpdb->blogid);    
  wp_mkdir_p ($music_dir); // make the music folder - root
  
  $skins_dir = OQEY_ABSPATH.'wp-content/oqey_gallery/skins/'.oqey_getBlogFolder($wpdb->blogid); 
  $skin_file = OQEY_ABSPATH.'wp-content/plugins/oqey-gallery/skins/skins.zip';
  wp_mkdir_p ($skins_dir); // make the music folder - root

    if(is_dir($skins_dir) && is_file($skin_file) ){
  			
		if ( class_exists('ZipArchive') ){
			$zip = new ZipArchive;
	        $zip_file = $skin_file;
            $zip->open($zip_file);
	        $zip_extract = OQEY_ABSPATH.'wp-content/oqey_gallery/skins/'.oqey_getBlogFolder($wpdb->blogid);
            $zip->extractTo($zip_extract);
            $zip->close();
		}else{
			require_once(OQEY_ABSPATH . 'wp-admin/includes/class-pclzip.php');			
			$archive = new PclZip($skin_file);
            $list = $archive->extract($skins_dir);
            if ($list == 0) {
            die("ERROR : '".$archive->errorInfo(true)."'");
            }
		}
    }
  
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
		`skinid` varchar(4) NOT NULL DEFAULT '',
		`firstrun` int(1) NOT NULL DEFAULT '1',
		PRIMARY KEY  (`id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";	
    $wpdb->query($sql6);	
	
    
   $wpdb->query("INSERT INTO $oqey_skins (name, description, folder, status, commercial, skinid) VALUES ('oQey Default Skin', '".esc_sql(stripslashes_deep("That's the default gray skin that comes with the free oQey Galleries pack"))."', 'oqeymainsk', '1', 'no', '0001')");
   $wpdb->query("INSERT INTO $oqey_skins (name, description, folder, status, commercial, skinid) VALUES ('Dark Theme Skin', '".esc_sql(stripslashes_deep("Just another free skin for dark wordpress themes"))."', 'darkfreesk', '0', 'no', '0002')");
}
   
   // Upgrade table code
   $installed_oqey_ver = get_option( "oqey_db_version" );

   if( $installed_oqey_ver != $oqey_db_version ) {	   
	   
	   if(!$sql=$wpdb->get_row($wpdb->prepare("SELECT id FROM $oqey_skins WHERE skinid = '%s' ", "0001" ))){
		   $wpdb->query("INSERT INTO $oqey_skins (name, description, folder, status, commercial, skinid) VALUES ('oQey Default Skin', '".esc_sql(stripslashes_deep("That's the default gray skin that comes with the free oQey Galleries pack"))."', 'oqeymainsk', '1', 'no', '0001')");
		   }else{
		   $wpdb->query( $wpdb->prepare("UPDATE {$oqey_skins} SET name = 'oQey Default Skin', folder = 'oqeymainsk'  WHERE skinid='0001'") );
		   
		   $durl = OQEY_ABSPATH. 'wp-content/oqey_gallery/skins/'.oqey_getBlogFolder($wpdb->blogid).'default';
		   if(is_dir($durl)){ 		   
		   oqey_rm($durl);
		   }
		   }
	   
	   if(!$sql=$wpdb->get_row($wpdb->prepare("SELECT id FROM $oqey_skins WHERE skinid = '%s' ", "0002" ))){
		   $wpdb->query("INSERT INTO $oqey_skins (name, description, folder, status, commercial, skinid) VALUES ('Dark Theme Skin', '".esc_sql(stripslashes_deep("Just another free skin for dark wordpress themes"))."', 'darkfreesk', '0', 'no', '0002')");
	      }

       update_option( "oqey_db_version", $oqey_db_version );
  }
}

if (function_exists('register_activation_hook')){
	register_activation_hook( __FILE__, 'oqey_db_install' );
}
 $plugin_name = "oQey Gallery plugins";
//.................................................................//	
function oqey_init_method() {   

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