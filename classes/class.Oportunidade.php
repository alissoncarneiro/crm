<?php

/*
 * class.Oportunidade.php
 * Autor: Alex
 * 14/04/2011 14:00:00
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */

class Oportunidade{
    private $NumregOportunidade;
    private $Pessoa;
    private $Contato;
    private $Mensagem;

    public function __construct($NumregOportunidade = NULL){
        if(!empty($NumregOportunidade)){
            $this->NumregOportunidade = $NumregOportunidade;
            $this->carregaDadosOportunidadeBD();
        } elseif($NumregOportunidade == NULL){
            $this->InsereOportunidade();
            $this->CarregaDadosOportunidadeBD();
        } else{
            echo 'Parâmetro inválido';
        }
    }

    public function setMensagem($Texto){
        $this->Mensagem .= $Texto."\n";
    }

    public function getMensagem(){
        return $this->Mensagem;
    }

    public function CarregaDadosOportunidadeBD(){
        $QryOportunidade = query("SELECT * FROM is_oportunidade WHERE numreg = ".$this->getNumregOportunidade());
        $ArOportunidade = farray($QryOportunidade);
        foreach($ArOportunidade as $k => $v){
            if(!is_numeric($k)){
                $this->DadosOportunidade[$k] = $v;
            }
        }
        /* Carregando Pessoa */
        if(!empty($this->DadosOportunidade['id_pessoa'])){
            $this->Pessoa = new Pessoa($this->DadosOportunidade['id_pessoa']);
        }
        /* Carregando contato */
        if(!empty($this->DadosOportunidade['id_contato'])){
            $this->Contato = new Contato($this->DadosOportunidade['id_contato']);
        }
    }
    
    protected function setNumregOportunidade($NumregOportunidade){
        if(empty($this->NumregOportunidade)){
            $this->NumregOportunidade = $NumregOportunidade;
            return true;
        } else{
            return false;
        }
    }

