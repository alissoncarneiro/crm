<?php
header('Content-Type: text/html; charset=iso-8859-1');
include('../../../conecta.php');
include('../../../functions.php');
include('../../../funcoes.php');
include('../../../classes/class.txtimport.php');
$importa = new txtimport;

$importa->ProcuraArquivo('fam-comerc');
$importa->TabelaName('is_familia_comercial');

$ar_chave = array('id_familia_erp');

$importa->SetArrayChaves($ar_chave);

$ar_campos = array(
                    'fm-cod-com' => 'id_familia_erp',
                    'descricao'  => 'nome_familia_comercial'
		);
$ar_default =	array('sn_ativo' => '1');
$importa->SetArrayDefault($ar_default);
//$importa->NewFieldName('id_pessoa');//CASO PRECISE INSERIR UM ID (AUTO INCREMENTO) AO REGISTRO
$importa->ImportaDados($ar_campos);
$importa->ShowResult();
?>