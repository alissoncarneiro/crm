<?php

/*
 * is_id_produto_compl.php
 * Autor: Bruno
 * 24/01/2011 17:08
 * -
 *
 * Log de Altera��es
 * yyyy-mm-dd <Pessoa respons�vel> <Descri��o das altera��es>
 */
session_start();
include('../../../conecta.php');
include('../../../functions.php');
include('../../../classes/class.impODBCProgressTable.php');

$ArrayConf = parse_ini_file('../../../conecta_odbc_erp_datasul.ini',true);
$CnxODBC = ConectaODBCErpDatasul($ArrayConf,'partnumber');
if(!$CnxODBC){
    echo 'N�o foi poss�vel estabelecer uma conex�o com o ERP.';
    exit;
}

$ArDepara = array(
                    'it-codigo' 	=> 'id_produto',
                    'part-number' 	=> 'id_produto_compl',
                    'descr-original' 	=> 'descr_produto_compl'
                  );

$ArChaves = array('it-codigo');

$Imp = new impODBCProgressTable();
$Imp->setCnxOdbc($CnxODBC);
$Imp->setTabelaOrigem('pub."part-number"');
$Imp->setTabelaDestino('is_id_produto_compl');
$Imp->setArDepara($ArDepara);
$Imp->setChaves($ArChaves);

$Imp->setCampoDepara('id_produto');
$Imp->setCampoDeparaTabelaCRM('is_produto');
$Imp->addCampoDeparaTabelaChaveCRM('id_produto_erp');

$Imp->Importa();
$Imp->mostraResultado();
odbc_close($CnxODBC);
?>