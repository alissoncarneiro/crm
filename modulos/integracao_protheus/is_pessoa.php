<?php

set_time_limit(600); /* 10 minutos */
include_once('../../conecta.php');
include_once('../../conecta_odbc_protheus.php');
include_once('../../functions.php');
include_once('../../classes/class.impODBCProtheus.php');

$CnxODBC = odbc_connect($AliasProtheus, $UsuarioProtheus, $SenhaProtheus);
if(!$CnxODBC){
    echo 'Não foi possível estabelecer uma conexão com o ERP Protheus.';
    exit;
}

class impODBCProtheusTableCustom_is_pessoa extends impODBCProtheus{
    public function setValorCustom($ArDados){
        /* Tratamento para estrangeiros */
        if($ArDados['a1_est'] == 'EX'){
            unset($ArDados['id_tp_pessoa']);
            $ArDados['sn_estrangeiro'] = 1;
        }
        else{
            /* Tratamento para definir o tipo de pessoa */
            switch($ArDados['id_tp_pessoa']){
                case 'F':
                    $ArDados['id_tp_pessoa'] = 2;
                    break;
                case 'J':
                     $ArDados['id_tp_pessoa'] = 1;
                    break;
                default:
                    $ArDados['id_tp_pessoa'] = NULL;
                    break;
            }
        }

        $ArDados['sn_contribuinte_icms']            = ($ArDados['sn_contribuinte_icms'] == 1)?1:0;
        $ArDados['sn_aceita_faturamento_parcial']   = ($ArDados['sn_aceita_faturamento_parcial'] == 1)?1:0;

        return $ArDados;
    }

    public function setValorCustomInsert($ArDados){
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

        /* Tratamento para estrangeiros */
        if($ArDados['id_tp_pessoa'] == '3'){
            $ArDados['id_tp_pessoa'] = GetParam('CLI_IMP_ERP_TIPO_PESSOA');
            $ArDados['sn_estrangeiro'] = 1;
        }
        return $ArDados;
    }
}
$ArDepara = array(
    'a1_dtnasc'      => 'dt_cadastro',
    'a1_pessoa'          => 'id_tp_pessoa',
    'a1_cod'      => 'id_pessoa_erp',
    'a1_nome'         => 'razao_social_nome',
    'a1_nreduz'        => 'fantasia_apelido',
    'a1_cgc'               => 'cnpj_cpf',
    'a1_inscr'      => 'ie_rg',
    'a1_email'            => 'email',
    'a1_end'          => 'endereco',
    'a1_bairro'            => 'bairro',
    'a1_mun'            => 'cidade',
    'a1_est'            => 'uf',
    'a1_paisdes'              => 'pais',
    'a1_cep'               => 'cep',
    'a1_vend'           => 'id_representante_padrao',
    'a1_vend'      => 'id_vendedor_padrao',
    'a1_grpven'        => 'id_grupo_cliente',
    'a1_tabela'         => 'id_tab_preco_padrao',
    'a1_transp'        => 'id_transportadora_padrao',
    'a1_cond'      => 'id_cond_pagto_padrao',
    'a1_hpage'         => 'site',
    'a1_tel'         => 'tel1',
    'a1_fax'           => 'fax',
    'a1_dtnasc'      => 'dt_virou_cliente',
    'a1_lc'       => 'vl_limite_credito',
    'a1_suframa'       => 'cod_suframa',
    'a1_obs'       => 'obs'
    );

    //'cod-canal-venda'   => 'id_canal_venda',
    //'contrib-icms'      => 'sn_contribuinte_icms',
    //'ind-fat-par'       => 'sn_aceita_faturamento_parcial',
    //'nat-operacao'      => 'cfop_estadual_padrao',
    //'nat-ope-ext'       => 'cfop_interestadual_padrao',
    //'ind-cre-cli'       => 'id_sit_cred',
    //'telefone2'         => 'tel2',



$ArChaves = array('a1_cgc');

$ArFixos = array(
    'dthr_importado_erp'        => date("Y-m-d"),
    'sn_cliente'                => 1,
    'sn_importado_erp'          => 1,
    'sn_grupo_inadimplente'     => 1
);

$SqlCustom = "SELECT * FROM SA1".$CodEmpresaProtheus;

$SqlClientesNacionais = $SqlCustom."";

$Imp = new impODBCProtheusTableCustom_is_pessoa();
$Imp->setCnxOdbc($CnxODBC);
$Imp->setTabelaOrigem("SA1".$CodEmpresaProtheus);
$Imp->setTabelaDestino('is_pessoa');
$Imp->setSqlOdbcCustom($SqlClientesNacionais);
$Imp->setArDepara($ArDepara);
$Imp->setArFixos($ArFixos);
$Imp->setChaves($ArChaves);

$Imp->setCampoDepara('id_vendedor_padrao');
$Imp->setCampoDeparaTabelaCRM('is_usuario');
$Imp->addCampoDeparaTabelaChaveCRM('id_representante');

$Imp->setCampoDepara('id_representante_padrao');
$Imp->setCampoDeparaTabelaCRM('is_usuario');
$Imp->addCampoDeparaTabelaChaveCRM('id_representante');

$Imp->setCampoDepara('id_transportadora_padrao');
$Imp->setCampoDeparaTabelaCRM('is_transportadora');
$Imp->addCampoDeparaTabelaChaveCRM('id_transportadora_erp');

$Imp->setCampoDepara('id_tab_preco_padrao');
$Imp->setCampoDeparaTabelaCRM('is_tab_preco');
$Imp->addCampoDeparaTabelaChaveCRM('id_tab_preco_erp');

$Imp->setCampoDepara('id_grupo_cliente');
$Imp->setCampoDeparaTabelaCRM('is_grupo_cliente');
$Imp->addCampoDeparaTabelaChaveCRM('id_grupo_cliente_erp');

$Imp->setCampoDepara('id_cond_pagto_padrao');
$Imp->setCampoDeparaTabelaCRM('is_cond_pagto');
$Imp->addCampoDeparaTabelaChaveCRM('id_cond_pagto_erp');

/*$Imp->setCampoDepara('id_canal_venda');
$Imp->setCampoDeparaTabelaCRM('is_canal_venda');
$Imp->addCampoDeparaTabelaChaveCRM('id_canal_venda_erp');

$Imp->setCampoDepara('cfop_estadual_padrao');
$Imp->setCampoDeparaTabelaCRM('is_cfop');
$Imp->addCampoDeparaTabelaChaveCRM('id_cfop_erp');

$Imp->setCampoDepara('cfop_interestadual_padrao');
$Imp->setCampoDeparaTabelaCRM('is_cfop');
$Imp->addCampoDeparaTabelaChaveCRM('id_cfop_erp');
*/
echo '<strong>Importa&ccedil;&atilde;o de clientes nacionais</strong><br />';
$Imp->Importa();
$Imp->mostraResultado();


/* Tratamento para estrangeiros */
if(GetParam('CLI_SN_ESTRANGEIRO') == '1'){
    $Imp->ZeraContadores();
    $SqlClientesEstrangeiros = $SqlCustom." AND a1_est = 'EX'";
    $Imp->setSqlOdbcCustom($SqlClientesEstrangeiros);

    $ArChaves = array('a1_cod');
    $Imp->setChaves($ArChaves);

    echo '<strong>Importa&ccedil;&atilde;o de clientes estrangeiros</strong><br />';
    $Imp->Importa();
    $Imp->mostraResultado();
}
$Imp = NULL;
?>