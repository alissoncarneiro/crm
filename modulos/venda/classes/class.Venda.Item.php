<?php

/*
 * class.Venda.Itens.php
 * Autor: Alex
 * 04/11/2010 17:00
 * -
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */

class VendaItem{
    public $ArrayDeParaCamposEspecificosTabelaVendaItem;
    public $ArrayDeParaCamposEspecificosTabelaVendaItemDesconto;

    protected $NumregItem;
    protected $NumregProduto;
    protected $DadosVendaItem;
    /**
     * @var Produto
     */
    protected $Produto;
    protected $PoliticaComercialDescVendaItemMedia;
    protected $PoliticaComercialDescVendaItemCampoDesconto;
    protected $PoliticaComercialDescVendaItemCampoDescontoFixoTabPreco;
    protected $PoliticaComercialBloqueioFinalizacao;
    protected $ItemComercial = true;
    protected $ArTabPreco = false;


    private $Erro = false;
    private $MensagemErro;
    private $MensagemLog = array();
    /**
     *
     * @var Venda
     */
    private $ObjVenda;
    private $ObjCFOP;
    private $ItemDescontos = array();

    private $ItemComissao = array();

    public function __construct(Venda $ObjVenda,$NumregItem=NULL){
        /*
         * Repassando a referência do objeto de venda
         */
        if(!is_object($ObjVenda)){/* Se o objeto de venda não é um objeto válido */
            echo 'Parâmetro objeto de venda inválido';
            $this->Erro = true;
            return false;
        }
        $this->setObjVenda($ObjVenda);

        /*
         * Definindo variáveis de acesso para pedido ou orçamento
         */
        if($this->ObjVenda->getTipoVenda() == 1){/* Se for tipo orçamento */
            $this->ArrayDeParaCamposEspecificosTabelaVendaItem = array(
                'id_orcamento' => 'id_venda'
            );
            $this->ArrayDeParaCamposEspecificosTabelaVendaItemDesconto = array(
                'id_orcamento_item'    => 'id_venda_item'
            );
        }
        elseif($this->ObjVenda->getTipoVenda() == 2){/* Se for tipo pedido */
            $this->ArrayDeParaCamposEspecificosTabelaVendaItem = array(
                'id_pedido'         => 'id_venda'
            );
            $this->ArrayDeParaCamposEspecificosTabelaVendaItemDesconto = array(
                'id_pedido_item'    => 'id_venda_item'
            );
        }
        else{
            return false;
        }

        $this->NumregItem = $NumregItem;
        if($this->NumregItem != NULL){
            $this->CarregaDadosVendaItemBD();
            $this->carregaItemDescontosDB();
        }
        else{
            $this->NumregProduto = $NumregProduto;
        }        
    }

    public function CarregaDadosVendaItemBD(){
        if($this->Erro == true){
            return false;
        }

        $QryVendaItem = query("SELECT * FROM ".$this->ObjVenda->getTabelaVendaItem()." WHERE numreg = ".$this->NumregItem);
        $ArVendaItem = farray($QryVendaItem);
        if($this->getObjVenda()->getTipoVenda() == 1 && $ArVendaItem['sn_item_comercial'] != 1){
            $this->setItemComercial(false);
        }
        foreach($ArVendaItem as $k => $v){
            if(!is_numeric($k)){
                $this->DadosVendaItem[$k] = $v;
            }
        }
        $this->NumregItem = $ArVendaItem['numreg'];
        $this->DadosVendaItem = $this->encodeDeParaCamposValor($this->DadosVendaItem);
        if($this->getItemComercial()){
            $this->Produto = new Produto($ArVendaItem['id_produto']);
            if(!empty($this->DadosVendaItem['id_cfop'])){
                $this->ObjCFOP = new CFOP($this->DadosVendaItem['id_cfop']);
            }
        }        
    }

    public function getArCamposPasso2_IsEditavel($IdCcampo){
        return $this->ArCamposPasso2[$IdCampo]['editavel'];
    }

    public function getArCamposPasso2_ExibeBrowse($IdCampo){
        return $this->ArCamposPasso2[$IdCampo]['exibe_browse'];
    }

    public function CarregaDadosVendaItemRepresentanteComissaoBD(){
        if($this->Erro == true){
            return false;
        }
        foreach($this->getObjVenda()->getRepresentantes() as $IndiceRepresentante => $Representante){
            $this->ItemComissao[$IndiceRepresentante] = new VendaItemComissao($this,$Representante->getIdRepresentante());
        }
        return true;
    }

    private function setMensagemLog($MensagemLog){
        $this->MensagemLog[] = $MensagemLog;
    }

    /**
     * Se o parâmetro for true retorna uma array com os erro gerados, caso contrário retorna uma string com os erros gerados
     * @param bool $RetornaEmArray Define se será retornado em array ou em string
     * @param string $Separador String com o separador no caso de retorno em forma de string
     * @return string
     */
    public function getMensagemLog($RetornaEmArray=false,$Separador = ' | '){
        if($RetornaEmArray == true){
            return $this->MensagemLog;
        }
        return implode($Separador,$this->MensagemLog);
    }

    public function setObjVenda(Venda $ObjVenda){
        $this->ObjVenda = $ObjVenda;
    }

    public function setItemComercial($Bool){
        $this->ItemComercial = $Bool;
    }

    public function getItemComercial(){
        return $this->ItemComercial;
    }

    public function setDadosItem($ArDados){
        if($this->Erro == true){
            return false;
        }
        $ArDados['id_venda'] = $this->ObjVenda->getNumregVenda();
        $this->DadosVendaItem = $this->decodeDeParaCamposValor($ArDados);
    }

    public function setDadoItem($IdCampo,$Valor){
        $this->DadosVendaItem[$IdCampo] = $Valor;
    }

    public function setPctDescontoItemDesconto($IndiceCampoDesconto,$PctDesconto){
        $this->ItemDescontos[$IndiceCampoDesconto]['pct_desconto'] = $PctDesconto;
    }

    public function getNumregItem(){
        return $this->NumregItem;
    }

    public function getObjVenda(){
        return $this->ObjVenda;
    }

    public function getObjCFOP(){
        return $this->ObjCFOP;
    }

    public function getProduto(){
        return $this->Produto;
    }

    public function getDescontos(){
        return $this->ItemDescontos;
    }

    public function getIdTabPreco(){
        return $this->getDadosVendaItem('id_tab_preco');
    }

    public function getNomeProduto(){
        if($this->getItemComercial()){
            return $this->getProduto()->getDadosProduto('nome_produto');
        }
        else{
            return $this->getDadosVendaItem('inc_descricao');
        }
    }
    
    public function getCodProdutoERP(){
        if($this->getItemComercial()){
            return $this->getProduto()->getDadosProduto('id_produto_erp');
        }
        else{
            return $this->getDadosVendaItem('inc_cod_compl');
        }
    }

    public function getPctDescontoItemDesconto($IndiceCampoDesconto){
        return $this->ItemDescontos[$IndiceCampoDesconto]['pct_desconto'];
    }

    public function getDescontoItem($NumregCampoDesconto){
        return $this->ItemDescontos[$NumregCampoDesconto]['pct_desconto'];
    }

    public function getPctDescontoTabPreco(){
        return $this->getDadosVendaItem('pct_desconto_tab_preco');
    }

    public function getSnReprovadoComercial(){
        if($this->getDadosVendaItem('sn_reprovado_comercial') == 1){
            return true;
        }
        else{
            return false;
        }
    }

    public function getVlUnitarioBaseCalculo(){
        return $this->getValorUnitario('vl_unitario_base_calculo');
    }

    public function getVlUnitarioComDescontos(){
        return $this->getValorUnitario('vl_unitario_com_descontos');
    }

    public function getVlUnitarioConvertido(){
        return $this->getValorUnitario('vl_unitario_convertido');
    }

    public function getValorUnitario($Campo){
        $VlRetorno = '';
        switch($Campo){
            case 'vl_unitario_base_calculo':
                $VlRetorno = $this->getDadosVendaItem('vl_unitario_base_calculo');
                break;
            case 'vl_unitario_com_descontos':
                $VlRetorno = $this->getDadosVendaItem('vl_unitario_com_descontos');
                break;
            case 'vl_unitario_convertido':
                $VlRetorno = $this->getDadosVendaItem('vl_unitario_convertido');
                break;
        }
        if($this->getObjVenda()->getVendaParametro()->getModoUnidMedida() == '3'){
            if($this->getObjVenda()->isAtacado()){
                $VlRetorno = $VlRetorno * $this->getQtdePorQtdeInformada() * $this->getQtdePorUnidMedida();
            }
            elseif($this->getObjVenda()->isVarejo()){
                $VlRetorno = $VlRetorno * $this->getQtdePorUnidMedida();
            }
        }        
        return $VlRetorno;
    }

    public function getItemComissao($IndiceRepresentante=NULL){
        if($IndiceRepresentante == NULL){
            return $this->ItemComissao;
        }
        return $this->ItemComissao[$IndiceRepresentante];
    }

    public function RemoveItemComissao($IndiceRepresentante){
        unset($this->ItemComissao[$IndiceRepresentante]);
    }

    public function getQtdePorQtdeInformada(){
        return $this->getDadosVendaItem('qtde_por_qtde_informada');
    }

    public function getQtdeBaseCalculo(){
        return $this->getDadosVendaItem('qtde_base_calculo');
    }

    public function getQtdePorUnidMedida(){
        return $this->getDadosVendaItem('qtde_por_unid_medida');
    }

    public function getTotalUnidades(){
        return $this->getDadosVendaItem('total_unidades');
    }

