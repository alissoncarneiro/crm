<?php
session_start();
header("Content-Type: text/html; charset=ISO-8859-1");
include('../../conecta.php');
include('../../classes/class.debug.php');
include('../../functions.php');
$ArSelectImportacao = array();
//$ArSelectImportacao[] = array('modulos/integracao_csv/is_canal_venda.php','Canal de Venda');
//$ArSelectImportacao[] = array('modulos/integracao_csv/is_cfop.php','CFOP');
//$ArSelectImportacao[] = array('modulos/integracao_csv/is_cfop_param.php','Parâmetros de CFOP');
//$ArSelectImportacao[] = array('modulos/integracao_csv/is_cond_pagto.php','Condições de Pagamento');
//$ArSelectImportacao[] = array('modulos/integracao_csv/is_estabelecimento.php','Estabelecimentos');
//$ArSelectImportacao[] = array('modulos/integracao_csv/is_familia.php','Família Materiais');
//$ArSelectImportacao[] = array('modulos/integracao_csv/is_familia_comercial.php','Família Comercial');
//$ArSelectImportacao[] = array('modulos/integracao_csv/is_unid_medida.php','Unidade de Medida');
$ArSelectImportacao[] = array('modulos/integracao_csv/is_representante.php','Representantes');
$ArSelectImportacao[] = array('modulos/integracao_csv/is_produto.php','Produtos');
//$ArSelectImportacao[] = array('modulos/integracao_csv/is_produto_estabelecimento.php','Produtos x Estabelecimentos');
//$ArSelectImportacao[] = array('modulos/integracao_csv/is_embalagem.php','Embalagens');
//$ArSelectImportacao[] = array('modulos/integracao_csv/is_produto_embalagem.php','Produtos x Embalagens');
//$ArSelectImportacao[] = array('modulos/integracao_csv/is_produto_cod_compl.php','Produtos x Cód. Compl.');
//$ArSelectImportacao[] = array('modulos/integracao_csv/is_produto_cod_compl_hist.php','Produtos x HIst. Cód. Compl.');
//$ArSelectImportacao[] = array('modulos/integracao_csv/is_grupo_cliente.php',' Grupos de Clientes');
$ArSelectImportacao[] = array('modulos/integracao_csv/is_pessoa.php','Pessoas (Clientes)');
$ArSelectImportacao[] = array('modulos/integracao_csv/is_contato.php','Contatos');
//$ArSelectImportacao[] = array('modulos/integracao_csv/is_pessoa_cfop.php','Parâmetro CFOP(Tipo Cliente)');
//$ArSelectImportacao[] = array('modulos/integracao_csv/is_pessoa_endereco.php','Endereços Clientes');
$ArSelectImportacao[] = array('modulos/integracao_csv/is_dm_notas_cab.php','Faturamento (Cabeçalho)');
$ArSelectImportacao[] = array('modulos/integracao_csv/is_dm_notas.php','Faturamento (Itens)');
$ArSelectImportacao[] = array('modulos/integracao_csv/is_dm_notas_atualiza.php','Faturamento (Atualização de Campos do CRM)');
//$ArSelectImportacao[] = array('modulos/integracao_csv/is_produto_cfop.php','Parâmetro CFOP(Tipo de Produto)');
//$ArSelectImportacao[] = array('modulos/integracao_csv/is_produto_pessoa.php','Cód Produtos por Cliente');
//$ArSelectImportacao[] = array('modulos/integracao_csv/is_moeda.php','Moedas');
//$ArSelectImportacao[] = array('modulos/integracao_csv/is_tab_preco.php','Tabelas de Preços');
//$ArSelectImportacao[] = array('modulos/integracao_csv/is_tab_preco_valor.php','Preços das Tabelas');
//$ArSelectImportacao[] = array('modulos/integracao_csv/is_cotacao.php','Cotações');
//$ArSelectImportacao[] = array('modulos/integracao_csv/is_titulo.php','Títulos');
//$ArSelectImportacao[] = array('modulos/integracao_csv/is_transportadora.php','Transportadora');
//$ArSelectImportacao[] = array('modulos/integracao_csv/is_estados_uf.php','Unidade Federação');
//$ArSelectImportacao[] = array('modulos/integracao_csv/is_produto_uf.php','Produtos UF (ST)');
//$ArSelectImportacao[] = array('modulos/integracao_csv/is_icms_uf_excecoes.php','ICMS Estados Exceções');
//$ArSelectImportacao[] = array('modulos/integracao_csv/is_icms_produto_diferenciado','ICMS Produto Diferênciado');

$ArSelectExportacao = array();
//$ArSelectExportacao[] = array('modulos/integracao_csv/interface_cliente_exp_csv.php','Clientes');
//$ArSelectExportacao[] = array('modulos/integracao_csv/interface_pedido_exp_csv.php','Pedidos');
?>
<style type="text/css">
legend{
    font-weight:bold;
    font-size:14px;
}
</style>
<fieldset>
    <legend>Integração Oasis CSV</legend>
    <fieldset>
        <legend>Integração(Importação) Oasis-CSV Layout Padrão</legend>
        Selecione a integração:<select name="edtarquivoimportacaocsv" id="edtarquivoimportacaocsv"><option value="" selected="selected"></option><?php foreach($ArSelectImportacao as $k => $v){?><option value="<?php echo $v[0];?>"><?php echo $v[1];?></option><?php } ?></select>
        <input type="button" value="Executar" onClick="javascript:abre_popup_integracao(document.getElementById('edtarquivoimportacaocsv').value);">
    </fieldset>
    <fieldset>
        <legend>Integração(Exportação) Oasis-CSV Layout Padrão</legend>
        Selecione a integração:<select name="edtarquivoexportacaocsv" id="edtarquivoexportacaocsv"><option value="" selected="selected"></option><?php foreach($ArSelectExportacao as $k => $v){?><option value="<?php echo $v[0];?>"><?php echo $v[1];?></option><?php } ?></select>
        <input type="button" value="Executar" onClick="javascript:abre_popup_integracao(document.getElementById('edtarquivoexportacaocsv').value);">
    </fieldset>
</fieldset>