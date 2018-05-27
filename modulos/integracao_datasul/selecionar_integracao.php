<?php
/*
 * ClienteExpTxt.php
 * Autor: Alex
 * 03/12/2010 08:32
 * -
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */

session_start();
header("Content-Type: text/html; charset=ISO-8859-1");
include('../../conecta.php');
include('../../classes/class.debug.php');
include('../../functions.php');
$ArSelectImportacao = array();
$ArSelectImportacao[] = array('modulos/integracao_datasul/carga_tabela_completa_odbc/is_canal_venda.php','Canal de Venda');
$ArSelectImportacao[] = array('modulos/integracao_datasul/carga_tabela_completa_odbc/is_cfop.php','CFOP');
$ArSelectImportacao[] = array('modulos/integracao_datasul/carga_tabela_completa_odbc/is_cfop_param.php','Parâmetros de CFOP');
$ArSelectImportacao[] = array('modulos/integracao_datasul/carga_tabela_completa_odbc/is_regiao_micro_regiao.php','Regiões e Micro Regiões');
$ArSelectImportacao[] = array('modulos/integracao_datasul/carga_tabela_completa_odbc/is_cidade_zona_franca.php','Cidades Zona Franca');
$ArSelectImportacao[] = array('modulos/integracao_datasul/carga_tabela_completa_odbc/is_tab_financiamento.php','Tabela de Financiamento');
$ArSelectImportacao[] = array('modulos/integracao_datasul/carga_tabela_completa_odbc/is_cond_pagto.php','Condições de Pagamento');
$ArSelectImportacao[] = array('modulos/integracao_datasul/carga_tabela_completa_odbc/is_estabelecimento.php','Estabelecimentos');
$ArSelectImportacao[] = array('modulos/integracao_datasul/carga_tabela_completa_odbc/is_familia.php','Família Materiais');
$ArSelectImportacao[] = array('modulos/integracao_datasul/carga_tabela_completa_odbc/is_familia_comercial.php','Família Comercial');
$ArSelectImportacao[] = array('modulos/integracao_datasul/carga_tabela_completa_odbc/is_unid_medida.php','Unidade de Medida');
$ArSelectImportacao[] = array('modulos/integracao_datasul/carga_tabela_completa_odbc/is_produto.php','Produtos');
$ArSelectImportacao[] = array('modulos/integracao_datasul/carga_tabela_completa_odbc/is_produto_estabelecimento.php','Produtos x Estabelecimentos');
$ArSelectImportacao[] = array('modulos/integracao_datasul/carga_tabela_completa_odbc/is_embalagem.php','Embalagens');
$ArSelectImportacao[] = array('modulos/integracao_datasul/carga_tabela_completa_odbc/is_produto_embalagem.php','Produtos x Embalagens');
$ArSelectImportacao[] = array('modulos/integracao_datasul/carga_tabela_completa_odbc/is_produto_fator_conversao.php','Produtos x Un. Med. x Fator Conversão');
$ArSelectImportacao[] = array('modulos/integracao_datasul/carga_tabela_completa_odbc/is_produto_cod_compl.php','Produtos x Cód. Compl.');
$ArSelectImportacao[] = array('modulos/integracao_datasul/carga_tabela_completa_odbc/is_produto_cod_compl_hist.php','Produtos x HIst. Cód. Compl.');
$ArSelectImportacao[] = array('modulos/integracao_datasul/carga_tabela_completa_odbc/is_grupo_cliente.php',' Grupos de Clientes');
$ArSelectImportacao[] = array('modulos/integracao_datasul/carga_tabela_completa_odbc/is_pessoa.php','Pessoas (Clientes)');
$ArSelectImportacao[] = array('modulos/integracao_datasul/carga_tabela_completa_odbc/is_pessoa_cfop.php','Parâmetro CFOP(Tipo Cliente)');
$ArSelectImportacao[] = array('modulos/integracao_datasul/carga_tabela_completa_odbc/is_pessoa_endereco.php','Endereços Clientes');
$ArSelectImportacao[] = array('modulos/integracao_datasul/carga_tabela_completa_odbc/is_produto_cfop.php','Parâmetro CFOP(Tipo de Produto)');
$ArSelectImportacao[] = array('modulos/integracao_datasul/carga_tabela_completa_odbc/is_produto_pessoa.php','Cód Produtos por Cliente');
$ArSelectImportacao[] = array('modulos/integracao_datasul/carga_tabela_completa_odbc/is_moeda.php','Moedas');
$ArSelectImportacao[] = array('modulos/integracao_datasul/carga_tabela_completa_odbc/is_tab_preco.php','Tabelas de Preços');
$ArSelectImportacao[] = array('modulos/integracao_datasul/carga_tabela_completa_odbc/is_tab_preco_valor.php','Preços das Tabelas');
$ArSelectImportacao[] = array('modulos/integracao_datasul/carga_tabela_completa_odbc/is_cotacao.php','Cotações');
$ArSelectImportacao[] = array('modulos/integracao_datasul/carga_tabela_completa_odbc/is_titulo.php','Títulos');
$ArSelectImportacao[] = array('modulos/integracao_datasul/carga_tabela_completa_odbc/is_transportadora.php','Transportadora');
$ArSelectImportacao[] = array('modulos/integracao_datasul/carga_tabela_completa_odbc/is_estados_uf.php','Unidade Federação');
$ArSelectImportacao[] = array('modulos/integracao_datasul/carga_tabela_completa_odbc/is_produto_uf.php','Produtos UF (ST)');
$ArSelectImportacao[] = array('modulos/integracao_datasul/carga_tabela_completa_odbc/is_icms_uf_excecoes.php','ICMS Estados Exceções');
$ArSelectImportacao[] = array('modulos/integracao_datasul/carga_tabela_completa_odbc/is_icms_produto_diferenciado.php','ICMS Produto Diferênciado');
$ArSelectImportacao[] = array('modulos/integracao_datasul/carga_tabela_completa_odbc/is_produto_estrutura.php','Estrutura de Produtos');

$ArSelectExportacao = array();
$ArSelectExportacao[] = array('modulos/integracao_datasul/interface_cliente_exp.php','Clientes');
$ArSelectExportacao[] = array('modulos/integracao_datasul/interface_pedido_exp.php','Pedidos');
?>
<style type="text/css">
legend{
    font-weight:bold;
    font-size:14px;
}
</style>
<fieldset>
    <legend>Integração Oasis</legend>
    <fieldset>
        <legend>Integração(Importação) Oasis-Datasul ODBC</legend>
        Selecione a integração:<select name="edtarquivoimportacaoodbc" id="edtarquivoimportacaoodbc"><option value="" selected="selected"></option><?php foreach($ArSelectImportacao as $k => $v){?><option value="<?php echo $v[0];?>"><?php echo $v[1];?></option><?php } ?></select>
        <input type="button" value="Executar" onClick="javascript:abre_popup_integracao(document.getElementById('edtarquivoimportacaoodbc').value);">
    </fieldset>    
</fieldset>