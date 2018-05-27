<?php
//==================================================================================================
// Este programa permite definir o titulo de um campo 
// Você pode utilizar por exemplo o conteúdo das seguinte variáveis :
// - $pnumreg para saber se é uma inclusão = -1 ou alteração <> -1
// - $_SESSION["id_usuario"] e $_SESSION["id_perfil"] para customizar direitos de acesso por campo
// - $id_funcao para saber qual é o cadastro corrente
// - $qry_gera_cad_campos["id_campo"] para saber qual é o id do campo corrente
// - $qry_cadastro[$qry_gera_cad_campos["id_campo"]] para saber qual é o conteudo do campo corrente
//  Altere o conteúdo da variável $lbl_nome_campo;
//==================================================================================================
$id_campo = $qry_gera_cad_campos["id_campo"];
$dir_gera_cad = 'gera_cad_detalhe_custom_label';
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