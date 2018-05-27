<?php

/*
 * is_tab_preco.php
 * Autor: Alex
 * 28/11/2010 09:04
 * -
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
session_start();
include_once('../../../conecta.php');
include_once('../../../functions.php');
include_once('../../../classes/class.impODBCProgressTable.php');

$ArrayConf = parse_ini_file('../../../conecta_odbc_erp_datasul.ini',true);
$CnxODBC = ConectaODBCErpDatasul($ArrayConf,'tb-preco');
if(!$CnxODBC){
    echo 'Não foi possível estabelecer uma conexão com o ERP.';
    exit;
}
class impODBCProgressTableCustom_is_tab_preco extends impODBCProgressTable{
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

$Imp = new impODBCProgressTableCustom_is_tab_preco();
$Imp->setCnxOdbc($CnxODBC);
$Imp->setTabelaOrigem('pub."tb-preco"');
$Imp->setTabelaDestino('is_tab_preco');
$Imp->setArDepara($ArDepara);
$Imp->setChaves($ArChaves);
$Imp->setArFixos($ArFixos);

$Imp->setCampoDepara('id_moeda');
$Imp->setCampoDeparaTabelaCRM('is_moeda');
$Imp->addCampoDeparaTabelaChaveCRM('id_moeda_erp');

$Imp->Importa();
$Imp->mostraResultado();
odbc_close($CnxODBC);
?>