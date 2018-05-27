<?php
/*
 * class.Venda.Orcamento.php
 * Autor: Alex
 * 29/10/2010 11:34:00
 */
class Orcamento extends Venda{

    public function __construct($TipoVenda,$NumregVenda = NULL, $CarregaItens = true, $CarregaUsuario = true,$VisualizarRevisao = false){
        $this->ArrayDeParaCamposEspecificosTabelaVenda = array(
            'id_orcamento'                  => 'id_venda',
            'tp_orcamento'                  => 'tp_venda',
            'id_orcamento_cliente'          => 'id_venda_cliente',
            'id_orcamento_representante'    => 'id_venda_representante',
            'id_tp_orcamento'               => 'id_tp_venda',
            'id_situacao_orcamento'         => 'id_situacao_venda',
            'id_revisao_orcamento'          => 'id_revisao_venda',
            'dt_orcamento'                  => 'dt_venda',
            'dt_validade_orcamento'         => 'dt_validade_venda',
            'obs_orcamento'                 => 'obs_venda',
            'id_orcamento_erp'              => 'obs_venda',
            'id_orcamento_bonificacao'      => 'id_venda_bonificacao',
            'id_orcamento_origem_clone'     => 'id_venda_origem_clone'
        );
        parent::__construct($TipoVenda,$NumregVenda,$CarregaItens,$CarregaUsuario,$VisualizarRevisao);
    }

    public function InsereOrcamento(){
        $ArInsert = array(
            'dt_cadastro'               => date("Y-m-d"),
            'hr_cadastro'               => date("H:i:s"),
            'dt_orcamento'              => date("Y-m-d"),
            'id_usuario_cad'            => $_SESSION['id_usuario'],
            'dt_orcamento'              => date("Y-m-d"),
            'sn_digitacao_completa'     => 0,
            'id_pessoa'                 => 0,
            'id_tp_orcamento'           => 0,
            'id_situacao_orcamento'     => 0,
            'id_moeda'                  => 0,
            'vl_total_desconto'         => 0,
            'id_tp_preco'               => 0,
            'vl_total_bruto'            => 0,
            'vl_total_liquido'          => 0,
            'vl_total_ipi'              => 0,
            'vl_total_st'               => 0,
            'vl_total_icms'             => 0,

            'dt_validade_orcamento'     => $this->CalculaDataValidade(),

            'vl_total_frete'            => 0,
            'sn_em_aprovacao_comercial' => 0,
            'sn_avaliado_credito'       => 0,
            'sn_avaliado_comercial'     => 0,
            'sn_faturamento_parcial'    => 0,
            'sn_antecipa_entrega'       => 0,
            'sn_aprovado_credito'       => 0,
            'sn_aprovacao_parcial'      => 0,
            'sn_aprovado_comercial'     => 0,
            'id_destino_mercadoria'     => 0,
            'sn_importado_erp'          => 0,
            'sn_exportado_erp'          => 0,
            'id_ciclo'                  => $this->getVendaParametro()->getOporIdCicloVendaPadrao()
        );

        /* Aplicando tratamentos na array de insert */
        $ArInsert = $this->TrataArInsertVenda($ArInsert);

        $SqlInsert = AutoExecuteSql(TipoBancoDados,$this->TabelaVenda, $ArInsert, 'INSERT');
        $NumregNovoOrcamento = iquery($SqlInsert);
        $this->setNumregVenda($NumregNovoOrcamento);
        $this->CriaAtividadeEnvioOrcamento();
        return true;
    }

    public function CarregaDadosVendaBD(){
        $SqlVenda = "SELECT * FROM ".$this->getTabelaVenda()." WHERE numreg = ".$this->getNumregVenda();

        $QryVenda = query($SqlVenda);
        $ArVenda = farray($QryVenda);
        foreach($ArVenda as $k => $v){
            if(!is_numeric($k)){
                $this->DadosVenda[$k] = $v;
            }
        }
        $this->setVlTotalVendaBruto($ArVenda['vl_total_bruto']);
        $this->setVlTotalVendaliquido($ArVenda['vl_total_liquido']);
        $this->setVlTotalVendaIPI($ArVenda['vl_total_ipi']);
        $this->setVlTotalVendaST($ArVenda['vl_total_st']);
        $this->setVlTotalVenda($ArVenda['vl_total']);
        $this->setVlTotalFrete($ArVenda['vl_total_frete']);
        $this->setPesoTotalVenda($ArVenda['peso_total']);

        $this->DadosVenda = $this->encodeDeParaCamposValor($this->DadosVenda);

        /*
         * Carregando Pessoa
         */
        if(!empty($this->DadosVenda['id_pessoa'])){
            $this->Pessoa = new Pessoa($this->DadosVenda['id_pessoa']);
         }
        /*
         * Carregando contato
         */
        if(!empty($this->DadosVenda['id_contato'])){
            $this->Contato = new Contato($this->DadosVenda['id_contato']);
        }
    }

    /**
     * Função que remove uma determinada revisão do banco de dados (Use com cuidado!)
     * @param int $NumregRevisao
     */
    public function ApagaRevisaoBD($NumregRevisao){
        $SqlItemRevisao = "SELECT numreg FROM is_orcamento_item_revisao WHERE id_orcamento = '".$NumregRevisao."'";
        $QryItemRevisao = query($SqlItemRevisao);
        while($ArItemRevisao = farray($QryItemRevisao)){
            query("DELETE FROM is_orcamento_item_desconto_revisao WHERE id_orcamento_item = '".$ArItemRevisao['numreg']."'");
        }
        query("DELETE FROM is_orcamento_item_revisao WHERE id_orcamento = '".$NumregRevisao."'");
        query("DELETE FROM is_orcamento_representante_revisao WHERE id_orcamento = '".$NumregRevisao."'");
        query("DELETE FROM is_orcamento_revisao WHERE numreg = '".$NumregRevisao."'");
        return true;
    }

