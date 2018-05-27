<?php

set_time_limit(600); /* 10 minutos */
include_once('../../conecta.php');
include_once('../../functions.php');
include_once('../../conecta_odbc_protheus.php');
include_once('../../classes/class.impODBCProtheus.php');

$CnxODBC = odbc_connect($AliasProtheus, $UsuarioProtheus, $SenhaProtheus);
if (!$CnxODBC) {
    echo 'Não foi possível estabelecer uma conexão com o ERP Protheus.';
    exit;
}

$ArDepara = array(
                    'b1_cod'                 => 'id_produto_erp',
                    'b1_grupo'                => 'id_familia_comercial',
                    'b1_desc'                 => 'nome_produto',
                    'b1_uprc'              => 'custo_ult_ent',
                    'b1_um'                        => 'id_unid_medida_padrao',
                    'b1_vlr_ipi'              => 'pct_aliq_ipi',
                    'b1_clasfis'              => 'classificacao_fiscal'
                    );

$ArFixos = array();

$ArChaves = array('b1_cod');

$Imp = new impODBCProtheus();
$Imp->setCnxOdbc($CnxODBC);
$Imp->setTabelaOrigem('sbm' . $CodEmpresaProtheus);
$Imp->setTabelaDestino('is_produto');
$Imp->setArDepara($ArDepara);
$Imp->setChaves($ArChaves);
$Imp->setArFixos($ArFixos);

$Imp->setCampoDepara('id_familia_comercial');
$Imp->setCampoDeparaTabelaCRM('is_familia_comercial');
$Imp->addCampoDeparaTabelaChaveCRM('id_familia_erp');

$Imp->setCampoDepara('id_unid_medida_padrao');
$Imp->setCampoDeparaTabelaCRM('is_unid_medida');
$Imp->addCampoDeparaTabelaChaveCRM('id_unid_medida_erp');

$Imp->Importa();
$Imp->mostraResultado();
odbc_close($CnxODBC);
?>