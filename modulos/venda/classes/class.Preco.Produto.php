<?php

/*
 * class.Preco.Produto.php
 * Autor: Alex
 * 17/01/2011 17:12:00
 *
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */

class PrecoProduto{

    private $IdProduto;
    private $IdTabPreco;
    private $IdMoeda;
    private $SiglaMoeda;
    private $Preco;
    private $VlCotacao;
    private $IdProdutoEmbalagem;

    private $PrecisaoValor;
    private $PrecisaoQtde;
    private $PrecisaoDesconto;

    private $Produto;
    private $VendaParametro;

    private $Mensagem;

    public $PrecoNF = false; /* Define se está sendo retornado um preco de NF */
    public $DtSugestaoPrecoNF;
    public $PrecoSugestaoPrecoNF;
    public $IdGrupoTabPreco;

    public function __construct($IdProduto,$IdGrupoTabPreco,$IdTabPreco,$IdUnidMedida=NULL,$IdProdutoEmbalagem=NULL){
        /*
         * Validando se foram preenchidos os parâmetros corretamente
         */
        if(empty($IdProduto)){
            $this->Mensagem = 'Produto e devem ser informados';
            return false;
        }
        $this->IdProduto = $IdProduto;
        $this->IdTabPreco = $IdTabPreco;
        $this->IdGrupoTabPreco = $IdGrupoTabPreco;
        $this->IdProdutoEmbalagem = $IdProdutoEmbalagem;
        $this->Produto = new Produto($IdProduto);
        $this->VendaParametro = new VendaParametro();

        $this->PrecisaoValor    = getParametrosVenda('precisao_valor');
        $this->PrecisaoQtde     = getParametrosVenda('precisao_qtde');
        $this->PrecisaoDesconto = getParametrosVenda('precisao_desconto');

        if($IdUnidMedida == NULL){
            $IdUnidMedida = $this->Produto->getDadosProduto('id_unid_medida_padrao');
        }
        $SqlTabPreco = "SELECT id_moeda FROM is_tab_preco WHERE numreg = '".$IdTabPreco."'";
        $QryTabPreco = query($SqlTabPreco);
        $ArTabPreco = farray($QryTabPreco);
        $this->IdMoeda = $ArTabPreco['id_moeda'];
        /* Se a tabela de preço não foi informada coloca o preço como 0 */
        if($IdTabPreco != NULL && $IdTabPreco != ''){
            $VlUnitario = $this->getPrecoBD($IdTabPreco,$IdUnidMedida);
            if($VlUnitario === false){
                $VlUnitario = $this->getPrecoBD($IdTabPreco,$this->Produto->getIdUnidMedidaPadrao());
                if($VlUnitario === false){
                    $VlUnitario = 0;
                }
            }
            $this->Preco = $VlUnitario;
        }
        else{
            $this->IdMoeda = NULL;
            $this->Preco = 0;
        }
        /* Pegando a cotação e a sigla da cotação */
        $this->VlCotacao = getCotacaoBD($this->IdMoeda,1);
        $this->SiglaMoeda = getSiglaMoedaDaTabPreco($IdTabPreco);
    }
    
    public function getPrecoBD($IdTabPreco,$IdUnidMedida){
        $SqlVlUnitario = "SELECT ".((TipoBancoDados == 'mssql')?'TOP(1)':'')." t1.vl_unitario
                                    FROM is_tab_preco_valor t1
                                    INNER JOIN is_tab_preco t2 ON t1.id_tab_preco = t2.numreg
                                    WHERE t1.id_tab_preco = '".$IdTabPreco."' AND t1.id_produto = '".$this->IdProduto."' AND id_unid_medida = '".$IdUnidMedida."' AND t1.sn_ativo = 1 AND dt_validade_ini <= '".date("Y-m-d")."' AND ('".date("Y-m-d")."' BETWEEN t2.dt_vigencia_ini AND t2.dt_vigencia_fim) ORDER BY t1.dt_validade_ini DESC".((TipoBancoDados == 'mysql')?' LIMIT 1':'');
        $QryVlUnitario = query($SqlVlUnitario);
        $ArVlUnitario = farray($QryVlUnitario);
        if($ArVlUnitario){
            return $ArVlUnitario['vl_unitario'];
        }
        else{
            return false;
        }
    }