    public function CalculaDataValidade($Data=NULL){
        $DataVenda = ($Data == NULL)?$this->getDadosVenda('dt_venda'):$Data;
        $QtdeDiasValidade = getParametrosVenda('qtde_dias_validade_orcamento');
        $DataDeValidade = date("Y-m-d",strtotime($DataVenda." + ".$QtdeDiasValidade." days"));
        $this->setDadoVenda('dt_validade_venda', $DataDeValidade);
        return $DataDeValidade;
    }

    public function GravaDataValidadeBD(){
        $ArUpdateVenda['numreg']                = $this->getNumregVenda();
        $ArUpdateVenda['dt_validade_venda']     = $this->getDadosVenda('dt_validade_venda');

        $ArUpdateVenda = $this->decodeDeParaCamposValor($ArUpdateVenda);
        $SqlUpdateVenda = AutoExecuteSql(TipoBancoDados,$this->getTabelaVenda(),$ArUpdateVenda,'UPDATE',array('numreg'));
        if(query($SqlUpdateVenda)){
            return true;
        }
        else{
            $this->setMensagem('Erro de SQL atualizar data de validade.');
            return false;
        }
    }

    private function DeletePedido($NumregPedido,$SufixoTabela=''){
        $SqlDeleteDescontos = "DELETE FROM is_pedido_item_desconto".$SufixoTabela." WHERE id_pedido_item IN(SELECT numreg FROM is_pedido_item WHERE id_pedido = ".$NumregPedido.")";
        $SqlDeleteItens     = "DELETE FROM is_pedido_item".$SufixoTabela." WHERE id_pedido = ".$NumregPedido;
        $SqlDeleteVenda     = "DELETE FROM is_pedido".$SufixoTabela." WHERE numreg = ".$NumregPedido;

        query($SqlDeleteDescontos);
        query($SqlDeleteItens);
        query($SqlDeleteVenda);
    }
	
	public function TransformaCondPagtoEmPedidoBD($NumregPedido){
		$ArUpdateVendaCondPagto = array();
        $ArUpdateVendaCondPagto['id_orcamento'] 	= $this->getDadosVenda('numreg');
        $ArUpdateVendaCondPagto['id_pedido'] 		= $NumregPedido ;
        $SqlUpdateVendaCondPagto = query(AutoExecuteSql(TipoBancoDados,'c_coaching_inscricao_pagto_orcamento_pedido',$ArUpdateVendaCondPagto,'UPDATE',array('id_orcamento')));
	}
	
