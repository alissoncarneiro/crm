<?php
header('Content-Type: text/html; charset=iso-8859-1');
include('../../../conecta.php');
include('../../../functions.php');
include('../../../funcoes.php');
include('../../../classes/class.txtimport.php');
$importa = new txtimport;

$importa->ProcuraArquivo('ped-venda');
$importa->TabelaName('is_pedidos');

$ar_chave = array('id_pedido');

$importa->SetArrayChaves($ar_chave);

$importa->tratamento_float = array('vl_bruto', 'vl_liquido');

$importa->trata_data = array('dt_pedido', 'dt_entrega');

$importa->troca_valor = array('id_pessoa' => 'SELECT id_pessoa FROM is_pessoa WHERE id_pessoa_erp = \'id_pessoa\'');
//$importa->tratamento_especial = array('razao_social_nome', 'endereco', 'nome_abreviado', 'nome_pessoa_contato');

$ar_campos = array(
		'nr-pedcli' 	=> 'id_pedido',
		'cod-emitente' 	=> 'id_pessoa',
		'no-ab-reppri' 	=> 'id_representante',
		'user-impl' 	=> 'id_vendedor',
		'nr-tabpre' 	=> 'id_tab_preco',
		'dt-emissao' 	=> 'dt_pedido',
		'dt-entrega' 	=> 'dt_entrega',
		'cod-cond-pag' 	=> 'id_cond_pagto',
		'vl-tot-ped' 	=> 'vl_bruto',
		'vl-liq-ped' 	=> 'vl_liquido',
		'cod-sit-ped' 	=> 'id_sit_ped',
		'cond-espec' 	=> 'obs',
		'observacoes' 	=> 'obs_nf',
		'nat-operacao' 	=> 'natureza_operacao',
		'cod-estabel' 	=> 'id_estabelecimento',
		'cod-entrega'	=> 'cod_entrega',
		'cod-des-merc' 	=> 'id_dest_merc',
		'nome-transp' 	=> 'id_transportadora'
                   );

$ar_default = array(
                    'hr_cadastro'	=> date('H:i:s'),
                    'id_usuario_cad'    => 'IMPORT',
                    'dt_alteracao'	=> date('Y-m-d'),
                    'hr_alteracao'	=> date('H:i:s'),
                    'id_usuario_alt'    => 'IMPORT',
                    'tipo_pedido'	=> 'NOR',
                    'exportado_erp'	=> 'S',
                    'integrado_erp'	=> '1'
                    );

$importa->SetArrayDefault($ar_default);
//$importa->NewFieldName('id_pessoa');//CASO PRECISE INSERIR UM ID (AUTO INCREMENTO) AO REGISTRO
$importa->ImportaDados($ar_campos);
$importa->ShowResult();

$importa = NULL;

$importa = new txtimport;

$importa->ProcuraArquivo('ped-item');
$importa->TabelaName('is_pedidos_itens');

$ar_chave = array('id_pedido', 'id_item');

$importa->SetArrayChaves($ar_chave);

$importa->tratamento_float = array('vl_desc', 'vl_tabela');

$ar_campos = array(
		'nr-pedcli'	=> 'id_pedido',
		'it-codigo'	=> 'id_produto',
		'qt-pedida'	=> 'qtde',//total_unid
		'vl-preuni'	=> 'vl_tabela',
		'nr-sequencia'	=> 'id_item',
//		'tp_desc' 	=> 'tp_desc',
//		'des-pct-desconto-inform' => 'vl_desc',
		'nat-operacao' => 'natureza_operacao',
		'cod-refer'    => 'id_referencia',
		'qt-atendida'  => 'qtde_faturada',
		'cod-sit-item' => 'id_situacao'
		);

$ar_default = array(
                    'hr_cadastro'	=> date('H:i:s'),
                    'id_usuario_cad'	=> 'IMPORT',
                    'dt_alteracao'	=> date('Y-m-d'),
                    'hr_alteracao'	=> date('H:i:s'),
                    'id_usuario_alt'	=> 'IMPORT'
		);

$importa->SetArrayDefault($ar_default);
//$importa->NewFieldName('id_pessoa');//CASO PRECISE INSERIR UM ID (AUTO INCREMENTO) AO REGISTRO
$importa->ImportaDados($ar_campos);
$importa->ShowResult();
?>