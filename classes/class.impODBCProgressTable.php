<?php
class impODBCProgressTable{

    /**
     * Conexão ODBC com a base de dados do ERP
     * @var type Resource
     */
    public $CnxOdbc = NULL;
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
    private $QtdeRegistrosIgnorados = 0;
    private $QtdeRegistrosErro = 0;
    private $ArFixosInsert = array();
    private $ArFixosUpdate = array();
    private $ArDeparaChave = array();
    private $IdCampoDeparaChave;
    private $TabelaDeparaChave;
    private $MicroTimeInicio;
    private $SqlOdbcCustom;
    private $ArCamposObrigatorios = array();

    function __construct(){
        $this->ArFixosInsert = array();
        $this->ArFixosUpdate = array();
        $this->MicroTimeInicio = microtime(true);
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
        $Sql = "SELECT COUNT(*) AS CNT FROM ".$this->TabelaOrigem;
        $Qry = odbc_exec($this->CnxOdbc,$Sql) or die($Sql);
        $Ar = odbc_fetch_array($Qry);
        return $Ar['CNT'];
    }

    private function executaDepara($ArDepara,$ArDados){
        #Executa depara para sincronizar arrays de dados
        $na = array();
        foreach($ArDepara as $k => $v){
            $na[$v] = trim($ArDados[$k]);
        }
        return $na;
    }

    public function AposIncluir($Numreg,$ArDados){}

    public function AposAlterar($ArDados){}

    public function AposIgnorar($ArDados){}

    public function AposErroSql($ArDados){}

    public function AposIncluirOuAlterar(){}

    public function ZeraContadores(){
        $this->QtdeRegistrosCriados         = 0;
        $this->QtdeRegistrosAtualizados     = 0;
        $this->QtdeRegistrosErro            = 0;
        $this->QtdeRegistrosIgnorados       = 0;
        $this->QtdeRegistrosProcessados     = 0;
        $this->MicroTimeInicio              = microtime(true);
    }

    public function mostraResultado(){
        echo 'Quantidade de registros criados: '.$this->QtdeRegistrosCriados.'<br />';
        echo 'Quantidade de registros atualizados: '.$this->QtdeRegistrosAtualizados.'<br />';
        echo 'Quantidade de registros com erro: '.$this->QtdeRegistrosErro.'<br />';
        echo 'Quantidade de registros ignorados: '.$this->QtdeRegistrosIgnorados.'<br />';
        echo 'Quantidade de registros processados: '.$this->QtdeRegistrosProcessados.'<br />';
        echo 'Tempo Gasto: '.(round((microtime(true)-$this->MicroTimeInicio),2)).' segundos<br />';
    }

    private function CriaLog(){
        $ArSqlInsertLog = array();
        $ArSqlInsertLog['dt_inicio'] = date("Y-m-d");
        $ArSqlInsertLog['hr_inicio'] = date("H:i:s");
        if($this->TabelaDestino != ''){
            $ArSqlInsertLog['nome_tabela'] = $this->TabelaDestino;
        }
        if($_SESSION['id_usuario'] != ''){
            $ArSqlInsertLog['id_usuario'] = $_SESSION['id_usuario'];
        }
        $SqlInsertLog = AutoExecuteSql(TipoBancoDados, 'is_log_integracao_odbc_erp_datasul', $ArSqlInsertLog, 'INSERT');
        return iQuery($SqlInsertLog);
    }

    private function FinalizaLog($NumregLog){
        $ArSqlUpdateLog = array();
        $ArSqlUpdateLog['numreg'] = $NumregLog;
        $ArSqlUpdateLog['dt_fim'] = date("Y-m-d");
        $ArSqlUpdateLog['hr_fim'] = date("H:i:s");
        $ArSqlUpdateLog['tempo_gasto'] = (round((microtime(true)-$this->MicroTimeInicio),2));
        $ArSqlUpdateLog['qtde_registros_criados'] = $this->QtdeRegistrosCriados;
        $ArSqlUpdateLog['qtde_registros_atualizados'] = $this->QtdeRegistrosAtualizados;
        $ArSqlUpdateLog['qtde_registros_erro'] = $this->QtdeRegistrosErro;
        $ArSqlUpdateLog['qtde_registros_ignorados'] = $this->QtdeRegistrosIgnorados;
        $ArSqlUpdateLog['qtde_registros_processados'] = $this->QtdeRegistrosProcessados;

        $SqlInsertLog = AutoExecuteSql(TipoBancoDados, 'is_log_integracao_odbc_erp_datasul', $ArSqlUpdateLog, 'UPDATE',array('numreg'));
        return iQuery($SqlInsertLog);
    }

