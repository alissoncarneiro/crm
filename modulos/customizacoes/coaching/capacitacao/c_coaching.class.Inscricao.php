<?php
/*
 * c_coaching.class.Inscricao.php
 * Autor: Alex
 * 22/07/2011 16:24:17
 */
class Inscricao{
    private $NumregInscricao;
    private $DadosInscricao;

    public function __construct($NumregInscricao=NULL){
        if($NumregInscricao === NULL){
            if($this->InsereNovaInscricao()){
                return true;
            }
            return false;
        }
        else{
            $this->NumregInscricao = $NumregInscricao;
            $this->CarregaDadosInscricao();
        }
    }

    private function InsereNovaInscricao(){
        $ArSqlInsert = array(
            'id_pessoa'     => 0,
            'id_curso'      => 0,
            'id_vendedor'   => $_SESSION['id_usuario'],
            'id_usuario_cad'=> $_SESSION['id_usuario'],
            'id_situacao'   => 1,
            'dt_inscricao'  => date("Y-m-d"),
            'hr_inscricao'  => date("H:i")
        );
        $SqlInsert = AutoExecuteSql(TipoBancoDados, 'c_coaching_inscricao', $ArSqlInsert, 'INSERT');
        $QryInsert = iquery($SqlInsert);
        if($QryInsert){
            $this->NumregInscricao = $QryInsert;
            $this->setDadoInscricao('numreg', $this->getNumregInscricao());
            return true;
        }
        return false;
    }

    public function getNumregInscricao(){
        return $this->NumregInscricao;
    }

    private function CarregaDadosInscricao(){
        if($this->NumregInscricao == ''){
            $this->DadosInscricao = array();
            $this->DadosInscricao['id_vendedor'] = $_SESSION['id_usuario'];
            $this->DadosInscricao['id_usuario_cad'] = $_SESSION['id_usuario'];
            $this->DadosInscricao['dt_inscricao'] = date("Y-m-d");
            $this->DadosInscricao['hr_inscricao'] = date("H:i");
            return true;
        }
        $Sql = "SELECT * FROM c_coaching_inscricao WHERE numreg = '".$this->NumregInscricao."'";
        $Qry = query($Sql);
        $Ar = farray($Qry);
        foreach($Ar as $k => $v){
            if(!is_numeric($k)){
                $this->DadosInscricao[$k] = $v;
            }
        }
        return true;
    }

    public function AdicionaInscricaoCurso($IdAgenda){
        $InscricaoCurso = new InscricaoCurso($this,NULL,$IdAgenda);
    }

    public function ExcluiInscricaoCurso($IdAgenda){
        $SqlDelete = "DELETE FROM c_coaching_inscricao_curso WHERE id_inscricao = '".$this->getNumregInscricao()."' AND id_agenda = '".$IdAgenda."'";
        $QryDelete = query($SqlDelete);
        if($QryDelete){
            return true;
        }
        return false;
    }

    public function setDadoInscricao($IdCampo,$Valor){
        if($IdCampo){
            $this->DadosInscricao[$IdCampo] = $Valor;
            return false;
        }
        return true;
    }

    public function getDadosInscricao($IdCampo = NULL){
        if($IdCampo == NULL){
            return $this->DadosInscricao;
        }
        return $this->DadosInscricao[$IdCampo];
    }

    public function AtualizaDadosBD(){
        $ArUpdate           = $this->getDadosInscricao();

        $SqlUpdate = AutoExecuteSql(TipoBancoDados,'c_coaching_inscricao',$ArUpdate,'UPDATE',array('numreg'));
        $QryUpdate = query($SqlUpdate);
        if($QryUpdate){
            return true;
        }
        else{
            return false;
        }
    }
    
    public function ValidaValorTotalPagamento($VlTotal){
        $QryTotalPagamento = query("SELECT SUM(vl_pagto) AS soma FROM c_coaching_inscricao_pagto WHERE id_inscricao = '".$this->getNumregInscricao()."' AND id_venda IS NULL");
        $ArTotalPagamento = farray($QryTotalPagamento);
        
        if($ArTotalPagamento['soma'] > $VlTotal || $ArTotalPagamento['soma'] < $VlTotal){
            return false;
        }       
        return true;
    }
    
    public function getFrequenciaPresenca(){
        $SqlTotalAulas = "SELECT COUNT(*) AS CNT FROM c_coaching_inscricao_curso_detalhe WHERE id_inscricao = '".$this->getNumregInscricao()."'";
        $QryTotalAulas = query($SqlTotalAulas);
        $ArTotalAulas = farray($QryTotalAulas);
        
        $SqlTotalAulasPresente = "SELECT COUNT(*) AS CNT FROM c_coaching_inscricao_curso_detalhe WHERE id_inscricao = '".$this->getNumregInscricao()."' AND sn_presente  = 1";
        $QryTotalAulasPresente = query($SqlTotalAulasPresente);
        $ArTotalAulasPresente = farray($QryTotalAulasPresente);
        
        $PctPresenca = @ceil(($ArTotalAulasPresente['CNT'] / $ArTotalAulas['CNT']) * 100);
        return $PctPresenca;        
    }
    
