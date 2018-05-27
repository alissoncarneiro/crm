<?php

/*
 * class.Venda.Representante.php
 * Autor: Lucas
 * 30/11/2010 14:22
 * -
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */

class VendaRepresentante{

    protected $NumregVendaRepresentante;
    protected $DadosVendaRepresentante;
    protected $RepresentanteUsuario;

    private $Erro = false;
    private $MensagemErro;
    private $ObjVenda;
    private $DescricaoTipoParticipacaoVenda;
    private $PctComissao;
    private $VlComissao;

    public function __construct($ObjVenda,$NumregVendaRepresentante=NULL){

        /*
         * Repassando a referência do objeto de venda
         */
        if(!is_object($ObjVenda)){//Se o objeto de venda não é um objeto válido
            echo 'Parâmetro objeto de venda inválido';
            $this->Erro = true;
            return false;
        }
        $this->setObjVenda($ObjVenda);

        /*
         * Definindo variáveis de acesso para pedido ou orçamento
         */
        if($this->ObjVenda->getTipoVenda() == 1){//Se for tipo orçamento
            $this->ArrayDeParaCamposEspecificosTabelaVendaRepresentante = array(
                'id_orcamento' => 'id_venda'
            );
        }
        elseif($this->ObjVenda->getTipoVenda() == 2){//Se for tipo pedido
            $this->ArrayDeParaCamposEspecificosTabelaVendaRepresentante = array(
                'id_pedido' => 'id_venda'
            );
        }
        else{
            return false;
        }

        $this->NumregVendaRepresentante = $NumregVendaRepresentante;
        $this->CarregaDadosVendaRepresentanteBD();
        $this->RepresentanteUsuario = new Usuario($this->getDadosVendaRepresentante('id_representante'));
    }

    public function CarregaDadosVendaRepresentanteBD(){
        if($this->Erro == true){
            return false;
        }

        $QryVendaRepresentante = query("SELECT * FROM ".$this->ObjVenda->getTabelaVendaRepresentante()." WHERE numreg = ".$this->NumregVendaRepresentante);
        $ArVendaRepresentante = farray($QryVendaRepresentante);
        foreach($ArVendaRepresentante as $k => $v){
            if(!is_numeric($k)){
                $this->DadosVendaRepresentante[$k] = $v;
            }
        }
        if(!empty($ArVendaRepresentante['id_tp_participacao'])){
            $QryDescricaoTipoParticipacaoVenda = query("SELECT nome_tp_participacao_venda FROM is_tp_participacao_venda WHERE numreg = '".$ArVendaRepresentante['id_tp_participacao']."'");
            $ArDescricaoTipoParticipacaoVenda = farray($QryDescricaoTipoParticipacaoVenda);
            $this->DescricaoTipoParticipacaoVenda = $ArDescricaoTipoParticipacaoVenda['nome_tp_participacao_venda'];
        }
        $this->NumregVendaRepresentante = $ArVendaRepresentante['numreg'];
        $this->PctComissao = $ArVendaRepresentante['pct_comissao'];
        $this->VlComissao = $ArVendaRepresentante['vl_comissao'];
    }
    
    public function setObjVenda($ObjVenda){
        $this->ObjVenda = $ObjVenda;
    }

    public function setVlComissao($VlComissao){
        $this->VlComissao = $VlComissao;
        $this->setDadoVendaRepresentante('vl_comissao',$VlComissao);

        $PctComissao = uM::uMath_pct_de_diferenca_de_valor($this->getObjVenda()->getVlTotalVendaLiquido(),$this->VlComissao);

        $this->PctComissao = $PctComissao;
        $this->setDadoVendaRepresentante('pct_comissao',$PctComissao);
    }

    public function setDadosVendaRepresentante($ArDados){
        if($this->Erro == true){
            return false;
        }
        $this->DadosVendaRepresentante = $this->decodeDeParaCamposValor($ArDados);
    }

    public function setDadoVendaRepresentante($IdCampo,$Valor){
        $this->DadosVendaRepresentante[$IdCampo] = $Valor;
    }

    public function getNumregVendaRepresentante(){
        return $this->NumregVendaRepresentante;
    }

    public function getIdRepresentante(){
        return $this->getDadosVendaRepresentante('id_representante');
    }

    public function getPctComissao(){
        return $this->PctComissao;
    }
    
    public function getVlComissao(){
        return $this->VlComissao;
    }

    public function getObjVenda(){
        return $this->ObjVenda;
    }

    public function getRepresentanteUsuario(){
        return $this->RepresentanteUsuario;
    }

    public function getDadosVendaRepresentante($IdCampo = NULL){
        if($IdCampo == NULL){
            return $this->DadosVendaRepresentante;
        }
        return $this->DadosVendaRepresentante[$IdCampo];
    }

    public function getDescricaoTipoParticipacaoVenda(){
        return $this->DescricaoTipoParticipacaoVenda;
    }

    public function getComissaoAlteradaManualmente(){
        if($this->getDadosVendaRepresentante('sn_alterado_manual') == 1){
            return true;
        }
        return false;
    }

    public function isPrincipal(){
        if($this->getDadosVendaRepresentante('sn_representante_principal') == 1){
            return true;
        }
        return false;
    }

    public function AtualizaDadosBD(){
        $ArSqlUpdateRepresentante = $this->getDadosVendaRepresentante();
        $SqlUpdateRepresentante = AutoExecuteSql(TipoBancoDados,$this->ObjVenda->getTabelaVendaRepresentante(),$ArSqlUpdateRepresentante,'UPDATE',array('numreg'));
        query($SqlUpdateRepresentante);
    }
    
    public function encodeDeParaCamposValor($ArDados){
        $ArDadosRetorno = $ArDados;
        $ArrayDePara = $this->ArrayDeParaCamposEspecificosTabelaVendaRepresentante;
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
            $Search = array_search($k,$this->ArrayDeParaCamposEspecificosTabelaVendaRepresentante);
            if($Search != ''){
                unset($ArDadosRetorno[$k]);
                $ArDadosRetorno[$Search] = $v;
            }
        }
        return $ArDadosRetorno;
    }


}
?>