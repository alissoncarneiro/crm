<?php
/*
 * class.CalculoAlocacao.php
 * Autor: Alex/Eduardo
 * 27/06/2011 12:32:52
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
class CalculoAlocacao{

    private $NumregChave;
    private $IdUsuario;
    private $HoraInicioExp;
    private $HoraInicioRef;
    private $HoraFimRef;
    private $HoraFimExp;
    private $Tabela;
    private $CampoDataInicio;
    private $CampoHoraInicio;
    private $CampoDataFim;
    private $CampoHoraFim;
    private $DataBase;
    private $HoraBase;
    
    private $LimiteDiasCalculo;

    public function __construct(){
        $this->NumregChave = 500;
        $this->DataBase = date("Y-m-d");
        $this->HoraBase = date("H:i");

        $this->HoraInicioExp = '08:30';
        $this->HoraInicioRef = '12:00';
        $this->HoraFimRef = '13:00';
        $this->HoraFimExp = '17:30';

        $this->Tabela = 'is_atividade';
        $this->CampoDataInicio = 'dt_inicio';
        $this->CampoHoraInicio = 'hr_inicio';
        $this->CampoDataFim = 'dt_prev_fim';
        $this->CampoHoraFim = 'hr_prev_fim';
        $this->CampoResponsavel = 'id_usuario_resp';
        $this->LimiteDiasCalculo = 365;
        query("DELETE FROM is_atividade_calculo_ocupacao WHERE numreg_chave = '".$this->NumregChave."'");
    }
    
    public function LimpaChache(){
        query("DELETE FROM is_atividade_calculo_ocupacao WHERE numreg_chave = '".$this->NumregChave."'");
    }

    public function setIdUsuario($IdUsuario){
        $this->IdUsuario = $IdUsuario;
    }
    
    public function setDataHoraBase($Data=NULL,$Hora=NULL){
        $this->DataBase = ($Data != NULL && $Data != '')?$Data:$this->DataBase;
        $this->HoraBase = ($Hora != NULL && $Hora != '')?$Hora:$this->HoraBase;
    }
    
    public function getHorasDisponiveis($Data,$Hora){
        $Hora = ($Hora == '')?$this->HoraBase:$Hora;
        $ArrayHorariosDisponiveis = array();
        $SqlVerifica = "SELECT * FROM is_atividade_calculo_ocupacao WHERE numreg_chave = '".$this->NumregChave."' AND id_usuario_resp = '".$this->IdUsuario."' AND data = '".$Data."' AND sn_livre = 1 AND hr_inicio >= '".$Hora."' ORDER BY hr_inicio";
        $QryVerfica = query($SqlVerifica);
        while($ArVerifica = farray($QryVerfica)){
            if(substr($ArVerifica['data'],0,10) == date("Y-m-d") && $ArVerifica['hr_inicio'] < $this->HoraBase){
                $ArVerifica['hr_inicio'] = $Hora;
            }
            $ArrayHorariosDisponiveis[] = array(
                                                'data'          => $Data,
                                                'hr_inicio'     => $ArVerifica['hr_inicio'],
                                                'hr_fim'        => $ArVerifica['hr_fim'],
                                                'qtde_minutos'    => $ArVerifica['qtde_minutos']);
        }
        if(count($ArrayHorariosDisponiveis) > 0){
            return $ArrayHorariosDisponiveis;
        }
        return false;
    }
    
    public function getHorasOcupadas($Data,$Hora){
        $Hora = ($Hora == '')?$this->HoraBase:$Hora;
        $ArrayHorariosOcupados = array();
        $SqlVerifica = "SELECT * FROM is_atividade_calculo_ocupacao WHERE numreg_chave = '".$this->NumregChave."' AND id_usuario_resp = '".$this->IdUsuario."' AND data = '".$Data."' AND sn_livre = 0 AND hr_inicio >= '".$Hora."' ORDER BY hr_inicio";
        $QryVerfica = query($SqlVerifica);
        while($ArVerifica = farray($QryVerfica)){
            $ArrayHorariosOcupados[] = array(
                                                'data'          => $Data,
                                                'hr_inicio'     => $ArVerifica['hr_inicio'],
                                                'hr_fim'        => $ArVerifica['hr_fim'],
                                                'qtde_minutos'    => $ArVerifica['qtde_minutos']);
        }
        if(count($ArrayHorariosOcupados) > 0){
            return $ArrayHorariosOcupados;
        }
        return false;
    }

    public function getProximoHorarioDisponivel($Data=NULL,$Hora=NULL){
        $Data = ($Data != NULL)?$Data:$this->DataBase;
        $Hora = ($Hora != NULL)?$Hora:$this->HoraBase;
        $Contador = 0;
        while($Contador <= $this->LimiteDiasCalculo){
            $CalendarioData = new CalendarioData(1,$Data);
            if(!$CalendarioData || !$CalendarioData->getSnDiaUtil()){
                $Data = date("Y-m-d",strtotime($Data.' + 1 day'));
                $Hora = $this->HoraInicioExp;
                $Contador++;
                continue;
            }
            $this->CalculaSaldoHorasDia($Data);
            $HorariosDisponiveis = $this->getHorasDisponiveis($Data,$Hora);
            if($HorariosDisponiveis !== false){
                return array(
                    'data' => $HorariosDisponiveis[0]['data'],
                    'hora'=> $HorariosDisponiveis[0]['hr_inicio']
                    );
            }
            $Data = date("Y-m-d",strtotime($Data.' + 1 day'));
            $Hora = $this->HoraInicioExp;
            $Contador++;
        }
        return false;
    }
    
    public function getProximoDiaLivre($Data){
        $Data = ($Data != NULL)?$Data:$this->DataBase;
        $Contador = 0;
        while($Contador <= $this->LimiteDiasCalculo){
            $CalendarioData = new CalendarioData(1,$Data);
            if(!$CalendarioData || !$CalendarioData->getSnDiaUtil()){
                $Data = date("Y-m-d",strtotime($Data.' + 1 day'));
                $Contador++;
                continue;
            }
            $SqlVerifica = "SELECT COUNT(*) AS CNT FROM ".$this->Tabela." WHERE id_situacao IN(1,2,3) AND ".$this->CampoResponsavel." = '".$this->IdUsuario."' AND NOT ".$this->CampoDataInicio." IS NULL AND NOT ".$this->CampoHoraInicio." IS NULL AND (".$this->CampoDataInicio." <= '".$Data."' AND ".$this->CampoDataFim." >= '".$Data."')";
            $QryVerifica = query($SqlVerifica);
            $ArVerifica = farray($QryVerifica);
            if($ArVerifica['CNT'] >= 1){
                $Data = date("Y-m-d",strtotime($Data.' + 1 day'));
                $Contador++;
                continue;
            }
            return $Data;
        }
    }

    public function CalculaDataTermino($Data,$Hora,$QtdeMinutos){
        $Contador = 0;
        $SaldoMinutos = $QtdeMinutos;
        while($Contador <= $this->LimiteDiasCalculo){
            $CalendarioData = new CalendarioData(1,$Data);
            if(!$CalendarioData || !$CalendarioData->getSnDiaUtil()){
                $Data = date("Y-m-d",strtotime($Data.' + 1 day'));
                $Hora = $this->HoraInicioExp;
                $Contador++;
                continue;
            }
            $this->CalculaSaldoHorasDia($Data);
            $HoraDisponiveis = $this->getHorasDisponiveis($Data,$Hora);
            if($HoraDisponiveis !== false){
                foreach($HoraDisponiveis as $Horas){
                    $SaldoMinutos -= $Horas['qtde_minutos'];
                    if($SaldoMinutos == 0){
                        $HoraFim = $Horas['hr_fim'];
                        break;
                    }
                    elseif($SaldoMinutos < 0){
                        $MtHoraFim = MakeTime($Horas['data'].' '.$Horas['hr_fim']);
                        $MtHoraFim = $MtHoraFim + ($SaldoMinutos * 60);
                        $HoraFim = date("H:i",$MtHoraFim);
                        break;
                    }
                }
                if($SaldoMinutos <= 0){
                    return array('data' => $Data, 'hora' => $HoraFim);
                }
            }
            $Data = date("Y-m-d",strtotime($Data.' + 1 day'));
            $Hora = $this->HoraInicioExp;
            $Contador++;
        }
    }
    
    public function GravaHorarioRefeicao($Data,$HoraInicio,$HoraFim){
        $ArInsert = array(
            'numreg_chave'      => $this->NumregChave,
            'sn_livre'          => 0,
            'id_usuario_resp'   => $this->IdUsuario,
            'data'              => $Data,
            'hr_inicio'         => $HoraInicio,
            'hr_fim'            => $HoraFim,
            'obs'               => 'Horário de refeição'
        );
        $ArInsert['qtde_minutos'] = $this->CalculaDiferencaEntreHora($ArInsert['hr_inicio'],$ArInsert['hr_fim']);
        $SqlInsert = AutoExecuteSql(TipoBancoDados, 'is_atividade_calculo_ocupacao', $ArInsert, 'INSERT');
        query($SqlInsert);
    }

    private function CalculaSaldoHorasDia($Data){
        query("DELETE FROM is_atividade_calculo_ocupacao WHERE numreg_chave = '".$this->NumregChave."' AND id_usuario_resp = '".$this->IdUsuario."' AND data = '".$Data."'");
        $SqlVerifica = "SELECT ".$this->CampoDataInicio.",".$this->CampoDataFim.",".$this->CampoHoraInicio.",".$this->CampoHoraFim." FROM ".$this->Tabela." WHERE id_situacao IN(1,2,3) AND ".$this->CampoResponsavel." = '".$this->IdUsuario."' AND NOT ".$this->CampoDataInicio." IS NULL AND NOT ".$this->CampoHoraInicio." IS NULL AND (".$this->CampoDataInicio." <= '".$Data."' AND ".$this->CampoDataFim." >= '".$Data."') ORDER BY ".$this->CampoDataInicio." ASC,".$this->CampoHoraInicio." ASC, ".$this->CampoDataFim." DESC";
        $QryVerifica = query($SqlVerifica);
        if(!$QryVerifica){
            return false;
        }
        while($ArVerifica = farray($QryVerifica)){
            $DataInicio = substr($ArVerifica[$this->CampoDataInicio], 0, 10);
            $DataFim = substr($ArVerifica[$this->CampoDataFim], 0, 10);
            $HoraInicio = $ArVerifica[$this->CampoHoraInicio];
            $HoraFim = $ArVerifica[$this->CampoHoraFim];

            $DataInicio = ($DataInicio == '')?$Data:$DataInicio;
            $DataFim = ($DataFim == '')?$Data:$DataFim;

            $HoraInicio = ($HoraInicio == '')?$this->HoraInicioExp:$HoraInicio;
            $HoraFim = ($HoraFim == '')?$this->HoraFimExp:$HoraFim;


            if($DataInicio < $Data && $DataFim > $Data){ /* Ignora o dia */
                $ArInsert = array(
                    'numreg_chave'      => $this->NumregChave,
                    'sn_livre'          => 0,
                    'id_usuario_resp'   => $this->IdUsuario,
                    'data'              => $Data,
                    'hr_inicio'         => $this->HoraInicioExp,
                    'hr_fim'            => $this->HoraFimExp,
                    'obs'               => 'Dia todo ocupado'
                );
                $ArInsert['qtde_minutos'] = $this->CalculaDiferencaEntreHora($ArInsert['hr_inicio'],$ArInsert['hr_fim']);
                $SqlInsert = AutoExecuteSql(TipoBancoDados, 'is_atividade_calculo_ocupacao', $ArInsert, 'INSERT');
                query($SqlInsert);
                return true;
            }
            else{
                if($Data > $DataInicio){
                    $HoraInicio = $this->HoraInicioExp;
                }
                if($Data < $DataFim){
                    $HoraFim = $this->HoraFimExp;
                }

                $ArInsert = array(
                    'numreg_chave'      => $this->NumregChave,
                    'sn_livre'          => 0,
                    'id_usuario_resp'   => $this->IdUsuario,
                    'data'              => $Data,
                    'hr_inicio'         => $HoraInicio,
                    'hr_fim'            => $HoraFim,
                    'obs'               => 'Ocupado'
                );
                $ArInsert['qtde_minutos'] = $this->CalculaDiferencaEntreHora($ArInsert['hr_inicio'],$ArInsert['hr_fim']);
                $SqlInsert = AutoExecuteSql(TipoBancoDados, 'is_atividade_calculo_ocupacao', $ArInsert, 'INSERT');
                query($SqlInsert);
            }
        }

        /* Gravando o Horário de refeição */
        $HoraInicioRef = $this->HoraInicioRef;
        $HoraFimRef = $this->HoraFimRef;
        
        $HorariosOcupados = $this->getHorasOcupadas($Data, $this->HoraInicioExp);
        $GravaHoraRefeicao = true;
        if($HorariosOcupados !== false){
            #Caso 1
            foreach($HorariosOcupados as $Horas){
                if($Horas['hr_inicio'] <= $this->HoraInicioRef && $Horas['hr_fim'] >= $this->HoraFimRef){
                    $GravaHoraRefeicao = false;
                    break;
                }
            }
            //TODO: Tratar casos em que a hora de almoço pode ser preenchida
        }
        if($GravaHoraRefeicao === true){
            $this->GravaHorarioRefeicao($Data, $HoraInicioRef, $HoraFimRef);
        }

        
        $MenorHora = $this->HoraInicioExp;
        $MaiorHora = $this->HoraInicioExp;

        $SqlOcupado = "SELECT * FROM is_atividade_calculo_ocupacao WHERE numreg_chave = '".$this->NumregChave."' AND id_usuario_resp = '".$this->IdUsuario."' AND data = '".$Data."' ORDER BY hr_inicio,hr_fim";
        $QryOcupado = query($SqlOcupado);
        while($ArOcupado = farray($QryOcupado)){
            if($MenorHora < $ArOcupado['hr_inicio']){
                $ArInsert = array(
                    'numreg_chave'      => $this->NumregChave,
                    'sn_livre'          => 1,
                    'id_usuario_resp'   => $this->IdUsuario,
                    'data'              => $Data,
                    'hr_inicio'         => $MenorHora,
                    'hr_fim'            => $ArOcupado['hr_inicio'],
                    'obs'               => 'Livre Antes'
                );
                $ArInsert['qtde_minutos'] = $this->CalculaDiferencaEntreHora($ArInsert['hr_inicio'],$ArInsert['hr_fim']);
                $SqlInsert = AutoExecuteSql(TipoBancoDados, 'is_atividade_calculo_ocupacao', $ArInsert, 'INSERT');
                query($SqlInsert);
            }
            if($MaiorHora < $ArOcupado['hr_fim']){
                $MaiorHora = $ArOcupado['hr_fim'];
            }
            $MenorHora = $ArOcupado['hr_fim'];
        }
        if($MaiorHora < $this->HoraFimExp){
            $ArInsert = array(
                'numreg_chave'      => $this->NumregChave,
                'sn_livre'          => 1,
                'id_usuario_resp'   => $this->IdUsuario,
                'data'              => $Data,
                'hr_inicio'         => $MaiorHora,
                'hr_fim'            => $this->HoraFimExp,
                'obs'               => 'Livre Depois'
            );
            $ArInsert['qtde_minutos'] = $this->CalculaDiferencaEntreHora($ArInsert['hr_inicio'],$ArInsert['hr_fim']);
            $SqlInsert = AutoExecuteSql(TipoBancoDados, 'is_atividade_calculo_ocupacao', $ArInsert, 'INSERT');
            query($SqlInsert);
        }
        return true;
    }

    private function CalculaDiferencaEntreHora($HoraInicio,$HoraFim){
        $Data = date("Y-m-d");
        $MtHoraInicio = MakeTime($Data.' '.$HoraInicio);
        $MtHoraFim = MakeTime($Data.' '.$HoraFim);

        $Diferenca  = $MtHoraFim - $MtHoraInicio;
        $Diferenca = ceil($Diferenca / 60); /* Transformando de segundos para minutos */
        return $Diferenca;
    }
}
?>