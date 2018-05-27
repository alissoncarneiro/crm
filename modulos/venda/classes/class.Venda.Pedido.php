<?php
/*
 * class.Venda.Pedido.php
 * Autor: Alex
 * 18/10/2010 15:02:00
 * Classe responsável por tratar os pedidos e orçamentos
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
class Pedido extends Venda{

    /**
     * Carrega todos os dados de um pedido
     * @param int $TipoVenda Fixo 2 para pedidos
     * @param int $NumregVenda Numreg do pedido. Se receber NULL gera um novo pedido
     * @param boolean $CarregaItens Se irá carregar os itens do banco de dados. Padrão = true
     * @param type $CarregaUsuario Se irá carregar os dados do usuário logado. Padrão = true
     * @param type $VisualizarRevisao Se está apenas em modo de visualização de revisão. Padrão = false (Válido apenas para orçamentos)
     */
    public function __construct($TipoVenda,$NumregVenda = NULL, $CarregaItens = true, $CarregaUsuario = true,$VisualizarRevisao = false){
        $this->ArrayDeParaCamposEspecificosTabelaVenda = array(
            'id_pedido'                     => 'id_venda',
            'tp_pedido'                     => 'tp_venda',
            'id_pedido_cliente'             => 'id_venda_cliente',
            'id_pedido_representante'       => 'id_venda_representante',
            'id_tp_pedido'                  => 'id_tp_venda',
            'id_situacao_pedido'            => 'id_situacao_venda',
            'id_revisao_orcamento'          => 'id_revisao_venda',
            'dt_pedido'                     => 'dt_venda',
            'dt_validade_pedido'            => 'dt_validade_venda',
            'id_pedido_bonificacao'         => 'id_venda_bonificacao',
            'sn_antecipa_pedido'            => 'sn_antecida_venda',
            'id_pedido_origem_clone'        => 'id_venda_origem_clone'
        );
        parent::__construct($TipoVenda,$NumregVenda,$CarregaItens,$CarregaUsuario,$VisualizarRevisao);
    }

    public function InserePedido(){
        $ArInsert = array(
            'dt_cadastro'           => date("Y-m-d"),
            'hr_cadastro'           => date("H:i:s"),
            'dt_pedido'             => date("Y-m-d"),
            'id_usuario_cad'        => $_SESSION['id_usuario'],
            'dt_pedido'             => date("Y-m-d"),
            'sn_digitacao_completa' => 0,
            'id_pessoa'             => 0,
            'id_tp_pedido'          => 0,
            'id_situacao_pedido'    => 0,
            'id_moeda'              => 0,
            'vl_total_desconto'     => 0,
            'id_tp_preco'           => 0,
            'vl_total_desconto'         => 0,
            'id_tp_preco'               => 0,
            'vl_total_bruto'            => 0,
            'vl_total_liquido'          => 0,
            'vl_total_ipi'              => 0,
            'vl_total_st'               => 0,
            'vl_total_icms'             => 0,

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
            'sn_exportado_erp'          => 0
        );

        /* Aplicando tratamentos na array de insert */
        $ArInsert = $this->TrataArInsertVenda($ArInsert);

        $SqlInsert = AutoExecuteSql(TipoBancoDados,$this->TabelaVenda, $ArInsert, 'INSERT');
        $QryNovoPedido = iquery($SqlInsert);
        if(!$QryNovoPedido){
            $this->setMensagemDebug('Erro de SQL: '.$SqlInsert.mysql_error());
            $this->setMensagem('Erro ao inserir registro!');
            return false;
        }
        $this->setNumregVenda($QryNovoPedido);
        return true;
    }

    public function CarregaDadosVendaBD(){
        $QryVenda = query("SELECT * FROM ".$this->getTabelaVenda()." WHERE numreg = ".$this->getNumregVenda());
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

    public function ExportaPedido(){
        return VendaCallBackCustom::ExecutaVenda($this, 'ExportaPedido', 'Inicio');
    }

    public function VerificaSeExisteOportunidadePai(){ return true;}

    public function VerificaSeExisteOportunidadeFilha(){return true;}

    public function GeraAtualizaOportunidadeFilha(){ return true;}

    public function AtualizaOportunidadePai(){ return true;}

    public function AtualizaItensOportunidadeFilha(){ return true;}

    public function AtualizaItensOportunidadePai(){ return true;}

    public function GeraAtualizaOportunidadePaiEFilha(){ return true;}

    public function getNumeroOportunidadeFilhaGerada(){ return true;}
}
?>