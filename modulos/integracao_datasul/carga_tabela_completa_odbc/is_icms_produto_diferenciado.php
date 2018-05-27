<?php
/*
 * is_produto_cfop.php
 * Autor: Anderson
 * 04/01/2011 16:16
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
$CnxODBC = ConectaODBCErpDatasul($ArrayConf,'icms-it-uf');
if(!$CnxODBC){
    echo 'Não foi possível estabelecer uma conexão com o ERP.';
    exit;
}

$ArDepara = array(
                'it-codigo'     => 'id_produto',
                'estado'        => 'uf',
                'aliquota-icm'  => 'pct_icms',
                'log-descons-para-nao-contribt' => 'sn_descons_nao_contrib'
                );
$ArChaves = array('it-codigo','estado');

$ArFixos = array('pais' => 'BRASIL');

$ArCamposObrigatorios = array('id_produto');

$Imp = new impODBCProgressTable();
$Imp->setCnxOdbc($CnxODBC);
$Imp->setTabelaOrigem('pub."icms-it-uf"');
$Imp->setTabelaDestino('is_icms_produto_diferenciado');
$Imp->setArDepara($ArDepara);
$Imp->setChaves($ArChaves);
$Imp->setArFixos($ArFixos);
$Imp->setArCamposObrigatorios($ArCamposObrigatorios);

$Imp->setCampoDepara('id_produto');
$Imp->setCampoDeparaTabelaCRM('is_produto');
$Imp->addCampoDeparaTabelaChaveCRM('id_produto_erp');

$Imp->Importa();
$Imp->mostraResultado();
odbc_close($CnxODBC);
?>