    public function getPreco(){
        return $this->Preco;
    }

    public function getVlCotacao(){
        return $this->VlCotacao;
    }

    public function getStringPreco($AdicionaCotacao=false){
        $Preco = $this->Preco;
        if($this->VendaParametro->getModoUnidMedida() == 3){
            if($this->IdGrupoTabPreco == 1){
                $QtdePorEmbalagem = $this->Produto->getQtdePorEmbalagem($this->IdProdutoEmbalagem);
                $Preco = $Preco * $QtdePorEmbalagem;
            }
            elseif($this->IdGrupoTabPreco == 2){
                $QtdePorUnidMedida = $this->Produto->getQtdePorUnidMedida($this->Produto->getIdUnidMedidaAtacadoVarejo($this->IdGrupoTabPreco));
                $Preco = $Preco * $QtdePorUnidMedida;
            }
        }
        
        $StringRetorno = '';
        $StringRetorno = $this->SiglaMoeda.' '.number_format_min($Preco,2,',','.');
        
        $IdMoedaReal = getParametrosVenda('id_moeda_real');
        $QryNumregMoedaReal = query("SELECT * FROM is_moeda WHERE numreg = '".$IdMoedaReal."'");
        $ArNumregMoedaReal = farray($QryNumregMoedaReal);
        if($this->IdMoeda != NULL && $this->IdMoeda != $ArNumregMoedaReal['numreg'] && $AdicionaCotacao === true){/* Se a moeda for diferente de 1-real e o parametro para adicionar a cotacao for true */
            $StringRetorno.= ' x '.number_format_min($this->VlCotacao).' = R$ '.number_format_min(($Preco*$this->VlCotacao),2,',','.');
        }
        if($this->PrecoNF === true){
            $StringRetorno .= ' (preço sugerido da ult. NF para o estado em '.uB::DataEn2Br($this->DtSugestaoPrecoNF,false).')';
        }
        return $StringRetorno;
    }

    public function getStringCotacao(){
        $StringRetorno = '';
        $IdMoedaReal = getParametrosVenda('id_moeda_real');
        $QryNumregMoedaReal = query("SELECT * FROM is_moeda WHERE numreg = '".$IdMoedaReal."'");
        $ArNumregMoedaReal = farray($QryNumregMoedaReal);
        if($this->IdMoeda != NULL && $this->IdMoeda != $ArNumregMoedaReal['numreg']){/* Se a moeda for diferente de 1-real */
            $StringRetorno.= ' Cotação: R$ '.number_format_min($this->VlCotacao);
        }
        return $StringRetorno;
    }

    public function NFV($Valor){
        return number_format($Valor,$this->PrecisaoValor,',','.');
    }

    public function RoundV($Valor){
        return round($Valor,$this->PrecisaoValor);
    }

    /**
     * Calcula o preço do produto baseado na última NF emitida. Se não encontrar retorna false.
     * @param array $Parametros Chaves possíveis id_produto_erp|uf
     * @return bool|float
     */
    public function CalculaSugestaoDePrecoDeNF($Parametros){
        $Produto                    = new Produto($Parametros['id_produto']);
        $IdProdutoErp               = $Produto->getDadosProduto('id_produto_erp');
        $SqlSugestaoPrecoNF         = "SELECT ".((TipoBancoDados == 'mssql')?' TOP(1)':'')." it_codigo, dt_emis_nota, qt_faturada, vl_tot_item FROM is_dm_notas WHERE it_codigo = '".$IdProdutoErp."' AND nome_estado = '".$Parametros['uf']."' ORDER BY dt_emis_nota DESC ".((TipoBancoDados == 'mysql')?' LIMIT 1':'')."";
        $QrySugestaoPrecoNF         = query($SqlSugestaoPrecoNF);
        if(numrows($QrySugestaoPrecoNF) == 0){ /* Se não enconrar preço retorna false */
            return false;
        }
        $ArSugestaoPrecoNF          = farray($QrySugestaoPrecoNF);
        $Preco                      = $ArSugestaoPrecoNF['vl_tot_item'] / $ArSugestaoPrecoNF['qt_faturada'];
        $this->PrecoNF              = true;
        $this->Preco                = $Preco;
        $this->DtSugestaoPrecoNF    = $ArSugestaoPrecoNF['dt_emis_nota'];
    }
}
?>