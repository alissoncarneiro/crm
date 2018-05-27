<?php
/*
 * log_calculo_item.php
 * Autor: Alex
 * 25/01/2011 13:58:00
 *
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
header("Content-Type: text/html; charset=ISO-8859-1");
session_start();
require('includes.php');

$NumregItem = $_POST['NumregItem'];
if($NumregItem == ''){
    echo 'Produto não informado';
    exit;
}

/*
 * Verifica se a váriável de tipo da venda foi preenchida.
 */

if($_POST['ptp_venda'] == 1 || $_POST['ptp_venda'] == 2){
    if($_POST['ptp_venda'] == 1){
        $Venda = new Orcamento($_POST['ptp_venda'],$_POST['pnumreg']);
    } elseif($_POST['ptp_venda'] == 2){
        $Venda = new Pedido($_POST['ptp_venda'],$_POST['pnumreg']);
    }
}
else{
    echo getError('0040010001',getParametrosGerais('RetornoErro'));
    exit;
}
$Item = $Venda->getItem($NumregItem);
$Item->CalculaCFOP();
$Item->CalculaTotais();
echo $Item->getMensagemLog();
?>