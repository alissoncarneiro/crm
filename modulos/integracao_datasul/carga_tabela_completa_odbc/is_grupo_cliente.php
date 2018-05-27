<?php

/*
 * is_grupo_cliente.php
 * Autor: Alex
 * 28/11/2010 09:04
 * -
 *
 * Log de Altera��es
 * yyyy-mm-dd <Pessoa respons�vel> <Descri��o das altera��es>
 */
session_start();
include_once('../../../conecta.php');
include_once('../../../functions.php');
include_once('../../../classes/class.impODBCProgressTable.php');


$ArrayConf = parse_ini_file('../../../conecta_odbc_erp_datasul.ini',true);
$CnxODBC = ConectaODBCErpDatasul($ArrayConf,'gr-cli');
if(!$CnxODBC){
    echo 'N�o foi poss�vel estabelecer uma conex�o com o ERP.';
    exit;
}

$ArDepara = array(
                    'cod-gr-cli'    => 'id_grupo_cliente_erp',
                    'descricao'     => 'nome_grupo_cliente'
                );

$ArFixos = array();

$ArChaves = array('cod-gr-cli');


$Imp = new impODBCProgressTable();
$Imp->setCnxOdbc($CnxODBC);
$Imp->setTabelaOrigem('pub."gr-cli"');
$Imp->setTabelaDestino('is_grupo_cliente');
$Imp->setArDepara($ArDepara);
$Imp->setChaves($ArChaves);
$Imp->setArFixos($ArFixos);

$Imp->Importa();
$Imp->mostraResultado();
odbc_close($CnxODBC);
?>