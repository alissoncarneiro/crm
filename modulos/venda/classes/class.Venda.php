<?php

/*
 * class.Vendas.php
 * Autor: Alex
 * 18/10/2010 15:02:00
 * Classe responsável por tratar os pedidos e orçamentos
 * Origens do Sistema
 * 1 - Atendimento Laboratório
 */

class Venda{

    public $pfuncao;
    public $DadosVenda = array(); // Array com os dados do pedido, onde as chaves s?o os nomes das colunas
    public $ArrayCamposEspecificosTabelaVenda; // Array com os campos especificos da tabela de venda
    public $ArrayDeParaCamposEspecificosTabelaVenda; // Array para depara dos campos das tabelas de pedidos e or?amentos
    public $PassoAtual; // Passo atual em que se encontra o processo de venda (p1,p2,p3,p4)
    public $TituloVenda; //T?tulo da Venda (or?amento ou pedido)
    public $NumeroRevisaoGerada; // N?mero de revis?o gerada pelo sistema
    public $NumeroCloneGerado; // N?mero do clone gerado pelo sistema
    public $NumeroOportunidadeFilhaGerada; // N?mero da oportunidade gerado pelo sistema
    public $OportunidadePai; //Objeto da oportunida pai
    public $OportunidadeFilha; //Objeto da oportunida filha
    public $NumregRevisaoGerada;
    public $VendaCustomizacoes; //Objeto de Customizacoes

    protected $TabelaVenda; //Tabela que ser? utilizada para gravar os dados (is_orcamento ou is_pedido)
    protected $TabelaVendaItem; // Tabela que ser? utilizada para gravar os dados dos itens (is_orcamento_item ou is_pedido_item)
    protected $TabelaVendaItemDesconto; // Tabela que ser? utilizada para gravar os descontos do item (is_orcamento_item_desconto ou is_pedido_item_desconto)
    protected $TabelaVendaItemPre;
    protected $TabelaVendaItemComissao;
    protected $TabelaVendaRepresentante; // Tabela que ser? utilizada para garavar os representantes da venda
    protected $TabelaVendaCondPagtoEspecial;
    protected $CampoChaveTabelaVenda; //Campo chave da tabela do cabecalho
    protected $CampoChaveTabelaVendaItem; // Campo chave da tabela de item (id_orcamento ou id_pedido)
    protected $CampoChaveTabelaVendaItemDesconto; // Campo chave da tabela de descontos do item (id_orcamento_item ou id_pedido_item)
    protected $CampoChaveTabelaVendaItemPre;
    protected $CampoChaveTabelaVendaRepresentante; // Campo chave da tabela de representante da venda (id_orcamento ou id_pedido)
    protected $CampoChaveTabelaVendaCondPagtoEspecial;
    protected $KIT = array();
    protected $Itens = array(); // Array com os itens(objetos VendaItem())
    protected $ItensSolicitacaoComercial = array();
    protected $ItensPre = array(); // Array com os pr? itens do or?amento/pedido
    protected $ItensOportunidadePai = array();
    protected $Representantes = array(); // Array com os representantes do pedido/or?amento(objetos VendaRepresentantes())
    protected $Pessoa; //Objeto Pessoa()
    protected $Contato;// Objeto de Contato da Venda
    protected $MediaDiasCondPagto; //M?dia de dias da cond. de pagamento da venda
    protected $PoliticaComercialDescVendaMedia; // Objeto de valida??o de pol?tica do cabecalho da venda
    protected $PoliticaComercialDescVendaCampoDescontoFixoTabPreco; // Objeto de valida??o de pol?tica do cabecalho da venda
    protected $PoliticaComercialDescVendaCampoDescontoFixoPessoa; // Objeto de valida??o de pol?tica do cabecalho da venda
    protected $PoliticaComercialDescVendaCampoDescontoFixoInformado; // Objeto de valida??o de pol?tica do cabecalho da venda
    protected $ArrayParamCampoDescontoVendaFixo; //Array com os dados dos campos de descontos fixo da capa do pedidos

    private $ArrayCamposDescontos = array(); //Array com os campos de desconto que est?o sendo utilizados nesta venda. Se a venda ainda n?o foi finalizada, utiliza os campos de descontos ativos da tabela is_param_campo_desconto
    private $NumregVenda; // numreg da tabela de venda
    private $TipoVenda; // tipo do registro, 1=or?amento, 2=pedido
    private $PrecisaoValor; // Qtde de decimais que ser? utilizada no c?lculo e exibi??o dos valores
    private $PrecisaoQtde; // Qtde de decimais que ser? utilizada no c?lculo e exibi??o das qtdes
    private $PrecisaoDesconto; // Qtde de decimais que ser? utilizada no c?lculo e exibi??o dos descontos
    private $StatusPoliticaComercialDesc = false; //Status da valida??o da pol?tica comercial, por padr?o ? false
    private $JustificativaEmAprovacaoComercial; // Texto de justificativa para o ir para aprova??o
    private $Usuario;// Objeto Usu?rio Logado
    private $VendaParametro; // Objeto VendaParametro
    private $ArDadosTpVenda = array(); // Array de dados do tipo de venda
    private $ArDadosEstabelecimento = array(); // Array de dados do estabelecimento da venda
    private $ArRevisoes = false; //Array com as revisoes do orcamento ou pedido
    private $MensagemDebug = array(); //Array de mensagens para debug
    private $PermiteFinalizar = true;

    public $ArTabPrecos = false; // Array com os registro de tabelas de pre?o
    public $MensagemAtualizacaoItens; // Mensagem de retorno ao atualizar os itens no passo 2
    public $AtualizacaoItensErro = false; // Se houve algum erro ao atualizar os itens no passo 2
    public $MensagemAdicionaItemErro; // Mensagem de retorno ao adicionar um item no passo 2

    public $MensagemRemoveItem; // Mensagem de retorno ao atualizar o item no passo 2
    public $RemoveItemErro = false; // Se houve algum erro ao remover o item no passo 2

    private $Erro = false;
    private $Mensagem;
    private $NumregPedidoBonificacao;

    public $VlTotalVenda = 0; // Valor total final da venda
    public $VlTotalVendaBruto = 0; // Valor total bruto da venda
    public $VlTotalVendaLiquido = 0; // Valor total liquido
    public $VlTotalVendaIPI = 0; // Valor total do IPI
    public $VlTotalVendaST = 0; // Valor total ST
    public $VlTotalVendaFrete = 0; // Valor total frete
    public $PesoTotalVenda = 0; // Peso total

    public $ArMoedas = false; //Array com as moedas do banco de dados numreg => nome_moeda
    public $Debug; //Define se o modo de Debug ser? ativo

    public function __construct($TipoVenda,$NumregVenda = NULL, $CarregaItens = true, $CarregaUsuario = true, $VisualizarRevisao = false){

        $this->Debug = ($_SESSION['debug'] == 'true');

        $this->VendaParametro = new VendaParametro();

        $this->VisualizarRevisao = $VisualizarRevisao;

        //Definindo o array com os campos que n?o s?o
        $this->ArrayCamposEspecificosTabelaVenda = array(
            'id_venda_cliente',
            'id_venda_representante',
            'id_tp_venda',
            'id_situacao_venda',
            'id_revisao_venda',
            'dt_venda',
            'obs_venda',
            'id_venda_erp',
            'id_venda_bonificacao'
        );

        $this->VendaCustomizacoes = new VendaCustomizacoesCustom($this);

        $this->TipoVenda        = $TipoVenda;
        $this->PrecisaoValor    = getParametrosVenda('precisao_valor');
        $this->PrecisaoQtde     = getParametrosVenda('precisao_qtde');
        $this->PrecisaoDesconto = getParametrosVenda('precisao_desconto');

        $this->CarregaArrayParamCampoDescontoVendaFixo();
        $this->DefineVariaveisOrcamentoPedido();
        if(!empty($NumregVenda)){
            $this->NumregVenda = $NumregVenda;
            $this->carregaDadosVendaBD();
            $this->CarregaUsuario($CarregaUsuario);

            if($CarregaItens == true){
                $this->CarregaItensBD();
            }
            $this->CarregaRepresentantes();
            $this->CarregaArrayCamposDescontosBD();
            $this->CarregaDadosVendaItemRepresentanteComissaoBD();
        } elseif($NumregVenda == NULL){
            $this->CarregaUsuario($CarregaUsuario);
            if($TipoVenda == 1){
                $StatusNovoRegistro = $this->InsereOrcamento();
            } elseif($TipoVenda == 2){
                $StatusNovoRegistro = $this->InserePedido();
            }
            if(!$StatusNovoRegistro){
                return false;
            }
            $this->CarregaDadosVendaBD();
            $this->CalculaTotaisVenda();
        } else{
            $this->setMensagem('Parámetro inválido');
            return false;
        }
        return true;
    }

    protected function TrataArInsertVenda($ArInsert){
        if(is_object($this->getUsuario())){
            if(!$this->getUsuario()->getPermissao('sn_permite_alterar_preco_venda')){
                $ArInsert['id_tp_preco'] = 2;
            }
        }
        /*
         * Efetuando o tratamento para campos com valor padr?o
         */
        if($this->getTipoVenda() == 1){
            $Campos = new VendaCamposCustom('orcamento',$this,$_GET);
        }
        else{
            $Campos = new VendaCamposCustom('pedido',$this,$_GET);
        }
        $ArrayCampos = $Campos->getArrayCampos();
        foreach($ArrayCampos as $k => $v){
            $ValorPadrao = $Campos->getValorPadrao($_SESSION['id_perfil'],$k);
            if($ValorPadrao != ''){
                $ArInsert[$k] = $ValorPadrao;
            }
        }
        if($this->getVendaParametro()->getModoUnidMedida() != '3'){
            $ArInsert['id_grupo_tab_preco'] = 2;
        }
        return $ArInsert;
    }

    protected function CarregaDadosVendaItemRepresentanteComissaoBD(){
        foreach($this->getItens() as $IndiceItem => $Item){
            if($Item->getItemComercial()){
                $Item->CarregaDadosVendaItemRepresentanteComissaoBD();
            }
        }
    }

    protected function CarregaUsuario($CarregaUsuario){
        if($CarregaUsuario){
            $IdUsuario = $_SESSION['id_usuario'];
            if(empty($_SESSION['id_usuario'])){
                $IdUsuario = $this->getDadosVenda('id_usuario_cad');
            }
            $this->Usuario = new Usuario($IdUsuario);
        }
    }

    protected function setNumregVenda($NumregVenda){
        if(empty($this->NumregVenda)){
            $this->NumregVenda = $NumregVenda;
            return true;
        } else{
            return false;
        }
    }

    /**
     * Retorna um objeto usuario, do usu?rio logado
     * @return Object
     */
    public function getUsuario(){
        return $this->Usuario;
    }

    public function getPFuncao(){
        return $this->pfuncao;
    }

    public function getNumregPedidoBonificacao(){
        return $this->NumregPedidoBonificacao;
    }

    public function setMensagemAtualizacaoItens($NumregItem,$ArrayMensagem){
        $this->MensagemAtualizacaoItens[$NumregItem] = $ArrayMensagem;
    }

    public function setAtualizacaoItensErro($Bool){
        $this->AtualizacaoItensErro = $Bool;
    }

    public function getVendaParametro(){
        return $this->VendaParametro;
    }

    public function setVlTotalVendaBruto($VlTotalVendaBruto){
        $this->VlTotalVendaBruto = $VlTotalVendaBruto;
    }

    public function setVlTotalFrete($VlTotalFrete){
        $this->VlTotalVendaFrete = $VlTotalFrete;
    }

    public function setPesoTotalVenda($PesoTotal){
        $this->PesoTotalVenda = $PesoTotal;
    }

    public function setErro($Boolean){
        $this->Erro = $Boolean;
    }

    public function setJustificativaEmAprovacaoComercial($JustificativaEmAprovacaoComercial){
        $this->JustificativaEmAprovacaoComercial = $JustificativaEmAprovacaoComercial;
    }

    public function getMensagem(){
        return $this->Mensagem;
    }

    public function setMensagemDebug($MensagemDebug){
        $this->MensagemDebug[] = $MensagemDebug;
    }

    /**
     * Se o par?metro for true retorna uma array com os erro gerados, caso contr?rio retorna uma string com os erros gerados
     * @param bool $RetornaEmArray Define se ser? retornado em array ou em string
     * @param string $Separador String com o separador no caso de retorno em forma de string
     * @return string
     */
    public function getMensagemDebug($RetornaEmArray=false,$Separador = ' | '){
        if($RetornaEmArray == true){
            return $this->MensagemDebug;
        }
        return implode($Separador,$this->MensagemDebug);
    }

    public function getRepresentantes(){
        return $this->Representantes;
    }

    public function getRepresentante($IndiceRepresentante){
        return $this->Representantes[$IndiceRepresentante];
    }

    public function setMensagem($Texto){
        $this->Mensagem .= $Texto."\n";
    }

    public function setDadoVenda($IdCampo,$Valor){
        $this->DadosVenda[$IdCampo] = $Valor;
    }

    public function setVlTotalVendaLiquido($VlTotalVendaLiquido){
        $this->VlTotalVendaLiquido = $VlTotalVendaLiquido;
    }

    public function setVlTotalVendaIPI($VlTotalVendaIPI){
        $this->VlTotalVendaIPI = $VlTotalVendaIPI;
    }

    public function setVlTotalVendaST($VlTotalVendaST){
        $this->VlTotalVendaST = $VlTotalVendaST;
    }

    public function setVlTotalVenda($VlTotalVenda){
        $this->VlTotalVenda = $VlTotalVenda;
    }

    public function CarregaArrayParamCampoDescontoVendaFixo(){
        $SqlCampoDesconto = "SELECT * FROM is_param_campo_desconto_venda_fixo ORDER BY numreg";
        $QryCampoDesconto = query($SqlCampoDesconto);
        $this->ArrayParamCampoDescontoVendaFixo = array();
        while($ArCampoDesconto = farray($QryCampoDesconto)){
            foreach($ArCampoDesconto as $k => $v){
            if(is_int($k)){continue;}
                $this->ArrayParamCampoDescontoVendaFixo[$ArCampoDesconto['numreg']][$k] = $v;
            }
        }
    }

    public function getProximaSequenciaKIT(){
        $Sql = "SELECT MAX(id_sequencia_kit) AS maior_sequencia_kit FROM ".$this->getTabelaVendaItem()." WHERE ".$this->getCampoChaveTabelaVendaItem()." = '".$this->getNumregVenda()."'";
        $Qry = query($Sql);
        $Ar = farray($Qry);
        $ProximaSequenciaKIT = ($Ar['maior_sequencia_kit'] == '')?1:$Ar['maior_sequencia_kit'] + 1;
        return $ProximaSequenciaKIT;
    }

    public function getParamCampoDescontoVendaFixo($IdCampoDesconto,$IdCampo){
        return $this->ArrayParamCampoDescontoVendaFixo[$IdCampoDesconto][$IdCampo];
    }

    public function CarregaArrayCamposDescontosBD(){
        if($this->getDigitacaoCompleta()){
            $SqlCampoDesconto = "SELECT * FROM is_param_campo_desconto WHERE numreg IN(SELECT id_campo_desconto FROM ".$this->getTabelaVendaItemDesconto()." WHERE ".$this->getCampoChaveTabelaVendaItemDesconto()." IN(SELECT numreg FROM ".$this->getTabelaVendaItem()." WHERE ".$this->getCampoChaveTabelaVendaItem()." = ".$this->getNumregVenda()."))";
        }
        else{
            $SqlCampoDesconto = "SELECT * FROM is_param_campo_desconto WHERE sn_ativo = 1";
        }
        $QryCampoDesconto = query($SqlCampoDesconto);
        while($ArCampoDesconto = farray($QryCampoDesconto)){
            $this->ArrayCamposDescontos[$ArCampoDesconto['numreg']] = $ArCampoDesconto;
        }
    }
    /**
     * Esta fun??o retorna o numreg do registro do cabe?alho do or?amento/pedido
     * @return int
     */
    public function getNumregVenda(){
        return $this->NumregVenda;
    }

    public function getPessoa(){
        return $this->Pessoa;
    }

    public function getIdPessoa(){
        return $this->getPessoa()->getNumregPessoa();
    }

    public function getContato(){
        return $this->Contato;
    }

    public function getStatusPoliticaComercialDesc(){
        return $this->StatusPoliticaComercialDesc;
    }

    public function setStatusPoliticaComercialDesc($StatusPoliticaComercialDesc){
        $this->StatusPoliticaComercialDesc = $StatusPoliticaComercialDesc;
    }

    public function getPermiteFinalizar(){
        return $this->PermiteFinalizar;
    }

    public function setPermiteFinalizar($PermiteFinalizar){
        $this->PermiteFinalizar = $PermiteFinalizar;
    }

    public function getTabelaVenda(){
        return $this->TabelaVenda;
    }

    public function getTabelaVendaItem(){
        return $this->TabelaVendaItem;
    }

    public function getTabelaVendaItemDesconto(){
        return $this->TabelaVendaItemDesconto;
    }

    public function getTabelaVendaItemPre(){
        return $this->TabelaVendaItemPre;
    }

    public function getTabelaVendaRepresentante(){
        return $this->TabelaVendaRepresentante;
    }

    public function getTabelaVendaItemRepresentanteComissao(){
        return $this->TabelaVendaItemRepresentanteComissao;
    }

    public function getTabelaVendaCondPagtoEspecial(){
        return $this->TabelaVendaCondPagtoEspecial;
    }

    public function getCampoChaveTabelaVenda(){
        return $this->CampoChaveTabelaVenda;
    }

    public function getCampoChaveTabelaVendaItem(){
        return $this->CampoChaveTabelaVendaItem;
    }

    public function getCampoChaveTabelaVendaItemDesconto(){
        return $this->CampoChaveTabelaVendaItemDesconto;
    }

    public function getCampoChaveTabelaVendaItemPre(){
        return $this->CampoChaveTabelaVendaItemPre;
    }

    public function getCampoChaveTabelaVendaRepresentante(){
        return $this->CampoChaveTabelaVendaRepresentante;
    }

    public function getCampoChaveTabelaVendaItemRepresentanteComissao(){
        return $this->CampoChaveTabelaVendaItemRepresentanteComissao;
    }

    public function getCampoChaveTabelaVendaCondPagtoEspecial(){
        return $this->CampoChaveTabelaVendaCondPagtoEspecial;
    }

    public function getTipoVenda(){
        return $this->TipoVenda;
    }

    public function getPoliticaComercialDescVendaMedia(){
        return $this->PoliticaComercialDescVendaMedia;
    }

    public function getPoliticaComercialDescVendaCampoDescontoFixoTabPreco(){
        return $this->PoliticaComercialDescVendaCampoDescontoFixoTabPreco;
    }

    public function getPoliticaComercialDescVendaCampoDescontoFixoPessoa(){
        return $this->PoliticaComercialDescVendaCampoDescontoFixoPessoa;
    }

    public function getPoliticaComercialDescVendaCampoDescontoFixoInformado(){
        return $this->PoliticaComercialDescVendaCampoDescontoFixoInformado;
    }

    public function getItensPre($DesconsideraItensJaInclusos=true){
        if($DesconsideraItensJaInclusos === false){
            return $this->ItensSolicitacaoComercial;
        }
        else{
            $ArrayRetorno = array();
            foreach($this->ItensPre as $ItemPre){ /* Loop dos itens da solicita??o */
                if($ItemPre['id_produto'] != ''){ /* Verifica se ? um produto comercial */
                    if($this->VerificaSeExisteItemPre($ItemPre['numreg'])){ /* Verifica se determinado produto j? est? na venda */
                        continue;
                    }
                    else{
                        $ArrayRetorno[] = $ItemPre;
                    }
                }
                else{ /* Caso seja um produto n?o comercial */
                    $Itens = $this->getItens();
                    $ItemExiste = false;
                    foreach($Itens as $Item){ /* Loop para verificar se o item n?o comercial est? na venda j? */
                        if($Item->getDadosVendaItem('inc_descricao') == $ItemPre['inc_descricao']){
                            $ItemExiste = true;
                            break;
                        }
                    }
                    if($ItemExiste === false){ /* Verifica se determinado produto j? est? na venda */
                        $ArrayRetorno[] = $ItemPre;
                    }
                }
            }
            return $ArrayRetorno;
        }
    }

