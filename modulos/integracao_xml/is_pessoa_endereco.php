<?php

header("Content-Type: text/html; charset=ISO-8859-1");

set_time_limit(600); /* 10 minutos */
include_once('../../conecta.php');
include_once('../../functions.php');
include_once('../../classes/class.xmlimport.php');

$a_param_xml = farray(query("select * from is_parametros_sistema where id_parametro = 'diretorio_xml_raiz'"));

$Imp = new xmlimport;
$subdir = 'cliente_endereco';
$Imp->Diretorio = $a_param_xml["parametro"] . 'erp/' . $subdir . '/pendentes_integracao/';
$Imp->DiretorioErro = $a_param_xml["parametro"] . 'erp/' . $subdir . '/com_erros/';
$Imp->DiretorioOK = $a_param_xml["parametro"] . 'erp/' . $subdir . '/backups/';

//--------------------------------------
$Imp->TagInicial = 'pessoa_endereco';
//--------------------------------------

$Imp->ProcuraArquivo();

$ArDepara = array(
    'id_pessoa_erp' => 'id_pessoa',
    'id_endereco_erp' => 'id_endereco_erp',
    'cep' => 'cep',
    'endereco' => 'endereco',
    'bairro' => 'bairro',
    'cidade' => 'cidade',
    'estado' => 'uf',
    'pais' => 'pais'
);
$ArFixos = array(
    'id_tp_endereco' => 1,
    'id_logradouro' => 1
);

$ArChaves = array('id_pessoa_erp', 'id_endereco_erp');

//$ArCamposObrigatorios = array('id_pessoa_erp', 'endereco', 'bairro', 'cidade', 'uf', 'pais', 'cep');

$Imp->setTabelaDestino('is_pessoa_endereco');
$Imp->setArDepara($ArDepara);
$Imp->setArFixos($ArFixos);
$Imp->setChaves($ArChaves);

//$Imp->setArCamposObrigatorios($ArCamposObrigatorios);

$Imp->setCampoDepara('id_pessoa');
$Imp->setCampoDeparaTabelaCRM('is_pessoa');
$Imp->addCampoDeparaTabelaChaveCRM('id_pessoa_erp');

$Imp->Importa();
$Imp->mostraResultado();
?>