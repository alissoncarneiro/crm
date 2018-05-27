<?php
/*
 * is_tab_preco_valor.php
 * Autor: Alex
 * 11/11/2011 11:14:38
 */
session_start();
include_once('../../../conecta.php');
include_once('../../../functions.php');
include_once('../../../classes/class.impTXTProgressTable.php');

class impTXTProgressTableCustom_is_tab_preco_valor extends impTXTProgressTable{
    public function setValorCustom($ArDados){
        $ArDados['sn_ativo'] = ($ArDados['sn_ativo'] == 1)?1:0;
        return $ArDados;
    }
}

$ArDepara = array(
                    'nr-tabpre'     => 'id_tab_preco',
                    'it-codigo'     => 'id_produto',
                    'preco-venda'   => 'vl_unitario',
                    'cod-unid-med'  => 'id_unid_medida',
                    'dt-inival'     => 'dt_validade_ini',
                    'situacao'      => 'sn_ativo'
                 );

$ArFixos = array();
$ArChaves = array('nr-tabpre','it-codigo','cod-unid-med','dt-inival','situacao');
$ArCamposObrigatorios = array('id_tab_preco','id_produto','id_unid_medida');
$ArTratamentoFixoData = array('dt_validade_ini');
$ArTratamentoFixoFloat = array('vl_unitario');

$Imp = new impTXTProgressTableCustom_is_tab_preco_valor();
$Imp->setNomeArquivoLeitura('preco-item');
$Imp->setTabelaDestino('is_tab_preco_valor');
$Imp->setArTratamentoFixoData($ArTratamentoFixoData);
$Imp->setArTratamentoFixoFloat($ArTratamentoFixoFloat);
$Imp->setArDepara($ArDepara);
$Imp->setChaves($ArChaves);
$Imp->setArFixos($ArFixos);
$Imp->setArCamposObrigatorios($ArCamposObrigatorios);

$Imp->setCampoDepara('id_tab_preco');
$Imp->setCampoDeparaTabelaCRM('is_tab_preco');
$Imp->addCampoDeparaTabelaChaveCRM('id_tab_preco_erp');

$Imp->setCampoDepara('id_produto');
$Imp->setCampoDeparaTabelaCRM('is_produto');
$Imp->addCampoDeparaTabelaChaveCRM('id_produto_erp');

$Imp->setCampoDepara('id_unid_medida');
$Imp->setCampoDeparaTabelaCRM('is_unid_medida');
$Imp->addCampoDeparaTabelaChaveCRM('id_unid_medida_erp');

$Imp->Importa();
$Imp->mostraResultado();
?>