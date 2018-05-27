<?php

/*
 * is_produto_uf.php
 * Autor: Alex
 * 02/12/2010 10:45
 * -
 *
 * Log de Altera��es
 * yyyy-mm-dd <Pessoa respons�vel> <Descri��o das altera��es>
 */
session_start();
set_time_limit(600); /* 10 minutos */
include_once('../../../conecta.php');
include_once('../../../functions.php');
include_once('../../../classes/class.impODBCProgressTable.php');

$ArrayConf = parse_ini_file('../../../conecta_odbc_erp_datasul.ini',true);
$CnxODBC = ConectaODBCErpDatasul($ArrayConf,'item-uf');
if(!$CnxODBC){
    echo 'N�o foi poss�vel estabelecer uma conex�o com o ERP.';
    exit;
}

$ArDepara = array(
                    'it-codigo'         => 'id_produto',
                    'cod-estado-orig'   => 'uf_origem',
                    'estado'            => 'uf_destino',
                    'per-sub-tri'       => 'pct_sub_tri',
                    'dec-1'             => 'pct_icms_estadual'
                    );

$ArChaves = array('it-codigo','cod-estado-orig','estado');

$ArCamposObrigatorios = array('id_produto');

/*
 * Definindo as tabelas que ser�o importadas
 */

$Imp = new impODBCProgressTable();
$Imp->setCnxOdbc($CnxODBC);
$Imp->setTabelaOrigem('pub."item-uf"');
$Imp->setTabelaDestino('is_produto_uf');
$Imp->setArDepara($ArDepara);
$Imp->setChaves($ArChaves);
$Imp->setArCamposObrigatorios($ArCamposObrigatorios);

$Imp->setCampoDepara('id_produto');
$Imp->setCampoDeparaTabelaCRM('is_produto');
$Imp->addCampoDeparaTabelaChaveCRM('id_produto_erp');

$Imp->Importa();
$Imp->mostraResultado();
odbc_close($CnxODBC);
?>