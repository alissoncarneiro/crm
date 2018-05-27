<?php
include('../../conecta.php');
include('../../functions.php');
include('../../classes/class.GeraLinhaTxt.php');
include('../../classes/class.uB.php');
include('../../classes/class.Usuario.php');
require('../venda/classes/class.Venda.Parametro.php');
include('../integracao_datasul/class.OrdemFaturamentoExpTxt.php');

$VendaParametro = new VendaParametro();

$TXT = NULL;

$NomeArquivo = 'ordemfaturamento_conteudo.lst';

if($VendaParametro->getModoExportacaoPedidoTXT() == 1){ // Grava o Arquivo no em um diret�rio parametrizado
    $DiretorioArquivo = $VendaParametro->getDirArquivoPedidoCRM();

    if(!is_dir($DiretorioArquivo)){
        echo 'Diret�rio parametrizado para gera��o do arquivo inv�lido';
        exit;
    }
    exit;

    $VendaExpTxt = new OrdemFaturamentoExpTxt();
    $VendaExpTxt->CarregaOrdemFaturamentoBD();
    $TXT = $VendaExpTxt->getTxt();

    $CaminhoArquivo = $DiretoriArquivo.$NomeArquivo;

    if(file_exists($CaminhoArquivo)){
        $MaxId = uB::getProximoMaxId(2);
        rename($CaminhoArquivo,$DiretorioArquivo.$MaxId.'_'.date("YmdHis").'_'.$NomeArquivo);
    }

    $Arquivo = fopen($CaminhoArquivo,"w+");
    fwrite($Arquivo,$TXT);
    fclose($Arquivo);
}
elseif($VendaParametro->getModoExportacaoPedidoTXT() == 2){ // For�a o Download do Arquivo

    $VendaExpTxt = new OrdemFaturamentoExpTxt();
    $VendaExpTxt->CarregaOrdemFaturamentoBD();
    $TXT = $VendaExpTxt->getTxt();

    if($TXT == ''){
        echo 'Nenhuma Ordem de Faturamento foi exportada.';
        exit;
    }

    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Content-Type: application/force-download");
    header("Content-Disposition: attachment; filename=".$NomeArquivo);
    header("Content-Description: File Transfer");
    echo $TXT;
}
else{
    echo 'Par�metro de exporta��o de arquivo de Ordem de Faturamento TXT n�o preenchido!';
    exit;
}

$ArSqlInsert = array();
$ArSqlInsert['dthr_exportacao'] = date("Y-m-d H:i:s");
$ArSqlInsert['id_usuario'] = $_SESSION['id_usuario'];
$ArSqlInsert['texto'] = $TXT;

$SqlInsert = AutoExecuteSql(getParametrosGerais('TipoBancoDados'),'is_pedido_txt_datasul',$ArSqlInsert,'INSERT');
iquery($SqlInsert);
?>