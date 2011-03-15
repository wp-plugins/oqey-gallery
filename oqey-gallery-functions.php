<?php 
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'functions.php' == basename($_SERVER['SCRIPT_FILENAME']))
	die ('Please do not load this page directly. Thanks!');
	
global $wpdb;
$oqey_galls = $wpdb->prefix . "oqey_gallery";

function oqey_get_gallery_folder($id){
global $wpdb;
$id = (int)$id;
$oqey_galls = $wpdb->prefix . "oqey_gallery";	
$r = $wpdb->get_row("SELECT folder FROM $oqey_galls WHERE id ='".mysql_real_escape_string($id)."'");
return $r->folder;
}

function oqey_getBlogFolder($id){ $folder=""; if($id==0 || $id==1){ $folder = "";}else{ $folder=$id."/"; } return $folder; }
function oqey_checkPost($post_id){ global $wpdb; $check = $wpdb->get_var("SELECT post_type FROM $wpdb->posts WHERE ID = '$post_id'"); return $check; }

function oqey_uploadSize(){
	$upload_size_unit = $max_upload_size =  wp_max_upload_size();
	$sizes = array( 'KB', 'MB', 'GB' );
	for ( $u = -1; $upload_size_unit > 1024 && $u < count( $sizes ) - 1; $u++ )
		$upload_size_unit /= 1024;
	if ( $u < 0 ) {
		$upload_size_unit = 0;
		$u = 0;
	} else {
		$upload_size_unit = (int) $upload_size_unit;
	}   
	printf( __( 'Maximum upload file size: %d%s' ), $upload_size_unit, $sizes[$u] );
}

function oqey_rm($fileglob)
{
    if (is_string($fileglob)) {
        if (is_file($fileglob)) {
            return unlink($fileglob);
        } else if (is_dir($fileglob)) {
            $ok = oqey_rm("$fileglob/*");
            if (! $ok) {
                return false;
            }
            return rmdir($fileglob);
        } else {
            $matching = glob($fileglob);
            if ($matching === false) {
               // trigger_error(sprintf('No files match supplied glob %s', $fileglob), E_USER_WARNING);
                return false;
            }       
            $rcs = array_map('oqey_rm', $matching);
            if (in_array(false, $rcs)) {
                return false;
            }
        }       
    } else if (is_array($fileglob)) {
        $rcs = array_map('oqey_rm', $fileglob);
        if (in_array(false, $rcs)) {
            return false;
        }
    } else {
        trigger_error('Param #1 must be filename or glob pattern, or array of filenames or glob patterns', E_USER_ERROR);
        return false;
    }
    return true;
}

function oqey_scanSkins( $outerDir, $type, $filters = array()){
    $dirs = array_diff( scandir( $outerDir ), array_merge( Array( ".", ".." ), $filters ) );
    $dir_array = Array();
    foreach( $dirs as $d ){
	if($type=="1"){  if(is_dir($outerDir."/".$d)){ $dir_array[] = $d; } }else{ $dir_array[] = $d; }
	}		
    return $dir_array;
} 

function oqey_php4_scandir($dir,$listDirectories=false, $skipDots=true) {
    $dirArray = array();
    if ($handle = opendir($dir)) {
        while (false !== ($file = readdir($handle))) {
            if (($file != "." && $file != "..") || $skipDots == true) {
                if($listDirectories == false) { if(is_dir($file)) { continue; } }
                array_push($dirArray,basename($file));
            }
        }
        closedir($handle);
    }
    return $dirArray;
}

function oqey_img_resize($tmpname, $save_dir, $save_name, $newheight, $newwidth ){	
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
        $coef = $width / $height;
        if ($coef > 1){
	    $newheight = $newwidth / $coef;
		if($newheight > $dheight){		
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
	
add_action('save_post', 'oqey_gallery_in_post');
function oqey_gallery_in_post($post_id) {
global $wpdb;
$array_id[$d] = $post_id;
$arr = $array_id;
$oqey_galls = $wpdb->prefix . "oqey_gallery";
$oqey_images = $wpdb->prefix . "oqey_images";
$control = oqey_checkPost($post_id);
if($control == "page" || $control == "post"){
$content = $wpdb->get_var("SELECT post_content FROM $wpdb->posts WHERE ID = '$post_id' AND post_type != 'revision'");
$wpdb->query( $wpdb->prepare( "UPDATE {$oqey_galls} SET post_id = %s WHERE post_id = %s", 0, $post_id ) );
$gal = preg_match_all('/\[oqeygallery id=([^]]+)]/i', $content, $gals);
foreach($gals[1] as $id){ 
$g = sprintf("UPDATE %s SET post_id=%d WHERE id = %d", $oqey_galls, $post_id, $id);
$gup = mysql_query($g) or die (mysql_error());
}}}
?>