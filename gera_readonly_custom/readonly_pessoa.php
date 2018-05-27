<?php
if($id_funcao == 'pessoa'){
    $SnIntegradoERP = (GetParam('INT_ERP') == 1)?true:false;
    $NomeERP = GetParam('INT_ERP_NOME');
    /*
     * Tratamento para cliente estrangeiro
     */
    if($id_campo == 'sn_estrangeiro'){
        if(GetParam('CLI_SN_ESTRANGEIRO') != '1'){
            $exibir_formulario = '0';
            $qry_gera_cad_campos['sn_obrigatorio'] = 0;
        }
    }

    /* $ArrayCamposObrigatorios = array('razao_social_nome','tel1','email'); */
    $ArrayCamposObrigatorios = array('razao_social_nome','tel1','id_grupo_cliente');
    $ArrayCamposOcultarPF = array('fantasia_apelido','id_segmento','id_ramo_atividade','qtde_func_filhos','cod_suframa','id_pertence_grupo','sn_grupo_inadimplente');
    $ArrayCamposOcultarAoIncluir = array('id_pessoa_erp','dt_cadastro');
    $ArrayCamposObrigatoriosAoIncluirCliente = array('sn_estrangeiro','id_tp_pessoa','cnpj_cpf');

    if(!$SnIntegradoERP){
        $ArrayCamposObrigatoriosAoIncluirCliente = array('sn_estrangeiro','id_tp_pessoa','cnpj_cpf','cep','endereco','numero','bairro','cidade','pais','uf');
    }

    if($qry_cadastro['sn_cliente'] == '1' && $id_campo == 'email'){
        $qry_gera_cad_campos['sn_obrigatorio'] = 0;
        $Keys = array_keys($ArrayCamposObrigatorios, 'email');
        if(is_array($Keys)){
            foreach($Keys as $Key){
                unset($ArrayCamposObrigatorios[$Key]);
            }
        }
    }

    if($pnumreg == '-1'){
        if(is_int(array_search($id_campo,$ArrayCamposOcultarAoIncluir))){
            $exibir_formulario = '0';
            $qry_gera_cad_campos['sn_obrigatorio'] = 0;
        }
        if(!$SnIntegradoERP && $_GET['pgetcustom'] == 'cliente' && is_int(array_search($id_campo, $ArrayCamposObrigatoriosAoIncluirCliente))){
            $exibir_formulario = '1';
            $qry_gera_cad_campos['sn_obrigatorio'] = 1;
        }
    }
    elseif($id_campo == 'id_tp_pessoa' || $id_campo == 'sn_estrangeiro'){//Travando campo Tipo Pessoa e Estrangeiro quando o registro j� est� salvo
        $readonly = 'readOnly="readOnly" style="background-color:#CCCCCC" ';
    }

    if($qry_cadastro['id_tp_pessoa'] == 2){//Se for pessoa fÍsica oculta alguns campos definidos na array acima
        if(is_int(array_search($id_campo,$ArrayCamposOcultarPF))){
            $exibir_formulario = '0';
            $qry_gera_cad_campos['sn_obrigatorio'] = 0;
        }
    }
    if(is_int(array_search($id_campo,$ArrayCamposObrigatorios))){
        $qry_gera_cad_campos['sn_obrigatorio'] = 1;
    }
    /*
     * Tratamento para quando for transformar um prospect em cliente
     */
    if($_GET['ptpec'] == '1'){
        if(!$Usuario->getPermissao('sn_trans_prospect_cliente')){
            echo alert(getError('0010020019',getParametrosGerais('RetornoErro')));
            echo historyBack(1);
            exit;
        }

        if($SnIntegradoERP){
            $ArrayCamposObrigatoriosTransformarProspectCliente = array(
                'id_ramo_atividade',    'id_tp_pessoa',
                'razao_social_nome',    'cnpj_cpf',
                'id_tab_preco_padrao',  'id_grupo_cliente',
                'id_origem',            'tel1',
                'email',                'cep',
                'endereco',             'numero',
                'bairro',               'cidade',
                'uf',                   'pais',
                'id_regiao',            'id_grupo_cliente',
                'id_canal_venda',       'id_tab_preco_padrao',
                'id_cond_pagto_padrao', 'id_transportadora_padrao',
                'id_tp_frete_padrao',   'sn_contribuinte_icms',
                'sn_aceita_faturamento_parcial');
        }
        else{
            $ArrayCamposObrigatoriosTransformarProspectCliente = array(
                'id_tp_pessoa',
                'razao_social_nome',    'cnpj_cpf',
                'id_origem',            'tel1',
                'email',                'cep',
                'endereco',             'numero',
                'bairro',               'cidade',
                'uf',                   'pais');
        }

        /* Se o cliente for do tipo estrangeiro remove o campo cnpj_cpf dos obrigatórios */
        if($qry_cadastro['sn_estrangeiro'] == '1'){
            $ChaveArray = array_search('cnpj_cpf', $ArrayCamposObrigatoriosTransformarProspectCliente);
            unset($ArrayCamposObrigatoriosTransformarProspectCliente[$ChaveArray]);
        }

        if(is_int(array_search($id_campo,$ArrayCamposObrigatoriosTransformarProspectCliente))){
            //Desconsiderando os campos que são ocultos para pessoa física
            if($qry_cadastro['id_tp_pessoa'] == 2 && is_int(array_search($id_campo,$ArrayCamposOcultarPF))){
                $qry_gera_cad_campos['sn_obrigatorio'] = 0;
            }
            else{
                $qry_gera_cad_campos['sn_obrigatorio'] = 1;
            }
        }
    }
    /*
     * Tratamento para quando um cliente já estiver salvo na base
     */
    if($qry_cadastro['sn_cliente'] == 1 || $_GET['pgetcustom'] == 'cliente' || $qry_cadastro['sn_fornecedor'] == 1 || $_GET['pgetcustom'] == 'fornecedor'){
        if(!$Usuario->getPermissao('sn_pemrite_editar_cliente')){
            $readonly = 'readOnly="readOnly" style="background-color:#CCCCCC" ';
        }
        $ArrayCamposEditaveisCliente = array(
            'id_origem_conta', 'id_tp_campanha', 'id_pessoa_indicado_por', 'faturamento_renda', 'id_segmento', 'id_ramo_atividade', 'qtde_func_filhos', 'sn_ativo', 'id_motivo_inativo', 'id_vendedor_padrao', 'id_operador_padrao', 'id_nivel_financeiro',
            /* Pessoa FÍsica */
            'email_pessoal','id_estcivil','dianascto','mesnascto','anonascto','id_graduacao','id_tratamento','id_forma_contato_preferida','id_periodo_contato_preferido','id_bebida_preferida','id_hobby','id_nacionalidade','skype','msn','id_sexo'
        );
        if ($NomeERP == 'PROTHEUS') {
            $ArrayCamposEditaveisCliente[] = 'razao_social_nome';
            $ArrayCamposEditaveisCliente[] = 'fantasia_apelido';
            $ArrayCamposEditaveisCliente[] = 'ddi_tel1';
            $ArrayCamposEditaveisCliente[] = 'ddi_tel2';
            $ArrayCamposEditaveisCliente[] = 'ddi_fax';
            $ArrayCamposEditaveisCliente[] = 'tel1';
            $ArrayCamposEditaveisCliente[] = 'tel2';
            $ArrayCamposEditaveisCliente[] = 'fax';
            $ArrayCamposEditaveisCliente[] = 'email';
            $ArrayCamposEditaveisCliente[] = 'cep';
            $ArrayCamposEditaveisCliente[] = 'endereco';
            $ArrayCamposEditaveisCliente[] = 'numero';
            $ArrayCamposEditaveisCliente[] = 'complemento';
            $ArrayCamposEditaveisCliente[] = 'referencia';
            $ArrayCamposEditaveisCliente[] = 'bairro';
            $ArrayCamposEditaveisCliente[] = 'cidade';
            $ArrayCamposEditaveisCliente[] = 'uf';
            $ArrayCamposEditaveisCliente[] = 'pais';
            $ArrayCamposEditaveisCliente[] = 'id_regiao';
            $ArrayCamposEditaveisCliente[] = 'id_micro_regiao';
        }
        if($qry_cadastro['id_tp_pessoa'] == '2'){
            $ArrayCamposEditaveisCliente[] = 'ie_rg';
        }
        if($SnIntegradoERP && is_int(array_search($id_campo,$ArrayCamposEditaveisCliente)) || (substr($id_campo,0,4)=='wcp_')){
            $readonly = '';
        }
        elseif(!$SnIntegradoERP){
            $ArCamposNaoEditaveisAlteracao = array(
                'id_tp_pessoa','sn_estrangeiro','dt_cadastro'
            );
            if($qry_gera_cad_campos['tipo_campo'] != 'calculado'){
                $readonly = '';
            }
            if($_GET['pnumreg'] != '-1' && is_int(array_search($id_campo,$ArCamposNaoEditaveisAlteracao))){
                $readonly = 'readOnly="readOnly" style="background-color:#CCCCCC" ';
            }

            $ArCamposObrigatoriosAlteracao = array(
                'sn_estrangeiro','id_tp_pessoa','cnpj_cpf','cep','endereco','numero','bairro','cidade','pais','uf'
            );
            if($_GET['pnumreg'] != '-1' && is_int(array_search($id_campo,$ArCamposObrigatoriosAlteracao))){
                $qry_gera_cad_campos['sn_obrigatorio'] = 1;
            }
        }
        else{
            $readonly = 'readOnly="readOnly" style="background-color:#CCCCCC" ';
        }
    }
    /*
     * Tratamento para ocultar campos que so devem ser exibidos para cliente
     */
    

    if(($qry_cadastro['sn_suspect'] == 1 || $qry_cadastro['sn_prospect'] == 1 || $_GET['pgetcustom'] == 'suspect' || $_GET['pgetcustom'] == 'prospect') && $_GET['ptpec'] != '1'){
        $ArrayCamposNaoExibirProspectSuspect = array(   'recencia',
                                                        'frequencia',
                                                        'valor',
                                                        'id_status_frequencia',
                                                        'pct_med_rentabilidade',
                                                        'life_time_value',
                                                        'score',
                                                        'dt_virou_cliente',
                                                        'sn_inadimplente',
                                                        'vl_limite_credito',
                                                        'dt_limite_credito_validade',
                                                        'dt_ult_nf_emitida',
                                                        'qtde_titulos_em_atraso',
                                                        'qtde_max_titulos_em_atraso',
                                                        'vl_maior_compra',
                                                        'dt_maior_compra',
                                                        'saldo_limite_credito',
                                                        'sn_grupo_inadimplente',
                                                        'calc_inadimplente',
                                                        'id_resultado_ult_pesquisa'
        );
        if(is_int(array_search($id_campo,$ArrayCamposNaoExibirProspectSuspect))){
            $exibir_formulario = '0';
            $qry_gera_cad_campos['sn_obrigatorio'] = 0;
        }
    }
}
