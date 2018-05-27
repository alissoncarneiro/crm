<?php

/*
 * is_cfop_param.php
 * Autor: Alex
 * 29/11/2010 10:45
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
$CnxODBC = ConectaODBCErpDatasul($ArrayConf,'es-cfop-param');
if(!$CnxODBC){
    echo 'Não foi possível estabelecer uma conexão com o ERP.';
    exit;
}

class impODBCProgressTableCustom_is_cfop_param extends impODBCProgressTable{
    public function setValorCustom($ArDados){
        /*
         * Tipo de Pessoa
         */
        if($ArDados['id_tp_pessoa'] == '5'){
            $ArDados['id_tp_pessoa'] = NULL;
        }
        /*
         * Contribuinte do ICMS
         */
        if($ArDados['sn_contribuinte_icms'] == '1'){
            $ArDados['sn_contribuinte_icms'] = NULL;
        } elseif($ArDados['sn_contribuinte_icms'] == '2'){
            $ArDados['sn_contribuinte_icms'] = '0';
        } elseif($ArDados['sn_contribuinte_icms'] == '3'){
            $ArDados['sn_contribuinte_icms'] = '1';
        }

        /*
         * Destino da mercadoria
         */
        if($ArDados['id_pedido_dest_merc'] == '3'){
            $ArDados['id_pedido_dest_merc'] = NULL;
        }
        return $ArDados;
    }
}


$ArDepara = array(
    'cod-cfop-oper'     => 'id_cfop_oper',
    'cod-estabel'       => 'id_pedido_estabelecimento',
    'estado'            => 'pessoa_uf',
    'cidade'            => 'pessoa_cidade',
    'cod-gr-cli'        => 'id_pessoa_grupo_cliente',
    'ge-codigo'         => 'id_produto_grupo_estoque',
    'fm-cod-com'        => 'id_produto_familia_comercial',
    'fm-codigo'         => 'id_produto_familia',
    'it-codigo'         => 'id_produto',
    'nat-operacao-de'   => 'cfop_estadual',
    'nat-operacao-fe'   => 'cfop_interestadual',
    'nat-operacao-in'   => 'cfop_internacional',
    'nr-pontos'         => 'pontos',
    'ind-contrib-icms'  => 'sn_contribuinte_icms',
    'ind-natureza'      => 'id_tp_pessoa',
    'ind-dest-mercad'   => 'id_pedido_dest_merc',
    'cod-canal-venda'   => 'id_pessoa_canal_venda',
    'tp-pedido'         => 'id_pedido_tp_venda',
    'cod-tp-cliente'    => 'id_tp_cliente',
    'cod-tp-item'       => 'id_tp_item'
    );

$ArChaves = array_keys($ArDepara);

$ArFixos = array(
    'sn_ativo'              => '1',
    'dthr_validade_ini'     => '2010-01-01',
    'dthr_validade_fim'     => '2099-01-01'
);

$Imp = new impODBCProgressTableCustom_is_cfop_param();
$Imp->setCnxOdbc($CnxODBC);
$Imp->setTabelaOrigem('pub."es-cfop-param"');
$Imp->setTabelaDestino('is_param_cfop');
$Imp->setArDepara($ArDepara);
$Imp->setArFixos($ArFixos);
$Imp->setChaves($ArChaves);

$Imp->setCampoDepara('id_pedido_tp_venda');
$Imp->setCampoDeparaTabelaCRM('is_tp_venda');
$Imp->addCampoDeparaTabelaChaveCRM('id_tp_venda_erp');

$Imp->setCampoDepara('id_pedido_estabelecimento');
$Imp->setCampoDeparaTabelaCRM('is_estabelecimento');
$Imp->addCampoDeparaTabelaChaveCRM('id_estabelecimento_erp');

$Imp->setCampoDepara('id_pessoa_grupo_cliente');
$Imp->setCampoDeparaTabelaCRM('is_grupo_cliente');
$Imp->addCampoDeparaTabelaChaveCRM('id_grupo_cliente_erp');

$Imp->setCampoDepara('id_produto');
$Imp->setCampoDeparaTabelaCRM('is_produto');
$Imp->addCampoDeparaTabelaChaveCRM('id_produto_erp');

$Imp->setCampoDepara('cfop_estadual');
$Imp->setCampoDeparaTabelaCRM('is_cfop');
$Imp->addCampoDeparaTabelaChaveCRM('id_cfop_erp');

$Imp->setCampoDepara('cfop_interestadual');
$Imp->setCampoDeparaTabelaCRM('is_cfop');
$Imp->addCampoDeparaTabelaChaveCRM('id_cfop_erp');

$Imp->setCampoDepara('cfop_internacional');
$Imp->setCampoDeparaTabelaCRM('is_cfop');
$Imp->addCampoDeparaTabelaChaveCRM('id_cfop_erp');


$Imp->Importa();
$Imp->mostraResultado();
odbc_close($CnxODBC);
?>