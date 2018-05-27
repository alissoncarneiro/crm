<?php
/*
 * consulta_estoque_xml_erp_datasul.php
 * Autor: Alex
 * 04/04/2011 14:30:00
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */

header("Content-Type: text/html; charset=ISO-8859-1");
include('../../conecta.php');
include('../../functions.php');
include('../../classes/class.ConsultaEstoque.php');
include('../../classes/class.ConsultaEstoqueCustom.php');
include('../../classes/class.ConsultaPrevisao.php');
include('../../classes/class.Ub.php');
include('../venda/classes/class.Venda.Parametro.php');

$VendaParametro = new VendaParametro();

$IdProduto          = trim($_GET['id_produto']);
$IdEstabelecimento  = trim($_GET['id_estabelecimento']);

if($IdProduto == ''){
    exit;
}

$ConsultaEstoque = new ConsultaEstoqueCustom($VendaParametro);
$ConsultaEstoque->setIdProduto($IdProduto);
$ConsultaEstoque->setIdEstabelecimento($IdEstabelecimento);

$PrevisaoCompra = new ConsultaPrevisao($VendaParametro);
$PrevisaoCompra->setIdProduto($IdProduto);
$PrevisaoCompra->setIdEstabelecimento($IdEstabelecimento);

$ConsultaEstoque->getSaldoEstoqueTotal();

$ArrayEstoque               = $ConsultaEstoque->getSaldoEstoque();
$ArrayPedidosDatasul        = $ConsultaEstoque->getPedidosNaoFaturadosErp();
$ArrayPrevisaoCompra        = $PrevisaoCompra->getConsultaPrevisao();
$ArrayPedidosNaoIntegrados  = $ConsultaEstoque->getPedidosNaoIntegrados();

/* Montando o XML */
$DOM = new DOMDocument('1.0', 'ISO-8859-1');
$DOM->preserveWhiteSpace = false;
$DOM->formatOutput = true;
$Root = $DOM->createElement('root');

/* Quantidade em estoque disponivel */
$QuantidadeDisponivel = $ConsultaEstoque->qtidade_disp;
$QuantidadeDisponivel = $DOM->createElement('quantidade_disponivel',$QuantidadeDisponivel);
$Root->appendChild($QuantidadeDisponivel);

/*
 * Quantidade em estoque atual
 */
$QuantidadeAtual = $ConsultaEstoque->getSaldoEstoqueTotal();
$QuantidadeAtual = $DOM->createElement('quantidade_atual',$QuantidadeAtual);
$Root->appendChild($QuantidadeAtual);
/* Detalhamento da quantidade atual */
$QuantidadeAtualDetalhes = $DOM->createElement('quantitade_atual_detalhes');
if(is_array($ArrayEstoque)){
    foreach($ArrayEstoque as $Indice => $ArrayDados){
        $IdEstabelecimento  = $DOM->createElement('id_estabelecimento',$ArrayDados[0]);
        $Quantidade         = $DOM->createElement('quantidade',$ArrayDados[1]);
        $Lote               = $DOM->createElement('lote',$ArrayDados[2]);
        $DataValidade       = $DOM->createElement('data_validade',$ArrayDados[3]);
        $Referencia         = $DOM->createElement('referencia',$ArrayDados[4]);

        $DetalheEstoque = $DOM->createElement('detalhe_estoque');

        $DetalheEstoque->appendChild($IdEstabelecimento);
        $DetalheEstoque->appendChild($Quantidade);
        $DetalheEstoque->appendChild($Lote);
        $DetalheEstoque->appendChild($DataValidade);
        $DetalheEstoque->appendChild($Referencia);

        $QuantidadeAtualDetalhes->appendChild($DetalheEstoque);
    }
}
$Root->appendChild($QuantidadeAtualDetalhes);

/*
 * Quantidade não faturada
 */
