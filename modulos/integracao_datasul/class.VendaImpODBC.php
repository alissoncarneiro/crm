<?php

/*
 * class.VendaImpODBC.php
 * Autor: Alex
 * 10/08/2011 12:34:27
 */
class VendaImpODBC{
    private $CnxODBC;
    protected $NumregPedido;
    protected $NomeAbrev;
    protected $NrPedcli;
    protected $DadosPedidoCRM;
    protected $DadosPedidoERP;
    private $ErroImportacao = false;
    private $Mensagem = array();
    
    private $ArItensProcessadosNumreg = array();
    private $ArRepresentantesProcessadosNumreg = array();
    
    public function __construct($NumregPedido,$CnxODBC){
        $this->NumregPedido = $NumregPedido;
        $this->CnxODBC = $CnxODBC;
        $this->CarregaDadosPedidoCRM();
    }

    protected function setMensagem($Mensagem){
        $this->Mensagem[] = $Mensagem;
    }

    public function getMensagem($RetornaEmArray=false,$Separador = ' | '){
        if($RetornaEmArray == true){
            return $this->Mensagem;
        }
        return implode($Separador,$this->Mensagem);
    }

    public function DefineNomeAbrevNrPedcli(){
        $QryPedidoCRM = query("SELECT t1.*,t2.fantasia_apelido FROM is_pedido t1 INNER JOIN is_pessoa t2 ON t1.id_pessoa = t2.numreg WHERE t1.numreg = '".$this->NumregPedido."'");
        $ArPedidoCRM = farray($QryPedidoCRM);
        foreach($ArPedidoCRM as $Coluna => $Valor){
            if(!is_int($Coluna)){
                $this->DadosPedidoCRM[$Coluna] = $Valor;
            }
        }
        if($ArPedidoCRM['fantasia_apelido'] == '' || $ArPedidoCRM['id_pedido_cliente'] == ''){
            $this->setMensagem('Nome Abreviado e/ou Pedido do Cliente vazio(s).');
            return false;
        }
        $this->NomeAbrev = $ArPedidoCRM['fantasia_apelido'];
        $this->NrPedcli = $ArPedidoCRM['id_pedido_cliente'];
        return true;
    }
    
    public function CarregaDadosPedidoCRM(){
        $QryPedidoCRM = query("SELECT * FROM is_pedido WHERE numreg = '".$this->NumregPedido."'");
        $ArPedidoCRM = farray($QryPedidoCRM);
        foreach($ArPedidoCRM as $Coluna => $Valor){
            if(!is_int($Coluna)){
                $this->DadosPedidoCRM[$Coluna] = $Valor;
            }
        }
    }

