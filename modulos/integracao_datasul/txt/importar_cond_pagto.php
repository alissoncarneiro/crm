<?php
header('Content-Type: text/html; charset=iso-8859-1');
include('../../../conecta.php');
include('../../../functions.php');
include('../../../funcoes.php');
include('../../../classes/class.txtimport.php');
$importa = new txtimport;

$importa->ProcuraArquivo('cond-pagto');
$importa->TabelaName('is_cond_pagto');

$ar_chave = array('id_cond_pagto_erp');

$importa->SetArrayChaves($ar_chave);

$importa->tratamento_float = array('media_dias');

$ar_campos = 	array('cod-cond-pag'         => 'id_cond_pagto_erp',
                      'descricao'            => 'nome_cond_pagto',
                      'qtd-dias-prazo-medio' => 'media_dias');
$ar_default =	array( );
$importa->SetArrayDefault($ar_default);
//$importa->NewFieldName('id_produto');//CASO PRECISE INSERIR UM ID (AUTO INCREMENTO) AO REGISTRO
$importa->ImportaDados($ar_campos);
$importa->ShowResult();
?>