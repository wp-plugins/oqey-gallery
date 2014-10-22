<?php
function oqey_get_wp_load_path(){
    $base = dirname(__FILE__);
    $path = false;

    if(@file_exists(dirname(dirname(dirname($base)))."/wp-load.php")){
        $path = dirname(dirname(dirname($base)))."/wp-load.php";
    }elseif(@file_exists(dirname(dirname($base))."/wp-load.php")){
        $path = dirname(dirname($base))."/wp-load.php";
    }elseif(@file_exists(dirname(dirname(dirname(dirname($base))))."/wp-load.php")){
        $path = dirname(dirname(dirname(dirname($base))))."/wp-load.php";
    }elseif(@file_exists(dirname(dirname(dirname(dirname(dirname($base)))))."/wp-load.php")){
        $path = dirname(dirname(dirname(dirname(dirname($base)))))."/wp-load.php";
    }else{
        $path = false;
    }
    if ($path != false){
        $path = str_replace("\\", "/", $path);
    }
    return $path;
}

require_once(oqey_get_wp_load_path());
?>
