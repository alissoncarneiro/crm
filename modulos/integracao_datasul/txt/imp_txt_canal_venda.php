<?php
header('Content-Type: text/html; charset=iso-8859-1');
include('../../../conecta.php');
include('../../../functions.php');
include('../../../funcoes.php');
include('../../../classes/class.txtimport.php');
$importa = new txtimport;

$importa->ProcuraArquivo('canal-venda');
$importa->TabelaName('is_canal_venda');

$ar_chave = array('id_canal_venda_erp');

$importa->tratamento_especial = array('nome_canal_venda');

$importa->SetArrayChaves($ar_chave);

$ar_campos = 	array('cod-canal-venda' => 'id_canal_venda_erp',
                      'descricao'       => 'nome_canal_venda');
$ar_default =	array( );
$importa->SetArrayDefault($ar_default);
//$importa->NewFieldName('id_pessoa');//CASO PRECISE INSERIR UM ID (AUTO INCREMENTO) AO REGISTRO
$importa->ImportaDados($ar_campos);
$importa->ShowResult();
?>