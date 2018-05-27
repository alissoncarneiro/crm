<?php
$dir_gera_cad = 'gera_cad_detalhe_custom_end';
if(is_dir($dir_gera_cad)){
    if($dh = opendir($dir_gera_cad)){
        while(($file = readdir($dh)) !== false){
            if($file != "." && $file != ".." && is_file($dir_gera_cad."/".$file)){
                include_once($dir_gera_cad."/".$file);
            }
        }
        closedir($dh);
    }
}
?>