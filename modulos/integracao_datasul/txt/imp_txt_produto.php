<?php
header('Content-Type: text/html; charset=iso-8859-1');
include('../../../conecta.php');
include('../../../functions.php');
include('../../../funcoes.php');
include('../../../classes/class.txtimport.php');

$importa = new txtimport;

$importa->ProcuraArquivo('item');
$importa->TabelaName('is_produto');

$ar_chave = array('id_produto_erp');

$importa->SetArrayChaves($ar_chave);

$importa->tratamento_float = array('custo_ult_ent', 'custo_repos', 'custo_base', 'pct_aliq_ipi');

$importa->Getnumreg = array(
'id_familia' 	=>'SELECT numreg FROM is_familia_comercial WHERE id_familia_erp = \'!id_familia!\''
);

$ar_nega['item_fat']['vl_valido'] = 'yes';
$ar_nega['item_fat']['importa_info'] = '0';
$importa->nega_importacao = $ar_nega;

$importa->tratamento_especial = array('nome_produto');

$ar_campos = array(
		'it-codigo' 		=> 'id_produto_erp',
		'fm-cod-com' 		=> 'id_familia',
		'desc-item' 		=> 'nome_produto',
		'narrativa'		=> 'nome_produto_detalhado',
		//'un'		 	=> 'id_uni_med',
		'preco-ul-ent' 		=> 'custo_ult_ent',
		'preco-repos' 		=> 'custo_repos',
		'preco-base' 		=> 'custo_base',
		//'cod-obsoleto' 	=> 'cod_ativo',
		//'class-fiscal'	=> 'id_classificacao_fiscal',
                'aliquota-ipi'          => 'pct_aliq_ipi',
		'ind-item-fat'          => 'item_fat'
		);

$ar_default =	array( );

$importa->SetArrayDefault($ar_default);
//$importa->NewFieldName('id_produto');//CASO PRECISE INSERIR UM ID (AUTO INCREMENTO) AO REGISTRO
$importa->ImportaDados($ar_campos);
$importa->ShowResult();
$importa = NULL;

$importa = new txtimport;

$importa->ProcuraArquivo('saldo-estoq');
$importa->TabelaName('is_produto');

$ar_chave = array('id_produto');

$importa->SetArrayChaves($ar_chave);

$importa->tratamento_float = array('qtd_estoque_atual');

$ar_nega['cod_estabelecimento']['vl_valido'] = '101';
$ar_nega['cod_estabelecimento']['importa_info'] = '0';

$importa->nega_importacao = $ar_nega;

$ar_campos = array(
                    'it-codigo'         => 'id_produto',
                    'qtidade-atu'       => 'qtd_estoque_atual',
                    'cod-estabel'       => 'cod_estabelecimento'
                );
$ar_default =	array(	);
$importa->SetArrayDefault($ar_default);
//$importa->NewFieldName('id_produto');//CASO PRECISE INSERIR UM ID (AUTO INCREMENTO) AO REGISTRO
$importa->ImportaDados($ar_campos);
$importa->ShowResult();
?>