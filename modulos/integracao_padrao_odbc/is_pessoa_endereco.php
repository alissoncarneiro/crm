<?php

/*
 * is_pessoa_endereco.php
 * Autor: Alex
 * 09/02/2011 12:41
 * -
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
set_time_limit(300);
if(!defined('CARGAODBCPADRAO')){
    define('CARGAODBCPADRAO',true);
}
include_once('../../conecta.php');
include_once('../../functions.php');
include_once('../../classes/class.impODBCTable.php');

$CnxODBC = odbc_connect(IntPadODBCServidor, IntPadODBCUsuario, IntPadODBCSenha);
if(!$CnxODBC){
    echo 'Não foi possível estabelecer uma conexão com o ERP.';
    exit;
}

$ArDepara = array(
                    'cep'               => 'cep',
                    'endereco'          => 'endereco',
                    'numero'            => 'numero',
                    'bairro'            => 'bairro',
                    'cidade'            => 'cidade',
                    'uf'            => 'uf',
                    'pais'              => 'pais',
                    'id_endereco_erp'   => 'id_endereco_erp',
                    'id_pessoa_erp'     => 'id_pessoa'
                );
$ArFixos = array(
                    'id_tp_endereco' 	=> 1,
                    'id_logradouro' 	=> 1
                );

$ArChaves = array('id_pessoa_erp','id_endereco_erp');

$ArCamposObrigatorios = array('id_pessoa','endereco','bairro','cidade','uf','pais','cep');

$Imp = new impODBCTable(IntPadODBCTipoBD);
$Imp->setCnxOdbc($CnxODBC);
$Imp->setTabelaOrigem('vw_is_int_pessoas_end_entrega');
$Imp->setTabelaDestino('is_pessoa_endereco');
$Imp->setArDepara($ArDepara);
$Imp->setChaves($ArChaves);
$Imp->setArFixos($ArFixos);
$Imp->setArCamposObrigatorios($ArCamposObrigatorios);

$Imp->setCampoDepara('id_pessoa');
$Imp->setCampoDeparaTabelaCRM('is_pessoa');
$Imp->addCampoDeparaTabelaChaveCRM('id_pessoa_erp');

$Imp->Importa();
$Imp->mostraResultado();
odbc_close($CnxODBC);
?>