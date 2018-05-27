<?php

if($id_funcao == 'pessoa'){
    if(!$Usuario instanceof Usuario){
        CarregaClasse('Usuario', 'classes/class.Usuario.php');
        $Usuario = new Usuario($_SESSION['id_usuario']);
    }
    if($qry_gera_cad_botoes['id_botao'] == 'btn_suspect_para_prospect'){
        if($qry_cadastro['sn_suspect'] != 1){
            $input_botao_custom = '';
        }
        elseif($_GET['ptsep'] == '1'){
            $input_botao_custom = '';
        }
        elseif(!$Usuario->getPermissao('sn_trans_suspect_prospect')){
            $input_botao_custom = '';
        }
    }

    if($qry_gera_cad_botoes['id_botao'] == 'btn_prospect_para_cliente'){
        if($qry_cadastro['sn_prospect'] != 1){
            $input_botao_custom = '';
        }
        elseif($_GET['ptpec'] == 1){
            $input_botao_custom = '';
        }
        elseif(!$Usuario->getPermissao('sn_trans_prospect_cliente')){
            $input_botao_custom = '';
        }
    }

    if($qry_gera_cad_botoes['id_botao'] == 'btn_consumidorfinal_para_prospect'){
        if($qry_cadastro['sn_consumidor_final'] != 1){
            $input_botao_custom = '';
        }
        elseif($_GET['ptpec'] == 1){
            $input_botao_custom = '';
        }
        elseif(!$Usuario->getPermissao('sn_trans_consfinal_cliente')){
            $input_botao_custom = '';
        }
    }
}
?>