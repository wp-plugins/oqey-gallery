<?php
include("../../../wp-load.php");
global $wpdb;
$oqey_images = $wpdb->prefix . "oqey_images";
$oqey_galls = $wpdb->prefix . "oqey_gallery";

if(isset($_GET['loc'])){
$f = explode("/", $_GET['loc']);
$folder = end($f);
$filespath = $_GET['loc'] . "/galimg/";
$filespaththumb= $_GET['loc'] . "/galthmb/";
$iphonepath= $_GET['loc'] . "/iphone/";
$gal = $wpdb->get_row("SELECT * FROM $oqey_galls WHERE folder ='".$folder."'");
$gal_id = $gal->id;
}

if(isset($_GET['folder']) && isset($_GET['id']) ){
$filespath = str_replace('\\', '/', ABSPATH).'wp-content/oqey_gallery/galleries/'.$_GET['folder'].'/galimg/';
$filespaththumb = str_replace('\\', '/', ABSPATH).'wp-content/oqey_gallery/galleries/'.$_GET['folder'].'/galthmb/';	
$iphonepath = str_replace('\\', '/', ABSPATH).'wp-content/oqey_gallery/galleries/'.$_GET['folder'].'/iphone/';
$gal_id = $_GET['id'];
}

if($_FILES['Filedata']['size']>0){
    	if($_FILES["Filedata"]["size"]>0){
    		$path = pathinfo($_FILES["Filedata"]["name"]);
			
			$ext = array('jpeg', 'jpg', 'png'); 
		    if (in_array(strtolower($path['extension']), $ext)){			
			
			 if(!isset($path['filename'])){
                $name = trim(sanitize_title(substr($path['basename'], 0,strpos($path['basename'],'.')))).".".$path['extension'];
             }else{
			    $name = trim(sanitize_title($path['filename']).".".$path['extension']); //denumirea imaginii
			 }

  			while(1){				
				if(!file_exists($filespath.$name)){	
				$name = trim(sanitize_title($path['filename']).".".$path['extension']); //denumirea imaginii
				$thumbname = trim(sanitize_title($path['filename']));
				$image_alt = $path['filename'];
				}else{		
				$name = trim(sanitize_title($path['filename'])."_".time().".".$path['extension']); //denumirea imaginii se schimba daca deja exista
				$thumbname = trim(sanitize_title($path['filename'])."_".time());
				$image_alt = $path['filename'];							
				}		
				$name = strtolower($name);
				$file = $filespath.$name; 
				
				
  					@move_uploaded_file($_FILES["Filedata"]["tmp_name"],$filespath.$name);
					//$newheight = get_option('oqey_thumb_height');
		            //$newwidth = get_option('oqey_thumb_width');
					//img_resize( $file, $filespaththumb, $name, $newheight, $newwidth );	
					
					img_resize( $file, $filespaththumb, $name, "100", "150" );		
					img_resize( $file, $iphonepath, $name, "640", "960" );

					$wpdb->query("INSERT INTO $oqey_images (title, gal_id, alt) VALUES ('$name ', '$gal_id', '$image_alt')");
  					break;  		
  			}
    	}
	}
}
echo 'File uploaded';

function img_resize($tmpname, $save_dir, $save_name, $newheight, $newwidth ){
	
	ini_set('memory_limit', '-1');
    $save_dir     .= ( substr($save_dir,-1) != "/") ? "/" : "";
    $gis        = getimagesize($tmpname);
    $type        = $gis[2];
    switch($type){
        case "1": $imorig = imagecreatefromgif($tmpname); break;
        case "2": $imorig = imagecreatefromjpeg($tmpname);break;
        case "3": $imorig = imagecreatefrompng($tmpname); break;
        default:  $imorig = imagecreatefromjpeg($tmpname);
        }

        $x = imagesx($imorig);
        $y = imagesy($imorig);
       
        $width = $x;
        $height = $y;
		
		
		if($width>$newwidth){
		
		$dheight = $newheight;
	    $dwidth = $newwidth;
        //CREATES IMAGE WITH NEW SIZES
        $coef = $width / $height;
        if ($coef > 1){
	    $newheight = $newwidth / $coef;
		if($newheight > $dheight){		
		//$newwidth = $dheight * 1.5;
		$newcoef = $newheight/$dheight;
		$newwidth = $dwidth/$newcoef;
		$newheight = $dheight;
		}
		
		if($coef >1.5){
		$newheight = $dheight;
		$newwidth = $dheight*$coef; 
		}
        } else {
	    $newwidth = $newheight * $coef;
        };	

		}else{
		$newwidth=$width; 
		$newheight=$height;
		}
		
        $im = imagecreatetruecolor($newwidth,$newheight);
        $i = imagecopyresampled($im,$imorig , 0,0,0,0,$newwidth,$newheight,$x,$y);
        $c = imagejpeg($im, $save_dir.$save_name, 90);
		imagedestroy($im);

    }
?>