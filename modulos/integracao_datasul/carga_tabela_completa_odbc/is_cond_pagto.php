<?php

/*
 * is_cond_pagto.php
 * Autor: Alex
 * 28/11/2010 09:04
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
$CnxODBC = ConectaODBCErpDatasul($ArrayConf,'cond-pagto');
if(!$CnxODBC){
    echo 'Não foi possível estabelecer uma conexão com o ERP.';
    exit;
}

$ArDepara = array(
                'cod-cond-pag'          => 'id_cond_pagto_erp',
                'descricao'             => 'nome_cond_pagto',
                'qtd-dias-prazo-medio'  => 'media_dias',
                'nr-tab-finan'          => 'id_tab_financiamento',
                'log-atual-idx'         => 'sn_indice_do_prazo_medio',
                'nr-ind-finan'          => 'id_taxa_financiamento'
                );
$ArChaves = array('cod-cond-pag');

$Imp = new impODBCProgressTable();
$Imp->setCnxOdbc($CnxODBC);
$Imp->setTabelaOrigem('pub."cond-pagto"');
$Imp->setTabelaDestino('is_cond_pagto');
$Imp->setArDepara($ArDepara);
$Imp->setChaves($ArChaves);

$Imp->setCampoDepara('id_tab_financiamento');
$Imp->setCampoDeparaTabelaCRM('is_tab_financiamento');
$Imp->addCampoDeparaTabelaChaveCRM('id_tab_financiamento_erp');

$Imp->Importa();
$Imp->mostraResultado();
odbc_close($CnxODBC);
?>