    public function getItensSolicitacaoComercial($DesconsideraItensJaInclusos=true){
        if($DesconsideraItensJaInclusos === false){
            return $this->ItensSolicitacaoComercial;
        }
        else{
            $ArrayRetorno = array();
            foreach($this->ItensSolicitacaoComercial as $ItemSolicitacaoComercial){ /* Loop dos itens da solicita??o */
                if($ItemSolicitacaoComercial['id_produto'] != ''){ /* Verifica se ? um produto comercial */
                    if($this->VerificaSeExisteProduto($ItemSolicitacaoComercial['id_produto'])){ /* Verifica se determinado produto j? est? na venda */
                        continue;
                    }
                    else{
                        $ArrayRetorno[] = $ItemSolicitacaoComercial;
                    }
                }
                else{ /* Caso seja um produto n?o comercial */
                    $Itens = $this->getItens();
                    $ItemExiste = false;
                    foreach($Itens as $Item){ /* Loop para verificar se o item n?o comercial est? na venda j? */
                        if($Item->getDadosVendaItem('inc_descricao') == $ItemSolicitacaoComercial['inc_descricao']){
                            $ItemExiste = true;
                            break;
                        }
                    }
                    if($ItemExiste === false){ /* Verifica se determinado produto j? est? na venda */
                        $ArrayRetorno[] = $ItemSolicitacaoComercial;
                    }
                }
            }
            return $ArrayRetorno;
        }
    }

    public function getItensOportunidadePai($DesconsideraItensJaInclusos=true){
        if($DesconsideraItensJaInclusos === false){
            return $this->ItensOportunidadePai;
        }
        else{
            $ArrayRetorno = array();
            foreach($this->ItensOportunidadePai as $ItemOportunidadePai){ /* Loop dos itens da solicita??o */
                if($ItemOportunidadePai['id_produto'] != ''){ /* Verifica se ? um produto comercial */
                    if($this->VerificaSeExisteProduto($ItemOportunidadePai['id_produto'])){ /* Verifica se determinado produto j? est? na venda */
                        continue;
                    }
                    else{
                        $ArrayRetorno[] = $ItemOportunidadePai;
                    }
                }
                else{ /* Caso seja um produto n?o comercial */
                    $Itens = $this->getItens();
                    $ItemExiste = false;
                    foreach($Itens as $Item){ /* Loop para verificar se o item n?o comercial est? na venda j? */
                        if($Item->getDadosVendaItem('inc_descricao') == $ItemOportunidadePai['inc_descricao']){
                            $ItemExiste = true;
                            break;
                        }
                    }
                    if($ItemExiste === false){ /* Verifica se determinado produto j? est? na venda */
                        $ArrayRetorno[] = $ItemOportunidadePai;
                    }
                }
            }
            return $ArrayRetorno;
        }
    }

    public function getSnGeradoBonificacaoAuto(){
        if($this->getDadosVenda('sn_gerado_bonificacao_auto') == 1){
            return true;
        }
        else{
            return false;
        }
    }

    public function getSnGerouPedidoBonificacao(){
        if($this->getDadosVenda('sn_gerou_pedido_bonificacao') == 1){
            return true;
        }
        else{
            return false;
        }
    }

    public function isOrcamento(){
        return ($this->TipoVenda == 1)?true:false;
    }

    public function isPedido(){
        return ($this->TipoVenda == 2)?true:false;
    }

    public function getGrupoTabPreco(){
        return $this->getDadosVenda('id_grupo_tab_preco');
    }

    public function isAtacado(){
        if($this->getGrupoTabPreco() == '1'){
            return true;
        }
        return false;
    }

    public function isVarejo(){
        if($this->getGrupoTabPreco() == '2'){
            return true;
        }
        return false;
    }

    public function isCancelado(){
        if($this->isPedido()){
            return ($this->getDadosVenda('id_situacao_venda') == '6')?true:false;
        }
        else{
            return ($this->getDadosVenda('id_situacao_venda') == '4')?true:false;
        }
    }

    public function getSnOrcamentoPerdido(){
        return ($this->isOrcamento() && $this->getDadosVenda('id_situacao_venda') == '4')?true:false;
    }

    public function isPrecoInformado(){
        return ($this->getDadosVenda('id_tp_preco') == '1')?true:false;
    }

    public function isTipoVenda(){
        return ($this->getDadosTpVenda('id_tipo') == 1)?true:false;
    }

    public function isTipoBonificacao(){
        return ($this->getDadosTpVenda('id_tipo') == 2)?true:false;
    }

    public function getIdTabPreco(){
        return $this->DadosVenda['id_tab_preco'];
    }

    public function getAvaliadoComercial(){
        if($this->getDadosVenda('sn_avaliado_comercial') == 1){
            return true;
        }
        return false;
    }

    public function getItem($IndiceItem){
        return $this->Itens[$IndiceItem];
    }

    public function getItens(){
        return $this->Itens;
    }

    public function getVendaRepresentante($IndiceRepresentante){
        return $this->Representante[$IndiceRepresentante];
    }

    public function getVendaRepresentantes(){
        return $this->Representantes;
    }

    public function getIdEstabelecimento(){
        return $this->DadosVenda['id_estabelecimento'];
    }

    public function getIdRepresPri(){
        return NULL;
    }

    public function getPctDescontoTabPreco(){
        return $this->getDadosVenda('pct_desconto_tab_preco');
    }

    public function getPctDescontoInformado(){
        return $this->getDadosVenda('pct_desconto_informado');
    }

    public function getPctDescontoPessoa(){
        return $this->getDadosVenda('pct_desconto_pessoa');
    }

    public function getTituloVenda($html = true,$ucw = true){
        $StringRetorno = $this->TituloVenda;
        if($ucw == true){
            $StringRetorno = ucwords($StringRetorno);
        }
        if($html == true){
            $StringRetorno = htmlentities($StringRetorno);
        }
        return $StringRetorno;
    }

    public function getArrayCamposDescontos(){
        return $this->ArrayCamposDescontos;
    }

    public function getDadosCampoDesconto($NumregCampoDesconto){
        return $this->ArrayCamposDescontos[$NumregCampoDesconto];
    }

    public function getDadosVenda($IdCampo=NULL){
        if($IdCampo == NULL){
            return $this->DadosVenda;
        }
        else{
            return $this->DadosVenda[$IdCampo];
        }

    }

    /**
     * Esta fun??o altera as flags de aprovado do item no banco de dados
     * @param int $IndiceItem numreg do item na tabela de itens da venda
     * @param int $Status 0 false/1 true
     * @param string $Justificativa
     */
    public function AprovaReprovaItem($IndiceItem,$Status,$Justificativa){
        if($Status == 0){
            $this->getItem($IndiceItem)->AprovaItem($Justificativa);
        }
        else{
            $this->getItem($IndiceItem)->ReprovaItem($Justificativa);
        }
    }

    public function PossuiItensNaoComerciais(){
        foreach($this->getItens() as $IndiceItem => $Item){
            if(!$Item->getItemComercial()){
                return true;
            }
        }
        return false;
    }

    public function PossuiItensSemPreco(){
        foreach($this->getItens() as $IndiceItem => $Item){
            $VlUnitarioBaseCalculo = $Item->getDadosVendaItem('vl_unitario_base_calculo');
            if($VlUnitarioBaseCalculo <= 0){
                return true;
            }
        }
        return false;
    }

    public function PossuiItensSemCFOP(){
        foreach($this->getItens() as $IndiceItem => $Item){
            if($Item->getItemComercial()){
                $IdCFOP = $Item->getDadosVendaItem('id_cfop');
                if(empty($IdCFOP)){
                    return true;
                }
            }
        }
        return false;
    }

    public function PossuiItensSemReferencia(){
        foreach($this->getItens() as $IndiceItem => $Item){
            $IdReferecia = $Item->getDadosVendaItem('id_referencia');
            if($IdReferecia == ''){
                return true;
            }
        }
        return false;
    }

    public function PossuiItensInativos(){
        foreach($this->getItens() as $IndiceItem => $Item){
            if($Item->getItemComercial()){
                if(!$Item->getProduto()->getSnAtivo()){
                    return true;
                }
            }
        }
        return false;
    }

    public function getVlBonificacaoComTolerancia(){
        $VlBonificacao = 0;
        $QryPedidoBonificacao = query("SELECT vl_total_bonificacao FROM is_pedido WHERE id_pedido_bonificacao = ".$this->getNumregVenda());
        if(numrows($QryPedidoBonificacao) == 1){
            $ArPedidoBonificacao = farray($QryPedidoBonificacao);
            $VlBonificacao = $ArPedidoBonificacao['vl_total_bonificacao'];
            $VlBonificacao += $this->getVendaParametro()->getVlToleranciaPedidoBonificacao();
        }
        return $VlBonificacao;
    }

    public function getDigitacaoCompleta(){
        if($this->getDadosVenda('sn_digitacao_completa') == 1){
            return true;
        }
        else{
            return false;
        }
    }

    public function getGerouPedido(){
        if($this->getDadosVenda('sn_gerou_pedido') == 1){
            return true;
        }
        else{
            return false;
        }
    }

    public function CancelaVenda($IdMotivoCancelamento){
        if($this->getDadosVenda('id_situacao_venda') <= 1){
            $this->setDadoVenda('sn_digitacao_completa', 1);
            $this->setDadoVenda('id_situacao_venda', 6);
            $this->setDadoVenda('sn_em_aprovacao_comercial', 0);
            $this->setDadoVenda('sn_avaliado_comercial', 0);
            $this->setDadoVenda('sn_aprovado_comercial', 0);
            $this->setDadoVenda('id_motivo_cancelamento', $IdMotivoCancelamento);
            $this->setDadoVenda('id_usuario_cancelamento', $_SESSION['id_usuario']);
            $this->setDadoVenda('dt_cancelamento', date("Y-m-d"));
            if($this->AtualizaDadosVendaBD()){
                $this->GravaLogBD(8,'Cancelamento da Venda');
                return true;
            }
        }
        else{
            $this->setMensagem('Situa??o n?o permite cancelamento.');
        }
        return false;
    }

    /**
     * LOG Eventos
     * 1 - Adicionar Item
     * 2 - Adicionar Participante
     * 3 - Alterar Cabe?alho
     * 4 - Alterar Comiss?o
     * 5 - Alterar Frete
     * 6 - Alterar Item
     * 7 - Aprova??o
     * 8 - Cancelamento da Venda
     * 9 - Clonar
     * 10 - Criar pedido Bonifica??o
     * 11 - Enviar por E-mail
     * 12 - Excluir Item
     * 13 - Finalizar
     * 14 - Impress?o
     * 15 - Reabrir Or?amento
     * 16 - Reprova??o
     * 17 - Restaurar Revis?o
     * 18 - Transformar em Pedido
     * 19 - Alterar Comiss?o do Item
     * 20 - Atualiza??o Atendimento Laborat?rio
     * 21 - Log Exporta??o ODBC
     * 22 - Perda Or?amento
     * 23 - Cancelar Bonifica??o
     * 24 - Erro inserir item
     * @param type $IdEvento
     * @param type $DescricaoResumida
     * @param type $DescricaoDetalhada
     * @param type $InformacoesTecnicas
     * @return type
     */
    public function GravaLogBD($IdEvento,$DescricaoResumida,$DescricaoDetalhada='',$InformacoesTecnicas=''){
        if($IdEvento == '' || $DescricaoResumida == ''){
            $this->setMensagemDebug('Par?metro incorreto na gera??o de Log.('.__FILE__.'-'.__LINE__.')');
            return false;
        }
        $ArInsertLog = array(
            'id_usuario'            => (($_SESSION)?$_SESSION['id_usuario']:''),
            'data'                  => date("Y-m-d"),
            'hora'                  => date("H:i:s"),
            'tipo_venda'            => $this->getTipoVenda(),
            'id_venda'              => $this->getNumregVenda(),
            'id_evento'             => $IdEvento,
            'descricao_resumida'    => $DescricaoResumida,
            'descricao'             => $DescricaoDetalhada,
            'descricao_tecnica'     => $InformacoesTecnicas
        );
        $SqlInsertLog = AutoExecuteSql(TipoBancoDados, 'is_log_venda', $ArInsertLog, 'INSERT');
        $QryInsertLog = query($SqlInsertLog);
        if(!$QryInsertLog){
            $this->setMensagemDebug('Erro de SQL ao inserir Log.('.__FILE__.'-'.__LINE__.')');
            return false;
        }
        return true;
    }

    public function getAprovadoComercial(){
        if($this->getDadosVenda('sn_avaliado_comercial') == 1){
            return true;
        }
        else{
            return false;
        }
    }

    public function getVendaDescontoPrecisao($NumregCampoDesconto){
        return $this->ArrayCamposDescontos[$NumregCampoDesconto]['precisao'];
    }

    public function getMensagemAdicionaItemErro(){
        return $this->MensagemAdicionaItemErro;
    }

    public function getAtualizacaoItensErro(){
        return $this->AtualizacaoItensErro;
    }

    public function getMensagemAtualizacaoItens(){
        $StringRetorno = '';
        if($this->AtualizacaoItensErro === false){
            $StringRetorno = 'Itens Atualizados com Sucesso!';
        }
        else{
            foreach($this->MensagemAtualizacaoItens as $k => $v){
                $StringRetorno .= '<strong style="color:#FF0000;">Item: '.$v[0].'</strong><i> - '.$v[1].' - '.$v[2].'</i>: '.$v[3];
                $StringRetorno .= '<hr />';
            }
        }
        return $StringRetorno;
    }

    public function getMedDiasCondPagto(){
        if($this->MediaDiasCondPagto != ''){
            return $this->MediaDiasCondPagto;
        }
        $MedDiasCondPagto = $this->getDadosVenda('id_cond_pagto');
        if(empty($MedDiasCondPagto)){
            return false;
        }
        $SqlCondPagto = "SELECT media_dias FROM is_cond_pagto WHERE numreg = ".$this->getDadosVenda('id_cond_pagto');
        $QryCondPagto = query($SqlCondPagto);
        $ArCondPagto = farray($QryCondPagto);
        $this->MediaDiasCondPagto = $ArCondPagto['media_dias'];
        return $this->MediaDiasCondPagto;
    }

    public function getMensagemRemoveItemErro(){
        $StringRetorno = '';
        if($this->RemoveItemErro === false){
            $StringRetorno = 'Item removido com sucesso!';
        }
        else{
            $StringRetorno .= $this->MensagemRemoveItem;
        }
        return $StringRetorno;
    }

    public function getPctDescontoBaseBD($Produto){
        $SqlDescontoBase = "SELECT * FROM is_param_desconto_base
            WHERE ('".date("Y-m-d")."' BETWEEN dthr_validade_ini AND dthr_validade_fim)
                AND (id_pessoa = '".TrataApostrofoBD($this->getPessoa->getDadoPessoa('numreg'))."' OR id_pessoa IS NULL)
                AND (id_pessoa_regiao = '".TrataApostrofoBD($this->getPessoa->getDadoPessoa('id_regiao'))."' OR id_pessoa_regiao IS NULL)
                AND (pessoa_cidade = '".TrataApostrofoBD($this->getPessoa->getDadoPessoa('cidade'))."' OR pessoa_cidade IS NULL)
                AND (pessoa_uf = '".TrataApostrofoBD($this->getPessoa->getDadoPessoa('uf'))."' OR pessoa_uf IS NULL)
                AND (id_pessoa_canal_venda = '".TrataApostrofoBD($this->getPessoa->getDadoPessoa('id_canal_venda'))."' OR id_pessoa_canal_venda IS NULL)
                AND (id_pessoa_grupo_cliente = '".TrataApostrofoBD($this->getPessoa->getDadoPessoa('id_grupo_cliente'))."' OR id_pessoa_grupo_cliente IS NULL)
                AND (id_tp_pessoa = '".TrataApostrofoBD($this->getPessoa->getDadoPessoa('id_tp_pessoa'))."' OR id_tp_pessoa IS NULL)
                AND (sn_contribuinte_icms = '".TrataApostrofoBD($this->getPessoa->getDadoPessoa('sn_contribuinte_icms'))."' OR sn_contribuinte_icms IS NULL)

                AND (id_produto = '".TrataApostrofoBD($Produto->getNumregProduto())."' OR id_produto IS NULL)
                AND (id_produto_linha = '".TrataApostrofoBD($Produto->getDadosProduto('id_linha'))."' OR id_produto_linha IS NULL)
                AND (id_produto_familia = '".TrataApostrofoBD($Produto->getDadosProduto('id_familia'))."' OR id_produto_familia IS NULL)
                AND (id_produto_familia_comercial = '".TrataApostrofoBD($Produto->getDadosProduto('id_familia_comercial'))."' OR id_produto_familia_comercial IS NULL)
                AND (id_produto_grupo_estoque = '".TrataApostrofoBD($Produto->getDadosProduto('id_grupo_estoque'))."' OR id_produto_grupo_estoque IS NULL)

                AND (id_pedido_estabelecimento = '".TrataApostrofoBD($this->getIdEstabelecimento())."' OR id_pedido_estabelecimento IS NULL)
                AND (id_pedido_repres_pri = '".TrataApostrofoBD($this->getIdRepresPri())."' OR id_pedido_repres_pri IS NULL)
                AND (id_pedido_tab_preco = '".TrataApostrofoBD($this->getIdTabPreco())."' OR id_pedido_tab_preco IS NULL)
                AND (id_pedido_tp_pedido = '".TrataApostrofoBD($this->getIdTpVenda())."' OR id_pedido_tp_pedido IS NULL)
                AND (id_pedido_tp_venda = '".TrataApostrofoBD($this->getIdTpVenda())."' OR id_pedido_tp_venda IS NULL)
                AND (id_pedido_dest_merc = '".TrataApostrofoBD($this->getIdTpVenda())."' OR id_pedido_dest_merc IS NULL)
                AND (id_pedido_moeda = '".TrataApostrofoBD($this->getIdTpVenda())."' OR id_pedido_moeda IS NULL)
                AND (('".TrataApostrofoBD($this->getIdTpVenda())."' BETWEEN med_dias_ini AND med_dias_fim) OR (med_dias_ini IS NULL AND med_dias_fim IS NULL))
                ";
    }

    public function getTitleBar(){

        $TituloVenda = utf8_encode( $this->TituloVenda);
        $TituloVenda = ($this->isTipoBonificacao())?$TituloVenda.' de bonificação':$TituloVenda;

        switch($this->PassoAtual){
            case 'p1':
                $StringPassoAtual = 'Passo 1';
                $StringDetalhePassoAtual = 'Dados do '.ucwords($TituloVenda);
                break;
            case 'p2': 
                $StringPassoAtual = 'Passo 2';
                $StringDetalhePassoAtual = 'Itens do '.ucwords($TituloVenda);
                break;
            case 'p3':
                $StringPassoAtual = 'Passo 3';
                $StringDetalhePassoAtual = 'Pol&iacute;tica Comercial';
                break;
            case 'p4':
                $StringPassoAtual = 'Passo 4';
                $StringDetalhePassoAtual = 'A&ccedil;&otilde;es do '.ucwords($TituloVenda);
                break;
        }
        $StringRetorno = '<hr size="1" />';
        $StringRetorno .= '<span class="venda_span_header">Voc&ecirc; est&aacute; no '.$StringPassoAtual.' ('.$StringDetalhePassoAtual.') <span>N&ordm; '.$this->getDadosVenda('id_venda_cliente').' ('.$this->getNumregVenda().')</span></span>';
        if($this->DadosVenda['sn_digitacao_completa'] == 0){
            $StringRetorno .= '<span class="venda_span_header" style="color:#000000;"> <img src="img/icone_atencao.jpg" /> '.ucwords($TituloVenda).' n&atilde;o finalizado</span>';
        }
        if($this->DadosVenda['sn_avaliado_comercial'] == 1 && $this->DadosVenda['sn_aprovado_comercial'] == 0){
            $StringRetorno .= '<span class="venda_span_header" style="color:#000000;"> | reprovado pelo comercial</span>';
        }
        if($this->DadosVenda['id_revisao'] != ''){
            $StringRetorno .= '<span class="venda_span_header" style="color:#000000;"> | revis&atilde;o N&ordm; '.$this->DadosVenda['id_revisao'].'</span>';
        }

        if($this->getDigitacaoCompleta()){

            $Url = new Url;
            $Url->setUrl(curPageURL());

            $Url->AlteraParam('ppagina', 'p1');
            $StringRetorno .= '&nbsp;&nbsp;&nbsp;<input type="button" class="botao_jquery btn_passo" href='.$Url->getUrl().' value="Passo 1">';

            $Url->AlteraParam('ppagina', 'p2');
            $StringRetorno .= '&nbsp;&nbsp;&nbsp;<input type="button" class="botao_jquery btn_passo" href='.$Url->getUrl().' value="Passo 2">';

            $Url->AlteraParam('ppagina', 'p3');
            $StringRetorno .= '&nbsp;&nbsp;&nbsp;<input type="button" class="botao_jquery btn_passo" href='.$Url->getUrl().' value="Passo 3">';

            $Url->AlteraParam('ppagina', 'p4');
            $StringRetorno .= '&nbsp;&nbsp;&nbsp;<input type="button" class="botao_jquery btn_passo" href='.$Url->getUrl().' value="Passo 4">';

        }

        $StringRetorno .= '&nbsp;&nbsp;&nbsp;<input type="button" class="botao_jquery btn_sair" value="Sair">';
        $StringRetorno .= '<hr size="1" />';
        return $StringRetorno;
    }

