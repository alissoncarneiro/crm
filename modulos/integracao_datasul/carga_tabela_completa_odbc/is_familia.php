<?php

/*
 * is_familia.php
 * Autor: Alex
 * 28/11/2010 09:04
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
$CnxODBC = ConectaODBCErpDatasul($ArrayConf,'familia');
if(!$CnxODBC){
    echo 'Não foi possível estabelecer uma conexão com o ERP.';
    exit;
}

$ArDepara = array(
                'fm-codigo' 	=> 'id_familia_erp',
                'descricao'     => 'nome_familia'
                );
$ArChaves = array('fm-codigo');

$ArFixos = array('sn_ativo' => 1);

$Imp = new impODBCProgressTable();
$Imp->setCnxOdbc($CnxODBC);
$Imp->setTabelaOrigem('pub."familia"');
$Imp->setTabelaDestino('is_familia');
$Imp->setArDepara($ArDepara);
$Imp->setArFixos($ArFixos);
$Imp->setChaves($ArChaves);

$Imp->Importa();
$Imp->mostraResultado();
odbc_close($CnxODBC);
?>