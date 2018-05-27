<?php

/*
 * p4_venda_frete_post.php
 * Autor: Alex
 * 04/07/2011 12:00:00
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
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
$VlTotalFrete = TrataFloatPost($_POST['vl_total_frete']);
$Venda->setVlTotalFrete($VlTotalFrete);
if($Venda->AtualizaTotaisVendaBD()){
    echo 'Valor do Frete foi atualizado!';
}
?>