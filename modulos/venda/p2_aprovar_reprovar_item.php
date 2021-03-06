<?php

/*
 * p2_aprovar_reprovar_item.php
 * Autor: Alex
 * 19/11/2010 20:23:00
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

$_POST = uB::UrlDecodePost($_POST);

$Venda->AprovaReprovaItem($_POST['NumregItem'],$_POST['status_aprovacao_item'],$_POST['justificativa_aprovacao_item']);

$XML = '<'.'?xml version="1.0" encoding="ISO-8859-1"?'.'>
    <resposta>
        <status>{!STATUS!}</status>
            <mensagem>{!MENSAGEM!}</mensagem>
    </resposta>
';

$XML = str_replace('{!STATUS!}',1,$XML);
$Mensagem = TextoParaXML($Venda->getMensagem());
$XML = str_replace('{!MENSAGEM!}',$Mensagem,$XML);
header("Content-Type: text/xml");
echo $XML;
/*
$DOM = new DOMDocument("1.0", "ISO-8859-1");
$DOM->preserveWhiteSpace = false;
$DOM->formatOutput = true;
$Root = $DOM->createElement("resposta");
$Status = $DOM->createElement("status",1);
$Mensagem = $DOM->createElement("mensagem",TextoParaXML($Venda->getMensagem()));
$Root->appendChild($Status);
$Root->appendChild($Mensagem);
$DOM->appendChild($Root);
header("Content-Type: text/xml");
print $DOM->saveXML();
*/
?>