<?php
/*
 * post_before_param_politica_comercial_comis.php
 * Autor: Alex
 * 19/05/2011 16:22:40
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
if($opc != 'excluir' && $id_funcao == 'param_politica_comercial_comis'){
    $ArrayCampos = array();
    foreach($_POST as $k => $v){
        if(substr($k, 0, 3) == 'edt'){
            $ArrayCampos[substr($k,3,strlen($k)-3)] = $_POST[$k];
        }
    }
    $GeraPontuacaoParametro = new GeraPontuacaoParametro($id_funcao);
    $_POST['edtnr_pontos'] = $GeraPontuacaoParametro->CalculaPontuacao($ArrayCampos);
}
?>