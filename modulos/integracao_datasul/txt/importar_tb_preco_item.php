<?php
header('Content-Type: text/html; charset=iso-8859-1');
include('../../../conecta.php');
include('../../../functions.php');
include('../../../funcoes.php');
include('../../../classes/class.txtimport.php');
$importa = new txtimport;

$importa->ProcuraArquivo('preco-item');
$importa->TabelaName('is_tab_preco_valor');

$ar_chave = array('id_produto', 'id_tab_preco');

$importa->SetArrayChaves($ar_chave);

$importa->tratamento_float = array('vl_unitario');

$importa->Getnumreg = array(
                            'id_produto'    =>'SELECT numreg FROM is_produto WHERE id_produto_erp = \'!id_produto!\'',
                            'id_tab_preco'  =>'SELECT numreg FROM is_tab_preco WHERE id_tab_preco_erp = \'!id_tab_preco!\''
                           );

$ar_campos = array('it-codigo'	 => 'id_produto',
                   'nr-tabpre'	 => 'id_tab_preco',
		   'preco-venda' => 'vl_unitario',
                    );
$ar_default = array( );
$importa->SetArrayDefault($ar_default);
//$importa->NewFieldName('id_produto');//CASO PRECISE INSERIR UM ID (AUTO INCREMENTO) AO REGISTRO
$importa->ImportaDados($ar_campos);
$importa->ShowResult();
?>