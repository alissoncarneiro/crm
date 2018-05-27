<?php

/*
 * p2_adiciona_item.php
 * Autor: Alex
 * 04/11/2010 17:24:00
 * -
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
header("content-type: text/xml");
session_start();
require('includes.php');

$_POST = uB::UrlDecodePost($_POST);

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
if($_POST['sn_produto_nao_comercial'] == 1){
    $Status = $Venda->AdicionaItemBD(NULL,$_POST,false);
}
else{
    $Status = $Venda->AdicionaItemBD($_POST['id_produto'],$_POST);
}
if($Status !== false){
    echo '<root>'."\n";
    echo "\t".'<status>1</status>'."\n";
    echo "\t".'<mensagem>';
    echo 'Item adicionado com sucesso!';
    echo '</mensagem>'."\n";
    echo "\t".'<pid_produto_pai>'.$_POST['id_produto_pai'].'</pid_produto_pai>'."\n";
    echo '</root>';
}
else{
    echo '<root>'."\n";
    echo "\t".'<status>2</status>'."\n";
    echo "\t".'<mensagem>';
    echo $Venda->getMensagem();
    echo '</mensagem>'."\n";
    echo '</root>';
}
?>