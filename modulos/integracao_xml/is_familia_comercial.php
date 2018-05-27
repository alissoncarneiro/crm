<?php
header("Content-Type: text/html; charset=ISO-8859-1");

set_time_limit(600); /* 10 minutos */
include_once('../../conecta.php');
include_once('../../functions.php');
include_once('../../classes/class.xmlimport.php');

$a_param_xml = farray(query("select * from is_parametros_sistema where id_parametro = 'diretorio_xml_raiz'"));

$Imp = new xmlimport;
$subdir = 'produtos_familia';
$Imp->Diretorio = $a_param_xml["parametro"].'erp/'.$subdir.'/pendentes_integracao/';
$Imp->DiretorioErro = $a_param_xml["parametro"].'erp/'.$subdir.'/com_erros/';
$Imp->DiretorioOK = $a_param_xml["parametro"].'erp/'.$subdir.'/backups/';
$Imp->TagInicial = 'familia_comercial';

$Imp->ProcuraArquivo();

$ArDepara = array(
    'numreg' => 'id_familia_erp',
    'nome_familia_comercial' => 'nome_familia_comercial',
    'sn_ativo' => 'sn_ativo'
);
//$ArFixos = array('sn_ativo' => 1);
$ArChaves = array('numreg');
$Imp->setTabelaDestino('is_familia_comercial');
$Imp->setArDepara($ArDepara);
//$Imp->setArFixos($ArFixos);
$Imp->setChaves($ArChaves);
$Imp->Importa();
$Imp->mostraResultado();
?>