    public function Importa(){
        $NumregLog = $this->CriaLog();
        if(empty($this->SqlOdbcCustom)){
            $Sql = "SELECT \"".implode('","',array_flip($this->ArDepara))."\" FROM ".$this->TabelaOrigem;
        }
        else{
            $Sql = $this->SqlOdbcCustom;
        }

        $Qry = odbc_exec($this->CnxOdbc,$Sql);

        while($Ar = odbc_fetch_array($Qry)){

            $ArSqlInsertUpdate = $this->executaDepara($this->ArDepara,$Ar);
            $ArSqlInsertUpdate = array_merge($ArSqlInsertUpdate,$this->ArFixos);

            $Ignorar = false;
            foreach($this->ArDeparaChave as $k => $v){
                $ArSqlInsertUpdate[$k] = $this->getDeparaChaveValor($k,$ArSqlInsertUpdate);
                if($ArSqlInsertUpdate[$k] == '' && is_int(array_search($k,$this->ArCamposObrigatorios))){
                    $Ignorar = true;
                    break;
                }
            }

            if($Ignorar === true){
                $this->QtdeRegistrosIgnorados++;
                $this->QtdeRegistrosProcessados++;
                continue;
            }

            $ArSqlInsertUpdate = $this->setValorCustom($ArSqlInsertUpdate);

            if($this->VerificaSeExiste){
                $SqlExiste = AutoExecuteSql(TipoBancoDados,$this->TabelaDestino,$ArSqlInsertUpdate,'COUNT',$this->getArChaves());
                $QryExiste = query($SqlExiste) or die($SqlExiste.chr(10));
                $ArExiste = farray($QryExiste);
            }
            if(!$this->VerificaSeExiste || ($this->VerificaSeExiste && $ArExiste['CNT'] <= 0)){
                if($this->consideraFixos == true){
                    $ArSqlInsertUpdate = array_merge($ArSqlInsertUpdate,$this->arFixosInsert);
                }
                $ArSqlInsertUpdate = $this->setValorCustomInsert($ArSqlInsertUpdate);
                $Sql = AutoExecuteSql(TipoBancoDados,$this->TabelaDestino,$ArSqlInsertUpdate,'INSERT');
                $QryImp = iquery($Sql);
            } elseif($ArExiste['CNT'] == 1){
                if($this->consideraFixos == true){
                    $ArSqlInsertUpdate = array_merge($ArSqlInsertUpdate,$this->arFixosUpdate);
                }
                $ArSqlInsertUpdate = $this->setValorCustomUpdate($ArSqlInsertUpdate);
                $Sql = AutoExecuteSql(TipoBancoDados,$this->TabelaDestino,$ArSqlInsertUpdate,'UPDATE',$this->getArChaves());
                $QryImp = query($Sql);
            } else{
                GravaLogDetalhe($NumregLog, '', 'Encontrado mais de um registro com as chaves específicadas', print_r($Ar,true), 'Erro');
                $this->QtdeRegistrosProcessados++;
                $this->QtdeRegistrosErro++;
                $this->AposIgnorar($ArSqlInsertUpdate);
                continue;
            }

            $this->QtdeRegistrosProcessados++;
            if(!$QryImp){
                if(TipoBancoDados == 'mysql'){
                    $MensagemErro = mysql_error();
                }
                elseif(TipoBancoDados == 'mssql'){
                    $MensagemErro = mssql_get_last_message();
                }
                else{
                    $MensagemErro = '';
                }
                GravaLogDetalhe($NumregLog, $Sql, $MensagemErro, print_r($Ar,true), 'Erro');
                $this->QtdeRegistrosErro++;
                $this->AposErroSql($ArSqlInsertUpdate);
            } else{
                if($ArExiste['CNT'] <= 0){
                    $this->QtdeRegistrosCriados++;
                    $this->AposIncluir($QryImp,$ArSqlInsertUpdate);
                } elseif($ArExiste['CNT'] == 1){
                    $this->QtdeRegistrosAtualizados++;
                    $this->AposAlterar($ArSqlInsertUpdate);
                }
            }
        }
        $this->FinalizaLog($NumregLog);
    }
}
?>