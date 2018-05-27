<?php
/*
 * class.RegistroOasis.php
 * Autor: Alex
 * 08/12/2011 09:24:06
 */
class RegistroOasis{
    protected $Numreg;
    protected $ArDados = array();
    protected $Mensagem = array();
    protected $NomeTabela;

    public function __construct($Numreg=NULL,$NomeTabela=NULL){
        if($NomeTabela !== NULL){
            $this->NomeTabela = $NomeTabela;
        }
        $this->Numreg = $Numreg;
        if($this->Numreg !== NULL){
            if(!$this->CarregaDadosBD()){
                return false;
            }
        }
        return true;
    }
    
    public function getNumreg(){
        return $this->Numreg;
    }

    public function setMensagem($Texto){
        $this->Mensagem[] = $Texto;
    }

    public function getMensagem($RetornaEmArray=false,$Separador = ' | '){
        if($RetornaEmArray == true){
            return $this->Mensagem;
        }
        return implode($Separador,$this->Mensagem);
    }

    public function CarregaDadosBD(){
        $Sql = "SELECT * FROM ".$this->NomeTabela." WHERE numreg = '".$this->Numreg."'";
        $Qry = query($Sql);
        $Ar = farray($Qry);
        if(!$Ar){
            $this->setMensagem('Registro não encontrado!');
            return false;
        }
        foreach($Ar as $Campo => $Valor){
            if(!is_int($Campo)){
                $this->ArDados[$Campo] = $Valor;
            }
        }
        return true;
    }

    public function getDado($Coluna=NULL){
        if($Coluna == NULL){
            return $this->ArDados;
        }
        return $this->ArDados[$Coluna];

    }

    public function setDado($Coluna,$Valor){
        $this->ArDados[$Coluna] = $Valor;
        return true;        
    }

    public function GravaBD(){
        $ArSql = $this->getDado();
        if($this->Numreg == NULL){
            $Sql = AutoExecuteSql(TipoBancoDados, $this->NomeTabela, $ArSql, 'INSERT');
            $this->Numreg = iquery($Sql);
            $this->setDado('numreg', $this->Numreg);
            return $this->Numreg;
        }
        else{
            $Sql = AutoExecuteSql(TipoBancoDados, $this->NomeTabela, $ArSql, 'UPDATE', array('numreg'));
            return query($Sql);
        }
    }
}
?>