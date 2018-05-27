<?php
class ImportacaoModeloCSV {
    public $ArCamposSistema = array();
    public $ArCamposSistemaQuebra = array();
    public $LimpaTabela;
    public $ArCamposObrigatorios = array();
    public $ArCamposDuplicados = array();
    public $ArCampoChave = array();
    public $TabelaValidacao = FALSE;
    public $TabelaImportacao;
    public $EfetuaImportacaoDireta = 0;
    public $EfetuaImportacaoSemCSV = 0;
    public $ArCampoImportacao;
    public $ArquivoCSV;
    public $QuebraArrayEm;
    public $ValoresQuebraArray = array();
    public $SnPrintrArray = 0;
    public $ArBusca;
    public $ArTroca;
    public $CampoRelatorio = NULL;
    public $ArCamposExtra = array();
    public $ArQuebraCampos = array();
    public $ArQuebraCamposExtra = array();
    public $ArQuebraCamposObrigatorio = array();
    public $CampoChaveQuebra = array();
    public $ArTabelaQuebra = array();
    private $CNTDadosInseridos = 0;
    private $CNTDadosNaoInseridos = 0;
    private $NaoInseridos = array();
    private $ArrayValores;
    private $ArrayValoresQuebra = array();
    private $Ready2SQL;
    private $SQLErro = 0;
    private $SQLOk = 0;
    private $ExibeRelatorio = array();
    private $Ready2SQLAuxiliar = array();
    private $CNTRegistrosInseridos = 0;

    public function ImportaDados(){
        if($this->EfetuaImportacaoDireta == 0 || $this->EfetuaImportacaoSemCSV == 0){
            chmod($this->ArquivoCSV,0777);
            $i = 0;
            $ar_txt = array();
            $Arquivo = fopen($this->ArquivoCSV,'r');
            $FileSize = filesize($this->ArquivoCSV);
            while($ar_txt = fgetcsv($Arquivo,$FileSize,';')){
                if($this->SnPrintrArray == 1){
                        pre($ar_txt);exit;
                }
                if($i > 0){
                    foreach($ar_txt as $k => $v){
                        $this->ArrayValores[$k] = addslashes($v);
                    }
                    if(count($this->ArTroca)>0){
                        $this->TrocaValores();
                    }
                    if(count($this->ArBusca)>0){
                        $this->BuscaValores();
                    }
                }
                $i++;
            }
            fclose($Arquivo);
            unlink($this->ArquivoCSV);
            if($this->EfetuaImportacaoDireta == 1){
                $this->ExecutaImportacao();
            }
        } else {
            $this->ExecutaImportacao();
        }
            
    }

    private function TrocaValores(){
        foreach($this->ArTroca as $k => $v){
            $this->ArrayValores[$k] = $v[$this->ArrayValores[$k]];
        }
    }
    private function BuscaValores(){
        foreach($this->ArBusca as $k => $v){
            if($this->ArrayValores[$k] != ''){
                $SqlBuscaValores = AutoExecuteSql(TipoBancoDados, $v[0], array($v[2] => $this->ArrayValores[$k]), 'SELECT', array($v[2]));
                $ArBuscaValores = farray(query($SqlBuscaValores));
                $this->ArrayValores[$k] = $ArBuscaValores[$v[1]];
            }
        }
        $this->CombinaArray();
    }

    private function CombinaArray(){
        if(count($this->ArrayValores) == count($this->ArCampoImportacao)){
            $this->Ready2SQL = array_combine($this->ArCampoImportacao, $this->ArrayValores);
            $this->EfetuaInsert($this->Ready2SQL);
        } else {
            echo 'Impossível Fazer a combinação de array pois o mesmo contém tamanhos diferentes. ArrayValores = '.count($this->ArrayValores).' <br/>ArCampoImportacao = '.count($this->ArCampoImportacao);
            pre($this->ArCampoImportacao);
        }
    }

    private function EfetuaInsert($ArSQL){
        if($this->VerificaCampoObrigatorio($ArSQL)){
            $SQL = AutoExecuteSql(TipoBancoDados, $this->TabelaImportacao, $ArSQL, 'INSERT');
            $VerficaQuery = query($SQL);

            if($VerficaQuery != '1'){
                $this->SQLErro++;
            } else {
                $this->SQLOk++;
                if($this->VerificaCampoDuplicados($ArSQL)){
                    $ArSQL['sn_importa'] = 1;
                    $SQL = AutoExecuteSql(TipoBancoDados, $this->TabelaImportacao, $ArSQL, 'UPDATE', $this->ArCampoChave);
                    query($SQL);
                } else {
                    $this->SQLOk--;
                    $this->SQLErro++;
                }
            }
        }
    }

    private function VerificaCampoObrigatorio($ArObrigatorio){
        $Validacao = true;
        foreach($this->ArCamposObrigatorios as $k => $v){
            if(trim($ArObrigatorio[$v]) == ''){
                $Validacao = false;
                $this->ExibeRelatorio[$ArObrigatorio[$this->CampoRelatorio]][0][] = $v;
            }
        }
        $ArObrigatorio = array();
        return $Validacao;
    }

