<?php

/*
 * is_tab_preco_valor.php
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
$CnxODBC = ConectaODBCErpDatasul($ArrayConf,'preco-item');
if(!$CnxODBC){
    echo 'Não foi possível estabelecer uma conexão com o ERP.';
    exit;
}

class impODBCProgressTableCustom_is_tab_preco_valor extends impODBCProgressTable{
    public function setValorCustom($ArDados){
        $ArDados['sn_ativo'] = ($ArDados['sn_ativo'] == 1)?1:0;
        return $ArDados;
    }
}

$ArDepara = array(
                    'nr-tabpre'     => 'id_tab_preco',
                    'it-codigo'     => 'id_produto',
                    'preco-venda'   => 'vl_unitario',
                    'cod-unid-med'  => 'id_unid_medida',
                    'dt-inival'     => 'dt_validade_ini',
                    'situacao'      => 'sn_ativo'
                 );

$ArFixos = array();

$ArChaves = array('nr-tabpre','it-codigo','cod-unid-med','dt-inival','situacao');

$ArCamposObrigatorios = array('id_tab_preco','id_produto','id_unid_medida');

/*
 * Definindo as tabelas que serão importadas
 */
$ArTabelasDePreco = array();
$QryTabPrecoCRM = query("SELECT DISTINCT id_tab_preco_erp FROM is_tab_preco");
while($ArTabPrecoCRM = farray($QryTabPrecoCRM)){
    if($ArTabPrecoCRM['id_tab_preco_erp'] != ''){
        $ArTabelasDePreco[] = TrataApostrofoBD($ArTabPrecoCRM['id_tab_preco_erp']);
    }
}
if(count($ArTabelasDePreco) == 0){
    echo 'Não há tabelas de preço para importar.';
    exit;
}

$SqlCustom = "SELECT \"nr-tabpre\",\"it-codigo\",\"preco-venda\",\"cod-unid-med\",\"dt-inival\",\"situacao\" FROM pub.\"preco-item\" WHERE \"nr-tabpre\" IN('".implode("','",$ArTabelasDePreco)."')";

$Imp = new impODBCProgressTableCustom_is_tab_preco_valor();
$Imp->setCnxOdbc($CnxODBC);
$Imp->setTabelaOrigem('pub."preco-item"');
$Imp->setTabelaDestino('is_tab_preco_valor');
$Imp->setSqlOdbcCustom($SqlCustom);
$Imp->setArDepara($ArDepara);
$Imp->setChaves($ArChaves);
$Imp->setArFixos($ArFixos);
$Imp->setArCamposObrigatorios($ArCamposObrigatorios);

$Imp->setCampoDepara('id_tab_preco');
$Imp->setCampoDeparaTabelaCRM('is_tab_preco');
$Imp->addCampoDeparaTabelaChaveCRM('id_tab_preco_erp');

$Imp->setCampoDepara('id_produto');
$Imp->setCampoDeparaTabelaCRM('is_produto');
$Imp->addCampoDeparaTabelaChaveCRM('id_produto_erp');

$Imp->setCampoDepara('id_unid_medida');
$Imp->setCampoDeparaTabelaCRM('is_unid_medida');
$Imp->addCampoDeparaTabelaChaveCRM('id_unid_medida_erp');

$Imp->Importa();
$Imp->mostraResultado();
odbc_close($CnxODBC);
?>