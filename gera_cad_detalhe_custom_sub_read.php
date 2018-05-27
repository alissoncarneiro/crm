<?php
/*
 * gera_cad_detalhe_custom_sub_read.php
 * Versão 4.0
 * 07/10/2010 16:26:00
 */
// mudar variavel $url_pread = "&pread=0";
//$exibe_mestre_detalhe = '0'
$dir_gera_cad = 'gera_cad_detalhe_custom_sub_read';
if(is_dir($dir_gera_cad)){
    if($dh = opendir($dir_gera_cad)){
        while(($file = readdir($dh)) !== false){
            if($file != "." && $file != ".." && is_file($dir_gera_cad."/".$file)){
                include($dir_gera_cad."/".$file);
            }
        }
        closedir($dh);
    }
}