    public function AprovaItem($Justificativa){
        if(!$this->getObjVenda()->getEmAprovacao()){
            $this->getObjVenda()->setMensagem(getError('0040020001',getParametrosGerais('RetornoErro'),array($this->ObjVenda->TituloVenda)));
            return false;
        }
        $ArUpdateItem = array();
        $ArUpdateItem['numreg']                     = $this->getNumregItem();
        $ArUpdateItem['sn_reprovado_comercial']     = 0;
        $ArUpdateItem['justificativa_reprov_com']   = $Justificativa;

        $SqlUpdateItem = AutoExecuteSql(TipoBancoDados,$this->getObjVenda()->getTabelaVendaItem(),$ArUpdateItem,'UPDATE',array('numreg'));

        if(query($SqlUpdateItem)){
            $this->getObjVenda()->setMensagem("Item Aprovado.");
            return true;
        }
        else{
            $this->getObjVenda()->setMensagem("Erro de SQL ao Aprovar Item");
            return false;
        }
    }

    public function ReprovaItem($Justificativa){
        if(!$this->getObjVenda()->getEmAprovacao()){
            $this->getObjVenda()->setMensagem(getError('0040020001',getParametrosGerais('RetornoErro'),array($this->ObjVenda->TituloVenda)));
            return false;
        }
        $ArUpdateItem = array();
        $ArUpdateItem['numreg']                     = $this->getNumregItem();
        $ArUpdateItem['sn_reprovado_comercial']     = 1;
        $ArUpdateItem['justificativa_reprov_com']   = $Justificativa;

        $SqlUpdateItem = AutoExecuteSql(TipoBancoDados,$this->getObjVenda()->getTabelaVendaItem(),$ArUpdateItem,'UPDATE',array('numreg'));
        if(query($SqlUpdateItem)){
            $this->getObjVenda()->setMensagem("Item Reprovado.");
            return true;
        }
        else{
            $this->getObjVenda()->setMensagem("Erro de SQL ao Reprovar Item");
            return false;
        }
    }

    public function getDadosVendaItem($IdCampo=NULL){
        if($IdCampo == NULL){
            return $this->DadosVendaItem;
        }
        else{
            return $this->DadosVendaItem[$IdCampo];
        }
    }

    public function getItemPerdido(){
        if($this->getDadosVendaItem('sn_item_perdido') == 1){
            return true;
        }
        return false;
    }

    /**
     * Retorna o cód de CFOP do ERP
     * @return string
     */
    public function getIdCFOPErp(){
        $NumregCFOP = $this->getDadosVendaItem('id_cfop');
        if(empty($NumregCFOP)){
            return false;
        }
        $QryCfop = query("SELECT id_cfop_erp FROM is_cfop WHERE numreg = ".$NumregCFOP);
        if(numrows($QryCfop) == 0){
            return null;
        }
        $ArCfop = farray($QryCfop);
        return $ArCfop['id_cfop_erp'];
    }

