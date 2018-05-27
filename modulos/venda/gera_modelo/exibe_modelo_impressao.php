<?php
header("Content-Type: text/html; charset=ISO-8859-1");
session_start();
$PrefixoIncludes = '../';
include('../includes.php');

if(empty($_GET['pnumreg'])){
    echo getError('0040010001',getParametrosGerais('RetornoErro'));
    exit;
}
else{
    if($_GET['ptp_venda'] == 1){
        $Venda = new Orcamento($_REQUEST['ptp_venda'],$_GET['pnumreg'],true,false);
    }
    else{
        $Venda = new Pedido($_REQUEST['ptp_venda'],$_GET['pnumreg'],true,false);
    }
}

$SnImpresso = $Venda->getDadosVenda('sn_impresso');
$Venda->setDadoVenda('sn_impresso',1);
if($Venda->isOrcamento()){
    $Venda->setDadoVenda('id_situacao_venda',2);
}
$Venda->AtualizaDadosVendaBD();
/* Se é um orçamento e o e-mail ainda nao foi enviado */
if($Venda->isOrcamento() && $SnImpresso != 1){
    $Venda->FinalizaAtividadeEnvioOrcamento();
    $Venda->CriaAtividadeFollowupOrcamento();
}

$sql_modelo = "select * from is_modelo_orcamento where numreg='".$_GET['id_modelo']."'";
$qry_modelo = query($sql_modelo);
$ar_modelo = farray($qry_modelo);
include($ar_modelo['caminho_arquivo_principal']);
if($ar_modelo['tp_arquivo'] == 'html'){
    echo '<script language="JavaScript"> window.print(); </script>';
}
?>