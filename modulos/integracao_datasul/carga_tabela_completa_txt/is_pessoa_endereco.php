<?php
/*
 * is_pessoa_endereco.php
 * Autor: Alex
 * 11/11/2011 10:45:55
 */
session_start();
include_once('../../../conecta.php');
include_once('../../../functions.php');
include_once('../../../classes/class.impTXTProgressTable.php');

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
                    'id_tp_endereco'    => 1,
                    'id_logradouro' 	=> 1
                );

$ArChaves = array('nome-abrev','cod-entrega');

$ArCamposObrigatorios = array('id_pessoa','endereco','bairro','cidade','uf','pais','cep');

$Imp = new impTXTProgressTable();
$Imp->setNomeArquivoLeitura('loc-entr');
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
?>