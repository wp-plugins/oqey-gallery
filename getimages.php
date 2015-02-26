<?php
require_once('oqeyload.php');
global $wpdb;   

if(isset($_POST['gal_id'])){

   $oqey_galls  = $wpdb->prefix . "oqey_gallery";
   $oqey_images = $wpdb->prefix . "oqey_images";
   $r           = '';
   $galqlrequest= '';
   $withvideo   = '';
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
   
   //set_transient( $transientName, $r);
   
   echo $r;
       
   }else{
       
   $id    = absint( $data[0] );
   $transientXmlName = "oqeygallery_xml_".$id;
       
   if ( false === ( $ImagesXml = get_transient( $transientXmlName ) ) ) { // if transient is not set, let set it.
   
      
       $gal   = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $oqey_galls WHERE id = %d ", $id ) );
   
   if( isset($_REQUEST['withvideo']) ){
      
      if($_REQUEST['withvideo']=="true"){
         
         $s         = "";
         $withvideo = 'on';
         
      }else{
        
         $s         = " AND img_type!='video'";
        
      }
      
   }
     
     if($gal->splash_img !=0){
        
	 $bg = $wpdb->get_row("SELECT * FROM $oqey_images WHERE id ='".$gal->splash_img."' AND status!=2 ");
									   
     if(!$bg){	
        
         $bg = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $oqey_images WHERE gal_id = %d AND status!= %d AND img_type != %s ORDER BY img_order ASC LIMIT 0,1 ", $id, "2", "video" ) );	
	 
     }
		
	 }else{ 

         $bg = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $oqey_images WHERE gal_id = %d AND status!=%d AND img_type != %s ORDER BY img_order ASC LIMIT 0,1 ", $id, "2", "video" ) );         
     } 
      
      
     /*if( $gal->splash_only == 1){ 
        
         $imgs = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $oqey_images WHERE gal_id = %d AND id !='".$gal->splash_img."' AND status!=2 ".$galqlrequest." ORDER BY img_order ASC", $id ) );
     
     }else{ 
     
         $imgs = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $oqey_images WHERE gal_id = %d AND status!=2 ".$galqlrequest." ORDER BY img_order ASC", $id ) ); 
     
     }*/
     
     if($gal->splash_only==1){ 
        
          $s = " AND id!=".$gal->splash_img; 
      
      }else{ 
        
          $s = ""; 
      
      }
     
     $transientName = "oqeygalery_images_".$id;
      
      if ( false === ( $imgs = get_transient( $transientName ) ) ) {
          
          $imgs = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $oqey_images WHERE gal_id = %d AND status!=2 ".$s." ORDER BY img_order ASC", $id  ));
          set_transient( $transientName, $imgs); 
          
      }
   
     if($bg->img_type=="nextgen"){
        
         $bg_image = get_option('siteurl').'/'.trim($bg->img_path).'/'.trim($bg->title);   
     
     }elseif($bg->img_type=="wp"){
        
         $bg_image = trim($bg->title);   
     
     }else{
         
         $image    = oQeyGetImageLink($bg, $gal);
         $bg_image = $image['img'];
     
     }  
   
   header('Content-Type: ' . feed_content_type('rss-http') . '; charset=' . get_option('blog_charset'), true);
   $r .= '<?xml version="1.0" encoding="UTF-8"?>';
   $r .= '<oqeygallery bgpath="'.$bg_image.'" galtitle="'.urlencode($gal->title).'" path="" imgPath="">'; 
    
   foreach($imgs as $i) { 
       
    $exif = '';
    
    $image = oQeyGetImageLink($i, $gal);
    
    if($i->img_type=="video" && $withvideo=="on"){
        
       $url = parse_url(urldecode($i->title));
       if( !empty($url['host']) ){
        
          $vurl = trim($i->title);
        
       }else{
        
          $vurl = get_option('siteurl').'/'.trim($i->title);
        
       }
          
       $r .= '<item>';
       $r .= '<thumb file="'.$image['img'].'" alt="'.$image['alt'].'" comments="'.$image['comments'].'" link="'.$image['link'].'"/>';
       $r .= '<video type="video" file="'.$vurl.'" alt="'.$image['alt'].'" comments="'.$image['comments'].'" link="'.$image['link'].'"/>';
       $r .= '</item>';
    
      }else{
      
       $r .= '<item>';
       $r .= '<thumb file="'.$image['thmb'].'" alt="'.$image['alt'].'" comments="'.$image['comments'].'" link="'.$image['link'].'"/>';
       $r .= '<image file="'.$image['img'].'" alt="'.$image['alt'].'" comments="'.$image['comments'].'" link="'.$image['link'].'">';
       
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
   
   
   set_transient( $transientXmlName, $r);
   
   echo $r;
   
   }else{
      
      echo $ImagesXml;
      
  }
     
   }
   
  
   
  }else{ 
    
    die("Access denied. Security check failed! What are you trying to do? It`s not working like that. ");  
}
?>