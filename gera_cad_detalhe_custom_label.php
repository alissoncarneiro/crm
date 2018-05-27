<?php
//==================================================================================================
// Este programa permite definir o titulo de um campo 
// Voc� pode utilizar por exemplo o conte�do das seguinte vari�veis :
// - $pnumreg para saber se � uma inclus�o = -1 ou altera��o <> -1
// - $_SESSION["id_usuario"] e $_SESSION["id_perfil"] para customizar direitos de acesso por campo
// - $id_funcao para saber qual � o cadastro corrente
// - $qry_gera_cad_campos["id_campo"] para saber qual � o id do campo corrente
// - $qry_cadastro[$qry_gera_cad_campos["id_campo"]] para saber qual � o conteudo do campo corrente
//  Altere o conte�do da vari�vel $lbl_nome_campo;
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