<?php
/*
 * is_produto_pessoa.php
 * Autor: Alex
 * 02/02/2011 15:25
 */
session_start();
set_time_limit(600); /* 10 minutos */
include_once('../../../conecta.php');
include_once('../../../functions.php');
include_once('../../../classes/class.impODBCProgressTable.php');

$ArrayConf = parse_ini_file('../../../conecta_odbc_erp_datasul.ini',true);
$CnxODBC = ConectaODBCErpDatasul($ArrayConf,'item-cli');
if(!$CnxODBC){
    echo 'Não foi possível estabelecer uma conexão com o ERP.';
    exit;
}

$ArDepara = array(
                'it-codigo'     => 'id_produto',
                'cod-emitente'  => 'id_pessoa',
                'item-do-cli'   => 'id_produto_pessoa',
                'unid-med-cli'  => 'id_unid_medida',
                'fator-conver'  => 'fator_conversao',
                'num-casa-dec'  => 'numero_casas_decimais',
                'lote-mulven'   => 'qtde_multipla_venda',
                'dec-1'         => 'pct_sub_tri'
                );
$ArChaves = array('it-codigo','cod-emitente');

$ArCamposObrigatorios = array('id_produto','id_pessoa','id_produto_pessoa');

$Imp = new impODBCProgressTable();
$Imp->setCnxOdbc($CnxODBC);
$Imp->setTabelaOrigem('pub."item-cli"');
$Imp->setTabelaDestino('is_produto_pessoa');
$Imp->setArDepara($ArDepara);
$Imp->setChaves($ArChaves);
$Imp->setArCamposObrigatorios($ArCamposObrigatorios);

$Imp->setCampoDepara('id_produto');
$Imp->setCampoDeparaTabelaCRM('is_produto');
$Imp->addCampoDeparaTabelaChaveCRM('id_produto_erp');

$Imp->setCampoDepara('id_pessoa');
$Imp->setCampoDeparaTabelaCRM('is_pessoa');
$Imp->addCampoDeparaTabelaChaveCRM('id_pessoa_erp');

$Imp->setCampoDepara('id_unid_medida');
$Imp->setCampoDeparaTabelaCRM('is_unid_medida');
$Imp->addCampoDeparaTabelaChaveCRM('id_unid_medida_erp');

$Imp->Importa();
$Imp->mostraResultado();
odbc_close($CnxODBC);
?>