    public function getPrecisaoValor(){
        return $this->PrecisaoValor;
    }

    public function getPrecisaoQtde(){
        return $this->PrecisaoQtde;
    }

    public function getPrecisaoDesconto(){
        return $this->PrecisaoDesconto;
    }

    public function getVlTotalVendaBruto(){
        return $this->VlTotalVendaBruto;
    }

    public function getVlTotalVendaLiquido(){
        return $this->VlTotalVendaLiquido;
    }

    public function getVlTotalVendaIPI(){
        return $this->VlTotalVendaIPI;
    }

    public function getVlTotalVendaST(){
        return $this->VlTotalVendaST;
    }

    public function getVlTotalVenda(){
        return $this->VlTotalVenda;
    }

    public function getVlTotalVendaFrete(){
        return $this->VlTotalVendaFrete;
    }

    public function getPesoTotalVenda(){
        return $this->PesoTotalVenda;
    }

    public function getDadosMoeda($IdMoeda,$IdCampo){
        if($this->ArMoedas === false){
            $QryMoedas = query("SELECT * FROM is_moeda ORDER BY numreg");
            while($ArMoedas = farray($QryMoedas)){
                $this->ArMoedas[$ArMoedas['numreg']] = $ArMoedas;
            }
        }
        return $this->ArMoedas[$IdMoeda][$IdCampo];
    }

    public function getSnExibeQtdePorUnidMedida(){
        if($this->getVendaParametro()->getModoUnidMedida() == 2){
            return true;
        }
        elseif($this->getVendaParametro()->getModoUnidMedida() == 3 && $this->isAtacado()){
            return true;
        }
        return false;
    }

    public function getSnExibeEmbalagem(){
        if($this->getVendaParametro()->getModoUnidMedida() == 3 && $this->isAtacado()){
            return true;
        }
        return false;
    }

    public function getQtdeItens($Unico = false){
        if($Unico === false){
            return count($this->getItens());
        }
        else{
            $ArItens = array();
            foreach($this->getItens() as $IndiceItem => $Item){
                $ArItens[$Item->getDadosVendaItem('id_produto')] = $Item->getDadosVendaItem('id_produto');
            }
            return count($ArItens);
        }

    }

    public function getQtdeRepresentantes(){
        return count($this->getRepresentantes());
    }

    public function getEmAprovacao(){
        if($this->getDadosVenda('sn_em_aprovacao_comercial') == 1 && $this->getDadosVenda('sn_avaliado_comercial') == 0){
            return true;
        }
        return false;
    }

    public function CarregaRepresentantes(){
        $QryVendaRepresentantes = query("SELECT numreg FROM ".$this->getTabelaVendaRepresentante()." WHERE ".$this->getCampoChaveTabelaVendaRepresentante()." = ".$this->getNumregVenda()." ORDER BY sn_representante_principal DESC,numreg");
        while($ArVendaRepresentantes = farray($QryVendaRepresentantes)){
            $Representante = new VendaRepresentante($this,$ArVendaRepresentantes['numreg']);
            if($Representante){
                $this->Representantes[$ArVendaRepresentantes['numreg']] = $Representante;
            }
        }
    }

    public function encodeDeParaCamposValor($ArDados){
        $ArDadosRetorno = $ArDados;
        foreach($ArDados as $k => $v){
            if(array_key_exists($k,$this->ArrayDeParaCamposEspecificosTabelaVenda)){
                unset($ArDadosRetorno[$k]);
                $ArDadosRetorno[$this->ArrayDeParaCamposEspecificosTabelaVenda[$k]] = $v;
            }
        }
        return $ArDadosRetorno;
    }

    public function decodeDeParaCamposValor($ArDados){
        $ArDadosRetorno = $ArDados;
        foreach($ArDados as $k => $v){
            $Search = array_search($k,$this->ArrayDeParaCamposEspecificosTabelaVenda);
            if($Search != ''){
                unset($ArDadosRetorno[$k]);
                $ArDadosRetorno[$Search] = $v;
            }
        }
        return $ArDadosRetorno;
    }

    public function DefineVariaveisOrcamentoPedido(){
        if($this->getTipoVenda() == 1){
            if($this->VisualizarRevisao){
                $this->TituloVenda = 'orçamento(revisão)';
                $this->TabelaVenda = 'is_orcamento_revisao';
                $this->TabelaVendaItem = 'is_orcamento_item_revisao';
                $this->TabelaVendaItemDesconto = 'is_orcamento_item_desconto_revisao';
                $this->TabelaVendaItemRepresentanteComissao = 'is_orcamento_item_representante_comissao_revisao';
                $this->TabelaVendaRepresentante = 'is_orcamento_representante_revisao';
                $this->TabelaVendaCondPagtoEspecial = 'is_orcamento_cond_pagto_especial_revisao';
                $this->CampoChaveTabelaVenda = 'id_orcamento';
                $this->CampoChaveTabelaVendaItem = 'id_orcamento';
                $this->CampoChaveTabelaVendaItemDesconto = 'id_orcamento_item';
                $this->CampoChaveTabelaVendaItemRepresentanteComissao = 'id_orcamento_item';
                $this->CampoChaveTabelaVendaRepresentante = 'id_orcamento';
                $this->CampoChaveTabelaVendaCondPagtoEspecial = 'id_orcamento';
            }
            else{
                $this->TituloVenda = 'orçamento';
                $this->TabelaVenda = 'is_orcamento';
                $this->TabelaVendaItem = 'is_orcamento_item';
                $this->TabelaVendaItemDesconto = 'is_orcamento_item_desconto';
                $this->TabelaVendaItemPre = 'is_orcamento_item_pre';
                $this->TabelaVendaItemRepresentanteComissao = 'is_orcamento_item_representante_comissao';
                $this->TabelaVendaRepresentante = 'is_orcamento_representante';
                $this->TabelaVendaCondPagtoEspecial = 'is_orcamento_cond_pagto_especial';
                $this->CampoChaveTabelaVenda = 'id_orcamento';
                $this->CampoChaveTabelaVendaItem = 'id_orcamento';
                $this->CampoChaveTabelaVendaItemDesconto = 'id_orcamento_item';
                $this->CampoChaveTabelaVendaItemPre = 'id_orcamento';
                $this->CampoChaveTabelaVendaItemRepresentanteComissao = 'id_orcamento_item';
                $this->CampoChaveTabelaVendaRepresentante = 'id_orcamento';
                $this->CampoChaveTabelaVendaCondPagtoEspecial = 'id_orcamento';
            }
        }
        elseif($this->getTipoVenda() == 2){
            if($this->VisualizarRevisao){
                $this->TituloVenda = 'pedido(revisão)';
                $this->TabelaVenda = 'is_pedido_revisao';
                $this->TabelaVendaItem = 'is_pedido_item_revisao';
                $this->TabelaVendaItemDesconto = 'is_pedido_item_desconto_revisao';
                $this->TabelaVendaItemRepresentanteComissao = 'is_pedido_item_representante_comissao_revisao';
                $this->TabelaVendaRepresentante = 'is_pedido_representante_revisao';
                $this->TabelaVendaCondPagtoEspecial = 'is_pedido_cond_pagto_especial_revisao';
                $this->CampoChaveTabelaVenda = 'id_pedido';
                $this->CampoChaveTabelaVendaItem = 'id_pedido';
                $this->CampoChaveTabelaVendaItemDesconto = 'id_pedido_item';
                $this->CampoChaveTabelaVendaItemRepresentanteComissao = 'id_pedido_item';
                $this->CampoChaveTabelaVendaRepresentante = 'id_pedido';
                $this->CampoChaveTabelaVendaCondPagtoEspecial = 'id_pedido';
            }
            else{
                $this->TituloVenda = 'pedido';
                $this->TabelaVenda = 'is_pedido';
                $this->TabelaVendaItem = 'is_pedido_item';
                $this->TabelaVendaItemDesconto = 'is_pedido_item_desconto';
                $this->TabelaVendaItemPre = 'is_pedido_item_pre';
                $this->TabelaVendaItemRepresentanteComissao = 'is_pedido_item_representante_comissao';
                $this->TabelaVendaRepresentante = 'is_pedido_representante';
                $this->TabelaVendaCondPagtoEspecial = 'is_pedido_cond_pagto_especial';
                $this->CampoChaveTabelaVenda = 'id_pedido';
                $this->CampoChaveTabelaVendaItem = 'id_pedido';
                $this->CampoChaveTabelaVendaItemDesconto = 'id_pedido_item';
                $this->CampoChaveTabelaVendaItemPre = 'id_pedido';
                $this->CampoChaveTabelaVendaItemRepresentanteComissao = 'id_pedido_item';
                $this->CampoChaveTabelaVendaRepresentante = 'id_pedido';
                $this->CampoChaveTabelaVendaCondPagtoEspecial = 'id_pedido';
            }
        }
    }

    private function CarregaItensBD(){
        $QryVendaItens = query("SELECT numreg FROM ".$this->getTabelaVendaItem()." WHERE ".$this->getCampoChaveTabelaVendaItem()." = ".$this->getNumregVenda()." ORDER BY id_sequencia");
        while($ArVendaItens = farray($QryVendaItens)){
            $Item = new VendaItem($this,$ArVendaItens['numreg']);
            if($Item){
                $this->Itens[$ArVendaItens['numreg']] = $Item;
            }
        }

        if($this->VendaParametro->getSnUsaKit()){
            $QryVendaItensKIT = query("SELECT id_sequencia_kit,id_kit FROM ".$this->getTabelaVendaItem()." WHERE ".$this->getCampoChaveTabelaVendaItem()." = '".$this->getNumregVenda()."' AND NOT id_sequencia_kit IS NULL AND NOT id_kit IS NULL GROUP BY id_sequencia_kit,id_kit ORDER BY id_sequencia_kit");
            $NumRowsVendaItensKIT = numrows($QryVendaItensKIT);
            if($NumRowsVendaItensKIT > 0){
                while($ArVendaItensKIT = farray($QryVendaItensKIT)){
                    $IdSequenciaKIT = $ArVendaItensKIT['id_sequencia_kit'];
                    $this->KIT[$IdSequenciaKIT] = new VendaKit($this,$IdSequenciaKIT);
                }
            }
        }
    }

    /**
     *
     * @param type $IdTpParticipacaoVenda
     * @param type $IdRepresentante
     * @param type $PctComissao
     * @param type $SnPrincipal
     * @return boolean
     */
    public function AdicionaRepresentanteBD($IdTpParticipacaoVenda,$IdRepresentante,$PctComissao,$SnPrincipal=0){
        if(empty($IdRepresentante)){
            $this->setMensagem('Representante n?o informado.');
            return false;
        }
        $PctComissao = TrataFloatPost($PctComissao);
        $QryVerificaRepresentante = query("SELECT COUNT(*) AS CNT FROM ".$this->getTabelaVendaRepresentante()." WHERE id_representante = ".$IdRepresentante." AND ".$this->getCampoChaveTabelaVendaRepresentante()." = '".$this->getNumregVenda()."'");
        $ArVerificaRepresentante = farray($QryVerificaRepresentante);
        if($ArVerificaRepresentante['CNT'] <= 0){
            $ArSqlInsert = array();
            $ArSqlInsert['id_tp_participacao'] = $IdTpParticipacaoVenda;
            $ArSqlInsert['sn_representante_principal'] = $SnPrincipal;
            $ArSqlInsert['pct_comissao'] = $PctComissao;
            $ArSqlInsert['vl_comissao'] = 0;
            $ArSqlInsert['id_representante'] = $IdRepresentante;
            $ArSqlInsert[$this->getCampoChaveTabelaVendaRepresentante()] = $this->getNumregVenda();

            $SqlInsert = AutoExecuteSql(TipoBancoDados,$this->getTabelaVendaRepresentante(),$ArSqlInsert,'INSERT');

            $QryInsert = iquery($SqlInsert);
            if(!is_numeric($QryInsert)){
                $this->setMensagem('Erro de SQL ao inserir representante.');
                return false;
            }
            else{
                $this->GravaLogBD(2,'Participante Adicionado','Numreg do participante:'.$QryInsert);
                return $QryInsert;
            }

        }
        else{
            $this->setMensagem('Participante j&aacute; esta incluso no '.$this->getTituloVenda());
            return false;
        }
        return true;
    }

    public function AtualizaRepresentanteBD($NumregRepresentante,$PctComissao,$SnAlteradoManual=NULL,$JustificativaAlteracaoComissao=''){
        if(empty($NumregRepresentante)){
            return false;
        }
        $PctComissao = TrataFloatPost($PctComissao);
        $VlComissao = uM::uMath_pct_de_valor($PctComissao,$this->getVlTotalVendaLiquido());
        $QryVerificaRepresentante = query("SELECT ".$this->getCampoChaveTabelaVendaRepresentante()." FROM ".$this->getTabelaVendaRepresentante()." WHERE numreg = ".$NumregRepresentante);
        $ArVerificaRepresentante = farray($QryVerificaRepresentante);
        if($ArVerificaRepresentante[$this->getCampoChaveTabelaVendaRepresentante()] == $this->getNumregVenda()){
            $Representante = $this->getRepresentantePorIndice($NumregRepresentante);
            if(!is_object($Representante) || !$Representante instanceof VendaRepresentante){
                return false;
            }
            $PctComissaoAtual = $Representante->getPctComissao();
            $Representante->setDadoVendaRepresentante('pct_comissao', $PctComissao);
            $Representante->setDadoVendaRepresentante('vl_comissao', $VlComissao);
            if($SnAlteradoManual != NULL){
                $Representante->setDadoVendaRepresentante('sn_alterado_manual', $SnAlteradoManual);
                if($PctComissaoAtual != $PctComissao){
                    $this->GravaLogBD(4, 'Altera??o de Comiss?o de '.number_format_min($PctComissaoAtual,0,',','').'% para '.number_format_min($PctComissao,0,',','').'%.', $JustificativaAlteracaoComissao);
                }
            }
            $Representante->AtualizaDadosBD();

            foreach($this->getItens() as $IndiceItem => $Item){
                $VlTotalLiquido = $Item->getDadosVendaItem('vl_total_liquido');
                $VlComissao = uM::uMath_pct_de_valor($PctComissao,$VlTotalLiquido);

                $ItemComissao = $Item->getItemComissao($NumregRepresentante);
                $ItemComissao->setDadosItemComissao('pct_comissao',$PctComissao);
                $ItemComissao->setDadosItemComissao('vl_comissao', $VlComissao);
                $ItemComissao->AtualizaDadosBD();
            }
        }
        else{
            $this->setMensagem('Representante n?o pertence ao '.$this->getTituloVenda());
            return false;
        }
        return true;
    }

    public function ZeraComissaoRepresentante($NumregRepresentante){
        if(empty($NumregRepresentante)){
            return false;
        }
        $Representante = $this->getRepresentantePorIndice($NumregRepresentante);
        if(!is_object($Representante) || !$Representante instanceof VendaRepresentante){
            return false;
        }
        $Representante->setDadoVendaRepresentante('pct_comissao', 0);
        $Representante->setDadoVendaRepresentante('vl_comissao', 0);
        foreach($this->getItens() as $IndiceItem => $Item){
            $ItemComissao = $Item->getItemComissao($NumregRepresentante);
            $ItemComissao->setDadosItemComissao('pct_comissao',0);
            $ItemComissao->setDadosItemComissao('vl_comissao', 0);
        }
        return true;
    }

    public function TransformaRepresentanteEmPrincipalBD($NumregRepresentante){
        if(empty($NumregRepresentante)){
            return false;
        }
        $QryVerificaRepresentante = query("SELECT ".$this->getCampoChaveTabelaVendaRepresentante()." FROM ".$this->getTabelaVendaRepresentante()." WHERE numreg = ".$NumregRepresentante);
        $ArVerificaRepresentante = farray($QryVerificaRepresentante);
        if($ArVerificaRepresentante[$this->getCampoChaveTabelaVendaRepresentante()] == $this->getNumregVenda()){
            $SqlRepresentanteUpdate = "UPDATE ".$this->getTabelaVendaRepresentante()." SET sn_representante_principal = 1 WHERE numreg = ".$NumregRepresentante;
            query($SqlRepresentanteUpdate);
            $SqlRepresentanteUpdate2 = "UPDATE ".$this->getTabelaVendaRepresentante()." SET sn_representante_principal = 0 WHERE numreg != ".$NumregRepresentante." AND ".$this->getCampoChaveTabelaVendaRepresentante()." = '".$this->getNumregVenda()."'";
            query($SqlRepresentanteUpdate2);
        }
        else{
            $this->setMensagem('Representante n?o pertence ao '.$this->getTituloVenda());
            return false;
        }
        return true;
    }

    public function RemoveRepresentanteBD($NumregRepresentante){
        if(empty($NumregRepresentante)){
            return false;
        }
        $QryVerificaRepresentante = query("SELECT ".$this->getCampoChaveTabelaVendaRepresentante()." FROM ".$this->getTabelaVendaRepresentante()." WHERE numreg = ".$NumregRepresentante);
        $ArVerificaRepresentante = farray($QryVerificaRepresentante);
        if($ArVerificaRepresentante[$this->getCampoChaveTabelaVendaRepresentante()] == $this->getNumregVenda()){
            $Representante = $this->getRepresentantePorIndice($NumregRepresentante);
            $IdRepresentante = $Representante->getIdRepresentante();

            $SqlRepresentanteDelete = "DELETE FROM ".$this->getTabelaVendaRepresentante()." WHERE numreg = ".$NumregRepresentante;
            query($SqlRepresentanteDelete);
            unset($this->Representantes[$NumregRepresentante]);

            $SqlDeleteItemComissao = "DELETE FROM ".$this->getTabelaVendaItemRepresentanteComissao()." WHERE ".$this->getCampoChaveTabelaVendaItemRepresentanteComissao()." IN(SELECT numreg FROM ".$this->getTabelaVendaItem()." WHERE ".$this->getCampoChaveTabelaVendaItem()." = ".$this->getNumregVenda().") AND id_representante = ".$IdRepresentante;
            query($SqlDeleteItemComissao);
            foreach($this->getItens() as $IndiceItem => $Item){
                if($Item->getItemComercial()){
                    $Item->RemoveItemComissao($NumregRepresentante);
                }
            }
        }
        else{
            $this->setMensagem('Representante n?o pertence ao '.$this->getTituloVenda());
            return false;
        }
        return true;
    }

    /**
     * Verifica se o representante informado existe na venda
     * @param int $IdRepresentante
     * @return bool
     */
    public function RepresentanteExiste($IdRepresentante){
        foreach($this->getRepresentantes() as $IndiceRepresentante => $Representante){
            if($Representante->getDadosVendaRepresentante('id_representante') == $IdRepresentante){
                return true;
            }
        }
        return false;
    }

    public function getRepresentantePorId($IdRepresentante){
        foreach($this->getRepresentantes() as $IndiceRepresentante => $Representante){
            if($Representante->getDadosVendaRepresentante('id_representante') == $IdRepresentante){
                return $this->getRepresentante($IndiceRepresentante);
            }
        }
        return false;
    }

    public function getRepresentantePorIndice($IndiceRepresentante){
        $Representante = $this->Representantes[$IndiceRepresentante];
        if(is_object($Representante)){
            return $Representante;
        }
        return false;
    }

