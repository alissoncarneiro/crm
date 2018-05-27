<?php
/*
 * is_produto.php
 * Autor: Alex
 * 08/02/2011 17:31
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
                    'id_erp'                    => 'id_produto_erp',
                    'nome'                      => 'nome_produto',
                    'id_produto_estr_erp'       => 'id_produto_compl',
                    'id_unidade_medida_erp'     => 'id_unid_medida_padrao'
                    );

$ArFixos = array();

$ArChaves = array('id_erp');

$SqlOdbcCustom = "SELECT TOP(100) * FROM vw_is_int_produtos";

/*
 * Definindo as tabelas que serão importadas
 */

$Imp = new impODBCTable(IntPadODBCTipoBD);
$Imp->setCnxOdbc($CnxODBC);
$Imp->setTabelaOrigem('vw_is_int_produtos');
$Imp->setTabelaDestino('is_produto');
$Imp->setArDepara($ArDepara);
$Imp->setChaves($ArChaves);
$Imp->setArFixos($ArFixos);
#$Imp->setSqlOdbcCustom($SqlOdbcCustom);
$Imp->setCamposObrigatorios('id_produto_erp');
/*
 * Na view não há campo de código de família comercial
$Imp->setCampoDepara('id_familia_comercial');
$Imp->setCampoDeparaTabelaCRM('is_familia_comercial');
$Imp->addCampoDeparaTabelaChaveCRM('id_familia_erp');
*/
$Imp->setCampoDepara('id_unid_medida_padrao');
$Imp->setCampoDeparaTabelaCRM('is_unid_medida');
$Imp->addCampoDeparaTabelaChaveCRM('id_unid_medida_erp');

$Imp->Importa();
$Imp->mostraResultado();
odbc_close($CnxODBC);
?>