    public function TransformarEmPedidoBD(){
        $ArInsertPedido = $this->getDadosVenda();
        
        $ArInsertPedido['id_pedido_cliente'] = $this->getDadosVenda('id_venda_cliente');
        $ArInsertPedido['id_pedido_representante'] = $this->getDadosVenda('id_venda_representante');
        $ArInsertPedido['id_tp_pedido'] = $this->getDadosVenda('id_tp_venda');
        $ArInsertPedido['id_situacao_pedido'] = $this->getDadosVenda('id_situacao_venda');
        $ArInsertPedido['tp_pedido'] = $this->getDadosVenda('tp_venda');
        
        unset($ArInsertPedido['numreg']);
        unset($ArInsertPedido['id_revisao']);
        unset($ArInsertPedido['id_atividade_pai']);
        unset($ArInsertPedido['id_oportunidade_pai']);
        unset($ArInsertPedido['id_oportunidade_filha']);
        unset($ArInsertPedido['sn_gerou_pedido']);
        unset($ArInsertPedido['id_venda_cliente']);
        unset($ArInsertPedido['id_venda_representante']);
        unset($ArInsertPedido['id_tp_venda']);
        unset($ArInsertPedido['id_situacao_venda']);
        unset($ArInsertPedido['dt_venda']);
        unset($ArInsertPedido['dt_validade_venda']);
        unset($ArInsertPedido['id_venda_bonificacao']);
        unset($ArInsertPedido['tp_venda']);
        unset($ArInsertPedido['id_venda_origem_clone']);
        
        $ArInsertPedido['id_situacao_pedido']               = 1; // Fixo 1, pois sempre entrará em aberto
        $ArInsertPedido['sn_digitacao_completa']            = 1; // Fixo 1, pois o pedido já entrará como completo
        $ArInsertPedido['dt_pedido']                        = date("Y-m-d");
        $ArInsertPedido['dt_cadastro']                      = date("Y-m-d");
        $ArInsertPedido['dt_entrega']                       = $this->CalculaDataEntrega(date("Y-m-d"));
        $ArInsertPedido['sn_em_aprovacao_comercial']        = 0; //Fixo 0
        $ArInsertPedido['sn_avaliado_credito']              = 1; //Fixo 1
        $ArInsertPedido['sn_aprovado_credito']              = 1; //Fixo 1
        $ArInsertPedido['sn_aprovado_comercial']            = 1; //Fixo 1
        $ArInsertPedido['sn_passou_pelo_passo1']            = 1; //Fixo 1
        $ArInsertPedido['id_usuario_avaliador_comercial']   = $_SESSION['id_usuario'];
        $ArInsertPedido['dt_avaliacao_comercial']           = date("Y-m-d H:i:s");
        $ArInsertPedido['id_orcamento']                     = $this->getNumregVenda();
        $ArInsertPedido['sn_importado_erp']                 = 0;
        $ArInsertPedido['sn_exportado_erp']                 = 0;
        
        /* Removendo as colunas que existem na tabela orçamentos, mas não existema na pedidos */
        $ArInsertPedido = $this->RemoveCampoCustomInexistentesPedido($ArInsertPedido,'is_pedido');

        /*
         * Se não foi preenchido o número do pedido de cliente, gera um número automático
         */
        if(trim($this->getDadosVenda('id_venda_cliente')) == ''){
            $MaxId = uB::getProximoMaxId(1);
            $ArInsertPedido['id_pedido_cliente'] = getParametrosVenda('prefixo_numero_pedido').$MaxId;            
        }

        $SqlInsertPedido = AutoExecuteSql(TipoBancoDados,'is_pedido', $ArInsertPedido, 'INSERT');
        $QryInsertPedido = iquery($SqlInsertPedido);
        if(!$QryInsertPedido){
            $this->setErro(true);
            $this->setMensagem("Erro SQL Cabeçalho Pedido");
            return false;
        }
        $NumregPedido = $QryInsertPedido;

        /*
         * Gravando os itens
         */
        foreach($this->getItens() as $IndiceItem => $Item){
            /*
             * Se o item foi reprovado pelo cliente(perdido) pula para o próximo
             */
            if($Item->getItemPerdido()){
                continue;
            }

            $QryMaxSequencia        = query("SELECT MAX(id_sequencia) AS id_sequencia FROM is_pedido_item WHERE id_pedido = ".$NumregPedido);
            $ArMaxSequencia         = farray($QryMaxSequencia);
            $ProximoIdSequencia     = ($ArMaxSequencia['id_sequencia'] == '')?10:$ArMaxSequencia['id_sequencia'] + 10;

            $ArInsertPedidoItem = $Item->getDadosVendaItem();
            
            unset($ArInsertPedidoItem['numreg']);
            unset($ArInsertPedidoItem['id_motivo_perda']);
            unset($ArInsertPedidoItem['id_pessoa_concorrente']);
            unset($ArInsertPedidoItem['vl_perda']);
            unset($ArInsertPedidoItem['qtde_dias_perda']);
            unset($ArInsertPedidoItem['descricao_perda']);
            unset($ArInsertPedidoItem['sn_item_perdido']);
            unset($ArInsertPedidoItem['inc_cod_compl']);
            unset($ArInsertPedidoItem['inc_descricao']);
            unset($ArInsertPedidoItem['sn_item_comercial']);
            unset($ArInsertPedidoItem['id_venda']);          
            
            $ArInsertPedidoItem['id_pedido']                        = $NumregPedido;
            $ArInsertPedidoItem['id_sequencia']                     = $ProximoIdSequencia;
            $ArInsertPedidoItem['dt_cadastro']                      = date("Y-m-d");
            $ArInsertPedidoItem['id_usuario_cad']                   = $_SESSION['id_usuario'];
            
            /* Removendo as colunas que existem na tabela orçamentos, mas não existema na pedidos */
            $ArInsertPedidoItem = $this->RemoveCampoCustomInexistentesPedido($ArInsertPedidoItem,'is_pedido_item');

            $SqlInsertPedidoItem = AutoExecuteSql(TipoBancoDados,'is_pedido_item', $ArInsertPedidoItem, 'INSERT');
            /*
             * Gravando o item, se houver erro no SQL retorna false e deleta o pedido e todos os registros que foram criados
             */
            $QryInsertPedidoItem = iquery($SqlInsertPedidoItem);
            if(!$QryInsertPedidoItem){
                $this->setErro(true);
                $this->setMensagem($this->getMensagem()." Erro SQL Item: ".$Item->getDadosVendaItem('id_sequencia').' - '.$Item->getDadosVendaItem('id_produto')."<br />");
                $this->DeletePedido($NumregPedido);
                return false;
            }
            $NumregPedidoItem = $QryInsertPedidoItem;
            /*
             * Gravando Descontos
             */
            foreach($Item->getDescontos() as $IndiceCampoDesconto => $ArDadosCampoDesconto){
                $ArSqlInsertPedidoItemDesconto = array();
                $ArSqlInsertPedidoItemDesconto['id_pedido_item']    = $NumregPedidoItem;
                $ArSqlInsertPedidoItemDesconto['id_campo_desconto'] = $IndiceCampoDesconto;
                $ArSqlInsertPedidoItemDesconto['pct_desconto']      = $ArDadosCampoDesconto['pct_desconto'];

                $SqlInsertPedidoItemDesconto = AutoExecuteSql(TipoBancoDados,'is_pedido_item_desconto',$ArSqlInsertPedidoItemDesconto,'INSERT');
                $QryInsertPedidoItemDesconto = iquery($SqlInsertPedidoItemDesconto);
                if(!$QryInsertPedidoItemDesconto){
                    $this->setErro(true);
                    $this->setMensagem("Erro SQL Desconto Item: ".$Item->getDadosVendaItem('is_sequencia').' - '.$Item->getDadosVendaItem('id_produto')."<br />");
                    $this->DeletePedido($NumregPedido);
                    return false;
                }
            }
        }
        /*
         * Condição de Pagamento Especial
         */
        $SqlCondPagtoEspecial = "SELECT * FROM ".$this->getTabelaVendaCondPagtoEspecial()." WHERE ".$this->getCampoChaveTabelaVendaCondPagtoEspecial()." = ".$this->getNumregVenda()." ORDER BY id_sequencia";
        $QryCondPagtoEspecial = query($SqlCondPagtoEspecial);
        while($ArCondPagtoEspecial = farray($QryCondPagtoEspecial)){
            $ArSqlInsertPedidoCondPagtoEspecial = array();
            $ArSqlInsertPedidoCondPagtoEspecial['id_pedido']                        = $NumregPedido;
            $ArSqlInsertPedidoCondPagtoEspecial['id_sequencia']                     = $ArCondPagtoEspecial['id_sequencia'];
            $ArSqlInsertPedidoCondPagtoEspecial['dt_pagto']                         = $ArCondPagtoEspecial['dt_pagto'];
            $ArSqlInsertPedidoCondPagtoEspecial['pct_parcela']                      = $ArCondPagtoEspecial['pct_parcela'];
            $ArSqlInsertPedidoCondPagtoEspecial['vl_parcela']                       = $ArCondPagtoEspecial['vl_parcela'];
            $ArSqlInsertPedidoCondPagtoEspecial['qtde_dias_vencimento_parcela']     = $ArCondPagtoEspecial['qtde_dias_vencimento_parcela'];
            $ArSqlInsertPedidoCondPagtoEspecial['obs']                              = $ArCondPagtoEspecial['obs'];

            $SqlInsertPedidoCondPagtoEspecial = AutoExecuteSql(TipoBancoDados, 'is_pedido_cond_pagto_especial', $ArSqlInsertPedidoCondPagtoEspecial, 'INSERT');

            $QryInsertPedidoCondPagtoEspecial = iquery($SqlInsertPedidoCondPagtoEspecial);
            if(!$QryInsertPedidoCondPagtoEspecial){
                $this->setErro(true);
                $this->setMensagem($this->getMensagem()." Erro SQL COnd. Pagto. Especial: ".$ArSqlInsertPedidoCondPagtoEspecial['dt_pagto']."<br />");
                $this->DeletePedido($NumregPedido);
                return false;
            }
        }
        /*
         * Gravando Representantes
         */
        $QryRepresentantes = query("SELECT * FROM is_orcamento_representante WHERE id_orcamento = ".$this->getNumregVenda());
        while($ArRepresentante = farray($QryRepresentantes)){
            $ArSqlInsertPedidoRepresentante = array();
            $ArSqlInsertPedidoRepresentante['id_pedido']                    = $NumregPedido;
            $ArSqlInsertPedidoRepresentante['id_representante']             = $ArRepresentante['id_representante'];
            $ArSqlInsertPedidoRepresentante['pct_comissao']                 = $ArRepresentante['pct_comissao'];
            $ArSqlInsertPedidoRepresentante['sn_representante_principal']   = $ArRepresentante['sn_representante_principal'];
            $ArSqlInsertPedidoRepresentante['sn_alterado_manual']           = $ArRepresentante['sn_alterado_manual'];
            $ArSqlInsertPedidoRepresentante['id_tp_participacao']           = $ArRepresentante['id_tp_participacao'];
            $ArSqlInsertPedidoRepresentante['vl_comissao']                  = $ArRepresentante['vl_comissao'];

            $SqlInsertPedidoRepresentante = AutoExecuteSql(TipoBancoDados,'is_pedido_representante',$ArSqlInsertPedidoRepresentante,'INSERT');

            $QryInsertPedidoRepresentante = iquery($SqlInsertPedidoRepresentante);
            if(!$QryInsertPedidoRepresentante){
                $this->setErro(true);
                $this->setMensagem($this->getMensagem()." Erro SQL Representante: ".$ArRepresentante['id_representante']."<br />");
                $this->DeletePedido($NumregPedido);
                return false;
            }
        }
        $this->setDadoVenda('id_situacao_venda',3);
        $this->setDadoVenda('sn_gerou_pedido',1);
        $this->AtualizaDadosVendaBD();
        $this->setMensagem('Pedido Nº '.$ArInsertPedido['id_pedido_cliente'].' ('.$NumregPedido.') gerado com sucesso!');
        $this->FinalizaAtividadeFollowupOrcamento();
        
        /* Executando callback customizado */
        VendaCallBackCustom::ExecutaVenda($this, 'TransformarEmPedidoBD','Final',array('id_pedido' => $NumregPedido));
        /*
         * Retornando o número do pedido gerado
         */
        return $NumregPedido;
    }

