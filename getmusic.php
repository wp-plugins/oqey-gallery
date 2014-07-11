<?php
require_once(dirname(dirname(dirname(dirname(__FILE__))))."/wp-load.php");
global $wpdb;

if(isset($_REQUEST['galleryid'])){
    
    $result         = "";
    $id             = absint(trim($_REQUEST['galleryid']));
    $oqey_music     = $wpdb->prefix . "oqey_music";
    $oqey_music_rel = $wpdb->prefix . "oqey_music_rel";

header('Content-Type: ' . feed_content_type('rss-http') . '; charset=' . get_option('blog_charset'), true);
$result .= '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>';
$result .= '<songs>'; 

          $mus = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $oqey_music AS f 
                                                        INNER JOIN $oqey_music_rel AS s 
					                        ON f.id = s.music_id
					                     WHERE s.gallery_id = %d
					                  ORDER BY s.mrel_order ASC", $id
                                                    )  
                                   ); 
   if(!empty($mus)){
       foreach($mus as $m){ 
           $result .= '<song path="'.urlencode(trim($m->link)).'" artist="" title="'.urlencode(trim($m->title)).'"></song>';
           }
           
       }else{
           $mus      = $wpdb->get_row( "SELECT * FROM $oqey_music WHERE status !=2 ORDER BY id ASC LIMIT 0,1" );
           $result  .= '<song path="'.urlencode(trim($mus->link)).'" artist="" title="'.urlencode(trim($mus->title)).'"></song>'."\n";
           }
      $result .= '</songs>';
      
   echo $result;
}
?>