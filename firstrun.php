<?php 
require_once(dirname(dirname(dirname(dirname(__FILE__))))."/wp-load.php");
global $wpdb;
$oqey_skins = $wpdb->prefix . "oqey_skins";

if(isset($_POST['skin_id'])){
    
    $id     = str_replace("?", "", mysql_real_escape_string($_POST['skin_id']));
    if($sql = $wpdb->query( $wpdb->prepare("UPDATE $oqey_skins SET firstrun = '0' WHERE skinid = %s ", $id) )){ echo "Message=Success"; }
}

/*save skin key*/
if(isset($_REQUEST['set_skin_key']) && $_REQUEST['pass']=="oqey" && isset($_REQUEST['pskinid'])){
    
$sql = $wpdb->query( $wpdb->prepare("UPDATE $oqey_skins SET comkey = %s WHERE skinid= %s ", 
                                     trim($_REQUEST['set_skin_key']),
                                     trim($_REQUEST['pskinid'])
                                     ) );

}
/*end*/

?>