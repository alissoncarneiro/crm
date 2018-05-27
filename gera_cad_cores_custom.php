<?php

//==================================================================================================
// Este programa permite mudar a cor de fundo da linha no browse 
// Você pode utilizar por exemplo o conteúdo das seguinte variáveis :
// - $lista_qry_cadastro["seu_campo"] para saber o conteudo de um campo no registro corrente
// - $_SESSION["id_usuario"] e $_SESSION["id_perfil"] para customizar direitos de acesso por campo
// - $id_funcao para saber qual é o cadastro corrente
// - $lista_qry_gera_cad["nome_tabela"] para saber qual a tabela 
//==================================================================================================
if ($lista_qry_gera_cad["nome_tabela"]=='is_atividade') {
   $lista_color = 'bgcolor="#EBEBEB"';
   $lista_tdstyle= 'style="color: #000000;"';
   if (($lista_qry_cadastro["id_situacao"] != '4')) {
       if ((substr($lista_qry_cadastro["dt_prev_fim"],0,10) < date("Y-m-d"))) {
           $lista_color = 'bgcolor="#FF0000"';
       }
       if ((substr($lista_qry_cadastro["dt_prev_fim"],0,10) == date("Y-m-d"))) {
           $lista_color = 'bgcolor="#FFFF00"';
       }
       if ((substr($lista_qry_cadastro["dt_prev_fim"],0,10) > date("Y-m-d"))) {
           $lista_color = 'bgcolor="#00FF00"';
       }
       if (($lista_qry_cadastro["id_situacao"] == '5')) { 
       $lista_color = 'bgcolor="#C0C0C0"';
       }
   } 
   else{
       $lista_color = 'bgcolor="#C0C0C0"';
   }
}
if ($lista_qry_gera_cad["nome_tabela"]=='is_oportunidade') {
   $lista_color = 'bgcolor="#EBEBEB"';
   $lista_tdstyle= 'style="color: #000000;"';
   if (empty($lista_qry_cadastro["dt_real_fim"])) {
       if ((substr($lista_qry_cadastro["dt_prev_fim"],0,10) < date("Y-m-d"))) {
           $lista_color = 'bgcolor="#FF0000"';
       }
       if ((substr($lista_qry_cadastro["dt_prev_fim"],0,10) == date("Y-m-d"))) {
           $lista_color = 'bgcolor="#FFFF00"';
       }
       if ((substr($lista_qry_cadastro["dt_prev_fim"],0,10) > date("Y-m-d"))) {
           $lista_color = 'bgcolor="#00FF00"';
       }
   } else {
       $lista_color = 'bgcolor="#C0C0C0"';
   }
}
if ($lista_qry_gera_cad["nome_tabela"]=='is_contrato') {
   $lista_color = 'bgcolor="#EBEBEB"';
   $lista_tdstyle= 'style="color: #000000;"';
   if (($lista_qry_cadastro["sn_ativo"] != '0')) {
       if ((substr($lista_qry_cadastro["dt_fim"],0,10) < date("Y-m-d"))) {
           $lista_color = 'bgcolor="#FF0000"';
       }
       if ((substr($lista_qry_cadastro["dt_fim"],0,7) == date("Y-m"))) {
           $lista_color = 'bgcolor="#FFFF00"';
       }
       if ((substr($lista_qry_cadastro["dt_fim"],0,7) > date("Y-m"))) {
           $lista_color = 'bgcolor="#00FF00"';
       }
   } else {
       $lista_color = 'bgcolor="#C0C0C0"';
   }
}

if ($lista_qry_gera_cad["nome_tabela"]=='is_pessoa_endereco') {
   if ($lista_qry_cadastro["id_endereco_erp"]) {
       $lista_color = 'bgcolor="#00FF00"';
   }
}
$dir_gera_cad = 'gera_cad_cores_custom';
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