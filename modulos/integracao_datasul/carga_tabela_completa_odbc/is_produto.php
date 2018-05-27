<?php

/*
 * is_produto.php
 * Autor: Alex
 * 02/12/2010 10:45
 * -
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
session_start();
set_time_limit(600); /* 10 minutos */
include_once('../../../conecta.php');
include_once('../../../functions.php');
include_once('../../../classes/class.impODBCProgressTable.php');

$ArrayConf = parse_ini_file('../../../conecta_odbc_erp_datasul.ini',true);
$CnxODBC = ConectaODBCErpDatasul($ArrayConf,'item');
if(!$CnxODBC){
    echo 'Não foi possível estabelecer uma conexão com o ERP.';
    exit;
}

class impODBCProgressTableCustom_is_produto extends impODBCProgressTable{

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
                        t1.\"class-fiscal\",
                        t1.\"compr-fabric\",
                        t1.\"peso-liquido\",
                        t1.\"codigo-orig\",
                        t1.\"cod-obsoleto\"
                FROM
                    pub.\"item\" t1
                    INNER JOIN pub.\"item-uni-estab\" t2 ON t1.\"it-codigo\" = t2.\"it-codigo\"
                WHERE
                    t2.\"ind-item-fat\" = 1";

$Imp = new impODBCProgressTableCustom_is_produto();
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