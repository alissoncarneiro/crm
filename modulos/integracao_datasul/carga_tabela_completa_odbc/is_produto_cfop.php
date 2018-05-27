<?php
/*
 * is_produto_cfop.php
 * Autor: Anderson
 * 29/11/2010 16:16
 * -
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
session_start();
include_once('../../../conecta.php');
include_once('../../../functions.php');
include_once('../../../classes/class.impODBCProgressTable.php');

$ArrayConf = parse_ini_file('../../../conecta_odbc_erp_datasul.ini',true);
$CnxODBC = ConectaODBCErpDatasul($ArrayConf,'es-cfop-item');
if(!$CnxODBC){
    echo 'Não foi possível estabelecer uma conexão com o ERP.';
    exit;
}

$ArDepara = array(
                'it-codigo'   => 'id_produto',
                'cod-tp-item'=> 'id_tp_produto'
                );
$ArChaves = array('it-codigo');

$ArCamposObrigatorios = array('id_produto','id_tp_produto');

$Imp = new impODBCProgressTable();
$Imp->setCnxOdbc($CnxODBC);
$Imp->setTabelaOrigem('pub."es-cfop-item"');
$Imp->setTabelaDestino('is_produto_cfop');
$Imp->setArDepara($ArDepara);
$Imp->setChaves($ArChaves);
$Imp->setArCamposObrigatorios($ArCamposObrigatorios);

$Imp->setCampoDepara('id_produto');
$Imp->setCampoDeparaTabelaCRM('is_produto');
$Imp->addCampoDeparaTabelaChaveCRM('id_produto_erp');

$Imp->Importa();
$Imp->mostraResultado();
odbc_close($CnxODBC);
?>