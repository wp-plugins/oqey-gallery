<?php
/*oQey gallery*/
require_once(dirname(dirname(dirname(dirname(__FILE__))))."/wp-load.php");
global $wpdb;   

if(isset($_POST['gal_id'])){

   $oqey_galls  = $wpdb->prefix . "oqey_gallery";
   $oqey_images = $wpdb->prefix . "oqey_images";
   $r           = '';
   $sqlrequest  = '';
   $withvideo   = '';
   $gthmbnew    = '';
   $gimgnew     = '';
   $bgimage     = '';

   $data  = explode("-", $_POST['gal_id']);
   $ids   = explode(",", $data[0]);
   
   if(count($ids)>1){
       
       $bgimage = wp_get_attachment_url( $ids[0] ); //splash image 

   header('Content-Type: ' . feed_content_type('rss-http') . '; charset=' . get_option('blog_charset'), true);
   $r .= '<?xml version="1.0" encoding="UTF-8"?>'."\n";
   $r .= '<oqeygallery bgpath="'.$bgimage.'" galtitle="Gallery" path="" imgPath="">'."\n"; 
    
   foreach($ids as $id) { 
       
       $imgurl   = wp_get_attachment_url( $id );
       $thumburl = wp_get_attachment_thumb_url( $id );
       $post     = get_post($id);
       
       $r .= '<item>'."\n";
       $r .= '<thumb file="'.$thumburl.'" alt="'.urlencode($post->post_excerpt).'" comments="'.urlencode($post->post_content).'" link=""/>'."\n";
       $r .= '<image file="'.$imgurl.'" alt="'.urlencode($post->post_excerpt).'" comments="'.urlencode($post->post_content).'" link="">'."\n";    
       $r .= '</image>'."\n";
       $r .= '</item>'."\n";
       
   }

   $r .= '</oqeygallery>'."\n";
   
   echo $r;
       
       
   }else{
       
   $id    = absint( $data[0] );
   $s     = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $oqey_galls WHERE id = %d ", $id ) );
   $gthmb = get_option('siteurl').'/wp-content/oqey_gallery/galleries/'.oqey_getBlogFolder($wpdb->blogid).$s->folder.'/galthmb/';
   $gimg  = get_option('siteurl').'/wp-content/oqey_gallery/galleries/'.oqey_getBlogFolder($wpdb->blogid).$s->folder.'/galimg/';

   if( isset($_REQUEST['withvideo']) ){
      
      if($_REQUEST['withvideo']=="true"){
         
         $sqlrequest = "";
         $withvideo  = 'on';
        
      }else{
        
         $sqlrequest = "AND img_type!='video'";
        
      }
      
   }
     
     if($s->splash_img !=0){
        
	 $bg = $wpdb->get_row("SELECT * FROM $oqey_images WHERE id ='".$s->splash_img."' AND status!=2 ");
									   
     if(!$bg){	
        
         $bg = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $oqey_images WHERE gal_id = %d AND status!= %d AND img_type != %s ORDER BY img_order ASC LIMIT 0,1 ", $id, "2", "video" ) );	
	 
     }
		
	 }else{ 

         $bg = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $oqey_images WHERE gal_id = %d AND status!=%d AND img_type != %s ORDER BY img_order ASC LIMIT 0,1 ", $id, "2", "video" ) );         
     } 
      
      
     if( $s->splash_only == 1){ 
        
         $imgs = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $oqey_images WHERE gal_id = %d AND id !='".$s->splash_img."' AND status!=2 ".$sqlrequest." ORDER BY img_order ASC", $id ) );
     
     }else{ 
     
         $imgs = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $oqey_images WHERE gal_id = %d AND status!=2 ".$sqlrequest." ORDER BY img_order ASC", $id ) ); 
     
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
       
    $exif = '';
    
    if($i->img_type=="nextgen"){
        
       $gthmbnew = get_option('siteurl').'/'.trim($i->img_path).'/thumbs/thumbs_';
       $gimgnew  = get_option('siteurl').'/'.trim($i->img_path).'/';   
     
     }elseif($i->img_type=="video"){
        
        $imgroot = OQEY_ABSPATH.trim($i->img_path);
        
        if(is_file($imgroot)){       
           
            $gthmbnew = get_option('siteurl').'/'.trim($i->img_path);
        
        }else{
            
            $gthmbnew = oQeyPluginUrl().'/images/no-2-photo.jpg';
            
        }
        
    }else{
     
       $gthmbnew = $gthmb;
       $gimgnew  = $gimg;
     
    }
    
    if($i->img_type=="video" && $withvideo=="on"){
        
       $url = parse_url(urldecode($i->title));
       if( !empty($url['host']) ){
        
          $vurl = trim($i->title);
        
       }else{
        
          $vurl = get_option('siteurl').'/'.trim($i->title);
        
       }
          
       $r .= '<item>';
       $r .= '<thumb file="'.$gthmbnew.'" alt="'.urlencode(trim($i->alt)).'" comments="'.urlencode(trim($i->comments)).'" link="'.urlencode(trim($i->img_link)).'"/>';
       $r .= '<video type="video" file="'.$vurl.'" alt="'.urlencode(trim($i->alt)).'" comments="'.urlencode(trim($i->comments)).'" link="'.urlencode(trim($i->img_link)).'"/>';
       $r .= '</item>';
    
      }else{
      
       $r .= '<item>';
       $r .= '<thumb file="'.$gthmbnew.trim($i->title).'" alt="'.urlencode(trim($i->alt)).'" comments="'.urlencode(trim($i->comments)).'" link="'.urlencode(trim($i->img_link)).'"/>';
       $r .= '<image file="'.$gimgnew.trim($i->title).'" alt="'.urlencode(trim($i->alt)).'" comments="'.urlencode(trim($i->comments)).'" link="'.urlencode(trim($i->img_link)).'">';
       
       if(isset($_REQUEST['withexif'])){
        if($_REQUEST['withexif']=="true"){
        
        $r .= '<exif>';
                $exif = json_decode($i->meta_data);
             
             if(!empty($exif->Make)){ 
                
                $r .='<parametru name="Make" value="'.urlencode($exif->Make).'" />';
             
             }
             if(!empty($exif->Model)){
                
               $r .='<parametru name="Model" value="'.urlencode($exif->Model).'" />';
             
             }
             if(!empty($exif->DateTime)){
                
               $r .='<parametru name="DateTime" value="'.urlencode($exif->DateTime).'" />';
             
             }
             if(!empty($exif->Software)){
                
               $r .='<parametru name="Software" value="'.urlencode($exif->Software).'" />';
             
             }
             if(!empty($exif->Artist)){
                
               $r .='<parametru name="Artist" value="'.urlencode($exif->Artist).'" />';
             
             }
             if(!empty($exif->ExposureTime)){
                
               $r .='<parametru name="ExposureTime" value="'.urlencode($exif->ExposureTime).'" />';
             
             }
             if(!empty($exif->FNumber)){ 
                
                $r .='<parametru name="FNumber" value="'.urlencode($exif->FNumber).'" />';
             
             }
             if(!empty($exif->ExposureProgram)){ 
                
                $r .='<parametru name="ExposureProgram" value="'.urlencode($exif->ExposureProgram).'" />';
             
             }
             if(!empty($exif->ISOSpeedRatings)){ 
                
                $r .='<parametru name="ISOSpeedRatings" value="'.urlencode($exif->ISOSpeedRatings).'" />';
             
             }
             if(!empty($exif->COMPUTED->CCDWidth)){ 
                
                $r .='<parametru name="CCDWidth" value="'.urlencode($exif->COMPUTED->CCDWidth).'" />';
             
             }
             
        
        $r .= '</exif>';       
                
       }
      }
       $r .= '</image>';
       $r .= '</item>';
       
    }    
    
 }

   $r .= '</oqeygallery>';
   
   echo $r;
  
   }
   
  }else{ 
    
    die("Access denied. Security check failed! What are you trying to do? It`s not working like that. ");
    
}
?>