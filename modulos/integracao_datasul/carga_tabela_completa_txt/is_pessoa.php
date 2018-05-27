<?php
/*
 * is_pessoa.php
 * Autor: Alex
 * 08/11/2011 15:25:31
 */
session_start();
set_time_limit(600); /* 10 minutos */
include_once('../../../conecta.php');
include_once('../../../functions.php');
include_once('../../../classes/class.impTXTProgressTable.php');

class impTXTProgressTableCustom_is_pessoa extends impTXTProgressTable{
    private $QtdeContatosCriados = 0;
    private $QtdeContatosErro = 0;
    
    public function IgnoraRegistroCustom($Ignorar,$ArDados){
        if($ArDados['id_tp_pessoa'] == '4'){
            return true;
        }
        if($ArDados['id_tp_pessoa'] != '3' && trim($ArDados['cnpj_cpf']) == ''){
            return true;
        }
        return false;
    }
    
    public function setValorCustom($ArDados){
        /* Tratamento para estrangeiros */
        if($ArDados['id_tp_pessoa'] == '3'){
            $ArDados['sn_estrangeiro'] = 1;
        }
        else{
            /* Tratamento para definir o tipo de pessoa */
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
        }
        if($ArDados['id_micro_regiao'] != ''){
            $QryMicroRegiao = query("SELECT id_regiao FROM is_micro_regiao WHERE numreg = ".$ArDados['id_micro_regiao']);
            $ArMicroRegiao = farray($QryMicroRegiao);
            $ArDados['id_regiao'] = $ArMicroRegiao['id_regiao'];
        }
        $ArDados['sn_possui_suframa']               = (trim($ArDados['cod_suframa']) != '')?'1':'0';
        $ArDados['sn_possui_insc_aux_st']           = (trim($ArDados['insc_aux_st']) != '')?'1':'0';
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

    public function setValorCustomUpdate($ArDados){
        $ArDados['sn_cliente']            = 1;
        $ArDados['sn_prospect']           = 0;
        $ArDados['sn_suspect']            = 0;
        $ArDados['sn_importado_erp']      = 1;
        $ArDados['sn_exportado_erp']      = 1;
        
        /* Tratamento para estrangeiros */
        if($ArDados['id_tp_pessoa'] == '3'){
            unset($ArDados['id_tp_pessoa']);
        }
        
        return $ArDados;
    }
    
    public function getArChavesCustom($ArChaves,$ArDados){
        if($ArDados['sn_estrangeiro'] == '1'){
            return array('id_pessoa_erp');
        }
        return $ArChaves;
    }
}

$ArDepara = array(
    'data-implant'      => 'dt_cadastro',
    'natureza'          => 'id_tp_pessoa',
    'cod-emitente'      => 'id_pessoa_erp',
    'nome-emit'         => 'razao_social_nome',
    'nome-abrev'        => 'fantasia_apelido',
    'cgc'               => 'cnpj_cpf',
    'ins-estadual'      => 'ie_rg',
    'e-mail'            => 'email',

    'endereco'          => 'endereco',
    'bairro'            => 'bairro',
    'cidade'            => 'cidade',
    'estado'            => 'uf',
    'pais'              => 'pais',
    'cep'               => 'cep',
    'nome-mic-reg'      => 'id_micro_regiao',
    
    'cod-rep'           => 'id_representante_padrao',
    'cod-rep-vend'      => 'id_vendedor_padrao',
    'cod-gr-cli'        => 'id_grupo_cliente',
    'nr-tabpre'         => 'id_tab_preco_padrao',
    'cod-transp'        => 'id_transportadora_padrao',
    'cod-cond-pag'      => 'id_cond_pagto_padrao',
    'cod-canal-venda'   => 'id_canal_venda',
    'contrib-icms'      => 'sn_contribuinte_icms',
    'ind-fat-par'       => 'sn_aceita_faturamento_parcial',
    'nat-operacao'      => 'cfop_estadual_padrao',
    'nat-ope-ext'       => 'cfop_interestadual_padrao',
    
    'ind-cre-cli'       => 'id_sit_cred',

    'home-page'         => 'site',
    'telefone1'         => 'tel1',
    'telefone2'         => 'tel2',
    'telefax'           => 'fax',
    'data-implant'      => 'dt_virou_cliente',
    'lim-credito'       => 'vl_limite_credito',
    'cod-suframa'       => 'cod_suframa',
    'insc-subs-trib'    => 'insc_aux_st',
    'observacoes'       => 'obs'
    );

$ArChaves = array('cgc');

$ArFixos = array(
    'dthr_importado_erp'        => date("Y-m-d"),
    'sn_cliente'                => 1,
    'sn_importado_erp'          => 1,
    'sn_grupo_inadimplente'     => 1
);

$ArTratamentoFixoData = array('dt_cadastro','dt_virou_cliente');
$ArTratamentoFixoSimNao = array('sn_contribuinte_icms','sn_aceita_faturamento_parcial');

$Imp = new impTXTProgressTableCustom_is_pessoa();
$Imp->setNomeArquivoLeitura('emitente');
$Imp->setTabelaDestino('is_pessoa');
$Imp->setArDepara($ArDepara);
$Imp->setArFixos($ArFixos);
$Imp->setChaves($ArChaves);

$Imp->setArTratamentoFixoData($ArTratamentoFixoData);
$Imp->setArTratamentoFixoSimNao($ArTratamentoFixoSimNao);

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

$Imp->setCampoDepara('id_micro_regiao');
$Imp->setCampoDeparaTabelaCRM('is_micro_regiao');
$Imp->addCampoDeparaTabelaChaveCRM('id_micro_regiao_erp');

$Imp->setCampoDepara('id_cond_pagto_padrao');
$Imp->setCampoDeparaTabelaCRM('is_cond_pagto');
$Imp->addCampoDeparaTabelaChaveCRM('id_cond_pagto_erp');

$Imp->setCampoDepara('id_canal_venda');
$Imp->setCampoDeparaTabelaCRM('is_canal_venda');
$Imp->addCampoDeparaTabelaChaveCRM('id_canal_venda_erp');

$Imp->setCampoDepara('cfop_estadual_padrao');
$Imp->setCampoDeparaTabelaCRM('is_cfop');
$Imp->addCampoDeparaTabelaChaveCRM('id_cfop_erp');

$Imp->setCampoDepara('cfop_interestadual_padrao');
$Imp->setCampoDeparaTabelaCRM('is_cfop');
$Imp->addCampoDeparaTabelaChaveCRM('id_cfop_erp');

echo '<strong>Importa&ccedil;&atilde;o de clientes nacionais</strong><br />';
$Imp->Importa();
$Imp->mostraResultado();
?>