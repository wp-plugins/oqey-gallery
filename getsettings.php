<?php
include('../../../wp-load.php');
if(get_option("oqey_license")==""){ die(); }
if(get_option('oqey_crop_images')=="on"){ $crop = "true"; }else{ $crop = "false"; }
if(get_option('oqey_HideThumbs')=="on"){ $HideThumbs = "true"; }else{ $HideThumbs = "false"; }
if(get_option('oqey_LoopOption')=="on"){ $LoopOption = "true"; }else{ $LoopOption = "false"; }
$maxw = get_option('oqey_width');
$maxh = get_option('oqey_height');
if(get_option('oqey_limitmax')=="on"){
if(get_option('oqey_max_width')!=""){ $maxw = get_option('oqey_max_width'); }
if(get_option('oqey_max_height')!=""){ $maxh = get_option('oqey_max_height'); }
$maximum = 'MaximumWidth='.$maxw.'&MaximumHeight='.$maxh.'&';
}else{ $maximum = ''; }
echo $maximum.'GalleryWidth='.get_option('oqey_width').'&GalleryHeight='.get_option('oqey_height').'&CropOption='.$crop.'&ThumbWidth='.get_option('oqey_thumb_width').'&ThumbHeight='.get_option('oqey_thumb_height').'&TransitionTime='.get_option('oqey_effects_trans_time').'&TransitionInterval='.get_option('oqey_pause_between_tran').'&HideThumbs='.$HideThumbs.'&LoopOption='.$LoopOption;
?>