    public function PerdeItem($IndiceItem,$Status,$MotivoPerda,$Concorrente,$Valor,$Justificativa){
            $Valor = str_replace(',','.',str_replace('.','',$Valor));
            if(!is_numeric($Valor)){
                $Valor = 0;
            }
            $ArKeyUpdateOrcamentoItem                                   =   array('numreg');
            $ArUpdateOrcamentoItem['numreg']                            =   $IndiceItem;
            $ArUpdateOrcamentoItem['sn_item_perdido']                   =   $Status;
            $ArUpdateOrcamentoItem['id_motivo_perda']                   =   $MotivoPerda;
            $ArUpdateOrcamentoItem['id_pessoa_concorrente']             =   $Concorrente;
            $ArUpdateOrcamentoItem['vl_perda']                          =   $Valor;
            $ArUpdateOrcamentoItem['descricao_perda']                   =   $Justificativa;
            $sql_perda_item = AutoExecuteSql(TipoBancoDados, 'is_orcamento_item', $ArUpdateOrcamentoItem, 'UPDATE', $ArKeyUpdateOrcamentoItem);
            $qry_perda_item = query($sql_perda_item);
            if($qry_perda_item) {
                $this->getItem($IndiceItem)->setDadoItem('sn_item_perdido',$Status);
                $this->getItem($IndiceItem)->setDadoItem('id_motivo_perda',$MotivoPerda);
                $this->getItem($IndiceItem)->setDadoItem('id_pessoa_concorrente',$Concorrente);
                $this->getItem($IndiceItem)->setDadoItem('vl_perda',$Valor);
                $this->getItem($IndiceItem)->setDadoItem('justificativa_reprov_com',$Justificativa);
                $this->CalculaTotaisVenda();
                $this->AtualizaTotaisVendaBD();
                if($Status == 0){
                    $this->setMensagem('Item restaurado ao orçamento com sucesso!');
                } else {
                    $this->setMensagem('Item configurado como perdido!');
                }
            } else {
                if($Status == 0){
                    $this->setMensagem('Erro ao configurar o item como ganho!');
                } else {
                    $this->setMensagem('Erro ao efetuar a perda do item!');
                }
            }
    }


