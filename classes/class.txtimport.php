<?php

class txtimport{

    public $RegistrosAtualizados = 0;
    public $RegistrosInseridos = 0;
    protected $ArquivosNome = array();
    protected $CaminhoInicial = NULL;
    protected $CaminhoFinal = NULL;
    protected $ArrayControle = array();
    protected $ArrayValores = array();
    protected $TableName = NULL;
    protected $ArrayKey = NULL;
    protected $Ready2SQL = NULL;
    protected $ArrayDefault = NULL;
    protected $GeraNovoID = false;
    protected $NomeCampoID = NULL;
    protected $marcador_inicial = NULL;
    protected $prefixo_chave = NULL;
    public $tratamento_float = array();
    public $usa_max_ids = false;
    public $sql_max_ids = NULL;
    public $campo_max_ids = NULL;
    public $update_max_ids = NULL;
    public $tratamento_especial = array();
    public $troca_valor = array();
    public $troca_valor_fixo = array();
    public $nega_importacao = array();
    public $nega_importacao_inverso = array();
    public $trata_data = array();
    public $sim_nao = array();
    public $Getnumreg = array();
    protected $TpPessoa = 'id_tp_pessoa';

    public function __construct(){
        $mtime = microtime(); // Pega o microtime
        $mtime = explode(' ',$mtime); // Quebra o microtime
        $mtime = $mtime[1] + $mtime[0]; // Soma as partes montando um valor inteiro
        $this->marcador_inicial = $mtime;

        $sql_import = 'SELECT parametro FROM is_parametros_sistema where id_parametro = \'txtimport\'';
        $qry_import = query($sql_import) or die('Erro ao executar comando mysql Linha:'.__LINE__.' Arquivo: '.__FILE__.' Erro: ');
        $ar_import = farray($qry_import);
        $this->CaminhoInicial = $ar_import['parametro'];

        $sql_import_after = 'SELECT parametro FROM is_parametros_sistema where id_parametro = \'movetxtimport\'';
        $qry_import_after = query($sql_import_after) or die('Erro ao executar comando mysql Linha:'.__LINE__.' Arquivo: '.__FILE__.' Erro: ');
        $ar_import_after = farray($qry_import_after);
        $this->CaminhoFinal = $ar_import_after['parametro'];
    }

    public function SetArrayDefault($MyArray){
        $this->ArrayDefault = $MyArray;
    }

    public function NewFieldName($nome_novo){
        $this->GeraNovoID = true;
        $this->NomeCampoID = $nome_novo;
    }

    public function SetArrayChaves($chaves){
        if(is_array($chaves)){
            $this->ArrayKey = $chaves;
        } else{
            echo 'Valor inv&aacute;lido, favor informar array novamente. Linha: ',__LINE__,' Arquivo: ',basename(__FILE__);
        }
    }

    public function Caminhos(){
        echo $this->CaminhoInicial,'<br />',$this->CaminhoFinal,'<br />';
    }

    public function TabelaName($NomeTabela){
        $this->TableName = $NomeTabela;
    }

    public function SetPrefixoChave($prefixo){
        $this->prefixo_chave = $prefixo;
    }

    public function ProcuraArquivo($FileName){
        $caminho_arquivos = opendir($this->CaminhoInicial);
        while($arquivo = readdir($caminho_arquivos)){
            $ValidaArquivo = str_replace($FileName,'',str_replace('.txt','',$arquivo));
            if(is_numeric($ValidaArquivo)){
                if(substr($arquivo,0,strlen($FileName)) != $FileName){
                    continue;
                } else{
                    $this->ArquivosNome[] = $arquivo;
                }
            }
        }
        if(count($this->ArquivosNome) < 1){
            echo '<div align="center">Nenhum arquivo encontrado';
            return false;
        } else{
            echo '<div align="center"><br /><br />Executando a integra&ccedil;&atilde;o em : ',count($this->ArquivosNome),' arquivo(s). Segue lista de arquivo(s) <br /><br />';
            foreach($this->ArquivosNome as $k => $v){
                echo $v,'<br />';
            }
            echo '<br /></div>';
        }
    }

