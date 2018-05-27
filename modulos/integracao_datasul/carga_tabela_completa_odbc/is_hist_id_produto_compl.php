<?php

/*
 * is_id_produto_compl.php
 * Autor: Bruno
 * 24/01/2011 18:19
 * -
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
session_start();
include('../../../conecta.php');
include('../../../functions.php');
include('../../../classes/class.impODBCProgressTable.php');

$ArrayConf = parse_ini_file('../../../conecta_odbc_erp_datasul.ini',true);
$CnxODBC = ConectaODBCErpDatasul($ArrayConf,'partnumber');
if(!$CnxODBC){
    echo 'Não foi possível estabelecer uma conexão com o ERP.';
    exit;
}

$ArDepara = array(
                    'it-codigo' 	=> 'id_produto',
                    'part-number' 	=> 'id_produto_compl',
                    'data' 		=> 'data',
                    'hora' 		=> 'hora',
                    'usuario' 		=> 'id_usuario'
                  );

$ArChaves = array('it-codigo','part-number');

$Imp = new impODBCProgressTable();
$Imp->setCnxOdbc($CnxODBC);
$Imp->setTabelaOrigem('pub."hist-part-number"');
$Imp->setTabelaDestino('is_hist_id_produto_compl');
$Imp->setArDepara($ArDepara);
$Imp->setChaves($ArChaves);

$Imp->setCampoDepara('id_produto');
$Imp->setCampoDeparaTabelaCRM('is_produto');
$Imp->addCampoDeparaTabelaChaveCRM('id_produto_erp');

$Imp->Importa();
$Imp->mostraResultado();
odbc_close($CnxODBC);
?>