<?php
require_once('oqeyload.php');
global $wpdb;

if(isset($_REQUEST['galleryid'])){
    
    $r                = "";
    $id               = absint(trim($_REQUEST['galleryid']));
    $oqey_music       = $wpdb->prefix . "oqey_music";
    $oqey_music_rel   = $wpdb->prefix . "oqey_music_rel";
    $transientXmlName = "oqeygallery_m_xml_".$id;
       
if ( false === ( $MusicXml = get_transient( $transientXmlName ) ) ) { // if transient is not set, let set it.

header('Content-Type: ' . feed_content_type('rss-http') . '; charset=' . get_option('blog_charset'), true);
$r .= '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>';
$r .= '<songs>'; 

          $mus = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $oqey_music AS f 
                                                        INNER JOIN $oqey_music_rel AS s 
					                        ON f.id = s.music_id
					                     WHERE s.gallery_id = %d
					                  ORDER BY s.mrel_order ASC", $id
                                                    )  
                                   ); 
   if(!empty($mus)){
       foreach($mus as $m){ 
           $r .= '<song path="'.urlencode(trim($m->link)).'" artist="" title="'.urlencode(trim($m->title)).'"></song>';
       }
           
       }else{
           $mus = $wpdb->get_row( "SELECT * FROM $oqey_music WHERE status !=2 ORDER BY id ASC LIMIT 0,1" );
           $r  .= '<song path="'.urlencode(trim($mus->link)).'" artist="" title="'.urlencode(trim($mus->title)).'"></song>'."\n";
       }
      $r .= '</songs>';
         
   set_transient( $transientXmlName, $r);
   
   echo $r;
   
   }else{
      
      echo $MusicXml;
      
  }
}
?>