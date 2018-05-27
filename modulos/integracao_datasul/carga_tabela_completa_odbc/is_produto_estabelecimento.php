<?php
/*
 * is_produto_estabelecimento.php
 * Autor: Anderson
 * 29/11/2010 16:16
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
$CnxODBC = ConectaODBCErpDatasul($ArrayConf,'item-uni-estab');
if(!$CnxODBC){
    echo 'Não foi possível estabelecer uma conexão com o ERP.';
    exit;
}

$ArDepara = array(
                'it-codigo'     => 'id_produto',
                'cod-estabel'   => 'id_estabelecimento',
                'lote-mulven'   => 'qtde_multipla_venda',
                'ind-item-fat'  => 'sn_faturavel'
                );
$ArChaves = array('it-codigo','cod-estabel');

$ArCamposObrigatorios = array('id_produto','id_estabelecimento');

$Imp = new impODBCProgressTable();
$Imp->setCnxOdbc($CnxODBC);
$Imp->setTabelaOrigem('pub."item-uni-estab"');
$Imp->setTabelaDestino('is_produto_estabelecimento');
$Imp->setArDepara($ArDepara);
$Imp->setChaves($ArChaves);
$Imp->setArCamposObrigatorios($ArCamposObrigatorios);

$Imp->setCampoDepara('id_produto');
$Imp->setCampoDeparaTabelaCRM('is_produto');
$Imp->addCampoDeparaTabelaChaveCRM('id_produto_erp');

$Imp->setCampoDepara('id_estabelecimento');
$Imp->setCampoDeparaTabelaCRM('is_estabelecimento');
$Imp->addCampoDeparaTabelaChaveCRM('id_estabelecimento_erp');

$Imp->Importa();
$Imp->mostraResultado();
odbc_close($CnxODBC);
?>