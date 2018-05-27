<?php
header('Content-Type: text/html; charset=iso-8859-1');
include('../../../conecta.php');
include('../../../functions.php');
include('../../../funcoes.php');
include('../../../classes/class.txtimport.php');

$importa = new txtimport;

$importa->ProcuraArquivo('loc-entr');
$importa->TabelaName('is_pessoa_endereco');

$ar_chave = array('id_pessoa', 'id_endereco_erp');

$importa->SetArrayChaves($ar_chave);

$importa->Getnumreg = array('id_pessoa' => "select numreg from is_pessoa where fantasia_apelido = '!id_pessoa!'");

$ar_nega['id_pessoa']['vl_valido'] = 'NULL';
$ar_nega['id_pessoa']['importa_info'] = '1';
$importa->nega_importacao_inverso = $ar_nega;

$importa->tratamento_especial = array();

$ar_campos = array(
                    'cep'           => 'cep',
                    'endereco'      => 'endereco',
                    'bairro'        => 'bairro',
                    'cidade'        => 'cidade',
                    'estado'        => 'uf',
                    'pais'          => 'pais',
                    'cod-entrega'   => 'id_endereco_erp',
                    'nome-abrev'    => 'id_pessoa',
);
$ar_default = array(
	'id_tp_endereco' => '1',
	'id_logradouro'  => '1'
);
$importa->SetArrayDefault($ar_default);
//$importa->NewFieldName('id_pessoa'); //CASO PRECISE INSERIR UM ID (AUTO INCREMENTO) AO REGISTRO
$importa->ImportaDados($ar_campos);
$importa->ShowResult();
?>