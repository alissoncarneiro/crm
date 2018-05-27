<?php
/*
 * class.VendaParametro.php
 * Autor: Alex
 * 28/10/2010 16:25
 * -
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
class VendaParametro{
    private  $DadosParametro;

    public function  __construct(){
        $SqlParametro = "SELECT * FROM is_venda_parametro";
        $QryParametro = query($SqlParametro);
        $ArParametro = farray($QryParametro);
        $this->DadosParametro = $ArParametro;
    }

    /**
     * Retorna o diretório de onde os arquivos de pedidos do CRM devem ser gerados
     * @return string
     */
    public function getDirArquivoPedidoCRM(){
        return $this->DadosParametro['dir_arquivo_pedido_crm'];
    }

    /**
     * Retorna o diretório de onde os arquivos de pedidos são gerados pelo ERP
     * @return string
     */
    public function getDirArquivoPedidoERP(){
        return $this->DadosParametro['dir_arquivo_pedido_erp'];
    }

    /**
     * Define se podem ser adicionados itens sem preço no orçamento/pedido
     * @return bool
     */
    public function getPermiteAdicionarItemSemPreco(){
        return ($this->DadosParametro['sn_permite_add_prod_sem_preco'] == 1);
    }

    /**
     * Define se o recurso de adicionar itens por familía no passo 2 estará ativo
     * @return bool
     */
    public function getPermiteAdicionarItemPorFamilia(){
        return ($this->DadosParametro['selec_it_multiplo_familia'] == 1);
    }

    public function getUtilizaLinhaProduto(){
        return ($this->DadosParametro['utiliza_linha_produto'] == 1);
    }

    public function getModoExportacaoPedidoTXT(){
        return $this->DadosParametro['modo_exportacao_pedido_txt'];
    }

    public function getNomeCampoCodComplementarProduto(){
        return $this->DadosParametro['nome_campo_cod_complementar'];
    }

    public function getPermiteAlterarCFOPItem(){
        return ($this->DadosParametro['sn_permite_alterar_cfop_item'] == 1);
    }

    public function getSnSugereCFOPCliente(){
        return ($this->DadosParametro['sn_sugere_cfop_cliente'] == 1);
    }

    public function getSnUsaItemNaoComercial(){
        return ($this->DadosParametro['sn_usa_item_nao_comercial'] == 1);
    }

    public function getSnUsaKit(){
        return ($this->DadosParametro['sn_usa_kit'] == 1);
    }

    public function getUsaPerdaItemOrcamento(){
        return ($this->DadosParametro['sn_usa_perda_item_orcamento'] == 1);
    }

    public function getPermiteAdicionarItemSemReferencia(){
        return ($this->DadosParametro['sn_permite_add_prod_sem_refer'] == 1);
    }

    public function getPermiteAdicionarItemSemReferenciaOrcamento(){
        return ($this->DadosParametro['sn_per_add_prod_sem_refer_orc'] == 1);
    }

    public function getSnDescontoEmCascata(){
        return $this->DadosParametro['sn_desconto_cascata'];
    }

    public function getSnExibeCFOPItem(){
        return ($this->DadosParametro['sn_exibe_cfop_no_item'] == 1);
    }

    public function getSnPermiteAdicionarItemRepetido(){
        return ($this->DadosParametro['sn_permite_add_prod_repetido'] == 1);
    }

    public function getSnPermiteEnviarPedidoBonificacaoParaAprovacao(){
        return ($this->DadosParametro['sn_pedido_bonificacao_aprov'] == 1);
    }

    public function getVlToleranciaPedidoBonificacao(){
        return $this->DadosParametro['vl_tolerancia_ped_bonific'];
    }

    public function getSnConsultaEstoque(){
        return ($this->DadosParametro['sn_consulta_estoque'] == 1);
    }

    public function getModoConsultaEstoque(){
        return $this->DadosParametro['modo_consulta_estoque'];
    }

    public function getSnCalculaIPI(){
        return ($this->DadosParametro['sn_calcula_ipi'] == 1);
    }

    public function getSnCalculaST(){
        return ($this->DadosParametro['sn_calcula_st'] == 1);
    }
    
    public function getSnExibeIPI(){
        return ($this->DadosParametro['sn_exibe_ipi'] == '1');
    }
    
    public function getSnExibeST(){
        return ($this->DadosParametro['sn_exibe_st'] == '1');
    }

    public function getModoUnidMedida(){
        return $this->DadosParametro['modo_unid_medida'];
    }

    public function getSnUsaTabPrecoPorItem(){
        return ($this->DadosParametro['sn_usa_tab_preco_por_item'] == 1);
    }

    public function getSnVendaMoedaUnica(){
        return ($this->DadosParametro['sn_venda_moeda_unica'] == 1);
    }

    public function getSnConsideraCotacaoFixaForaPolitca(){
        return ($this->DadosParametro['sn_cons_cot_fixa_fora_politc'] == 1);
    }

    public function getSnSnUsaCodProdutoCliente(){
        return ($this->DadosParametro['sn_usa_cod_produto_cliente'] == 1);
    }

    public function getSnUsaTabComplProd(){
        return ($this->DadosParametro['sn_usa_tab_compl_prod'] == 1);
    }

    public function getSnExibeValorSTItem(){
        return ($this->DadosParametro['sn_exibe_vl_st_no_item'] == 1);
    }

    public function getTextoVendaForaPolitica(){
        return $this->DadosParametro['texto_venda_fora_politica'];
    }

    public function getSnAlterarDtEntPorItem(){
        return ($this->DadosParametro['sn_alterar_dt_ent_por_item'] == 1);
    }

    public function getSnControlaAtividade(){
        return ($this->DadosParametro['sn_controla_atividade'] == 1);
    }

    public function getSnUsaSugestaoDePrecoDeNF(){
        return ($this->DadosParametro['sn_usa_sugestao_preco_nf'] == 1);
    }

    /**
     * Define se a política comercial por sugestão de preço de NF será considerada
     * @return bool
     */
    public function getSnAplicaPolComSugPreNF(){
        return ($this->DadosParametro['sn_aplica_pol_com_sug_pre_nf'] == 1);
    }

    /**
     * Quantidade de dias de validade do preço quando sugerido da última NF
     * @return int
     */
    public function getQtdeDiasPolComSugPreNF(){
        return $this->DadosParametro['qtde_dias_pol_com_sug_pre_nf'];
    }

    /**
     * Percentual mínimo de diferença do preço quando sugerido pela última NF e alterado pelo usuário
     * @return float
     */
    public function getPctMinPolComSugPreNF(){
        return $this->DadosParametro['pct_min_pol_com_sug_pre_nf'];
    }

    /**
     * Percentual máximo de diferença do preço quando sugerido pela última NF e alterado pelo usuário
     * @return float
     */
    public function getPctMaxPolComSugPreNF(){
        return $this->DadosParametro['pct_max_pol_com_sug_pre_nf'];
    }

    /**
     * Define se os pedidos serão exportados somente um dia antes da data desejada de entrega
     * @return bool
     */
    public function getSnExportaNaDataEntrega(){
        return ($this->DadosParametro['sn_exporta_na_data_entrega'] == 1);
    }

    /**
     * Retorna o código da moeda Real do sistema
     * @return <type>
     */
    public function getIdMoedaReal(){
        return $this->DadosParametro['id_moeda_real'];
    }

    /**
     * Define se a cotação dos itens será recalculada no momento em que o pedido for exportado para o ERP pelo primeira vez
     * @return bool
     */
    public function getSnAtualizaCotacaoExportacao(){
        return ($this->DadosParametro['sn_atualiza_cotacao_exportacao'] == 1);
    }

    /**
     * Define se usa função de estoque do ERP Datasul via XML
     * @return bool
     */
    public function getSnUsaURLEstoqXmlDatasul(){
        return ($this->DadosParametro['sn_usa_url_estoq_xml_datasul'] == 1);
    }

    /**
     * Retorna a URL do XML de estoque do ERP Datasul
     * @return string
     */
    public function getURLEstoqueXmlErpDatasul(){
        return $this->DadosParametro['url_estoque_xml_erp_datasul'];
    }

    public function getSnUsaDataDesejadaEntrega(){
        return ($this->DadosParametro['sn_usa_data_desejada_entrega'] == 1);
    }

    public function getSnPermiteAlterarDtEntrega(){
        return ($this->DadosParametro['sn_permite_alterar_dt_entrega'] == 1);
    }

    public function getSnPermiteClonarVenda(){
        return ($this->DadosParametro['sn_permite_clonar_venda'] == 1);
    }

    public function getSnOrcamentoGeraOportunidade(){
        return ($this->DadosParametro['sn_orcamento_gera_oportunidade'] == 1);
    }

    public function getOporIdFaseVendaPadrao(){
        return $this->DadosParametro['opor_id_fase_venda_padrao'];
    }

    public function getOporIdCicloVendaPadrao(){
        return $this->DadosParametro['opor_id_ciclo_venda_padrao'];
    }

    public function getSnExportaPrecoInformado(){
        return ($this->DadosParametro['sn_exporta_preco_informado'] == 1);
    }

    public function getSnExibeFaixaPrecoComissao(){
        return ($this->DadosParametro['sn_exibe_faixa_preco_comissao'] == 1);
    }

    public function getSnUsaDescTabPrecoItem(){
        return ($this->DadosParametro['sn_usa_desc_tab_preco_item'] == 1);
    }
    
    public function getSnExibeDescontoCapaNoItem(){
        return ($this->DadosParametro['sn_exibe_desconto_capa_no_item'] == 1);
    }

    public function getSnUsaCalculoCFOPCustomizado(){
        return ($this->DadosParametro['sn_usa_calculo_cfop_customizado'] == 1);
    }
    
    public function getNomeCampoDescTabPrecoItem(){
        return $this->DadosParametro['nome_campo_desc_tab_preco_item'];
    }
    
    public function getSnUsaCalculoDeFrete(){
        return $this->DadosParametro['sn_usa_calculo_frete'];
    }
    
    public function getSnUsaCalculoDeFreteCustomizado(){
        return $this->DadosParametro['sn_usa_calculo_frete_custom'];
    }
    
    public function getSnAdicionaNomeKitObsItem(){
        return ($this->DadosParametro['sn_adiciona_nome_kit_obs_item'] == 1);
    }
    
    public function getSnAutocompletaDadosCliente(){
        return ($this->DadosParametro['sn_autocompleta_dados_cliente'] == 1);
    }
    
    /**
     * Retorna se o sistema utilizará a restrição de cond. e forma de pagto por estabelecimento 
     * @return boolean 
     */
    public function getSnUsaRestrEstFCondPagto(){
        return ($this->DadosParametro['sn_usa_restr_est_f_cond_pagto'] == 1);
    }
    
    public function getSnPermiteAddProdNaoFat(){
        return ($this->DadosParametro['sn_permite_add_prod_nao_fat'] == 1);
    }
    
    public function getSnUsaCalcDescontoItem(){
        return ($this->DadosParametro['sn_usa_calc_desconto_item'] == 1);
    }
    
    public function getIdCondPagtoBonificPadrao(){
        return $this->DadosParametro['id_cond_pagto_bonific_padrao'];
    }
    
    public function getIntTxtErpDatasulObs(){
        return $this->DadosParametro['int_txt_erp_datasul_obs'];
    }
    
    public function getIntTxtErpDatasulObsNF(){
        return $this->DadosParametro['int_txt_erp_datasul_obs_nf'];
    }
    
    /**
     * Retorna se é permitido finalizar a venda com o campo de desconto de tabela de preço no item fora da política comercial.
     * @return boolean 
     */
    public function getSnPermFinDecTbItForaPol(){
        return ($this->DadosParametro['sn_perm_fin_dec_tb_it_fora_pol'] == 1);
    }
    
    public function getSnExportaPedidoAoFinalizar(){
        return ($this->DadosParametro['sn_exporta_pedido_ao_finalizar'] == 1);
    }
    
    public function getSnExpPedAoFinalizarCustom(){
        return ($this->DadosParametro['sn_exp_ped_ao_finalizar_custom'] == 1);
    }
    
    public function getSnEnviaEmailReprov(){
        return ($this->DadosParametro['sn_envia_email_reprov'] == 1);
    }
    
    public function getEmailEnvioReprov(){
        return $this->DadosParametro['email_envio_reprov'];
    }

    public function getSnEnviaEmailReduComis(){
        return ($this->DadosParametro['sn_envia_email_redu_comis'] == 1);
    }
    
    public function getEmailEnvioReduComis(){
        return $this->DadosParametro['email_envio_redu_comis'];
    }
    
    public function getSnGeraBonificVendaForaPol(){
        return ($this->DadosParametro['sn_gera_bonific_venda_fora_pol'] == 1);
    }
    
    public function getSnUtilizaRefEstoque(){
        return ($this->DadosParametro['sn_utiliza_ref_estoque'] == 1);        
    }
    
    public function getSnExibePctMaxDescItem(){
        return ($this->DadosParametro['sn_exibe_pct_max_desc_item'] == 1);        
    }
    
    public function getCampoAtualizarPasso1(){
        return $this->DadosParametro['campos_automaticos_conta_p1'];
    }
    
    public function getSnUsaBloqueioRepxFam(){
        $QryExisteRegras = query("SELECT numreg FROM is_param_representantexfamilia_comercial");
        return (farray($QryExisteRegras))?true:false;        
    }
    
    /**
     * 1 - Miniatura, 2- Normal, 3 - Não Exibe
     * @return int 
     */
    public function getModoImgProd(){
        return $this->DadosParametro['modo_img_prod'];
    }
    
    /*
     * Retorno de regras de cálculos
     */
    public function getPrecisaoCalculoIntermediarioMoedaPadrao(){
        return $this->DadosParametro['precisao_calc_int_mo_pad'];
    }

    public function getPrecisaoCalculoIntermediarioMoedaOriginal(){
        return $this->DadosParametro['precisao_calc_int_mo_orig'];
    }

    public function getTipoArredondamentoIntermediarioMoedaPadrao(){
        return $this->DadosParametro['modo_arred_calc_int_mo_pad'];
    }

    public function getTipoArredondamentoIntermediarioMoedaOriginal(){
        return $this->DadosParametro['modo_arred_calc_int_mo_orig'];
    }

    public function getPrecisaoCalculoFinalMoedaPadrao(){
        return $this->DadosParametro['precisao_calc_final_mo_pad'];
    }

    public function getPrecisaoCalculoFinalMoedaOriginal(){
        return $this->DadosParametro['precisao_calc_final_mo_orig'];
    }

    public function getTipoArredondamentoFinalMoedaPadrao(){
        return $this->DadosParametro['modo_arred_calc_final_mo_pad'];
    }

    public function getTipoArredondamentoFinalMoedaOriginal(){
        return $this->DadosParametro['modo_arred_calc_final_mo_orig'];
    }

    public function getPrecisaoCalculoUnitarioFinal(){
        return $this->DadosParametro['precisao_calc_final_conv'];
    }

    public function getTipoArredondamentoUnitarioFinal(){
        return $this->DadosParametro['modo_arred_calc_final_conv'];
    }

    public function getPrecisaoCalculoUnitarioFinalTotal(){
        return $this->DadosParametro['precisao_calc_final_tot_conv'];
    }

    public function getTipoArredondamentoUnitarioFinalTotal(){
        return $this->DadosParametro['modo_arred_calc_final_tot_conv'];
    }
}
?>