    public function ImportaCabecalho(){
        $SqlPedidoERP = "SELECT
                            \"nr-pedido\",
                            \"nr-pedcli\",
                            \"nome-abrev\",
                            \"cod-sit-ped\",
                            \"cod-cond-pag\",
                            \"nat-operacao\",
                            \"nome-transp\",
                            \"nr-tabpre\",
                            \"val-pct-desconto-tab-preco\",
                            \"des-pct-desconto-inform\",
                            \"perc-desco1\",
                            \"cod-sit-aval\",
                            \"observacoes\",
                            \"cond-espec\",
                            \"cond-redespa\",
                            \"tp-preco\",
                            \"vl-tot-ped\",
                            \"vl-liq-ped\"
                        FROM
                            pub.\"ped-venda\"
                        WHERE
                            \"nome-abrev\" = '".TrataApostrofoBD($this->NomeAbrev)."'
                        AND
                            \"nr-pedcli\" = '".$this->NrPedcli."'";
        $QryPedidoERP = odbc_exec($this->CnxODBC,$SqlPedidoERP);
        $this->DadosPedidoERP = odbc_fetch_array($QryPedidoERP);
        
        if($this->DadosPedidoERP['cod-cond-pag'] == '0'){
            $IdCondPagto = 0;
        }
        else{
            $IdCondPagto = $this->DeparaCodigoCRMERP('is_cond_pagto', 'id_cond_pagto_erp', $this->DadosPedidoERP['cod-cond-pag']);
            if($IdCondPagto == ''){
                $this->setMensagem('Relacionamento de Cond. Pagto. com CRM não encontrado('.$this->DadosPedidoERP['cod-cond-pag'].').');
                $this->ErroImportacao = true;
                return false;
            }
        }

        $IdSituacaoPedido = $this->DeparaCodigoCRMERP('is_situacao_pedido', 'id_situacao_pedido_erp', $this->DadosPedidoERP['cod-sit-ped']);
        if($IdSituacaoPedido == ''){
            $this->setMensagem('Relacionamento de Situação. com CRM não encontrado('.$this->DadosPedidoERP['cod-sit-ped'].').');
            $this->ErroImportacao = true;
            return false;
        }

        $IdCFOP = $this->DeparaCodigoCRMERP('is_cfop', 'id_cfop_erp', $this->DadosPedidoERP['nat-operacao']);
        if($IdCFOP == ''){
            $this->setMensagem('Relacionamento de CFOP com CRM não encontrado('.$this->DadosPedidoERP['nat-operacao'].').');
            $this->ErroImportacao = true;
            return false;
        }

        $IdTransportadora = $this->DeparaCodigoCRMERP('is_transportadora', 'nome_abrev_transportadora', $this->DadosPedidoERP['nome-transp']);
        if($IdTransportadora == ''){
            $this->setMensagem('Relacionamento de Transportadora com CRM não encontrado('.$this->DadosPedidoERP['nome-transp'].').');
            $this->ErroImportacao = true;
            return false;
        }

        $IdTabPreco = $this->DeparaCodigoCRMERP('is_tab_preco', 'id_tab_preco_erp', $this->DadosPedidoERP['nr-tabpre']);
        if($IdTransportadora == ''){
            $this->setMensagem('Relacionamento de Tab. Preço com CRM não encontrado('.$this->DadosPedidoERP['nr-tabpre'].').');
            $this->ErroImportacao = true;
            return false;
        }

        if(!$this->GeraRevisao()){
            return false;
        }

        $ArSqlPedido = array();
        $ArSqlPedido['numreg']                  = $this->DadosPedidoCRM['numreg'];
        $ArSqlPedido['id_situacao_pedido']      = $IdSituacaoPedido;
        $ArSqlPedido['id_cond_pagto']           = $IdCondPagto;
        $ArSqlPedido['id_cfop']                 = $IdCFOP;
        $ArSqlPedido['obs']                     = $this->DadosPedidoERP['cond-espec'];
        $ArSqlPedido['obs_nf']                  = $this->DadosPedidoERP['observacoes'];
        $ArSqlPedido['id_transportadora']       = $IdTransportadora;
        $ArSqlPedido['id_tp_preco']             = $this->DadosPedidoERP['tp-preco'];
        $ArSqlPedido['id_tab_preco']            = $IdTabPreco;

        $ArSqlPedido['vl_total_liquido']        = $this->DadosPedidoERP['vl-liq-ped'];

        $ArSqlPedido['pct_desconto_tab_preco']  = $this->DadosPedidoERP['val-pct-desconto-tab-preco'];
        $ArSqlPedido['pct_desconto_informado']  = TrataFloatPost($this->DadosPedidoERP['des-pct-desconto-inform']);
        $ArSqlPedido['pct_desconto_pessoa']     = $this->DadosPedidoERP['perc-desco1'];
        
        if($this->DadosPedidoERP['vl-tot-ped'] > 0){
            $ArSqlPedido['vl_total'] = $this->DadosPedidoERP['vl-tot-ped'];
        }

        /* Tratamento de crédito */
        switch($this->DadosPedidoERP['cod-sit-aval']){
            case '1': /* Não Avaliado */
                $ArSqlPedido['sn_avaliado_credito'] = '0';
                $ArSqlPedido['sn_aprovado_credito'] = '0';
                break;
            case '2': /* Avaliado */
                $ArSqlPedido['sn_avaliado_credito'] = '0';
                $ArSqlPedido['sn_aprovado_credito'] = '0';
                break;
            case '3': /* Aprovado */
                $ArSqlPedido['sn_avaliado_credito'] = '1';
                $ArSqlPedido['sn_aprovado_credito'] = '1';
                break;
            case '4': /* Não Aprovado */
                $ArSqlPedido['sn_avaliado_credito'] = '1';
                $ArSqlPedido['sn_aprovado_credito'] = '0';
                break;
            case '5': /* Pendente de Informação */
                $ArSqlPedido['sn_avaliado_credito'] = '1';
                $ArSqlPedido['sn_aprovado_credito'] = '0';
                break;
        }
        
        $SqlPedido = AutoExecuteSql(TipoBancoDados,'is_pedido',$ArSqlPedido,'UPDATE',array('numreg'));
        /* Query UPDATE do Pedido no CRM */
        $QryUpdatePedido = query($SqlPedido);
        pre($SqlPedido);
        if(!$QryUpdatePedido){
            $this->setMensagem('Erro de SQL ao atualizar dados do cabeçalho. Pedido:'.$this->NumregPedido.'. SQL('.$SqlPedido.')');
            return false;
        }
        return true;
    }
    
