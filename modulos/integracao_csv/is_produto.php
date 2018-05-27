<?php
header("Content-Type: text/html; charset=ISO-8859-1");

set_time_limit(600); /* 10 minutos */
include_once('../../conecta.php');
include_once('../../functions.php');
include_once('../../classes/class.csvimport.php');

$a_param_csv = farray(query("select * from is_parametros_sistema where id_parametro = 'diretorio_csv_raiz'"));

$Imp = new csvimport;
$subdir = 'produtos';
$Imp->Diretorio = $a_param_csv["parametro"].'erp/'.$subdir.'/pendentes_integracao/';
$Imp->DiretorioErro = $a_param_csv["parametro"].'erp/'.$subdir.'/com_erros/';
$Imp->DiretorioOK = $a_param_csv["parametro"].'erp/'.$subdir.'/backups/';

$Imp->ProcuraArquivo();

$ArDepara = array(
    '0' => 'id_produto_erp',
    '1' => 'nome_produto',
    '2' => 'id_produto_compl',
    '3' => 'nome_produto_detalhado',
    '4' => 'pct_aliq_ipi',
    '5' => 'classificacao_fiscal',
    '6' => 'id_familia',
    '7' => 'id_familia_comercial',
    //'8' => 'id_grupo_estoque',
    '9' => 'custo_ult_ent',
    '10' => 'custo_repos',
    '11' => 'custo_base',
    '12' => 'id_fornecedor',
    '13' => 'id_unid_medida_padrao',
    '14' => 'sn_recorrente',
    '15' => 'id_obsoleto',
    '16' => 'pontos_fortes',
    '17' => 'pontos_fracos',
    '18' => 'arquivo_imagem',
    '19' => 'garantia_meses'
);

$ArFixos = array();

$ArChaves = array('0');

$Imp->setTabelaDestino('is_produto');
$Imp->setArDepara($ArDepara);
//$Imp->setArFixos($ArFixos);
$Imp->setChaves($ArChaves);

$Imp->setCampoDepara('id_familia_comercial');
$Imp->setCampoDeparaTabelaCRM('is_familia_comercial');
$Imp->addCampoDeparaTabelaChaveCRM('id_familia_erp');

$Imp->setCampoDepara('id_familia');
$Imp->setCampoDeparaTabelaCRM('is_familia');
$Imp->addCampoDeparaTabelaChaveCRM('id_familia_erp');

/*$Imp->setCampoDepara('id_grupo_estoque');
$Imp->setCampoDeparaTabelaCRM('is_grupo_estoque');
$Imp->addCampoDeparaTabelaChaveCRM('id_grupo_estoque_erp');*/

$Imp->setCampoDepara('id_unid_medida_padrao');
$Imp->setCampoDeparaTabelaCRM('is_unid_medida');
$Imp->addCampoDeparaTabelaChaveCRM('id_unid_medida_erp');


$Imp->Importa();
$Imp->mostraResultado();
?>