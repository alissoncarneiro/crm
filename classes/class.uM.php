<?php
/*
 * class.uM.php (utilMath)
 * Autor: Alex
 * 23/09/2010 14:20:00
 *
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
class uM{

    /**
     * Retorna o percentual em valor de outro valor
     * @param decimal $Pct Percentual
     * @param decimal $Valor Valor para o cálculo
     * @param int $Precisao Quantidade de casas para arredondamento final
     * @param int $TipoArredondamento Por padrão o valor é arredondado, passar "1" para truncar
     * @return decimal
     */
    static function uMath_pct_de_valor($Pct,$Valor,$Precisao=NULL,$TipoArredondamento=2){
        $Calculo = ($Valor * $Pct) / 100;
        if($Precisao != NULL){
            if($TipoArredondamento == 1){
                return uM::uMath_truncar_valor($Calculo,$Precisao);
            }
            else{
                return round($Calculo,$Precisao);
            }
            
        }
        return $Calculo;
    }

    /**
     * Retorna o percentual em valor de outro valor (Cálculo Financeiro)
     * @param decimal $Pct Percentual
     * @param decimal $Valor Valor para o cálculo
     * @param int $Precisao Quantidade de casas para arredondamento final
     * @return decimal
     */
    static function uMath_pct_de_valor_financeiro($Pct,$Valor,$Precisao=NULL,$TipoArredondamento=2){
        $Calculo = (100 - $Pct) / 100;
        $Calculo = $Valor / $Calculo;
        $Calculo = $Calculo - $Valor;
        if($Precisao != NULL){
            if($TipoArredondamento == 1){
                return uM::uMath_truncar_valor($Calculo,$Precisao);
            }
            else{
                return round($Calculo,$Precisao);
            }
        }
        return $Calculo;
    }

    /**
     * Retorna o valor com a soma de um percentual sobre este mesmo valor
     * @param decimal $Pct Percentual
     * @param decimal $Valor Valor para o cálculo
     * @param int $Precisao Quantidade de casas para arredondamento final
     * @return decimal
     */
    static function uMath_vl_mais_pct($Pct,$Valor,$Precisao=NULL,$TipoArredondamento=2){
        $Calculo = $Valor + (($Valor * $Pct) / 100);
        if($Precisao != NULL){
            if($TipoArredondamento == 1){
                return uM::uMath_truncar_valor($Calculo,$Precisao);
            }
            else{
                return round($Calculo,$Precisao);
            }
        }
        return $Calculo;
    }

    /**
     * Retorna o valor com a soma de um percentual sobre este mesmo valor (Cálculo Financeiro)
     * @param decimal $Pct Percentual
     * @param decimal $Valor Valor para o cálculo
     * @param int $Precisao Quantidade de casas para arredondamento final
     * @return decimal
     */
    static function uMath_vl_mais_pct_financeiro($Pct,$Valor,$Precisao=NULL,$TipoArredondamento=2){
        $Calculo = (100 - $Pct) / 100;
        $Calculo = $Valor / $Calculo;
        if($Precisao != NULL){
            if($TipoArredondamento == 1){
                return uM::uMath_truncar_valor($Calculo,$Precisao);
            }
            else{
                return round($Calculo,$Precisao);
            }
        }
        return $Calculo;
    }

    /**
     * Retorna o valor com a subtração de um percentual sobre este mesmo valor
     * @param decimal $Pct Percentual
     * @param decimal $Valor Valor para o cálculo
     * @param int $Precisao Quantidade de casas para arredondamento final
     * @return decimal
     */
    static function uMath_vl_menos_pct($Pct,$Valor,$Precisao=NULL,$TipoArredondamento=2){
        $Calculo = $Valor - (($Valor * $Pct) / 100);
        if($Precisao != NULL){
            if($TipoArredondamento == 1){
                return uM::uMath_truncar_valor($Calculo,$Precisao);
            }
            else{
                return round($Calculo,$Precisao);
            }
        }
        return $Calculo;
    }

    /**
     * Retorna o percentual equivalente de um valor($Valor) sobre outro valor($ValorBase)
     * @param decimal $ValorBase
     * @param decimal $Valor
     * @param int $Precisao
     * @return decimal
     */
    static function uMath_pct_de_diferenca_de_valor($ValorBase,$Valor,$Precisao=NULL,$TipoArredondamento=2){
        if($Valor == 0 || empty($Valor) || $ValorBase == 0 || empty($ValorBase)){
            return 0;
        }
        $Calculo = ($Valor / $ValorBase) * 100;
        if($Precisao != NULL){
            if($TipoArredondamento == 1){
                return uM::uMath_truncar_valor($Calculo,$Precisao);
            }
            else{
                return round($Calculo,$Precisao);
            }
        }
        return $Calculo;
    }

    /**
     * Trunca um valor decimal
     * @param float $Valor
     * @param int $CasasDecimais
     * @return float
     */
    static function uMath_truncar_valor($Valor,$CasasDecimais){
        $VerificaPonto = strpos($Valor, '.');
        if($VerificaPonto === false){
            return $Valor;
        }
        $ArrayValor = explode('.',$Valor);
        if($CasasDecimais == 0){
            return $ArrayValor[0];
        }
        $ValorTruncado = $ArrayValor[0].'.'.substr($ArrayValor[1],0,$CasasDecimais);
        return $ValorTruncado;
    }

    /**
     * Arredonda ou trunca um valor de acordo com o parâmetro $TipoArredondamento, por padrão arredonda o valor (1=Truncar, 2=Arredondar).
     * @param float $Valor
     * @param int $CasasDecimais
     * @param int $TipoArredondamento
     */
    static function uMath_arredonda_trunca($Valor,$CasasDecimais,$TipoArredondamento=2){
        if($TipoArredondamento == '1'){
            return uM::uMath_truncar_valor($Valor, $CasasDecimais);
        }
        else{
            return round($Valor,$CasasDecimais);
        }
    }

}
?>