    public function CalculaTotais(){

        $this->setMensagemLog('<h1>Cálculo ITEM '.$this->getProduto()->getDadosProduto('nome_produto').'</h1>');
        $Qtde                           = $this->getDadosVendaItem('qtde');
        $QtdePorQtdeInformada           = $this->getDadosVendaItem('qtde_por_qtde_informada');
        $QtdeBaseCalculo                = $Qtde * $QtdePorQtdeInformada;
        $QtdePorUnidMedida              = $this->getDadosVendaItem('qtde_por_unid_medida');
        $TotalUnidades                  = $QtdeBaseCalculo * $QtdePorUnidMedida;
        $VlUnitarioTabelaOriginal       = $this->getDadosVendaItem('vl_unitario_tabela_original');
        $VlUnitarioBaseCalculo          = $this->getDadosVendaItem('vl_unitario_base_calculo');
        $VlCotacao                      = $this->getDadosVendaItem('vl_cotacao');
        $CoeficienteDescontoTotal       = 1;
        $PctAliquotaIPI                 = $this->getDadosVendaItem('pct_aliquota_ipi');
        $TaxaFinanceira                 = $this->getObjVenda()->getDadosVenda('vl_taxa_financiamento');
        $BaseIPI                        = (is_object($this->ObjCFOP))?$this->ObjCFOP->getDadosCFOP('base_ipi'):2; /* Se foi selecionada uma CFOP, obtem o tipo de base para o IPI (por padrão é 2=líquido) */
        $PctDescontoICMSZF              = (is_object($this->ObjCFOP))?$this->ObjCFOP->getDadosCFOP('pct_desc_icms_zf'):0;
        $PesoUnitario                   = ($this->getItemComercial())?$this->getProduto()->getDadosProduto('peso_liquido'):0;
        $PesoTotal                      = 0;
        $SnPossuiST                     = 0;

        $this->setMensagemLog('Qtde Informada: '.$Qtde.'<br/>');
        $this->setMensagemLog('Qtde Varejo/Atacado: '.$QtdePorQtdeInformada.'<br/>');
        $this->setMensagemLog('Qtde Fator Conv.: '.$QtdePorUnidMedida.'<br/>');
        $this->setMensagemLog('Total Unidades: '.$TotalUnidades.'<br/>');

        /* ================== */
        /*      Descontos     */
        /* ================== */
        /* Descontos da Capa */
        $PctDescCapaTabPreco            = $this->getObjVenda()->getPctDescontoTabPreco();
        $PctDescCapaInformado           = $this->getObjVenda()->getPctDescontoInformado();
        $PctDescCapaCliente             = $this->getObjVenda()->getPctDescontoPessoa();
        /* Descontos do item */
        $PctDescItemTabPreco            = $this->getDadosVendaItem('pct_desconto_tab_preco');
        #Desconto Informado em sub tabela;

        /* ================== */
        /*  Arredondamentos   */
        /* ================== */
        /* Definindo as variáveis usadas para arredondamento e conversão */
        //TODO: Tratar para quando a moeda não for padrão, pegar os parâmetros corretos
        $PrecisaoCalculoIntermediario               = $this->getObjVenda()->getVendaParametro()->getPrecisaoCalculoIntermediarioMoedaPadrao();
        $TipoArredondamentoIntermediario            = $this->getObjVenda()->getVendaParametro()->getTipoArredondamentoIntermediarioMoedaPadrao();
        $PrecisaoCalculoFinal                       = $this->getObjVenda()->getVendaParametro()->getPrecisaoCalculoFinalMoedaPadrao();
        $TipoArredondamentoFinal                    = $this->getObjVenda()->getVendaParametro()->getTipoArredondamentoFinalMoedaPadrao();

        //TODO: Tratar para quando a moeda não for padrão, pegar os parâmetros corretos
        $PrecisaoCalculoUnitarioConversao           = $this->getObjVenda()->getVendaParametro()->getPrecisaoCalculoFinalMoedaPadrao();
        $TipoArredondamentoCalculoUnitarioConversao = $this->getObjVenda()->getVendaParametro()->getTipoArredondamentoFinalMoedaPadrao();
        $PrecisaoCalculoTotalConversao              = $this->getObjVenda()->getVendaParametro()->getPrecisaoCalculoFinalMoedaPadrao();
        $TipoArredondamentoCalculoTotalConversao    = $this->getObjVenda()->getVendaParametro()->getTipoArredondamentoFinalMoedaPadrao();

        $this->setMensagemLog('Precisão Cálculos Intermediários: '.$PrecisaoCalculoIntermediario.'<br/>');
        $this->setMensagemLog('Precisão Cálculos Finais: '.$PrecisaoCalculoFinal.'<br/>');

        /* Variáveis que ainda não estão sendo tratadas */
        $VlUnitarioICMS                             = 0;
        $VlTotalIPI                                 = 0;

        /* ================== */
        /* Iniciando Cálculos */
        /* ================== */
        $this->setMensagemLog('Vl. Unit. Base de Cálc.: '.$VlUnitarioBaseCalculo.'<br/>');
        $this->setMensagemLog('Taxa Financeira.: '.$TaxaFinanceira.'<br/>');
        if(!$this->getObjVenda()->isPrecoInformado()){ /* Se não for preço informado */
            $VlUnitarioBaseCalculoComTaxaFinanceira = $VlUnitarioBaseCalculo * $TaxaFinanceira;
        }
        else{
            $VlUnitarioBaseCalculoComTaxaFinanceira = $VlUnitarioBaseCalculo;
        }
        $this->setMensagemLog('Vl. Unit. Base de Cálc. Com Taxa: '.$VlUnitarioBaseCalculoComTaxaFinanceira.'<br/>');

        $VlTotalBaseCalculo = $TotalUnidades * $VlUnitarioBaseCalculoComTaxaFinanceira;

        $ValorTotalSemDescontos = $VlTotalBaseCalculo;

        $this->setMensagemLog('Vl. Total Base de Cálc.: '.$ValorTotalSemDescontos.'<br/>');

        /* Aplicando desconto de tab. preço do item */
        $VlTotalBaseCalculo = uM::uMath_vl_menos_pct($PctDescItemTabPreco, $VlTotalBaseCalculo, $PrecisaoCalculoIntermediario, $TipoArredondamentoIntermediario);

        $this->setMensagemLog('Vl. Total Base de Cálc. c/ Desc. Item Tab. Preço: '.$VlTotalBaseCalculo.'<br/>');

        /* Aplicando descontos informados no Item */
        foreach($this->getObjVenda()->getArrayCamposDescontos() as $IndiceCampoDesconto => $CampoDesconto){
            $PctDesconto                = $this->getPctDescontoItemDesconto($IndiceCampoDesconto);
            $CoeficienteDescontoTotal   -= (uM::uMath_pct_de_valor($PctDesconto,$CoeficienteDescontoTotal));
        }
        /* Aplicando descontos */
        $VlTotalBaseCalculo = $VlTotalBaseCalculo * $CoeficienteDescontoTotal;
        $this->setMensagemLog('Coeficiente de Desconto: '.$CoeficienteDescontoTotal.'<br/>');
        $VlTotalBaseCalculo = uM::uMath_arredonda_trunca($VlTotalBaseCalculo, $PrecisaoCalculoIntermediario, $TipoArredondamentoIntermediario);
        $this->setMensagemLog('Valor com descontos do item: '.$VlTotalBaseCalculo.'<br/>');

        /* Aplicando desconto de capa de tab. preço */
        $VlTotalBaseCalculo = uM::uMath_vl_menos_pct($PctDescCapaTabPreco, $VlTotalBaseCalculo, $PrecisaoCalculoIntermediario, $TipoArredondamentoIntermediario);

        $this->setMensagemLog('Vl. Total Base de Cálc. c/ Desc. Capa Tab. Preço: '.$VlTotalBaseCalculo.'<br/>');

        /* Aplciando desconto de capa do cliente */
        $VlTotalBaseCalculo = uM::uMath_vl_menos_pct($PctDescCapaCliente, $VlTotalBaseCalculo, $PrecisaoCalculoIntermediario, $TipoArredondamentoIntermediario);

        $this->setMensagemLog('Vl. Total Base de Cálc. c/ Desc. Capa Cliente: '.$VlTotalBaseCalculo.'<br/>');

        /* Aplicando desconto de capa informado */
        $VlTotalBaseCalculo = uM::uMath_vl_menos_pct($PctDescCapaInformado, $VlTotalBaseCalculo, $PrecisaoCalculoIntermediario, $TipoArredondamentoIntermediario);

        $this->setMensagemLog('Vl. Total Base de Cálc. c/ Desc. Capa Inf.: '.$VlTotalBaseCalculo.'<br/>');

        $VlTotalComDescontos = $VlTotalBaseCalculo;
        $this->setMensagemLog('Valor com todos descontos: '.$VlTotalComDescontos.'<br/>');

        /* Calculando o percentual médio de desconto no item */
        $ArrayDescontos = array();

        if($PctDescItemTabPreco != 0){
            $ArrayDescontos[] = $PctDescItemTabPreco;
        }
        if($ParametroTipoDesconto == 0){ /* Se for desconto somado */
            if($PctDescontoTotal != 0){
                $ArrayDescontos[] = $PctDescontoTotal;
            }
        }
        else{
            foreach($this->getObjVenda()->getArrayCamposDescontos() as $IndiceCampoDesconto => $CampoDesconto){
                $PctDesconto = $this->getPctDescontoItemDesconto($IndiceCampoDesconto);
                if($PctDesconto != 0){
                    $ArrayDescontos[] = $PctDesconto;
                }
            }
        }
        if($PctDescCapaTabPreco != 0){
            $ArrayDescontos[] = $PctDescCapaTabPreco;
        }
        if($PctDescCapaCliente != 0){
            $ArrayDescontos[] = $PctDescCapaCliente;
        }
        if($PctDescCapaInformado != 0){
            $ArrayDescontos[] = $PctDescCapaInformado;
        }

        $PctMediaDesconto = 0;
        if(count($ArrayDescontos) > 0){
            $PctMediaDesconto = $ArrayDescontos[0];
            foreach($ArrayDescontos as $Indice => $PctDesconto){
                if($Indice == 0){continue;}
                $PctMediaDesconto -= uM::uMath_pct_de_valor($PctDesconto,$PctMediaDesconto);
            }
        }
        /* Fim do cálculo da média de desconto do item */

        /* Calculando Desconto Total sem considerar o desconto base e sem arredondar os valores */
        $PctTotalDesconto = (($VlTotalBaseCalculo - $VlTotalComDescontos) / $VlTotalBaseCalculo) * 100;

        /* Calculando o valor unitário */
        $VlUnitarioComDescontos = $VlTotalComDescontos / $TotalUnidades;

        $this->setMensagemLog('Vl. Unit. c/ Todos os descontos antes arredondamento: '.$VlUnitarioComDescontos.'<br/>');
        $this->setMensagemLog('Tipo de Arredondamento utilizado: '.$TipoArredondamentoFinal.'<br/>');

        $VlUnitarioComDescontos = uM::uMath_arredonda_trunca($VlUnitarioComDescontos, $PrecisaoCalculoFinal, $TipoArredondamentoFinal);

        $this->setMensagemLog('Vl. Unit. c/ Todos os descontos: '.$VlUnitarioComDescontos.'<br/>');

        $VlTotalComDescontos = $VlUnitarioComDescontos * $TotalUnidades;

        /* Aplicando desconto de ICMS-ZonaFranca */
        if($PctDescontoICMSZF > 0){
            $this->setMensagemLog('Pct. Desc. ICMS ZF: '.$PctDescontoICMSZF.'<br/>');
            $VlTotalComDescontos = uM::uMath_vl_menos_pct($PctDescontoICMSZF, $VlTotalComDescontos, $PrecisaoCalculoIntermediario, $TipoArredondamentoIntermediario);
            $this->setMensagemLog('Valor com desconto ZF: '.$VlTotalComDescontos.'<br/>');

            $this->setMensagemLog('Recalculando valor unitário arredondando para 5 casas<br/>');
            $VlUnitarioComDescontos = $VlTotalComDescontos / $TotalUnidades;
            $this->setMensagemLog('Valor unitário final sem arred.: '.$VlUnitarioComDescontos.'<br/>');
            $VlUnitarioComDescontos = uM::uMath_arredonda_trunca($VlUnitarioComDescontos, 5, 2);
            $this->setMensagemLog('Valor unitário final: '.$VlUnitarioComDescontos.'<br/>');

            $VlTotalComDescontos = $VlUnitarioComDescontos * $TotalUnidades;
            $VlTotalComDescontos = uM::uMath_arredonda_trunca($VlTotalComDescontos, $PrecisaoCalculoFinal, $TipoArredondamentoFinal);

            $this->setMensagemLog('Valor total final: '.$VlTotalComDescontos.'<br/>');
        }

        /* Calculando o valor total bruto e líquido de base de cálculo */
        $VlTotalBrutoBaseCalculo    = $ValorTotalSemDescontos;
        $VlTotalLiquidoBaseCalculo  = $VlUnitarioComDescontos * $TotalUnidades;

        /*
         * Calculando valor total base de cálculo da NF
         */
        $VlTotalLiquidoVendaNF = $QtdePorUnidMedida * $VlUnitarioTabelaOriginal;
        /* Aplicando desconto de capa de tab. preço */
        $VlTotalLiquidoVendaNF = uM::uMath_vl_menos_pct($PctDescCapaTabPreco, $VlTotalLiquidoVendaNF, $PrecisaoCalculoIntermediario, $TipoArredondamentoIntermediario);
        /* Aplicando desconto de capa informado */
        $VlTotalLiquidoVendaNF = uM::uMath_vl_menos_pct($PctDescCapaInformado, $VlTotalLiquidoVendaNF, $PrecisaoCalculoIntermediario, $TipoArredondamentoIntermediario);
        /* Aplciando desconto de capa do cliente */
        $VlTotalLiquidoVendaNF = uM::uMath_vl_menos_pct($PctDescCapaCliente, $VlTotalLiquidoVendaNF, $PrecisaoCalculoIntermediario, $TipoArredondamentoIntermediario);
        /* Aplicando desconto de tab. preço do item */
        $VlTotalLiquidoVendaNF = uM::uMath_vl_menos_pct($PctDescItemTabPreco, $VlTotalLiquidoVendaNF, $PrecisaoCalculoIntermediario, $TipoArredondamentoIntermediario);
        /* Aplicando descontos informados no Item */
        foreach($this->getObjVenda()->getArrayCamposDescontos() as $IndiceCampoDesconto => $CampoDesconto){
            $PctDesconto                = $this->getPctDescontoItemDesconto($IndiceCampoDesconto);
            $CoeficienteDescontoTotal   -= (uM::uMath_pct_de_valor($PctDesconto,$CoeficienteDescontoTotal));
        }
        /* Aplicando descontos */
        $VlTotalLiquidoVendaNF = $VlTotalLiquidoVendaNF * $CoeficienteDescontoTotal;
        $VlTotalLiquidoVendaNF = uM::uMath_arredonda_trunca($VlTotalLiquidoVendaNF,$PrecisaoCalculoFinal,$TipoArredondamentoFinal);
        $VlTotalLiquidoVendaNF = $VlTotalLiquidoVendaNF * $QtdePorQtdeInformada * $Qtde;

        $this->setMensagemLog('Valor total liquido de Venda NF:'.$VlTotalLiquidoVendaNF.'<br/>');
        /* Fim cálculo valor NF */

        if($VlCotacao != 1){
            /* Fazendo a conversão de moedas */
            $VlTotalLiquido = $VlTotalComDescontos * $VlCotacao;

            $VlTotalBruto = $VlTotalBrutoBaseCalculo * $VlCotacao;
            $VlTotalBruto = uM::uMath_arredonda_trunca($VlTotalBruto, $PrecisaoCalculoFinal, $TipoArredondamentoFinal);

            $VlUnitarioConvertido = $VlTotalLiquido / $TotalUnidades;
            $VlUnitarioConvertido = uM::uMath_arredonda_trunca($VlUnitarioConvertido, $PrecisaoCalculoFinal, $TipoArredondamentoFinal);

            $VlTotalLiquido = $VlUnitarioConvertido * $TotalUnidades;
        }
        else{
            $VlTotalLiquido = $VlTotalLiquidoBaseCalculo;
            $VlTotalBruto = $VlTotalBrutoBaseCalculo;
            $VlUnitarioConvertido = $VlUnitarioComDescontos;
        }

        /* Calculando IPI */
        if(!$this->getObjVenda()->getVendaParametro()->getSnCalculaIPI()){
            $VlTotalIPI = 0;
            $this->setMensagemLog('Parâmetro de venda configurado para não calcular IPI<br/>');
        }
        elseif(is_object($this->ObjCFOP)){
            $this->setMensagemLog('CFOP encontrada, calculando IPI<br/>');
            $IPI = new IPI();
            $IPI->setObjCFOP($this->ObjCFOP);
            $IPI->setPctAliquotaIPI($PctAliquotaIPI);

            if($BaseIPI == 2){ /* Liquido */
                $this->setMensagemLog('Cálculo de IPI sobre base líquida ('.$VlTotalLiquido.')<br/>');
                $VlTotalIPI = $IPI->CalculaVlIPI($VlTotalLiquido);
            }
            else{
                $this->setMensagemLog('Cálculo de IPI sobre base bruta ('.$VlTotalBruto.')<br/>');
                $VlTotalIPI = $IPI->CalculaVlIPI($VlTotalBruto);
            }
            $this->setMensagemLog('Log cálculo de IPI:'.$IPI->getMensagemLog());
        }
        else{
            $VlTotalIPI = 0;
            $this->setMensagemLog('Item não possui CFOP, IPI não calculado.<br/>');
        }

        /*
         * Calculando ST
         */
        $IdCFOP = $this->getDadosVendaItem('id_cfop');
        if($this->getItemComercial() && !empty($IdCFOP)){

            $SubstituicaoTributaria = new SubstituicaoTributaria($this->getObjVenda()->getVendaParametro(),$this->getObjVenda()->getDadosVenda('id_pessoa'),$this->getDadosVendaItem('id_produto'),$this->getObjVenda()->getDadosVenda('id_destino_mercadoria'),$this->getDadosVendaItem('id_cfop'),$this->getObjVenda()->getDadosEstabelecimento('uf'),$this->getObjVenda()->getDadosEstabelecimento('pais'),$this->getObjVenda()->getDadosEnderecoEntrega('uf'),$this->getObjVenda()->getDadosEnderecoEntrega('pais'),$PctAliquotaIPI,$VlTotalLiquido,0 /* Valor de Frete */, 0 /* Despesas Adicionais */);

            $VlTotalST = $SubstituicaoTributaria->CalculaVlSubstituicaoTributaria();
            if($SubstituicaoTributaria->getSnPossuiSubstituicaoTributaria()){
                $SnPossuiST = '1';
            }
            $this->setMensagemLog('LOG Calculo ST<hr>'.$SubstituicaoTributaria->getMensagemLog(false,'</br>'));

            if($VlTotalST === false){
                echo $SubstituicaoTributaria->getMensagem();
            }
        }
        else{
            $VlTotalST = 0;
        }

        /* Calculando Peso */
        $PesoTotal = $TotalUnidades * $PesoUnitario;

        /* Aplicando os dados nos itens */

        $this->setDadoItem('qtde_base_calculo', $QtdeBaseCalculo);
        $this->setDadoItem('total_unidades', $TotalUnidades);

        $this->setDadoItem('vl_unitario_com_descontos',$VlUnitarioComDescontos);
        $this->setDadoItem('vl_unitario_convertido',$VlUnitarioConvertido);

        $this->setDadoItem('vl_total_bruto_base_calculo',$VlTotalBrutoBaseCalculo);
        $this->setDadoItem('vl_total_liquido_base_calculo',$VlTotalLiquidoBaseCalculo);

        $this->setDadoItem('vl_total_liquido',$VlTotalLiquido);
        $this->setDadoItem('vl_total_bruto',$VlTotalBruto);
        $this->setDadoItem('vl_total_ipi',$VlTotalIPI);
        $this->setDadoItem('vl_total_st',$VlTotalST);

        $this->setDadoItem('sn_possui_st',$SnPossuiST);

        $this->setDadoItem('pct_desconto_total',$PctMediaDesconto);

        $this->setDadoItem('peso_total',$PesoTotal);

        $this->setMensagemLog('<h2>FIM Cálculo ITEM '.$this->getProduto()->getDadosProduto('nome_produto').'</h2>');
    }

