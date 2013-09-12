<?php
require_once(dirname(dirname(dirname(dirname(__FILE__))))."/wp-load.php");
global $wpdb;
$result = "";

if(isset($_REQUEST['galleryid'])){
    
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

   foreach($mus as $m){ 
   
      $result .= '<song path="'.urlencode(trim($m->link)).'" artist="" title="'.urlencode(trim($m->title)).'"></song>';
   
   }
   
      $result .= '</songs>';
      
   echo $result;
}
?>