    /**
     * Grava na tabela de representantes o representante principal da capa da venda
     */
    public function GravaRepresentantePrincipalCapa(){
        $IdRepresentantePrincipalCapa = $this->getDadosVenda('id_representante_principal');
        if(empty($IdRepresentantePrincipalCapa) || $IdRepresentantePrincipalCapa == ''){
            $this->setMensagem('Representante da capa em branco.');
            return false;
        }
        if($this->RepresentanteExiste($IdRepresentantePrincipalCapa)){
            $RepresentantePrincipal = $this->getRepresentantePorId($IdRepresentantePrincipalCapa);
            $this->TransformaRepresentanteEmPrincipalBD($RepresentantePrincipal->getNumregVendaRepresentante());
        }
        else{
            $NumregNovoRepresentante = $this->AdicionaRepresentanteBD(1,$IdRepresentantePrincipalCapa,0,1);
            $this->TransformaRepresentanteEmPrincipalBD($NumregNovoRepresentante);
        }
        VendaCallBackCustom::ExecutaVenda($this, 'GravaRepresentantePrincipalCapa', 'Final');
    }

    public function GravaRepresentantesDaVendaBD(){
        $NumregVenda = $this->getNumregVenda();
        $CampoChave = $this->getCampoChaveTabelaVendaRepresentante();

        /*
         * Verifica se o representante do cliente existe
         */
        $IdRepresentantePadraoPessoa = $this->getPessoa()->getDadoPessoa('id_representante_padrao');
        $IdRepresentantePadraoPessoa = trim($IdRepresentantePadraoPessoa);
        if(!empty($IdRepresentantePadraoPessoa)){
            $this->AdicionaRepresentanteBD(1,$IdRepresentantePadraoPessoa,0,1); //Adiciona o representante como principal
            /*
             * Verificando se o representante possui um gestor
             */
            $UsuarioRepresentantePadraoPessoa = new Usuario($IdRepresentantePadraoPessoa);
            $IdUsuarioGestorRepresentantePadraoPessoa = $UsuarioRepresentantePadraoPessoa->getIdUsuarioGestor();
            if(!empty($IdUsuarioGestorRepresentantePadraoPessoa)){
                $UsuarioGestorRepresentantePadraoPessoa = new Usuario($IdUsuarioGestorRepresentantePadraoPessoa);
                $IdRepresentanteUsuarioGestorRepresentantePadraoPessoa = $UsuarioGestorRepresentantePadraoPessoa->getIdRepresentante();
                if(!empty($IdRepresentanteUsuarioGestorRepresentantePadraoPessoa)){
                    $this->AdicionaRepresentanteBD(3,$IdUsuarioGestorRepresentantePadraoPessoa,0,0); //Adiciona o gestor do representante
                }
            }
        }

        /*
         * Adiciona o vendedor
         */
        $IdVendedor = $this->getDadosVenda('id_usuario_cad');
        $IdVendedor = trim($IdVendedor);
        if(!empty($IdVendedor)){
            $UsuarioVendedor = new Usuario($IdVendedor);
            $IdRepresentanteUsuarioVendedor = $UsuarioVendedor->getIdRepresentante();
            if(!empty($IdRepresentanteUsuarioVendedor)){
                $this->AdicionaRepresentanteBD(2,$IdVendedor,0,0); //Adiciona o vendedor
                /*
                 * Verificando se o representante possui um gestor
                 */
                $UsuarioVendedor = new Usuario($IdVendedor);
                $IdUsuarioGestorVendedor = $UsuarioVendedor->getIdUsuarioGestor();
                if(!empty($IdUsuarioGestorVendedor)){
                    $UsuarioGestorVendedor = new Usuario($IdUsuarioGestorVendedor);
                    $IdRepresentanteUsuarioGestorVendedor = $UsuarioGestorVendedor->getIdRepresentante();
                    if(!empty($IdRepresentanteUsuarioGestorVendedor)){
                        $this->AdicionaRepresentanteBD(4,$IdUsuarioGestorVendedor,0,0); //Adiciona o gestor do vendedor
                    }
                }
            }
        }
        $this->Representantes = array();
        $this->CarregaRepresentantes();
    }

    public function GravaRepresentantePessoaBD(){
        $Pessoa = $this->getPessoa();
        if(is_object($Pessoa) && $Pessoa instanceof Pessoa){
            $IdRepresentantePessoa = $Pessoa->getDadoPessoa('id_representante_padrao');
            if(!empty($IdRepresentantePessoa) && $IdRepresentantePessoa != ''){
                $ArSqlVenda = array();
                $ArSqlVenda['numreg']  = $this->getNumregVenda();
                $ArSqlVenda['id_representante_pessoa'] = $IdRepresentantePessoa;
                $this->setDadoVenda('id_representante_pessoa',$IdRepresentantePessoa);
                $Qry = query(AutoExecuteSql(TipoBancoDados,$this->getTabelaVenda(),$ArSqlVenda,'UPDATE',array('numreg')));
                if(!$Qry){
                    $this->setMensagem('Erro ao atualizar o representante do cliente do cabe?alho da venda. (Linha: '.__LINE__.')');
                    return false;
                }
            }
        }
        VendaCallBackCustom::ExecutaVenda($this, 'GravaRepresentantePessoaBD', 'Final');
        return true;
    }

    public function getDadosEnderecoEntrega($IdCampo){
        $QryEnderecoEntrega = query("SELECT * FROM is_pessoa_endereco WHERE numreg = ".$this->getDadosVenda('id_endereco_entrega'));
        $ArEnderecoEntrega = farray($QryEnderecoEntrega);
        return $ArEnderecoEntrega[$IdCampo];
    }

    public function getDadosTpVenda($IdCampo){
        if(count($this->ArDadosTpVenda) == 0){
            $QryTpVenda = query("SELECT * FROM is_tp_venda WHERE numreg = ".$this->getDadosVenda('id_tp_venda'));
            $ArTpVenda  = farray($QryTpVenda);
            $this->ArDadosTpVenda = $ArTpVenda;
        }
        return $this->ArDadosTpVenda[$IdCampo];
    }

    public function getDadosEstabelecimento($IdCampo){
        $IdEstabelecimento = $this->getIdEstabelecimento();
        if(empty($IdEstabelecimento)){
            $this->setMensagem('C?d do estabeleicmento vazio ('.__LINE__.').');
            return false;
        }
        if(count($this->ArDadosEstabelecimento) == 0){
            $QryEstabelecimento = query("SELECT * FROM is_estabelecimento WHERE numreg = ".$this->getIdEstabelecimento());
            $ArEstabelecimento  = farray($QryEstabelecimento);
            $this->ArDadosEstabelecimento = $ArEstabelecimento;
        }
        return $this->ArDadosEstabelecimento[$IdCampo];
    }

    public function getDadosTabPreco($IdCampo){
        $IdTabPreco = $this->getDadosVenda('id_tab_preco');
        if($IdTabPreco == ''){
            return '';
        }
        $QryTabPreco = query("SELECT * FROM is_tab_preco WHERE numreg = ".$IdTabPreco);

        $ArTabPreco = farray($QryTabPreco);
        return $ArTabPreco[$IdCampo];
    }

    public function getDadosCondPagto($IdCampo){
        $QryCondPagto = query("SELECT * FROM is_cond_pagto WHERE numreg = ".$this->getDadosVenda('id_cond_pagto'));
        $ArCondPagto = farray($QryCondPagto);
        return $ArCondPagto[$IdCampo];
    }

    public function getDadosRepresentantePrincipal($IdCampo){
        $QryRepresentantePrincipal = query("SELECT * FROM ".$this->getTabelaVendaRepresentante()." WHERE ".$this->getCampoChaveTabelaVendaRepresentante()." = ".$this->NumregVenda);
        $ArRepresentantePrincipal = farray($QryRepresentantePrincipal);
        return $ArRepresentantePrincipal[$IdCampo];
    }

    public function GravaCFOPVendaBD(){
        $ArSqlVenda = array();
        $ArSqlVenda['numreg']  = $this->getNumregVenda();

        if(!$this->getSnPermiteAlterarCFOP() || $this->getDadosVenda('id_cfop') == ''){ /* Se ja passou pelo passo 1 e o usu?rio n?o tem permiss?o para alterar a CFOP coloca a CFOP do primeiro item na Venda */
            $CFOP = $this->getCFOPPrimeiroItem();
            $ArSqlVenda['id_cfop'] = $CFOP;
            $this->setDadoVenda('id_cfop',$CFOP);
            $Qry = query(AutoExecuteSql(TipoBancoDados,$this->getTabelaVenda(),$ArSqlVenda,'UPDATE',array('numreg')));
            if(!$Qry){
                $this->setMensagem('Erro ao atualizar a CFOP do cabe?alho da venda. (Linha: '.__LINE__.')');
                return false;
            }
        }
        return true;
    }

    public function getPassouPeloPasso1(){
        if($this->getDadosVenda('sn_passou_pelo_passo1') == '1'){
            return true;
        }
        return false;
    }

    public function getSnPermiteAlterarCFOP(){
        if($this->getUsuario()->getPermissao('sn_permite_alterar_cfop_item') && $this->getVendaParametro()->getPermiteAlterarCFOPItem()){
            return true;
        }
        else{
            return false;
        }
    }

    public function getCFOPPrimeiroItem(){
        $Itens = $this->getItens();
        if(count($Itens) > 0){
            $Item = reset($this->getItens());
            return $Item->getDadosVendaItem('id_cfop');
        }
        else{
            return false;
        }
    }

    /**
     * Retorna o CFOP do cadastro do cliente de acordo com a origem e o destino da venda
     * @param string $PaisEstabelecimento
     * @param string $UFEstabelecimento
     * @param string $PaisEnderecoEntrega
     * @param string $UFEnderecoEntrega
     * @return int|bool C?d. da CFOP ou
     */
    public function getCFOPCliente($PaisEstabelecimento,$UFEstabelecimento,$PaisEnderecoEntrega,$UFEnderecoEntrega){
        if($UFEstabelecimento == '' || $PaisEstabelecimento == '' || $UFEnderecoEntrega == '' || $PaisEnderecoEntrega == ''){/* Se algum par?metros estiver em branco retornal false */
            $this->setMensagem('C?lculo de CFOP (Linha:'.__LINE__.'): UF ou Pais de origem ou destino em branco.');
            return false;
        }
        if($PaisEnderecoEntrega != $PaisEstabelecimento || $UFEnderecoEntrega != $UFEstabelecimento){ /* Se o pais for diferente usa a CFOP internacinal ou Se o estado for diferente usa o interestadual */
            $IdCFOP = $this->getPessoa()->getDadoPessoa('cfop_interestadual_padrao');
        }
        elseif($UFEnderecoEntrega == $UFEstabelecimento){ /* Se o estado for igual usa o estadual */
            $IdCFOP = $this->getPessoa()->getDadoPessoa('cfop_estadual_padrao');
        }
        return $IdCFOP;
    }

    public function RecarregaValorUnitarioItensDB(){
        foreach($this->getItens() as $IndiceItem => $Item){
            $Item->RecarregaValorUnitarioDB();
        }
    }

    public function getItemPorIdProduto($IdProduto){
        $Itens = $this->getItens();
        foreach($Itens as $Item){
            if($Item->getProduto()->getNumregProduto() == $IdProduto){
                return $Item;
            }
        }
        return false;
    }
    
    public function getItemPorIdSequencia($IdSequencia){
        $Itens = $this->getItens();
        foreach($Itens as $Item){
            if($Item->getSequenciaItem() == $IdSequencia){
                return $Item;
            }
        }
        return false;
    }

    public function AdicionaItemBD($NumregProduto,$ArDados,$ItemComercial=true){
        if(!$this->VendaParametro->getSnPermiteAdicionarItemRepetido()){
            if($this->getItemPorIdProduto($NumregProduto) !== false){
                $this->setMensagem('N?o permitido adicionar itens repetidos!');
                return false;
            }
        }
        if(!$this->VendaParametro->getSnUtilizaRefEstoque()){
            $ArDados['id_referencia'] = '';
        }

        $ArDadosItem = array();
        $ArDadosItem['id_produto']              = $NumregProduto;
        $ArDadosItem['qtde']                    = TrataFloatPost($ArDados['qtde']);
        $ArDadosItem['qtde_base_calculo']       = TrataFloatPost($ArDados['qtde']);
        $ArDadosItem['id_moeda']                = $ArDados['id_moeda'];
        $ArDadosItem['id_unid_medida']          = $ArDados['id_unid_medida'];
        $ArDadosItem['id_produto_embalagem']    = $ArDados['id_produto_embalagem'];
        $ArDadosItem['id_referencia']           = $ArDados['id_referencia'];
        $ArDadosItem['id_tab_preco']            = $ArDados['id_tab_preco'];
        $ArDadosItem['id_kit']                  = $ArDados['id_kit'];
        $ArDadosItem['qtde_kit']                = $ArDados['qtde_kit'];
        $ArDadosItem['vl_unitario_base_calculo']= TrataFloatPost($ArDados['vl_unitario']);

        $ArDadosItem['qtde_por_kit']            = $ArDados['qtde_por_kit'];
        $ArDadosItem['id_item_kit']             = $ArDados['id_item_kit'];
        $ArDadosItem['id_sequencia_kit']        = $ArDados['id_sequencia_kit'];
        $ArDadosItem['id_item_pre']             = $ArDados['id_item_pre'];

        $NovoItem = new VendaItem($this);

        $ArDadosItem['qtde_por_unid_medida'] = 1;
        $ModoUnidMedida = $this->getVendaParametro()->getModoUnidMedida();
        if($ModoUnidMedida == '3' && $this->isAtacado()){
            $ArDadosItem['qtde_por_unid_medida']    = $ArDados['qtde_por_unid_medida'];
        }

        if($ItemComercial === false){
            $NovoItem->setItemComercial($ItemComercial);//Setando o true ou false de item comercial
            $ArDadosItem['inc_cod_compl']               = $ArDados['cod_compl'];
            $ArDadosItem['inc_descricao']               = $ArDados['descricao'];
            $ArDadosItem['sn_item_comercial']           = 0;
            $ArDadosItem['vl_unitario_base_calculo']    = TrataFloatPost($ArDados['vl_unitario']);
        }
        if(!$this->getVendaParametro()->getSnUsaTabPrecoPorItem()){
            $ArDadosItem['id_tab_preco'] = $this->getIdTabPreco();
        }

        $NovoItem->setDadosItem($ArDadosItem);
        $NumregItem = $NovoItem->AdicionaItemBD();
        $NovoItem = NULL;
        if($NumregItem){
            if($this->Itens[$NumregItem] = new VendaItem($this,$NumregItem)){
                $this->CalculaTotaisVenda();
                $this->AtualizaTotaisVendaBD();
                $this->GravaLogBD(1,'Item Adicionado','','Numreg do Item:'.$NumregItem);
                VendaCallBackCustom::ExecutaVenda($this,'AdicionaItemBD','Final',array('IndiceItem' => $NumregItem));
                return $NumregItem;
            }
            return false;
        } else{
            return false;
        }
    }

    public function RemoveItem($NumregItem){
        if(empty($NumregItem)){
            return false;
        }
        //Verificando se o item pertence ? venda
        $QryVerificaItem = query("SELECT ".$this->getCampoChaveTabelaVendaItem()." FROM ".$this->getTabelaVendaItem()." WHERE numreg = ".$NumregItem);
        $ArVerificaItem = farray($QryVerificaItem);
        if($ArVerificaItem[$this->getCampoChaveTabelaVendaItem()] == $this->getNumregVenda()){
            $SqlItemDelete = "DELETE FROM ".$this->getTabelaVendaItem()." WHERE numreg = ".$NumregItem;
            $SqlItemDescontoDelete = "DELETE FROM ".$this->getTabelaVendaItemDesconto()." WHERE ".$this->getCampoChaveTabelaVendaItemDesconto()." = ".$NumregItem;
            query($SqlItemDescontoDelete);
            query($SqlItemDelete);
            unset($this->Itens[$NumregItem]);
            $this->GravaLogBD(12,'Item Exclu?do','','Numreg do Item:'.$NumregItem);
        }
        else{
            $this->RemoveItemErro = true;
            $this->MensagemRemoveItem .= 'Item n?o pertence ao '.$this->getTituloVenda();
        }
        return true;
    }

    public function getArTabPrecos(){
        if($this->ArTabPrecos !== false){ /* Caso j? tenho sido carregada anteriormente */
            return $this->ArTabPrecos;
        }
        $SqlTabPreco = "SELECT numreg,nome_tab_preco,id_moeda FROM is_tab_preco WHERE sn_ativa = 1";
        $QryTabPreco = query($SqlTabPreco);
        $NumRowsTabPreco = numrows($QryTabPreco);
        if($NumRowsTabPreco == 0){
            $this->ArTabPrecos = array();
        }
        else{
            while($ArTabPrecos = farray($QryTabPreco)){
                $this->ArTabPrecos[] = array($ArTabPrecos['numreg'],$ArTabPrecos['nome_tab_preco'],$ArTabPrecos['id_moeda']);
            }
        }
        return $this->ArTabPrecos;
    }

    public function VerificaSeExisteProduto($NumregProduto){
        foreach($this->getItens() as $IndiceItem => $Item){
            if($NumregProduto == $Item->getDadosVendaItem('id_produto')){
                return true;
            }
        }
        return false;
    }

    public function VerificaSeExisteItemPre($IdItemPre){
        foreach($this->getItens() as $IndiceItem => $Item){
            if($IdItemPre == $Item->getDadosVendaItem('id_item_pre')){
                return true;
            }
        }
        return false;
    }

    public function VerificaItensObrigatoriosBonificacao(){
        $SqlItensObrigatorios = "SELECT id_produto FROM is_campanha_bonificacao_produto_permitido WHERE id_campanha = ".$this->getDadosVenda('id_campanha_bonificacao')." AND sn_obrigatorio = 1";
        $QryItensObrigatorios = query($SqlItensObrigatorios);
        while($ArItensObrigatorios = farray($QryItensObrigatorios)){
            if(!$this->VerificaSeExisteProduto($ArItensObrigatorios['id_produto'])){
                return false;
            }
        }
        return true;
    }

    public function CalculaTotaisVenda(){
        $this->VlTotalVenda         = 0;
        $this->VlTotalVendaBruto    = 0;
        $this->VlTotalVendaLiquido  = 0;
        $this->VlTotalVendaIPI      = 0;
        $this->VlTotalVendaST       = 0;
        $this->PesoTotalVenda       = 0;
        foreach($this->getItens() as $IndiceItem => $Item){
            if($Item->getItemPerdido()){ /* Se o item foi marcado como perdido no or?amento n?o o considera nos c?lculos */
                continue;
            }
            $this->VlTotalVendaBruto    += $Item->getDadosVendaItem('vl_total_bruto');
            $this->VlTotalVendaLiquido  += $Item->getDadosVendaItem('vl_total_liquido');
            $this->VlTotalVendaIPI      += $Item->getDadosVendaItem('vl_total_ipi');
            $this->VlTotalVendaST       += $Item->getDadosVendaItem('vl_total_st');
            $this->PesoTotalVenda       += $Item->getDadosVendaItem('peso_total');
        }
        $this->VlTotalVenda = $this->VlTotalVendaLiquido + $this->VlTotalVendaIPI + $this->VlTotalVendaST;
        return true;
    }

    public function CalculaFrete(){
        if($this->getVendaParametro()->getSnUsaCalculoDeFreteCustomizado()){
            $VendaFreteCustom = new VendaFreteCustom($this);
            $this->VlTotalVendaFrete    = 0;
            if($VendaFreteCustom->CalculaValorTotalFrete() !== false){
                $this->setVlTotalFrete($VendaFreteCustom->getVlTotalFrete());
                return true;
            }
            $this->setMensagem('Erro ao calcular frete.');
            $this->setMensagemDebug('Erro ao calcular frete. ('.__FILE__.' '.__LINE__.')');
            return false;
        }
        /* C?lculo Padr?o do sistema */
        $this->VlTotalVendaFrete    = 0;
        $VendaFrete = new VendaFrete($this);
        $VendaFrete->CalculaValorTotalFrete();
        $this->setVlTotalFrete($VendaFrete->getVlTotalFrete());
        return true;
    }

    public function CalculaTotaisVendaItemBD(){
        foreach($this->getItens() as $IndiceItem => $Item){
           // $Item->CalculaTotais();
            $Item->AtualizaDadosItemBD();
        }
        $this->CalculaTotaisVenda();
        $this->AtualizaTotaisVendaBD();
    }

