<?php
/*
* class.CalendarioData.php
* Autor: Alex
* 30/06/2011 15:49:47
*
* Log de Alterações
* yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
*/
class CalendarioData{
    private $IdCalendario;
    private $Data;
    private $DadosData = array();
    public function __construct($IdCalendario,$Data){
        $Data = substr($Data,0,10);
        $SqlData = "SELECT * FROM is_calendario_data WHERE id_calendario = ".$IdCalendario." AND data = '".$Data."'";
        $QryData = query($SqlData);
        $NumRowsData = numrows($QryData);
        if($NumRowsData != 1){
            return false;
        }
        $this->IdCalendario = $IdCalendario;
        $this->Data = $Data;
        $ArData = farray($QryData);
        foreach($ArData as $Coluna => $Valor){
            if(!is_int($Coluna)){
                $this->DadosData[$Coluna] = $Valor;
            }
        }
        return true;
    }

    public function getSnDiaUtil(){
        if($this->DadosData['sn_dia_util'] == '1'){
            return true;
        }
        return false;
    }

    /**
     *
     * @param bolean $ConsideraDataAtual Define se a data atual será considerada como dia útil
     * @return data serã retornado a proxima data útil
     */
    public function getProximoDiaUtil($ConsideraDataAtual = false){
        if($ConsideraDataAtual === true && $this->getSnDiaUtil()){
            return $this->Data;
        }
        else {
            $DataBase = $this->Data;
            while(true){
                $DataBase = date('Y-m-d',strtotime($DataBase." + 1 day"));
                $Data = new CalendarioData($this->IdCalendario, $DataBase);
                if(!$Data){
                    return false;
                }
                if($Data->getSnDiaUtil()){
                    return $DataBase;
                }
                unset($Data);
            }
        }
    }

    /**
     *
     * @param interger $ParamQtdeDias Valor numerico em dias para encontrar a futura data
     * @return date Exibe a futura data útil encontrada
     */
    public function getFuturoDiaUtil($ParamQtdeDias){
        if($ParamQtdeDias <= 0){
            return false;
        }
        $DataBase = $this->Data;
        for($i = 0; $i < $ParamQtdeDias; $i++){
            $Data = new CalendarioData($this->IdCalendario, $DataBase);
            $ProximoDiaUtil = $Data->getProximoDiaUtil();
            if(!$ProximoDiaUtil){
                return false;
            }
            $DataBase = $ProximoDiaUtil;
        }
        return $DataBase;
    }
}



include ('../conecta.php');

$Data = '2012-08-16';

$C = new CalendarioData(1, $Data);
#echo $C->getProximoDiaUtil().'<br />';
echo $C->getFuturoDiaUtil(1);
?>