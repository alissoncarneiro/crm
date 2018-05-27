<?php
/*
 * is_familia_comercial.php
 * Autor: Alex
 * 08/02/2011 17:34
 * -
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
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
                'id_erp'    => 'id_familia_erp',
                'nome'      => 'nome_familia_comercial'
                );
$ArChaves = array('id_erp');
$ArFixos = array('sn_ativo' => '1');

$Imp = new impODBCTable(IntPadODBCTipoBD);
$Imp->setCnxOdbc($CnxODBC);
$Imp->setTabelaOrigem('vw_is_int_familia_produtos');
$Imp->setTabelaDestino('is_familia_comercial');
$Imp->setArDepara($ArDepara);
$Imp->setChaves($ArChaves);
$Imp->setArFixos($ArFixos);

$Imp->Importa();
$Imp->mostraResultado();
odbc_close($CnxODBC);
?>