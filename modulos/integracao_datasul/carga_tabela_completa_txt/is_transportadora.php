<?php

/*
 * is_transportadora.php
 * Autor: Alex
 * 11/11/2011 11:16:40
 */
session_start();
include_once('../../../conecta.php');
include_once('../../../functions.php');
include_once('../../../classes/class.impTXTProgressTable.php');

$ArDepara = array(
                    'cod-transp'    => 'id_transportadora_erp',
                    'nome'          => 'nome_transportadora',
                    'nome-abrev'    => 'nome_abrev_transportadora',
                    'cgc'           => 'cnpj',
                    'endereco'      => 'endereco',
                    'bairro'        => 'bairro',
                    'cidade'        => 'cidade',
                    'estado'        => 'uf',
                    'telefone'      => 'tel1'
                );

$ArFixos = array();

$ArChaves = array('cod-transp');

$Imp = new impTXTProgressTable();
$Imp->setNomeArquivoLeitura('transporte');
$Imp->setTabelaDestino('is_transportadora');
$Imp->setArDepara($ArDepara);
$Imp->setChaves($ArChaves);
$Imp->setArFixos($ArFixos);

$Imp->Importa();
$Imp->mostraResultado();
?>