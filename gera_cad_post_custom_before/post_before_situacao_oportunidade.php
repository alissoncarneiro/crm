<?php
/*
 * post_before_situacao_oportunidade.php
 * Autor: Alex
 * 20/04/2011 10:30:00
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
if($id_funcao == 'situacao_oportunidade' && $opc == 'excluir') {
    if($pnumreg <= 4){
        echo 'Não é permitido excluir esta opção.';
        exit;
    }
}
?>