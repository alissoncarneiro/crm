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
set_time_limit(600); /* 10 minutos */
include_once('../../../conecta.php');
include_once('../../../functions.php');
include_once('../../../classes/class.impODBCProgressTable.php');

$ArrayConf = parse_ini_file('../../../conecta_odbc_erp_datasul.ini',true);
$CnxODBC = ConectaODBCErpDatasul($ArrayConf,'loc-entr');
if(!$CnxODBC){
    echo 'Não foi possível estabelecer uma conexão com o ERP.';
    exit;
}

$ArDepara = array(
                    'cep'               => 'cep',
                    'endereco'          => 'endereco',
                    'bairro'            => 'bairro',
                    'cidade'            => 'cidade',
                    'estado'            => 'uf',
                    'pais'              => 'pais',
                    'cod-entrega'       => 'id_endereco_erp',
                    'nome-abrev'        => 'id_pessoa'
                );
$ArFixos = array(
                    'id_tp_endereco' 				=> 1,
                    'id_logradouro' 				=> 1
                );

$ArChaves = array('nome-abrev','cod-entrega');

$ArCamposObrigatorios = array('id_pessoa','endereco','bairro','cidade','uf','pais','cep');

$Imp = new impODBCProgressTable();
$Imp->setCnxOdbc($CnxODBC);
$Imp->setTabelaOrigem('pub."loc-entr"');
$Imp->setTabelaDestino('is_pessoa_endereco');
$Imp->setArDepara($ArDepara);
$Imp->setChaves($ArChaves);
$Imp->setArFixos($ArFixos);
$Imp->setArCamposObrigatorios($ArCamposObrigatorios);

$Imp->setCampoDepara('id_pessoa');
$Imp->setCampoDeparaTabelaCRM('is_pessoa');
$Imp->addCampoDeparaTabelaChaveCRM('fantasia_apelido');

$Imp->Importa();
$Imp->mostraResultado();
odbc_close($CnxODBC);
?>