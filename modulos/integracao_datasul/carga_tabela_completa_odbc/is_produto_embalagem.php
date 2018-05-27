<?php
/*
 * is_produto_embalagem.php
 * Autor: Alex
 * 11/01/2011 11:00
 * -
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
session_start();
set_time_limit(600); /* 10 minutos */
include_once('../../../conecta.php');
include_once('../../../functions.php');
include_once('../../../classes/class.impODBCProgressTable.php');

$ArrayConf = parse_ini_file('../../../conecta_odbc_erp_datasul.ini',true);
$CnxODBC = ConectaODBCErpDatasul($ArrayConf,'item-caixa');
if(!$CnxODBC){
    echo 'Não foi possível estabelecer uma conexão com o ERP.';
    exit;
}

$ArDepara = array(
                'it-codigo'     => 'id_produto',
                'fm-codigo'     => 'id_familia',
                'fm-cod-com'    => 'id_familia_comercial',
                'sigla-emb'     => 'id_embalagem',
                'qt-item'       => 'qtde'
                );
$ArChaves = array('it-codigo','fm-codigo','fm-cod-com','sigla-emb');

$ArCamposObrigatorios = array('id_produto','id_embalagem');

$Imp = new impODBCProgressTable();
$Imp->setCnxOdbc($CnxODBC);
$Imp->setTabelaOrigem('pub."item-caixa"');
$Imp->setTabelaDestino('is_produto_embalagem');
$Imp->setArDepara($ArDepara);
$Imp->setChaves($ArChaves);
$Imp->setArCamposObrigatorios($ArCamposObrigatorios);

$Imp->setCampoDepara('id_produto');
$Imp->setCampoDeparaTabelaCRM('is_produto');
$Imp->addCampoDeparaTabelaChaveCRM('id_produto_erp');

$Imp->setCampoDepara('id_familia');
$Imp->setCampoDeparaTabelaCRM('is_familia');
$Imp->addCampoDeparaTabelaChaveCRM('id_familia_erp');

$Imp->setCampoDepara('id_familia_comercial');
$Imp->setCampoDeparaTabelaCRM('is_familia_comercial');
$Imp->addCampoDeparaTabelaChaveCRM('id_familia_erp');

$Imp->setCampoDepara('id_embalagem');
$Imp->setCampoDeparaTabelaCRM('is_embalagem');
$Imp->addCampoDeparaTabelaChaveCRM('sigla');

$Imp->Importa();
$Imp->mostraResultado();
odbc_close($CnxODBC);
?>