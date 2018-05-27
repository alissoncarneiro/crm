<?php
/*
 * is_familia_comercial.php
 * Autor: Alex
 * 11/11/2011 11:08:32
 */
session_start();
include_once('../../../conecta.php');
include_once('../../../functions.php');
include_once('../../../classes/class.impTXTProgressTable.php');

$ArDepara = array(
                'fm-cod-com' 	=> 'id_familia_erp',
                'descricao'     => 'nome_familia_comercial'
                );
$ArFixos = array('sn_ativo' => 1);
$ArChaves = array('fm-cod-com');

$Imp = new impTXTProgressTable();
$Imp->setNomeArquivoLeitura('fam-comerc');
$Imp->setTabelaDestino('is_familia_comercial');
$Imp->setArDepara($ArDepara);
$Imp->setArFixos($ArFixos);
$Imp->setChaves($ArChaves);

$Imp->Importa();
$Imp->mostraResultado();
?>