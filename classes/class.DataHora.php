<?php

/*
 * class.DataHora.php
 * Autor: Alex
 * 17/03/2011 16:00
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
class DataHora{
    public static function CalculaMinutosUteisDecorridos($DataHoraInicio, $DataHoraFim = NULL, $HoraInicioExp = '08:00', $HoraFimExp = '18:00'){
        $MinutosUteis = 0;
        $DataHoraFim = ($DataHoraFim === NULL)?date("Y-m-d H:i:s"):$DataHoraFim;
        if($DataHoraInicio > $DataHoraFim){
            return false;
        }
        $DataHoraBase = $DataHoraInicio;
        $UltimaDataProcessada = substr($DataHoraBase,0,10);
        set_time_limit(60);
        $MinutosPorDiaUtil = false;
        while($DataHoraBase < $DataHoraFim){
            $DataHoraBase = date("Y-m-d H:i:s", strtotime($DataHoraBase." + 1 minute"));
            if($UltimaDataProcessada != substr($DataHoraBase, 0, 10)){
                $Data = new CalendarioData(1, substr($DataHoraBase, 0, 10));
                if(!$Data->getSnDiaUtil()){
                    $DataHoraBase = date("Y-m-d", strtotime($DataHoraBase.' + 1 day')).' '.$HoraInicioExp;
                    $UltimaDataProcessada = substr($DataHoraBase,0,10);
                    continue;
                }
            }
            $DataBase = substr($DataHoraBase, 0, 10);
            $DataInicio = substr($DataHoraInicio, 0, 10);
            $DataFim = substr($DataHoraFim, 0, 10);
            if($DataBase != $DataInicio && $DataBase != $DataFim){
                if($MinutosPorDiaUtil === false){
                    $MinutosPorDiaUtil = DataHora::CalculaMinutosUteisDecorridos('2012-01-02 '.$HoraInicioExp, '2012-01-02 '.$HoraFimExp, $HoraInicioExp, $HoraFimExp);
                }
                $DataHoraBase = date("Y-m-d", strtotime($DataHoraBase.' + 1 day')).' '.$HoraInicioExp;
                $UltimaDataProcessada = substr($DataHoraBase,0,10);
                $MinutosUteis += $MinutosPorDiaUtil;
                continue;
            }
            if(substr($DataHoraBase, 11, 5) < $HoraInicioExp){
                $DataHoraBase = substr($DataHoraBase, 0, 10).' '.$HoraInicioExp;
                continue;
            }
            elseif(substr($DataHoraBase, 11, 5) > $HoraFimExp){
                $DataHoraBase = date("Y-m-d", strtotime($DataHoraBase.' + 1 day')).' '.$HoraInicioExp;
                $DataHoraBase;
                continue;
            }
            $MinutosUteis++;
        }
        return $MinutosUteis;
    }

    public static function SomaHoras(){
        $Args = func_get_args();
        $TempoEmSegundos = 0;
        $UsaMinutos = false;
        foreach($Args as $Hora){
            $ArHora = explode(':',$Hora);
            $TempoEmSegundos += ($ArHora[0] * 3600) + ($ArHora[1] * 60);
            if($ArHora[2] != ''){
                $TempoEmSegundos += $ArHora[2];
                $UsaMinutos = true;
            }
        }
        $Horas = str_pad(floor($TempoEmSegundos / 3600),2,'0',STR_PAD_LEFT);
        $Minutos = str_pad(floor(($TempoEmSegundos % 3600) / 60),2,'0',STR_PAD_LEFT);
        $Segundos = str_pad(floor(($TempoEmSegundos % 3600) % 60),2,'0',STR_PAD_LEFT);
        return ($UsaMinutos)?$Horas.':'.$Minutos.':'.$Segundos:$Horas.':'.$Minutos;
    }
    
    public function HorasParaSegundos($Hora){
        $ArHora = explode(':',$Hora);
        $TempoEmSegundos = ($ArHora[0] * 3600) + ($ArHora[1] * 60);
        if($ArHora[2] != ''){
            $TempoEmSegundos += $ArHora[2];
        }
        return $TempoEmSegundos;
    }
    
    public function SegundosParaHoras($Segundos,$RetornoString = true){
        $Negativo = ($Segundos < 0)?'-':'';
        $Segundos = abs($Segundos);
        $Horas = str_pad(floor($Segundos / 3600),2,'0',STR_PAD_LEFT);
        $Minutos = str_pad(floor(($Segundos % 3600) / 60),2,'0',STR_PAD_LEFT);
        $Segundos = str_pad(floor(($Segundos % 3600) % 60),2,'0',STR_PAD_LEFT);
        return $Negativo.$Horas.':'.$Minutos.':'.$Segundos;
    }

    public static function MinutosParaHoras($Minutos,$RetornoString = true){
        if(!$RetornoString){
            round(($Minutos/60),1);
        }
        if($Minutos < 59){
            return '00:'.str_pad($Minutos,2,'0',STR_PAD_LEFT);
        }
        $QtdeHoras = floor($Minutos / 60);
        $QtdeMinutos = $Minutos % 60;
        return str_pad($QtdeHoras,2,'0',STR_PAD_LEFT).':'.str_pad($QtdeMinutos,2,'0',STR_PAD_LEFT);
    }

    /**
     * Calcula e retorna a diferenca entre dois horários
     * @param string $Hora1 (Hora:Minuto:Segundo)
     * @param string $Hora2 (Hora:Minuto:Segundo)
     * @param int $Retorno 1=horas | 2=minutos | 3=segundos
     * @return float
     */
    public static function CalculaDiferencaHoras($Hora1, $Hora2, $Retorno = 1){
        switch($Retorno){
            case 1: $Coeficiente = 3600;
                break;
            case 2: $Coeficiente = 60;
                break;
            case 3: $Coeficiente = 1;
                break;
        }
        $MakeTimeHora1 = make_time(date("Y-m-d").' '.$Hora1);
        $MakeTimeHora2 = make_time(date("Y-m-d").' '.$Hora2);
        return ($MakeTimeHora2 - $MakeTimeHora1) / $Coeficiente;
    }

    /**
     * Calcula a diferença de dias entre duas datas
     * @param date $Data1 YYYY-mm-dd
     * @param date $Data2 YYYY-mm-dd
     * @return int
     */
    public static function CalculaDiferencaDias($Data1, $Data2){
        $MakeTimeData1 = make_time($Data1);
        $MakeTimeData2 = make_time($Data2);
        return ceil(($MakeTimeData2 - $MakeTimeData1) / 86400);
    }

    public static function CalculaDiferencaDataHoraAtividades($Data1, $Hora1, $Data2, $Hora2, $TempoIntervalo = 0){
        $DiferencaDias = DataHora::CalculaDiferencaDias($Data1, $Data2);
        $QtdeDias = ($DiferencaDias == 0)?1:$DiferencaDias + 1;

        $DiferencaHoras = DataHora::CalculaDiferencaHoras($Hora1, $Hora2);
        $TotalHoras = $DiferencaHoras * $QtdeDias;
        $TotalHoras = $TotalHoras - ($TempoIntervalo * $QtdeDias);
        return $TotalHoras;
    }

    public static function getStringDataHora($DataHora){
        $Ano = substr($DataHora, 0, 4);
        $Mes = substr($DataHora, 5, 2);
        $Dia = substr($DataHora, 8, 2);
        $DiaSemana = date("w", strtotime($DataHora));
        $Hora = substr($DataHora, 11, 2);
        $Minuto = substr($DataHora, 14, 2);
        $Segundo = substr($DataHora, 17, 2);
        $Hora = (!empty($Hora))?$Hora:0;
        $Minuto = (!empty($Minuto))?$Minuto:0;
        $Segundo = (!empty($Segundo))?$Segundo:0;

        $ArrayRetorno = array();
        $ArrayRetorno['ano'] = $Ano;
        $ArrayRetorno['mes'] = $Mes;
        $ArrayRetorno['dia'] = $Dia;
        $ArrayRetorno['dia_semana'] = $DiaSemana;
        $ArrayRetorno['hora'] = $Hora;
        $ArrayRetorno['minuto'] = $Minuto;
        $ArrayRetorno['segundo'] = $Segundo;

        switch($DiaSemana){
            case 0:
                $ArrayRetorno['nome_dia_semana'] = 'Domingo';
                break;
            case 1:
                $ArrayRetorno['nome_dia_semana'] = 'Segunda-Feira';
                break;
            case 2:
                $ArrayRetorno['nome_dia_semana'] = 'Terça-Feira';
                break;
            case 3:
                $ArrayRetorno['nome_dia_semana'] = 'Quarta-Feira';
                break;
            case 4:
                $ArrayRetorno['nome_dia_semana'] = 'Quinta-Feira';
                break;
            case 5:
                $ArrayRetorno['nome_dia_semana'] = 'Sexta-Feira';
                break;
            case 6:
                $ArrayRetorno['nome_dia_semana'] = 'Sábado';
                break;
            default:
                $ArrayRetorno['nome_dia_semana'] = '';
                break;
        }

        switch($Mes){
            case '01':
                $ArrayRetorno['nome_mes'] = 'Janeiro';
                break;
            case '02':
                $ArrayRetorno['nome_mes'] = 'Fevereiro';
                break;
            case '03':
                $ArrayRetorno['nome_mes'] = 'Março';
                break;
            case '04':
                $ArrayRetorno['nome_mes'] = 'Abril';
                break;
            case '05':
                $ArrayRetorno['nome_mes'] = 'Maio';
                break;
            case '06':
                $ArrayRetorno['nome_mes'] = 'Junho';
                break;
            case '07':
                $ArrayRetorno['nome_mes'] = 'Julho';
                break;
            case '08':
                $ArrayRetorno['nome_mes'] = 'Agosto';
                break;
            case '09':
                $ArrayRetorno['nome_mes'] = 'Setembro';
                break;
            case '10':
                $ArrayRetorno['nome_mes'] = 'Outubro';
                break;
            case '11':
                $ArrayRetorno['nome_mes'] = 'Novembro';
                break;
            case '12':
                $ArrayRetorno['nome_mes'] = 'Dezembro';
                break;
            default:
                $ArrayRetorno['nome_mes'] = '';
                break;
        }
        return $ArrayRetorno;
    }
    
    public static function SomaDias($DataBase,$Dias){
        $Mascara = (strlen($DataBase > 10))?'Y-m-d':'Y-m-d H:i:s';
        return date($Mascara,strtotime($DataBase.' + '.$Dias.' days'));
    }
}
?>