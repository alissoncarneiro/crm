<?php
header('Content-Type: text/html; charset=iso-8859-1');
include('../../../conecta.php');
include('../../../functions.php');
include('../../../funcoes.php');
include('../../../classes/class.txtimport.php');
$importa = new txtimport;

$importa->ProcuraArquivo('tb-preco');
$importa->TabelaName('is_tab_preco');

$ar_chave = array('id_tab_preco_erp');

$importa->SetArrayChaves($ar_chave);

$ar_campos = 	array('nr-tabpre'	=> 'id_tab_preco_erp',
                      'descricao'	=> 'nome_tab_preco');
$ar_default =	array('sn_ativa' => '1');
$importa->SetArrayDefault($ar_default);
//$importa->NewFieldName('id_produto');//CASO PRECISE INSERIR UM ID (AUTO INCREMENTO) AO REGISTRO
$importa->ImportaDados($ar_campos);
$importa->ShowResult();
?>