    public function VerificaSeExiste(){
        $executa_insert = true;
        if(count($this->tratamento_especial) > 0){
            foreach($this->tratamento_especial as $k2 => $v2){
                $new_especial_value = explode(';',$this->Ready2SQL[$v2]);
                $new_especial = str_replace('"','',$new_especial_value[0]);
                $this->Ready2SQL[$v2] = addslashes(str_replace('\\','',$new_especial));
            }
        }
        if(count($this->tratamento_float) > 0){
            foreach($this->tratamento_float as $k1 => $v1){
                $new_int_value = str_replace('.','',$this->Ready2SQL[$v1]);
                $new_int_value = str_replace(',','.',$this->Ready2SQL[$v1]);
                $this->Ready2SQL[$v1] = $new_int_value;
            }
        }
        if(count($this->troca_valor) > 0){
            foreach($this->troca_valor as $k3 => $v3){
                $v3 = str_replace("'$k3'",'\''.$this->Ready2SQL[$k3].'\'',$v3);
                $qry_novo = query($v3) or die('erro linha'.__LINE__);
                $novo_valor = farray($qry_novo);
                $this->Ready2SQL['id_pessoa'] = $novo_valor[0];
            }
        }
        if(!empty($this->Ready2SQL['id_tp_pessoa'])){
            if(trim($this->Ready2SQL['id_tp_pessoa']) < 3){
                if($this->Ready2SQL['id_tp_pessoa'] == '1'){
                    $valor_real = '2';
                } else if($this->Ready2SQL['id_tp_pessoa'] == '2'){
                    $valor_real = '1';
                }
                $this->Ready2SQL['id_tp_pessoa'] = addslashes($valor_real);
            } else{
                $this->Ready2SQL['id_tp_pessoa'] = "NULL";
            }
        }
        if(count($this->trata_data) > 0){
            foreach($this->trata_data as $k5 => $v5){
                $peace = explode('/',$this->Ready2SQL[$v5]);
                if($peace[2] > 0 && $peace[2] < 50){
                    $peace[2] = '20'.$peace[2];
                } else{
                    $peace[2] = '19'.$peace[2];
                }
                $novo_valor = $peace[2].'-'.$peace[1].'-'.$peace[0];
                $this->Ready2SQL[$v5] = $novo_valor;
            }
        }

        if(count($this->Getnumreg) > 0){
            foreach($this->Getnumreg as $k8 => $v8){
                $v8 = str_replace("'!$k8!'",'\''.$this->Ready2SQL[$k8].'\'',$v8);
                $var = 'mysql';
                if($var == 'mssql'){
                    $v8 = str_replace("\'","''",$v8);
                }
                
                $qry_novo = query($v8) or die('erro linha'.__LINE__);
                $novo_valor = farray($qry_novo);
                $this->Ready2SQL[$k8] = (empty($novo_valor[0])?NULL:$novo_valor[0]);
            }
        }

        if(count($this->troca_valor_fixo) > 0){
            foreach($this->troca_valor_fixo as $k6 => $v6){
                $this->Ready2SQL[$k6] = $this->Ready2SQL[$v6];
            }
        }
        if(count($this->nega_importacao) > 0){
            foreach($this->nega_importacao as $k4 => $v4){
                if($v4['vl_valido'] != $this->Ready2SQL[$k4]){
                    $executa_insert = false;
                }
                if($v4['importa_info'] == 0){
                    unset($this->Ready2SQL[$k4]);
                }
            }
        }
        if(count($this->nega_importacao_inverso) > 0){
            foreach($this->nega_importacao_inverso as $k4 => $v4){
                if($v4['vl_valido'] == $this->Ready2SQL[$k4]){
                    $executa_insert = false;
                }
                if($v4['importa_info'] == 0){
                    unset($this->Ready2SQL[$k4]);
                }
            }
        }
        if(count($this->sim_nao) > 0){
            foreach($this->sim_nao as $k7 => $v7){

                if(strtolower($this->Ready2SQL[$v7]) == 'yes'){
                    $this->Ready2SQL[$v7] = '1';
                } else{
                    $this->Ready2SQL[$v7] = '0';
                }
            }
        }

        $this->Ready2SQL = array_merge($this->Ready2SQL,$this->ArrayDefault);

        $ar_where = array();
        foreach($this->ArrayKey as $k => $v){
            if($this->Ready2SQL[$v] == 'NULL'){
                $executa_insert = false;
                $ar_where[] = "".$v." is ".$this->Ready2SQL[$v];
            } else if($this->Ready2SQL[$v] == NULL){
                $executa_insert = false;
                $ar_where[] = "".$v." is ".$this->Ready2SQL[$v];
            } else if(trim($this->Ready2SQL[$v]) == ''){
                $executa_insert = false;
            }else{
                $ar_where[] = "".$v." = '".$this->Ready2SQL[$v]."'";
            }
        }
        
        $sql_existe = "SELECT COUNT(*) AS CNT FROM ".$this->TableName." WHERE ".implode(' AND ',$ar_where);
        $qry_existe = query($sql_existe) or die('Erro ao executar comando mysql.<hr>'.$sql_existe.'<hr> Linha:'.__LINE__.' Arquivo: '.__FILE__.' Erro: ');
        $ar_existe = farray($qry_existe);


        /* echo '<pre>';
          print_r($this->Ready2SQL);
          echo '</pre>';

          echo $sql_existe;

          exit; */


        if($ar_existe['CNT'] >= 1){
            $this->RegistrosAtualizados++;
            if($executa_insert){
                $sql = AutoExecuteSql('mysql',$this->TableName,$this->Ready2SQL,'UPDATE',$this->ArrayKey);
            }
        } else{

            $this->RegistrosInseridos++;

            if($this->GeraNovoID){

                if($usa_max_ids){

                    if(empty($this->sql_max_ids)){

                        if(strtoupper(substr($this->sql_max_ids,0,6)) == 'SELECT'){
                            echo 'SQL de SELECT inválida.';
                            exit;
                        }

                        echo 'SQL de SELECT da MAX ID deve ser preenchido.';
                        exit;
                    } else if(empty($this->update_max_ids)){

                        if(strtoupper(substr($this->update_max_ids,0,6)) == 'UPDATE'){
                            echo 'SQL de UPDATE inválida.';
                            exit;
                        }

                        echo 'UPDATE da MAX ID deve ser preenchido';
                        exit;
                    }
                    $qry_max = query($this->sql_max_ids) or die('Erro na instrução MYSQL: ');
                    $ar_max = farray($qry_max);
                    $novachave = $this->prefixo_chave.$ar_max[$this->campo_max_ids];
                    query($this->update_max_ids) or die('Erro na atualização da MAX IDS: ');
                    $NovoCampo = array($this->NomeCampoID => $novachave);
                } else{

                    $sql_novo_id = "SELECT numreg FROM [".$this->TableName."] order by numreg desc";
                    $qry_novo_id = query($sql_novo_id) or die('Erro ao executar comando mysql Linha:'.__LINE__.' Arquivo: '.__FILE__.' Erro: ');
                    $ar_novo_id = farray($qry_novo_id);
                    $NovoCampo = array($this->NomeCampoID => $ar_novo_id['numreg'] + 1);
                }

                $this->Ready2SQL = array_merge($this->Ready2SQL,$NovoCampo);
            }

            $sql = AutoExecuteSql('mysql',$this->TableName,$this->Ready2SQL,'INSERT');
        }

        //mssql_free_result($qry_existe);
        if($executa_insert){
//            echo $sql, '<hr>';exit;
            query(RemoveAcentos($sql)) or die($sql.'<hr>Erro ao executar comando mysql <strong>Linha:</strong>'.__LINE__.' <br /><strong>Arquivo:</strong> '.__FILE__.' <br /><strong>Erro:</strong> ');
        }
    }