    public function FinalizaInscricao($VlTotalInscricao,$Obs){
        $SqlMaxIdVenda = "SELECT MAX(id_venda) AS max_id_venda FROM c_coaching_inscricao_venda WHERE id_inscricao = '".$this->getNumregInscricao()."'";
        $QryMaxIdVenda = query($SqlMaxIdVenda);
        $ArMaxIdVenda = farray($QryMaxIdVenda);

        $IdVenda = $ArMaxIdVenda['max_id_venda'];
        $IdVenda = ($IdVenda == '')?1:$IdVenda+1;

        $ArInsertIncricaoVenda = array(
            'id_inscricao'          => $this->getNumregInscricao(),
            'id_venda'              => $IdVenda,
            'dt_venda'              => date("Y-m-d"),
            'vl_total_venda'        => $VlTotalInscricao,
            'obs'                   => $Obs
        );

        $SqlInsertInscricaoVenda = AutoExecuteSql(TipoBancoDados, 'c_coaching_inscricao_venda', $ArInsertIncricaoVenda, 'INSERT');
        $QryInsertInscricaoVenda = iquery($SqlInsertInscricaoVenda);
        
        if(!$QryInsertInscricaoVenda){
            return false;
        }
        
        $SqlAgendasSelecionadas = "SELECT t2.*,t1.dt_curso,t1.hr_inicio,t1.hr_fim FROM c_coaching_agenda_curso_detalhe t1 INNER JOIN c_coaching_agenda_curso t2 ON t1.id_agenda_curso = t2.numreg
                                        WHERE t2.numreg IN(SELECT t3.id_agenda FROM c_coaching_inscricao_curso t3 WHERE t3.id_inscricao = '".$this->getNumregInscricao()."' AND t3.id_venda IS NULL)";
        $QryAgendasSelecionadas = query($SqlAgendasSelecionadas);
        
        /* Cód. do estabelecimento da primeira agenda */
        $IdEstabelecimentoPrimeiraAgenda = false;
        while($ArAgendasSelecionadas = farray($QryAgendasSelecionadas)){
            if($IdEstabelecimentoPrimeiraAgenda === false){
                $IdEstabelecimentoPrimeiraAgenda = $ArAgendasSelecionadas['id_estabelecimento'];
            }            
            $ArSqlInsertInscricaoCursoDetalhe = array(
                'id_venda'              => $IdVenda,
                'id_inscricao'          => $this->getNumregInscricao(),
                'id_agenda'             => $ArAgendasSelecionadas['numreg'],
                'id_pessoa'             => $this->getDadosInscricao('id_pessoa'),
                'id_pessoa_financeiro'  => $this->getDadosInscricao('id_pessoa_financeiro'),
                'id_estabelecimento'    => $ArAgendasSelecionadas['id_estabelecimento'],
                'id_pessoa_licenciado'  => $ArAgendasSelecionadas['id_pessoa_licenciado'],
                'id_curso'              => $ArAgendasSelecionadas['id_curso'],
                'id_parte'              => $ArAgendasSelecionadas['id_parte'],
                'id_instrutor'          => $ArAgendasSelecionadas['id_instrutor'],
                'dt_curso'              => $ArAgendasSelecionadas['dt_curso'],
                'id_modulo'             => $ArAgendasSelecionadas['id_modulo'],
                'id_local_curso'        => $ArAgendasSelecionadas['id_local_curso'],
                'id_hotel'              => $ArAgendasSelecionadas['id_hotel'],
                'dt_inscricao'          => $this->getDadosInscricao('dt_inscricao'),
                'hr_inscricao'          => $this->getDadosInscricao('hr_inscricao'),
                'sn_presente'           => 0
            );

            $SqlInsertInscricaoCursoDetalhe = AutoExecuteSql(TipoBancoDados, 'c_coaching_inscricao_curso_detalhe', $ArSqlInsertInscricaoCursoDetalhe, 'INSERT');

            $QryInsertInscricaoCursoDetalhe = query($SqlInsertInscricaoCursoDetalhe);
            
            if(!$QryInsertInscricaoCursoDetalhe){
                return false;
            }            
        }
        
        $SqlUpdatePagamento = "UPDATE c_coaching_inscricao_pagto SET id_venda = '".$IdVenda."', numreg_venda = '".$QryInsertInscricaoVenda."' WHERE id_inscricao = '".$this->getNumregInscricao()."' AND id_venda IS NULL";
        $QryUpdatePagamento = query($SqlUpdatePagamento);

        $SqlUpdateAgendasSelecinadas = "UPDATE c_coaching_inscricao_curso SET id_venda = '".$IdVenda."' WHERE id_inscricao = '".$this->getNumregInscricao()."' AND id_venda IS NULL";
        $QryUpdateAgendasSelecinadas = query($SqlUpdateAgendasSelecinadas);
        
        $SqlUpdateInscricao = "UPDATE c_coaching_inscricao SET id_situacao = 4, id_estabelecimento = '".$IdEstabelecimentoPrimeiraAgenda."' WHERE numreg = '".$this->getNumregInscricao()."'";
        $QryUpdateInscricao = query($SqlUpdateInscricao);

        return true;
    }
}
?>