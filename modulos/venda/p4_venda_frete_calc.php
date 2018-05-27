<?php
/*
 * p4_venda_frete_calc.php
 * Autor: Alex
 * 05/07/2011 08:52:09
 */
session_start();
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Content-Type: text/html; charset=ISO-8859-1");

require('includes.php');

$Usuario = ($_SESSION['id_usuario'] != '')?new Usuario($_SESSION['id_usuario']):null;
/*
 * Verifica se a váriável de tipo da venda foi preenchida.
 */
if($_POST['ptp_venda'] != 1 && $_POST['ptp_venda'] != 2){
    echo getError('0040010001', getParametrosGerais('RetornoErro'));
    exit;
}
elseif(empty($_POST['pnumreg'])){
    echo getError('0040010001', getParametrosGerais('RetornoErro'));
    exit;
}
else{
    if($_POST['ptp_venda'] == 1){
        $Venda = new Orcamento($_POST['ptp_venda'], $_POST['pnumreg']);
    }
    else{
        $Venda = new Pedido($_POST['ptp_venda'], $_POST['pnumreg']);
    }
    /* Tratando os campos */
    $Venda->pfuncao = $_POST['pfuncao'];
}
$DOM = new DOMDocument('1.0', 'ISO-8859-1');
$DOM->preserveWhiteSpace = false;
$DOM->formatOutput = true;
$Root = $DOM->createElement('resposta');

if(!$Venda->CalculaFrete()){
    $Acao = $DOM->createElement('acao','1');
    $Status = $DOM->createElement('status',0);
    $Mensagem = $DOM->createElement('mensagem',TextoParaXML($Venda->getMensagem()));
}
else{
    $Acao = $DOM->createElement('acao','2');
    $Status = $DOM->createElement('status',1);
    $Mensagem = $DOM->createElement('mensagem','');
    $VlFrete = $DOM->createElement('vl_total_frete',number_format_min($Venda->getVlTotalVendaFrete(),2,',','.'));
    $Root->appendChild($VlFrete);
}
$Root->appendChild($Status);
$Root->appendChild($Acao);
$Root->appendChild($Mensagem);
$DOM->appendChild($Root);
header('Content-Type: text/xml');
print $DOM->saveXML();
exit;
?>