<?php

class csvimport {

    public $RegistrosAtualizados = 0;
    public $RegistrosInseridos = 0;
    public $Diretorio = '';
    public $TagInicial = '';
    public $ArquivosNome = array();
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
    private $ArCamposObrigatorios = array();

    public function __construct() {
        $this->Diretorio = '';
        $this->TagInicial = '';
        $this->ArFixosInsert = array();
        $this->ArFixosUpdate = array();
        $this->MicroTimeInicio = microtime(true);
    }

    public function setVerificaSeExiste($Bool) {
        $this->VerificaSeExiste = $Bool;
    }

    public function setArDepara($Ar) {
        $this->ArDepara = $Ar;
    }

    public function setCampoDepara($IdCampo) {
        $this->TabelaDeparaChave = $IdCampo;
    }

    public function setValorCustom($ArDados) {
        return $ArDados;
    }

    public function setValorCustomInsert($ArDados) {
        return $ArDados;
    }

    public function setValorCustomUpdate($ArDados) {
        return $ArDados;
    }

    public function setArFixos($Ar) {
        $this->ArFixos = $Ar;
    }

    public function setArCamposObrigatorios($ArCamposObrigatorios) {
        $this->ArCamposObrigatorios = $ArCamposObrigatorios;
    }

    public function setCampoDeparaTabelaCRM($Tabela) {
        $this->IdCampoDeparaChave = $Tabela;
    }

    public function addCampoDeparaTabelaChaveCRM($IdCampo) {
        $this->ArDeparaChave[$this->TabelaDeparaChave][$this->IdCampoDeparaChave][] = $IdCampo;
    }

    public function getDeparaChaveValor($IdCampo, $ArDados) {
        $Chaves = array();
        $Tabela = key($this->ArDeparaChave[$IdCampo]);
        foreach ($this->ArDeparaChave[$IdCampo] as $Tabela => $CamposChave) {
            foreach ($CamposChave as $Indice => $IdCampoChave) {
                if ($ArDados[$IdCampo] == '') { // Se alguma das chaves for vazia, retorna NULL
                    return NULL;
                }
                $ArSelect[$IdCampoChave] = $ArDados[$IdCampo];
                $Chaves[] = $IdCampoChave;
            }
        }

        $SqlCRM = AutoExecuteSql(TipoBancoDados, $Tabela, $ArSelect, 'SELECT', $Chaves);
        $QryCRM = query($SqlCRM);
        $ArCRM = farray($QryCRM);
        return $ArCRM['numreg'];
    }

    public function setTabelaOrigem($p) {
        $this->TabelaOrigem = $p;
    }

    public function getArDepara() {
        return $this->ArDepara;
    }

    public function setTabelaDestino($p) {
        $this->TabelaDestino = $p;
    }

    public function setChaves($p) {
        $this->Chaves = $p;
    }

    public function setCamposObrigatorios($p) {
        $this->CamposObrigatorios = $p;
    }

    public function getArChaves() {
        $na = array();
        foreach ($this->Chaves as $k => $v) {
            $na[] = $this->ArDepara[$v];
        }
        return $na;
    }

    public function getNumImp() {
        $Sql = "SELECT COUNT(*) AS CNT FROM " . $this->TabelaOrigem;
        $Qry = odbc_exec($this->CnxOdbc, $Sql) or die($Sql);
        $Ar = odbc_fetch_array($Qry);
        return $Ar['CNT'];
    }

    private function executaDepara($ArDepara, $ArDados) {
        #Executa depara para sincronizar arrays de dados
        $na = array();
        foreach ($ArDepara as $k => $v) {
            $na[$v] = trim($ArDados[$k*1]);
        }
        return $na;
    }

    public function ZeraContadores() {
        $this->QtdeRegistrosCriados = 0;
        $this->QtdeRegistrosAtualizados = 0;
        $this->QtdeRegistrosErro = 0;
        $this->QtdeRegistrosIgnorados = 0;
        $this->QtdeRegistrosProcessados = 0;
        $this->MicroTimeInicio = microtime(true);
    }

    public function mostraResultado() {
        echo 'Quantidade de registros criados: ' . $this->QtdeRegistrosCriados . '<br />';
        echo 'Quantidade de registros atualizados: ' . $this->QtdeRegistrosAtualizados . '<br />';
        echo 'Quantidade de registros com erro: ' . $this->QtdeRegistrosErro . '<br />';
        echo 'Quantidade de registros ignorados: ' . $this->QtdeRegistrosIgnorados . '<br />';
        echo 'Quantidade de registros processados: ' . $this->QtdeRegistrosProcessados . '<br />';
        echo 'Tempo Gasto: ' . (round((microtime(true) - $this->MicroTimeInicio), 2)) . ' segundos<br />';
    }