    public function getDadosTabPreco($IdCampo){
        $IdTabPreco = $this->getDadosVendaItem('id_tab_preco');
        if($IdTabPreco == ''){
            return false;
        }
        if($this->ArTabPreco != false){
            return $this->ArTabPreco[$IdCampo];
        }
        $QryTabPreco = query("SELECT * FROM is_tab_preco WHERE numreg = ".$IdTabPreco);
        $ArTabPreco = farray($QryTabPreco);
        $this->ArTabPreco = $ArTabPreco;
        return $this->ArTabPreco[$IdCampo];
    }

    public function getProximoIdSequencia(){
        $QryMaxSequencia        = query("SELECT MAX(id_sequencia) AS id_sequencia FROM ".$this->ObjVenda->getTabelaVendaItem()." WHERE ".$this->ObjVenda->getCampoChaveTabelaVendaItem()." = ".$this->ObjVenda->getNumregVenda());
        $ArMaxSequencia         = farray($QryMaxSequencia);
        $ProximoIdSequencia     = ($ArMaxSequencia['id_sequencia'] == '')?10:$ArMaxSequencia['id_sequencia'] + 10;
        return $ProximoIdSequencia;
    }

    public function getPoliticaComercialDescVendaItemMedia(){
        return $this->PoliticaComercialDescVendaItemMedia;
    }

    public function getPoliticaComercialDescVendaItemCampoDesconto(){
        return $this->PoliticaComercialDescVendaItemCampoDesconto;
    }

    public function getPoliticaComercialDescVendaItemCampoDescontoFixoTabPreco(){
        return $this->PoliticaComercialDescVendaItemCampoDescontoFixoTabPreco;
    }

    public function getPoliticaComercialBloqueioFinalizacao(){
        return $this->PoliticaComercialBloqueioFinalizacao;
    }

    public function AdicionaItemBD(){
        if($this->Erro == true){
            return false;
        }
        if($this->getItemComercial()){ /* Se é um item comercial */
            $this->Produto          = new Produto($this->DadosVendaItem['id_produto']);
            $VlCotacao = false; /* Setando o valor da cotação como false para quando não for controlado por tabela de preço, não ocorrer erro */
            if($this->getObjVenda()->getVendaParametro()->getSnUsaTabPrecoPorItem()){ /* Se a tabela de preço é controlada por item */
                $IdMoedaTabPreco    = $this->getDadosTabPreco('id_moeda');
                $VlCotacao          = getCotacaoBD($IdMoedaTabPreco,1);
                if(!$VlCotacao){
                    $this->getObjVenda()->setMensagem('Cotação não cadastrada. Entre em contato com o Administrador.');
                    return false;
                }
            }
            $SnVlUnitarioSugestaoNF     = 0;
            $VlUnitarioSugestaoNF       = NULL;
            $DtVlUnitarioSugestaoNF     = NULL;
            if($this->getObjVenda()->isPrecoInformado()){ /* Se for tipo de preço informado */
                if($this->getObjVenda()->getVendaParametro()->getSnUsaSugestaoDePrecoDeNF()){ /* Se utiliza sugestão de preço da última NF emitida */
                    $ArDadosPrecoNF                 = array();
                    $ArDadosPrecoNF['uf']           = $this->getObjVenda()->getDadosEnderecoEntrega('uf');
                    $ArDadosPrecoNF['id_produto']   = $this->getProduto()->getNumregProduto();

                    $PrecoProduto           = new PrecoProduto($this->getProduto()->getNumregProduto(),$this->getObjVenda()->getGrupoTabPreco(),$this->getIdTabPreco(),$this->getDadosVendaItem('id_unid_medida'), $this->getDadosVendaItem('id_produto_embalagem'));
                    $PrecoProduto->CalculaSugestaoDePrecoDeNF($ArDadosPrecoNF);
                    $VlUnitarioOriginal     = $PrecoProduto->getPreco();
                    $VlUnitarioSugestaoNF   = $VlUnitarioOriginal;
                    $DtVlUnitarioSugestaoNF = $PrecoProduto->DtSugestaoPrecoNF;
                    $SnVlUnitarioSugestaoNF = 1;
                }
                else{ /* Caso não utilize a sugestão de preço através da ultima NF emitida pega o preço informado */
                    $VlUnitarioOriginal = $this->getDadosVendaItem('vl_unitario_base_calculo');
                }
            }
            else{
                $IdTabPreco             = $this->getIdTabPreco();
                $PrecoProduto = new PrecoProduto($this->getProduto()->getNumregProduto(),$this->getObjVenda()->getGrupoTabPreco(), $IdTabelaPreco);
                $VlUnitarioOriginal     = $this->getProduto()->getVlUnitarioTabelaBD($this->getObjVenda()->getGrupoTabPreco(),$IdTabPreco);
            }
            $VlCotacao              = ($VlCotacao === false)?1:$VlCotacao;
            $PctAliquotaIPI         = ($this->getObjVenda()->getVendaParametro()->getSnCalculaIPI())?$this->getProduto()->getDadosProduto('pct_aliq_ipi'):0;
        }
        else{ /* Se não for um item comercial */
            $this->DadosVendaItem['id_produto']     = 0;
            $VlUnitarioOriginal                     = $this->DadosVendaItem['vl_unitario_tabela_original'];
            $VlCotacao                              = 1;
            $VlUnitario                             = $VlUnitarioOriginal * $VlCotacao;
            $this->DadosVendaItem['id_moeda']       = 0;
            $PctAliquotaIPI                         = 0;
        }
        $VlUnitarioOriginal = ($VlUnitarioOriginal == '')?0:$VlUnitarioOriginal;
        $this->DadosVendaItem['id_sequencia']                   = $this->getProximoIdSequencia();
        $this->DadosVendaItem['dt_cadastro']                    = date("Y-m-d");
        $this->DadosVendaItem['id_usuario_cad']                 = $_SESSION['id_usuario'];
        $this->DadosVendaItem['id_situacao_item']               = 1;
        $this->DadosVendaItem['vl_unitario_tabela_original']    = $VlUnitarioOriginal;
        $this->DadosVendaItem['vl_unitario_base_calculo']       = $VlUnitarioOriginal;
        $this->DadosVendaItem['vl_unitario_convertido']         = 0;
        $this->DadosVendaItem['vl_cotacao']                     = $VlCotacao;
        $this->DadosVendaItem['pct_desconto_base']              = 0;
        $this->DadosVendaItem['pct_aliquota_ipi']               = $PctAliquotaIPI;
        $this->DadosVendaItem['pct_aliquota_iva']               = 0;
        $this->DadosVendaItem['id_tp_preco']                    = 1; /* Fixado tipo informado */
        $this->DadosVendaItem['sn_vl_unitario_sugestao_nf']     = $SnVlUnitarioSugestaoNF;
        $this->DadosVendaItem['vl_unitario_sugestao_nf']        = $VlUnitarioSugestaoNF;
        $this->DadosVendaItem['dt_vl_unitario_sugestao_nf']     = $DtVlUnitarioSugestaoNF;
        $this->DadosVendaItem['qtde_por_qtde_informada']        = ($this->getObjVenda()->isAtacado())?$this->Produto->getQtdePorQtdeInformada($this->getDadosVendaItem('id_produto_embalagem')):1;
        if($this->getObjVenda()->getVendaParametro()->getModoUnidMedida() == '3'){
            $this->DadosVendaItem['id_unid_medida'] = $this->Produto->getIdUnidMedidaAtacadoVarejo($this->getObjVenda()->getGrupoTabPreco());
        }
        if($this->DadosVendaItem['id_unid_medida'] == ''){
            $this->DadosVendaItem['id_unid_medida'] = $this->Produto->getIdUnidMedidaPadrao();
        }
        $this->DadosVendaItem['qtde_por_unid_medida']           = $this->Produto->getQtdePorUnidMedida($this->DadosVendaItem['id_unid_medida']);

        $this->DadosVendaItem['vl_unitario_convertido']         = 0;
        $this->DadosVendaItem['pct_aliquota_ipi']               = ($this->DadosVendaItem['pct_aliquota_ipi'] == '')?0:$this->DadosVendaItem['pct_aliquota_ipi'];
        if($this->getItemComercial()){ /* Se a CFOP esta vazia e o item é um item comercial */
            $this->DadosVendaItem['id_cfop']                    = $this->CalculaCFOP();
            if($this->DadosVendaItem['id_cfop'] == ''){
                unset($this->DadosVendaItem['id_cfop']);
            }
            else{
                $this->ObjCFOP = new CFOP($this->DadosVendaItem['id_cfop']);
            }
        }

        if($this->getObjVenda()->getVendaParametro()->getSnUsaKit()){
            if($this->getObjVenda()->getVendaParametro()->getSnAdicionaNomeKitObsItem()){
                if($this->DadosVendaItem['id_kit'] != ''){
                    $QryKIT = query("SELECT nome_kit FROM is_kit WHERE numreg = '".$this->DadosVendaItem['id_kit']."'");
                    $ArKIT = farray($QryKIT);
                    if($ArKIT['nome_kit'] != ''){
                        $this->DadosVendaItem['obs'] = $ArKIT['nome_kit'].' '.$this->DadosVendaItem['obs'];
                    }
                }
            }
        }

        /*
         * Adicionando os campos de desconto
         */
        foreach($this->ObjVenda->getArrayCamposDescontos() as $IndiceCampoDesconto => $CampoDesconto){
            $this->setPctDescontoItemDesconto($IndiceCampoDesconto,0);
            if($CampoDesconto['sn_autopreenchido_com_desc_max'] == 1){
                $this->ValidaPoliticaComercialDescVendaItemCampoDesconto($IndiceCampoDesconto);
                $PctDesconto = $this->getPoliticaComercialDescVendaItemCampoDesconto()->getPctMaxCampoDescontoItem();
                $this->setPctDescontoItemDesconto($IndiceCampoDesconto,$PctDesconto);
            }
        }
        
        $this->CalculaTotais();
        $SqlItem = AutoExecuteSql(TipoBancoDados,$this->ObjVenda->getTabelaVendaItem(),$this->DadosVendaItem,'INSERT');
        $QryItem = iquery($SqlItem);
        if($QryItem){
            /* Setando o numreg do item */
            $this->NumregItem = $QryItem;
            $this->AtualizaItemDescontoBD();
            return $QryItem;
        }
        $this->ObjVenda->setMensagemDebug('Erro de SQL ao inserir item ('.$SqlItem.')');
        return false;
    }