    public function AtualizaTotaisVendaBD(){
        $ArUpdateVenda = array();
        $ArUpdateVenda['numreg']             = $this->getNumregVenda();
        $ArUpdateVenda['vl_total_bruto']     = $this->getVlTotalVendaBruto();
        $ArUpdateVenda['vl_total_liquido']   = $this->getVlTotalVendaLiquido();
        $ArUpdateVenda['vl_total_ipi']       = $this->getVlTotalVendaIPI();
        $ArUpdateVenda['vl_total_st']        = $this->getVlTotalVendaST();
        $ArUpdateVenda['vl_total']           = $this->getVlTotalVenda();
        $ArUpdateVenda['vl_total_frete']     = $this->getVlTotalVendaFrete();
        $ArUpdateVenda['peso_total']         = $this->getPesoTotalVenda();

        $SqlUpdateVenda = AutoExecuteSql(TipoBancoDados,$this->getTabelaVenda(),$ArUpdateVenda,'UPDATE',array('numreg'));
        $Qry = query($SqlUpdateVenda);
        if(!$Qry){
            return false;
        }
        return true;
    }

    public function PreencheIdVendaClienteBD(){
        if(trim($this->getDadosVenda('id_venda_cliente')) != ''){
            return true;
        }
        $MaxId = VendaCallBackCustom::ExecutaVenda($this, 'PreencheIdVendaClienteBD', 'Antes');
        $IdVendaCliente = ($MaxId === true)?getParametrosVenda('prefixo_numero_pedido').uB::getProximoMaxId(1):$MaxId;
        
        $ArUpdateVenda = array();
        $ArUpdateVenda['numreg']                = $this->getNumregVenda();
        $ArUpdateVenda['id_venda_cliente']      = $IdVendaCliente;
        $ArUpdateVenda = $this->decodeDeParaCamposValor($ArUpdateVenda);
        $SqlUpdateVenda = AutoExecuteSql(TipoBancoDados,$this->getTabelaVenda(),$ArUpdateVenda,'UPDATE',array('numreg'));
        if(query($SqlUpdateVenda)){
            $this->setDadoVenda('id_venda_cliente',$IdVendaCliente);
            return true;
        }
        else{
            $this->setMensagem('Erro de SQL ao gerar numero de venda autom?tico.');
            return false;
        }
    }

    public function CompletaDigitacaoVendaBD(){
        $ArUpdateVenda = array();
        $ArUpdateVenda['numreg']                = $this->getNumregVenda();
        $ArUpdateVenda['sn_digitacao_completa'] = 1;
        $SqlUpdateVenda = AutoExecuteSql(TipoBancoDados,$this->getTabelaVenda(),$ArUpdateVenda,'UPDATE',array('numreg'));
        if(query($SqlUpdateVenda)){
            return true;
        }
        else{
            return false;
        }
    }

    public function ReabreDigitacaoVendaBD(){
        $ArUpdateVenda = array();
        $ArUpdateVenda['numreg']                = $this->getNumregVenda();
        $ArUpdateVenda['sn_digitacao_completa'] = 0;
        $SqlUpdateVenda = AutoExecuteSql(TipoBancoDados,$this->getTabelaVenda(),$ArUpdateVenda,'UPDATE',array('numreg'));
        if(query($SqlUpdateVenda)){
            return true;
        }
        else{
            $this->setMensagem('Erro de SQL ao reabrir para digita??o.');
            return false;
        }
    }

    public function EnviaParaAprovacao($Justificativa){
        $ArUpdateVenda = array();
        $ArUpdateVenda['numreg']                            = $this->getNumregVenda();
        $ArUpdateVenda['sn_avaliado_comercial']             = 0;
        $ArUpdateVenda['id_usuario_avaliador_comercial']    = NULL;
        $ArUpdateVenda['sn_em_aprovacao_comercial']         = 1;
        $ArUpdateVenda['sn_digitacao_completa']             = 1;
        $ArUpdateVenda['dt_avaliacao_comercial']            = NULL;
        $ArUpdateVenda['justificativa_em_aprov_com']        = $Justificativa;
        $SqlUpdateVenda = AutoExecuteSql(TipoBancoDados,$this->getTabelaVenda(),$ArUpdateVenda,'UPDATE',array('numreg'));
        if(query($SqlUpdateVenda)){
            $this->setMensagem($this->getTituloVenda(false,true).' foi enviado para aprova??o comercial.');
            return true;
        }
        else{
            $this->setMensagem('Erro de SQL ao reabrir para digita??o.');
            return false;
        }
    }

    public function VerificaSeHaItemReprovado(){
        /*
         * Verificando se h? itens reprovados
         */
        foreach($this->getItens() as $IndiceItem => $Item){
            if($Item->getDadosVendaItem('sn_reprovado_comercial') == 1){
                return true;
            }
        }
        return false;
    }

    public function RecalculaItensCFOPBD($ConsideraParametro = true){
        if($ConsideraParametro == true){
            if($this->getVendaParametro()->getPermiteAlterarCFOPItem()){// Se o parametro de venda n?o permite alteracao de CFOP
                return true;
            }
            elseif($this->getUsuario()->getPermissao('sn_permite_alterar_cfop_item') == 1){ // Se o usu?rio n?o permite alteracao de CFOP
                return true;
            }
        }

        foreach($this->getItens() as $IndiceItem => $Item){
            if($Item->getItemComercial()){
                if(!$Item->RecalculaCFOPBD()){
                    return false;
                }
            }
        }
        return true;
    }

    public function CalculaDataVenda($Data=NULL){
        $DataVenda = ($Data == NULL)?date("Y-m-d"):$Data;
        return $DataVenda;
    }

    public function AtualizaDataVendaBD(){
        $ArUpdateVenda['numreg']        = $this->getNumregVenda();
        $ArUpdateVenda['dt_venda']      = $this->CalculaDataVenda();

        $ArUpdateVenda = $this->decodeDeParaCamposValor($ArUpdateVenda);

        $SqlUpdateVenda = AutoExecuteSql(TipoBancoDados,$this->getTabelaVenda(),$ArUpdateVenda,'UPDATE',array('numreg'));
        if(query($SqlUpdateVenda)){
            $this->setDadoVenda('dt_venda', $ArUpdateVenda['dt_venda']);
            return true;
        }
        else{
            $this->setMensagem('Erro de SQL atualizar data da venda.');
            return false;
        }
    }

    public function CalculaDataEntrega($Data=NULL){
        if($this->getVendaParametro()->getSnPermiteAlterarDtEntrega()){ /* Se permite altera??o da data de entrega */
            return $this->getDadosVenda('dt_entrega');
        }
        $DataVenda = ($Data == NULL)?$this->getDadosVenda('dt_venda'):$Data;
        $QtdeDiasEntrega = getParametrosVenda('qtde_dias_entrega');
        $DataDeEntrega = date("Y-m-d",strtotime($DataVenda." + ".$QtdeDiasEntrega." days"));
        return $DataDeEntrega;
    }

    public function AtualizaDataEntregaBD(){
        $ArUpdateVenda['numreg']                = $this->getNumregVenda();
        $ArUpdateVenda['dt_entrega']            = $this->CalculaDataEntrega();
        if(!$this->getVendaParametro()->getSnUsaDataDesejadaEntrega()){ /* Se n?o usa data desejada de entrega, coloca no campo a mesma data de entrega */
            $ArUpdateVenda['dt_entrega_desejada']   = $ArUpdateVenda['dt_entrega'];
        }
        $SqlUpdateVenda = AutoExecuteSql(TipoBancoDados,$this->getTabelaVenda(),$ArUpdateVenda,'UPDATE',array('numreg'));
        if(query($SqlUpdateVenda)){
            $this->setDadoVenda('dt_entrega', $ArUpdateVenda['dt_entrega']);
            return true;
        }
        else{
            $this->setMensagem('Erro de SQL atualizar data de entrega.');
            return false;
        }
    }