    public function ImportaDados($ar_DePara){

        if(is_array($ar_DePara)){

            $cont_ar_file = count($this->ArquivosNome);
            echo '<div align="center">';
            foreach($this->ArquivosNome as $mk => $mv){

                chmod($this->CaminhoInicial.$this->ArquivosNome[$mk],0777);
                $i = 0;
                $ar_txt = array();
                $this->ArrayControle = array();
		//$conteiner = file($this->CaminhoInicial.$this->ArquivosNome[$mk]);
                $arquivo = fopen($this->CaminhoInicial.$this->ArquivosNome[$mk],"r");
                $FileSize = filesize($this->CaminhoInicial.$this->ArquivosNome[$mk]);
                $file = count($conteiner);
                //for($i=0;$i < $file; $i++){
                while($ar_txt = fgetcsv($arquivo,$FileSize,";")){
                    //$ar_txt = explode(';',$conteiner[$i]);
                    //$b = count($ar_txt);
                    if($i == 0){ // CASO SEJA A PRIMERIA LINHA
                        foreach($ar_txt as $k => $v){
                            $ar_txt[$k] = trim($v);
                        }
                        $quantcampos = count($campos);
                        $key = -1;
                        foreach($ar_DePara as $k2 => $v2){
                            $key = array_search($k2,$ar_txt);
                            if($key > -1){
                                $this->ArrayControle[] = $key;
                                $key = -1;
                            }
                        }
                    } else{
                        foreach($this->ArrayControle as $k3 => $v3){
                            $this->ArrayValores[$k3] = addslashes($ar_txt[$v3]);
                        }
                        //pre($this->ArrayValores);pre($ar_DePara);exit;
                    }
                    if($i > 0){
                        $this->Ready2SQL = array_combine($ar_DePara,$this->ArrayValores);
                        $this->VerificaSeExiste();
                    }
                    $i++;
                }
                fclose($arquivo);
                $this->MoveArquivo($this->ArquivosNome[$mk]);
            }
        } else{
            echo 'Argumente deve ser passado como array para que o processo funcione normalmente.';
        }
    }

    public function ArquivosEncontrados(){
        echo '<pre>';
        print_r($this->ArquivosNome);
        echo '</pre>';
    }

    public function MoveArquivo($FileName){
        copy($this->CaminhoInicial.$FileName,$this->CaminhoFinal.'IMPORTADO_'.$FileName);
        unlink($this->CaminhoInicial.$FileName);
    }

    public function ShowResult(){
        echo 'Registros Inseridos: ',$this->RegistrosInseridos,'<br />';
        echo 'Registros Atualizados: ',$this->RegistrosAtualizados,'<br />';
        $mtime = microtime(); // Pega o microtime
        $mtime = explode(' ',$mtime); // Quebra o microtime
        $mtime = $mtime[1] + $mtime[0]; // Soma as partes montando um valor inteiro

        echo "Tempo para execu&ccedil;&atilde;o da importa&ccedil;&atilde;o na tabela [",$this->TableName,"]: <strong>",number_format($mtime - $this->marcador_inicial,6),"</strong> segundos. <br />";
    }

}

?>