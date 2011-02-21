<?php
include ("../../../wp-load.php");
global $wpdb;   

if(get_option("oqey_license")==""){ die(); }

   $oqey_galls = $wpdb->prefix . "oqey_gallery";
   $oqey_images = $wpdb->prefix . "oqey_images";

$id = mysql_real_escape_string($_REQUEST['gal_id']);

$s = $wpdb->get_row("SELECT * FROM $oqey_galls WHERE id ='".$id."' ");

$gthmb = get_option('siteurl').'/wp-content/oqey_gallery/galleries/'.$s->folder.'/galthmb/';
$gimg = get_option('siteurl').'/wp-content/oqey_gallery/galleries/'.$s->folder.'/galimg/';

      if($s->splash_img !=0){
	 	$bg = $wpdb->get_row("SELECT * FROM $oqey_images WHERE id ='".$s->splash_img."' AND status!=2 ");
									   
	    if(!$bg){		
		$bg = $wpdb->get_row("SELECT * FROM $oqey_images WHERE gal_id ='".$id."' AND status!=2 ORDER BY img_order ASC LIMIT 0,1 ");		
		}
		
	  }else{ $bg = $wpdb->get_row("SELECT * FROM $oqey_images WHERE gal_id ='".$id."' AND status!=2 ORDER BY img_order ASC LIMIT 0,1 "); } 

header('Content-Type: ' . feed_content_type('rss-http') . '; charset=' . get_option('blog_charset'), true);
$r .= '<?xml version="1.0" encoding="UTF-8"?>';
$r .= '<oqeygallery bgpath="'.$gimg.trim($bg->title).'" galtitle="'.urlencode($s->title).'" path="'.$gthmb.'" imgPath="'.$gimg.'">'; 

if( $s->splash_only == 1){ 
$imgs = $wpdb->get_results("SELECT * FROM $oqey_images WHERE gal_id = '".$id."' AND id !='".$s->splash_img."' AND status!=2 ORDER BY img_order ASC");
}else{ 
$imgs = $wpdb->get_results("SELECT * FROM $oqey_images WHERE gal_id = '".$id."' AND status!=2 ORDER BY img_order ASC"); 
}

foreach($imgs as $i) { 
$r .= '<item>
<thumb file="'.trim($i->title).'" />
<image file="'.trim($i->title).'" />
</item>
';
}
$r .= '</oqeygallery>';
echo $r;
?>