    public function PerdeOrcamento($IdMotivoCancelamento){
        $this->setDadoVenda('sn_digitacao_completa', 1);
        $this->setDadoVenda('id_situacao_venda', 4);
        $this->setDadoVenda('sn_em_aprovacao_comercial', 0);
        $this->setDadoVenda('sn_avaliado_comercial', 0);
        $this->setDadoVenda('sn_aprovado_comercial', 0);
        $this->setDadoVenda('id_motivo_cancelamento', $IdMotivoCancelamento);
        $this->setDadoVenda('id_usuario_cancelamento', $_SESSION['id_usuario']);
        $this->setDadoVenda('dt_cancelamento', date("Y-m-d"));
        if($this->AtualizaDadosVendaBD()){
            $this->GravaLogBD(22,'Perda Orçamento');
            return true;
        }
        $this->AtualizaDadosVendaBD();
        $this->CompletaDigitacaoVendaBD();
        $this->FinalizaAtividadeFollowupOrcamento();
        $this->GeraAtualizaOportunidadePaiEFilha();
        return true;
    }

    protected function ValidaDadosParaTransformarEmPedido(){
        return true;
    }

    /**
     * Função para criar uma atividade de log quando o orçamento for incluído no sistema
     */
    public function CriaAtividadeEnvioOrcamento(){
        /* Se o parametro de controle de atividades para orçamentos estiver ativo */
        if($this->getVendaParametro()->getSnControlaAtividade()){
            /* Inserindo atividade */
            $ArSqlInsertAtividade = array();
            $ArSqlInsertAtividade['id_tp_atividade']        = '13';
            $ArSqlInsertAtividade['id_usuario_resp']        = $_SESSION['id_usuario'];
            $ArSqlInsertAtividade['assunto']                = 'Envio de Orçamento Nº '.$this->getNumregVenda();
            $ArSqlInsertAtividade['dt_inicio']              = date("Y-m-d");
            $ArSqlInsertAtividade['hr_inicio']              = date("H:i");
            $ArSqlInsertAtividade['dt_prev_fim']            = date("Y-m-d");
            $ArSqlInsertAtividade['hr_prev_fim']            = date("H:i");
            $ArSqlInsertAtividade['id_situacao']            = 1;
            $ArSqlInsertAtividade['id_situacao_orcamento']  = 1;
            $ArSqlInsertAtividade['id_orcamento']           = $this->getNumregVenda();

            $ArSqlInsertAtividade['id_usuario_cad']         = $_SESSION['id_usuario'];
            $ArSqlInsertAtividade['dt_cadastro']            = date("Y-m-d");
            $ArSqlInsertAtividade['hr_cadastro']            = date("H:i");

            $SqlInsertAtividade = AutoExecuteSql(TipoBancoDados, 'is_atividade', $ArSqlInsertAtividade, 'INSERT');
            query($SqlInsertAtividade);
        }
    }

    public function AtualizaAtividadeEnvioOrcamento(){
        /* Se o parametro de controle de atividades para orçamentos estiver ativo */
        if($this->getVendaParametro()->getSnControlaAtividade()){
            $ArSqlUpdateAtividade = array();
            $ArSqlUpdateAtividade['id_tp_atividade']        = '13';
            $ArSqlUpdateAtividade['id_orcamento']           = $this->getNumregVenda();
            $ArSqlUpdateAtividade['id_pessoa']              = $this->getDadosVenda('id_pessoa');
            $ArSqlUpdateAtividade['id_pessoa_contato']      = $this->getDadosVenda('id_contato');

            $SqlUpdateAtividade = AutoExecuteSql(TipoBancoDados, 'is_atividade', $ArSqlUpdateAtividade, 'UPDATE',array('id_orcamento','id_tp_atividade'));
            query($SqlUpdateAtividade);
        }
        /* Fim | Se o parametro de controle de atividades para orçamentos estiver ativo */
    }

    public function FinalizaAtividadeEnvioOrcamento(){
        /* Se o parametro de controle de atividades para orçamentos estiver ativo */
        if($this->getVendaParametro()->getSnControlaAtividade()){
            $ArSqlUpdateAtividade = array();
            $ArSqlUpdateAtividade['id_tp_atividade']        = '13';
            $ArSqlUpdateAtividade['id_orcamento']           = $this->getNumregVenda();
            $ArSqlUpdateAtividade['dt_real_fim']            = date("Y-m-d");
            $ArSqlUpdateAtividade['hr_real_fim']            = date("H:i");
            $ArSqlUpdateAtividade['id_situacao_orcamento']  = $this->getDadosVenda('id_situacao_venda');
            $ArSqlUpdateAtividade['id_situacao']            = 4;

            $SqlUpdateAtividade = AutoExecuteSql(TipoBancoDados, 'is_atividade', $ArSqlUpdateAtividade, 'UPDATE',array('id_orcamento','id_tp_atividade'));
            query($SqlUpdateAtividade);
        }
        /* Fim | Se o parametro de controle de atividades para orçamentos estiver ativo */
    }

