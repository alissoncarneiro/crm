<?php
session_start();
include('../../classes/class.GeraLinhaTxt.php');
include('class.VendaExpTxt.php');
include('class.VendaExpTxtCustom.php');

if($_SESSION['id_usuario'] == ''){
    echo 'Usuário não logado!';
    exit;
}

$PrefixoIncludes = '../venda/';
include('../venda/includes.php');

$VendaParametro = new VendaParametro();

$TXT = NULL;

$NomeArquivo = 'pedido_conteudo.lst';

$VendaExpTxt = new VendaExpTxtCustom();
if($_GET['dt_base_exportacao'] != ''){
    $DataBaseExportacao = dtbr2en($_GET['dt_base_exportacao']);
    $VendaExpTxt->setDataBaseExportacao($DataBaseExportacao);
}

if($VendaParametro->getModoExportacaoPedidoTXT() == 1){ // Grava o Arquivo no em um diretório parametrizado
    $DiretorioArquivo = $VendaParametro->getDirArquivoPedidoCRM();
    $CaminhoArquivo = $DiretorioArquivo.$NomeArquivo;
    if(!is_dir($DiretorioArquivo)){
        echo 'Diretório parametrizado para geração do arquivo inválido';
        exit;
    }
    if(file_exists($CaminhoArquivo)){
        echo 'Já existe o arquivo <em>'.$CaminhoArquivo.'</em>. Nenhum pedido foi exportado.';
        exit;
    }
    $VendaExpTxt->CarregaPedidosBD();
    $TXT = $VendaExpTxt->getTxt();


    if(file_exists($CaminhoArquivo)){
        $MaxId = uB::getProximoMaxId(2);
        rename($CaminhoArquivo,$DiretorioArquivo.$MaxId.'_'.date("YmdHis").'_'.$NomeArquivo);
    }
    if($TXT != ''){
        $Arquivo = fopen($CaminhoArquivo,"w+");
        fwrite($Arquivo,$TXT);
        fclose($Arquivo);
        echo 'Arquivo gerado em <em>'.$CaminhoArquivo.'</em><hr><input type="button" onclick="javascript:window.close();" value="Fechar" />';
    }
    else{
        echo 'Nenhum Pedido foi exportado.';
        exit;
    }
}
elseif($VendaParametro->getModoExportacaoPedidoTXT() == 2){ // Força o Download do Arquivo

    $VendaExpTxt->CarregaPedidosBD();
    $TXT = $VendaExpTxt->getTxt();

    if($TXT == ''){
        echo 'Nenhum Pedido foi exportado.';
        exit;
    }

    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Content-Type: application/force-download");
    header( "Content-Disposition: attachment; filename=".$NomeArquivo);
    header( "Content-Description: File Transfer");
    echo $TXT;
}
else{
    echo 'Parâmetro de exportação de arquivo de pedido TXT não preenchido!';
    exit;
}

$ArSqlInsert = array();
$ArSqlInsert['dthr_exportacao'] = date("Y-m-d H:i:s");
$ArSqlInsert['id_usuario'] = $_SESSION['id_usuario'];
$ArSqlInsert['texto'] = $TXT;

$SqlInsert = AutoExecuteSql(getParametrosGerais('TipoBancoDados'),'is_pedido_txt_datasul',$ArSqlInsert,'INSERT');
iquery($SqlInsert);
?>