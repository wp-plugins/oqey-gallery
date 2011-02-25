<?php
header ("Content-type: image/jpeg");
require_once("../../../wp-load.php");
$path = OQEY_ABSPATH.'wp-content/oqey_gallery/galleries/'.$_GET['folder'];
$img  = $path . '/iphone/' . $_GET['img'];
if (file_exists($img)) {
	$procent = $_GET['procent'];
	$constrain = $_GET['constrain'];
	$width = $_GET['width'];
	$hight = $_GET['hight'];
}
$x = @getimagesize($img);
$oqeyw = $x[0];
$oqeyh = $x[1];
if($width<$oqeyw){
if ($procent > 0) {
	$procent = $procent * 0.01;
	$width = $oqeyw * $procent;
	$hight = $oqeyh * $procent;
} else {
	if (isset ($width) AND !isset ($hight)) {
		$hight = (100 / ($oqeyw / $width)) * .01;
		$hight = @round ($oqeyh * $hight);
	} elseif (isset ($hight) AND !isset ($width)) {
		$width = (100 / ($oqeyh / $hight)) * .01;
		$width = @round ($oqeyw * $width);
	} elseif (isset ($hight) AND isset ($width) AND isset ($constrain)) {
		$hightx = (100 / ($oqeyw / $width)) * .01;
		$hightx = @round ($oqeyh * $hightx);
		$widthx = (100 / ($oqeyh / $hight)) * .01;
		$widthx = @round ($oqeyw * $widthx);
		if ($hightx < $hight) {
			$hight = (100 / ($oqeyw / $width)) * .01;
			$hight = @round ($oqeyh * $hight);
		} else {
			$width = (100 / ($oqeyh / $hight)) * .01;
			$width = @round ($oqeyw * $width);
		}	} }
}else{ $hight = $oqeyh; $width=$oqeyw; }
$im = @ImageCreateFromJPEG ($img) or 
$im = @ImageCreateFromPNG ($img) or 
$im = false;
if (!$im) {
	readfile ($img);
} else {
	$thumb = @ImageCreateTrueColor ($width, $hight);
	@ImageCopyResampled ($thumb, $im, 0, 0, 0, 0, $width, $hight, $oqeyw, $oqeyh);
	@ImageJPEG ($thumb);
}
?>