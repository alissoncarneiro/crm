<?php
/*
 * post_before_situacao_oportunidade.php
 * Autor: Alex
 * 20/04/2011 10:30:00
 *
 * Log de Altera��es
 * yyyy-mm-dd <Pessoa respons�vel> <Descri��o das altera��es>
 */
if($id_funcao == 'situacao_oportunidade' && $opc == 'excluir') {
    if($pnumreg <= 4){
        echo 'N�o � permitido excluir esta op��o.';
        exit;
    }
}
?>