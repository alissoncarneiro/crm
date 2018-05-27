<?php

/*
 * is_unid_medida.php
 * Autor: Alex
 * 02/12/2010 11:27
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
$CnxODBC = ConectaODBCErpDatasul($ArrayConf,'tab-unidade');
if(!$CnxODBC){
    echo 'N�o foi poss�vel estabelecer uma conex�o com o ERP.';
    exit;
}

$ArDepara = array(
                'un' 	=> 'id_unid_medida_erp',
                'descricao'     => 'nome_unid_medida'
                );
$ArChaves = array('un');

$Imp = new impODBCProgressTable();
$Imp->setCnxOdbc($CnxODBC);
$Imp->setTabelaOrigem('pub."tab-unidade"');
$Imp->setTabelaDestino('is_unid_medida');
$Imp->setArDepara($ArDepara);
$Imp->setChaves($ArChaves);

$Imp->Importa();
$Imp->mostraResultado();
odbc_close($CnxODBC);
?>