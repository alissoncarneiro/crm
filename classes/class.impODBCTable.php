<?php
/*
 * class.impODBCTable.php
 * Autor: Alex
 * 08/02/2011 17:34
 * -
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
class impODBCTable{

    private $CnxOdbc = NULL;
    private $Sep;
    private $TabelaOrigem = NULL;
    private $TabelaDestino = NULL;
    private $VerificaSeExiste = true;
    private $Chaves = array();
    private $CamposObrigatorios = array();
    private $ConsideraFixos = true;
    private $ArDepara = array();
    private $ArFixos = array();
    private $QtdeRegistrosCriados = 0;
    private $QtdeRegistrosAtualizados = 0;
    private $QtdeRegistrosExcluidos = 0;
    private $QtdeRegistrosProcessados = 0;
    private $QtdeRegistrosIgnorados;
    private $QtdeRegistrosErro = 0;
    private $ArFixosInsert = array();
    private $ArFixosUpdate = array();
    private $ArDeparaChave = array();
    private $IdCampoDeparaChave;
    private $TabelaDeparaChave;
    private $MicroTimeInicio;
    private $SqlOdbcCustom;
    private $ArCamposObrigatorios = array();

    function __construct($TipoBD){
        $this->ArFixosInsert = array();
        $this->ArFixosUpdate = array();
        $this->MicroTimeInicio = microtime(true);
        $Sep = array();
        switch($TipoBD){
            case 'mysql' :
                $Sep[0] = '`';
                $Sep[1] = '`';
                $Sep[2] = "'";
                $Sep[3] = "'";
                break;
            case 'mssql' :
                $Sep[0] = '[';
                $Sep[1] = ']';
                $Sep[2] = "'";
                $Sep[3] = "'";
                break;
            case 'progress' :
                $Sep[0] = '"';
                $Sep[1] = '"';
                $Sep[2] = "'";
                $Sep[3] = "'";
                break;
            default :
                die;
        }
        $this->Sep = $Sep;
    }

    public function setSqlOdbcCustom($SqlOdbcCustom){
        $this->SqlOdbcCustom = $SqlOdbcCustom;
    }

    public function setVerificaSeExiste($Bool){
        $this->VerificaSeExiste = $Bool;
    }

    public function setCnxOdbc($Cnx){
        $this->CnxOdbc = $Cnx;
    }

    public function setArDepara($Ar){
        $this->ArDepara = $Ar;
    }

    public function setCampoDepara($IdCampo){
        $this->TabelaDeparaChave = $IdCampo;
    }

    public function setValorCustom($ArDados){
        return $ArDados;
    }

    public function setValorCustomInsert($ArDados){
        return $ArDados;
    }

    public function setValorCustomUpdate($ArDados){
        return $ArDados;
    }

    public function setArFixos($Ar){
        $this->ArFixos = $Ar;
    }

    public function setArFixosInsert($ArFixosInsert){
        $this->ArFixosInsert = $ArFixosInsert;
    }

    public function setArFixosUpdate($ArFixosUpdate){
        $this->ArFixosUpdate = $ArFixosUpdate;
    }

    public function setArCamposObrigatorios($ArCamposObrigatorios){
        $this->ArCamposObrigatorios = $ArCamposObrigatorios;
    }

    public function setCampoDeparaTabelaCRM($Tabela){
        $this->IdCampoDeparaChave = $Tabela;
    }

    public function addCampoDeparaTabelaChaveCRM($IdCampo){
        $this->ArDeparaChave[$this->TabelaDeparaChave][$this->IdCampoDeparaChave][] = $IdCampo;
    }

    public function getDeparaChaveValor($IdCampo,$ArDados){
        $Chaves = array();
        $Tabela = key($this->ArDeparaChave[$IdCampo]);
        foreach($this->ArDeparaChave[$IdCampo] as $Tabela => $CamposChave){
            foreach($CamposChave as $Indice => $IdCampoChave){
                if($ArDados[$IdCampo] == ''){ // Se alguma das chaves for vazia, retorna NULL
                    return NULL;
                }
                $ArSelect[$IdCampoChave] = $ArDados[$IdCampo];
                $Chaves[] = $IdCampoChave;
            }
        }

        $SqlCRM = AutoExecuteSql(TipoBancoDados,$Tabela,$ArSelect,'SELECT',$Chaves);
        $QryCRM = query($SqlCRM);
        $ArCRM = farray($QryCRM);
        return $ArCRM['numreg'];
    }

    public function setTabelaOrigem($p){
        $this->TabelaOrigem = $p;
    }

    public function getArDepara(){
        return $this->ArDepara;
    }

    public function setTabelaDestino($p){
        $this->TabelaDestino = $p;
    }

    public function setChaves($p){
        $this->Chaves = $p;
    }

    public function setCamposObrigatorios($p){
        $this->CamposObrigatorios = $p;
    }

    public function getArChaves(){
        $na = array();
        foreach($this->Chaves as $k => $v){
            $na[] = $this->ArDepara[$v];
        }
        return $na;
    }

    public function getNumImp(){
        $Sql = "SELECT COUNT(*) AS CNT FROM ".$Sep[0].$this->TabelaOrigem.$Sep[1];
        $Qry = odbc_exec($this->CnxOdbc,$Sql) or die($Sql);
        $Ar = odbc_fetch_array($Qry);
        return $Ar['CNT'];
    }

    private function executaDepara($ArDepara,$ArDados){
        #Executa depara para sincronizar arrays de dados
        $na = array();
        foreach($ArDepara as $k => $v){
            $na[$v] = $ArDados[$k];
        }
        return $na;
    }

    public function mostraResultado(){
        echo 'Quantidade de registros criados: '.$this->QtdeRegistrosCriados.'<br />';
        echo 'Quantidade de registros atualizados: '.$this->QtdeRegistrosAtualizados.'<br />';
        echo 'Quantidade de registros com erro: '.$this->QtdeRegistrosErro.'<br />';
        echo 'Quantidade de registros ignorados: '.$this->QtdeRegistrosIgnorados.'<br />';
        echo 'Quantidade de registros processados: '.$this->QtdeRegistrosProcessados.'<br />';
        echo 'Tempo Gasto: '.(round((microtime(true)-$this->MicroTimeInicio),2)).' segundos<br />';

    }

    public function Importa(){
        if(empty($this->SqlOdbcCustom)){
            $Sql = "SELECT ".$Sep[0].implode($Sep[1].','.$Sep[0],array_flip($this->ArDepara))." FROM ".$Sep[0].$this->TabelaOrigem.$Sep[1];
        }
        else{
            $Sql = $this->SqlOdbcCustom;
        }

        $Qry = odbc_exec($this->CnxOdbc,$Sql);

        while($Ar = odbc_fetch_array($Qry)){

            $ArSqlInsert = $this->executaDepara($this->ArDepara,$Ar);
            $ArSqlInsert = array_merge($ArSqlInsert,$this->ArFixos);

            $Ignorar = false;
            foreach($this->ArDeparaChave as $k => $v){
                $ArSqlInsert[$k] = $this->getDeparaChaveValor($k,$ArSqlInsert);
                if($ArSqlInsert[$k] == '' && is_int(array_search($k,$this->ArCamposObrigatorios))){
                    $Ignorar = true;
                    break;
                }
            }

            if($Ignorar === true){
                $this->QtdeRegistrosIgnorados++;
                $this->QtdeRegistrosProcessados++;
                continue;
            }

            $ArSqlInsert = $this->setValorCustom($ArSqlInsert);

            if($this->VerificaSeExiste){
                $SqlExiste = AutoExecuteSql(TipoBancoDados,$this->TabelaDestino,$ArSqlInsert,'COUNT',$this->getArChaves());
                $QryExiste = query($SqlExiste) or die($SqlExiste.chr(10));
                $ArExiste = farray($QryExiste);
            }

            if(!$this->VerificaSeExiste || ($this->VerificaSeExiste && $ArExiste['CNT'] <= 0)){
                if($this->consideraFixos == true){
                    $ArSqlInsert = array_merge($ArSqlInsert,$this->arFixosInsert);
                }
                $ArSqlInsert = $this->setValorCustomInsert($ArSqlInsert);
                $ArSqlInsert = array_merge($ArSqlInsert,$this->ArFixosInsert);
                $Sql = AutoExecuteSql(TipoBancoDados,$this->TabelaDestino,$ArSqlInsert,'INSERT');
            } elseif($ArExiste['CNT'] == 1){
                if($this->consideraFixos == true){
                    $ArSqlInsert = array_merge($ArSqlInsert,$this->arFixosUpdate);
                }
                $ArSqlInsert = $this->setValorCustomUpdate($ArSqlInsert);
                $ArSqlInsert = array_merge($ArSqlInsert,$this->ArFixosUpdate);
                $Sql = AutoExecuteSql(TipoBancoDados,$this->TabelaDestino,$ArSqlInsert,'UPDATE',$this->getArChaves());
            } else{
                $this->QtdeRegistrosProcessados++;
                $this->QtdeRegistrosErro++;
                continue;
            }

            $QryImp = query($Sql);
            $this->QtdeRegistrosProcessados++;
            if(!$QryImp){
                echo $Sql.chr(10);
                $this->QtdeRegistrosErro++;
            } else{
                if($ArExiste['CNT'] <= 0){
                    $this->QtdeRegistrosCriados++;
                } elseif($ArExiste['CNT'] == 1){
                    $this->QtdeRegistrosAtualizados++;
                }
            }
        }
    }

}
?>