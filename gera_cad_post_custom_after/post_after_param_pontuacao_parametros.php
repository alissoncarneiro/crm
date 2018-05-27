<?php
/*
 * post_after_param_pontuacao_parametros.php
 * Autor: Alex
 * 10/06/2011 17:12:50
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
if($opc != 'excluir' && $id_funcao == 'param_pontuacao_parametros'){
    if($_POST['edtid_cadastro'] != ''){
        $GeraPontuacaoParametro = new GeraPontuacaoParametro($_POST['edtid_cadastro']);
        $_POST['edtnr_pontos'] = $GeraPontuacaoParametro->CalculaPontuacaoGeralBD();
    }
}
?>