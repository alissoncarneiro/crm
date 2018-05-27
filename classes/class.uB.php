<?php

/*
 * class.uB.php (utilBasic)
 * Autor: Alex
 * 23/09/2010 14:20:00
 *
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */

class uB{

    public static function GeraStringSqlBetweenCampo($Campo1,$Campo2,$Valor){
        return "'".TrataApostrofoBD($Valor)."' BETWEEN ".$Campo1." AND ".$Campo2;
    }

    public static function Utf8DecodePost($POST){
        if(!is_array($POST)){
            return false;
        } else{
            $NovoPost = array();
            foreach($POST as $k => $v){
                $NovoPost[$k] = utf8_decode($v);
            }
            return $NovoPost;
        }
    }

    public static function Utf8EncodePost($POST){
        if(!is_array($POST)){
            return false;
        } else{
            $NovoPost = array();
            foreach($POST as $k => $v){
                $NovoPost[$k] = utf8_encode($v);
            }
            return $NovoPost;
        }
    }

    public static function UrlEncodePost($POST){
        if(!is_array($POST)){
            return false;
        } else{
            $NovoPost = array();
            foreach($POST as $k => $v){
                $NovoPost[$k] = urlencode($v);
            }
            return $NovoPost;
        }
    }

    public static function UrlDecodePost($POST){
        if(!is_array($POST)){
            return false;
        } else{
            $NovoPost = array();
            foreach($POST as $k => $v){
                $NovoPost[$k] = urldecode($v);
            }
            return $NovoPost;
        }
    }

    public static function DataEn2Br($Data,$UsaHora=true){
        if(trim($Data) == ''){
            return false;
        }
        $DataRetorno = substr($Data,8,2)."/".substr($Data,5,2)."/".substr($Data,0,4);
        if($UsaHora === true){
            $DataRetorno .= ' '.substr($Data,10,10);
        }
        return $DataRetorno;
    }

    public static function DataBr2En($Data,$UsaHora=true){
        if(trim($Data) == ''){
            return false;
        }
        $DataRetorno = substr($Data,6,4)."-".substr($Data,3,2)."-".substr($Data,0,2);
        if($UsaHora === true){
            $DataRetorno .= ' '.substr($Data,10,10);
        }
        return $DataRetorno;
    }

    public static function getProximoMaxId($Numreg){
        if(empty($Numreg)){
            return false;
        }
        $QryMax = query("SELECT max_id FROM is_max_ids WHERE numreg = $Numreg");
        $ArMax = farray($QryMax);
        query("UPDATE is_max_ids SET max_id = (max_id+1) WHERE numreg = $Numreg");
        return $ArMax['max_id'];
    }

    /**
     * Valida se a primeira data é maior que a segunda data <br> Caso a primeira data for vazia retorna false, caso a segunda data for vazia retorna true.
     * @param date $DataInicial yyyy-mm-dd
     * @param date $DataFinal yyyy-mm-dd
     * @param boolean $UsaHora
     * @return boolean
     */

    public static function VerificaSeDataInicialMaiorQueFinal($DataInicial, $DataFinal, $UsaHora=true){
        if($DataInicial == ''){
            return false;
        }
        if($DataFinal == ''){
            return true;
        }

        if($UsaHora===false){
            $DataInicial = substr($DataInicial, 0,10);
            $DataFinal   = substr($DataFinal, 0,10);
        }

        $tsDataInicial = MakeTime($DataInicial);
        $tsDataFinal   = MakeTime($DataFinal);

        return ($tsDataInicial > $tsDataFinal)?true:false;
    }

}

?>