    public function ImportaItens(){
        
        $SqlItensERP = "SELECT \"it-codigo\",
                                \"nr-sequencia\",
                                \"it-codigo\",
                                \"tp-preco\",
                                \"vl-pretab\",
                                \"vl-preori\",
                                \"vl-preuni\",
                                \"vl-tot-it\",
                                \"vl-liq-it\",
                                \"aliquota-ipi\",
                                \"nat-operacao\",
                                \"qt-un-fat\",
                                \"qt-pedida\",
                                \"qt-atendida\",
                                \"cod-sit-item\",
                                \"des-un-medida\",
                                \"val-ipi\",
                                \"dec-ftconv-unest\",
                                \"num-casa-dec-unest\",
                                \"val-pct-desconto-tab-preco\",
                                \"des-pct-desconto-inform\",
                                \"observacao\"
                            FROM pub.\"ped-item\" WHERE \"nome-abrev\" = '".TrataApostrofoBD($this->NomeAbrev)."' AND \"nr-pedcli\" = '".TrataApostrofoBD($this->NrPedcli)."' ORDER BY \"nr-sequencia\" ASC";
        $QryItensERP = odbc_exec($this->CnxODBC,$SqlItensERP);
        while($ArItemERP = odbc_fetch_array($QryItensERP)){
            $IdProduto = $this->DeparaCodigoCRMERP('is_produto', 'id_produto_erp', $ArItemERP['it-codigo']);
            if($IdProduto == ''){
                $this->setMensagem('Relacionamento de Produto com CRM não encontrado('.$ArItemERP['it-codigo'].').');
                $this->ErroImportacao = true;
                return false;
            }
            
            $IdUnidMedida = $this->DeparaCodigoCRMERP('is_unid_medida', 'id_unid_medida_erp', $ArItemERP['des-un-medida']);
            if(UnidMedida == ''){
                $this->setMensagem('Relacionamento de Unid. Medida com CRM não encontrado('.$ArItemERP['des-un-medida'].').');
                $this->ErroImportacao = true;
                return false;
            }
            
            $IdCFOPItem = $this->DeparaCodigoCRMERP('is_cfop', 'id_cfop_erp', $ArItemERP['nat-operacao']);
            if($IdCFOPItem == ''){
                $this->setMensagem('Relacionamento de Natureza de Operação com CRM não encontrado('.$ArItemERP['nat-operacao'].').');
                $this->ErroImportacao = true;
                return false;
            }
            
            $ArSqlPedidoItem = array();
            $ArSqlPedidoItem['id_produto']                      = $IdProduto;
            $ArSqlPedidoItem['id_situacao_item']                = $ArItemERP['cod-sit-item'];
            $ArSqlPedidoItem['id_sequencia']                    = $ArItemERP['nr-sequencia'];
            $ArSqlPedidoItem['id_tp_preco']                     = $ArItemERP['tp-preco'];
            $ArSqlPedidoItem['qtde']                            = $ArItemERP['qt-un-fat'];
            $ArSqlPedidoItem['vl_unitario_com_descontos']       = $ArItemERP['vl-preuni'];
            $ArSqlPedidoItem['vl_total_bruto']                  = $ArItemERP['vl-tot-it'];
            $ArSqlPedidoItem['vl_total_liquido']                = $ArItemERP['vl-liq-it'];
            $ArSqlPedidoItem['pct_aliquota_ipi']                = $ArItemERP['aliquota-ipi'];
            $ArSqlPedidoItem['id_cfop']                         = $IdCFOPItem;
            $ArSqlPedidoItem['qtde_faturada']                   = $ArItemERP['qt-atendida'];
            $ArSqlPedidoItem['id_unid_medida']                  = $IdUnidMedida;
            $ArSqlPedidoItem['vl_total_ipi']                    = $ArItemERP['val-ipi'];
            $ArSqlPedidoItem['qtde_por_unid_medida']            = 1;
            $ArSqlPedidoItem['total_unidades']                  = $ArItemERP['qt-pedida'];
            $ArSqlPedidoItem['vl_unitario_base_calculo']        = $ArItemERP['vl-pretab'];
            $ArSqlPedidoItem['vl_unitario_convertido']          = $ArItemERP['vl-preuni'];
            $ArSqlPedidoItem['pct_desconto_tab_preco']          = $ArItemERP['val-pct-desconto-tab-preco'];
            $ArSqlPedidoItem['qtde_por_qtde_informada']         = 1;
            $ArSqlPedidoItem['qtde_base_calculo']               = $ArItemERP['qt-un-fat'];
            $ArSqlPedidoItem['obs']                             = $ArItemERP['observacao'];
            
            if($ArItemERP['dec-ftconv-unest'] > 1){
                $ArSqlPedidoItem['qtde_por_unid_medida'] = CalculaFatorConversaoDatasul($ArItemERP['dec-ftconv-unest'],$ArItemERP['num-casa-dec-unest']);
            }   
            
            $SqlPedidoItemCRM = "SELECT numreg,id_sequencia FROM is_pedido_item WHERE id_pedido = ".$this->NumregPedido." AND id_sequencia = '".$ArItemERP['nr-sequencia']."'";
            $QryPedidoItemCRM = query($SqlPedidoItemCRM);
            $NumrowsPedidoItemCRM = numrows($QryPedidoItemCRM);
            if($NumrowsPedidoItemCRM == 1){
                $ArPedidoItemCRM = farray($QryPedidoItemCRM);
                
                $NumregItem = $ArPedidoItemCRM['numreg'];
                
                $ArSqlPedidoItem['numreg'] = $ArPedidoItemCRM['numreg'];
                
                $SqlPedidoItem = AutoExecuteSql(TipoBancoDados,'is_pedido_item',$ArSqlPedidoItem,'UPDATE',array('numreg'));
                $QryPedidoItem = query($SqlPedidoItem);
                pre($SqlPedidoItem);
                if(!$QryPedidoItem){
                    $this->setMensagem('Erro de sql ao atualizar item do pedido: Item: '.$ArSqlPedidoItem['id_sequencia'].' - '.$ArItemERP['it-codigo']);
                    $this->ErroImportacao = true;
                    return false;
                }
            }
            else{
                $ArSqlPedidoItem['id_pedido']                   = $this->NumregPedido;
                $ArSqlPedidoItem['dt_cadastro']                 = date("Y-m-d");
                $ArSqlPedidoItem['id_usuario_cad']              = 9999;
                $ArSqlPedidoItem['vl_cotacao']                  = 1;
                $ArSqlPedidoItem['vl_unitario_tabela_original'] = $ArItemERP['vl-pretab'];

                $SqlPedidoItem = AutoExecuteSql(TipoBancoDados,'is_pedido_item',$ArSqlPedidoItem,'INSERT');
                $QryPedidoItem = iquery($SqlPedidoItem);
                pre($SqlPedidoItem);
                if(!$QryPedidoItem){
                    $this->setMensagem('Erro de sql ao inserir item do pedido: Item: '.$ArSqlPedidoItem['id_sequencia'].' - '.$ArItemERP['it-codigo']);
                    $this->ErroImportacao = true;
                    return false;
                }
                $NumregItem = $QryPedidoItem;
            }
            $this->ArItensProcessadosNumreg[] = $NumregItem;
            
            if(!$this->ImportaDescontos($NumregItem,$ArItemERP)){
                return false;
            }
        }
        if(!$this->DeletaItensNaoProcessados()){
            return false;
        }
        return true;
    }
    