    public function getNumregOportunidade(){
        return $this->NumregOportunidade;
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

    public function getDadosOportunidade($IdCampo=NULL){
        if($IdCampo == NULL){
            return $this->DadosOportunidade;
        }
        else{
            return $this->DadosOportunidade[$IdCampo];
        }
    }

    public function setDadoOportunidade($IdCampo,$Valor){
        $this->DadosOportunidade[$IdCampo] = $Valor;
    }

    public function AtualizaDadosOportunidadeBD(){
        $ArUpdate = $this->getDadosOportunidade();
        $SqlUpdate = AutoExecuteSql(TipoBancoDados,'is_oportunidade',$ArUpdate,'UPDATE',array('numreg'));
        $QryUpdate = query($SqlUpdate);
        if($QryUpdate){
            return true;
        }
        else{
            return false;
        }
    }

    private function InsereOportunidade(){
        $ArInsert = array(
            'assunto'       => 'Nova Oportunidade',
            'dt_inicio'     => date("Y-m-d")
        );

        $SqlInsert = AutoExecuteSql(TipoBancoDados,'is_oportunidade', $ArInsert, 'INSERT');
        $NumregNovaOportunidade = iquery($SqlInsert);
        if(!$NumregNovaOportunidade){
            return false;
        }
        $this->setNumregOportunidade($NumregNovaOportunidade);
        return true;
    }

    public function CalculaPercentualProbabilidade(){
        $IdCiclo = $this->getDadosOportunidade('id_opor_ciclo');
        $IdFase = $this->getDadosOportunidade('id_opor_ciclo_fase');
        if(trim($IdCiclo) == '' || trim($IdCiclo) == ''){
            $PercentualProbabilidade = 0;
        }
        else{
            $SqlPercentualProbabilidade = "SELECT probabilidade FROM is_opor_ciclo_fase WHERE id_opor_ciclo = ".$IdCiclo." AND id_opor_fase = ".$IdFase;
            $QryPercentualProbabilidade = query($SqlPercentualProbabilidade);
            $ArPercentualProbabilidade = farray($QryPercentualProbabilidade);
            $PercentualProbabilidade = $ArPercentualProbabilidade['probabilidade'];
        }
        $this->setDadoOportunidade('pct_sucesso', $PercentualProbabilidade);
    }

    public function DeletaTodosItens($Confirmacao=false){
        if($Confirmacao === true){
            $SqlDelete = "DELETE FROM is_opor_produto WHERE id_oportunidade = ".$this->getNumregOportunidade();
            if(query($SqlDelete)){
                return true;
            }
            return false;
        }
        return false;
    }

    public function AdicionaItem($ArDadosItem){
        /* Aplicando consistencias */
        if(trim($ArDadosItem['id_produto']) == '' && trim($ArDadosItem['outro']) == ''){
            $this->setMensagem('Cód. Produto ou Descrição não pode ser branco.');
            return false;
        }

        unset($ArDadosItem['numreg']);
        $ArDadosItem['id_oportunidade'] = $this->getNumregOportunidade();
        /* Calculando o valor total */
        $ValorTotal = $ArDadosItem['qtde'] * $ArDadosItem['valor'];
        $ValorTotal = uM::uMath_vl_menos_pct($ArDadosItem['pct_desc'], $ValorTotal);
        $ValorTotal = round($ValorTotal,2);
        $ArDadosItem['valor_total']     = $ValorTotal;

        $SqlInsertItemOportunidade = AutoExecuteSql(TipoBancoDados, 'is_opor_produto', $ArDadosItem, 'INSERT');
        $QryInsertItemOportunidade = query($SqlInsertItemOportunidade);
        if(!$QryInsertItemOportunidade){
            return false;
        }
        return true;
    }

    public function GeraOrcamento(){
        if($this->getDadosOportunidade('id_orcamento_filho') != ''){
            $this->setMensagem('Já existe o orçamento Nº '.$this->getDadosOportunidade('id_orcamento_filho').' que foi gerado a partir desta oportunidade.');
            return false;
        }
        elseif($this->getDadosOportunidade('id_orcamento_pai') != ''){
            $this->setMensagem('Esta oportunidade foi gerada a partir do orçamento Nº '.$this->getDadosOportunidade('id_orcamento_filho').' e não pode gerar outro orçamento.');
            return false;
        }
        $Orcamento = new Orcamento(1);
        $Orcamento->setDadoVenda('id_oportunidade_pai',$this->getNumregOportunidade());
        $Orcamento->setDadoVenda('id_pessoa',$this->getDadosOportunidade('id_pessoa'));
        $Orcamento->setDadoVenda('id_contato',$this->getDadosOportunidade('id_pessoa_contato'));
        $Orcamento->setDadoVenda('id_origem',$this->getDadosOportunidade('id_origem'));
        $Orcamento->setDadoVenda('id_pessoa_indicacao',$this->getDadosOportunidade('id_pessoa_indic'));
        $Orcamento->setDadoVenda('id_usuario_cad',$this->getDadosOportunidade('id_usuario_resp'));
        $Orcamento->setDadoVenda('id_representante_pessoa',$this->getDadosOportunidade('id_usuario_gestor'));
        $Orcamento->setDadoVenda('id_tab_preco',$this->getDadosOportunidade('id_tab_preco'));
        $Orcamento->setDadoVenda('id_cond_pagto',$this->getDadosOportunidade('id_cond_pagto'));
        $Orcamento->setDadoVenda('id_situacao_venda',$this->getDadosOportunidade('id_situacao'));
        $Orcamento->setDadoVenda('obs',$this->getDadosOportunidade('obs'));
        $Orcamento->AtualizaDadosVendaBD();

        $this->setDadoOportunidade('id_orcamento_filho', $Orcamento->getNumregVenda());
        $this->AtualizaDadosOportunidadeBD();

        $this->setMensagem('Orçamento Nº '.$Orcamento->getNumregVenda().' gerado.');

        return true;
    }
}
?>