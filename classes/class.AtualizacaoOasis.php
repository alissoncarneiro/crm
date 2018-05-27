<?php
/*
 * class.AtualizacaoOasis.php
 * Autor: Alex
 * 03/01/2011 17:47:00
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
class AtualizacaoOasis{
    private $ArquivoPacote;
    private $ArquivosAtualizacaoPacote;
    private $PastaTemp;
    private $ZIPPacote;
    private $ZIPArquivosAtualizacaoPacote;

    private $Versao;
    private $Release;
    private $DataRelease;
    private $VersaoDependente;
    private $ReleaseDependente;
    private $VersaoAtual;
    private $ReleaseAtual;

    private $ArraySql = array();
    private $PastaRaizSistema;
    private $SeparadorDePastas;
    private $LogAlteracoes;
    private $Status = false;

    private $Mensagens = array();

    public function  __construct($ArquivoPacote){
        $this->SeparadorDePastas = (PHP_OS != 'Linux')?'\\':'/';
        $this->PastaRaizSistema = dirname(dirname(dirname($_SERVER['SCRIPT_FILENAME'])));

        $this->ArquivoPacote = $ArquivoPacote;
        $this->GeraPastaTemp();
        $this->ZIPPacote = new PclZip($this->ArquivoPacote);
        $this->ZIPPacote->extract($this->PastaTemp);
        if(file_exists($this->PastaTemp.'/arquivos.zip')){
            $this->ZIPArquivosAtualizacaoPacote = new PclZip($this->PastaTemp.'/arquivos.zip');
        }

        $this->loadInfoPacote();
        $this->loadReleaseVersaoAtual();
        if(!$this->ValidaPacote()){
            $this->DeletaArquivosTemporarios();
            return false;
            exit;
        }
        $this->loadSql();
        $this->BackUpArquivos();
        if($this->ExecutaSql()){
            $this->ExtraiArquivosNoSistema();
            $ArSqlInsert = array();
            $ArSqlInsert['versao']                  = $this->Versao;
            $ArSqlInsert['numero_release']          = $this->Release;
            $ArSqlInsert['dt_release']              = $this->DataRelease;
            $ArSqlInsert['dt_atualizacao']          = date("Y-m-d H:i:s");
            $ArSqlInsert['detalhes_atualizacao']    = $this->LogAlteracoes;
            $SqlInsert = AutoExecuteSql(TipoBancoDados, 'is_sistema_atualizacao', $ArSqlInsert, 'INSERT');
            query($SqlInsert);
            $this->Status = true;
            $this->addMensagem(3, 'Sistema atualizado com sucesso. Versão atual do sistema '.$this->getVersao().'/'.$this->getRelease());
        }
        else{
            return false;
        }
        $this->DeletaArquivosTemporarios();
        return true;
    }

    public function getVersao(){
        return $this->Versao;
    }

    public function getRelease(){
        return $this->Release;
    }

    public function getLogAlteracoes(){
        return $this->LogAlteracoes;
    }

    public function getStatus(){
        return $this->Status;
    }

    public function getArrayMensagens(){
        return $this->Mensagens;
    }

    public function DeletaArquivosTemporarios(){
        rmdirr($this->PastaTemp);
    }

    private function addMensagem($Tipo,$Mensagem){
        $this->Mensagens[] = array('Tipo'=> $Tipo,'Mensagem'=>$Mensagem);
    }

    private function TrataCaminho($Caminho){
        $Caminho = str_replace('\\', $this->SeparadorDePastas, $Caminho);
        $Caminho = str_replace('/', $this->SeparadorDePastas, $Caminho);
        return $Caminho;
    }

    private function GeraPastaTemp(){
        $this->PastaTemp = md5(base64_encode(rand(10,1000).rand(10,1000).rand(10,1000).rand(10,1000).rand(10,1000).rand(10,1000)));
        if(!is_dir($this->PastaTemp)){
            mkdir($this->PastaTemp,0777);
            chmodr($this->PastaTemp, 0777);
        }
    }

    public function getListaArquivos(){
        return $this->ZIPPacote->listContent();
    }

    private function ValidaPacote(){
        /* Validando se possui o arquivo info.txt */
        if(!file_exists($this->PastaTemp.'/info.txt')){
            $this->addMensagem(1, 'Arquivo info.txt não localizado.');
            return false;
        }
        if($this->ReleaseAtual > $this->Release){
            $this->addMensagem(1, 'A versão atual ('.$this->VersaoAtual.') é mais recente que a enviada ('.$this->Versao.').');
            return false;
        }

        if($this->ReleaseAtual == $this->Release){
            $this->addMensagem(1, 'Esta atualização já está instalada.');
            return false;
        }

        /* Validando se o último release instalado é o de dependencia deste pacote */
        if($this->ReleaseAtual != $this->ReleaseDependente){
            $this->addMensagem(1, 'Esta atualização depende da versão '.$this->VersaoDependente);
            return false;
        }
        return true;
    }

    public function loadInfoPacote(){
        $ArInfo = parse_ini_file($this->PastaTemp.'/info.txt',true);
        $this->Versao               = trim($ArInfo['info']['versao']);
        $this->Release              = trim($ArInfo['info']['release']);
        $this->DataRelease          = trim($ArInfo['info']['data_release']);
        $this->VersaoDependente     = trim($ArInfo['info']['versao_dependente']);
        $this->ReleaseDependente    = trim($ArInfo['info']['release_dependente']);
        $this->LogAlteracoes        = trim($ArInfo['log']['log_alteracoes']);
    }

    public function loadReleaseVersaoAtual(){
        $QryReleaseVersao = query("SELECT numero_release,versao FROM is_sistema_atualizacao ORDER BY numero_release DESC");
        $ArReleaseVersao = farray($QryReleaseVersao);
        $this->ReleaseAtual = $ArReleaseVersao['numero_release'];
        $this->VersaoAtual = $ArReleaseVersao['versao'];
    }

    /**
     * Função que carrega os comandos sql para uma variável da classe
     */
    public function loadSql(){
        $StringSql = file_get_contents($this->PastaTemp.'/'.TipoBancoDados.'.sql');
        switch(TipoBancoDados){
            case 'mysql':
                $ArrayExplodeSql = explode(";\r\n",$StringSql);
                break;
            case 'mssql':
                $ArrayExplodeSql = explode("\r\nGO\r\n",$StringSql);
                break;
            case 'pssql':
                $ArrayExplodeSql = explode(";\r\n",$StringSql);
                break;
        }

        foreach($ArrayExplodeSql as $k => $Sql){
            if(trim($Sql) == ''){
                continue;
            }
            $this->ArraySql[] = $Sql;
        }
    }

    public function BackUpArquivos(){
        $PastaZIPBackUp = $this->PastaTemp.$this->SeparadorDePastas.'BackUp_r'.$this->ReleaseDependente.'.zip';
        $ZIPBackUp = new PclZip($PastaZIPBackUp);
        $Arquivos = $this->ZIPArquivosAtualizacaoPacote->listContent();
        $PastaBackUp = $this->PastaTemp.$this->SeparadorDePastas.'BackUpArquivos_r'.$this->ReleaseDependente;
        $PastaBase = dirname(dirname(dirname($_SERVER['SCRIPT_FILENAME'])));
        if(!is_dir($PastaBackUp)){
            mkdir($PastaBackUp,0777);
            chmodr($PastaBackUp, 0777);
        }
        foreach($Arquivos as $k => $Arquivo){
            if($Arquivo['folder'] == '0'){
                $Diretorio = dirname($PastaBackUp.$this->SeparadorDePastas.$Arquivo['filename']);
                $Diretorio = $this->TrataCaminho($Diretorio);
                if(!file_exists($Diretorio)){
                    mkdir($Diretorio,0777,true);
                    chmodr($Diretorio,0777,true);
                }
                if(!file_exists('..'.$this->SeparadorDePastas.'..'.$this->SeparadorDePastas.$this->SeparadorDePastas.$Arquivo['filename'])){
                    continue;
                }
                $Copia = copy('..'.$this->SeparadorDePastas.'..'.$this->SeparadorDePastas.$this->SeparadorDePastas.$Arquivo['filename'], $PastaBackUp.$this->SeparadorDePastas.$Arquivo['filename']);
                if(!$Copia){
                    $this->addMensagem(1, 'Arquivo <em>'.$Arquivo['filename'].'</em> não pode ser copiado<br />');
                }
            }
        }
        $NomePastaRaizOasis = basename($this->PastaRaizSistema);
        $PastaMoverZIPBackUp = dirname($this->PastaRaizSistema).'/BackUpAtualizacoes/'.$NomePastaRaizOasis;
        $PastaMoverZIPBackUp = $this->TrataCaminho($PastaMoverZIPBackUp);
        $ZIPBackUp->create($PastaBackUp);
        if(!file_exists($PastaMoverZIPBackUp)){
            mkdir($PastaMoverZIPBackUp,0777,true);
            chmodr($PastaMoverZIPBackUp, 0777);
        }
        rename($PastaZIPBackUp,$PastaMoverZIPBackUp.$this->SeparadorDePastas.'BackUp_r'.$this->ReleaseDependente.'.zip');
        chmod($PastaMoverZIPBackUp.$this->SeparadorDePastas.'BackUp_r'.$this->ReleaseDependente.'.zip',0777);
        chmodr($this->PastaTemp, 0777);
    }

    public function ExecutaSql(){
        if(is_array($this->ArraySql) && count($this->ArraySql) > 0){
            #start_transaction();
            foreach($this->ArraySql as $k => $Sql){
                $Qry = query($Sql);
                if(!$Qry){
                    #rollback_transaction();
                    $this->addMensagem(1, 'Erro de SQL, o sistema não foi atualizado. SQL('.$Sql.').');
                    return false;
                }
            }
            return true;
            #if(commit_transaction()){
                #return true;
            #}
            #rollback_transaction();
            #return false;
        }
        return true;
    }

    public function ExtraiArquivosNoSistema(){
        $this->ZIPArquivosAtualizacaoPacote->extract(PCLZIP_OPT_PATH,$this->PastaRaizSistema,PCLZIP_OPT_REPLACE_NEWER);
        $Arquivos = $this->ZIPArquivosAtualizacaoPacote->listContent();
        foreach($Arquivos as $k => $Arquivo){
            if($Arquivo['folder'] == '0'){
                chmodr($this->PastaRaizSistema.$this->SeparadorDePastas.$Arquivo['filename'], 0777);
            }
        }
    }
}
?>