    /**
     * Retorna o número de sequência do item na venda
     * @return int
     */
    public function getSequenciaItem(){
        return $this->getDadosVendaItem('id_sequencia');
    }

    /**
     * Atualiza a cotação do item no objeto
     * @return bool
     */
    public function AtualizaCotacao(){
        if($this->getObjVenda()->getVendaParametro()->getSnUsaTabPrecoPorItem() && !$this->isCotacaoFixa()){ /* Se usa tabela de preço por item e a cotação não foi marcada como fixa */
            $IdMoedaTabPreco    = $this->getDadosTabPreco('id_moeda');
            $VlCotacao          = getCotacaoBD($IdMoedaTabPreco,1);
            if(!$VlCotacao){ /* Caso nao tenha encontrado a cotação */
                $this->getObjVenda()->setMensagem('Item '.$this->getSequenciaItem().' não possui cotação para a data atual.');
                $this->getObjVenda()->setMensagemDebug('Item '.$this->getSequenciaItem().' não possui cotação para a data atual. Linha:('.__LINE__.')');
                return false;
            }
            $this->setDadoItem('vl_cotacao',$VlCotacao);
        }
        return true;
    }

    /**
     * Atualiza a cotação do item no banco de dados
     * @return bool
     */
    public function AtualizaCotacaoBD(){
        if($this->AtualizaCotacao() && $this->AtualizaDadosItemBD()){ /* Se atualizar a cotação e conseguir atualizar os dados no banco de dados */
            return true;
        }
        return false;
    }

    /**
     * Retorna true caso o item seja marcado como cotação fixa, false caso contrário.
     * @return bool
     */
    public function isCotacaoFixa(){
        if($this->getDadosVendaItem('sn_cotacao_fixa') == 1){
            return true;
        }
        return false;
    }

    public function carregaItemDescontosDB(){
        $SqlItemDesconto = "SELECT * FROM ".$this->getObjVenda()->getTabelaVendaItemDesconto()." WHERE ".$this->getObjVenda()->getCampoChaveTabelaVendaItemDesconto()." = ".$this->getNumregItem();
        $QryItemDesconto = query($SqlItemDesconto);
        while($ArItemDesconto = farray($QryItemDesconto)){
            $NovaArray = array();
            foreach($ArItemDesconto as $k => $v){
                if(!is_numeric($k)){
                    $NovaArray[$k] = $v;
                }
            }
            $this->ItemDescontos[$ArItemDesconto['id_campo_desconto']] = $NovaArray;
        }
    }

    public function AtualizaItemDescontoBD(){
        /*
         * Atualizando os campos de desconto
         */
        foreach($this->getDescontos() as $IndiceCampoDesconto => $ArDadosCampoDesconto){
            /*
             * Tratamento para campo de desconto automático com pct extraido da política comercial de campos de desconto
             */
            $CampoDesconto = $this->getObjVenda()->getDadosCampoDesconto($IndiceCampoDesconto);
            if($CampoDesconto['sn_editavel'] != 1 && $CampoDesconto['sn_autopreenchido_com_desc_max'] == 1){
                $this->ValidaPoliticaComercialDescVendaItemCampoDesconto($IndiceCampoDesconto);
                $PctDesconto = $this->getPoliticaComercialDescVendaItemCampoDesconto()->getPctMaxCampoDescontoItem();
                $this->setPctDescontoItemDesconto($IndiceCampoDesconto,$PctDesconto);
                $ArDadosCampoDesconto['pct_desconto'] = $PctDesconto;
            }

            $ArSqlDesconto = array();
            $ArSqlDesconto['id_venda_item']         = $this->getNumregItem();
            $ArSqlDesconto['id_campo_desconto']     = $IndiceCampoDesconto;
            $ArSqlDesconto['pct_desconto']          = $ArDadosCampoDesconto['pct_desconto'];

            $SqlCountDesconto = AutoExecuteSql(TipoBancoDados,$this->ObjVenda->getTabelaVendaItemDesconto(),$this->decodeDeParaCamposValorDesconto($ArSqlDesconto),'COUNT',array($this->ObjVenda->getCampoChaveTabelaVendaItemDesconto(),'id_campo_desconto'));
            $QryCountDesconto = query($SqlCountDesconto);
            $ArCountDesconto = farray($QryCountDesconto);
            if($ArCountDesconto['CNT'] == 0){/* Se não foi encontrado desconto para este item, insere na base de dados os desconto */
                $SqlDesconto = AutoExecuteSql(TipoBancoDados,$this->ObjVenda->getTabelaVendaItemDesconto(),$this->decodeDeParaCamposValorDesconto($ArSqlDesconto),'INSERT');
            }
            else{
                $SqlDesconto = AutoExecuteSql(TipoBancoDados,$this->ObjVenda->getTabelaVendaItemDesconto(),$this->decodeDeParaCamposValorDesconto($ArSqlDesconto),'UPDATE',array($this->ObjVenda->getCampoChaveTabelaVendaItemDesconto(),'id_campo_desconto'));
            }
            query($SqlDesconto);
        }
    }

