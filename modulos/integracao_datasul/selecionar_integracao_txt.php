<?php

/*
 * selecionar_integracao_txt.php
 * Autor: Alex
 * 08/11/2011 16:04:56
 */

session_start();
header("Content-Type: text/html; charset=ISO-8859-1");
include('../../conecta.php');
include('../../classes/class.debug.php');
include('../../functions.php');
$ArSelectImportacao = array();
$ArSelectImportacao[] = array('modulos/integracao_datasul/carga_tabela_completa_txt/is_canal_venda.php','Canal de Venda');
$ArSelectImportacao[] = array('modulos/integracao_datasul/carga_tabela_completa_txt/is_cond_pagto.php','Condi��es de Pagamento');
$ArSelectImportacao[] = array('modulos/integracao_datasul/carga_tabela_completa_txt/is_grupo_cliente.php','Grupo de Cliente');
$ArSelectImportacao[] = array('modulos/integracao_datasul/carga_tabela_completa_txt/is_micro_regiao.php','Micro Regi�o');
$ArSelectImportacao[] = array('modulos/integracao_datasul/carga_tabela_completa_txt/is_transportadora.php','Transportadora');
$ArSelectImportacao[] = array('modulos/integracao_datasul/carga_tabela_completa_txt/is_pessoa.php','Pessoas (Clientes)');
$ArSelectImportacao[] = array('modulos/integracao_datasul/carga_tabela_completa_txt/is_pessoa_endereco.php','Endere�o Cliente');
$ArSelectImportacao[] = array('modulos/integracao_datasul/carga_tabela_completa_txt/is_familia_comercial.php','Fam�lia Comercial');
$ArSelectImportacao[] = array('modulos/integracao_datasul/carga_tabela_completa_txt/is_produto.php','Produto');
$ArSelectImportacao[] = array('modulos/integracao_datasul/carga_tabela_completa_txt/is_tab_preco.php','Tabela de Pre�o');
$ArSelectImportacao[] = array('modulos/integracao_datasul/carga_tabela_completa_txt/is_tab_preco_valor.php','Pre�o Tabela de Pre�o/Produto');
?>
<style type="text/css">
legend{
    font-weight:bold;
    font-size:14px;
}
</style>
<fieldset>
    <legend>Integra��o Oasis</legend>
    <fieldset>
        <legend>Integra��o(Importa��o) Oasis-Datasul TXT</legend>
        Selecione a integra��o:<select name="edtarquivoimportacaotxt" id="edtarquivoimportacaotxt"><option value="" selected="selected"></option><?php foreach($ArSelectImportacao as $k => $v){?><option value="<?php echo $v[0];?>"><?php echo $v[1];?></option><?php } ?></select>
        <input type="button" value="Executar" onClick="javascript:abre_popup_integracao(document.getElementById('edtarquivoimportacaotxt').value);">
    </fieldset>    
</fieldset>