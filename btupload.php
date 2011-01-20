<?php
include("../../../wp-load.php");
global $wpdb;
$oqey_skins = $wpdb->prefix . "oqey_skins";
$filespath = $_GET['loc'] . "/";

if($_FILES['Filedata']['size']>0){
        	if($_FILES["Filedata"]["size"]>0){
    		$path = pathinfo($_FILES["Filedata"]["name"]);			
    		$ext = array('zip', 'ZIP'); 
		    if(in_array( strtolower($path['extension']), $ext)){	
            $name = trim(sanitize_title($path['filename']).".".$path['extension']); //denumirea arhivei
        
  	        while(1){
			ini_set('memory_limit', '-1');
	        global $wpdb;
            $zip = new ZipArchive;
	        $zip_file = $_FILES["Filedata"]["tmp_name"];
            $zip->open($zip_file);
	        $zip_extract = ABSPATH."wp-content/plugins/oqey-gallery/skins/";
            $zip->extractTo($zip_extract);
            $zip->close();
    		break;
  			}
    	}
	}
}
echo 'File uploaded';
?>