    /**
     * Função para criar uma atividade de log quando o orçamento for incluído no sistema
     */
    public function CriaAtividadeFollowupOrcamento(){
        /* Se o parametro de controle de atividades para orçamentos estiver ativo */
        if($this->getVendaParametro()->getSnControlaAtividade()){
            /* Inserindo atividade */
            $ArSqlInsertAtividade = array();
            $ArSqlInsertAtividade['id_tp_atividade']        = '23';
            $ArSqlInsertAtividade['id_usuario_resp']        = $_SESSION['id_usuario'];
            $ArSqlInsertAtividade['assunto']                = 'Follow-up Orçamento Nº '.$this->getNumregVenda();
            $ArSqlInsertAtividade['id_pessoa']              = $this->getDadosVenda('id_pessoa');
            $ArSqlInsertAtividade['id_pessoa_contato']      = $this->getDadosVenda('id_contato');
            $ArSqlInsertAtividade['dt_inicio']              = date("Y-m-d");
            $ArSqlInsertAtividade['hr_inicio']              = date("H:i");
            $ArSqlInsertAtividade['dt_prev_fim']            = date("Y-m-d");
            $ArSqlInsertAtividade['hr_prev_fim']            = date("H:i");
            $ArSqlInsertAtividade['id_situacao']            = 1;
            $ArSqlInsertAtividade['id_situacao_orcamento']  = $this->getDadosVenda('id_situacao_venda');
            $ArSqlInsertAtividade['id_orcamento']           = $this->getNumregVenda();

            $ArSqlInsertAtividade['id_usuario_cad']         = $_SESSION['id_usuario'];
            $ArSqlInsertAtividade['dt_cadastro']            = date("Y-m-d");
            $ArSqlInsertAtividade['hr_cadastro']            = date("H:i");

            $SqlInsertAtividade = AutoExecuteSql(TipoBancoDados, 'is_atividade', $ArSqlInsertAtividade, 'INSERT');
            query($SqlInsertAtividade);
        }
    }

    public function FinalizaAtividadeFollowupOrcamento(){
        /* Se o parametro de controle de atividades para orçamentos estiver ativo */
        if($this->getVendaParametro()->getSnControlaAtividade()){
            $ArSqlUpdateAtividade = array();
            $ArSqlUpdateAtividade['id_tp_atividade']        = '23';
            $ArSqlUpdateAtividade['id_orcamento']           = $this->getNumregVenda();
            $ArSqlUpdateAtividade['dt_real_fim']            = date("Y-m-d");
            $ArSqlUpdateAtividade['hr_real_fim']            = date("H:i");
            $ArSqlUpdateAtividade['id_situacao_orcamento']  = $this->getDadosVenda('id_situacao_venda');
            $ArSqlUpdateAtividade['id_situacao']            = 4;

            $SqlUpdateAtividade = AutoExecuteSql(TipoBancoDados, 'is_atividade', $ArSqlUpdateAtividade, 'UPDATE',array('id_orcamento','id_tp_atividade'));
            query($SqlUpdateAtividade);
        }
        /* Fim | Se o parametro de controle de atividades para orçamentos estiver ativo */
    }

    public function VerificaSeExisteOportunidadePai(){
        $SqlVerifica = "SELECT COUNT(*) AS CNT FROM is_oportunidade WHERE id_orcamento_filho = '".$this->getNumregVenda()."'";
        $QryVerifica = query($SqlVerifica);
        $ArVerifica = farray($QryVerifica);
        if($ArVerifica['CNT'] >= 1){
            return true;
        }
        else{
            return false;
        }
    }

    public function VerificaSeExisteOportunidadeFilha(){
        $SqlVerifica = "SELECT COUNT(*) AS CNT FROM is_oportunidade WHERE id_orcamento_pai = '".$this->getNumregVenda()."'";
        $QryVerifica = query($SqlVerifica);
        $ArVerifica = farray($QryVerifica);
        if($ArVerifica['CNT'] >= 1){
            return true;
        }
        else{
            return false;
        }
    }

