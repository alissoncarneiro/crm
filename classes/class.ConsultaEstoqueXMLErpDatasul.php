<?php
/*
 * class.ConsultaEstoqueXMLErpDatasul.php
 * Autor: Alex
 * 04/04/2011 16:00:00
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
class ConsultaEstoqueXMLErpDatasul{
    private $VendaParametro;
    private $UrlXML;
    private $StringXML;
    private $XML;

    public function  __construct($VendaParametro,$IdProduto,$IdEstabelecimento){
        $this->VendaParametro   = $VendaParametro;
        $this->UrlXML           = $this->VendaParametro->getURLEstoqueXmlErpDatasul().'?id_produto='.$IdProduto.'&id_estabelecimento='.$IdEstabelecimento;
        $this->StringXML        = file_get_contents($this->UrlXML,false);
        $this->XML              = simplexml_load_string($this->StringXML);
    }

    public function getArrayEstoqueAtual(){
        $ArrayRetorno = array();
        foreach($this->XML->quantitade_atual_detalhes->detalhe_estoque as $DetalheEstoque){
            $ArrayDetalheEstoque = array();

            $ArrayDetalheEstoque[] = $DetalheEstoque->id_estabelecimento;
            $ArrayDetalheEstoque[] = (float)$DetalheEstoque->quantidade;
            $ArrayDetalheEstoque[] = $DetalheEstoque->lote;
            $ArrayDetalheEstoque[] = $DetalheEstoque->data_validade;
            $ArrayDetalheEstoque[] = $DetalheEstoque->referencia;
            
            $ArrayRetorno[]      = $ArrayDetalheEstoque;
        }
        return $ArrayRetorno;
    }

    public function getArrayPedidosNaoFaturadosERP(){
        $ArrayRetorno = array();
        foreach($this->XML->quantidade_nao_faturada_detalhes->detalhe_estoque as $DetalheEstoque){
            $ArrayDetalheEstoque = array();

            $ArrayDetalheEstoque[] = $DetalheEstoque->nome_cliente;
            $ArrayDetalheEstoque[] = $DetalheEstoque->nome_vendedor;
            $ArrayDetalheEstoque[] = (float)$DetalheEstoque->quantidade;
            $ArrayDetalheEstoque[] = $DetalheEstoque->data_entrega;

            $ArrayRetorno[]      = $ArrayDetalheEstoque;
        }
        return $ArrayRetorno;
    }

    public function getArrayPedidosNaoIntegrados(){
        $ArrayRetorno = array();
        foreach($this->XML->quantidade_nao_integrada_detalhes->detalhe_estoque as $DetalheEstoque){
            $ArrayDetalheEstoque = array();

            $ArrayDetalheEstoque[] = $DetalheEstoque->numero_pedido;
            $ArrayDetalheEstoque[] = $DetalheEstoque->nome_cliente;
            $ArrayDetalheEstoque[] = (float)$DetalheEstoque->quantidade;
            $ArrayDetalheEstoque[] = $DetalheEstoque->data_entrega;
            $ArrayDetalheEstoque[] = $DetalheEstoque->nome_vendedor;

            $ArrayRetorno[]      = $ArrayDetalheEstoque;
        }
        return $ArrayRetorno;
    }

    public function getArrayPrevisaoCompras(){
        $ArrayRetorno = array();
        foreach($this->XML->previsao_compras_detalhes->detalhe_estoque as $DetalheEstoque){
            $ArrayDetalheEstoque = array();

            $ArrayDetalheEstoque[] = $DetalheEstoque->numero_pedido;
            $ArrayDetalheEstoque[] = $DetalheEstoque->data_compra;
            $ArrayDetalheEstoque[] = $DetalheEstoque->nome_fornecedor;
            $ArrayDetalheEstoque[] = $DetalheEstoque->data_prevista;
            $ArrayDetalheEstoque[] = (float)$DetalheEstoque->quantidade;

            $ArrayRetorno[]      = $ArrayDetalheEstoque;
        }
        return $ArrayRetorno;
    }

    public function getArReferencia(){
        return array();
    }

    public function getQuantidadeDisponivel(){
        return (float)$this->XML->quantidade_disponivel;
    }

    public function getQuantidadeAtual(){
        return (float)$this->XML->quantidade_atual;
    }

    public function getQuantidadeNaoFaturada(){
        return (float)$this->XML->quantidade_nao_faturada;
    }

    public function getQuantidadeNaoIntegrada(){
        return (float)$this->XML->quantidade_nao_integrada;
    }
}
?>