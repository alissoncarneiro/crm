<?php
/*
 * is_unid_medida.php
 * Autor: Alex
 * 08/02/2011 17:34
 * -
 *
 * Log de Altera��es
 * yyyy-mm-dd <Pessoa respons�vel> <Descri��o das altera��es>
 */
if(!defined('CARGAODBCPADRAO')){
    define('CARGAODBCPADRAO',true);
}
include_once('../../conecta.php');
include_once('../../functions.php');
include_once('../../classes/class.impODBCTable.php');

$CnxODBC = odbc_connect(IntPadODBCServidor, IntPadODBCUsuario, IntPadODBCSenha);
if(!$CnxODBC){
    echo 'N�o foi poss�vel estabelecer uma conex�o com o ERP.';
    exit;
}

$ArDepara = array(
                'id_erp'    => 'id_unid_medida_erp',
                'nome'      => 'nome_unid_medida'
                );
$ArChaves = array('id_erp');

$Imp = new impODBCTable(IntPadODBCTipoBD);
$Imp->setCnxOdbc($CnxODBC);
$Imp->setTabelaOrigem('vw_is_int_unidade_medida');
$Imp->setTabelaDestino('is_unid_medida');
$Imp->setArDepara($ArDepara);
$Imp->setChaves($ArChaves);

$Imp->Importa();
$Imp->mostraResultado();
odbc_close($CnxODBC);
?>