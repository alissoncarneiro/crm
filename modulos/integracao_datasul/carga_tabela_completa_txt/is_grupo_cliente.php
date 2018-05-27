<?php
/*
 * is_grupo_cliente.php
 * Autor: Alex
 * 11/11/2011 11:10:03
 */
session_start();
include_once('../../../conecta.php');
include_once('../../../functions.php');
include_once('../../../classes/class.impTXTProgressTable.php');

$ArDepara = array(
                    'cod-gr-cli'    => 'id_grupo_cliente_erp',
                    'descricao'     => 'nome_grupo_cliente'
                );

$ArFixos = array();

$ArChaves = array('cod-gr-cli');


$Imp = new impTXTProgressTable();
$Imp->setNomeArquivoLeitura('gr-cli');
$Imp->setTabelaDestino('is_grupo_cliente');
$Imp->setArDepara($ArDepara);
$Imp->setChaves($ArChaves);
$Imp->setArFixos($ArFixos);

$Imp->Importa();
$Imp->mostraResultado();
?>