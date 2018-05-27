<?php
/*
 * post_before_param_politica_comercial_desc.php
 * Autor: Alex
 * 12/05/2011 11:41:37
 * Respons�vel por calcular a pontua��o de um registro no cadastro de par�metros de pol�tica comercial
 * Log de Altera��es
 * yyyy-mm-dd <Pessoa respons�vel> <Descri��o das altera��es>
 */
/* Se n�o for exclus�o e n�o for nenhum dos cadastros de par�metros */
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