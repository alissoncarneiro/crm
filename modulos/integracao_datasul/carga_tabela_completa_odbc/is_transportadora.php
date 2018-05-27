<?php

/*
 * is_transportadora.php
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
$CnxODBC = ConectaODBCErpDatasul($ArrayConf,'transporte');
if(!$CnxODBC){
    echo 'Não foi possível estabelecer uma conexão com o ERP.';
    exit;
}

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


$Imp = new impODBCProgressTable();
$Imp->setCnxOdbc($CnxODBC);
$Imp->setTabelaOrigem('pub."transporte"');
$Imp->setTabelaDestino('is_transportadora');
$Imp->setArDepara($ArDepara);
$Imp->setChaves($ArChaves);
$Imp->setArFixos($ArFixos);

$Imp->Importa();
$Imp->mostraResultado();
odbc_close($CnxODBC);
?>