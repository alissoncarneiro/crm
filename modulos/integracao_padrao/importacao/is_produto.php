<?php
/*
 * is_produto.php
 * Autor: Alex
 * 02/02/2010 11:10
 * 
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
include_once('../../../conecta.php');
include_once('../../../functions.php');
include_once('../../../classes/class.impODBCProgressTable.php');


























exit;
$ArrayConf = parse_ini_file('../../../conecta_odbc_erp.ini',true);
$CnxODBC = ConectaODBCErpDatasul($ArrayConf,'item');
if(!$CnxODBC){
    echo 'Não foi possível estabelecer uma conexão com o ERP.';
    exit;
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
                    'class-fiscal'              => 'classificacao_fiscal'
                    );

$ArFixos = array();

$ArChaves = array('it-codigo');

/*
 * Definindo as tabelas que serão importadas
 */

$SqlCustom = "SELECT DISTINCT
                        t1.\"it-codigo\",
                        t1.\"fm-cod-com\",
                        t1.\"desc-item\",
                        t1.\"narrativa\",
                        t1.\"preco-ul-ent\",
                        t1.\"preco-repos\",
                        t1.\"preco-base\",
                        t1.\"un\",
                        t1.\"aliquota-ipi\",
                        t1.\"ge-codigo\",
                        t1.\"class-fiscal\"
                FROM
                    pub.\"item\" t1
                    INNER JOIN pub.\"item-uni-estab\" t2 ON t1.\"it-codigo\" = t2.\"it-codigo\"
                WHERE
                    t2.\"ind-item-fat\" = 1";

$Imp = new impODBCProgressTable();
$Imp->setCnxOdbc($CnxODBC);
$Imp->setTabelaOrigem('pub."item"');
$Imp->setTabelaDestino('is_produto');
$Imp->setSqlOdbcCustom($SqlCustom);
$Imp->setArDepara($ArDepara);
$Imp->setChaves($ArChaves);
$Imp->setArFixos($ArFixos);

$Imp->setCampoDepara('id_familia_comercial');
$Imp->setCampoDeparaTabelaCRM('is_familia_comercial');
$Imp->addCampoDeparaTabelaChaveCRM('id_familia_erp');

$Imp->setCampoDepara('id_unid_medida_padrao');
$Imp->setCampoDeparaTabelaCRM('is_unid_medida');
$Imp->addCampoDeparaTabelaChaveCRM('id_unid_medida_erp');

$Imp->Importa();
$Imp->mostraResultado();
odbc_close($CnxODBC);
?>