$QuantidadeNaoFaturada = $ConsultaEstoque->getPedidosNaoFaturadosErpTotal();
$QuantidadeNaoFaturada = $DOM->createElement('quantidade_nao_faturada',$QuantidadeNaoFaturada);
$Root->appendChild($QuantidadeNaoFaturada);
/* Detalhamento da quantidade não faturada */
$QuantidadeNaoFaturadaDetalhes = $DOM->createElement('quantidade_nao_faturada_detalhes');
if(is_array($ArrayPedidosDatasul)){
    foreach($ArrayPedidosDatasul as $Indice => $ArrayDados){
        $NomeCliente    = $DOM->createElement('nome_cliente',TextoParaXML($ArrayDados[0],true));
        $NomeVendedor   = $DOM->createElement('nome_vendedor',TextoParaXML($ArrayDados[1],true));
        $Quantidade     = $DOM->createElement('quantidade',$ArrayDados[2]);
        $DataEntrega    = $DOM->createElement('data_entrega',$ArrayDados[3]);

        $DetalheEstoque = $DOM->createElement('detalhe_estoque');
        
        $DetalheEstoque->appendChild($NomeCliente);
        $DetalheEstoque->appendChild($NomeVendedor);
        $DetalheEstoque->appendChild($Quantidade);
        $DetalheEstoque->appendChild($DataEntrega);

        $QuantidadeNaoFaturadaDetalhes->appendChild($DetalheEstoque);
    }
}
$Root->appendChild($QuantidadeNaoFaturadaDetalhes);

/*
 * Quantidade pedidos não integrados
 */
$QuantidadeNaoIntegrada = $ConsultaEstoque->getPedidosNaoIntegradosTotal();
$QuantidadeNaoIntegrada = $DOM->createElement('quantidade_nao_integrada',$QuantidadeNaoIntegrada);
$Root->appendChild($QuantidadeNaoIntegrada);
/* Detalhamento da quantidade não integrada */
$QuantidadeNaoIntegradaDetalhes = $DOM->createElement('quantidade_nao_integrada_detalhes');
if(is_array($ArrayPedidosNaoIntegrados)){
    foreach($ArrayPedidosNaoIntegrados as $Indice => $ArrayDados){
        $NumeroPedido   = $DOM->createElement('numero_pedido',$ArrayDados[0]);
        $NomeCliente    = $DOM->createElement('nome_cliente',TextoParaXML($ArrayDados[1],true));
        $Quantidade     = $DOM->createElement('quantidade',$ArrayDados[2]);
        $DataEntrega    = $DOM->createElement('data_entrega',$ArrayDados[3]);
        $NomeVendedor   = $DOM->createElement('nome_vendedor',TextoParaXML($ArrayDados[4],true));

        $DetalheEstoque = $DOM->createElement('detalhe_estoque');

        $DetalheEstoque->appendChild($NumeroPedido);
        $DetalheEstoque->appendChild($NomeCliente);
        $DetalheEstoque->appendChild($Quantidade);
        $DetalheEstoque->appendChild($DataEntrega);
        $DetalheEstoque->appendChild($NomeVendedor);

        $QuantidadeNaoIntegradaDetalhes->appendChild($DetalheEstoque);
    }
}
$Root->appendChild($QuantidadeNaoIntegradaDetalhes);

/*
 * Previsão de compras
 */
/* Detalhamento da quantidade prevista */
$PrevisaoComprasDetalhes = $DOM->createElement('previsao_compras_detalhes');
if(is_array($ArrayPrevisaoCompra)){
    foreach($ArrayPrevisaoCompra as $Indice => $ArrayDados){
        $NumeroPedido   = $DOM->createElement('numero_pedido',$ArrayDados[0]);
        $NomeCliente    = $DOM->createElement('data_compra',$ArrayDados[1]);
        $Quantidade     = $DOM->createElement('nome_fornecedor',TextoParaXML($ArrayDados[2],true));
        $DataEntrega    = $DOM->createElement('data_prevista',$ArrayDados[3]);
        $NomeVendedor   = $DOM->createElement('quantidade',$ArrayDados[4]);

        $DetalheEstoque = $DOM->createElement('detalhe_estoque');

        $DetalheEstoque->appendChild($NumeroPedido);
        $DetalheEstoque->appendChild($NomeCliente);
        $DetalheEstoque->appendChild($Quantidade);
        $DetalheEstoque->appendChild($DataEntrega);
        $DetalheEstoque->appendChild($NomeVendedor);

        $PrevisaoComprasDetalhes->appendChild($DetalheEstoque);
    }
}
$Root->appendChild($PrevisaoComprasDetalhes);

/*
 * Imprimindo o XML
 */
$DOM->appendChild($Root);
header("Content-Type: text/xml; charset=ISO-8859-1");
print $DOM->saveXML();
?>