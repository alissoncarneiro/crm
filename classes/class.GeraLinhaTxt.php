<?php
/*
 * class.GeraLinhaTxt.php
 * Autor: Alex
 * 16/11/2010 14:58
 * -
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
class GeraLinhaTxt{
    private $Txt;
    private $ArrayValores;

    public function  __construct($TamanhoLinha){
        for($i=0;$i<$TamanhoLinha;$i++){
            $this->Txt .= ' ';
        }
    }
    
    public function getValor($I,$T){
        return $this->ArrayValores[$I.'-'.$T][2];
    }

    public function AdicionaValor($I,$T,$Valor){
        $this->ArrayValores[$I.'-'.$T] = array($I,$T,substr($Valor,0,$T));
    }
    
    public function CriaTxt(){
        foreach($this->ArrayValores as $k => $v){
            $this->Txt = substr_replace($this->Txt,$v[2],$v[0],strlen($v[2]));
        }
        return $this->Txt;
    }
}
?>