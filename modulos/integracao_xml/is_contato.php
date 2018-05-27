<?php

header("Content-Type: text/html; charset=ISO-8859-1");

set_time_limit(600); /* 10 minutos */
include_once('../../conecta.php');
include_once('../../functions.php');
include_once('../../classes/class.xmlimport.php');

$a_param_xml = farray(query("select * from is_parametros_sistema where id_parametro = 'diretorio_xml_raiz'"));

$Imp = new xmlimport;
$subdir = 'cliente_contato';
$Imp->Diretorio = $a_param_xml["parametro"] . 'erp/' . $subdir . '/pendentes_integracao/';
$Imp->DiretorioErro = $a_param_xml["parametro"] . 'erp/' . $subdir . '/com_erros/';
$Imp->DiretorioOK = $a_param_xml["parametro"] . 'erp/' . $subdir . '/backups/';

//---------------------------------------
$Imp->TagInicial = 'pessoa_contato';
//---------------------------------------

$Imp->ProcuraArquivo();

$ArDepara = array(
    'id_pessoa_erp' => 'id_empresa',
    'id_contato_erp' => 'id_contato_erp',
    'nome' => 'nome',
    'tel1' => 'tel1',
    'tel2' => 'tel2',
    'tel3' => 'tel3',
    'email_profissional' => 'email_profissional',
    'email_pessoal' => 'email_pessoal'
);
$ArFixos = array(
    'dt_cadastro' => date("Y-m-d")
);

$ArChaves = array('id_pessoa_erp', 'id_contato_erp');

$Imp->setTabelaDestino('is_contato');
$Imp->setArDepara($ArDepara);
$Imp->setArFixos($ArFixos);
$Imp->setChaves($ArChaves);
$Imp->setArCamposObrigatorios($ArCamposObrigatorios);

$Imp->setCampoDepara('id_empresa');
$Imp->setCampoDeparaTabelaCRM('is_pessoa');
$Imp->addCampoDeparaTabelaChaveCRM('id_pessoa_erp');

$Imp->Importa();
$Imp->mostraResultado();
?>