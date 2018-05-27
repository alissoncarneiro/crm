<?php

/*
 * is_moeda.php
 * Autor: Alex
 * 14/01/2011 16:38:00
 * -
 *
 * Log de Altera��es
 * yyyy-mm-dd <Pessoa respons�vel> <Descri��o das altera��es>
 */
session_start();
include_once('../../../conecta.php');
include_once('../../../functions.php');
include_once('../../../classes/class.impODBCProgressTable.php');

$ArrayConf = parse_ini_file('../../../conecta_odbc_erp_datasul.ini',true);
$CnxODBC = ConectaODBCErpDatasul($ArrayConf,'moeda');
if(!$CnxODBC){
    echo 'N�o foi poss�vel estabelecer uma conex�o com o ERP.';
    exit;
}

$ArDepara = array(
                    'mo-codigo'     => 'id_moeda_erp',
                    'descricao'     => 'nome_moeda',
                    'sigla'         => 'sigla'
                );

$ArFixos = array();

$ArChaves = array('mo-codigo');

$Imp = new impODBCProgressTable();
$Imp->setCnxOdbc($CnxODBC);
$Imp->setTabelaOrigem('pub."moeda"');
$Imp->setTabelaDestino('is_moeda');
$Imp->setArDepara($ArDepara);
$Imp->setChaves($ArChaves);
$Imp->setArFixos($ArFixos);

$Imp->Importa();
$Imp->mostraResultado();
odbc_close($CnxODBC);
?>