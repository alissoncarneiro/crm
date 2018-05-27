<?php
header("Content-Type: text/html; charset=ISO-8859-1");

set_time_limit(0); /* 10 minutos */
include_once('../../conecta.php');
include_once('../../functions.php');
include_once('../../classes/class.csvimport.php');

$a_param_csv = farray(query("select * from is_parametros_sistema where id_parametro = 'diretorio_csv_raiz'"));

$Imp = new csvimport;
$subdir = 'clientes';
$Imp->Diretorio = $a_param_csv["parametro"].'erp/'.$subdir.'/pendentes_integracao/';
$Imp->DiretorioErro = $a_param_csv["parametro"].'erp/'.$subdir.'/com_erros/';
$Imp->DiretorioOK = $a_param_csv["parametro"].'erp/'.$subdir.'/backups/';


$Imp->ProcuraArquivo();

$ArDepara = array(
    '0' => 'id_tp_pessoa',
    '1' => 'razao_social_nome',
    '2' => 'fantasia_apelido',
    '3' => 'sn_estrangeiro',
    '4' => 'cnpj_cpf',
    '5' => 'ie_rg',
    '6' => 'email',
    '7' => 'endereco',
    '8' => 'numero',
    '9' => 'complemento',
    '10' => 'bairro',
    '11' => 'cidade',
    '12' => 'uf',
    '13' => 'id_cep',
    '14' => 'cep',
    '15' => 'pais',
    '16' => 'dt_cadastro',
    '17' => 'id_ramo_atividade',
    '18' => 'site',
    '19' => 'tel1',
    '20' => 'tel2',
    '21' => 'fax',
    '22' => 'id_grupo_cliente',
    '23' => 'id_canal_venda',
    '24' => 'sn_contribuinte_icms',
    '25' => 'dt_limite_credito_validade',
    '26' => 'vl_limite_credito',
    '27' => 'sn_aceita_faturamento_parcial',
    '28' => 'dt_ult_pedido_emitido',
    '29' => 'id_transportadora_padrao',
    '30' => 'id_tab_preco_padrao',
    '31' => 'id_cond_pagto_padrao',
    //'32' => 'id_forma_pagto_padrao',
    '33' => 'cfop_interestadual_padrao',
    '34' => 'cfop_estadual_padrao',
    '35' => 'cfop_internacional_padrao',
    '36' => 'id_pessoa_erp',
    '37' => 'im',
    '38' => 'dt_virou_cliente',
    //'39' => 'id_regiao',
    //'40' => 'id_sit_cred',
    '41' => 'cod_suframa',
    '42' => 'saldo_limite_credito',
    '43' => 'obs',
//    '44' => 'id_segmento',
    '45' => 'id_tp_frete_padrao',
    '46' => 'id_representante_padrao'
);

$ArFixos = array(
    'dthr_importado_erp' => date("Y-m-d"),
    'sn_cliente' => 1,
    'sn_importado_erp' => 1,
    'sn_grupo_inadimplente' => 0,
    'sn_inadimplente' => 0,
    'sn_prospect' => 0,
    'sn_suspect' => 0,
    'sn_concorrente' => 0,
    'sn_parceiro' => 0,
    'sn_fornecedor' => 0,
    'sn_representante' => 0,
    'sn_contato' => 0
);

$ArChaves = array('36');

$Imp->setTabelaDestino('is_pessoa');
$Imp->setArDepara($ArDepara);
$Imp->setArFixos($ArFixos);
$Imp->setChaves($ArChaves);

$Imp->setCampoDepara('id_vendedor_padrao');
$Imp->setCampoDeparaTabelaCRM('is_usuario');
$Imp->addCampoDeparaTabelaChaveCRM('id_representante');

$Imp->setCampoDepara('id_representante_padrao');
$Imp->setCampoDeparaTabelaCRM('is_usuario');
$Imp->addCampoDeparaTabelaChaveCRM('id_representante');

$Imp->setCampoDepara('id_transportadora_padrao');
$Imp->setCampoDeparaTabelaCRM('is_transportadora');
$Imp->addCampoDeparaTabelaChaveCRM('id_transportadora_erp');

$Imp->setCampoDepara('id_tab_preco_padrao');
$Imp->setCampoDeparaTabelaCRM('is_tab_preco');
$Imp->addCampoDeparaTabelaChaveCRM('id_tab_preco_erp');

$Imp->setCampoDepara('id_grupo_cliente');
$Imp->setCampoDeparaTabelaCRM('is_grupo_cliente');
$Imp->addCampoDeparaTabelaChaveCRM('id_grupo_cliente_erp');

$Imp->setCampoDepara('id_cond_pagto_padrao');
$Imp->setCampoDeparaTabelaCRM('is_cond_pagto');
$Imp->addCampoDeparaTabelaChaveCRM('id_cond_pagto_erp');

$Imp->setCampoDepara('id_canal_venda');
$Imp->setCampoDeparaTabelaCRM('is_canal_venda');
$Imp->addCampoDeparaTabelaChaveCRM('id_canal_venda_erp');

$Imp->setCampoDepara('id_ramo_atividade');
$Imp->setCampoDeparaTabelaCRM('is_ramo');
$Imp->addCampoDeparaTabelaChaveCRM('id_ramo_erp');


$Imp->setCampoDepara('cfop_estadual_padrao');
$Imp->setCampoDeparaTabelaCRM('is_cfop');
$Imp->addCampoDeparaTabelaChaveCRM('id_cfop_erp');

$Imp->setCampoDepara('cfop_interestadual_padrao');
$Imp->setCampoDeparaTabelaCRM('is_cfop');
$Imp->addCampoDeparaTabelaChaveCRM('id_cfop_erp');


$Imp->Importa();
$Imp->mostraResultado();
?>