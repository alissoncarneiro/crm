<?php

/*
 * post_before_param_cfop.php
 * Autor: Alex
 * 02/09/2011 13:19:08
 */
if($opc != 'excluir' && $id_funcao == 'param_cfop'){
    $ArrayCampos = array();
    foreach($_POST as $k => $v){
        if(substr($k, 0, 3) == 'edt'){
            $ArrayCampos[substr($k,3,strlen($k)-3)] = $_POST[$k];
        }
    }
    $GeraPontuacaoParametro = new GeraPontuacaoParametro($id_funcao);
    $_POST['edtpontos'] = $GeraPontuacaoParametro->CalculaPontuacao($ArrayCampos);
}
?>