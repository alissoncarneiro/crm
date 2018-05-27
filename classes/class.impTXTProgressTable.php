<?php

/*
 * class.impTXTProgressTable
 * Autor: Alex
 * 08/11/2011 15:26:06
 */

class impTXTProgressTable{

    private $TabelaDestino = NULL;
    private $VerificaSeExiste = true;
    private $Chaves = array();
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

    /* Específico TXT */
    private $CaminhoLeitura;
    private $CaminhoMoverImportados;
    private $ArrayArquivosImportar = array();
    private $NomeArquivoLeitura;
    private $ArTratamentoFixoData = array();
    private $ArTratamentoFixoSimNao = array();
    private $ArTratamentoFixoFloat = array();

    function __construct(){
        $this->ArFixosInsert = array();
        $this->ArFixosUpdate = array();
        $this->MicroTimeInicio = microtime(true);

        $this->CaminhoLeitura = GetParam('DIR_IMP_TXT');
        $this->CaminhoMoverImportados = GetParam('DIR_MOV_TXT');
    }

    public function setVerificaSeExiste($Bool){
        $this->VerificaSeExiste = $Bool;
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

    public function getDeparaChaveValor($IdCampo, $ArDados){
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

        $SqlCRM = AutoExecuteSql(TipoBancoDados, $Tabela, $ArSelect, 'SELECT', $Chaves);
        $QryCRM = query($SqlCRM);
        $ArCRM = farray($QryCRM);
        return $ArCRM['numreg'];
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

    public function getArChaves(){
        $na = array();
        foreach($this->Chaves as $k => $v){
            $na[] = $this->ArDepara[$v];
        }
        return $na;
    }

    public function getNumImp(){
        $Sql = "SELECT COUNT(*) AS CNT FROM ".$this->TabelaOrigem;
        $Qry = odbc_exec($this->CnxOdbc, $Sql) or die($Sql);
        $Ar = odbc_fetch_array($Qry);
        return $Ar['CNT'];
    }

    private function executaDepara($ArDepara, $ArDados){
        #Executa depara para sincronizar arrays de dados
        $na = array();
        foreach($ArDepara as $k => $v){
            $na[$v] = trim($ArDados[$k]);
        }
        return $na;
    }

    public function AposIncluir($Numreg, $ArDados){
        
    }

    public function AposAlterar($ArDados){
        
    }

    public function AposIgnorar($ArDados){
        
    }

    public function AposErroSql($ArDados){
        
    }

    public function AposIncluirOuAlterar(){
        
    }

    public function ZeraContadores(){
        $this->QtdeRegistrosCriados = 0;
        $this->QtdeRegistrosAtualizados = 0;
        $this->QtdeRegistrosErro = 0;
        $this->QtdeRegistrosIgnorados = 0;
        $this->QtdeRegistrosProcessados = 0;
        $this->MicroTimeInicio = microtime(true);
    }

    public function mostraResultado(){
        echo 'Quantidade de registros criados: '.$this->QtdeRegistrosCriados.'<br />';
        echo 'Quantidade de registros atualizados: '.$this->QtdeRegistrosAtualizados.'<br />';
        echo 'Quantidade de registros com erro: '.$this->QtdeRegistrosErro.'<br />';
        echo 'Quantidade de registros ignorados: '.$this->QtdeRegistrosIgnorados.'<br />';
        echo 'Quantidade de registros processados: '.$this->QtdeRegistrosProcessados.'<br />';
        echo 'Tempo Gasto: '.(round((microtime(true) - $this->MicroTimeInicio), 2)).' segundos<br />';
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
        $SqlInsertLog = AutoExecuteSql(TipoBancoDados, 'is_log_integracao_txt_erp_datasul', $ArSqlInsertLog, 'INSERT');
        return iQuery($SqlInsertLog);
    }

    private function FinalizaLog($NumregLog){
        $ArSqlUpdateLog = array();
        $ArSqlUpdateLog['numreg'] = $NumregLog;
        $ArSqlUpdateLog['dt_fim'] = date("Y-m-d");
        $ArSqlUpdateLog['hr_fim'] = date("H:i:s");
        $ArSqlUpdateLog['tempo_gasto'] = (round((microtime(true) - $this->MicroTimeInicio), 2));
        $ArSqlUpdateLog['qtde_registros_criados'] = $this->QtdeRegistrosCriados;
        $ArSqlUpdateLog['qtde_registros_atualizados'] = $this->QtdeRegistrosAtualizados;
        $ArSqlUpdateLog['qtde_registros_erro'] = $this->QtdeRegistrosErro;
        $ArSqlUpdateLog['qtde_registros_ignorados'] = $this->QtdeRegistrosIgnorados;
        $ArSqlUpdateLog['qtde_registros_processados'] = $this->QtdeRegistrosProcessados;

        $SqlInsertLog = AutoExecuteSql(TipoBancoDados, 'is_log_integracao_txt_erp_datasul', $ArSqlUpdateLog, 'UPDATE', array('numreg'));
        return iQuery($SqlInsertLog);
    }

    public function Importa(){
        $NumregLog = $this->CriaLog();
        $this->ProcuraArquivos();
        if(!$this->PossuiArquivosParaImportar()){
            $this->FinalizaLog($NumregLog);
            return false;
        }

        foreach($this->ArrayArquivosImportar as $NomeArquivoImportar){
            $CaminhoArquivoImportar = $this->CaminhoLeitura.$NomeArquivoImportar;

            chmod($CaminhoArquivoImportar, 0777);

            $ContadorLinha = 0;

            $fpArquivo = fopen($CaminhoArquivoImportar, "r");
            $fsArquivo = filesize($fpArquivo);
            $ArrayColunas = array();
            while($Linha = fgetcsv($fpArquivo, $fsArquivo, ";")){
                if($ContadorLinha == 0){
                    foreach($Linha as $Chave => $Valor){
                        $ArrayColunas[$Chave] = $Valor;
                    }
                    $ContadorLinha++;
                    continue;
                }
                $ContadorLinha++;
                $Ar = array();
                foreach($Linha as $Chave => $Valor){
                    $Ar[$ArrayColunas[$Chave]] = $Valor;
                }
                /* Fim tratamento TXT */
                $ArSqlInsertUpdate = $this->executaDepara($this->ArDepara, $Ar);
                $ArSqlInsertUpdate = array_merge($ArSqlInsertUpdate, $this->ArFixos);
                $ArSqlInsertUpdate = $this->AplicaTratamentoFixo($ArSqlInsertUpdate);

                $Ignorar = false;

                foreach($this->ArDeparaChave as $k => $v){
                    $ArSqlInsertUpdate[$k] = $this->getDeparaChaveValor($k, $ArSqlInsertUpdate);
                    if($ArSqlInsertUpdate[$k] == '' && is_int(array_search($k, $this->ArCamposObrigatorios))){
                        $Ignorar = true;
                        break;
                    }
                }

                $Ignorar = $this->IgnoraRegistroCustom($Ignorar, $ArSqlInsertUpdate);

                if($Ignorar === true){
                    $this->QtdeRegistrosIgnorados++;
                    $this->QtdeRegistrosProcessados++;
                    continue;
                }

                $ArSqlInsertUpdate = $this->setValorCustom($ArSqlInsertUpdate);

                $ArChaves = $this->getArChaves();
                $ArChaves = $this->getArChavesCustom($ArChaves, $ArSqlInsertUpdate);

                if($this->VerificaSeExiste){
                    $SqlExiste = AutoExecuteSql(TipoBancoDados, $this->TabelaDestino, $ArSqlInsertUpdate, 'COUNT', $ArChaves);
                    $QryExiste = query($SqlExiste) or die($SqlExiste.chr(10));
                    $ArExiste = farray($QryExiste);
                }
                if(!$this->VerificaSeExiste || ($this->VerificaSeExiste && $ArExiste['CNT'] <= 0)){
                    if($this->consideraFixos == true){
                        $ArSqlInsertUpdate = array_merge($ArSqlInsertUpdate, $this->arFixosInsert);
                    }
                    $ArSqlInsertUpdate = $this->setValorCustomInsert($ArSqlInsertUpdate);
                    $Sql = AutoExecuteSql(TipoBancoDados, $this->TabelaDestino, $ArSqlInsertUpdate, 'INSERT');
                    $QryImp = iquery($Sql);
                }
                elseif($ArExiste['CNT'] == 1){
                    if($this->consideraFixos == true){
                        $ArSqlInsertUpdate = array_merge($ArSqlInsertUpdate, $this->arFixosUpdate);
                    }
                    $ArSqlInsertUpdate = $this->setValorCustomUpdate($ArSqlInsertUpdate);
                    $Sql = AutoExecuteSql(TipoBancoDados, $this->TabelaDestino, $ArSqlInsertUpdate, 'UPDATE', $ArChaves);
                    $QryImp = query($Sql);
                }
                else{
                    $this->GravaLogDetalheTXT($NumregLog, '', 'Encontrado mais de um registro com as chaves específicadas', print_r($Ar, true), 'Erro');
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
                    $this->GravaLogDetalheTXT($NumregLog, $Sql, $MensagemErro, print_r($Ar, true), 'Erro');
                    $this->QtdeRegistrosErro++;
                    $this->AposErroSql($ArSqlInsertUpdate);
                }
                else{
                    if($ArExiste['CNT'] <= 0){
                        $this->QtdeRegistrosCriados++;
                        $this->AposIncluir($QryImp, $ArSqlInsertUpdate);
                    }
                    elseif($ArExiste['CNT'] == 1){
                        $this->QtdeRegistrosAtualizados++;
                        $this->AposAlterar($ArSqlInsertUpdate);
                    }
                }
            }
            fclose($fpArquivo);
            /* Movendo o arquivo */
            $Copy = copy($CaminhoArquivoImportar, $this->CaminhoMoverImportados.'IMPORTADO_'.$NomeArquivoImportar);
            if($Copy){
                unlink($CaminhoArquivoImportar);
            }
        }
        $this->FinalizaLog($NumregLog);
    }

    /* Específicos TXT */

    public function GravaLogDetalheTXT($NumregLog, $StringSql, $MensagemLog, $StringDadosErp, $Tipo){
        $ArSqlInsertDetalhe = array(
            'id_log' => $NumregLog,
            'string_sql' => $StringSql,
            'mensagem_erro' => $MensagemLog,
            'string_dados_erp' => $StringDadosErp,
            'tipo' => $Tipo);
        $ArSqlInsertDetalhe = AutoExecuteSql(TipoBancoDados, 'is_log_integracao_txt_erp_datasul_detalhe', $ArSqlInsertDetalhe, 'INSERT');
        return query($ArSqlInsertDetalhe);
    }

    public function setNomeArquivoLeitura($p){
        $this->NomeArquivoLeitura = $p;
    }

    public function setArTratamentoFixoData($p){
        $this->ArTratamentoFixoData = $p;
    }

    public function setArTratamentoFixoSimNao($p){
        $this->ArTratamentoFixoSimNao = $p;
    }

    public function setArTratamentoFixoFloat($p){
        $this->ArTratamentoFixoFloat = $p;
    }

    public function PossuiArquivosParaImportar(){
        if(count($this->ArrayArquivosImportar) < 1){
            return false;
        }
        return true;
    }

    public function getArChavesCustom($ArChaves, $ArDados){
        return $ArChaves;
    }

    public function IgnoraRegistroCustom($Ignorar, $ArDados){
        return $Ignorar;
    }

    public function ProcuraArquivos(){
        $OpenDir = opendir($this->CaminhoLeitura);
        if(!$OpenDir){
            return false;
        }
        while($Arquivo = readdir($OpenDir)){
            $ValidaArquivo = str_replace($this->NomeArquivoLeitura, '', str_replace('.txt', '', $Arquivo));
            if(is_numeric($ValidaArquivo)){
                if(substr($Arquivo, 0, strlen($this->NomeArquivoLeitura)) != $this->NomeArquivoLeitura){
                    continue;
                }
                else{
                    $this->ArrayArquivosImportar[] = $Arquivo;
                }
            }
        }
    }

    public function AplicaTratamentoFixo($Dados){
        foreach($Dados as $Coluna => $Valor){
            if(array_search($Coluna, $this->ArTratamentoFixoData) !== false){
                $Dados[$Coluna] = $this->TratamentoFixoData($Valor);
            }
            elseif(array_search($Coluna, $this->ArTratamentoFixoSimNao) !== false){
                $Dados[$Coluna] = $this->TratamentoFixoSimNao($Valor);
            }
            elseif(array_search($Coluna, $this->ArTratamentoFixoFloat) !== false){
                $Dados[$Coluna] = $this->TratamentoFixoFloat($Valor);
            }
        }
        return $Dados;
    }

    public function TratamentoFixoData($Data){
        $ArData = explode('/', $Data);
        if(strlen($ArData[2]) == 2){
            if($ArData[2] > 0 && $ArData[2] < 50){
                $ArData[2] = '20'.$ArData[2];
            }
            else{
                $ArData[2] = '19'.$ArData[2];
            }
        }
        return $ArData[2].'-'.$ArData[1].'-'.$ArData[0];
    }

    public function TratamentoFixoSimNao($SimNao){
        if($SimNao == 'yes'){
            return 1;
        }
        elseif($SimNao == 'no'){
            return 0;
        }
        return '';
    }

    public function TratamentoFixoFloat($Valor){
        $Valor = TrataFloatPost($Valor);
        return $Valor;
    }
}

?>