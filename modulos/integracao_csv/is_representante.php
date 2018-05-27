<?php
header("Content-Type: text/html; charset=ISO-8859-1");

set_time_limit(600); /* 10 minutos */
include_once('../../conecta.php');
include_once('../../functions.php');
include_once('../../classes/class.csvimport.php');

$a_param_csv = farray(query("select * from is_parametros_sistema where id_parametro = 'diretorio_csv_raiz'"));

$Imp = new csvimport;
$subdir = 'representantes';
$Imp->Diretorio = $a_param_csv["parametro"].'erp/'.$subdir.'/pendentes_integracao/';
$Imp->DiretorioErro = $a_param_csv["parametro"].'erp/'.$subdir.'/com_erros/';
$Imp->DiretorioOK = $a_param_csv["parametro"].'erp/'.$subdir.'/backups/';

$Imp->ProcuraArquivo();

$ArDepara = array(
    '0' => 'id_representante',
    '1' => 'nome_usuario',
    //'2' => 'sn_ativo',
    //'3' => 'cnpj_cpf',
    '4' => 'email',
    '5' => 'tel1',
    '6' => 'tel2'
/*    '7' => 'endereco',
    '8' => 'numero',
    '9' => 'complemento',
    '10' => 'bairro',
    '11' => 'cidade',
    '12' => 'uf',
    '13' => 'cep'
  */
);

$ArFixos = array(
    'id_perfil' => '5'
);


$ArChaves = array('0','1');

$Imp->setTabelaDestino('is_usuario');
$Imp->setArDepara($ArDepara);
$Imp->setArFixos($ArFixos);
$Imp->setChaves($ArChaves);


$Imp->Importa();

query("update is_usuario set senha = 'oasis', idioma='PT', id_usuario = id_representante where id_perfil = '5'");

$Imp->mostraResultado();
?>