    private function VerificaCampoDuplicados($ArDuplicado){
        $Validacao = true;
        foreach($this->ArCamposDuplicados as $k => $v){
            if($ArDuplicado[$v] != ''){
                $SQL = AutoExecuteSql(TipoBancoDados, $this->TabelaImportacao, $ArDuplicado, 'COUNT', array($v));
                $ArSQLDuplicado = farray(query($SQL));

                if($ArSQLDuplicado['CNT'] > 1){
                    $this->ExibeRelatorio[$ArDuplicado[$this->CampoRelatorio]][1][] = $v;
                    $Validacao = false;
                } else {
                    $SQL = AutoExecuteSql(TipoBancoDados, $this->TabelaValidacao, $ArDuplicado, 'COUNT', array($v));
                    $ArSQLDuplicado = farray(query($SQL));
                    if($ArSQLDuplicado['CNT'] > 0){
                        $this->ExibeRelatorio[$ArDuplicado[$this->CampoRelatorio]][1][] = $v;
                        $Validacao = false;
                    }
                }
            }
        }
        return $Validacao;
    }

    public function getRelatorioErro(){
        return $this->ExibeRelatorio;
        /*EXEMPLO DE RETORNOR
        foreach ($this->ExibeRelatorio as $k => $v){
            echo $k, ' - '; //PARA IMPRIMIR Nome para exibição
            echo implode(', ',$v[0]), ' - '; //Para retornar os campos em brancos separados por virgula
            echo implode(', ',$v[1]); //Para retornar os campos em duplicidade separados por virgula
        }*/
    }

    private function ExecutaImportacao(){
        $this->ArCampoImportacao['sn_importa'] = 1;
        $SQLDados = 'SELECT '.implode(', ',$this->ArCampoImportacao).' FROM '.$this->TabelaImportacao . ' WHERE sn_importa = 1';
        unset($this->ArCampoImportacao['sn_importa']);
        $QRYDados = query($SQLDados);
        while($ArSQLDados = farray($QRYDados)){
            $this->CNTRegistrosInseridos++;
            $this->Ready2SQL = array();
            $this->Ready2SQLAuxiliar = array();

            //QUEBRANDO OS INSERTS
            for($x=0;$x<$this->QuebraArrayEm;$x++){
                if($this->ValoresQuebraArray[$x+1] != ''){
                    $ValidacaoY = $this->ValoresQuebraArray[$x+1];
                } else {
                    krsort($this->ArCampoImportacao);
                    $ValidacaoY = key($this->ArCampoImportacao)+1;
                    ksort($this->ArCampoImportacao);
                }
                $ValorQuebraArray = $this->ValoresQuebraArray[$x];
                $this->ArrayValoresQuebra[$x] = array();
                for($y=$ValorQuebraArray;$y<$ValidacaoY;$y++){
                    $this->ArrayValoresQuebra[$x][$y] = $ArSQLDados[$y];
                    unset($this->ArCampoImportacao[$y]);
                }
            }
            foreach($this->ArCampoImportacao as $o => $p){
                $this->Ready2SQL[$p] = $ArSQLDados[$p];
            }
            $SQLFinal = array_merge($this->Ready2SQL,$this->ArCamposExtra);
            $QRYFINAL = AutoExecuteSql(TipoBancoDados, $this->TabelaValidacao, $SQLFinal, 'INSERT');
            $numreg = iquery($QRYFINAL);
            //pre($this->ArrayValoresQuebra);
            if($numreg !== false ){
                $this->CNTDadosInseridos++;
                foreach($this->ArrayValoresQuebra as $k => $v){
                    $FazerUniaoQuebra = array();
                    if(count($v) == count($this->ArQuebraCampos[$k])){
                        $FazerUniaoQuebra = array_combine($this->ArQuebraCampos[$k],$v);
                        $FazerUniaoQuebra[$this->CampoChaveQuebra] = $numreg;//numreg
                        foreach($this->ArQuebraCamposExtra[$k] as $y => $z){
                            $FazerUniaoQuebra[$y] = $z;
                        }
                        $InsereAuxiliar = true;
                        foreach($this->ArQuebraCamposObrigatorio[$k] as $y => $z){
                            if(trim($FazerUniaoQuebra[$z]) == ''){
                                $InsereAuxiliar = false;
                            }
                        }
                        if($InsereAuxiliar){
                            $SQLAuxiliar = AutoExecuteSql(TipoBancoDados, $this->ArTabelaQuebra[$k], $FazerUniaoQuebra, 'INSERT');
                            query($SQLAuxiliar);
                        }
                    }
                }
            } else {
                $this->CNTDadosNaoInseridos++;
                $this->NaoInseridos[] = $SQLFinal[$this->CampoRelatorio];
            }
        }
            query('truncate table '.$this->TabelaImportacao);
    }

    public function getResultadoDaImportacao(){
        $Registo['RegistrosInseridos'] = $this->CNTDadosInseridos;
        $Registo['RegistrosNaoInseridos'] = $this->CNTDadosNaoInseridos;
        $Registo['TotalDeRegistos'] = $this->CNTRegistrosInseridos;
        $Registro['NomeRegistroNaoInseridos'] = $this->NaoInseridos;
        return $Registo;
    }
}
?>