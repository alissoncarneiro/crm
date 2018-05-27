<?php
/*
 * is_micro_regiao.php
 * Autor: Alex
 * 17/05/2011 17:50:50
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
session_start();
include_once('../../../conecta.php');
include_once('../../../functions.php');
include_once('../../../classes/class.impODBCProgressTable.php');

$ArrayConf = parse_ini_file('../../../conecta_odbc_erp_datasul.ini',true);
$CnxODBC = ConectaODBCErpDatasul($ArrayConf,'regiao');
if(!$CnxODBC){
    echo 'N&atilde;o foi poss&iacute;vel estabelecer uma conex&atilde;o com o ERP.';
    exit;
}
$ArDepara = array(
                'nome-regiao'       => 'nome_regiao',
                'nome-ab-reg'       => 'id_regiao_erp',
                'cod-estabel'       => 'id_estabelecimento',
                'pais'              => 'pais',
                'narrativa'         => 'descricao'
            );
$ArChaves = array('nome-ab-reg');

$Imp = new impODBCProgressTable();
$Imp->setCnxOdbc($CnxODBC);
$Imp->setTabelaOrigem('pub."regiao"');
$Imp->setTabelaDestino('is_regiao');
$Imp->setArDepara($ArDepara);
$Imp->setChaves($ArChaves);

$Imp->setCampoDepara('id_estabelecimento');
$Imp->setCampoDeparaTabelaCRM('is_estabelecimento');
$Imp->addCampoDeparaTabelaChaveCRM('id_estabelecimento_erp');

echo '<h2>Importando Tabela de Regi&atilde;o</h2>';
$Imp->Importa();
$Imp->mostraResultado();
odbc_close($CnxODBC);

$CnxODBC = ConectaODBCErpDatasul($ArrayConf,'micro-reg');
if(!$CnxODBC){
    echo 'N&atilde;o foi poss&iacute;vel estabelecer uma conex&atilde;o com o ERP.';
    exit;
}
$ArDepara = array(
                'nome-ab-reg'       => 'id_regiao',
                'desc-mic-reg'      => 'nome_micro_regiao',
                'cod-estabel'       => 'id_estabelecimento',
                'narrativa'         => 'descricao',
                'nome-mic-reg'      => 'id_micro_regiao_erp'
            );
$ArChaves = array('nome-ab-reg','nome-mic-reg');

$Imp = new impODBCProgressTable();
$Imp->setCnxOdbc($CnxODBC);
$Imp->setTabelaOrigem('pub."micro-reg"');
$Imp->setTabelaDestino('is_micro_regiao');
$Imp->setArDepara($ArDepara);
$Imp->setChaves($ArChaves);

$Imp->setCampoDepara('id_regiao');
$Imp->setCampoDeparaTabelaCRM('is_regiao');
$Imp->addCampoDeparaTabelaChaveCRM('id_regiao_erp');

$Imp->setCampoDepara('id_estabelecimento');
$Imp->setCampoDeparaTabelaCRM('is_estabelecimento');
$Imp->addCampoDeparaTabelaChaveCRM('id_estabelecimento_erp');

echo '<h2>Importando Tabela de Micro Regi&atilde;o</h2>';
$Imp->Importa();
$Imp->mostraResultado();
odbc_close($CnxODBC);
?>