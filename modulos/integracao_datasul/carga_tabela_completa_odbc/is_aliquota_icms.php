<?php

/*
 * is_aliquota_icms.php
 * Autor: Alex
 * 09/12/2010 17:11
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
$CnxODBC = ConectaODBCErpDatasul($ArrayConf,'unid-feder');
if(!$CnxODBC){
    echo 'Não foi possível estabelecer uma conexão com o ERP.';
    exit;
}
$ArDepara = array(
                'estado'            => 'uf',
                'pais'              => 'pais',
                'per-icms-int'      => 'pct_icms_interno',
                'per-icms-ext'      => 'pct_icms_externo'
                );
$ArChaves = array('estado','pais');

$SqlCustom = "SELECT \"estado\",\"per-icms-int\",\"per-icms-ext\",\"pais\" FROM pub.\"unid-feder\" WHERE \"pais\" = 'BRASIL'";

$Imp = new impODBCProgressTable();
$Imp->setCnxOdbc($CnxODBC);
$Imp->setTabelaOrigem('pub."unid-feder"');
$Imp->setTabelaDestino('is_aliquota_icms');
$Imp->setArDepara($ArDepara);
$Imp->setChaves($ArChaves);
$Imp->setSqlOdbcCustom($SqlCustom);

$Imp->Importa();
$Imp->mostraResultado();
odbc_close($CnxODBC);
?>