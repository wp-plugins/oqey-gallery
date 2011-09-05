<?php 
include('../../../wp-load.php');
global $wpdb;
$oqey_skins = $wpdb->prefix . "oqey_skins";

if(isset($_REQUEST['skin_id'])){
$id = str_replace("?", "", mysql_real_escape_string($_REQUEST['skin_id']) );
if($sql = $wpdb->query( $wpdb->prepare("UPDATE {$oqey_skins} SET firstrun = '0' WHERE skinid='$id'") )){ echo "Message=Success"; }
}

/*save skin key*/
if(isset($_REQUEST['set_skin_key']) && $_REQUEST['pass']=="oqey" && isset($_REQUEST['pskinid'])){
$sql = $wpdb->query( $wpdb->prepare("UPDATE {$oqey_skins} SET comkey = '".mysql_real_escape_string($_REQUEST['set_skin_key'])."' WHERE skinid='".mysql_real_escape_string($_REQUEST['pskinid'])."'") );
}
/*end*/

?>