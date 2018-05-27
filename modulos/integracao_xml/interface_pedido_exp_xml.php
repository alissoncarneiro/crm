<?php
include('../../conecta.php');
include('../../functions.php');
include('../../funcoes.php');
include('../../classes/class.uB.php');
include('../../classes/class.Usuario.php');
include('../../classes/class.GeraLinhaTxt.php');
include('class.VendaExpXml.php');

$PrefixoIncludes = '../venda/';
include('../venda/includes.php');

$VendaParametro = new VendaParametro();

$TXT = NULL;

$DiretorioArquivo = GetParam('diretorio_xml_raiz').'crm/pedidos/pendentes_integracao/';
if(!is_dir($DiretorioArquivo)){
    echo 'Diretуrio parametrizado para geraзгo do arquivo invбlido';
    exit;
}
$VendaExpXml = new VendaExpXml();
$VendaExpXml->CaminhoArquivo = $DiretorioArquivo;
$VendaExpXml->CarregaPedidosBD();
?>