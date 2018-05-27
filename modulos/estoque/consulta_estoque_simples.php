<?php
/*
 * consulta_estoque_simples.php
 * Autor: Alex
 * 16/02/2012 13:06:18
 */
header("Content-Type: text/html; charset=ISO-8859-1");
session_start();
$PrefixoIncludes = '../venda/';
require('../venda/includes.php');

$Produto = new Produto($_POST['pid_produto']);
$IdProduto = $Produto->getNumregProduto();
$VendaParametro = new VendaParametro();

if($VendaParametro->getSnConsultaEstoque()){
    if($VendaParametro->getSnUsaURLEstoqXmlDatasul() && $VendaParametro->getURLEstoqueXmlErpDatasul() != ''){
        $ConsultaEstoqueErpDatasul      = new ConsultaEstoqueXMLErpDatasul($VendaParametro,$IdProduto,$IdEstabelecimento);
        $QuantidadeDisponivel           = $ConsultaEstoqueErpDatasul->getQuantidadeDisponivel();
        $SaldoEstoqueTotal              = $ConsultaEstoqueErpDatasul->getQuantidadeAtual();
        $PedidosNaoFaturadosErpTotal    = $ConsultaEstoqueErpDatasul->getQuantidadeNaoFaturada();
        $PedidosNaoIntegradosTotal      = $ConsultaEstoqueErpDatasul->getQuantidadeNaoIntegrada();
        $ArReferencia                   = $ConsultaEstoqueErpDatasul->getArReferencia();
    }
    else{
        //ESTOQUE
        $SaldoEstoque = new ConsultaEstoqueCustom($VendaParametro);
        $SaldoEstoque->setIdProduto($IdProduto);
        $SaldoEstoque->setIdEstabelecimento($IdEstabelecimento);
        $QuantidadeDisponivel           = $SaldoEstoqueTotal - $PedidosNaoFaturadosErpTotal - $PedidosNaoIntegradosTotal;
        $SaldoEstoqueTotal              = $SaldoEstoque->getSaldoEstoqueTotal();
        $PedidosNaoFaturadosErpTotal    = $SaldoEstoque->getPedidosNaoFaturadosErpTotal();
        $PedidosNaoIntegradosTotal      = $SaldoEstoque->getPedidosNaoIntegradosTotal();

        $ArReferencia                   = $SaldoEstoque->getArReferencia();
    }
    $QtdeEmEstoque = $SaldoEstoqueTotal - $PedidosNaoFaturadosErpTotal - $PedidosNaoIntegradosTotal;
}
else{
    $QtdeEmEstoque = 0;
}
echo number_format_min($QtdeEmEstoque,0,',','.');
exit;
?>