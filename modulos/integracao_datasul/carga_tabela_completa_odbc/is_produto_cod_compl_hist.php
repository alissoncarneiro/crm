<?php

/*
 * is_produto_cod_compl_hist.php
 * Autor: Bruno
 * 24/01/2011 18:19
 * -
 *
 * Log de Alterações
 * 2011-02-07 Alex Corrigido nome da tabela e nome de colunas, e arquivo recriado com o nome correto
 */
session_start();
set_time_limit(600); /* 10 minutos */
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
                    'part-number' 	=> 'id_produto_cod_compl_hist'
                  );

$ArChaves = array('it-codigo','part-number');

$ArCamposObrigatorios = array('id_produto');

$Imp = new impODBCProgressTable();
$Imp->setCnxOdbc($CnxODBC);
$Imp->setTabelaOrigem('pub."hist-part-number"');
$Imp->setTabelaDestino('is_produto_cod_compl_hist');
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