    public function GeraAtualizaOportunidadeFilha(){
        $Acao = 1; /* Incluir */
        if($this->getDadosVenda('id_oportunidade_pai') != ''){
            return false;
        }
        elseif($this->VerificaSeExisteOportunidadeFilha()){
            $OportunidadeFilha = new Oportunidade($this->getDadosVenda('id_oportunidade_filha'));
            $Acao = 2; /* Alterar */
        }
        else{
            $OportunidadeFilha = new Oportunidade(NULL);
        }
        if($OportunidadeFilha === false){
            return false;
        }
        $this->OportunidadeFilha = $OportunidadeFilha;
        $this->NumeroOportunidadeGerada = $this->OportunidadeFilha->getNumregOportunidade();

        if($Acao == 1){ /* Se for incluir */
            /* Atualizando o cabeçalho do orcamento com cód da oportunidade gerada */
            $ArSqlUpdateVenda = array();
            $ArSqlUpdateVenda['numreg']                = $this->getNumregVenda();
            $ArSqlUpdateVenda['id_oportunidade_filha'] = $this->OportunidadeFilha->getNumregOportunidade();
            $SqlUpdateVenda = AutoExecuteSql(TipoBancoDados, $this->getTabelaVenda(), $ArSqlUpdateVenda, 'UPDATE',array('numreg'));
            if(query($SqlUpdateVenda)){
                $this->setDadoVenda('id_oportunidade_filha', $ArSqlUpdateVenda['id_oportunidade_filha']);
            }
            else{
                return false;
            }
            $IdOporCiclo = ($this->getDadosVenda('id_ciclo') == '')?$this->getVendaParametro()->getOporIdCicloVendaPadrao():$this->getDadosVenda('id_ciclo');
            $IdOporFase = ($this->getDadosVenda('id_fase') == '')?$this->getVendaParametro()->getOporIdFaseVendaPadrao():$this->getDadosVenda('id_fase');
            
            /* Definindo campos que somente serão preenchidos na inclusão */
            $OportunidadeFilha->setDadoOportunidade('assunto', 'Oportunidade gerada pelo orçamento Nº '.$this->getNumregVenda());
            $OportunidadeFilha->setDadoOportunidade('id_opor_ciclo', $IdOporCiclo);
            $OportunidadeFilha->setDadoOportunidade('id_opor_ciclo_fase', $IdOporFase);
        }
        if($this->getDadosVenda('sn_gerou_pedido') == '1' || $this->getDadosVenda('id_situacao_venda') == '4'){
            $OportunidadeFilha->setDadoOportunidade('dt_real_fim', date("Y-m-d"));
        }
        $OportunidadeFilha->setDadoOportunidade('id_orcamento_pai', $this->getNumregVenda());
        $OportunidadeFilha->setDadoOportunidade('id_pessoa', $this->getIdPessoa());
        $OportunidadeFilha->setDadoOportunidade('id_origem', $this->getDadosVenda('id_origem'));
        $OportunidadeFilha->setDadoOportunidade('id_pessoa_indic', $this->getDadosVenda('id_pessoa_indicacao'));
        $OportunidadeFilha->setDadoOportunidade('id_usuario_resp', $this->getDadosVenda('id_usuario_cad'));
        $OportunidadeFilha->setDadoOportunidade('id_usuario_gestor', $this->getDadosVenda('id_representante_pessoa'));
        $OportunidadeFilha->setDadoOportunidade('id_representante_principal', $this->getDadosVenda('id_representante_principal'));
        $OportunidadeFilha->setDadoOportunidade('id_pessoa_contato', $this->getDadosVenda('id_contato'));
        $OportunidadeFilha->setDadoOportunidade('id_tab_preco', $this->getDadosVenda('id_tab_preco'));
        $OportunidadeFilha->setDadoOportunidade('id_cond_pagto', $this->getDadosVenda('id_cond_pagto'));
        $OportunidadeFilha->setDadoOportunidade('id_situacao', $this->getDadosVenda('id_situacao_venda'));
        $OportunidadeFilha->setDadoOportunidade('dt_inicio', date("Y-m-d"));
        $OportunidadeFilha->setDadoOportunidade('dt_prev_fim', $this->getDadosVenda('dt_validade_venda'));
        $OportunidadeFilha->setDadoOportunidade('valor', $this->getVlTotalVendaLiquido());
        $OportunidadeFilha->setDadoOportunidade('obs', $this->getDadosVenda('obs'));

        $OportunidadeFilha->CalculaPercentualProbabilidade();
        VendaCallBackCustom::ExecutaVenda($this, 'GeraAtualizaOportunidadeFilha', 'AntesAtualizarBD', array('objOportunidade' => $OportunidadeFilha));
        $Retorno = $OportunidadeFilha->AtualizaDadosOportunidadeBD();
        /* Executando callback customizado */
        VendaCallBackCustom::ExecutaVenda($this, 'GeraAtualizaOportunidadeFilha', 'Final', array('Retorno' => $Retorno,'objOportunidade' => $OportunidadeFilha));        
        return $Retorno;
    }

    public function AtualizaOportunidadePai(){
        if($this->getDadosVenda('id_oportunidade_pai') == ''){
            return false;
        }
        $OportunidadePai = new Oportunidade($this->getDadosVenda('id_oportunidade_pai'));

        $this->OportunidadePai = $OportunidadePai;

        if($this->getDadosVenda('sn_gerou_pedido') == '1'){
            $OportunidadePai->setDadoOportunidade('dt_real_fim', date("Y-m-d"));
        }

        $OportunidadePai->setDadoOportunidade('id_orcamento_filho', $this->getNumregVenda());
        $OportunidadePai->setDadoOportunidade('id_pessoa', $this->getIdPessoa());
        $OportunidadePai->setDadoOportunidade('id_pessoa_contato', $this->getDadosVenda('id_contato'));
        $OportunidadePai->setDadoOportunidade('id_origem', $this->getDadosVenda('id_origem'));
        $OportunidadePai->setDadoOportunidade('id_pessoa_indic', $this->getDadosVenda('id_pessoa_indicacao'));
        $OportunidadePai->setDadoOportunidade('id_usuario_resp', $this->getDadosVenda('id_usuario_cad'));
        $OportunidadePai->setDadoOportunidade('id_usuario_gestor', $this->getDadosVenda('id_representante_pessoa'));
        $OportunidadePai->setDadoOportunidade('id_representante_principal', $this->getDadosVenda('id_representante_principal'));
        $OportunidadePai->setDadoOportunidade('id_tab_preco', $this->getDadosVenda('id_tab_preco'));
        $OportunidadePai->setDadoOportunidade('id_cond_pagto', $this->getDadosVenda('id_cond_pagto'));
        $OportunidadePai->setDadoOportunidade('id_situacao', $this->getDadosVenda('id_situacao_venda'));
        $OportunidadePai->setDadoOportunidade('dt_inicio', date("Y-m-d"));
        $OportunidadePai->setDadoOportunidade('dt_prev_fim', $this->getDadosVenda('dt_validade_venda'));
        $OportunidadePai->setDadoOportunidade('valor', $this->getVlTotalVendaLiquido());
        $OportunidadePai->setDadoOportunidade('obs', $this->getDadosVenda('obs'));

        $OportunidadePai->CalculaPercentualProbabilidade();
        return $OportunidadePai->AtualizaDadosOportunidadeBD();
    }

