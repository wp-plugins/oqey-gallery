<?php
include ("../../../wp-load.php");
global $wpdb;   

if(get_option("oqey_license")==""){ die(); }

if(isset($_REQUEST['gal_id'])){

   $oqey_galls = $wpdb->prefix . "oqey_gallery";
   $oqey_images = $wpdb->prefix . "oqey_images";

   $data = explode("-", $_REQUEST['gal_id']);

   $id = $data[0];
   $pid = $data[1];//post id
/*
   $content = $wpdb->get_var("SELECT post_content FROM $wpdb->posts WHERE ID = '$pid' AND post_type != 'revision'");
   $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $content, $m);
*/
   $s = $wpdb->get_row("SELECT * FROM $oqey_galls WHERE id ='".$id."' ");

   $gthmb = get_option('siteurl').'/wp-content/oqey_gallery/galleries/'.oqey_getBlogFolder($wpdb->blogid).$s->folder.'/galthmb/';
   $gimg = get_option('siteurl').'/wp-content/oqey_gallery/galleries/'.oqey_getBlogFolder($wpdb->blogid).$s->folder.'/galimg/';


   if(!empty($m[0])){ 
     $gthmb2 = "";
     $gimg2 = "";
     
   }else{
     $gthmb2 = $gthmb;
     $gimg2 = $gimg;
     $gthmbnew = "";
     $gimgnew = "";
   }

     if($s->splash_img !=0){
	 	$bg = $wpdb->get_row("SELECT * FROM $oqey_images WHERE id ='".$s->splash_img."' AND status!=2 ");
									   
     if(!$bg){		
		$bg = $wpdb->get_row("SELECT * FROM $oqey_images WHERE gal_id ='".$id."' AND status!=2 ORDER BY img_order ASC LIMIT 0,1 ");		
	 }
		
	 }else{ $bg = $wpdb->get_row("SELECT * FROM $oqey_images WHERE gal_id ='".$id."' AND status!=2 ORDER BY img_order ASC LIMIT 0,1 "); } 
      
      
     if( $s->splash_only == 1){ 
         $imgs = $wpdb->get_results("SELECT * FROM $oqey_images WHERE gal_id = '".$id."' AND id !='".$s->splash_img."' AND status!=2 ORDER BY img_order ASC");
     }else{ 
         $imgs = $wpdb->get_results("SELECT * FROM $oqey_images WHERE gal_id = '".$id."' AND status!=2 ORDER BY img_order ASC"); 
     }
   
     if($bg->img_type=="nextgen"){
       $bg_image = get_option('siteurl').'/'.trim($bg->img_path).'/'.trim($bg->title);   
     }else{
       $bg_image = $gimg.trim($bg->title);
     }  
   
   header('Content-Type: ' . feed_content_type('rss-http') . '; charset=' . get_option('blog_charset'), true);
   $r .= '<?xml version="1.0" encoding="UTF-8"?>';
   $r .= '<oqeygallery bgpath="'.$bg_image.'" galtitle="'.urlencode($s->title).'" path="" imgPath="">'; 
    
   foreach($imgs as $i) { 
    
    if($i->img_type=="nextgen"){
       $gthmbnew = get_option('siteurl').'/'.trim($i->img_path).'/thumbs/thumbs_';
       $gimgnew = get_option('siteurl').'/'.trim($i->img_path).'/';   
     }else{
       $gthmbnew = $gthmb;
       $gimgnew = $gimg;
     }      
    $r .= '<item><thumb file="'.$gthmbnew.trim($i->title).'" alt="'.urlencode(trim($i->alt)).'" comments="'.urlencode(trim($i->comments)).'" link="'.urlencode(trim($i->img_link)).'"/>
    <image file="'.$gimgnew.trim($i->title).'" alt="'.urlencode(trim($i->alt)).'" comments="'.urlencode(trim($i->comments)).'" link="'.urlencode(trim($i->img_link)).'"/></item>';
    }

/*
   $j=0;
   if(!empty($m[0])){
   foreach($m[1] as $i) { 

    $a = preg_match_all('/(alt=.)([a-zA-Z0-9\s]{1,})/',$m[0][$j],$p); 

    $r .= '<item><thumb file="'.trim($i).'" alt="'.trim($p[2][0]).'" comments="" /><image file="'.trim($i).'" alt="'.trim($p[2][0]).'" comments="" /></item>';
    
    $j++; 
    }
   }
   */
   $r .= '</oqeygallery>';
   echo $r;
  
  }else{ 
    die();
}
?>