    public function AprovaVendaBD($Justificativa){
        /*
         * Verificando se h? itens reprovados
         */
        if($this->VerificaSeHaItemReprovado()){
            $this->setMensagem('N?o ? permitido aprovar o '.$this->getTituloVenda(false,false).' se houver itens reprovados.');
            return false;
        }
        if($this->getTipoVenda() == 1){//Se for um or?amento
            $NumregNovoPedido = $this->TransformarEmPedidoBD();
            if(!is_numeric($NumregNovoPedido)){//Se n?o foi gerado o pedido
                $this->setMensagem('Erro ao criar pedido.');
                return false;
            }
            $NovoPedido = new Pedido(2,$NumregNovoPedido);
            if($NovoPedido->getSnCondPagtoProgramado()){
                $NovoPedido->AtualizaCondPagtoProgramadoBD();
            }
            $NovoPedido->setDadoVenda('dt_avaliacao_comercial', date("Y-m-d H:i:s"));
            $NovoPedido->setDadoVenda('justificativa_aprov_reprov_com', $Justificativa);
            $NovoPedido->setDadoVenda('sn_avaliado_comercial', 1);
            $NovoPedido->setDadoVenda('sn_aprovado_comercial', 1);
            if($this->VendaParametro->getSnGeraBonificVendaForaPol()){
                $NovoPedido->CalculaValorBonificacao();
            }
            $NovoPedido->AtualizaDadosVendaBD();
        }
        elseif($this->VendaParametro->getSnGeraBonificVendaForaPol()){
            $this->CalculaValorBonificacao();
        }

        $ArUpdateVenda = array();

        $ArUpdateVenda['numreg']                            = $this->getNumregVenda();
        $ArUpdateVenda['sn_avaliado_comercial']             = 1;
        $ArUpdateVenda['sn_aprovado_comercial']             = 1;
        $ArUpdateVenda['id_usuario_avaliador_comercial']    = $_SESSION['id_usuario'];
        $ArUpdateVenda['sn_em_aprovacao_comercial']         = 0;
        $ArUpdateVenda['sn_digitacao_completa']             = 1;
        $ArUpdateVenda['dt_avaliacao_comercial']            = date("Y-m-d H:i:s");
        $ArUpdateVenda['justificativa_aprov_reprov_com']    = $Justificativa;

        if($this->getTipoVenda() == 2){ /* Se for um pedido */
            /*
             * Se n?o foi preenchido o n?mero do pedido de cliente, gera um n?mero autom?tico
             */
            if(trim($this->getDadosVenda('id_venda_cliente')) == ''){
                $MaxId = uB::getProximoMaxId(1);
                $ArUpdateVenda['id_pedido_cliente'] = getParametrosVenda('prefixo_numero_pedido').$MaxId;
                $this->setDadoVenda('id_pedido_cliente', $ArUpdateVenda['id_pedido_cliente']);
            }
            if($this->getSnCondPagtoProgramado()){
                $this->AtualizaCondPagtoProgramadoBD();
            }
        }

        $SqlUpdateVenda = AutoExecuteSql(TipoBancoDados,$this->getTabelaVenda(),$ArUpdateVenda,'UPDATE',array('numreg'));
        if(query($SqlUpdateVenda)){
            $this->setDadoVenda('sn_avaliado_comercial', $ArUpdateVenda['sn_avaliado_comercial']);
            $this->setDadoVenda('sn_aprovado_comercial', $ArUpdateVenda['sn_aprovado_comercial']);
            $this->setDadoVenda('id_usuario_avaliador_comercial', $ArUpdateVenda['id_usuario_avaliador_comercial']);
            $this->setDadoVenda('sn_em_aprovacao_comercial', $ArUpdateVenda['sn_em_aprovacao_comercial']);
            $this->setDadoVenda('sn_digitacao_completa', $ArUpdateVenda['sn_digitacao_completa']);
            $this->setDadoVenda('dt_avaliacao_comercial', $ArUpdateVenda['dt_avaliacao_comercial']);
            $this->setDadoVenda('justificativa_aprov_reprov_com', $ArUpdateVenda['justificativa_aprov_reprov_com']);

            $this->setMensagem($this->getTituloVenda(false,true).' foi aprovado.');
            if($this->getTipoVenda() == 1){
                $this->setMensagem('Pedido N? '.$NumregNovoPedido.' foi gerado.');
            }
            if($this->getVendaParametro()->getSnEnviaEmailReduComis()){
                $IdRepresentantePrincipal = $this->getRepresentantePrincipal();
                if($IdRepresentantePrincipal != ''){
                    $RepresentantePrincipal = $this->getRepresentantePorId($IdRepresentantePrincipal);
                    if($RepresentantePrincipal->getComissaoAlteradaManualmente()){/* Se a comiss?o foi alterada */
                        $UsuarioRepresentante = new Usuario($IdRepresentantePrincipal);
                        if($UsuarioRepresentante){
                            $EmailsDestindo = explode(';',$this->getVendaParametro()->getEmailEnvioReduComis());
                            if(count($EmailsDestindo) > 0){
                                $EmailsDestindo = $this->ReplaceVariaveisEmail($EmailsDestindo);
                                if(count($EmailsDestindo) > 0){
                                    $EmailsDestindo = $this->ReplaceVariaveisEmail($EmailsDestindo);
                                    if(count($EmailsDestindo) > 0){
                                        $Email = new Email();
                                        $Email->_Assunto('Aprova??o de '.$this->getTituloVenda(false,true));
                                        $Email->_Corpo('Prezado(a),<br/>O seu '.$this->getTituloVenda().' N? '.$this->getDadosVenda('id_venda_cliente').' ('.$this->getNumregVenda().') foi aprovado, por?m teve a comiss?o alterada pelo comercial. Verificar no menu '.$this->getTituloVenda(false).'s de Venda.<hr/>** E-mail de notifica??o autom?tico, n?o ? necess?rio respond?-lo**');
                                        foreach($EmailsDestindo as $EmailDestino){
                                            $Email->_AdicionaDestinatario($EmailDestino);
                                        }
                                        if($Email->_EnviaEmail()){
                                            $this->setMensagem('<hr/>Foi enviado email de notifica??o para os seguintes e-mails:<br/><em>'.implode(';',$EmailsDestindo).'</em><br/>');
                                        }
                                        else{
                                            $this->setMensagem('<hr/>Erro ao enviar e-mail de notifica??o.');
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            if($this->VendaParametro->getSnExportaPedidoAoFinalizar()){
                if($this->getTipoVenda() == 1){
                    if($NovoPedido){
                        $NovoPedido->ExportaPedido();
                    }
                }
                else{
                    $this->ExportaPedido();
                }

            }
            $this->GravaLogBD(7,'Aprova??o da Venda',$Justificativa);
            return true;
        }
        else{
            $this->setMensagem('Erro de SQL ao reabrir para digita??o.');
            return false;
        }
    }

    public function ReprovaVendaBD($Justificativa){
        $ArUpdateVenda = array();
        $ArUpdateVenda['numreg']                            = $this->getNumregVenda();
        $ArUpdateVenda['sn_avaliado_comercial']             = 1;
        $ArUpdateVenda['sn_aprovado_comercial']             = 0;
        $ArUpdateVenda['id_usuario_avaliador_comercial']    = $_SESSION['id_usuario'];
        $ArUpdateVenda['sn_em_aprovacao_comercial']         = 0;
        $ArUpdateVenda['sn_digitacao_completa']             = 0;
        $ArUpdateVenda['dt_avaliacao_comercial']            = date("Y-m-d H:i:s");
        $ArUpdateVenda['justificativa_aprov_reprov_com']    = $Justificativa;

        $SqlUpdateVenda = AutoExecuteSql(TipoBancoDados,$this->getTabelaVenda(),$ArUpdateVenda,'UPDATE',array('numreg'));
        if(query($SqlUpdateVenda)){
            $this->setDadoVenda('sn_avaliado_comercial', $ArUpdateVenda['sn_avaliado_comercial']);
            $this->setDadoVenda('sn_aprovado_comercial', $ArUpdateVenda['sn_aprovado_comercial']);
            $this->setDadoVenda('id_usuario_avaliador_comercial', $ArUpdateVenda['id_usuario_avaliador_comercial']);
            $this->setDadoVenda('sn_em_aprovacao_comercial', $ArUpdateVenda['sn_em_aprovacao_comercial']);
            $this->setDadoVenda('sn_digitacao_completa', $ArUpdateVenda['sn_digitacao_completa']);
            $this->setDadoVenda('dt_avaliacao_comercial', $ArUpdateVenda['dt_avaliacao_comercial']);
            $this->setDadoVenda('justificativa_aprov_reprov_com', $ArUpdateVenda['justificativa_aprov_reprov_com']);

            $this->setMensagem($this->getTituloVenda(false,true).' foi reprovado.');
            $this->GravaLogBD(16,'Reprova??o da Venda',$Justificativa);

            if($this->getVendaParametro()->getSnEnviaEmailReprov()){
                $EmailsDestindo = explode(';',$this->getVendaParametro()->getEmailEnvioReprov());
                if(count($EmailsDestindo) > 0){
                    $EmailsDestindo = $this->ReplaceVariaveisEmail($EmailsDestindo);
                    if(count($EmailsDestindo) > 0){
                        $EmailsDestindo = $this->ReplaceVariaveisEmail($EmailsDestindo);
                        if(count($EmailsDestindo) > 0){
                            $Email = new Email();
                            $Email->_Assunto('Reprova??o de '.$this->getTituloVenda(false,true));
                            $Email->_Corpo('Prezado(a),<br/>O seu '.$this->getTituloVenda().' N? '.$this->getDadosVenda('id_venda_cliente').' ('.$this->getNumregVenda().') foi reprovado pelo comercial. Verificar no menu '.$this->getTituloVenda(false).'s Reprovados Comercial.<hr/>** E-mail de notifica??o autom?tico, n?o ? necess?rio respond?-lo**');
                            foreach($EmailsDestindo as $EmailDestino){
                                $Email->_AdicionaDestinatario($EmailDestino);
                            }
                            if($Email->_EnviaEmail()){
                                $this->setMensagem('<hr/>Foi enviado email de notifica??o para os seguintes e-mails:<br/><em>'.implode(';',$EmailsDestindo).'</em><br/>');
                            }
                            else{
                                $this->setMensagem('<hr/>Erro ao enviar e-mail de notifica??o.');
                            }
                        }
                    }
                }
            }
            return true;
        }
        else{
            $this->setMensagem('Erro de SQL ao reabrir para digita??o.');
            return false;
        }
    }

    public function ReplaceVariaveisEmail($ArEmails){
        $ArReplace = array(
            '{EMAIL_REPRESENTANTE_PRINCIPAL}'   => '',
            '{EMAIL_CLIENTE}'                   => '',
            '{EMAIL_CONTATO}'                   => '',
            '{EMAIL_USUARIO_CAD}'               => ''
        );

        $IdRepresentantePrincipal = $this->getRepresentantePrincipal();
        if($IdRepresentantePrincipal != '' && $RepresentantePrincipal = new Usuario($IdRepresentantePrincipal)){
            $ArReplace['{EMAIL_REPRESENTANTE_PRINCIPAL}'] = $RepresentantePrincipal->getEmail();
        }
        //TODO: Tratar o e-mail do cliente e demais op??es

        $ArReplace1 = array_keys($ArReplace);
        $ArReplace2 = array_values($ArReplace);

        foreach($ArEmails as $Key => $Email){
            $Replace = str_replace($ArReplace1, $ArReplace2, $Email);
            if($Replace != ''){
                $ArEmails[$Key] = $Replace;
            }
            else{
                unset($ArEmails[$Key]);
            }
        }
        return $ArEmails;
    }

    public function AtualizaTaxaFinanceira(){
        $IdCondPagto = $this->getDadosVenda('id_cond_pagto');
        $SqlCondPagto = "SELECT id_tab_financiamento, sn_indice_do_prazo_medio, sn_indice_do_prazo_medio, media_dias, id_taxa_financiamento FROM is_cond_pagto WHERE numreg = ".$IdCondPagto;
        $QryCondPagto = query($SqlCondPagto);
        $ArCondPagto = farray($QryCondPagto);

        $IdTabFinanciamento = $ArCondPagto['id_tab_financiamento'];
        $IdTaxaFinanciamento = $ArCondPagto['id_taxa_financiamento'];
        $MediaDias = $ArCondPagto['media_dias'];

        if($ArCondPagto['sn_indice_do_prazo_medio'] == 1){
            $SqlFinanciamentoTaxa = "SELECT vl_taxa,id_taxa_financiamento FROM is_tab_financiamento_taxa WHERE id_tab_financiamento = ".$IdTabFinanciamento." AND dia_taxa = '".$MediaDias."'";
        }
        else{
            $SqlFinanciamentoTaxa = "SELECT vl_taxa,id_taxa_financiamento FROM is_tab_financiamento_taxa WHERE id_tab_financiamento = ".$IdTabFinanciamento." AND id_taxa_financiamento = '".$IdTaxaFinanciamento."'";
        }

        $QryFinanciamentoTaxa = query($SqlFinanciamentoTaxa);
        $ArFinanciamentoTaxa = farray($QryFinanciamentoTaxa);

        $VlTaxa = ($ArFinanciamentoTaxa['vl_taxa'] == '')?1:$ArFinanciamentoTaxa['vl_taxa'];

        $ArSqlVenda = array();
        $ArSqlVenda['numreg']  = $this->getNumregVenda();
        $ArSqlVenda['id_tab_financiamento'] = $IdTabFinanciamento;
        $ArSqlVenda['id_taxa_financiamento'] = $ArFinanciamentoTaxa['id_taxa_financiamento'];
        $ArSqlVenda['vl_taxa_financiamento'] = $VlTaxa;
        $this->setDadoVenda('id_tab_financiamento',$IdTabFinanciamento);
        $this->setDadoVenda('id_taxa_financiamento',$ArFinanciamentoTaxa['id_taxa_financiamento']);
        $this->setDadoVenda('vl_taxa_financiamento',$VlTaxa);
        $Qry = query(AutoExecuteSql(TipoBancoDados,$this->getTabelaVenda(),$ArSqlVenda,'UPDATE',array('numreg')));
        if(!$Qry){
            $this->setMensagem('Erro ao atualizar a taxa financeira da venda. (Linha: '.__LINE__.')');
            return false;
        }
        return true;
    }

    public function ValidaPoliticaComercialDesc(){
        $Status = true;

        $ArDadosVenda               = $this->getDadosVenda();
        $ArDadosPessoa              = $this->getPessoa()->getDadoPessoa();

        /* Validando cabe?alho m?dia
        //TODO: Construir regra de m?dia considerando o valor de m?dia de desconto corretamente
        $this->PoliticaComercialDescVendaMedia = new PoliticaComercialDescVendaMedia();
        $this->PoliticaComercialDescVendaMedia->setArDadosVenda($ArDadosVenda);
        $this->PoliticaComercialDescVendaMedia->setArDadosPessoa($ArDadosPessoa);
        $this->PoliticaComercialDescVendaMedia->ValidaPolitica($this->getMediaDescontoVenda());
        $RetornoValidacao = $this->PoliticaComercialDescVendaMedia->getStatus();
        $Status = ($Status === true)?$RetornoValidacao:false;
        */
        /*
         * Validando campos individuais do cabe?alho
         */

        /* Validando campo desconto de tab. pre?o */
        $this->PoliticaComercialDescVendaCampoDescontoFixoTabPreco = new PoliticaComercialDescVendaCampoDescontoFixo(1);
        $this->PoliticaComercialDescVendaCampoDescontoFixoTabPreco->setArDadosVenda($ArDadosVenda);
        $this->PoliticaComercialDescVendaCampoDescontoFixoTabPreco->setArDadosPessoa($ArDadosPessoa);
        $this->PoliticaComercialDescVendaCampoDescontoFixoTabPreco->ValidaPolitica($this->getPctDescontoTabPreco());
        $RetornoValidacao = $this->PoliticaComercialDescVendaCampoDescontoFixoTabPreco->getStatus();
        $Status = ($Status === true)?$RetornoValidacao:false;
        if($RetornoValidacao === false && !$this->PoliticaComercialDescVendaCampoDescontoFixoTabPreco->getSnPermiteFinalizar()){
            $this->setMensagemDebug('Valida??o campo desc. tab. pre?o (cab.) fora da pol?tica e n?o permite finalizar.');
            $this->setPermiteFinalizar(false);
        }

        /* Validando campo desconto do cliente */
        $this->PoliticaComercialDescVendaCampoDescontoFixoPessoa = new PoliticaComercialDescVendaCampoDescontoFixo(2);
        $this->PoliticaComercialDescVendaCampoDescontoFixoPessoa->setArDadosVenda($ArDadosVenda);
        $this->PoliticaComercialDescVendaCampoDescontoFixoPessoa->setArDadosPessoa($ArDadosPessoa);
        $this->PoliticaComercialDescVendaCampoDescontoFixoPessoa->ValidaPolitica($this->getPctDescontoPessoa());
        $RetornoValidacao = $this->PoliticaComercialDescVendaCampoDescontoFixoPessoa->getStatus();
        $Status = ($Status === true)?$RetornoValidacao:false;
        if($RetornoValidacao === false && !$this->PoliticaComercialDescVendaCampoDescontoFixoPessoa->getSnPermiteFinalizar()){
            $this->setMensagemDebug('Valida??o campo desc. cliente (cab.) fora da pol?tica e n?o permite finalizar.');
            $this->setPermiteFinalizar(false);
        }

        /* Validando campo desconto informado */
        $this->PoliticaComercialDescVendaCampoDescontoFixoInformado = new PoliticaComercialDescVendaCampoDescontoFixo(3);
        $this->PoliticaComercialDescVendaCampoDescontoFixoInformado->setArDadosVenda($ArDadosVenda);
        $this->PoliticaComercialDescVendaCampoDescontoFixoInformado->setArDadosPessoa($ArDadosPessoa);
        $this->PoliticaComercialDescVendaCampoDescontoFixoInformado->ValidaPolitica($this->getPctDescontoInformado());
        $RetornoValidacao = $this->PoliticaComercialDescVendaCampoDescontoFixoInformado->getStatus();
        $Status = ($Status === true)?$RetornoValidacao:false;
        if($RetornoValidacao === false && !$this->PoliticaComercialDescVendaCampoDescontoFixoInformado->getSnPermiteFinalizar()){
            $this->setMensagemDebug('Valida??o campo desc. informado (cab.) fora da pol?tica e n?o permite finalizar.');
            $this->setPermiteFinalizar(false);
        }
        /*
         * Validando os itens
         */
        foreach($this->getItens() as $IndiceItem => $Item){
            $StatusItem = true;

            $RetornoValidacao = $Item->ValidaPoliticaComercialDescVendaItemMedia();
            $StatusItem = ($StatusItem === true)?$RetornoValidacao:false;

            /* Valida o desconto de tabela de pre?o caso o par?metro esteja ativado */
            if($this->getVendaParametro()->getSnUsaDescTabPrecoItem()){
                $RetornoValidacao = $Item->ValidaPoliticaComercialDescVendaItemCampoDescontoFixoTabPreco();
                $StatusItem = ($StatusItem === true)?$RetornoValidacao:false;
                if(!$StatusItem && !$this->VendaParametro->getSnPermFinDecTbItForaPol()){
                    $this->setPermiteFinalizar(false);
                }
            }
            /*
             * Valida??o dos campos de desconto individualemnte
             */
            foreach($this->getArrayCamposDescontos() as $IndiceCampoDesconto => $CampoDesconto){
                $RetornoValidacaoCampoDesconto = $Item->ValidaPoliticaComercialDescVendaItemCampoDesconto($IndiceCampoDesconto);
                $StatusItem = ($StatusItem === true)?$RetornoValidacaoCampoDesconto:false;
            }
            $Status = ($Status == true)?$StatusItem:$Status;
        }
        $this->setStatusPoliticaComercialDesc($Status);
    }

    public function getMediaDescontoVenda(){
        $VlTotalVendaBruto          = $this->getVlTotalVendaBruto();
        $VlTotalVendaLiquido        = $this->getVlTotalVendaLiquido();
        $PctMediaDescontoVenda      = ((($VlTotalVendaBruto - $VlTotalVendaLiquido) / $VlTotalVendaBruto) * 100);
        $PctMediaDescontoVenda      = $this->RoundFD($PctMediaDescontoVenda);
        return $PctMediaDescontoVenda;
    }

    public function getRepresentantePrincipal(){
        foreach($this->getRepresentantes() as $IndiceRepresentante => $Representante){
            if($Representante->getDadosVendaRepresentante('sn_representante_principal') == 1){
                return $Representante->getDadosVendaRepresentante('id_representante');
            }
        }
    }

    public function CalculaValorBonificacao(){
        $VlTotalBonificacao         = 0;
        $QtdeItens                  = $this->getQtdeItens(true);
        $VlTotVenda                 = $this->getVlTotalVendaLiquido();
        $IdRepresentantePrincipal   = $this->getRepresentantePrincipal();

        $VendaBonificacao = new VendaBonificacao($this);
        $VendaBonificacao->setQtdeItens($QtdeItens);
        $VendaBonificacao->setVlTotVenda($VlTotVenda);
        $IdBonificacao = $VendaBonificacao->VerificaCampanhaBonificacao();

        if($IdBonificacao !== false){

            $SnUsoImediato = $VendaBonificacao->getSnUsoImediato();
            $IdCampanha = $VendaBonificacao->getNumregCampanhaBonificacao();

            foreach($this->getItens() as $IndiceItem => $Item){
                if($Item->getItemComercial()){
                    $VendaBonificacao = new VendaBonificacao($this);
                    $VendaBonificacao->setQtdeItens($QtdeItens);
                    $VendaBonificacao->setVlTotVenda($VlTotVenda);
                    $VendaBonificacao->setIdFamiliaComercial($Item->getProduto()->getDadosProduto('id_familia_comercial'));
                    $VendaBonificacao->setIdProduto($Item->getDadosVendaItem('id_produto'));
                    $VendaBonificacao->setIdRepresentantePrincipal($IdRepresentantePrincipal);
                    $PctBonificacao = $VendaBonificacao->CalculaPctBonificacao();
                    $Item->AtualizaVlBonificacaoItem($PctBonificacao);
                    $VlTotalBonificacao += $Item->getDadosVendaItem('vl_total_bonificacao');
                }
            }
            /*
             * Atualizando os dados de bonifica??o
             */
            $this->AtualizaVlBonificacao($IdCampanha,$SnUsoImediato,$this->getRepresentantePrincipal(),$VlTotalBonificacao);
        }
        else{
            /*
             * Se n?o foi encontrada campanha de bonifica??o deleta todos os registro de bonifica??o para a venda
             */
            $this->setMensagemDebug('Nenhuma campanha encontrada, eliminado os registros de bonifica??o');
            $ArUpdate = array();
            $ArUpdate['numreg']                         = $this->getNumregVenda();
            $ArUpdate['vl_total_bonificacao']           = $VlTotalBonificacao;
            $ArUpdate['id_campanha_bonificacao']        = NULL;

            $SqlUpdate = AutoExecuteSql(TipoBancoDados,$this->getTabelaVenda(),$ArUpdate,'UPDATE',array('numreg'));
            query($SqlUpdate);
            $this->setDadoVenda('vl_total_bonificacao',$ArUpdate['vl_total_bonificacao']);
            query("DELETE FROM is_campanha_bonificacao_saldo WHERE id_tp_venda = ".$this->getTipoVenda()." AND id_venda = ".$this->getNumregVenda());
        }
        return true;
    }

    public function AtualizaVlBonificacao($IdCampanha,$SnUsoImediato,$IdUsuario,$VlTotalBonificacao){
        $ArUpdate = array();
        $ArUpdate['numreg']                         = $this->getNumregVenda();
        $ArUpdate['vl_total_bonificacao']           = $VlTotalBonificacao;
        $ArUpdate['id_campanha_bonificacao']        = $IdCampanha;
        $SqlUpdate = AutoExecuteSql(TipoBancoDados,$this->getTabelaVenda(),$ArUpdate,'UPDATE',array('numreg'));
        query($SqlUpdate);
        $this->setDadoVenda('vl_total_bonificacao',$ArUpdate['vl_total_bonificacao']);

        $ArUpdateBonificacao = array();
        $ArUpdateBonificacao['id_campanha']             = $IdCampanha;
        $ArUpdateBonificacao['sn_uso_imediato']         = $SnUsoImediato;
        $ArUpdateBonificacao['id_usuario']              = $IdUsuario;
        $ArUpdateBonificacao['id_tp_venda']             = $this->getTipoVenda();
        $ArUpdateBonificacao['id_venda']                = $this->getNumregVenda();
        $ArUpdateBonificacao['vl_total_bonificacao']    = $VlTotalBonificacao;
        $ArUpdateBonificacao['sn_aprovado']             = $ArUpdateBonificacao['sn_uso_imediato'];//Caso seja uso imediato = 1, entra como aprovado = 1 tamb?m

        $QryBonificacao = query("SELECT numreg FROM is_campanha_bonificacao_saldo WHERE id_tp_venda = ".$this->getTipoVenda()." AND id_venda = ".$this->getNumregVenda());
        $NumrowsBonificacao = numrows($QryBonificacao);
        if($NumrowsBonificacao == 0){
            $SqlUpdateBonificacao = AutoExecuteSql(TipoBancoDados,'is_campanha_bonificacao_saldo',$ArUpdateBonificacao,'INSERT');
            query($SqlUpdateBonificacao);
        }
        elseif($NumrowsBonificacao == 1){
            $ArBonificacao = farray($QryBonificacao);
            $ArUpdateBonificacao['numreg']               = $ArBonificacao['numreg'];

            $SqlUpdateBonificacao = AutoExecuteSql(TipoBancoDados,'is_campanha_bonificacao_saldo',$ArUpdateBonificacao,'UPDATE',array('numreg'));
            query($SqlUpdateBonificacao);
        }
    }

    public function CriaPedidoBonificacao(){
        $NovoPedido = new Pedido(2,NULL);//Criando novo pedido com dados vazios
        $this->NumregPedidoBonificacao  = $NovoPedido->getNumregVenda();
        $ArInsertPedido                 = $this->getDadosVenda();//Obtendo os dados da venda atual
        unset($ArInsertPedido['numreg']);//Limpando numreg do set de dados
        unset($ArInsertPedido['id_revisao']);//Limpando do set de dados
        unset($ArInsertPedido['id_atividade_pai']);//Limpando do set de dados
        unset($ArInsertPedido['id_revisao_orcamento']);//Limpando do set de dados
        unset($ArInsertPedido['id_oportunidade_pai']);//Limpando do set de dados
        unset($ArInsertPedido['dt_validade_orcamento']);//Limpando do set de dados
        unset($ArInsertPedido['id_venda_cliente']);//Limpando do set de dados

        $ArInsertPedido['id_tp_venda']                  = 2;//Fixado 2 - Bonifica??o
        $ArInsertPedido['id_grupo_tab_preco']           = 2;//Fixado 2 - Varejo
        $ArInsertPedido['sn_gerado_bonificacao_auto']   = 1;
        $ArInsertPedido['vl_total_bonificacao']         = 0;
        $ArInsertPedido['sn_em_aprovacao_comercial']    = 0;
        $ArInsertPedido['sn_avaliado_credito']          = 0;
        $ArInsertPedido['sn_avaliado_comercial']        = 0;
        $ArInsertPedido['sn_aprovado_credito']          = 0;
        $ArInsertPedido['sn_importado_erp']             = 0;
        $ArInsertPedido['sn_exportado_erp']             = 0;
        $ArInsertPedido['sn_digitacao_completa']        = 0;

        $ArInsertPedido['vl_total_bruto']               = 0;
        $ArInsertPedido['vl_total_liquido']             = 0;
        $ArInsertPedido['vl_total_ipi']                 = 0;
        $ArInsertPedido['vl_total_st']                  = 0;
        $ArInsertPedido['vl_total_icms']                = 0;
        $ArInsertPedido['vl_total_frete']               = 0;
        $ArInsertPedido['id_pedido_erp']                = '';

        if($this->getVendaParametro()->getIdCondPagtoBonificPadrao() != ''){
            $ArInsertPedido['id_cond_pagto'] = $this->getVendaParametro()->getIdCondPagtoBonificPadrao();
        }

        foreach($ArInsertPedido as $IdCampo => $Valor){ /* Para cada campo atualiza os dados no novo pedido */
            $NovoPedido->setDadoVenda($IdCampo,$Valor);
        }
        if($NovoPedido->AtualizaDadosVendaBD()){ /* Faz a atualiza??o dos dados no bd */
            /*
             * Gravando os representantes
             */
            foreach($this->getRepresentantes() as $IndiceRepresentante => $Representante){
                $IdRepresentante            = $Representante->getDadosVendaRepresentante('id_representante');
                $IdTpParticipacaoVenda      = $Representante->getDadosVendaRepresentante('id_tp_participacao');
                $PctComis                   = 0;//$Representante->getDadosVendaRepresentante('id_representante');
                $SnRepresentantePrincipal   = $Representante->getDadosVendaRepresentante('sn_representante_principal');
                $NovoPedido->AdicionaRepresentanteBD(1,$IdRepresentante,$PctComis,$SnRepresentantePrincipal);
            }

            $this->setDadoVenda('id_venda_bonificacao',$this->NumregPedidoBonificacao);
            $this->setDadoVenda('sn_gerou_pedido_bonificacao',1);
            $this->AtualizaDadosVendaBD();
            $this->setMensagem('Pedido de bonifica??o n?mero '.$NovoPedido->getNumregVenda().' foi criado.');
            return true;
        }
        else{
            return false;
        }
    }

    public function CancelaBonificacao(){
        $ArUpdate = array();
        $ArUpdate['numreg']                         = $this->getNumregVenda();
        $ArUpdate['vl_total_bonificacao']           = 0;
        $ArUpdate['id_campanha_bonificacao']        = NULL;
        $SqlUpdate = AutoExecuteSql(TipoBancoDados,$this->getTabelaVenda(),$ArUpdate,'UPDATE',array('numreg'));
        $this->GravaLogBD(23,'Cancelar bonifica??o');
        if(query($SqlUpdate)){
            $this->setMensagem('Bonifica??o cancelada com sucesso!');
            return true;
        }
        else{
            $this->setMensagem('Houve um problema ao cancelar a bonifica??o. Se o problema persistir entre em contato com o administrador do sistema.');
            return false;
        }
    }

    public function getArRevisoes(){
        if($this->ArRevisoes !== false){
            return $this->ArRevisoes;
        }
        $this->ArRevisoes = array();
        $SqlRevisoes = "SELECT DISTINCT numreg,id_revisao,dt_revisao FROM ".$this->getTabelaVenda()."_revisao WHERE ".$this->getCampoChaveTabelaVenda()." = '".$this->getNumregVenda()."' ORDER BY id_revisao DESC";
        $QryRevisoes = query($SqlRevisoes);
        while($ArRevisoes = farray($QryRevisoes)){
            $this->ArRevisoes[$ArRevisoes['numreg']] = array('id_revisao' => $ArRevisoes['id_revisao'], 'dt_revisao' => $ArRevisoes['dt_revisao']);
        }
        return $this->ArRevisoes;
    }

    public function AtualizaDadosVendaBD(){
        $ArUpdate           = $this->getDadosVenda();
        $ArUpdate           = $this->decodeDeParaCamposValor($ArUpdate);

        $SqlUpdate = AutoExecuteSql(TipoBancoDados,$this->getTabelaVenda(),$ArUpdate,'UPDATE',array('numreg'));
        $QryUpdate = query($SqlUpdate);
        if($QryUpdate){
            return true;
        }
        else{
            return false;
        }
    }

    public function CalculaComissaoItens(){
        VendaCallBackCustom::ExecutaVenda($this,'CalculaComissaoItens','Inicio');
        $this->setMensagemDebug('<h1># Debug C?lculo de Comiss?o #</h1>');

        $PctComissao = 0;

        $VlTotalComissao = 0;
        foreach($this->getRepresentantes() as $IndiceRepresentante => $Representante){
            $Representante->setVlComissao(0);
        }

        foreach($this->getItens() as $IndiceItem => $Item){
            $this->setMensagemDebug('<h3>Item:'.$Item->getDadosVendaItem('id_sequencia').'</h3>');
            $VlTotalComissaoItem = 0;
            foreach($this->getRepresentantes() as $IndiceRepresentante => $Representante){
                $ItemComissao = $Item->getItemComissao($IndiceRepresentante);

                if(!$Item->getItemComercial()){/* Se for um item n?o comercial a comiss?o ? 0 */
                    $VlComissao = 0;
                }
                elseif($ItemComissao->getComissaoAlteradaManualmente()){ /* Se a comiss?o foi alterada manualmente utiliza o pct preenchido */
                    $VlComissao = uM::uMath_pct_de_valor($ItemComissao->getDadosItemComissao('pct_comissao'),$Item->getDadosVendaItem('vl_total_liquido'));
                }
                else{
                    $VlComissao = $Item->CalculaComissaoItem($IndiceRepresentante);
                }
                $VlTotalComissaoItem += $VlComissao;
                $Representante->setVlComissao($Representante->getVlComissao() + $VlComissao);
            }
            $VlTotalComissao += $VlTotalComissaoItem;

            $PctComissao = uM::uMath_pct_de_diferenca_de_valor($Item->getDadosVendaItem('vl_total_liquido'),$VlTotalComissaoItem);
            $Item->setDadoItem('pct_comissao',$PctComissao);
            $Item->setDadoItem('vl_total_comissao',$VlTotalComissao);
        }
        $PctComissao = uM::uMath_pct_de_diferenca_de_valor($this->getVlTotalVendaLiquido(),$VlTotalComissao);

        $this->setDadoVenda('pct_comissao',$PctComissao);
        $this->setDadoVenda('vl_total_comissao',$VlTotalComissao);
        VendaCallBackCustom::ExecutaVenda($this,'CalculaComissaoItens','Final');
        return true;
    }

    public function getPermiteAprovar(){
        $VendaAprovacaoComercial = new VendaAprovacaoComercial($this);
        return $VendaAprovacaoComercial->PermiteAprovar();
    }

    public function CalculaTotaisComissaoBD(){
        foreach($this->getRepresentantes() as $IndiceRepresentante => $Representante){
            $Representante->setVlComissao(0);
        }

        $VlTotalComissaoVenda = 0;
        foreach($this->getItens() as $IndiceItem => $Item){
            $VlTotalComissaoItem = 0;
            foreach($this->getRepresentantes() as $IndiceRepresentante => $Representante){
                $ItemComissao = $Item->getItemComissao($IndiceRepresentante);

                $PctComissao = $ItemComissao->getDadosItemComissao('pct_comissao');
                $VlTotalLiquido = $Item->getDadosVendaItem('vl_total_liquido');
                $VlComissao = uM::uMath_pct_de_valor($PctComissao,$VlTotalLiquido);

                $Representante->setVlComissao($Representante->getVlComissao() + $VlComissao);

                if($ItemComissao->getComissaoAlteradaManualmente()){
                    $Representante->setDadoVendaRepresentante('sn_alterado_manual',1);
                }

                $VlTotalComissaoItem += $VlComissao;
                $VlTotalComissaoVenda += $VlComissao;
                $ItemComissao->setDadosItemComissao('pct_comissao',$PctComissao);
                $ItemComissao->setDadosItemComissao('vl_comissao',$VlComissao);
                $ItemComissao->AtualizaDadosBD();
            }
            $PctComissaoItem = uM::uMath_pct_de_diferenca_de_valor($Item->getDadosVendaItem('vl_total_liquido'),$VlTotalComissaoItem);
            $Item->setDadoItem('pct_comissao',$PctComissaoItem);
            $Item->setDadoItem('vl_total_comissao',$VlTotalComissaoItem);
            $Item->AtualizaDadosItemBD();
        }

        foreach($this->getRepresentantes() as $IndiceRepresentante => $Representante){
            #$PctComissaoRepresentante = uM::uMath_pct_de_diferenca_de_valor($this->getVlTotalVendaLiquido(),$Representante->getVlComissao());
            #$Representante->setPctComissao($PctComissaoRepresentante);
            $Representante->AtualizaDadosBD();
        }
        $PctComissaoVenda = uM::uMath_pct_de_diferenca_de_valor($this->getVlTotalVendaLiquido(),$VlTotalComissaoVenda);
        $this->setDadoVenda('pct_comissao',$PctComissaoVenda);
        $this->setDadoVenda('vl_total_comissao',$VlTotalComissaoVenda);
        $this->AtualizaDadosVendaBD();
    }

    /**
     * Gera uma revis?o do or?amento ou pedido
     */
    public function GeraRevisaoVenda(){

        /*
         * Definindo o sufixo para as tabelas de revisao
         */
        $SufixoTabelasRevisao = '_revisao';

        /*
         * Gravando dados do cabe?alho
         */
        $ArInsertVenda = array();
        $ArInsertVenda = $this->getDadosVenda(); //Pega a array de dados do or?ameto na mem?ria
        $ArInsertVenda['id_venda']   = $ArInsertVenda['numreg']; // Altera o numreg para o numreg do cabe?alho que foi inserido
        $QryMaxIdRevisao = query("SELECT MAX(id_revisao) AS id_revisao FROM ".$this->getTabelaVenda().$SufixoTabelasRevisao." WHERE ".$this->getCampoChaveTabelaVenda()." = '".$this->getNumregVenda()."'");
        $ArMaxIdRevisao = farray($QryMaxIdRevisao);
        $ArInsertVenda['id_revisao'] = ($ArMaxIdRevisao['id_revisao'] == '')?1:$ArMaxIdRevisao['id_revisao']+1;
        unset($ArInsertVenda['numreg']); //Remove a coluna numreg da array de dados
        $ArInsertVenda = $this->decodeDeParaCamposValor($ArInsertVenda); //Executa o depara de nome de colunas para or?amento e pedido
        $ArInsertVenda['sn_digitacao_completa'] = 1;
        $ArInsertVenda['dt_revisao']            = date("Y-m-d H:i:s");
        $SqlInsertVenda = AutoExecuteSql(TipoBancoDados, $this->getTabelaVenda().$SufixoTabelasRevisao, $ArInsertVenda, 'INSERT');
        
        
        
        $NumregVenda = iquery($SqlInsertVenda);
        if($NumregVenda === false){/* Se houve erro na query */
            $this->setErro(true);
            $this->setMensagem('Erro de SQL');
            $this->setMensagemDebug('Erro de SQL. '.$SqlInsertVenda);
            return false;
        }
        /*
         * Gravando os itens
         */
        $ArItens = $this->getItens();
        foreach($ArItens as $IndiceItem => $Item){
            $ArInsertVendaItem = array();
            $ArInsertVendaItem = $Item->getDadosVendaItem();
            $ArInsertVendaItem['id_venda'] = $NumregVenda;
            unset($ArInsertVendaItem['numreg']);

            $ArInsertVendaItem = $this->decodeDeParaCamposValor($ArInsertVendaItem);
            $SqlInsertVendaItem = AutoExecuteSql(TipoBancoDados, $this->getTabelaVendaItem().$SufixoTabelasRevisao, $ArInsertVendaItem, 'INSERT');
            $NumregVendaItem = iquery($SqlInsertVendaItem);
            if($NumregVendaItem === false){/* Se houve erro na query */
                $this->setErro(true);
                $this->setMensagem('Erro de SQL');
                $this->setMensagemDebug('Erro de SQL. '.$SqlInsertVendaItem);
                $this->DeleteVenda($NumregVenda,$SufixoTabelasRevisao);
                return false;
            }
            /*
             * Gravando os descontos
             */
            $ArDescontos = $Item->getDescontos();
            foreach($ArDescontos as $IndiceCampoDesconto => $ArDadosCampoDesconto){
                $ArSqlInsertVendaItemDesconto = array();
                $ArSqlInsertVendaItemDesconto[$this->getCampoChaveTabelaVendaItemDesconto()]   = $NumregVendaItem;
                $ArSqlInsertVendaItemDesconto['id_campo_desconto']                             = $IndiceCampoDesconto;
                $ArSqlInsertVendaItemDesconto['pct_desconto']                                  = $ArDadosCampoDesconto['pct_desconto'];

                $SqlInsertVendaItemDesconto = AutoExecuteSql(TipoBancoDados, $this->getTabelaVendaItemDesconto().$SufixoTabelasRevisao, $ArSqlInsertVendaItemDesconto, 'INSERT');
                $NumregVendaItemDesconto = iquery($SqlInsertVendaItemDesconto);
                if($NumregVendaItemDesconto === false){/* Se houve erro na query */
                    $this->setErro(true);
                    $this->setMensagem('Erro de SQL');
                    $this->setMensagemDebug('Erro de SQL. '.$SqlInsertVendaItemDesconto);
                    $this->DeleteVenda($NumregVenda,$SufixoTabelasRevisao);
                    return false;
                }
            }
            /*
             * Gravando as comiss?es por item
             */
            $ArRepresentantes = $this->getRepresentantes();
            foreach($ArRepresentantes as $IndiceRepresentante => $Representante){
                $ItemComissao = $Item->getItemComissao($IndiceRepresentante);
                if(is_object($ItemComissao)){
                    $ArInsertVendaItemComissao = array();
                    $ArInsertVendaItemComissao = $ItemComissao->getDadosItemComissao();
                    $ArInsertVendaItemComissao[$this->getCampoChaveTabelaVendaItemRepresentanteComissao()] = $NumregVendaItem;
                    unset($ArInsertVendaItemComissao['numreg']);

                    $SqlInsertVendaItemComissao = AutoExecuteSql(TipoBancoDados, $this->getTabelaVendaItemRepresentanteComissao(), $ArInsertVendaItemComissao, 'INSERT');
                    $NumregInsertVendaItemComissao = query($SqlInsertVendaItemComissao);
                    if($NumregInsertVendaItemComissao === false){
                        $this->setErro(true);
                        $this->setMensagem('Erro de SQL');
                        $this->setMensagemDebug('Erro de SQL. '.$SqlInsertVendaItemComissao);
                        $this->DeleteVenda($NumregVenda,$SufixoTabelasRevisao);
                        return false;
                    }
                }
            }
        }
        /*
         * Gravando Representantes
         */
        $ArRepresentantes = $this->getRepresentantes();
        foreach($ArRepresentantes as $IndiceRepresentante => $Representante){
            $ArInsertVendaRepresentante = array();
            $ArInsertVendaRepresentante = $Representante->getDadosVendaRepresentante();
            $ArInsertVendaRepresentante[$this->getCampoChaveTabelaVendaRepresentante()] = $NumregVenda;
            unset($ArInsertVendaRepresentante['numreg']);
            $ArInsertVendaRepresentante = $Representante->decodeDeParaCamposValor($ArInsertVendaRepresentante);

            $SqlInsertVendaRepresentante = AutoExecuteSql(TipoBancoDados, $this->getTabelaVendaRepresentante().$SufixoTabelasRevisao, $ArInsertVendaRepresentante, 'INSERT');
            $NumregVendaRepresentante = iquery($SqlInsertVendaRepresentante);
            if($NumregVendaRepresentante === false){/* Se houve erro na query */
                $this->setErro(true);
                $this->setMensagem('Erro de SQL');
                $this->setMensagemDebug('Erro de SQL. '.$SqlInsertVendaRepresentante);
                $this->DeleteVenda($NumregVenda,$SufixoTabelasRevisao);
                return false;
            }
        }
        /*
         * Se n?o ocorreu nenhum erro, atualiza a revis?o atual da venda
         */
        $QryUpdateVenda = query("UPDATE ".$this->getTabelaVenda()." SET id_revisao = '".($ArInsertVenda['id_revisao'])."' WHERE numreg  = '".$this->getNumregVenda()."'");
        if($QryUpdateVenda){
            $this->NumeroRevisaoGerada = $ArInsertVenda['id_revisao'];
            return VendaCallBackCustom::ExecutaVenda($this,'GeraRevisaoVenda','Final',array('id_venda' => $NumregVenda));
        }
        else{
            $this->DeleteVenda($NumregVenda,$SufixoTabelasRevisao);
            return false;
        }
    }

    public function ValidaPoliticaBloqueioFinalizacao(){
        $Itens = $this->getItens();
        foreach($Itens as $Item){
            $Item->ValidaPoliticaBloqueioFinalizacaoItem();
        }
    }

    /**
     * Gera um clone da venda
     */
    public function GeraClone(){

        /*
         * Gravando dados do cabe?alho
         */
        $ArInsertVenda = array();
        $ArInsertVenda = $this->getDadosVenda(); //Pega a array de dados do or?ameto na mem?ria

        /* Removendo Dados que n?o podem ser clonados */
        unset($ArInsertVenda['numreg'],
        $ArInsertVenda['id_venda_cliente'],
        $ArInsertVenda['id_venda_representante'],
        $ArInsertVenda['id_oportunidade_pai'],
        $ArInsertVenda['id_oportunidade_filha'],
        $ArInsertVenda['sn_email_enviado'],
        $ArInsertVenda['sn_impresso'],
        $ArInsertVenda['sn_passou_pelo_passo1'],
        $ArInsertVenda['sn_gerado_bonificacao_auto'],
        $ArInsertVenda['dt_entrega'],
        $ArInsertVenda['dt_entrega_desejada'],
        $ArInsertVenda['dt_cancelamento'],
        $ArInsertVenda['id_usuario_cancelamento'],
        $ArInsertVenda['id_motivo_cancelamento'],
        $ArInsertVenda['id_usuario_avaliador_credito'],
        $ArInsertVenda['dt_avaliacao_credito'],
        $ArInsertVenda['id_usuario_avaliador_comercial'],
        $ArInsertVenda['dt_avaliacao_comercial'],
        $ArInsertVenda['id_pedido_bonificacao'],
        $ArInsertVenda['justificativa_em_aprov_com'],
        $ArInsertVenda['justificativa_aprov_reprov_com'],
        $ArInsertVenda['id_atividade_pai'],
        $ArInsertVenda['dt_envio'],
        $ArInsertVenda['id_forma_envio'],
        $ArInsertVenda['sn_email_enviado'],
        $ArInsertVenda['dt_recebimento'],
        $ArInsertVenda['id_campanha_bonificacao'],
        $ArInsertVenda['tp_venda'],
        $ArInsertVenda['id_revisao'],
        $ArInsertVenda['dt_validade_orcamento'],
        $ArInsertVenda['id_oprotunidade_pai'],
        $ArInsertVenda['id_oprotunidade_filha'],
        $ArInsertVenda['sn_gerou_pedido'],
        $ArInsertVenda['id_revisao_orcamento'],
        $ArInsertVenda['dt_hr_importado_erp'],
        $ArInsertVenda['dt_hr_exportado_erp'],
        $ArInsertVenda['id_orcamento']);

        /* Definindo campos com valor padr?o */
        $ArInsertVenda['sn_passou_pelo_passo1']         = 0;
        $ArInsertVenda['sn_impresso']                   = 0;
        $ArInsertVenda['dt_venda']                      = date("Y-m-d");
        $ArInsertVenda['dt_cadastro']                   = date("Y-m-d");
        $ArInsertVenda['sn_digitacao_completa']         = 0;
        $ArInsertVenda['id_situacao_venda']             = 1;
        $ArInsertVenda['id_usuario_cad']                = $_SESSION['id_usuario'];
        $ArInsertVenda['vl_total']                      = 0;
        $ArInsertVenda['vl_total_bruto']                = 0;
        $ArInsertVenda['vl_total_liquido']              = 0;
        $ArInsertVenda['vl_total_ipi']                  = 0;
        $ArInsertVenda['vl_total_st']                   = 0;
        $ArInsertVenda['vl_total_icms']                 = 0;
        $ArInsertVenda['vl_total_frete']                = 0;
        $ArInsertVenda['peso_total']                    = 0;
        $ArInsertVenda['sn_avaliado_credito']           = 0;
        $ArInsertVenda['sn_aprovado_credito']           = 0;
        $ArInsertVenda['sn_avaliado_comercial']         = 0;
        $ArInsertVenda['sn_aprovado_comercial']         = 0;
        $ArInsertVenda['sn_em_aprovacao_comercial']     = 0;
        $ArInsertVenda['sn_importado_erp']              = 0;
        $ArInsertVenda['sn_exportado_erp']              = 0;
        $ArInsertVenda['vl_total_bonificacao']          = 0;
        $ArInsertVenda['pct_comissao']                  = 0;
        $ArInsertVenda['vl_total_comissao']             = 0;
        $ArInsertVenda['sn_gerado_bonificacao_auto']    = 0;
        $ArInsertVenda['sn_gerou_pedido_bonificacao']   = 0;

        $ArInsertVenda['sn_gerado_de_clone']            = 1;
        $ArInsertVenda['id_venda_origem_clone']         = $this->NumregVenda;

        $ArInsertVenda = $this->decodeDeParaCamposValor($ArInsertVenda); //Executa o depara de nome de colunas para or?amento e pedido
        $SqlInsertVenda = AutoExecuteSql(TipoBancoDados, $this->getTabelaVenda(), $ArInsertVenda, 'INSERT');
        $NumregVenda = iquery($SqlInsertVenda);
        if($NumregVenda === false){/* Se houve erro na query */
            $this->setErro(true);
            $this->setMensagem('Erro de SQL');
            $this->setMensagemDebug('Erro de SQL. '.$SqlInsertVenda);
            return false;
        }
        /*
         * Gravando os itens
         */
        $ArItens = $this->getItens();
        foreach($ArItens as $IndiceItem => $Item){
            $ArInsertVendaItem = array();
            $ArInsertVendaItem = $Item->getDadosVendaItem();
            $ArInsertVendaItem['id_venda'] = $NumregVenda;

            unset($ArInsertVendaItem['numreg'],
            $ArInsertVendaItem['descricao_perda'],
            $ArInsertVendaItem['dt_cancelamento'],
            $ArInsertVendaItem['dt_entrega'],
            $ArInsertVendaItem['dt_minima_faturamento'],
            $ArInsertVendaItem['dt_vl_unitario_sugestao_nf'],
            $ArInsertVendaItem['id_motivo_cancelamento'],
            $ArInsertVendaItem['id_motivo_perda'],
            $ArInsertVendaItem['id_orcamento'],
            $ArInsertVendaItem['id_pessoa_concorrente'],
            $ArInsertVendaItem['id_referencia'],
            $ArInsertVendaItem['id_usuario_cancelamento'],
            $ArInsertVendaItem['justificativa_reprov_com'],
            $ArInsertVendaItem['qtde_dias_perda'],
            $ArInsertVendaItem['sn_item_perdido'],
            $ArInsertVendaItem['vl_perda'],
            $ArInsertVendaItem['vl_total_bonificacao']
            );

            $ArInsertVendaItem['dt_cadastro']                       = date("Y-m-d");
            $ArInsertVendaItem['id_usuario_cad']                    = $_SESSION['id_usuario'];
            $ArInsertVendaItem['pct_aliquota_iva']                  = 0;
            $ArInsertVendaItem['pct_comissao']                      = 0;
            $ArInsertVendaItem['pct_desconto_base']                 = 0;
            $ArInsertVendaItem['pct_desconto_total']                = 0;
            $ArInsertVendaItem['peso_bruto']                        = 0;
            $ArInsertVendaItem['peso_liquido']                      = 0;
            $ArInsertVendaItem['peso_total']                        = 0;
            $ArInsertVendaItem['qtde_faturada']                     = 0;
            $ArInsertVendaItem['sn_reprovado_comercial']            = 0;
            $ArInsertVendaItem['sn_vl_unitario_sugestao_nf']        = 0;
            $ArInsertVendaItem['vl_cotacao']                        = 1;
            $ArInsertVendaItem['vl_total_bruto']                    = 0;
            $ArInsertVendaItem['vl_total_bruto_base_calculo']       = 0;
            $ArInsertVendaItem['vl_total_comissao']                 = 0;
            $ArInsertVendaItem['vl_total_frete']                    = 0;
            $ArInsertVendaItem['vl_total_ipi']                      = 0;
            $ArInsertVendaItem['vl_total_liquido']                  = 0;
            $ArInsertVendaItem['vl_total_liquido_base_calculo']     = 0;
            $ArInsertVendaItem['vl_total_st']                       = 0;
            $ArInsertVendaItem['vl_unitario_convertido']            = 0;
            $ArInsertVendaItem['vl_unitario_icms']                  = 0;
            $ArInsertVendaItem['vl_unitario_ipi']                   = 0;
            $ArInsertVendaItem['vl_unitario_st']                    = 0;

            if($this->getVendaParametro()->getSnUsaTabPrecoPorItem() && $Item->isCotacaoFixa()){
                $IdMoedaTabPreco    = $Item->getDadosTabPreco('id_moeda');
                $VlCotacao          = getCotacaoBD($IdMoedaTabPreco,1);
                if(!$VlCotacao){
                    $this->setMensagem('Item '.$Item->getSequenciaItem().' n?o possui cota??o para a data atual.');
                    $this->setMensagemDebug('Item '.$Item->getSequenciaItem().' n?o possui cota??o para a data atual. Linha:('.__LINE__.')');
                }
                else{
                    $ArInsertVendaItem['vl_cotacao'] = $VlCotacao;
                }
            }

            $ArInsertVendaItem = $this->decodeDeParaCamposValor($ArInsertVendaItem);
            $SqlInsertVendaItem = AutoExecuteSql(TipoBancoDados, $this->getTabelaVendaItem(), $ArInsertVendaItem, 'INSERT');
            $NumregVendaItem = iquery($SqlInsertVendaItem);
            if($NumregVendaItem === false){/* Se houve erro na query */
                $this->setErro(true);
                $this->setMensagem('Erro de SQL');
                $this->setMensagemDebug('Erro de SQL. '.$SqlInsertVendaItem);
                $this->DeleteVenda($NumregVenda);
                return false;
            }
            /*
             * Gravando os descontos
             */
            $ArDescontos = $Item->getDescontos();
            foreach($ArDescontos as $IndiceCampoDesconto => $ArDadosCampoDesconto){
                $ArSqlInsertVendaItemDesconto = array();
                $ArSqlInsertVendaItemDesconto[$this->getCampoChaveTabelaVendaItemDesconto()]   = $NumregVendaItem;
                $ArSqlInsertVendaItemDesconto['id_campo_desconto']                             = $IndiceCampoDesconto;
                $ArSqlInsertVendaItemDesconto['pct_desconto']                                  = $ArDadosCampoDesconto['pct_desconto'];

                $SqlInsertVendaItemDesconto = AutoExecuteSql(TipoBancoDados, $this->getTabelaVendaItemDesconto(), $ArSqlInsertVendaItemDesconto, 'INSERT');
                $NumregVendaItemDesconto = iquery($SqlInsertVendaItemDesconto);
                if($NumregVendaItemDesconto === false){/* Se houve erro na query */
                    $this->setErro(true);
                    $this->setMensagem('Erro de SQL');
                    $this->setMensagemDebug('Erro de SQL. '.$SqlInsertVendaItemDesconto);
                    $this->DeleteVenda($NumregVenda);
                    return false;
                }
            }
        }
        /*
         * Gravando Representantes
         */
        $ArRepresentantes = $this->getRepresentantes();
        foreach($ArRepresentantes as $IndiceRepresentante => $Representante){
            $ArInsertVendaRepresentante = array();
            $ArInsertVendaRepresentante = $Representante->getDadosVendaRepresentante();
            $ArInsertVendaRepresentante[$this->getCampoChaveTabelaVendaRepresentante()] = $NumregVenda;
            unset($ArInsertVendaRepresentante['numreg']);
            $ArInsertVendaRepresentante = $Representante->decodeDeParaCamposValor($ArInsertVendaRepresentante);

            $SqlInsertVendaRepresentante = AutoExecuteSql(TipoBancoDados, $this->getTabelaVendaRepresentante(), $ArInsertVendaRepresentante, 'INSERT');
            $NumregVendaRepresentante = iquery($SqlInsertVendaRepresentante);
            if($NumregVendaRepresentante === false){/* Se houve erro na query */
                $this->setErro(true);
                $this->setMensagem('Erro de SQL');
                $this->setMensagemDebug('Erro de SQL. '.$SqlInsertVendaRepresentante);
                $this->DeleteVenda($NumregVenda);
                return false;
            }
        }
        if($this->isOrcamento()){
            $NovaVenda = new Orcamento($this->getTipoVenda(),$NumregVenda);
        }
        elseif($this->isPedido()){
            $NovaVenda = new Pedido($this->getTipoVenda(),$NumregVenda);
        }
        if(!$NovaVenda->RecalculaVenda()){
            $this->setMensagem('N?o foi poss?vel atualizar os dados do '.$this->TituloVenda.'.');
            $this->setMensagemDebug($NovaVenda->getMensagemDebug());
        }
        /*
         * Se n?o ocorreu nenhum erro preenche a mensagem de sucesso
         */
        $this->NumregCloneGerado = $NumregVenda;
        $this->setMensagem('C?pia gerada com sucesso! N?: '.$this->NumregCloneGerado);
        $this->GravaLogBD(9,'C?pia Gerada','','Numreg C?pia:'.$this->NumregCloneGerado);
        VendaCallBackCustom::ExecutaVenda($this, 'GeraClone', 'Final', array('id_venda' => $NumregVenda));
        return true;
    }

    public function AtualizaComissaoItensBD(){
        foreach($this->getItens() as $IndiceItem => $Item){
            $Item->AtualizaComissaoItemBD();
        }
        $ArUpdateVenda = array();
        $ArUpdateVenda['numreg']                = $this->getNumregVenda();
        $ArUpdateVenda['pct_comissao']          = $this->getDadosVenda('pct_comissao');
        $ArUpdateVenda['vl_total_comissao']     = $this->getDadosVenda('vl_total_comissao');
        $SqlUpdateVenda = AutoExecuteSql(TipoBancoDados,$this->getTabelaVenda(),$ArUpdateVenda,'UPDATE',array('numreg'));

        $QryUpdateVenda = query($SqlUpdateVenda);
        foreach($this->getRepresentantes() as $IndiceRepresentante => $Representante){
            $Representante->AtualizaDadosBD();
        }
        return true;
    }

    /**
     * Recalcula todos os dados da venda 1=Cabe?alho,2=CFOP do Itens,3=Valores,4=Valores de Comiss?es,5=Valor Bonifica??o
     * @param int $Opcao 1,2,3,4,5
     */
    public function RecalculaVenda($Opcao = NULL){
        if($Opcao == NULL){
            $ArOpcao = array(1,2,3,4,5);
        }
        else{
            $ArOpcao = str_split($Opcao);
        }

        /*
         * Recalculando a CFOP dos itens
         */
        if(is_int(array_search(2,$ArOpcao))){
            if(!$this->RecalculaItensCFOPBD()){
                $this->setMensagemDebug('Erro ao recalcular CFOP dos itens.');
                return false;
            }
        }

        /*
         * Recalculando os dados do cabe?alho
         */
        if(is_int(array_search(1,$ArOpcao))){
            if(!$this->AtualizaDataVendaBD()){
                $this->setMensagemDebug('Erro ao atualizar a data da venda.');
                return false;
            }
            if(!$this->AtualizaDataEntregaBD()){
                $this->setMensagemDebug('Erro ao atualizar a data de entrega.');
                return false;
            }
            if(!$this->GravaCfopVendaBD()){
                $this->setMensagemDebug('Erro ao atualizar na base de dados a cfop do cabe?alho da venda.');
                return false;
            }
        }

        /*
         * Recalculando os valores (Cota??o, ST, ICMS, etc.)
         */
        if(is_int(array_search(3,$ArOpcao))){
            $ArItens = $this->getItens();
            foreach($ArItens as $IndiceItem => $Item){
                if($Item->getItemComercial()){ /* Se ? um item comercial */
                    if($this->getVendaParametro()->getSnUsaTabPrecoPorItem()){ /* Caso utlize tabela de pre?o por item */
                        $Item->AtualizaCotacao();
                    }

                    /* Atualizando IPI */
                    //TODO: Atualizar IPI

                    if(!$this->isPrecoInformado()){ /* Se n?o for pre?o informado */
                        /* Determinando qual tabela de pre?o ser? usada */
                        if($this->getVendaParametro()->getSnUsaTabPrecoPorItem()){ /* Se usa tabela de pre?o por item */
                            $IdTabPreco = $this->DadosVendaItem['id_tab_preco'];
                        }
                        else{
                            $IdTabPreco = $this->getIdTabPreco();
                        }
                        $VlUnitarioOriginal     = $Item->getProduto()->getVlUnitarioTabelaBD($this->getGrupoTabPreco(),$IdTabPreco);
                        $Item->setDadoItem('vl_unitario_tabela_original',$VlUnitarioOriginal);
                        $Item->setDadoItem('vl_unitario_com_desconto_base',$VlUnitarioOriginal);
                        $Item->setDadoItem('vl_unitario_base_calculo',$VlUnitarioOriginal);
                    }
                }
                if(!$Item->AtualizaItemBD()){
                    $this->setMensagemDebug('Erro ao atualizar o item no banco de dados.');
                    return false;
                }
            }
            if(!$this->CalculaTotaisVenda() || !$this->AtualizaTotaisVendaBD()){
                return false;
            }
        }

        /*
         * Recalculando Comiss?es
         */
        if(is_int(array_search(4,$ArOpcao))){
            if(!$this->getDigitacaoCompleta()){
                if(!$this->CalculaComissaoItens() || !$this->AtualizaComissaoItensBD()){
                    return false;
                }
            }
        }

        /*
         * Recalculando Bonifica??es
         */
        if(is_int(array_search(5,$ArOpcao))){
            if($this->isPedido() && $this->getDigitacaoCompleta() && $this->isTipoVenda() && !$this->getSnGerouPedidoBonificacao()){
                $this->CalculaValorBonificacao();
            }
        }
        if($this->isOrcamento()){
            $this->CalculaDataValidade(date("Y-m-d"));
            $this->GravaDataValidadeBD();
        }
        return true;
    }

    /**
     * Restaura uma revis?o da venda
     * @param <type> $NumregRevisao
     */
    public function RestauraRevisao($NumregRevisao){
        if($this->TipoVenda == 1){
            $VendaRevisao = new Orcamento($this->TipoVenda,$NumregRevisao,true,true,true);
        }
        elseif($this->TipoVenda == 2){
            $VendaRevisao = new Pedido($this->TipoVenda,$NumregRevisao,true,true,true);
        }
        /*
         * Verificando se a revis?o foi originada desta venda
         */
        if($this->getNumregVenda() != $VendaRevisao->getDadosVenda('id_venda')){
            return false;
        }
        #start_transaction();
        /*
         * Preenchendo os dados do cabe?alho
         */
        foreach($VendaRevisao->getDadosVenda() as $IdCampo => $Valor){
            if($IdCampo == 'numreg' || $IdCampo == 'id_venda' || $IdCampo = 'dt_revisao'){
                continue;
            }
            $this->setDadoVenda($IdCampo, $Valor);
        }
        if(!$this->AtualizaDadosVendaBD()){ /* Se n?o gravar o cabecalho */
            #rollback_transaction();
            $this->setErro(true);
            $this->setMensagem('Erro de SQL/a');
            return false;
        }
        /*
         * Gravando os itens
         */
        $ArItens = $VendaRevisao->getItens();
        $ArItensProcessados = array();
        foreach($ArItens as $IndiceItem => $ItemRevisao){
            $ArDadosItem = $ItemRevisao->getDadosVendaItem();
            $ArDadosItem['id_venda'] = $this->getNumregVenda();
            /*
             * Verificando se o item j? existe
             */
            $SqlVendaItem = "SELECT numreg FROM ".$this->getTabelaVendaItem()." WHERE ".$this->getCampoChaveTabelaVendaItem()." = '".$ArDadosItem['id_venda']."' AND id_sequencia = '".$ArDadosItem['id_sequencia']."'";
            $QryVendaItem = query($SqlVendaItem);
            $NumRowsVendaItem = numrows($QryVendaItem);
            $ArDadosItem = $ItemRevisao->decodeDeParaCamposValor($ArDadosItem);
            if($NumRowsVendaItem <= 0){
                $SqlVendaItem               = AutoExecuteSql(TipoBancoDados, $this->getTabelaVendaItem(), $ArDadosItem, 'INSERT');
                $QryVendaItem               = iquery($SqlItem);
                $NumregVendaItem            = $QryVendaItem;
            }
            else{
                $ArVendaItem                = farray($QryVendaItem);
                $ArDadosItem['numreg']      = $ArVendaItem['numreg'];
                $NumregVendaItem            = $ArVendaItem['numreg'];
                $SqlVendaItem               = AutoExecuteSql(TipoBancoDados, $this->getTabelaVendaItem(), $ArDadosItem, 'UPDATE',array('numreg'));
                $QryVendaItem               = query($SqlVendaItem);
            }
            if(!$QryVendaItem){/* Se houve erro na query */
                #rollback_transaction();
                $this->setErro(true);
                $this->setMensagem('Erro de SQL/b');
                return false;
            }
            $ArItensProcessados[] = $NumregVendaItem; //Adicionando os numregs processados para deletar os demais no final do loop
            /*
             * Gravando os descontos
             */
            $ArDescontos = $ItemRevisao->getDescontos();
            $ArDescontosProcessados = array();
            foreach($ArDescontos as $IndiceCampoDesconto => $ArDadosCampoDesconto){
                $ArSqlInsertVendaItemDesconto = array();
                $ArSqlInsertVendaItemDesconto[$this->getCampoChaveTabelaVendaItemDesconto()]   = $NumregVendaItem;
                $ArSqlInsertVendaItemDesconto['id_campo_desconto']                             = $IndiceCampoDesconto;
                $ArSqlInsertVendaItemDesconto['pct_desconto']                                  = $ArDadosCampoDesconto['pct_desconto'];

                $SqlVendaItemCampoDesconto      = "SELECT numreg FROM ".$this->getTabelaVendaItemDesconto()." WHERE ".$this->getCampoChaveTabelaVendaItemDesconto()." = '".$NumregVendaItem."' AND id_campo_desconto = '".$IndiceCampoDesconto."'";
                $QryVendaItemCampoDesconto      = query($SqlVendaItemCampoDesconto);
                $NumRowsVendaItemCampoDesconto  = numrows($QryVendaItemCampoDesconto);

                if($NumRowsVendaItemCampoDesconto <= 0){
                    $SqlVendaItemDesconto = AutoExecuteSql(TipoBancoDados, $this->getTabelaVendaItemDesconto(), $ArSqlInsertVendaItemDesconto, 'INSERT');
                    $QryVendaItemDesconto = iquery($SqlVendaItemDesconto);
                    $NumregVendaItemDesconto = $QryVendaItemDesconto;
                }
                else{
                    $ArVendaItemCampoDesconto = farray($QryVendaItemCampoDesconto);
                    $ArSqlInsertVendaItemDesconto['numreg']                                     = $ArVendaItemCampoDesconto['numreg'];
                    $NumregVendaItemDesconto                                                    = $ArVendaItemCampoDesconto['numreg'];
                    $SqlVendaItemDesconto = AutoExecuteSql(TipoBancoDados, $this->getTabelaVendaItemDesconto(), $ArSqlInsertVendaItemDesconto, 'UPDATE',array('numreg'));
                    $QryVendaItemDesconto = query($SqlVendaItemDesconto);

                }
                if(!$QryVendaItemDesconto){/* Se houve erro na query */
                        #rollback_transaction();
                        $this->setErro(true);
                        $this->setMensagem('Erro de SQL/c');
                        return false;
                }
                $ArDescontosProcessados[] = $NumregVendaItemDesconto; //Adicionando os numregs processados para deletar os demais no final do loop
            }
        }
        /*
         * Caso n?o tenho ocorrido erros da um commit
         */
        #commit_transaction();
        return true;
    }

    /**
     * Atualiza a cota??o de todo os itens no banco de dados
     * @return bool
     */
    public function AtualizaCotacaoBD(){
        $Itens = $this->getItens();
        foreach($Itens as $IndiceItem => $Item){
            if(!$Item->AtualizaCotacaoBD()){
                return false;
            }
        }
        $this->CalculaTotaisVendaItemBD();
        return true;
    }

    public function CarregaItenPre(){
        if($this->VisualizarRevisao){
            return true;
        }
        $this->ItensPre = array();
        $QryItensPre = query("SELECT * FROM ".$this->getTabelaVendaItemPre()." WHERE ".$this->getCampoChaveTabelaVendaItemPre()." = ".$this->getNumregVenda());
        while($ArItensPre = farray($QryItensPre)){
            $ArDadosItem = array();
            foreach($ArItensPre as $Coluna => $Valor){
                if(!is_int($Coluna)){
                    $ArDadosItem[$Coluna] = $Valor;
                }
            }
            $this->ItensPre[] = $ArDadosItem;
        }
    }

    public function CarregaSolicitacaoComercial(){
        $this->ItensSolicitacaoComercial = array();
        $QryItensSolicitacaoComercial = query("SELECT id_produto,produto_nao_cadastrado,qtde FROM is_atividade_solicitacao WHERE acao_id_orcamento_gerado = ".$this->getNumregVenda()." AND acao_id_usuario_resp = '".$this->getDadosVenda('id_usuario_cad')."' AND acao_id_tab_preco = '".$this->getIdTabPreco()."'");
        while($ArItensSolicitacaoComercial = farray($QryItensSolicitacaoComercial)){
            $ArDadosItem = array(
                'id_produto'        => $ArItensSolicitacaoComercial['id_produto'],
                'inc_descricao'     => $ArItensSolicitacaoComercial['produto_nao_cadastrado'],
                'qtde'              => $ArItensSolicitacaoComercial['qtde']
            );
            $this->ItensSolicitacaoComercial[] = $ArDadosItem;
        }
    }

    public function CarregaOportunidadePai(){
        $this->ItensOportunidadePai = array();
        if($this->getDadosVenda('id_oportunidade_pai') != ''){
            $QryItensOportunidadePai = query("SELECT id_produto,outro,qtde,valor FROM is_opor_produto WHERE id_oportunidade = ".$this->getDadosVenda('id_oportunidade_pai'));
            while($ArItensOportunidadePai = farray($QryItensOportunidadePai)){
                $ArDadosItem = array(
                    'id_produto'        => $ArItensOportunidadePai['id_produto'],
                    'inc_descricao'     => $ArItensOportunidadePai['outro'],
                    'qtde'              => $ArItensOportunidadePai['qtde'],
                    'valor'             => $ArItensOportunidadePai['valor']
                );
                $this->ItensOportunidadePai[] = $ArDadosItem;
            }
        }
    }

    public function getSnCondPagtoProgramado(){
        $CondicaoPagamentoProgramado = new VendaCondicaoPagamentoProgramado($this);
        return $CondicaoPagamentoProgramado->getSnCondPagtoProgramado();
    }

    public function AtualizaCondPagtoProgramadoBD(){
        $CondicaoPagamentoProgramado = new VendaCondicaoPagamentoProgramado($this);
        if($CondicaoPagamentoProgramado->getSnCondPagtoProgramado()){
            return $CondicaoPagamentoProgramado->GravaAtualizaDatasBD();
        }
        else{
            $this->setMensagemDebug('Condi??o de pagamento n?o ? condi??o com programa??o autom?tica.');
            return true;
        }
    }

    public function AlteraStatusAtendimentoLaboratorio(){
        if($this->getDadosVenda('id_origem_sistema') == '1'){
            $SqlUpdateAtendimento = "   UPDATE
                                        is_atividade
                                    SET
                                        id_status_reparo = 9
                                    WHERE
                                        id_tp_atividade = 55
                                    AND
                                        id_status_reparo = 8
                                    AND
                                        id_orcamento = ".$this->getNumregVenda();
            $QryUpdateAtendimento = query($SqlUpdateAtendimento);
            $this->GravaLogBD(20, 'Atualiza??o de atendimentos de laborat?rio', '', 'SQL: '.$SqlUpdateAtendimento);
        }
        return true;
    }

    protected function DeleteVenda($NumregVenda,$SufixoTabelasRevisao=''){
        $SqlDeleteVenda = "DELETE FROM ".$this->getTabelaVenda().$SufixoTabelasRevisao." WHERE numreg = ".$NumregVenda;
        $SqlDeleteVendaItem = "DELETE FROM ".$this->getTabelaVendaItem().$SufixoTabelasRevisao." WHERE ".$this->getCampoChaveTabelaVendaItem()." = ".$NumregVenda;
        $SqlDeleteVendaItemDesconto = "DELETE FROM ".$this->getTabelaVendaItemDesconto().$SufixoTabelasRevisao." WHERE numreg IN(SELECT ".$this->getCampoChaveTabelaVendaItemDesconto()." FROM ".$this->getTabelaVendaItem().$SufixoTabelasRevisao." WHERE ".$this->getCampoChaveTabelaVendaItem()." = ".$NumregVenda.")";
        $SqlDeleteVendaRepresentante = "DELETE FROM ".$this->getTabelaVendaRepresentante().$SufixoTabelasRevisao." WHERE ".$this->getCampoChaveTabelaVendaRepresentante()." = ".$NumregVenda;
    }

    public function NFV($Valor){
        return number_format($Valor,$this->getPrecisaoValor(),',','.');
    }

    public function NFQ($Qtde){
        return number_format($Qtde,$this->getPrecisaoQtde(),',','.');
    }

    public function NFD($Desconto){
        return number_format($Desconto,$this->getPrecisaoDesconto(),',','.');
    }

    public function RoundV($Valor){
        return round($Valor,$this->getPrecisaoValor());
    }

    public function RoundFQ($Qtde){
        return round($Qtde,$this->getPrecisaoQtde());
    }

    public function RoundFD($Desconto){
        return round($Desconto,$this->getPrecisaoDesconto());
    }
}
?>