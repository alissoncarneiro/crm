<?php
header("Content-Type: text/html; charset=ISO-8859-1");

set_time_limit(600); /* 10 minutos */
include_once('../../conecta.php');
include_once('../../functions.php');
include_once('../../classes/class.csvimport.php');

$a_param_csv = farray(query("select * from is_parametros_sistema where id_parametro = 'diretorio_csv_raiz'"));

$Imp = new csvimport;
$subdir = 'contatos';
$Imp->Diretorio = $a_param_csv["parametro"].'erp/'.$subdir.'/pendentes_integracao/';
$Imp->DiretorioErro = $a_param_csv["parametro"].'erp/'.$subdir.'/com_erros/';
$Imp->DiretorioOK = $a_param_csv["parametro"].'erp/'.$subdir.'/backups/';

$Imp->ProcuraArquivo();

$ArDepara = array(
    '0' => 'dt_cadastro',
    '1' => 'id_empresa',
    '2' => 'id_contato_erp',
    '3' => 'nome',
    '4' => 'tel1',
    '5' => 'tel2',
    '6' => 'tel3',
    '7' => 'email_profissional',
    '8' => 'email_pessoal',
    '9' => 'skype',
    '10' => 'id_cargo',
    '11' => 'id_graduacao',
    '12' => 'id_grau_influencia',
    '13' => 'id_grau_autoridade',
    '14' => 'id_bebida_preferida',
    '15' => 'id_hobby',
    '16' => 'id_clube_preferido',
    '17' => 'id_area',
    '18' => 'id_estado_civil',
    '19' => 'id_nacionalidade',
    '20' => 'id_forma_contato_preferida',
    '21' => 'id_periodo_contato_preferido',
    '22' => 'dia_nascimento',
    '23' => 'mes_nascimento',
    '24' => 'ano_nascimento',
    '25' => 'dia_casamento',
    '26' => 'mes_casamento',
    '27' => 'ano_casamento',
    '28' => 'obs',
    '29' => 'sn_decide_pela_compra',
    '30' => 'cnpj_cpf',
    '31' => 'ie_rg',
    '32' => 'id_vendedor',
    '33' => 'sn_padrao',
    '34' => 'sn_recebe_mailing'
);

$ArFixos = array();

$ArChaves = array('1','3');

$Imp->setTabelaDestino('is_contato');
$Imp->setArDepara($ArDepara);
//$Imp->setArFixos($ArFixos);
$Imp->setChaves($ArChaves);

$Imp->setCampoDepara('id_empresa');
$Imp->setCampoDeparaTabelaCRM('is_pessoa');
$Imp->addCampoDeparaTabelaChaveCRM('id_pessoa_erp');

$Imp->Importa();
$Imp->mostraResultado();
?>