    public function ImportaDescontos($NumregItem,$ArItemERP){
        $ArDescontos = explode('+',$ArItemERP['des-pct-desconto-inform']);
        if(count($ArDescontos) > 0){
            foreach($ArDescontos as $Key => $Desconto){
                $ArDescontos[$Key] = ($Desconto == '')?0:TrataFloatPost($Desconto);
            }
            $SqlCamposDescontoCRM = "SELECT numreg FROM is_param_campo_desconto WHERE sn_ativo = 1 ORDER BY numreg";
            $QryCamposDescontoCRM = query($SqlCamposDescontoCRM);
            if(count($ArDescontos) > numrows($QryCamposDescontoCRM)){
                $DescricaoDetalhada = 'Quantidade de descontos(('.count($ArDescontos).') - Desc. Informado) maior que a quantidade de campos de descontos disponíveis('.numrows($QryCamposDescontoCRM).')'.' - Pedido CRM:'.$this->NumregPedido.' - Item:'.$ArItemERP['nr-sequencia'].$ArItemERP['it-codigo'];
                GravaLogEvento(500, false, 'Aviso importação de Pedido', $DescricaoDetalhada);
            }
            $i = 0;
            while($ArCamposDescontoCRM = farray($QryCamposDescontoCRM)){
                
                $ArSqlDescontoItem = array();
                $ArSqlDescontoItem['pct_desconto'] = $ArDescontos[$i];
                
                $SqlCampoDescontoItem = "SELECT numreg FROM is_pedido_item_desconto WHERE id_pedido_item = ".$NumregItem." AND id_campo_desconto = ".$ArCamposDescontoCRM['numreg'];
                $QryCampoDescontoItem = query($SqlCampoDescontoItem);
                $NumrowsCampoDescontoItem = numrows($QryCampoDescontoItem);
                
                if($NumrowsCampoDescontoItem == 1){
                    $ArCampoDescontoItem = farray($QryCampoDescontoItem);
                    $ArSqlDescontoItem['numreg'] = $ArCampoDescontoItem['numreg'];
                    
                    $SqlDescontoItem = AutoExecuteSql(TipoBancoDados, 'is_pedido_item_desconto', $ArSqlDescontoItem, 'UPDATE', array('numreg'));
                    $QryDescontoItem = query($SqlDescontoItem);
                    pre($SqlDescontoItem);
                    if(!$QryDescontoItem){
                        $this->setMensagem('Erro de sql ao atualizar desconto item do pedido: Item: '.$ArSqlPedidoItem['id_sequencia'].' - '.$ArItemERP['it-codigo']);
                        $this->ErroImportacao = true;
                        return false;
                    }
                }
                else{
                    $ArSqlDescontoItem['id_pedido_item'] = $NumregItem;
                    $ArSqlDescontoItem['id_campo_desconto'] = $ArCamposDescontoCRM['numreg'];
                    
                    $SqlDescontoItem = AutoExecuteSql(TipoBancoDados, 'is_pedido_item_desconto', $ArSqlDescontoItem, 'INSERT');
                    $QryDescontoItem = query($SqlDescontoItem);
                    pre($SqlDescontoItem);
                    if(!$QryDescontoItem){
                        $this->setMensagem('Erro de sql ao inserir desconto item do pedido: Item: '.$ArSqlPedidoItem['id_sequencia'].' - '.$ArItemERP['it-codigo']);
                        $this->ErroImportacao = true;
                        return false;
                    }
                }
                $i++;
            }
        }
        return true;
    }
    