    public function CalculaCFOP(){
        /* Se utiliza cálculo de CFOP customizado */
        if($this->getObjVenda()->getVendaParametro()->getSnUsaCalculoCFOPCustomizado()){
            $SugestaoCFOPCustom = new SugestaoCFOPCustom($this->getObjVenda(),$this);
            $IdCFOP = $SugestaoCFOPCustom->SugereCFOP();
            $IdCFOP = (empty($IdCFOP) || !is_numeric($IdCFOP))?0:$IdCFOP;
            $this->setMensagemLog('<h1>Cálculo de CFOP</h1>');
            $this->setMensagemLog($SugestaoCFOPCustom->getMensagemLog(false,'<br/>'));
            return $IdCFOP;
        }
        /*
         * Definindo parâmetros de estado e pais de origem e destino
         */
        $UFEstabelecimento      = $this->getObjVenda()->getDadosEstabelecimento('uf');
        $UFEstabelecimento      = trim(strtoupper($UFEstabelecimento));
        $PaisEstabelecimento    = $this->getObjVenda()->getDadosEstabelecimento('pais');
        $PaisEstabelecimento    = trim(strtoupper($PaisEstabelecimento));

        $UFEnderecoEntrega      = $this->getObjVenda()->getDadosEnderecoEntrega('uf');
        $UFEnderecoEntrega      = trim(strtoupper($UFEnderecoEntrega));
        $PaisEnderecoEntrega    = $this->getObjVenda()->getDadosEnderecoEntrega('pais');
        $PaisEnderecoEntrega    = trim(strtoupper($PaisEnderecoEntrega));

        if($this->getObjVenda()->getVendaParametro()->getSnSugereCFOPCliente()){ /* Se o parâmetro do cadastro de parâmetros de vendas estiver como ativo, considera a CFOP do cliente */
            $IdCFOP = $this->getObjVenda()->getCFOPCliente($PaisEstabelecimento,$UFEstabelecimento,$PaisEnderecoEntrega,$UFEnderecoEntrega);
            return $IdCFOP;
        }

        $ArValores = array();
        $ArValores['id_pedido_estabelecimento']     = $this->getObjVenda()->getDadosVenda('id_estabelecimento');
        $ArValores['id_pedido_dest_merc']           = $this->getObjVenda()->getDadosVenda('id_destino_mercadoria');
        $ArValores['id_pessoa']                     = $this->getObjVenda()->getDadosVenda('id_pessoa');
        $ArValores['id_pessoa_regiao']              = $this->getObjVenda()->getPessoa()->getDadoPessoa('id_regiao');
        $ArValores['pessoa_cidade']                 = $this->getObjVenda()->getPessoa()->getDadoPessoa('cidade');
        $ArValores['pessoa_uf']                     = $this->getObjVenda()->getPessoa()->getDadoPessoa('uf');
        $ArValores['id_pessoa_canal_venda']         = $this->getObjVenda()->getPessoa()->getDadoPessoa('id_canal_venda');
        $ArValores['id_pessoa_grupo_cliente']       = $this->getObjVenda()->getPessoa()->getDadoPessoa('id_grupo_cliente');
        $ArValores['id_tp_pessoa']                  = $this->getObjVenda()->getPessoa()->getDadoPessoa('id_tp_pessoa');
        $ArValores['sn_contribuinte_icms']          = $this->getObjVenda()->getPessoa()->getDadoPessoa('sn_contribuinte_icms');
        $ArValores['pessoa_categoria_erp']          = $this->getObjVenda()->getPessoa()->getDadoPessoa('categoria_erp');
        $ArValores['id_produto']                    = $this->getProduto()->getNumregProduto();
        $ArValores['id_produto_familia']            = $this->getProduto()->getDadosProduto('id_familia');
        $ArValores['id_produto_familia_comercial']  = $this->getProduto()->getDadosProduto('id_familia_comercial');
        $ArValores['id_produto_grupo_estoque']      = $this->getProduto()->getDadosProduto('id_grupo_estoque');
        $ArValores['id_produto_linha']              = $this->getProduto()->getDadosProduto('id_linha');
        $ArValores['id_cfop_oper']                  = $this->getObjVenda()->getDadosVenda('id_tp_venda');
        $ArValores['id_pedido_tp_venda']            = $this->getObjVenda()->getDadosVenda('id_tp_venda');
        $ArValores['id_pedido_dest_merc']           = $this->getObjVenda()->getDadosVenda('id_destino_mercadoria');
        $ArValores['id_pedido_moeda']               = $this->getObjVenda()->getDadosVenda('id_moeda');
        $ArValores['sn_triangular']                 = ($this->getObjVenda()->getDadosVenda('id_pessoa_triangular') != '')?1:0;

        include('CFOPCustom.php');

        $CFOP = new SugestaoCFOP();
        $CFOP->setCamposCustom($ArValores);
        $CFOP->setUFPais($PaisEstabelecimento,$UFEstabelecimento,$PaisEnderecoEntrega,$UFEnderecoEntrega);
        $CFOP->setOrdemCustom('pontos DESC');
        $IdCFOP = $CFOP->getCFOP();
        $IdCFOP = (empty($IdCFOP) || !is_numeric($IdCFOP))?0:$IdCFOP;
        return $IdCFOP;
    }

    public function RecalculaCFOPBD(){
        if(!$this->getItemComercial()){/* Se não é um item comercial */
            return false;
        }
        $ArUpdateItem = array();
        $ArUpdateItem['numreg']     = $this->getNumregItem();
        $ArUpdateItem['id_cfop']    = $this->CalculaCFOP();

        $this->setDadoItem('id_cfop',$ArUpdateItem['id_cfop']);

        $SqlUpdateItem = AutoExecuteSql(TipoBancoDados,$this->ObjVenda->getTabelaVendaItem(),$ArUpdateItem,'UPDATE',array('numreg'));
        if(!query($SqlUpdateItem)){
            $this->getObjVenda()->setMensagem('Erro de SQL ao atualizar CFOP item');
            return false;
        }
        return true;
    }

    public function setDadosItemPOST($ArrayDadosPOST){
        TrimValorArray($ArrayDadosPOST);

        foreach($this->ObjVenda->getArrayCamposDescontos() as $IndiceCampoDesconto => $CampoDesconto){
            $this->setPctDescontoItemDesconto($IndiceCampoDesconto,TrataFloatPost($ArrayDadosPOST['tabela_item_desc_'.$this->getNumregItem().'_'.$IndiceCampoDesconto]));
        }
        /* Tratando post de cotacao fixa */
        $ArrayDadosPOST['tabela_item_chk_cotacao_fixa_'.$this->getNumregItem()] = ($ArrayDadosPOST['tabela_item_chk_cotacao_fixa_'.$this->getNumregItem()] == 1)?1:0;

        /* Adicionando os valores de POST nos dados do item */
        $this->setDadoItem('qtde',TrataFloatPost($ArrayDadosPOST['tabela_item_qtde_'.$this->getNumregItem()]));
        $this->setDadoItem('id_unid_medida',$ArrayDadosPOST['tabela_item_id_unid_medida_'.$this->getNumregItem()]);
        $this->setDadoItem('id_moeda',1); /* Fixo 1 = REAL */
        $this->setDadoItem('id_unid_medida',$ArrayDadosPOST['tabela_item_id_unid_medida_'.$this->getNumregItem()]);
        $this->setDadoItem('obs',$ArrayDadosPOST['tabela_item_obs_'.$this->getNumregItem()]);
        $this->setDadoItem('sn_cotacao_fixa',$ArrayDadosPOST['tabela_item_chk_cotacao_fixa_'.$this->getNumregItem()]);
        /* Tratamento para o campo de desconto de tab. de preço */
        if(isset($ArrayDadosPOST['tabela_item_pct_desconto_tab_preco_'.$this->getNumregItem()])){
            $this->setDadoItem('pct_desconto_tab_preco',TrataFloatPost($ArrayDadosPOST['tabela_item_pct_desconto_tab_preco_'.$this->getNumregItem()]));
        }

        if($this->getObjVenda()->getVendaParametro()->getSnAlterarDtEntPorItem()){ /* Se o parametro de data de entrega por item estiver ativo, trata a data de entrega */
            $DtEntregaItem = $ArrayDadosPOST['tabela_item_dt_entrega_'.$this->getNumregItem()];
            $DtEntregaItem = ($DtEntregaItem != '')?uB::DataBr2En($DtEntregaItem, false):$DtEntregaItem;
            $this->setDadoItem('dt_entrega',$DtEntregaItem);
        }

        if($this->getObjVenda()->getSnPermiteAlterarCFOP()){
            $this->setDadoItem('id_cfop',$ArrayDadosPOST['tabela_item_id_cfop_'.$this->getNumregItem()]);
        }

        if($this->ObjVenda->isPrecoInformado() || !$this->getItemComercial()){
            $this->setDadoItem('vl_unitario_base_calculo',TrataFloatPost($ArrayDadosPOST['tabela_item_vl_unitario_'.$this->getNumregItem()]));
        }
    }

