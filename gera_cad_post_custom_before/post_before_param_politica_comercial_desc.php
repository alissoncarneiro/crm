<?php
/*
 * post_before_param_politica_comercial_desc.php
 * Autor: Alex
 * 12/05/2011 11:41:37
 * Responsável por calcular a pontuação de um registro no cadastro de parâmetros de política comercial
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
/* Se não for exclusão e não for nenhum dos cadastros de parâmetros */
if($opc != 'excluir' && ($id_funcao == 'param_politica_comercial_desc_venda_media' || $id_funcao == 'param_politica_comercial_desc_venda_campo_desconto_fixo' || $id_funcao == 'param_politica_comercial_desc_venda_item_media' || $id_funcao == 'param_politica_comercial_desc_venda_item_campo_desconto' || $id_funcao == 'param_politica_comercial_desc_venda_item_campo_desconto_fixo')){
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