    public function DeletaItensNaoProcessados(){
        $SqlDeleteItensDesconto = "DELETE FROM is_pedido_item_desconto WHERE id_pedido_item IN(SELECT numreg FROM is_pedido_item WHERE id_pedido = '".$this->NumregPedido."') AND NOT id_pedido_item IN(".implode(',',$this->ArItensProcessadosNumreg).")";
        pre($SqlDeleteItensDesconto);
        query($SqlDeleteItensDesconto);
        $SqlDeleteItens = "DELETE FROM is_pedido_item WHERE id_pedido = ".$this->NumregPedido." AND NOT numreg IN(".implode(',',$this->ArItensProcessadosNumreg).")";
        pre($SqlDeleteItens);
        $QryDeleteItens = query($SqlDeleteItens);
        return true;
    }
    
    public function ImportaRepresentantes(){
        $QryRepresentanteERP = odbc_exec($this->CnxODBC,"SELECT \"nome-ab-rep\",\"ind-repbase\",\"perc-comis\" FROM pub.\"ped-repre\" WHERE \"nr-pedido\" = ".$this->DadosPedidoERP['nr-pedido']);
        while($ArRepresentanteERP = odbc_fetch_array($QryRepresentanteERP)){
            $SqlRepresentanteCRM = "SELECT numreg FROM is_usuario WHERE nome_abreviado = '".TrataApostrofoBD($ArRepresentanteERP['nome-ab-rep'])."'";
            $QryRepresentanteCRM = query($SqlRepresentanteCRM);
            $ArRepresentanteCRM = farray($QryRepresentanteCRM);
            if($ArRepresentanteCRM['numreg'] == ''){
                $this->setMensagem('Relacionamento de Representante com CRM não encontrado('.$ArRepresentanteERP['nome-ab-rep'].').');
                $this->ErroImportacao = true;
                return false;
            }

            $ArSqlPedidoRepresentante = array();
            $ArSqlPedidoRepresentante['id_pedido']                      = $this->NumregPedido;
            $ArSqlPedidoRepresentante['id_representante']               = $ArRepresentanteCRM['numreg'];
            $ArSqlPedidoRepresentante['pct_comissao']                   = $ArRepresentanteERP['perc-comis'];
            $ArSqlPedidoRepresentante['vl_comissao']                    = uM::uMath_pct_de_valor($ArRepresentanteERP['perc-comis'], $this->DadosPedidoERP['vl-liq-ped'],2,2);
            $ArSqlPedidoRepresentante['sn_representante_principal']     = $ArRepresentanteERP['ind-repbase'];

            $SqlPedidoRepresentanteCRM = "SELECT numreg FROM is_pedido_representante WHERE id_pedido = ".$this->NumregPedido." AND id_representante = '".$ArRepresentanteCRM['numreg']."'";
            $QryPedidoRepresentanteCRM = query($SqlPedidoRepresentanteCRM);
            pre($SqlPedidoRepresentanteCRM);
            $NumrowsPedidoRepresentanteCRM = numrows($QryPedidoRepresentanteCRM);
            if($NumrowsPedidoRepresentanteCRM == 1){
                $ArPedidoRepresentanteCRM = farray($QryPedidoRepresentanteCRM);
                $NumregRepresentante = $ArPedidoRepresentanteCRM['numreg'];
                $ArSqlPedidoRepresentante['numreg'] = $ArPedidoRepresentanteCRM['numreg'];
                $SqlPedidoRepresentante = AutoExecuteSql(TipoBancoDados,'is_pedido_representante',$ArSqlPedidoRepresentante,'UPDATE',array('numreg'));
                $QryUpdatePedidoRepresentante = query($SqlPedidoRepresentante);
                pre($SqlPedidoRepresentante);
                if(!$QryUpdatePedidoRepresentante){
                    $this->setMensagem('Erro de SQL ao atualizar de Representante('.$SqlPedidoRepresentante.').');
                    $this->ErroImportacao = true;
                    return false;
                }
            }
            else{
                $ArSqlPedidoRepresentante['id_tp_participacao'] = 1;
                $SqlPedidoRepresentante = AutoExecuteSql(TipoBancoDados,'is_pedido_representante',$ArSqlPedidoRepresentante,'INSERT');
                $QryUpdatePedidoRepresentante = iquery($SqlPedidoRepresentante);
                pre($SqlPedidoRepresentante);
                if(!$QryUpdatePedidoRepresentante){
                    $this->setMensagem('Erro de SQL ao inserir de Representante('.$SqlPedidoRepresentante.').');
                    $this->ErroImportacao = true;
                    return false;
                }
                $NumregRepresentante = $QryUpdatePedidoRepresentante;
            }
            $this->ArRepresentantesProcessadosNumreg[] = $NumregRepresentante;
        }
        if(!$this->DeletaRepresentantesNaoProcessados()){
            return false;
        }
        return true;
    }
    
