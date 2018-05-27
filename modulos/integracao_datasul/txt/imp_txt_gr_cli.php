<?php
header('Content-Type: text/html; charset=iso-8859-1');
include('../../../conecta.php');
include('../../../functions.php');
include('../../../funcoes.php');
include('../../../classes/class.txtimport.php');

$importa = new txtimport;

$importa->ProcuraArquivo('gr-cli');
$importa->TabelaName('is_grupo_cliente');

$ar_chave = array('id_grupo_cliente_erp');

$importa->SetArrayChaves($ar_chave);

$ar_campos = 	array('cod-gr-cli' => 'id_grupo_cliente_erp',
                      'descricao' => 'nome_grupo_cliente');
$ar_default =	array( );
$importa->SetArrayDefault($ar_default);
//$importa->NewFieldName('id_produto');//CASO PRECISE INSERIR UM ID (AUTO INCREMENTO) AO REGISTRO
$importa->ImportaDados($ar_campos);
$importa->ShowResult();
?>