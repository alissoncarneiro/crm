<?php
/*
 * is_tab_financiamento.php
 * Autor: Alex
 * 09/05/2011 10:58:18
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
session_start();
include_once('../../../conecta.php');
include_once('../../../functions.php');
include_once('../../../classes/class.impODBCProgressTable.php');

$ArrayConf = parse_ini_file('../../../conecta_odbc_erp_datasul.ini',true);
$CnxODBC = ConectaODBCErpDatasul($ArrayConf,'tab-finan');
if(!$CnxODBC){
    echo 'Não foi possível estabelecer uma conexão com o ERP.';
    exit;
}

$ArDepara = array(
                'nr-tab-finan'      => 'id_tab_financiamento_erp',
                'dt-ini-val'        => 'dt_validade_ini',
                'dt-fim-val'        => 'dt_validade_fim',
            );
$ArChaves = array('nr-tab-finan');

$Imp = new impODBCProgressTable();
$Imp->setCnxOdbc($CnxODBC);
$Imp->setTabelaOrigem('pub."tab-finan"');
$Imp->setTabelaDestino('is_tab_financiamento');
$Imp->setArDepara($ArDepara);
$Imp->setChaves($ArChaves);
echo '<h2>Importando Tabela de Financiamento</h2>';
$Imp->Importa();
$Imp->mostraResultado();

/* ========================================================================== */

$ArDepara = array(
                'nr-tab-finan'  => 'id_tab_financiamento',
                'tab-dia-fin'   => 'dia_taxa',
                'tab-ind-fin'   => 'vl_taxa',
                'num-seq'       => 'id_taxa_financiamento'
            );
$ArChaves = array('nr-tab-finan','num-seq');

$Imp = new impODBCProgressTable();
$Imp->setCnxOdbc($CnxODBC);
$Imp->setTabelaOrigem('pub."tab-finan-indice"');
$Imp->setTabelaDestino('is_tab_financiamento_taxa');
$Imp->setArDepara($ArDepara);
$Imp->setChaves($ArChaves);

$Imp->setCampoDepara('id_tab_financiamento');
$Imp->setCampoDeparaTabelaCRM('is_tab_financiamento');
$Imp->addCampoDeparaTabelaChaveCRM('id_tab_financiamento_erp');

echo '<h2>Importando Taxas das Tabelas de Financiamento</h2>';
$Imp->Importa();
$Imp->mostraResultado();

odbc_close($CnxODBC);
?>