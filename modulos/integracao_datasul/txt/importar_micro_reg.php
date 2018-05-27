<?php
header('Content-Type: text/html; charset=iso-8859-1');
include('../../../conecta.php');
include('../../../functions.php');
include('../../../funcoes.php');
include('../../../classes/class.txtimport.php');
$importa = new txtimport;

$importa->ProcuraArquivo('micro-reg');
$importa->TabelaName('is_regiao');

$ar_chave = array('id_regiao_erp');

$importa->SetArrayChaves($ar_chave);

$ar_campos = array(
                    'nome-mic-reg' => 'id_regiao_erp',
                    'desc-mic-reg' => 'nome_regiao'
		);
$ar_default = array( );
$importa->SetArrayDefault($ar_default);
//$importa->NewFieldName('id_pessoa');//CASO PRECISE INSERIR UM ID (AUTO INCREMENTO) AO REGISTRO
$importa->ImportaDados($ar_campos);
$importa->ShowResult();
?>