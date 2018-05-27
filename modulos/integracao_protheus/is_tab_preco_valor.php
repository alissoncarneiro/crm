<?php
set_time_limit(600); /* 10 minutos */
include_once('../../conecta.php');
include_once('../../functions.php');
include_once('../../conecta_odbc_protheus.php');
include_once('../../classes/class.impODBCProtheus.php');

$CnxODBC = odbc_connect($AliasProtheus, $UsuarioProtheus, $SenhaProtheus);
if(!$CnxODBC){
    echo 'Não foi possível estabelecer uma conexão com o ERP Protheus.';
    exit;
}

class impODBCProgressTableCustom_is_tab_preco_valor extends impODBCProtheus{
    public function setValorCustom($ArDados){
        $ArDados['sn_ativo'] = ($ArDados['sn_ativo'] == 1)?1:0;
        return $ArDados;
    }
}

$ArDepara = array(
                    'da1_codtab'     => 'id_tab_preco',
                    'da1_codpro'     => 'id_produto',
                    'da1_prcven'   => 'vl_unitario',
                    'da1_datvig'     => 'dt_validade_ini',
                    'da1_ativo'      => 'sn_ativo'
                 );
//'cod-unid-med'  => 'id_unid_medida',

$ArFixos = array();

$ArChaves = array('da1_codtab','da1_codpro');

$ArCamposObrigatorios = array('id_tab_preco','id_produto');

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

$SqlCustom = "SELECT * FROM DA1'.$CodEmpresaProtheus.' WHERE da1_codtab IN('".implode("','",$ArTabelasDePreco)."')";

$Imp = new impODBCProgressTableCustom_is_tab_preco_valor();
$Imp->setCnxOdbc($CnxODBC);
$Imp->setTabelaOrigem('DA1'.$CodEmpresaProtheus);
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

/*$Imp->setCampoDepara('id_unid_medida');
$Imp->setCampoDeparaTabelaCRM('is_unid_medida');
$Imp->addCampoDeparaTabelaChaveCRM('id_unid_medida_erp');*/

$Imp->Importa();
$Imp->mostraResultado();
odbc_close($CnxODBC);
?>