<?php
if(isset($_GET['loc'])){

include("../../../wp-load.php");
global $wpdb;
$oqey_skins = $wpdb->prefix . "oqey_skins";
$filespath = $_GET['loc'] . "/";

if($_FILES['Filedata']['size']>0){
        if($_FILES["Filedata"]["size"]>0){
    		$path = pathinfo($_FILES["Filedata"]["name"]);			
    		$ext = array('zip', 'ZIP'); 
		if(in_array( strtolower($path['extension']), $ext)){	
        
  	    while(1){
			
		if ( class_exists('ZipArchive') ){
			$zip = new ZipArchive;
	        $zip_file = $_FILES["Filedata"]["tmp_name"];
            $zip->open($zip_file);
	        $zip_extract = OQEY_ABSPATH."wp-content/oqey_gallery/skins/";
            $zip->extractTo($zip_extract);
            $zip->close();
		}else{
			require_once(OQEY_ABSPATH . 'wp-admin/includes/class-pclzip.php');				
			$zip_file = $_FILES["Filedata"]["tmp_name"];
			$zip_extract = OQEY_ABSPATH."wp-content/oqey_gallery/skins/";
			$archive = new PclZip($zip_file);
            $list = $archive->extract($zip_extract);
            if ($list == 0) {
            die("ERROR : '".$archive->errorInfo(true)."'");
            }
		}
    		break;
  			}
    	}
	}
}
echo 'File uploaded';

}else{
	die ('Please do not load this page directly. Thanks!');
}
?>