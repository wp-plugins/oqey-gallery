<?php 
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'functions.php' == basename($_SERVER['SCRIPT_FILENAME'])) die ('Please do not load this page directly. Thanks!');
	
global $wpdb;
$oqey_galls = $wpdb->prefix . "oqey_gallery";

function oqey_get_gallery_folder($id){
global $wpdb;
$id = (int)$id;
$oqey_galls = $wpdb->prefix . "oqey_gallery";	
$r = $wpdb->get_row("SELECT folder FROM $oqey_galls WHERE id ='".mysql_real_escape_string($id)."'");
return $r->folder;
}

function oqey_get_nextgen_title($id){
global $wpdb;
$id = (int)$id;
$nggal = $wpdb->prefix . 'ngg_gallery';	
$r = $wpdb->get_row("SELECT title FROM $nggal WHERE gid ='".mysql_real_escape_string($id)."'");
return $r->title;
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


function oqey_get_all_images($content){
  global $post;

  preg_match_all('/\[oqeygallery id=([0-9+])\s/', $content, $m);
  
  if(!empty($m[0])){
  $content = preg_replace('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', '', $content);
  }
  return $content;
}


//add_filter('the_content', 'oqey_get_all_images');



/**
 * Scale down an image to fit a particular size and save a new copy of the image.
 *
 * The PNG transparency will be preserved using the function, as well as the
 * image type. If the file going in is PNG, then the resized image is going to
 * be PNG. The only supported image types are PNG, GIF, and JPEG.
 *
 * Some functionality requires API to exist, so some PHP version may lose out
 * support. This is not the fault of WordPress (where functionality is
 * downgraded, not actual defects), but of your PHP version.
 *
 * @since 2.5.0
 *
 * @param string $file Image file path.
 * @param int $max_w Maximum width to resize to.
 * @param int $max_h Maximum height to resize to.
 * @param bool $crop Optional. Whether to crop image or resize.
 * @param string $suffix Optional. File Suffix.
 * @param string $dest_path Optional. New image file path.
 * @param int $jpeg_quality Optional, default is 90. Image quality percentage.
 * @return mixed WP_Error on failure. String with new destination path.
 */
function oqey_image_resize( $file, $max_w, $max_h, $crop = false, $suffix = null, $dest_path = null, $jpeg_quality = 90 ) {

	$image = wp_load_image( $file );
	if ( !is_resource( $image ) )
		return new WP_Error( 'error_loading_image', $image, $file );

	$size = @getimagesize( $file );
	if ( !$size )
		return new WP_Error('invalid_image', __('Could not read image size'), $file);
	list($orig_w, $orig_h, $orig_type) = $size;

	$dims = image_resize_dimensions($orig_w, $orig_h, $max_w, $max_h, $crop);
	if ( !$dims )
		return new WP_Error( 'error_getting_dimensions', __('Could not calculate resized image dimensions') );
	list($dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h) = $dims;

	$newimage = wp_imagecreatetruecolor( $dst_w, $dst_h );

	imagecopyresampled( $newimage, $image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);

	// convert from full colors to index colors, like original PNG.
	if ( IMAGETYPE_PNG == $orig_type && function_exists('imageistruecolor') && !imageistruecolor( $image ) )
		imagetruecolortopalette( $newimage, false, imagecolorstotal( $image ) );

	// we don't need the original in memory anymore
	imagedestroy( $image );

	// $suffix will be appended to the destination filename, just before the extension
	if ( !$suffix )
		$suffix = "";

	$info = pathinfo($file);
	$dir = $info['dirname'];
	$ext = $info['extension'];
	$name = wp_basename($file, ".$ext");

	if ( !is_null($dest_path) and $_dest_path = realpath($dest_path) )
		$dir = $_dest_path;
	$destfilename = "{$dir}/{$name}.{$ext}";

	if ( IMAGETYPE_GIF == $orig_type ) {
		if ( !imagegif( $newimage, $destfilename ) )
			return new WP_Error('resize_path_invalid', __( 'Resize path invalid' ));
	} elseif ( IMAGETYPE_PNG == $orig_type ) {
		if ( !imagepng( $newimage, $destfilename ) )
			return new WP_Error('resize_path_invalid', __( 'Resize path invalid' ));
	} else {
		// all other formats are converted to jpg
		$destfilename = "{$dir}/{$name}.jpg";
		if ( !imagejpeg( $newimage, $destfilename, apply_filters( 'jpeg_quality', $jpeg_quality, 'image_resize' ) ) )
			return new WP_Error('resize_path_invalid', __( 'Resize path invalid' ));
	}

	imagedestroy( $newimage );

	// Set correct file permissions
	$stat = stat( dirname( $destfilename ));
	$perms = $stat['mode'] & 0000666; //same permissions as parent folder, strip off the executable bits
	@ chmod( $destfilename, $perms );

	return $destfilename;
}
?>