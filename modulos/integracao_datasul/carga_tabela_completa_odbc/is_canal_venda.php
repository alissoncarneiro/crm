<?php

/*
 * is_canal_venda.php
 * Autor: Alex
 * 02/12/2010 11:40
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
$CnxODBC = ConectaODBCErpDatasul($ArrayConf,'canal-venda');
if(!$CnxODBC){
    echo 'N�o foi poss�vel estabelecer uma conex�o com o ERP.';
    exit;
}
$ArDepara = array(
                'cod-canal-venda'   => 'id_canal_venda_erp',
                'descricao'         => 'nome_canal_venda'
                );
$ArChaves = array('cod-canal-venda');

$Imp = new impODBCProgressTable();
$Imp->setCnxOdbc($CnxODBC);
$Imp->setTabelaOrigem('pub."canal-venda"');
$Imp->setTabelaDestino('is_canal_venda');
$Imp->setArDepara($ArDepara);
$Imp->setChaves($ArChaves);

$Imp->Importa();
$Imp->mostraResultado();
odbc_close($CnxODBC);
?>