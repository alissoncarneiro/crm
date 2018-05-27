<?php
/*
 * is_canal_venda.php
 * Autor: Alex
 * 11/11/2011 10:43:28
 */
session_start();
include_once('../../../conecta.php');
include_once('../../../functions.php');
include_once('../../../classes/class.impTXTProgressTable.php');

$ArDepara = array(
                'cod-canal-venda' => 'id_canal_venda_erp',
                'descricao'       => 'nome_canal_venda'
                );
$ArChaves = array('cod-canal-venda');

$Imp = new impTXTProgressTable();
$Imp->setNomeArquivoLeitura('canal-venda');
$Imp->setTabelaDestino('is_canal_venda');
$Imp->setArDepara($ArDepara);
$Imp->setChaves($ArChaves);

$Imp->Importa();
$Imp->mostraResultado();
?>