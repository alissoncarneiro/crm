<?php
/*
 * is_pessoa_cfop.php
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
$CnxODBC = ConectaODBCErpDatasul($ArrayConf,'es-cfop-cliente');
if(!$CnxODBC){
    echo 'Não foi possível estabelecer uma conexão com o ERP.';
    exit;
}

$ArDepara = array(
                'cod-emitente' 	=> 'id_pessoa',
                'cod-tp-cliente'=> 'id_tp_cliente'
                );
$ArChaves = array('cod-emitente');

$ArCamposObrigatorios = array('id_pessoa');

$Imp = new impODBCProgressTable();
$Imp->setCnxOdbc($CnxODBC);
$Imp->setTabelaOrigem('pub."es-cfop-cliente"');
$Imp->setTabelaDestino('is_pessoa_cfop');
$Imp->setArDepara($ArDepara);
$Imp->setChaves($ArChaves);
$Imp->setCamposObrigatorios($ArCamposObrigatorios);

$Imp->setCampoDepara('id_pessoa');
$Imp->setCampoDeparaTabelaCRM('is_pessoa');
$Imp->addCampoDeparaTabelaChaveCRM('id_pessoa_erp');

$Imp->Importa();
$Imp->mostraResultado();
odbc_close($CnxODBC);
?>