    public function AtualizaItensOportunidadeFilha(){
        /* Apagando todos os itens */
        $this->OportunidadeFilha->DeletaTodosItens(true);
        /* Adicionando os itens */
        $Itens = $this->getItens();
        foreach($Itens as $IndiceItem => $Item){
            $ArDadosOportunidadeFilhaItem = array();
            $ArDadosOportunidadeFilhaItem['id_produto']  = $Item->getDadosVendaItem('id_produto');
            $ArDadosOportunidadeFilhaItem['outro']       = $Item->getDadosVendaItem('inc_descricao');
            $ArDadosOportunidadeFilhaItem['qtde']        = $Item->getDadosVendaItem('total_unidades');
            $ArDadosOportunidadeFilhaItem['valor']       = $Item->getDadosVendaItem('vl_unitario_convertido');
            $ArDadosOportunidadeFilhaItem['pct_desc']    = $Item->getDadosVendaItem('pct_desconto_total');
            $ArDadosOportunidadeFilhaItem['valor_total']    = $Item->getDadosVendaItem('vl_total_liquido');
            if($Item->getItemComercial()){ /* Se é um item comercial */
                $ArDadosOportunidadeFilhaItem['id_fornecedor']    = $Item->getProduto()->getDadosProduto('id_fornecedor');
                $ArDadosOportunidadeFilhaItem['id_linha']    = $Item->getProduto()->getDadosProduto('id_linha');
                $ArDadosOportunidadeFilhaItem['id_familia_comercial']    = $Item->getProduto()->getDadosProduto('id_familia_comercial');
            }
            if(!$this->OportunidadeFilha->AdicionaItem($ArDadosOportunidadeFilhaItem)){
                return false;
            }
        }
        return true;
    }

    public function AtualizaItensOportunidadePai(){
        /* Apagando todos os itens */
        $this->OportunidadePai->DeletaTodosItens(true);
        /* Adicionando os itens */
        $Itens = $this->getItens();
        foreach($Itens as $IndiceItem => $Item){
            $ArDadosOportunidadePaiItem = array();
            $ArDadosOportunidadePaiItem['id_produto']  = $Item->getDadosVendaItem('id_produto');
            $ArDadosOportunidadePaiItem['outro']       = $Item->getDadosVendaItem('inc_descricao');
            $ArDadosOportunidadePaiItem['qtde']        = $Item->getDadosVendaItem('total_unidades');
            $ArDadosOportunidadePaiItem['valor']       = $Item->getDadosVendaItem('vl_unitario_convertido');
            $ArDadosOportunidadePaiItem['pct_desc']    = $Item->getDadosVendaItem('pct_desconto_total');
            $ArDadosOportunidadePaiItem['valor_total']    = $Item->getDadosVendaItem('vl_total_liquido');
            if($Item->getItemComercial()){ /* Se é um item comercial */
                $ArDadosOportunidadePaiItem['id_fornecedor']        = $Item->getProduto()->getDadosProduto('id_fornecedor');
                $ArDadosOportunidadePaiItem['id_linha']             = $Item->getProduto()->getDadosProduto('id_linha');
                $ArDadosOportunidadePaiItem['id_familia_comercial'] = $Item->getProduto()->getDadosProduto('id_familia_comercial');
            }
            if(!$this->OportunidadePai->AdicionaItem($ArDadosOportunidadePaiItem)){
                return false;
            }
        }
        return true;
    }

    public function GeraAtualizaOportunidadePaiEFilha(){
        /*
         * Tratamento para oportunidade filha
         */
        if($this->getVendaParametro()->getSnOrcamentoGeraOportunidade() && $this->getDadosVenda('id_oportunidade_pai') == ''){/* Se o orçamento gera oportunidade */
            $Acao = ($this->getDadosVenda('id_oportunidade_filha') == '')?1:2;
            if($this->GeraAtualizaOportunidadeFilha() && $this->AtualizaItensOportunidadeFilha()){ /* Se não houver erros */
                if($Acao == 1){
                    $this->setMensagem(' Gerada oportunidade Nº '.$this->getNumeroOportunidadeFilhaGerada().'.');
                }
                else{
                    $this->setMensagem(' Oportunidade Nº '.$this->getDadosVenda('id_oportunidade_filha').' atualizada.');
                }
            }
            else{
                $this->setMensagem(' Erro ao gerar/atualizar a oportunidade.');
                if($this->Debug === true){
                    $this->setMensagem($this->OportunidadeFilha->getMensagem());
                }
            }
        }
        /*
         * Tratamento para oportunidade pai
         */
        if($this->getDadosVenda('id_oportunidade_pai') != ''){
            if($this->AtualizaOportunidadePai() && $this->AtualizaItensOportunidadePai()){ /* Se não houver erros */
                $this->setMensagem(' Oportunidade Nº '.$this->getDadosVenda('id_oportunidade_pai').' atualizada.');
            }
            else{
                $this->setMensagem(' Erro ao atualizar a oportunidade.');
                if($this->Debug === true){
                    $this->setMensagem($this->OportunidadePai->getMensagem());
                }
            }
        }
    }

    public function getNumeroOportunidadeFilhaGerada(){
        return $this->NumeroOportunidadeGerada;
    }
    
    private function RemoveCampoCustomInexistentesPedido($ArInsert,$Tabela){
        $ArrayCamposPedido = array();
        if(TipoBancoDados == 'mysql'){
            $SqlCamposPedido = "SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = database(   ) AND TABLE_NAME = '".$Tabela."'";
            $QryCamposPedido = query($SqlCamposPedido);
            while($ArCamposPedido = farray($QryCamposPedido)){
                $ArrayCamposPedido[] = $ArCamposPedido['COLUMN_NAME'];
            }            
        }
        foreach($ArInsert as $Campo => $Valor){
            if(!array_search($Campo, $ArrayCamposPedido)){
                unset($ArInsert[$Campo]);
            }
        }
        return $ArInsert;
    }
}
?>