    public function DeletaRepresentantesNaoProcessados(){
        $SqlDeleteItensRepresentantesComissao = "DELETE FROM is_pedido_item_representante_comissao WHERE id_pedido_item IN(SELECT numreg FROM is_pedido_item WHERE id_pedido = '".$this->NumregPedido."') AND NOT id_representante IN(SELECT id_representante FROM is_pedido_representante WHERE numreg IN(".implode(',',$this->ArRepresentantesProcessadosNumreg)."))";
        pre($SqlDeleteItensRepresentantesComissao);
        query($SqlDeleteItensRepresentantesComissao);
        $SqlDeleteRepresentantes = "DELETE FROM is_pedido_representante WHERE id_pedido = ".$this->NumregPedido." AND NOT numreg IN(".implode(',',$this->ArRepresentantesProcessadosNumreg).")";
        pre($SqlDeleteRepresentantes);
        query($SqlDeleteRepresentantes);
        return true;
    }
    
    public function DeparaCodigoCRMERP($TabelaCRM,$CampoChaveCRM,$CodigoERP){
        $QryDePara = query("SELECT numreg FROM ".$TabelaCRM." WHERE ".$CampoChaveCRM." = '".$CodigoERP."'");
        $ArDePara = farray($QryDePara);
        return $ArDePara['numreg'];
    }

