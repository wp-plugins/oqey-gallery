<?php
require_once("../../../wp-load.php");
$path = OQEY_ABSPATH.'wp-content/oqey_gallery/galleries/'.oqey_getBlogFolder($wpdb->blogid).$_REQUEST['folder'];
$img  = $path . '/iphone/' . $_REQUEST['img'];

if (file_exists($img)) {
	$procent = $_REQUEST['procent'];
	$constrain = $_REQUEST['constrain'];
	$width = $_REQUEST['width'];
	$height = $_REQUEST['height'];
	$new_height = $_REQUEST['new_height'];
}
$x = getimagesize($img);
$oqeyw = $x[0];
$oqeyh = $x[1];
if($width<$oqeyw){
if ($procent > 0) {
	$procent = $procent * 0.01;
	$width = $oqeyw * $procent;
	$height = $oqeyh * $procent;
} else {
	if (isset ($width) AND !isset ($height)) {
		$height = (100 / ($oqeyw / $width)) * .01;
		$height = round ($oqeyh * $height);
	} elseif (isset ($height) AND !isset ($width)) {
		$width = (100 / ($oqeyh / $height)) * .01;
		$width = round ($oqeyw * $width);
	} elseif (isset ($height) AND isset ($width) AND isset ($constrain)) {
		$heightx = (100 / ($oqeyw / $width)) * .01;
		$heightx = round ($oqeyh * $heightx);
		$widthx = (100 / ($oqeyh / $height)) * .01;
		$widthx = round ($oqeyw * $widthx);
		if ($heightx < $height) {
			$height = (100 / ($oqeyw / $width)) * .01;
			$height = round ($oqeyh * $height);
		} else {
			$width = (100 / ($oqeyh / $height)) * .01;
			$width = round ($oqeyw * $width);
		}	} }
}else{ $height = $oqeyh; $width=$oqeyw; }


if($new_height<$height){ 
$c = $height/$new_height;
$width = $width/$c;
$height = $new_height;
}

$im = ImageCreateFromJPEG ($img) or 
$im = ImageCreateFromPNG ($img) or 
$im = false;
/*
$fisier = str_replace(OQEY_ABSPATH, get_option("siteurl")."/", $img);
header('Location: '.$fisier);
exit();
*/


if (!$im) {
    header ("Content-type: image/jpeg");
	readfile ($img);
} else {
    header ("Content-type: image/jpeg");
	$thumb = @ImageCreateTrueColor ($width, $height);
	@ImageCopyResampled ($thumb, $im, 0, 0, 0, 0, $width, $height, $oqeyw, $oqeyh);
	@ImageJPEG ($thumb);
}
?>