    public function Importa() {

        foreach ($this->ArquivosNome as $k => $arquivo_csv_imp) {

            $resultado_csv = '';

            if (!(is_file($this->Diretorio . $arquivo_csv_imp))) {
                continue;
            }

            $conteiner = file($this->Diretorio . $arquivo_csv_imp);
            $file = count($conteiner);
            for ($i = 0; $i < $file; $i++) {

                $resultado_csv = '';

                $Ar = explode(';', $conteiner[$i]);
                $b = count($Ar);


                $ArSqlInsert = $this->executaDepara($this->ArDepara, $Ar);
                $ArSqlInsert = array_merge($ArSqlInsert, $this->ArFixos);
                
                $Ignorar = false;
                foreach ($this->ArDeparaChave as $k => $v) {
                    $ArSqlInsert[$k] = $this->getDeparaChaveValor($k, $ArSqlInsert);
                    if ($ArSqlInsert[$k] == '' && is_int(array_search($k, $this->ArCamposObrigatorios))) {
                        $Ignorar = true;
                        break;
                    }
                }

                if ($Ignorar === true) {
                    $this->QtdeRegistrosIgnorados++;
                    $resultado_csv = 'Erro - Campos Obrig.';
                }

                $ArSqlInsert = $this->setValorCustom($ArSqlInsert);

                if ($this->VerificaSeExiste) {
                    $SqlExiste = AutoExecuteSql(TipoBancoDados, $this->TabelaDestino, $ArSqlInsert, 'COUNT', $this->getArChaves());
                    $QryExiste = query($SqlExiste) or die($SqlExiste . chr(10));
                    $ArExiste = farray($QryExiste);
                }

                if (!$this->VerificaSeExiste || ($this->VerificaSeExiste && $ArExiste['CNT'] <= 0)) {
                    if ($this->consideraFixos == true) {
                        $ArSqlInsert = array_merge($ArSqlInsert, $this->arFixosInsert);
                    }
                    $ArSqlInsert = $this->setValorCustomInsert($ArSqlInsert);
                    $Sql = AutoExecuteSql(TipoBancoDados, $this->TabelaDestino, $ArSqlInsert, 'INSERT');
                } elseif ($ArExiste['CNT'] == 1) {
                    if ($this->consideraFixos == true) {
                        $ArSqlInsert = array_merge($ArSqlInsert, $this->arFixosUpdate);
                    }
                    $ArSqlInsert = $this->setValorCustomUpdate($ArSqlInsert);
                    $Sql = AutoExecuteSql(TipoBancoDados, $this->TabelaDestino, $ArSqlInsert, 'UPDATE', $this->getArChaves());
                } else {
                    $resultado_csv = 'Erro - Duplicidade';
                }

                if (empty($resultado_csv)) {
                    $QryImp = query($Sql);
                } else {
                    $QryImp = false;
                }
                //query("delete from is_produto where id_produto_erp = '".$ArSqlInsert["id_produto_erp"]."'");
                $this->QtdeRegistrosProcessados++;
                if (!$QryImp) {
                    $this->QtdeRegistrosErro++;
                    //copy($this->Diretorio . $arquivo_csv_imp, $this->DiretorioErro . $arquivo_csv_imp);
                    //unlink($this->Diretorio . $arquivo_csv_imp);
                    if (empty($resultado_csv)) {
                        $resultado_csv = 'Erro - SQL';
                    }
                    echo $resultado_csv.'---'.$Sql;
                    pre($ArSqlInsert);

                } else {
                    if ($ArExiste['CNT'] <= 0) {
                        $this->QtdeRegistrosCriados++;
                        $resultado_csv = 'Inserido';
                    } elseif ($ArExiste['CNT'] == 1) {
                        $this->QtdeRegistrosAtualizados++;
                        $resultado_csv = 'Atualizado';
                    }
                    //copy($this->Diretorio . $arquivo_csv_imp, $this->DiretorioOK . $arquivo_csv_imp);
                    //unlink($this->Diretorio . $arquivo_csv_imp);
                }
                // Gerando LOG
                //query("insert into is_integracao_csv_log(dt_log,hr_log,id_usuario,cadastro,nome_arquivo_csv,conteudo_arquivo_csv,comando_sql,resultado_log) values ('" . date("Y-m-d") . "','" . date("H:i") . "'," . ( $_SESSION["id_usuario"] ? "'" . $_SESSION["id_usuario"] . "'" : 'NULL') . ",'" . $this->TagInicial . "','" . $arquivo_csv_imp . "','" . str_replace("'", "''", nl2br($csv_conteudo)) . "','" . str_replace("'", "''", $Sql) . "','" . $resultado_csv . "')");
            }
        }
    }

    //-----------------------------------------------------------------

    public function ProcuraArquivo() {
        $caminho_arquivos = opendir($this->Diretorio);
        while ($arquivo = readdir($caminho_arquivos)) {
            $this->ArquivosNome[] = $arquivo;
        }
        if (count($this->ArquivosNome) < 1) {
            echo '<div align="center">Nenhum arquivo encontrado';
            return false;
        } else {
            echo '<div align="center"><br /><br />Executando a integra&ccedil;&atilde;o em : ', (count($this->ArquivosNome) - 2), ' arquivo(s). Segue lista de arquivo(s) <br /><br />';
            foreach ($this->ArquivosNome as $k => $v) {
                echo $v, '<br />';
            }
            echo '<br /></div>';
        }
    }

}

?>
