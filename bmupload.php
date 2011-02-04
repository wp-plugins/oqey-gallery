<?php
if(isset($_GET['loc'])){

include("../../../wp-load.php");
global $wpdb;
$oqey_music = $wpdb->prefix . "oqey_music";
$filespath = $_GET['loc'] . "/";

if($_FILES['Filedata']['size']>0){
    	if($_FILES["Filedata"]["size"]>0){
    		$path = pathinfo($_FILES["Filedata"]["name"]);
			
    		$ext = array('mp3', 'MP3'); 
		    if (in_array( strtolower($path['extension']), $ext) ){	
		
			
			if(!isset($path['filename'])){
                $name = trim(sanitize_title(substr($path['basename'], 0,strpos($path['basename'],'.')))).".".$path['extension'];
				$music_title = substr($path['basename'], 0,strpos($path['basename'],'.'));
             }else{
			    $name = trim(sanitize_title($path['filename']).".".$path['extension']); //denumirea imaginii
				$music_title = $path['filename'];
			 }
			
        
  			while(1){
				if(!file_exists($filespath.$name)){	
				$name = trim(sanitize_title($path['filename']).".".$path['extension']); //denumirea melodiei
				$music_title = $path['filename'];
				}else{		
				$name = trim(sanitize_title($path['filename'])."_".time().".".$path['extension']); //denumirea melodiei se schimba daca deja exista
				$music_title = $path['filename'];							
				}
					ini_set('memory_limit', '-1');
  					@move_uploaded_file($_FILES["Filedata"]["tmp_name"],$filespath.$name);
					$wpdb->query("INSERT INTO $oqey_music (link, title) VALUES ('$name', '$music_title')");		
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