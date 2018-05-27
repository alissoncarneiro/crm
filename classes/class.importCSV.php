<?php

class importCSV{

    private $CaminhoArquivo;
    private $TabelaDestino = NULL;
    private $ArColunas;
    private $LimparTabela = false;
    private $PossuiCabecalho = true;
    private $UsaCabecalho = true;
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
    private $QtdeRegistrosErro = 0;
    private $ArFixosInsert = array();
    private $ArFixosUpdate = array();
    private $ArDeparaChave = array();
    private $IdCampoDeparaChave;
    private $TabelaDeparaChave;
    private $MicroTimeInicio;

    function __construct(){
        $this->ArFixosInsert = array();
        $this->ArFixosUpdate = array();
        $this->MicroTimeInicio = microtime(true);
    }

    public function setTabelaDestino($TabelaDestino){
        $this->TabelaDestino = $TabelaDestino;
    }

    public function setVerificaSeExiste($Bool){
        $this->VerificaSeExiste = $Bool;
    }

    public function setPossuiCabecalho($Bool){
        $this->PossuiCabecalho = $Bool;
    }

    public function setUsaCabecalho($Bool){
        $this->UsaCabecalho = $Bool;
    }

    public function setArColunas($ArColunas){
        $this->ArColunas = $ArColunas;
    }

    public function setLimparTabela($LimparTabela){
        $this->LimparTabela = $LimparTabela;
    }

    public function setCaminhoArquivo($CaminhoArquivo){
        $this->CaminhoArquivo = $CaminhoArquivo;
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

    public function setCampoDeparaTabelaCRM($TabelaDestino){
        $this->IdCampoDeparaChave = $TabelaDestino;
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
            $na[$v] = $ArDados[$k];
        }
        return $na;
    }

    public function mostraResultado(){
        echo 'Quantidade de registros criados: '.$this->QtdeRegistrosCriados.'<br />';
        echo 'Quantidade de registros atualizados: '.$this->QtdeRegistrosAtualizados.'<br />';
        echo 'Quantidade de registros com erro: '.$this->QtdeRegistrosErro.'<br />';
        echo 'Quantidade de registros processados: '.$this->QtdeRegistrosProcessados.'<br />';
        echo 'Tempo Gasto: '.(round((microtime(true)-$this->MicroTimeInicio),2)).' segundos<br />';

    }

    public function Importa(){
        if($this->TabelaDestino == ''){
            echo 'Tabela não informada!';
            return false;
        }
        if(!file_exists($this->CaminhoArquivo)){
            echo 'Arquivo não encontrado!';
            return false;
        }
        if($this->LimparTabela == true){
            query("TRUNCATE TABLE ".$this->TabelaDestino);
        }
        if(!is_array($this->ArColunas) && $this->UsaCabecalho == false){
            echo 'Array de colunas não preenchida.';
            return false;
        }

        $ArColunas = $this->ArColunas;
        $Arquivo = fopen($this->CaminhoArquivo,"r");
        $i = 0;
        while($Linha = fgetcsv($Arquivo,filesize($this->CaminhoArquivo),";")){
           
            if($i == 0 && $this->PossuiCabecalho == true && $this->UsaCabecalho == true){
                foreach($Linha as $Indice => $Valor){
                    $ArColunas[] = $Valor;
                }
                $i++;
                continue;
            }
            elseif($i == 0 && $this->PossuiCabecalho == true){
                $i++;
                continue;
            }
            
            $ArSqlInsert = array_combine($ArColunas,$Linha);
            $ArSqlInsert = array_merge($ArSqlInsert,$this->ArFixos);

            

            foreach($this->ArDeparaChave as $k => $v){
                $ArSqlInsert[$k] = $this->getDeparaChaveValor($k,$ArSqlInsert);
            }
            
            $ArSqlInsert = $this->setValorCustom($ArSqlInsert);

            if($this->VerificaSeExiste){
                $SqlExiste = AutoExecuteSql(TipoBancoDados,$this->TabelaDestino,$ArSqlInsert,'COUNT',$this->getArChaves());
                $QryExiste = query($SqlExiste) or die($Sql.chr(10));
                $ArExiste = farray($QryExiste);
            }

            if(!$this->VerificaSeExiste || ($this->VerificaSeExiste && $ArExiste['CNT'] <= 0)){
                if($this->consideraFixos == true){
                    $ArSqlInsert = array_merge($ArSqlInsert,$this->arFixosInsert);
                }
                $ArSqlInsert = $this->setValorCustomInsert($ArSqlInsert);
                $Sql = AutoExecuteSql(TipoBancoDados,$this->TabelaDestino,$ArSqlInsert,'INSERT');
            } elseif($ArExiste['CNT'] == 1){
                if($this->consideraFixos == true){
                    $ArSqlInsert = array_merge($ArSqlInsert,$this->arFixosUpdate);
                }
                $ArSqlInsert = $this->setValorCustomUpdate($ArSqlInsert);
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