<?php

header("Content-Type: text/html; charset=ISO-8859-1");

set_time_limit(600); /* 10 minutos */
include_once('../../conecta.php');
include_once('../../functions.php');
include_once('../../classes/class.xmlimport.php');

$a_param_xml = farray(query("select * from is_parametros_sistema where id_parametro = 'diretorio_xml_raiz'"));

$Imp = new xmlimport;
$subdir = 'clientes';
$Imp->Diretorio = $a_param_xml["parametro"] . 'erp/' . $subdir . '/pendentes_integracao/';
$Imp->DiretorioErro = $a_param_xml["parametro"] . 'erp/' . $subdir . '/com_erros/';
$Imp->DiretorioOK = $a_param_xml["parametro"] . 'erp/' . $subdir . '/backups/';
$Imp->TagInicial = 'pessoa';

$Imp->ProcuraArquivo();

$ArDepara = array(
    'dt_cadastro' => 'dt_cadastro',
    'id_tp_pessoa' => 'id_tp_pessoa',
    'id_pessoa_erp' => 'id_pessoa_erp',
    'razao_social_nome' => 'razao_social_nome',
    'fantasia_apelido' => 'fantasia_apelido',
    'cnpj_cpf' => 'cnpj_cpf',
    'ie_rg' => 'ie_rg',
    'email' => 'email',
    'endereco' => 'endereco',
    'bairro' => 'bairro',
    'cidade' => 'cidade',
    'uf' => 'uf',
    'pais' => 'pais',
    'cep' => 'cep',
    'id_representante_padrao' => 'id_representante_padrao',
    'id_vendedor_padrao' => 'id_vendedor_padrao',
    'id_grupo_cliente' => 'id_grupo_cliente',
    'id_tab_preco_padrao' => 'id_tab_preco_padrao',
    'id_transportadora_padrao' => 'id_transportadora_padrao',
    'id_cond_pagto_padrao' => 'id_cond_pagto_padrao',
    'id_canal_venda' => 'id_canal_venda',
    'sn_contribuinte_icms' => 'sn_contribuinte_icms',
    'sn_aceita_faturamento_parcial' => 'sn_aceita_faturamento_parcial',
    'cfop_estadual_padrao' => 'cfop_estadual_padrao',
    'cfop_interestadual_padrao' => 'cfop_interestadual_padrao',
    'id_sit_cred' => 'id_sit_cred',
    'site' => 'site',
    'tel1' => 'tel1',
    'tel2' => 'tel2',
    'fax' => 'fax',
    'dt_virou_cliente' => 'dt_virou_cliente',
    'cod_suframa' => 'cod_suframa',
    'obs' => 'obs'
);

$ArChaves = array('cnpj_cpf');

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

$Imp->setCampoDepara('cfop_estadual_padrao');
$Imp->setCampoDeparaTabelaCRM('is_cfop');
$Imp->addCampoDeparaTabelaChaveCRM('id_cfop_erp');

$Imp->setCampoDepara('cfop_interestadual_padrao');
$Imp->setCampoDeparaTabelaCRM('is_cfop');
$Imp->addCampoDeparaTabelaChaveCRM('id_cfop_erp');


$Imp->Importa();
$Imp->mostraResultado();
?>