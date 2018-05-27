<?php
/*
 * is_cfop_param.php
 * Autor: Alex
 * 03/12/2010 10:45
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
$CnxODBC = ConectaODBCErpDatasul($ArrayConf,'emitente');
if(!$CnxODBC){
    echo 'Não foi possível estabelecer uma conexão com o ERP.';
    exit;
}

class impODBCProgressTableCustom_is_pessoa extends impODBCProgressTable{
    private $QtdeContatosCriados = 0;
    private $QtdeContatosErro = 0;
    
    public function setValorCustom($ArDados){
        /* Tratamento para estrangeiros */
        if($ArDados['id_tp_pessoa'] == '3'){
            unset($ArDados['id_tp_pessoa']);
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
        return $ArDados;
    }
    
    public function AposIncluir($Numreg,$ArDados){
        $IdPessoaERP = $ArDados['id_pessoa_erp'];
        $SqlContatosERP = "SELECT \"sequencia\", \"nome\", \"cargo\", \"area\", \"telefone\", \"ramal\", \"telefax\", \"ramal-fax\", \"e-mail\", \"observacao\" FROM pub.\"cont-emit\" WHERE \"cod-emitente\" = '".$IdPessoaERP."' ORDER BY \"sequencia\" ASC";
        $QryContatosERP = odbc_exec($this->CnxOdbc,$SqlContatosERP);
        while($ArContatosERP = odbc_fetch_array($QryContatosERP)){
            $Nome = trim($ArContatosERP['nome']);           
            if($Nome == ''){
                continue;
            }
            $Cargo = ucwords(trim($ArContatosERP['cargo']));
            $Area = ucwords(trim($ArContatosERP['area']));
            $Observacoes = '';
            if($Cargo != ''){
                $Observacoes .= 'Cargo: '.$Cargo."\r\n";
            }
            if($Area != ''){
                $Observacoes .= 'Àrea: '.$Area."\r\n";
            }
            $Observacoes .= $ArContatosERP['observacao'];
            
            $ArSqlInsertContato = array(
                'id_empresa' => $Numreg,
                'id_contato_erp' => $ArContatosERP['sequencia'],
                'nome' => $Nome,
                'tel1' => $ArContatosERP['telefone'],
                'tel2' => $ArContatosERP['telefax'],
                'email' => $ArContatosERP['e-mail'],
                'obs' => $Observacoes,
                
                'dt_cadastro' => date("Y-m-d"),
                'sn_padrao' => '0',
                'sn_recebe_mailing' => '0'                
            );
            $SqlInsertContato = AutoExecuteSql(TipoBancoDados, 'is_contato', $ArSqlInsertContato, 'INSERT');
            $QryInsertContato = query($SqlInsertContato);
            if($QtdeContatosCriados){
                $this->QtdeContatosCriados++;
            }
            else{
                $this->QtdeContatosErro++;
            }
        }
    }
    
    public function ZeraContadoresContato(){
        $this->QtdeContatosCriados  = 0;
        $this->QtdeContatosErro     = 0;
    }
    
    public function MostraResultadoContatos(){
        echo '<strong>Importa&ccedil;&atilde;o de contatos</strong><br />';
        echo 'Quantidade de registros criados: '.$this->QtdeContatosCriados.'<br />';
        echo 'Quantidade de registros com erro: '.$this->QtdeContatosErro.'<br />';
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
    'categoria'         => 'categoria_erp',
    'observacoes'       => 'obs'
    );

$ArChaves = array('cgc');

$ArFixos = array(
    'dthr_importado_erp'        => date("Y-m-d"),
    'sn_cliente'                => 1,
    'sn_importado_erp'          => 1,
    'sn_grupo_inadimplente'     => 1
);

$SqlCustom = "SELECT 
                    \"data-implant\",
                    \"natureza\",
                    \"cod-emitente\",
                    \"nome-emit\",
                    \"nome-abrev\",
                    \"cgc\",
                    \"ins-estadual\",
                    \"e-mail\",
                    \"endereco\",
                    \"bairro\",
                    \"cidade\",
                    \"estado\",
                    \"pais\",
                    \"cep\",
                    \"nome-mic-reg\",
                    \"cod-rep\" AS \"cod-rep-vend\",
                    \"cod-rep\",
                    \"cod-gr-cli\",
                    \"nr-tabpre\",
                    \"cod-transp\",
                    \"contrib-icms\",
                    \"ind-fat-par\",
                    \"cod-cond-pag\",
                    \"cod-canal-venda\",
                    \"ind-cre-cli\",
                    \"nat-operacao\",
                    \"nat-ope-ext\",
                    \"home-page\",
                    \"telefone\"[1] AS \"telefone1\",
                    \"telefone\"[2] AS \"telefone2\",
                    \"telefax\",
                    \"lim-credito\",
                    \"cod-suframa\",
                    \"bonificacao\",
                    \"insc-subs-trib\",
                    \"categoria\",
                    \"observacoes\"                    
                FROM pub.\"emitente\" WHERE (\"identific\" = 1 OR \"identific\" = 3) ";

$SqlClientesNacionais = $SqlCustom."AND \"natureza\" IN(1,2)";

$Imp = new impODBCProgressTableCustom_is_pessoa();
$Imp->setCnxOdbc($CnxODBC);
$Imp->setTabelaOrigem('pub."emitente"');
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
$Imp->MostraResultadoContatos();

/* Tratamento para estrangeiros */
if(GetParam('CLI_SN_ESTRANGEIRO') == '1'){
    $Imp->ZeraContadores();
    $Imp->ZeraContadoresContato();
    $SqlClientesEstrangeiros = $SqlCustom."AND \"natureza\" = 3";
    $Imp->setSqlOdbcCustom($SqlClientesEstrangeiros);

    $ArChaves = array('cod-emitente');
    $Imp->setChaves($ArChaves);
    
    echo '<strong>Importa&ccedil;&atilde;o de clientes estrangeiros</strong><br />';
    $Imp->Importa();
    $Imp->mostraResultado();
    $Imp->MostraResultadoContatos();
}
$Imp = NULL;
/*
 * -----------------------------------------------------------------------------
 * IMPORTANDO O CAMPO PERTENCE AO GRUPO
 */

$ArDepara = array(
    'nome-matriz'   => 'id_pertence_grupo',
    'cgc'           => 'cnpj_cpf'
);

$ArChaves = array('cgc');

$ArFixos = array();

$SqlCustom = "SELECT \"cgc\",\"nome-matriz\" FROM pub.\"emitente\" WHERE (\"identific\" = 1 OR \"identific\" = 3) AND (\"natureza\" = 1 OR \"natureza\" = 2) AND \"nome-matriz\" != \"nome-abrev\"";

$Imp = new impODBCProgressTable();
$Imp->setCnxOdbc($CnxODBC);
$Imp->setTabelaOrigem('pub."emitente"');
$Imp->setTabelaDestino('is_pessoa');
$Imp->setSqlOdbcCustom($SqlCustom);
$Imp->setArDepara($ArDepara);
$Imp->setArFixos($ArFixos);
$Imp->setChaves($ArChaves);

$Imp->setCampoDepara('id_pertence_grupo');
$Imp->setCampoDeparaTabelaCRM('is_pessoa');
$Imp->addCampoDeparaTabelaChaveCRM('fantasia_apelido');

echo '<strong>Importa&ccedil;&atilde;o do campo pertence ao grupo</strong><br />';
$Imp->Importa();
$Imp->mostraResultado();
odbc_close($CnxODBC);
?>