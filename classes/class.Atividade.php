<?php
/*
 * class.Atividade.php
 * Autor: Alex
 * 15/02/2011 18:42:00
 *
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
class Atividade{
    private $NumregAtividade;
    private $DadosAtividade;

    public function  __construct($NumregAtividade){
        if(trim($NumregAtividade) == ''){
            return false;
        }
        $this->NumregAtividade = $NumregAtividade;
        $this->CarregaDadosAtividadeDB();

    }

    public function getNumregAtividade(){
        return $this->NumregAtividade;
    }

    private function CarregaDadosAtividadeDB(){
        $QryVenda = query("SELECT * FROM is_atividade WHERE numreg = ".$this->getNumregAtividade());
        $ArVenda = farray($QryVenda);
        foreach($ArVenda as $k => $v){
            if(!is_numeric($k)){
                $this->DadosAtividade[$k] = $v;
            }
        }
    }
    
    public function getDadosAtividade($IdCampo=NULL){
        if($IdCampo == NULL){
            return $this->DadosAtividade;
        }
        return $this->DadosAtividade[$IdCampo];
    }
    
    public function isReplicavel(){
        $SqlTpAtividade = "SELECT sn_replicavel FROM is_tp_atividade WHERE numreg = '".$this->getDadosAtividade('id_tp_atividade')."'";
        $QryTpAtividade = query($SqlTpAtividade);
        $ArTpAtividade = farray($QryTpAtividade);
        if($ArTpAtividade['sn_replicavel'] == '1'){
            return true;
        }
        return false;
    }
}
?>