<?php
/*
 * calcula_desconto_venda_fixo.php
 * Autor: Alex
 * 13/05/2011 16:07:54
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */

include('includes.php');
$_POST = uB::UrlDecodePost($_POST);
$IdCampoDesconto = $_POST['id_campo_desconto'];
$SqlCampoDesconto = "SELECT * FROM is_param_campo_desconto_venda_fixo WHERE numreg = ".$IdCampoDesconto." ORDER BY numreg";
$QryCampoDesconto = query($SqlCampoDesconto);

$ArCampoDesconto = farray($QryCampoDesconto);
$ArrayIdCamposDesconto = array('1' => 'pct_desconto_tab_preco', '2' => 'pct_desconto_pessoa', '3' => 'pct_desconto_informado');

$Pessoa = new Pessoa($_POST['edtid_pessoa']);

$ArDadosVenda = array(
    'id_estabelecimento'      => $_POST['edtid_estabelecimento'],
    'id_repres_pri'           => $_POST['edtid_repres_pri'],
    'id_vendedor'             => $_POST['edtid_usuario_cad'],
    'id_tab_preco'            => $_POST['edtid_tab_preco'],
    'id_tp_venda'             => $_POST['edtid_tp_venda'],
    'id_dest_merc'            => $_POST['edtid_destino_mercadoria'],
    'id_moeda'                => $_POST['edtid_moeda'],
    'id_grupo_tab_preco'      => $_POST['edtid_grupo_tab_preco'],
    'med_dias'                => $_POST['edtmed_dias_cond_pagto'],
    'qtde'                    => $_POST['edtqtde'],
    'vl_tot_venda'            => $_POST['edtvl_total_liquido']
);
$ArDadosPessoa = array(
    'id_pessoa'              => $_POST['edtid_pessoa'],
    'id_pessoa_regiao'       => $_POST['edtid_regiao'],
    'cidade'                 => $_POST['edtcidade'],
    'uf'                     => $Pessoa->getDadoPessoa('uf'),
    'id_canal_venda'         => $_POST['edtid_canal_venda'],
    'id_grupo_cliente'       => $_POST['edtid_grupo_cliente'],
    'id_tp_pessoa'           => $_POST['edtid_tp_pessoa'],
    'sn_contribuinte_icms'   => $_POST['edtsn_contribuinte_icms']
);

$PoliticaComercialDescVendaCampoDescontoFixo = new PoliticaComercialDescVendaCampoDescontoFixo($IdCampoDesconto);
$PoliticaComercialDescVendaCampoDescontoFixo->setArDadosVenda($ArDadosVenda);
$PoliticaComercialDescVendaCampoDescontoFixo->setArDadosPessoa($ArDadosPessoa);
$PoliticaComercialDescVendaCampoDescontoFixo->ValidaPolitica(0);
$PctMaxCampoDesconto = $PoliticaComercialDescVendaCampoDescontoFixo->getPctMaxCampoDesconto();
$IdCampo = $ArrayIdCamposDesconto[$IdCampoDesconto];

if($PctMaxCampoDesconto > $ArCampoDesconto['pct_max_desc_fim']){
    $PctMaxCampoDesconto = $ArCampoDesconto['pct_max_desc_fim'];
}

$XML = '<'.'?xml version="1.0" encoding="ISO-8859-1"?'.'>'."\n";
$XML .= '<resposta>'."\n";
$XML .= "\t".'<status>1</status>'."\n";
$XML .= "\t".'<campo_desconto pct_desconto="'.number_format_min($PctMaxCampoDesconto,2,',','.').'" id_campo="edt'.$IdCampo.'" />'."\n";
$XML .= '</resposta>'."\n";
header("Content-Type: text/xml");
echo $XML;
?>