    public function AtualizaItemBD(){
        /*
         * Verificando se algum dos descontos é inválido
         */
        $ErroItem = false;
        $MensagemErro = '';
        foreach($this->getDescontos() as $IndiceCampoDesconto => $ArDadosCampoDesconto){
            $DadosCampoDesconto = $this->getObjVenda()->getDadosCampoDesconto($IndiceCampoDesconto);
            if($ArDadosCampoDesconto['pct_desconto'] < $DadosCampoDesconto['pct_max_desc_ini']){
                $MensagemErro .= 'Desconto menor que '.number_format($DadosCampoDesconto['pct_max_desc_ini'],2,',','').'% no campo '.$DadosCampoDesconto['nome_campo'].' não permitido!';
                $ErroItem = true;
                break;
            }
            elseif($ArDadosCampoDesconto['pct_desconto'] > $DadosCampoDesconto['pct_max_desc_fim']){
                $MensagemErro .= 'Desconto maior que '.number_format($DadosCampoDesconto['pct_max_desc_fim'],2,',','').'% no campo '.$DadosCampoDesconto['nome_campo'].' não permitido!';
                $ErroItem = true;
                break;
            }
            elseif($ArDadosCampoDesconto['pct_desconto'] == ''){
                $ArDadosCampoDesconto['pct_desconto'] = 0;
            }
        }
        if($this->getDadosVendaItem('qtde') == ''){
            $MensagemErro .= 'Qtde não pode ser vazia!';
            $this->ObjVenda->setMensagemDebug($MensagemErro);
            $ErroItem = true;
        }
        if($this->getDadosVendaItem('vl_unitario_base_calculo') == '' && !$this->getObjVenda()->isPrecoInformado()){
            $MensagemErro .= 'Valor Unitário não pode ser vazio!';
            $this->ObjVenda->setMensagemDebug($MensagemErro);
            $ErroItem = true;
        }

        if($this->getItemComercial() && $this->getDadosVendaItem('id_cfop') == '' && !$this->getObjVenda()->getDigitacaoCompleta() && $this->getObjVenda()->getUsuario()->getPermissao('sn_permite_alterar_cfop_item') && $this->getObjVenda()->getVendaParametro()->getPermiteAlterarCFOPItem()){
            $MensagemErro .= 'CFOP não pode ser vazio!';
            $this->ObjVenda->setMensagemDebug($MensagemErro);
            $ErroItem = true;
        }

        if($ErroItem === true){
            $MensagemErro = $MensagemErro.' <br /><strong style="font-size:14px; color:#FF0000;">Item não atualizado!</strong>';
            $this->ObjVenda->setAtualizacaoItensErro(true);
            if($this->getItemComercial()){
                $this->ObjVenda->setMensagemAtualizacaoItens($this->getNumregItem(),array($this->getDadosVendaItem('id_sequencia'),$this->getProduto()->getDadosProduto('id_produto_erp'),$this->getProduto()->getDadosProduto('nome_produto'),$MensagemErro));
            }
            else{
                $this->ObjVenda->setMensagemAtualizacaoItens($this->getNumregItem(),array($this->getDadosVendaItem('id_sequencia'),$this->getDadosVendaItem('inc_cod_compl'),$this->getDadosVendaItem('inc_descricao'),$MensagemErro));
            }
            return false;
        }

        $this->AtualizaItemDescontoBD();

        /* Recalculando todos os valores */
        $this->CalculaTotais();

        /* Pegando todos as colunas que serão atualizadas */
        $ArUpdate = $this->getDadosVendaItem();

        if($this->getObjVenda()->getSnPermiteAlterarCFOP()){
            $ArUpdate['id_cfop'] = $this->getDadosVendaItem('id_cfop');
        }

        /*
         * Fazendo depara de campos
         */
        $ArUpdate           = $this->decodeDeParaCamposValor($ArUpdate);

        $SqlUpdate = AutoExecuteSql(TipoBancoDados,$this->ObjVenda->getTabelaVendaItem(),$ArUpdate,'UPDATE',array('numreg'));
        $QryUpdate = query($SqlUpdate);
        if(!query($SqlUpdate)){
            $this->ObjVenda->setMensagemDebug('Erro de SQL. '.$SqlUpdate);
            $this->ObjVenda->setMensagemAtualizacaoItens($this->getNumregItem(),array(
                                                                            $this->getDadosVendaItem('id_sequencia'),
                                                                            $this->getProduto()->getDadosProduto('id_produto_erp'),
                                                                            $this->getProduto()->getDadosProduto('nome_produto'),
                                                                            'Erro de SQL'
                                                                            ));
            $this->ObjVenda->setAtualizacaoItensErro(true);
            return false;
        }
        VendaCallBackCustom::ExecutaVendaItem($this->ObjVenda,$this,'AtualizaItemBD','Final');
        return true;
    }

    public function AtualizaVlBonificacaoItem($PctBonificacao){
        $ArUpdate = array();
        $ArUpdate['numreg']                         = $this->getNumregItem();
        $ArUpdate['vl_total_bonificacao']           = $this->getObjVenda()->RoundV((($this->getDadosVendaItem('vl_total_liquido') * $PctBonificacao) / 100));
        $SqlUpdate = AutoExecuteSql(TipoBancoDados,$this->getObjVenda()->getTabelaVendaItem(),$ArUpdate,'UPDATE',array('numreg'));
        query($SqlUpdate);

        $this->setDadoItem('vl_total_bonificacao',$ArUpdate['vl_total_bonificacao']);
    }

    public function ValidaPoliticaComercialDescVendaItemMedia(){
        $this->PoliticaComercialDescVendaItemMedia = new PoliticaComercialDescVendaItemMedia();

        if(!$this->getItemComercial()){ /* Se for item não comercial está fora da política */
            $this->PoliticaComercialDescVendaItemMedia->setStatus(false);
            $this->PoliticaComercialDescVendaItemMedia->setStringStatus('Fora da política Item não Comercial');
            return false;
        }

        /*
         * Validando se considera flag de cotação fixa fora da política
         */
        if($this->getObjVenda()->getVendaParametro()->getSnConsideraCotacaoFixaForaPolitca()){
            if($this->getDadosVendaItem('sn_cotacao_fixa') == '1'){
                $this->PoliticaComercialDescVendaItemMedia->setStatus(false);
                $this->PoliticaComercialDescVendaItemMedia->setStringStatus('Fora da política, cotação marcada como fixa.');
                return false;
            }
        }

        /*
         * Validando o item caso o parâmetro de validação por preço sugerido por NF
         */
        if($this->getObjVenda()->getVendaParametro()->getSnAplicaPolComSugPreNF()){
            if($this->getObjVenda()->isPrecoInformado() && $this->getDadosVendaItem('sn_vl_unitario_sugestao_nf') == '1'){
                $VlUnitarioSugestaoNF       = $this->getDadosVendaItem('vl_unitario_sugestao_nf');
                $DtVlUnitarioSugestaoNF     = $this->getDadosVendaItem('dt_vl_unitario_sugestao_nf');
                $VlUnitarioComDescontos     = $this->getDadosVendaItem('vl_unitario_com_descontos');
                /*
                 * Validando se o preço foi encontrado
                 */
                if($VlUnitarioSugestaoNF == 0){ /* Se o preço sugerido for igual a 0 (não encontrado) */
                    $this->PoliticaComercialDescVendaItemMedia->setStatus(false);
                    $this->PoliticaComercialDescVendaItemMedia->setStringStatus('Fora da política, não foi encontrado preço para sugestão.');
                    return false;
                }
                /*
                 * Validando se o preço possui mais de x dias
                 */
                $DiferencaDias = DiferencaEntreDatas($DtVlUnitarioSugestaoNF,date("Y-m-d"));
                if($DiferencaDias > $this->getObjVenda()->getVendaParametro()->getQtdeDiasPolComSugPreNF()){ /* Se a nota fiscal da qual o preço foi obtido já possui mais de x dias */
                    $this->PoliticaComercialDescVendaItemMedia->setStatus(false);
                    $this->PoliticaComercialDescVendaItemMedia->setStringStatus('Fora da política, a NF que sugeriu o preço de venda possui mais de '.$this->getObjVenda()->getVendaParametro()->getQtdeDiasPolComSugPreNF().' dias.');
                    return false;
                }
                /*
                 * Validando se o preço foi alterado mais que x%, máximo permitido, ou menos que x% mínimo permitido
                 */
                $PctMinDiferencaDoPrecoSugerido = $this->getObjVenda()->getVendaParametro()->getPctMinPolComSugPreNF();
                $PctMaxDiferencaDoPrecoSugerido = $this->getObjVenda()->getVendaParametro()->getPctMaxPolComSugPreNF();
                $VlMinDiferencaDoPrecoSugerido  = $this->getObjVenda()->RoundV(uM::uMath_vl_mais_pct($PctMinDiferencaDoPrecoSugerido, $VlUnitarioSugestaoNF));
                $VlMaxDiferencaDoPrecoSugerido  = $this->getObjVenda()->RoundV(uM::uMath_vl_mais_pct($PctMaxDiferencaDoPrecoSugerido, $VlUnitarioSugestaoNF));

                if($VlUnitarioComDescontos < $VlMinDiferencaDoPrecoSugerido || $VlUnitarioComDescontos > $VlMaxDiferencaDoPrecoSugerido){
                    $this->PoliticaComercialDescVendaItemMedia->setStatus(false);
                    $this->PoliticaComercialDescVendaItemMedia->setStringStatus('Fora da política, o preço está fora da faixa permitida ('.$this->getObjVenda()->NFV($VlMinDiferencaDoPrecoSugerido).' e '.$this->getObjVenda()->NFV($VlMaxDiferencaDoPrecoSugerido).').');
                    return false;
                }
            }
        }

        $ArDadosItem                    = $this->getDadosVendaItem();
        $ArDadosProduto                 = $this->getProduto()->getDadosProduto();
        $ArDadosProduto['id_produto']   = $this->getProduto()->getNumregProduto();
        $ArDadosVenda['med_dias']       = $this->getObjVenda()->getMedDiasCondPagto();

        $this->PoliticaComercialDescVendaItemMedia->setArDadosItem($ArDadosItem);
        $this->PoliticaComercialDescVendaItemMedia->setArDadosProduto($ArDadosProduto);
        $this->PoliticaComercialDescVendaItemMedia->setArDadosVenda($ArDadosVenda);

        return $this->PoliticaComercialDescVendaItemMedia->ValidaItem();
    }

    public function ValidaPoliticaComercialDescVendaItemCampoDescontoFixoTabPreco(){
        $this->PoliticaComercialDescVendaItemCampoDescontoFixoTabPreco = new PoliticaComercialDescVendaItemCampoDescontoFixo($this,1);

        if(!$this->getItemComercial()){ /* Se for item não comercial está fora da política */
            $this->PoliticaComercialDescVendaItemCampoDescontoFixoTabPreco->setStatus(false);
            $this->PoliticaComercialDescVendaItemCampoDescontoFixoTabPreco->setStringStatus('Fora da política Item não Comercial');
            return false;
        }

        $ArDadosVenda                   = $this->getObjVenda()->getDadosVenda();
        $ArDadosVenda['med_dias']       = $this->getObjVenda()->getMedDiasCondPagto();

        $ArDadosPessoa                  = $this->getObjVenda()->getPessoa()->getDadoPessoa();
        $ArDadosPessoa['id_pessoa']     = $this->getObjVenda()->getPessoa()->getNumregPessoa();

        $ArDadosItem                    = $this->getDadosVendaItem();

        $ArDadosProduto                 = $this->getProduto()->getDadosProduto();
        $ArDadosProduto['id_produto']   = $this->getProduto()->getNumregProduto();

        $this->PoliticaComercialDescVendaItemCampoDescontoFixoTabPreco->setArDadosVenda($ArDadosVenda);
        $this->PoliticaComercialDescVendaItemCampoDescontoFixoTabPreco->setArDadosPessoa($ArDadosPessoa);
        $this->PoliticaComercialDescVendaItemCampoDescontoFixoTabPreco->setArDadosItem($ArDadosItem);
        $this->PoliticaComercialDescVendaItemCampoDescontoFixoTabPreco->setArDadosProduto($ArDadosProduto);

        $this->PoliticaComercialDescVendaItemCampoDescontoFixoTabPreco->setPctCampoDescontoItem($this->getPctDescontoTabPreco());

        $this->PoliticaComercialDescVendaItemCampoDescontoFixoTabPreco->CalculaMaximoDescontoPermitido();

        return $this->PoliticaComercialDescVendaItemCampoDescontoFixoTabPreco->ValidaCampoDescontoItem();
    }

