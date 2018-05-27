<?php
/*
 * is_micro_regiao.php
 * Autor: Alex
 * 11/11/2011 11:05:20
 */
session_start();
include_once('../../../conecta.php');
include_once('../../../functions.php');
include_once('../../../classes/class.impTXTProgressTable.php');

$ArDepara = array(
                'nome-ab-reg'       => 'id_regiao',
                'desc-mic-reg'      => 'nome_micro_regiao',
                'cod-estabel'       => 'id_estabelecimento',
                'narrativa'         => 'descricao',
                'nome-mic-reg'      => 'id_micro_regiao_erp'
            );
$ArChaves = array('nome-ab-reg','nome-mic-reg');

$Imp = new impTXTProgressTable();
$Imp->setNomeArquivoLeitura('micro-reg');
$Imp->setTabelaDestino('is_micro_regiao');
$Imp->setArDepara($ArDepara);
$Imp->setChaves($ArChaves);

$Imp->setCampoDepara('id_regiao');
$Imp->setCampoDeparaTabelaCRM('is_regiao');
$Imp->addCampoDeparaTabelaChaveCRM('id_regiao_erp');

$Imp->setCampoDepara('id_estabelecimento');
$Imp->setCampoDeparaTabelaCRM('is_estabelecimento');
$Imp->addCampoDeparaTabelaChaveCRM('id_estabelecimento_erp');

$Imp->Importa();
$Imp->mostraResultado();
?>