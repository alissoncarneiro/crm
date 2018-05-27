<?php
header("Content-Type: text/html; charset=ISO-8859-1");

set_time_limit(600); /* 10 minutos */
include_once('../../conecta.php');
include_once('../../functions.php');
include_once('../../classes/class.xmlimport.php');

$a_param_xml = farray(query("select * from is_parametros_sistema where id_parametro = 'diretorio_xml_raiz'"));

$Imp = new xmlimport;
$subdir = 'produtos';
$Imp->Diretorio = $a_param_xml["parametro"].'erp/'.$subdir.'/pendentes_integracao/';
$Imp->DiretorioErro = $a_param_xml["parametro"].'erp/'.$subdir.'/com_erros/';
$Imp->DiretorioOK = $a_param_xml["parametro"].'erp/'.$subdir.'/backups/';

$Imp->TagInicial = 'produto';

$Imp->ProcuraArquivo();

$ArDepara = array(
    'id_produto_erp' => 'id_produto_erp',
    'id_familia_comercial' => 'id_familia_comercial',
    'nome_produto' => 'nome_produto',
    'nome_produto_detalhado' => 'nome_produto_detalhado',
    'custo_ult_ent' => 'custo_ult_ent',
    'custo_repos' => 'custo_repos',
    'custo_base' => 'custo_base',
    'id_unid_medida_padrao' => 'id_unid_medida_padrao',
    'pct_aliq_ipi' => 'pct_aliq_ipi',
    'id_grupo_estoque' => 'id_grupo_estoque',
    'classificacao_fiscal' => 'classificacao_fiscal'
);

$ArFixos = array();

$ArChaves = array('id_produto_erp');

$Imp->setTabelaDestino('is_produto');
$Imp->setArDepara($ArDepara);
//$Imp->setArFixos($ArFixos);
$Imp->setChaves($ArChaves);

$Imp->setCampoDepara('id_familia_comercial');
$Imp->setCampoDeparaTabelaCRM('is_familia_comercial');
$Imp->addCampoDeparaTabelaChaveCRM('id_familia_erp');

$Imp->setCampoDepara('id_unid_medida_padrao');
$Imp->setCampoDeparaTabelaCRM('is_unid_medida');
$Imp->addCampoDeparaTabelaChaveCRM('id_unid_medida_erp');


$Imp->Importa();
$Imp->mostraResultado();
?>