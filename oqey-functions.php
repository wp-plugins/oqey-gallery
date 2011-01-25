<?php 
global $wpdb;
   $oqey_galls = $wpdb->prefix . "oqey_gallery";
   
//get the gallery folder
function getFolder($id){
global $wpdb;
$oqey_galls = $wpdb->prefix . "oqey_gallery";	
$r = $wpdb->get_row("SELECT folder FROM $oqey_galls WHERE id ='".mysql_real_escape_string($id)."'");
return $r->folder;
}

function checkPost($post_id){
global $wpdb;
$check = $wpdb->get_var("SELECT post_type
						   FROM $wpdb->posts
					      WHERE ID = '$post_id'");
	return $check;
}


   
function uploadSize(){
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

function rm($fileglob)
{
    if (is_string($fileglob)) {
        if (is_file($fileglob)) {
            return unlink($fileglob);
        } else if (is_dir($fileglob)) {
            $ok = rm("$fileglob/*");
            if (! $ok) {
                return false;
            }
            return rmdir($fileglob);
        } else {
            $matching = glob($fileglob);
            if ($matching === false) {
                trigger_error(sprintf('No files match supplied glob %s', $fileglob), E_USER_WARNING);
                return false;
            }       
            $rcs = array_map('rm', $matching);
            if (in_array(false, $rcs)) {
                return false;
            }
        }       
    } else if (is_array($fileglob)) {
        $rcs = array_map('rm', $fileglob);
        if (in_array(false, $rcs)) {
            return false;
        }
    } else {
        trigger_error('Param #1 must be filename or glob pattern, or array of filenames or glob patterns', E_USER_ERROR);
        return false;
    }
    return true;
}



/*jSon*/
class custom_json {

    /**
     * Convert array to javascript object/array
     * @param array $array the array
     * @return string
     */
    public static function encode($array)
    {

        // determine type
        if(is_numeric(key($array))) {

            // indexed (list)
            $output = '[';
            for($i = 0, $last = (sizeof($array) - 1); isset($array[$i]); ++$i) {
                if(is_array($array[$i])) $output .= self::encode($array[$i]);
                else  $output .= self::_val($array[$i]);
                if($i !== $last) $output .= ',';
            }
            $output .= ']';

        } else {

            // associative (object)
            $output = '{';
            $last = sizeof($array) - 1;
            $i = 0;
            foreach($array as $key => $value) {
                $output .= '"'.$key.'":';
                if(is_array($value)) $output .= self::encode($value);
                else  $output .= self::_val($value);
                if($i !== $last) $output .= ',';
                ++$i;
            }
            $output .= '}';

        }

        // return
        return $output;

    }

    /**
     * [INTERNAL] Format value
     * @param mixed $val the value
     * @return string
     */
    private static function _val($val)
    {
        if(is_string($val)) return '"'.rawurlencode($val).'"';
        elseif(is_int($val)) return sprintf('%d', $val);
        elseif(is_float($val)) return sprintf('%F', $val);
        elseif(is_bool($val)) return ($val ? 'true' : 'false');
        else  return 'null';
    }

}


//scan for folders
//function scanSkins( $outerDir, $filters = array() ){ return array_diff( scandir( $outerDir ), array_merge( Array( ".", "..", ".php", ".ini" ), $filters ) ); } 

function scanSkins( $outerDir, $type, $filters = array()){
    $dirs = array_diff( scandir( $outerDir ), array_merge( Array( ".", ".." ), $filters ) );
    $dir_array = Array();
    foreach( $dirs as $d ){
	if($type=="1"){  if(is_dir($outerDir."/".$d)){ $dir_array[] = $d; } }else{ $dir_array[] = $d; }
	}		
    return $dir_array;
} 


function php4_scandir($dir,$listDirectories=false, $skipDots=true) {
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
?>