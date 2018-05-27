<?php
/* Alterar variavel $input_botao_custom  */
include_once("classes/class.Usuario.php");
if(!is_object($Usuario)){
    $Usuario = new Usuario($_SESSION['id_usuario']);
}

$dir_gera_cad = 'gera_cad_detalhe_custom_botao';
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
?>
