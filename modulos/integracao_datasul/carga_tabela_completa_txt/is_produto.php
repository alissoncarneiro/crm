<?php

/*
 * is_produto.php
 * Autor: Alex
 * 11/11/2011 10:53:48
 */
session_start();
include_once('../../../conecta.php');
include_once('../../../functions.php');
include_once('../../../classes/class.impTXTProgressTable.php');

class impTXTProgressTableCustom_is_produto extends impTXTProgressTable{

    public function setValorCustom($ArDados){
        switch($ArDados['id_tp_origem_produto']){
            case '0': /* Nacional */
                $ArDados['id_tp_origem_produto'] = 1;
                break;
            case '1': /* Estrang. Import */
                $ArDados['id_tp_origem_produto'] = 2;
                break;
            case '2': /* Estrang. Merc. Interno */
                $ArDados['id_tp_origem_produto'] = 3;
            default:
                $ArDados['id_tp_origem_produto'] = 1;
                break;
        }
        $ArDados['sn_ativo'] = ($ArDados['id_obsoleto'] != '1')?'0':'1';
        return $ArDados;
    }
}

$ArDepara = array(
                'it-codigo'                 => 'id_produto_erp',
                'fm-cod-com'                => 'id_familia_comercial',
                'desc-item'                 => 'nome_produto',
                'narrativa'                 => 'nome_produto_detalhado',
                'preco-ul-ent'              => 'custo_ult_ent',
                'preco-repos'               => 'custo_repos',
                'preco-base'                => 'custo_base',
                'un'                        => 'id_unid_medida_padrao',
                'aliquota-ipi'              => 'pct_aliq_ipi',
                'ge-codigo'                 => 'id_grupo_estoque',
                'class-fiscal'              => 'classificacao_fiscal',
                'compr-fabric'              => 'id_forma_aquisicao',
                'peso-liquido'              => 'peso_liquido',
                'codigo-orig'               => 'id_tp_origem_produto',
                'cod-obsoleto'              => 'id_obsoleto'
                );
$ArChaves = array('it-codigo');

$ArTratamentoFixoFloat = array('custo_ult_ent','custo_repos','custo_base','pct_aliq_ipi');

$Imp = new impTXTProgressTableCustom_is_produto();
$Imp->setNomeArquivoLeitura('item');
$Imp->setTabelaDestino('is_produto');
$Imp->setArDepara($ArDepara);
$Imp->setChaves($ArChaves);
$Imp->setArTratamentoFixoFloat($ArTratamentoFixoFloat);

$Imp->setCampoDepara('id_familia_comercial');
$Imp->setCampoDeparaTabelaCRM('is_familia_comercial');
$Imp->addCampoDeparaTabelaChaveCRM('id_familia_erp');

$Imp->setCampoDepara('id_unid_medida_padrao');
$Imp->setCampoDeparaTabelaCRM('is_unid_medida');
$Imp->addCampoDeparaTabelaChaveCRM('id_unid_medida_erp');

$Imp->Importa();
$Imp->mostraResultado();
?>