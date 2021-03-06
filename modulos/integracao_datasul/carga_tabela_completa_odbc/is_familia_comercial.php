<?php

/*
 * is_familia_comercial.php
 * Autor: Alex
 * 28/11/2010 09:04
 * -
 *
 * Log de Altera��es
 * yyyy-mm-dd <Pessoa respons�vel> <Descri��o das altera��es>
 */
session_start();
set_time_limit(600); /* 10 minutos */
include_once('../../../conecta.php');
include_once('../../../functions.php');
include_once('../../../classes/class.impODBCProgressTable.php');

$ArrayConf = parse_ini_file('../../../conecta_odbc_erp_datasul.ini',true);
$CnxODBC = ConectaODBCErpDatasul($ArrayConf,'fam-comerc');
if(!$CnxODBC){
    echo 'N�o foi poss�vel estabelecer uma conex�o com o ERP.';
    exit;
}

$ArDepara = array(
                'fm-cod-com' 	=> 'id_familia_erp',
                'descricao'     => 'nome_familia_comercial'
                );
$ArFixos = array('sn_ativo' => 1);
$ArChaves = array('fm-cod-com');

$Imp = new impODBCProgressTable();
$Imp->setCnxOdbc($CnxODBC);
$Imp->setTabelaOrigem('pub."fam-comerc"');
$Imp->setTabelaDestino('is_familia_comercial');
$Imp->setArDepara($ArDepara);
$Imp->setArFixos($ArFixos);
$Imp->setChaves($ArChaves);

$Imp->Importa();
$Imp->mostraResultado();
odbc_close($CnxODBC);
?>