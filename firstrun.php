<?php 
include('../../../wp-config.php');
global $wpdb;
$oqey_skins = $wpdb->prefix . "oqey_skins";

//firstrun
if(isset($_REQUEST['skin_id'])){
$g = sprintf("UPDATE $oqey_skins SET firstrun = '%d' WHERE skinid = '%s' ", "0", mysql_real_escape_string($_REQUEST['skin_id']) );						
$k= mysql_query($g) or die (mysql_error());

if($k){ echo "Message=Success"; }
}

?>