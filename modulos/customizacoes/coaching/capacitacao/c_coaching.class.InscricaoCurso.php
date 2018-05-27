<?php
/*
 * c_coaching.class.Inscricao.php
 * Autor: Alex
 * 22/07/2011 16:24:17
 */
class InscricaoCurso{
    private $NumregInscricaoCurso;
    private $DadosInscricaoCurso;
    private $ObjInscricao;
    
    public function __construct(Inscricao $ObjInscricao,$NumregInscricaoCurso = NULL,$IdAgenda=''){
        $this->ObjInscricao = $ObjInscricao;
        
        if($NumregInscricaoCurso == NULL){
            if($this->InsereNovaInscricaoCurso($IdAgenda)){
                return true;
            }
            return false;
        }
        else{
            $this->NumregInscricaoCurso = $NumregInscricao;
            $this->CarregaDadosInscricaoCurso();
        }
    }
    
    public function InsereNovaInscricaoCurso($IdAgenda){
        if($IdAgenda == '' || $this->NumregInscricaoCurso != ''){
            return false;
        }
        
        $SqlAgenda = "SELECT id_modulo,id_local_curso FROM c_coaching_agenda_curso WHERE id_situacao not in(4,5) and numreg = ".$IdAgenda;
        $QryAgenda = query($SqlAgenda);
        $ArAgenda = farray($QryAgenda);
        
        $SqlModulo = "SELECT numreg,id_parte FROM c_coaching_modulo WHERE numreg = ".$ArAgenda['id_modulo'];
        $QryModulo = query($SqlModulo);
        $ArModulo = farray($QryModulo);
        
        $ArSqlInsert = array(
            'id_inscricao'  => $this->ObjInscricao->getNumregInscricao(),
            'id_agenda'     => $IdAgenda,
            'id_pessoa'     => $this->ObjInscricao->getDadosInscricao('id_pessoa'),
            'id_curso'      => $this->ObjInscricao->getDadosInscricao('id_curso'),
            'id_parte'      => $ArModulo['id_parte'],
            'id_modulo'     => $ArModulo['numreg'],
            'id_local_curso'=> $ArAgenda['id_local_curso'],
            'dt_inscricao'  => $this->ObjInscricao->getDadosInscricao('dt_inscricao'),
            'hr_inscricao'  => $this->ObjInscricao->getDadosInscricao('hr_inscricao')
        );
        $SqlInsert = AutoExecuteSql(TipoBancoDados, 'c_coaching_inscricao_curso', $ArSqlInsert, 'INSERT');
        $QryInsert = iquery($SqlInsert);
        if($QryInsert){
            $this->NumregInscricaoCurso = $QryInsert;
            return true;
        }
        return false;
    }
    
    public function getNumregInscricaoCurso(){
        return $this->NumregInscricao;
    }
    
    private function CarregaDadosInscricaoCurso(){
        $Sql = "SELECT * FROM c_coaching_inscricao_curso WHERE numreg = '".$this->NumregInscricao."'";
        $Qry = query($Sql);
        $Ar = farray($Qry);
        foreach($Ar as $k => $v){
            if(!is_numeric($k)){
                $this->DadosInscricaoCurso[$k] = $v;
            }
        }
    }
    
    public function setDadoInscricaoCurso($IdCampo,$Valor){
        if($IdCampo){
            $this->DadosInscricaoCurso[$IdCampo] = $Valor;
            return false;
        }
        return true;
    }
    
    public function getDadosInscricaoCurso($IdCampo = NULL){
        if($IdCampo == NULL){
            return $this->DadosInscricaoCurso;
        }
        return $this->DadosInscricaoCurso[$IdCampo];
    }
    
    public function AtualizaDadosBD(){
        $ArUpdate           = $this->getDadosInscricao();
        
        $SqlUpdate = AutoExecuteSql(TipoBancoDados,'c_coaching_inscricao_curso',$ArUpdate,'UPDATE',array('numreg'));
        $QryUpdate = query($SqlUpdate);
        if($QryUpdate){
            return true;
        }
        else{
            return false;
        }
    }
}
?>