    public function ValidaPoliticaComercialDescVendaItemCampoDesconto($IdCampoDesconto){
        $this->PoliticaComercialDescVendaItemCampoDesconto = new PoliticaComercialDescVendaItemCampoDesconto($IdCampoDesconto);

        if(!$this->getItemComercial()){ /* Se for item não comercial está fora da política */
            $this->PoliticaComercialDescVendaItemCampoDesconto->setStatus(false);
            $this->PoliticaComercialDescVendaItemCampoDesconto->setStringStatus('Fora da política Item não Comercial');
            return false;
        }

        $ArDadosVenda                   = $this->getObjVenda()->getDadosVenda();
        $ArDadosVenda['med_dias']       = $this->getObjVenda()->getMedDiasCondPagto();

        $ArDadosPessoa                  = $this->getObjVenda()->getPessoa()->getDadoPessoa();

        $ArDadosItem                    = $this->getDadosVendaItem();

        $ArDadosProduto                 = $this->getProduto()->getDadosProduto();
        $ArDadosProduto['id_produto']   = $this->getProduto()->getNumregProduto();

        $this->PoliticaComercialDescVendaItemCampoDesconto->setArDadosVenda($ArDadosVenda);
        $this->PoliticaComercialDescVendaItemCampoDesconto->setArDadosPessoa($ArDadosPessoa);
        $this->PoliticaComercialDescVendaItemCampoDesconto->setArDadosItem($ArDadosItem);
        $this->PoliticaComercialDescVendaItemCampoDesconto->setArDadosProduto($ArDadosProduto);

        $this->PoliticaComercialDescVendaItemCampoDesconto->setPctCampoDescontoItem($this->getDescontoItem($IdCampoDesconto));

        $this->PoliticaComercialDescVendaItemCampoDesconto->CalculaMaximoDescontoPermitido();

        $Status = $this->PoliticaComercialDescVendaItemCampoDesconto->ValidaCampoDescontoItem();
        /* Se está fora da política e o campo não permite finalizar a venda neste caso */
        if($Status === false && !$this->PoliticaComercialDescVendaItemCampoDesconto->getSnPermiteFinalizar()){
            $this->getObjVenda()->setPermiteFinalizar(false);
        }
        return $Status;
    }

    public function ValidaPoliticaBloqueioFinalizacaoItem(){
        $this->PoliticaComercialBloqueioFinalizacao = new PoliticaComercialBloqueioFinalizacao();
        /*
         * Dados do cabeçalho
         */
        $ArDadosVenda                   = $this->getObjVenda()->getDadosVenda();
        $ArDadosVenda['tipo_venda']     = $this->getObjVenda()->getTipoVenda();
        $ArDadosVenda['med_dias']       = $this->getObjVenda()->getMedDiasCondPagto();
        $ArDadosVenda['id_repres_pri']  = $this->getObjVenda()->getRepresentantePrincipal();
        $ArDadosVenda['venda_vl_tot']   = $this->getObjVenda()->getVlTotalVendaLiquido();

        /*
         * Dados do cliente
         */
        $ArDadosPessoa                  = $this->getObjVenda()->getPessoa()->getDadoPessoa();

        /*
         * Dados do produto
         */
        $ArDadosProduto                 = $this->getProduto()->getDadosProduto();

        if($ArDadosProduto['id_familia_comercial'] != ''){
            $QryFamiliaComercial = query("SELECT id_produto_linha FROM is_familia_comercial WHERE numreg = ".$ArDadosProduto['id_familia_comercial']);
            $ArFamiliaComercial = farray($QryFamiliaComercial);
            $ArDadosProduto['id_linha']     = $ArFamiliaComercial['id_produto_linha'];
        }
        $ArDadosProduto['id_produto']   = $this->getProduto()->getNumregProduto();

        /*
         * Dados do item da venda
         */
        $ArDadosItem                    = $this->getDadosVendaItem();
        $ArDadosItem['item_vl_tot']     = $this->getDadosVendaItem('vl_total_liquido');
        $ArDadosItem['pct_media_desc']  = $this->getDadosVendaItem('pct_desconto_total');

        $this->PoliticaComercialBloqueioFinalizacao->setArDadosVenda($ArDadosVenda);
        $this->PoliticaComercialBloqueioFinalizacao->setArDadosPessoa($ArDadosPessoa);
        $this->PoliticaComercialBloqueioFinalizacao->setArDadosProduto($ArDadosProduto);
        $this->PoliticaComercialBloqueioFinalizacao->setArDadosItem($ArDadosItem);

        if($this->PoliticaComercialBloqueioFinalizacao->ValidaBloqueioFinalizacao()){
            $this->getObjVenda()->setPermiteFinalizar(false);
            return false;
        }
        return true;
    }

    public function CalculaComissaoItem($IndiceRepresentante){
        $VlComissaoItem = $this->ItemComissao[$IndiceRepresentante]->CalculaComissao();
        $this->ItemComissao[$IndiceRepresentante]->AtualizaDadosBD();
        return $VlComissaoItem;
    }

    public function AtualizaDadosItemBD(){
        /* Pegando todos as colunas que serão atualizadas */
        $ArUpdate = $this->getDadosVendaItem();
        /* Fazendo depara de campos */
        $ArUpdate           = $this->decodeDeParaCamposValor($ArUpdate);

        $SqlUpdate = AutoExecuteSql(TipoBancoDados,$this->ObjVenda->getTabelaVendaItem(),$ArUpdate,'UPDATE',array('numreg'));
        $QryUpdate = query($SqlUpdate);
        if(!query($SqlUpdate)){
            $this->getObjVenda()->setMensagemDebug('Erro de SQL ao atualizadar dados do item. SQL:'.$SqlUpdate);
            return false;
        }
        return true;
    }

    public function AtualizaComissaoItemBD(){
        $ArUpdateVendaItem = array();
        $ArUpdateVendaItem['numreg']                = $this->getNumregItem();
        $ArUpdateVendaItem['pct_comissao']          = $this->getDadosVendaItem('pct_comissao');
        $ArUpdateVendaItem['vl_total_comissao']     = $this->getDadosVendaItem('vl_total_comissao');
        $SqlUpdateVendaItem = AutoExecuteSql(TipoBancoDados,$this->getObjVenda()->getTabelaVendaItem(),$ArUpdateVendaItem,'UPDATE',array('numreg'));

        $QryUpdateVendaItem = query($SqlUpdateVendaItem);
    }

    /*
     * VALORES (PREÇO, DESCONTO ETC.)
     */
    public function RecarregaValorUnitarioDB(){
        if(!$this->getObjVenda()->isPrecoInformado()){ /* Somente carrega o preço se não for preço informado */
            if($this->getItemComercial()){
                if($this->getObjVenda()->getVendaParametro()->getSnUsaTabPrecoPorItem()){ /* Se usa tabela de preço por item */
                    $IdTabPreco = $this->getDadosVendaItem('id_tab_preco');
                }
                else{
                    $IdTabPreco = $this->ObjVenda->getIdTabPreco();
                }
                $this->setDadoItem('vl_unitario_base_calculo',$this->getProduto()->getVlUnitarioTabelaBD($this->getObjVenda()->getGrupoTabPreco(),$IdTabPreco));
            }
        }
    }

    public function encodeDeParaCamposValor($ArDados){
        $ArDadosRetorno = $ArDados;
        $ArrayDePara = $this->ArrayDeParaCamposEspecificosTabelaVendaItem;
        foreach($ArDados as $k => $v){
            if(array_key_exists($k,$ArrayDePara)){
                unset($ArDadosRetorno[$k]);
                $ArDadosRetorno[$ArrayDePara[$k]] = $v;
            }
        }
        return $ArDadosRetorno;
    }

    public function decodeDeParaCamposValor($ArDados){
        $ArDadosRetorno = $ArDados;
        foreach($ArDados as $k => $v){
            $Search = array_search($k,$this->ArrayDeParaCamposEspecificosTabelaVendaItem);
            if($Search != ''){
                unset($ArDadosRetorno[$k]);
                $ArDadosRetorno[$Search] = $v;
            }
        }
        return $ArDadosRetorno;
    }

    public function encodeDeParaCamposValorDesconto($ArDados){
        $ArDadosRetorno = $ArDados;
        $ArrayDePara = $this->ArrayDeParaCamposEspecificosTabelaVendaItemDesconto;
        foreach($ArDados as $k => $v){
            if(array_key_exists($k,$ArrayDePara)){
                unset($ArDadosRetorno[$k]);
                $ArDadosRetorno[$ArrayDePara[$k]] = $v;
            }
        }
        return $ArDadosRetorno;
    }

    public function decodeDeParaCamposValorDesconto($ArDados){
        $ArDadosRetorno = $ArDados;
        foreach($ArDados as $k => $v){
            $Search = array_search($k,$this->ArrayDeParaCamposEspecificosTabelaVendaItemDesconto);
            if($Search != ''){
                unset($ArDadosRetorno[$k]);
                $ArDadosRetorno[$Search] = $v;
            }
        }
        return $ArDadosRetorno;
    }
}