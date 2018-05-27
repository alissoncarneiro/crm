<?php
/*
 * is_cond_pagto.php
 * Autor: Alex
 * 11/11/2011 10:26:06
 */
session_start();
include_once('../../../conecta.php');
include_once('../../../functions.php');
include_once('../../../classes/class.impTXTProgressTable.php');

$ArDepara = array(
                'cod-cond-pag'          => 'id_cond_pagto_erp',
                'descricao'             => 'nome_cond_pagto',
                'qtd-dias-prazo-medio'  => 'media_dias',
                'nr-tab-finan'          => 'id_tab_financiamento',
                'log-atual-idx'         => 'sn_indice_do_prazo_medio',
                'nr-ind-finan'          => 'id_taxa_financiamento'
                );
$ArChaves = array('cod-cond-pag');
$ArTratamentoFixoSimNao = array('sn_indice_do_prazo_medio');

$Imp = new impTXTProgressTable();
$Imp->setNomeArquivoLeitura('cond-pagto');
$Imp->setTabelaDestino('is_cond_pagto');
$Imp->setArDepara($ArDepara);
$Imp->setChaves($ArChaves);
$Imp->setArTratamentoFixoSimNao($ArTratamentoFixoSimNao);

$Imp->setCampoDepara('id_tab_financiamento');
$Imp->setCampoDeparaTabelaCRM('is_tab_financiamento');
$Imp->addCampoDeparaTabelaChaveCRM('id_tab_financiamento_erp');

$Imp->Importa();
$Imp->mostraResultado();
?>