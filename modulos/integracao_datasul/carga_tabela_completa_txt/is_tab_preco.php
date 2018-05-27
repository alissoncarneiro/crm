<?php
/*
 * is_tab_preco.php
 * Autor: Alex
 * 11/11/2011 11:12:50
 */
session_start();
include_once('../../../conecta.php');
include_once('../../../functions.php');
include_once('../../../classes/class.impTXTProgressTable.php');

class impTXTProgressTableCustom_is_tab_preco extends impTXTProgressTable{
    public function setValorCustom($ArDados){
        switch($ArDados['sn_ativa']){
            case '1':
                $ArDados['sn_ativa'] = 1;
                break;
            default:
                $ArDados['sn_ativa'] = 0;
                break;
        }
        return $ArDados;
    }
}

$ArDepara = array(
                    'nr-tabpre'     => 'id_tab_preco_erp',
                    'descricao'     => 'nome_tab_preco',
                    'dt-inival'     => 'dt_vigencia_ini',
                    'dt-fimval'     => 'dt_vigencia_fim',
                    'mo-codigo'     => 'id_moeda',
                    'situacao'      => 'sn_ativa'
                );

$ArFixos = array();

$ArChaves = array('nr-tabpre');

$ArTratamentoFixoData = array('dt_vigencia_ini','dt_vigencia_fim');

$Imp = new impTXTProgressTableCustom_is_tab_preco();
$Imp->setNomeArquivoLeitura('tb-preco');
$Imp->setArTratamentoFixoData($ArTratamentoFixoData);
$Imp->setTabelaDestino('is_tab_preco');
$Imp->setArDepara($ArDepara);
$Imp->setChaves($ArChaves);
$Imp->setArFixos($ArFixos);

$Imp->setCampoDepara('id_moeda');
$Imp->setCampoDeparaTabelaCRM('is_moeda');
$Imp->addCampoDeparaTabelaChaveCRM('id_moeda_erp');

$Imp->Importa();
$Imp->mostraResultado();
?>