<?php

/*
 * is_estados_uf.php
 * Autor: Alex
 * 06/12/2010 17:11
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
                'no-estado'         => 'nome_uf',
                'per-icms-int'      => 'per_icms_int',
                'per-icms-ext'      => 'per_icms_ext',
                'per-sub-tri'       => 'per_sub_tri',
                'pc-icms-st'        => 'pct_icms_st',
                'pais'              => 'pais',
                'ind-uf-subs'       => 'sn_possui_st'
                );
$ArChaves = array('estado');

$SqlCustom = "SELECT \"estado\",\"no-estado\",\"per-icms-int\",\"per-icms-ext\",\"per-sub-tri\",\"pc-icms-st\",\"pais\",\"ind-uf-subs\" FROM pub.\"unid-feder\" WHERE \"pais\" = 'BRASIL'";

$Imp = new impODBCProgressTable();
$Imp->setCnxOdbc($CnxODBC);
$Imp->setTabelaOrigem('pub."unid-feder"');
$Imp->setTabelaDestino('is_estados_uf');
$Imp->setArDepara($ArDepara);
$Imp->setChaves($ArChaves);
$Imp->setSqlOdbcCustom($SqlCustom);

$Imp->Importa();
$Imp->mostraResultado();
odbc_close($CnxODBC);
?>