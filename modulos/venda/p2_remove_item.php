<?php

/*
 * p2_remove_item.php
 * Autor: Alex
 * 09/11/2010 17:21:00
 * -
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
header("content-type: text/xml");
session_start();
require('includes.php');

/*
 * Verifica se a váriável de tipo da venda foi preenchida.
 */
if(empty($_POST['ptp_venda']) || empty($_POST['pnumreg'])){
    echo getError('0040030001',getParametrosGerais('RetornoErro'));
    exit;
}
if($_POST['ptp_venda'] == 1 || $_POST['ptp_venda'] == 2){
    if($_POST['ptp_venda'] == 1){
        $Venda = new Orcamento($_POST['ptp_venda'],$_POST['pnumreg']);
    }
    elseif($_POST['ptp_venda'] == 2){
        $Venda = new Pedido($_POST['ptp_venda'],$_POST['pnumreg']);
    }
}
echo '<?'.'xml version="1.0" encoding="ISO-8859-1"'.'?>'."\n";
if($Venda->RemoveItem($_POST['NumregItem'])){
    $Venda->CalculaTotaisVenda();
    $Venda->AtualizaTotaisVendaBD();
    echo '<root>'."\n";
    echo "\t".'<status>true</status>'."\n";
    echo "\t".'<mensagem>';
    echo 'Item removido com sucesso!';
    echo '</mensagem>'."\n";
    echo '</root>';
}
else{
    echo '<root>'."\n";
    echo "\t".'<status>true</status>'."\n";
    echo "\t".'<mensagem>';
    echo $Venda->getMensagemRemoveItemErro();
    echo '</mensagem>'."\n";
    echo '</root>';
}
?>