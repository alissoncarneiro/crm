<?php

/*
 * is_pessoa.php
 * Autor: Alex
 * 08/02/2011 14:10:00
 * -
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
set_time_limit(600);
if(!defined('CARGAODBCPADRAO')){
    define('CARGAODBCPADRAO',true);
}
include_once('../../conecta.php');
include_once('../../functions.php');
include_once('../../classes/class.impODBCTable.php');

$CnxODBC = odbc_connect(IntPadODBCServidor, IntPadODBCUsuario, IntPadODBCSenha);
if(!$CnxODBC){
    echo 'Não foi possível estabelecer uma conexão com o ERP.';
    exit;
}

class impODBCTableCustom_is_pessoa extends impODBCTable{
    public function setValorCustom($ArDados){
        switch($ArDados['id_tp_pessoa']){
            case '1':
                $ArDados['id_tp_pessoa'] = 2;
                break;
            case '2':
                 $ArDados['id_tp_pessoa'] = 1;
                break;
            default:
                $ArDados['id_tp_pessoa'] = NULL;
                break;
        }

        $ArDados['sn_contribuinte_icms']            = ($ArDados['sn_contribuinte_icms'] == 1)?1:0;
        $ArDados['sn_aceita_faturamento_parcial']   = ($ArDados['sn_aceita_faturamento_parcial'] == 1)?1:0;

        return $ArDados;
    }

    public function setValorCustomInsert($ArDados){
        $ArDados['dt_cadastro']           = date("Y-m-d");
        $ArDados['sn_inadimplente']       = 0;
        $ArDados['sn_cliente']            = 1;
        $ArDados['sn_prospect']           = 0;
        $ArDados['sn_suspect']            = 0;
        $ArDados['sn_concorrente']        = 0;
        $ArDados['sn_parceiro']           = 0;
        $ArDados['sn_fornecedor']         = 0;
        $ArDados['sn_representante']      = 0;
        $ArDados['sn_contato']            = 0;
        $ArDados['sn_importado_erp']      = 1;
        $ArDados['sn_exportado_erp']      = 1;

        return $ArDados;
    }
}


$ArDepara = array(
    'tipo_pessoa'                       => 'id_tp_pessoa',
    'id_pessoa_erp'                     => 'id_pessoa_erp',
    'razao_social_nome'                 => 'razao_social_nome',
    'fantasia_apelido'                  => 'fantasia_apelido',
    'cnpj_cpf'                          => 'cnpj_cpf',
    'ie_rg'                             => 'ie_rg',
    'email'                             => 'email',

    'tel1'                              => 'tel1',
    'tel2'                              => 'tel2',
    'fax'                               => 'fax',

    'endereco'                          => 'endereco',
    'numero'                            => 'numero',
    'bairro'                            => 'bairro',
    'cidade'                            => 'cidade',
    'uf'                                => 'uf',
    'pais'                              => 'pais',
    'cep'                               => 'cep',

    'id_grupo_cliente_erp'              => 'id_grupo_cliente',
    'dt_limite_credito_validade'        => 'dt_limite_credito_validade',
    'vl_limite_credito'                 => 'vl_limite_credito',

    'obs'                               => 'obs'
);

$ArChaves = array('id_pessoa_erp');

$ArFixos = array(
    'dthr_importado_erp'        => date("Y-m-d"),
    'sn_cliente'                => 1,
    'sn_importado_erp'          => 1,
    'sn_grupo_inadimplente'     => 1
);

$Imp = new impODBCTableCustom_is_pessoa(IntPadODBCTipoBD);
$Imp->setCnxOdbc($CnxODBC);
$Imp->setTabelaOrigem('vw_is_int_pessoas');
$Imp->setTabelaDestino('is_pessoa');
$Imp->setArDepara($ArDepara);
$Imp->setArFixos($ArFixos);
$Imp->setChaves($ArChaves);

$Imp->setCampoDepara('id_grupo_cliente');
$Imp->setCampoDeparaTabelaCRM('is_grupo_cliente');
$Imp->addCampoDeparaTabelaChaveCRM('id_grupo_cliente_erp');

$Imp->Importa();
$Imp->mostraResultado();
odbc_close($CnxODBC);
?>