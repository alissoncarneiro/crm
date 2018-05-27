<?php
header('Content-Type: text/html; charset=iso-8859-1');
include('../../../conecta.php');
include('../../../functions.php');
include('../../../funcoes.php');
include('../../../classes/class.txtimport.php');
$importa = new txtimport;

$importa->ProcuraArquivo('transporte');
$importa->TabelaName('is_transportadora');

$ar_chave = array('id_transportadora_erp');

$importa->SetArrayChaves($ar_chave);

$ar_campos = array(
                    'cod-transp' => 'id_transportadora_erp',
                    'nome' 	 => 'nome_transportadora',
                    'nome-abrev' => 'nome_abrev_transportadora'
            );
$ar_default =	array( );
$importa->SetArrayDefault($ar_default);
//$importa->NewFieldName('id_pessoa');//CASO PRECISE INSERIR UM ID (AUTO INCREMENTO) AO REGISTRO
$importa->ImportaDados($ar_campos);
$importa->ShowResult();
?>