    public function GeraRevisao(){
        if($this->DadosPedidoCRM['id_revisao'] == ''){
            $Pedido = new Pedido(2,$this->NumregPedido);
            if(!$Pedido->GeraRevisaoVenda()){
                $this->setMensagem('Erro ao gerar revisão do pedido. Pedido CRM: '.$this->NumregPedido);
                return false;
            }
        }
        return true;
    }
    
    public function AtualizaTotaisPedido(){
        $SqlUpdateIPI = "UPDATE is_pedido t1 SET t1.vl_total_ipi = (SELECT SUM(t2.vl_total_ipi) FROM is_pedido_item t2 WHERE t2.id_pedido = t1.numreg) WHERE t1.numreg = ".$this->NumregPedido;
        pre($SqlUpdateIPI);
        $QryUpdateIPI = query($SqlUpdateIPI);

        $SqlUpdateVlTotalST = "UPDATE is_pedido t1 SET t1.vl_total_st = ((".$this->DadosPedidoERP['vl-tot-ped'].") - t1.vl_total_liquido - t1.vl_total_ipi) WHERE t1.numreg = ".$this->NumregPedido;
        pre($SqlUpdateVlTotalST);
        $QryUpdateVlTotalST = query($SqlUpdateVlTotalST);
        
        return true;
    }
    
    public function ImportaPedido(){
        $Cabecalho = $this->ImportaCabecalho();
        if(!$Cabecalho){
            return false;
        }
        $Itens = $this->ImportaItens();
        if(!$Itens){
            return false;
        }
        $Representantes = $this->ImportaRepresentantes();
        if(!$Representantes){
            return false;
        }
        $AtualizacaoTotais = $this->AtualizaTotaisPedido();
        if(!$AtualizacaoTotais){
            return false;
        }
        return true;
    }
}