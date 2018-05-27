<?php
/*
 * functions.php
 * Versão 4.0
 * 30/09/2010 17:41:00
 */

function CarregaClasse($Nome,$Caminho){
    if(!class_exists($Nome)){
        require_once($Caminho);
    }
}

/**
 * Converte o valor das chaves de uma array para caracteres minúsculos 
 * @param Array $Array
 * @return Array 
 */
function ArrayKeysToLower($Array){
    foreach($Array as $k => $v){
        unset($Array[$k]);
        $Array[strtolower($k)] = $v;
    }
    return $Array;
}

/**
 * Converte o valor das chaves de uma array para caracteres maiúsculos 
 * @param Array $Array
 * @return Array 
 */
function ArrayKeysToUpper($Array){
    foreach($Array as $k => $v){
        unset($Array[$k]);
        $Array[strtoupper($k)] = $v;
    }
    return $Array;
}

/**
 * Converte os valores de uma array para caracteres minúsculos 
 * @param Array $Array
 * @return Array 
 */
function ArrayValuesToLower($Array){
    foreach($Array as $k => $v){
        unset($Array[$k]);
        $Array[$k] = strtolower($v);
    }
    return $Array;
}

/**
 * Converte os valores de uma array para caracteres maiúsculos 
 * @param Array $Array
 * @return Array 
 */
function ArrayValuesToUpper($Array){
    foreach($Array as $k => $v){
        unset($Array[$k]);
        $Array[$k] = strtoupper($v);
    }
    return $Array;
}

/**
 * Aplica uma máscara e retorna o valor desejado
 * @param type $Mascara
 * @param type $Valor
 * @return type 
 */
function MascaraNumerica($Mascara, $Valor){
    $Retorno = '';
    $k = 0;
    for($i = 0; $i <= strlen($Mascara) - 1; $i++){
        if($Mascara[$i] == '#'){
            if(isset($Valor[$k])){
                $Retorno .= $Valor[$k++];
            }
        }
        else{
            if(isset($Mascara[$i])){
                $Retorno .= $Mascara[$i];
            }
        }
    }
    return $Retorno;
}
/**
 *
 * @param string $Tabela
 * @param string $CampoChave
 * @param string $CamposDescricao
 * @param string $IdCampo
 * @param string $ValorPadrao
 * @param string $Where
 * @param string $OrderBy
 * @return string
 */
function TabelaParaCombobox($Tabela,$CampoChave,$CamposDescricao,$IdCampo,$ValorPadrao=NULL,$Where='',$OrderBy=''){
    $StringPos = strpos($CamposDescricao,',');
    $StringSaida = '<select name="'.$IdCampo.'" id="'.$IdCampo.'">';
    $SqlCombobox = "SELECT ".$CampoChave.",".$CamposDescricao." FROM ".$Tabela." ".$Where." ".$OrderBy;
    $QryCombobox = query($SqlCombobox);
    if(!$QryCombobox){
        return false;
    }
    $StringSaida .= '<option value="">--Selecione--</option>';
    while($ArCombobox = farray($QryCombobox)){
        if($StringPos !== false){
            $ArStringOpcao = array();
            $ArOpcoes = explode(',',$CamposDescricao);
            foreach($ArOpcoes as $Campo){
                $ArStringOpcao[] .= $ArCombobox[$Campo];
            }
            $StringOpcao = implode(' - ',$ArStringOpcao);
        }
        else{
            $StringOpcao = $ArCombobox[$CamposDescricao];
        }
        $Selected = ($ValorPadrao !== NULL && $ArCombobox[$CampoChave] == $ValorPadrao)?' selected="selected"':'';
        $StringSaida .= '<option value="'.$ArCombobox[$CampoChave].'"'.$Selected.'>'.$StringOpcao.'</option>';
    }
    $StringSaida .= '</select>';
    return $StringSaida;
}

/**
 * Efetua um depara de código/descrição
 * @param String $Tabela Ex: is_pessoa
 * @param Array $ArColunaDescricao Ex: array('numreg')
 * @param Array $ArChave Ex: array('cnpj_cpf' => '2321546543')
 * @return String|Array
 */
function DeparaCodigoDescricao($Tabela, $ArColunaDescricao, $ArChave){
    $SqlDepara = "SELECT ".implode(',', $ArColunaDescricao)." FROM ".$Tabela." ";
    if(count($ArChave) >= 1){
        $ArrayColunaValor = array();
        foreach($ArChave as $Coluna => $Valor){
            $ArrayColunaValor[] = $Coluna."='".TrataApostrofoBD($Valor)."'";
        }
        $SqlDepara .= " WHERE ".implode(' AND ', $ArrayColunaValor);
    }

    $QryDepara = query($SqlDepara);
    if(!$QryDepara){
        return false;
    }

    $ArDepara = farray($QryDepara);
    if(!$ArDepara){
        return false;
    }
    if(count($ArColunaDescricao) == 1){
        return $ArDepara[$ArColunaDescricao[0]];
    }
    $ArrayRetorno = array();
    foreach($ArDepara as $Chave => $Valor){
        if(!is_int($Chave)){
            $ArrayRetorno[] = $Valor;
        }
    }
    return $ArrayRetorno;
}

/**
 * Calcula a quantidade correspondente de acordo com o fator de conversão do ERP Datasul
 * @param decimal $Fator
 * @param int $Digitos
 * @return int
 */
function CalculaFatorConversaoDatasul($Fator,$Digitos){
    $Coeficiente = '1'.str_repeat('0', $Digitos);
    return round($Coeficiente/$Fator,0);
}

/**
 * Retorna uma string com options em formato html. Retorna falso caso não seja passada uma array válida de opções.
 * @param Array $ArrayOptions
 * @param String $Selected
 * @return string|boolean
 */
function Array2Options($ArrayOptions,$Selected=false){
    if(!is_array($ArrayOptions)){
        return false;
    }
    $String = '';
    foreach($ArrayOptions as $Option){
        if($Selected !== false){
            $OptionSelected = ($Selected == $Option[0])?' selected="selected"':'';
        }
        else{
            $OptionSelected = '';
        }
        $String .= '<option value="'.$Option[0].'"'.$OptionSelected.'>'.$Option[1].'</option>';
    }
    return $String;
}

function BloqueiaAcessoDireto(){
    echo 'Acesso Direto não permitido';
    exit;
}

/**
 * Separa uma data em uma array
 * @param datetime $Data
 * @param int $Indice Define qual informação da data será retornada. Padrão retorna uma array com todos os indices.
 * @return string|array Retorna uma array com os dados da data separados nesta ordem 0=Ano, 1=Mes, 2=Dia, 3=Hora, 4=Minuto e 5=Segundo
 */
function SeparaData($Data,$Indice=NULL){
    $A = array(substr($Data,8,2),substr($Data,5,2),substr($Data,0,4),substr($Data,11,2),substr($Data,14,2),substr($Data,17,2));
    return ($Indice == NULL)?$A:$A[$Indice];
}

/**
 * Realiza uma consulta na base de dados e obtem os parâmetros para envio de e-mail
 * @return array Array de dados para o envio do e-mail
 */
function getDadosParametroEmail(){
    $SqlParamEmail = "SELECT * FROM is_parametros_email";
    $QryParamEmail = query($SqlParamEmail);
    $ArParamEmail  = farray($QryParamEmail);
    return $ArParamEmail;
}

/**
 * Conecta com um banco do ERP Datasul de acordo com a tabela selecionda
 * @param array $ArrayConf parse_ini_file('conecta_odbc_erp_datasul.ini',true)
 * @param string $Tabela Nome da tabela a qual deseja utilizar, sem o prefixo "pub."
 * @return resource Conexão com o ERP Datasul
 */
function ConectaODBCErpDatasul($ArrayConf,$Tabela){
    $ArrayAlias = $ArrayConf['Alias'];
    $ArrayTabelaxAlias = $ArrayConf['TabelasxAlias'];

    if(!array_key_exists($Tabela, $ArrayTabelaxAlias)){
        return false;
    }
    $ConexaoODBC = @odbc_connect($ArrayAlias[$ArrayTabelaxAlias[$Tabela]], 'sysprogress', 'sysprogress');
    if($ConexaoODBC){
        return $ConexaoODBC;
    }
    else{
        return false;
    }
}

/**
 * Função que gera string de alert de javascript
 * @param string $str Texto que será exibido no alert
 * @param bool $tag true = coloca tag <script>, false não coloca tag <script>
 * @return string
 */
function alert($str,$tag=true){
    if($tag === true){
        return "<script> alert('$str'); </script>";
    } else{
        return "alert('$str');";
    }
}

/**
 * Insere um registro na tabela de log de integração ODBC. Retorna o numreg do registro inserido
 * @param String $TabelaCarga
 * @return int
 */
function CriaLog($TabelaCarga){
    $ArSqlInsertLog = array();
    $ArSqlInsertLog['dt_inicio'] = date("Y-m-d");
    $ArSqlInsertLog['hr_inicio'] = date("H:i:s");
    if($TabelaCarga != ''){
        $ArSqlInsertLog['nome_tabela'] = $TabelaCarga;
    }
    if($_SESSION['id_usuario'] != ''){
        $ArSqlInsertLog['id_usuario'] = $_SESSION['id_usuario'];
    }
    $SqlInsertLog = AutoExecuteSql(TipoBancoDados, 'is_log_integracao_odbc_erp_datasul', $ArSqlInsertLog, 'INSERT');
    return iQuery($SqlInsertLog);
}
/**
 * Grava um detalhe associado a um log de integração
 * @param int $NumregLog
 * @param string $StringSql
 * @param string $MensagemLog
 * @param string $StringDadosErp
 * @param string $Tipo
 * @return query()
 */
function GravaLogDetalhe($NumregLog,$StringSql,$MensagemLog,$StringDadosErp,$Tipo){
    $ArSqlInsertDetalhe = array(
        'id_log'            => $NumregLog,
        'string_sql'        => $StringSql,
        'mensagem_erro'     => $MensagemLog,
        'string_dados_erp'  => $StringDadosErp,
        'tipo'              => $Tipo);
    $ArSqlInsertDetalhe = AutoExecuteSql(TipoBancoDados, 'is_log_integracao_odbc_erp_datasul_detalhe', $ArSqlInsertDetalhe, 'INSERT');
    return query($ArSqlInsertDetalhe);
}
/**
 * Grava um detalhe associado a um log de integração
 * @param int $NumregLog
 * @param string $StringSql
 * @param string $MensagemLog
 * @param string $StringDadosErp
 * @param string $Tipo
 * @return query()
 */
function GravaLogDetalheProtheus($NumregLog,$StringSql,$MensagemLog,$StringDadosErp,$Tipo){
    $ArSqlInsertDetalhe = array(
        'id_log'            => $NumregLog,
        'string_sql'        => $StringSql,
        'mensagem_erro'     => $MensagemLog,
        'string_dados_erp'  => $StringDadosErp,
        'tipo'              => $Tipo);
    $ArSqlInsertDetalhe = AutoExecuteSql(TipoBancoDados, 'is_log_integracao_odbc_erp_protheus_detalhe', $ArSqlInsertDetalhe, 'INSERT');
    return query($ArSqlInsertDetalhe);
}
/**
 * Finaliza um registro log
 * @param int $NumregLog
 * @param float $MicroTimeInicio
 * @param int $QtdeRegistrosCriados
 * @param int $QtdeRegistrosAtualizados
 * @param int $QtdeRegistrosErro
 * @param int $QtdeRegistrosIgnorados
 * @param int $QtdeRegistrosProcessados
 * @return iQuery()
 */
function FinalizaLog($NumregLog,$MicroTimeInicio,$QtdeRegistrosCriados,$QtdeRegistrosAtualizados,$QtdeRegistrosErro,$QtdeRegistrosIgnorados,$QtdeRegistrosProcessados){
    $ArSqlUpdateLog = array();
    $ArSqlUpdateLog['numreg']                       = $NumregLog;
    $ArSqlUpdateLog['dt_fim']                       = date("Y-m-d");
    $ArSqlUpdateLog['hr_fim']                       = date("H:i:s");
    $ArSqlUpdateLog['tempo_gasto']                  = (round((microtime(true)-$MicroTimeInicio),2));
    $ArSqlUpdateLog['qtde_registros_criados']       = $QtdeRegistrosCriados;
    $ArSqlUpdateLog['qtde_registros_atualizados']   = $QtdeRegistrosAtualizados;
    $ArSqlUpdateLog['qtde_registros_erro']          = $QtdeRegistrosErro;
    $ArSqlUpdateLog['qtde_registros_ignorados']     = $QtdeRegistrosIgnorados;
    $ArSqlUpdateLog['qtde_registros_processados']   = $QtdeRegistrosProcessados;
    
    $SqlUpdateLog = AutoExecuteSql(TipoBancoDados, 'is_log_integracao_odbc_erp_datasul', $ArSqlUpdateLog, 'UPDATE',array('numreg'));
    $QryUpdateLog = query($SqlUpdateLog);
    if(!$QryUpdateLog){
        return false;
    }
    return true;
}

function historyBack($n=1,$tag=true){
    if($tag === true){
        return "<script> history.back($n); </script>";
    } else{
        return "history.back($n);";
    }
}

function historyGo($n=1,$tag=true){
    if($tag === true){
        return "<script> history.go($n); </script>";
    } else{
        return "history.go($n);";
    }
}

function windowclose($tag=true){
    if($tag === true){
        return "<script>window.close();</script>";
    } else{
        return "window.close();";
    }
}

function windowlocationhref($Url,$tag=true){
    if($tag === true){
        return "<script> window.location.href = '".$Url."'; </script>";
    } else{
        return "window.location.href = '".$Url."';";
    }
}

function getVariavelDeUrl($QueryString,$Url=false){  //we need to see if this URL is passing any GET variables
    $Retorno = array();

    if($Url == true){
        $InicioQueryString = strpos($QueryString,"?");
        if($InicioQueryString === false){
            return(false);
        }
        $InicioQueryString += 1;
        $FimQueryString = strpos($QueryString,"#",$InicioQueryString);
        if($FimQueryString){
            $QueryString = substr($QueryString,$InicioQueryString,$FimQueryString - $InicioQueryString);
        }
        else{
            $QueryString = substr($QueryString,$InicioQueryString);
        }
    }

    $ArrayVariaveis = explode("&",$QueryString);
    foreach($ArrayVariaveis as $ArrayVariavel){
        $PrimeiroDivisor = strpos($ArrayVariavel,"=");
        $Retorno[substr($ArrayVariavel,0,$PrimeiroDivisor)] = substr($ArrayVariavel,$PrimeiroDivisor + 1,strlen($ArrayVariavel));
    }
    return($Retorno);
}

function getError($cod_erro,$Retorno,$ArrayReplace=array()){
    $qry_erro = query("SELECT * FROM is_erro_sistema WHERE cod_erro = '".$cod_erro."'");
    if(numrows($qry_erro) >= 1){
        $ar_erro = farray($qry_erro);
        if(is_array($ArrayReplace) && count($ArrayReplace) > 0){
            foreach($ArrayReplace as $k => $v){
                $ar_erro['descr_usuario'] = str_replace('{R['.$k.']}',$v,$ar_erro['descr_usuario']);
                $ar_erro['descr_tec'] = str_replace('{R['.$k.']}',$v,$ar_erro['descr_tec']);
                $ar_erro['descr_tec_detalhe'] = str_replace('{R['.$k.']}',$v,$ar_erro['descr_tec_detalhe']);
            }

        }

        $arrayErros[1] = $ar_erro['descr_usuario'];
        $arrayErros[2] = $ar_erro['descr_tec'];
        $arrayErros[3] = $ar_erro['descr_tec_detalhe'];

        $arrayRetorno = array();
        $arrayErroExibir = array();
        for($i = 0; $i < strlen($Retorno); $i++){
            $arrayErroExibir[] = substr($Retorno,$i,1);
        }
        foreach($arrayErroExibir as $k => $v){
            $arrayRetorno[] = $arrayErros[$v];
        }
        return implode(' | ',$arrayRetorno);
    } else{
        return 'Erro '.$cod_erro.' não encontrado';
    }
}

/**
 *
 * @param array $Entrada
 */
function Pre($Entrada){
    echo '<pre>';
    print_r($Entrada);
    echo '</pre>';
}

/**
 * Retorna a string concatenando um <hr/>
 * @param string $String
 */
function ehr($String){
    echo $String.'<hr/>';
}

/**
 * Retorna a string concatenando um <br/>
 * @param string $String
 */
function ebr($String){
    echo $String.'<br/>';
}


function getSiglaMoedaDaTabPreco($IdTabPreco){
    if($IdTabPreco == ''){
        return false;
    }
    $QryTabPreco = query("SELECT id_moeda FROM is_tab_preco WHERE numreg = '".$IdTabPreco."'");
    $NumRowsTabPreco = numrows($QryTabPreco);
    if($NumRowsTabPreco <= 0){
        return false;
    }
    $ArTabPreco = farray($QryTabPreco);
    if($ArTabPreco['id_moeda'] == ''){
        return false;
    }

    $QryMoeda = query("SELECT sigla FROM is_moeda WHERE numreg = '".$ArTabPreco['id_moeda']."'");
    $NumRowsMoeda = numrows($QryMoeda);
    if($NumRowsMoeda <= 0){
        return false;
    }
    $ArMoeda = farray($QryMoeda);
    if($ArMoeda['sigla'] == ''){
        return false;
    }
    return $ArMoeda['sigla'];
}

function getParametrosGerais($IdParametro){
    if($IdParametro == 'RetornoErro'){
        return RetornoErro;
    }
    elseif($IdParametro == 'TipoBancoDados'){
        return TipoBancoDados;
    }
}

function getParametrosVenda($IdParametro){
    $QryParametroVenda = query("SELECT * FROM is_venda_parametro");
    $ArParametroVenda = farray($QryParametroVenda);

    return $ArParametroVenda[$IdParametro];
}

/**
 * Formata um número para exibição, porém com um quantidade mínima de decimais para arredondamento, caso as casas passem, a função retorna o valor sem os zeros a direita
 * @param float $Valor
 * @param int $Precisao
 * @param string $StrDec
 * @param string $StrMil
 * @return string
 */
function number_format_min($Valor,$Precisao=2,$StrDec=',',$StrMil='.'){
    if(strpos($Valor,'.') === false){
        return number_format($Valor,$Precisao,$StrDec,$StrMil);
    }
    $Valor = $Valor * 1;
    $Divisao = explode('.',$Valor);
    $QtdeCasas = strlen(($Divisao[1]));
    if($Precisao > $QtdeCasas){
        return number_format($Valor,$Precisao,$StrDec,$StrMil);
    }
    else{
        return number_format($Valor,$QtdeCasas,$StrDec,$StrMil);
    }
}

/**
 * Processa uma array de dados, e contrói um comando SQL de INSERT, UPDATE ou SELECT COUNT<br />
 * Exemplo de Uso:<br />
 * $ArraySql = array('id' => 10, 'campo1' => 'texto');<br />
 * $Sql = AutoExecuteSql(TipoBancoDados(mysql),'nome_da_tabela',$ArraySql,'UPDATE',array('id')); //UPDATE `nome_da_tabela` SET `id`='10',`campo1`='texto' WHERE `id`='10'
 * @param string $TipoBD mysql, mssql ou progress(No Oasis usar constante TipoBancoDados)
 * @param string $Tabela Nome da tabela
 * @param array $ArrayDados Array de dados onde a chave é o nome da coluna no banco de dados
 * @param string $TipoSQL 'INSERT','UPDATE' ou 'COUNT'
 * @param array $CamposChave Array com o nome das colunas chaves
 * @param string $Schema
 * @return string
 */
function AutoExecuteSql($TipoBD,$Tabela,$ArrayDados,$TipoSQL,$CamposChave = '',$Schema = ''){
    switch($TipoBD){
        case 'mysql' :
            $Sep = array('`','`',"'","'");
            break;
        case 'mssql' :
            $Sep = array('[',']',"'","'");
            break;
        case 'progress' :
            $Sep = array('"','"',"'","'");
            if($Schema == ''){$Schema = 'pub.';}
            break;
        default :
            die;
    }
    $ArrayCampos = array();
    $ArrayValores = array();
    $ArrayCamposValores = array();
    foreach($ArrayDados as $k => $v){
        $ArrayCampos[]          = $k;
        $Valor = (trim($v) == '' && $TipoBD != 'progress')?'NULL':$Sep[2].TrataApostrofoBD($v).$Sep[3];
        $ArrayValores[]         = $Valor;
    }
    if($TipoSQL == 'UPDATE' || $TipoSQL == 'COUNT' || $TipoSQL == 'SELECT'){
        if(count($CamposChave) == 0){
            return false;
        }
        foreach($ArrayDados as $k => $v){
            if($k == 'numreg'){continue;}
            $Valor = (trim($v) == '' && $TipoBD != 'progress')?'NULL':$Sep[2].TrataApostrofoBD($v).$Sep[3];
            $ArrayCamposValores[]   = $Sep[0].$k.$Sep[1].'='.$Valor;
        }

        $ar_where = array();
        foreach($CamposChave as $k => $v){
            if(trim($ArrayDados[$v]) == '' && $TipoBD != 'progress'){
                $ArrayWhere[] = $Sep[0].$v.$Sep[1].' IS NULL';
            }
            else{
                $ArrayWhere[] = $Sep[0].$v.$Sep[1].'='.$Sep[2].TrataApostrofoBD($ArrayDados[$v]).$Sep[3];
            }
        }
    }

    if($TipoSQL == 'INSERT' || $TipoSQL == 'INSERT_SELECT'){
        $sql = 'INSERT INTO '.$Schema.$Sep[0].$Tabela.$Sep[1].'
				('.$Sep[0].implode($Sep[1].','.$Sep[0],$ArrayCampos).$Sep[1].')
				VALUES
				('.implode(',',$ArrayValores).')
				';
        
        if($TipoSQL == 'INSERT_SELECT'){
            $sql .=  ' SELECT @@IDENTITY AS PK ';
        }
    }
    elseif($TipoSQL == 'UPDATE'){
        $sql = 'UPDATE '."\r\n\t".$Schema.$Sep[0].$Tabela.$Sep[1]."\r\n".' SET '."\r\n\t".implode(','."\r\n\t",$ArrayCamposValores)."\r\n".' WHERE '."\r\n\t".implode("\r\n".' AND '."\r\n\t",$ArrayWhere);
    }
    elseif($TipoSQL == 'COUNT'){
        $sql = 'SELECT COUNT(*) AS CNT FROM '.$Schema.$Sep[0].$Tabela.$Sep[1].' WHERE '.implode(' AND ',$ArrayWhere);
    }
    elseif($TipoSQL == 'SELECT'){
        $sql = 'SELECT * FROM '.$Schema.$Sep[0].$Tabela.$Sep[1].' WHERE '.implode(' AND ',$ArrayWhere);
    }
    else{
        return false;
    }
    return $sql;
}

function curPageURL(){
    $pageURL = 'http';
    if($_SERVER["HTTPS"] == "on"){
        $pageURL .= "s";
    }
    $pageURL .= "://";
    if($_SERVER["SERVER_PORT"] != "80"){
        $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
    } else{
        $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    }
    return $pageURL;
}

function chrbr($String){
    return str_replace(chr(10),'<br />',$String);
}

function strsadds($String){//stripslashes/addslashes
    $String = stripslashes($String);
    $String = addslashes($String);
    return $String;
}

function TrataApostrofoBD($String){
    $String = stripslashes($String);
    $String = str_replace("'","''",$String);
    return $String;
}

function TrimValorArray($Array){
    if(!is_array($Array)){
         return false;
    }

    foreach($Array as $k => $v){
        $Array[$k] = trim($v);
    }
    return $Array;

}

/**
 * Retorna o valor de cotação de uma determinda data. <br>
 * Caso a moeda passada como parâmetro seja (R$)Real, retorna 1
 * @param int $IdMoeda
 * @param int|date $DtCotacao 1=Cotação do dia, 2=Cotação do dia anterior, data no formato YYYY-mm-dd. Padrão = 2
 * @return float|bool Retorna <em>false</em> caso não encontre nenhuma cotação ou a cotação seja 0
 */
function getCotacaoBD($IdMoeda,$DtCotacao=2){
    if(getParametrosVenda('sn_usa_tab_preco_por_item') == 0){//Se não é utilizada tabela de preço por item
        return 1;
    }
    if(getParametrosVenda('sn_usa_sugestao_preco_nf') == 1 && $IdMoeda == NULL){
        return 1;
    }
    $IdMoedaReal = getParametrosVenda('id_moeda_real');
    $QryNumregMoedaReal = query("SELECT * FROM is_moeda WHERE numreg = '".$IdMoedaReal."'");
    $ArNumregMoedaReal = farray($QryNumregMoedaReal);
    if($IdMoeda == $ArNumregMoedaReal['numreg']){/* Se a moeda for 1-real retorna 1 */
        return 1;
    }
    if($DtCotacao == 1){
        $DtCotacao = date("Y-m-d");
    }
    elseif($DtCotacao == 2){
        $DtCotacao = date("Y-m-d",strtotime(date("Y-m-d").' - 1 day'));
    }
    $SqlCotacao = "SELECT vl_cotacao FROM is_cotacao WHERE id_moeda = '".$IdMoeda."' AND dt_cotacao = '".$DtCotacao."'";
    $QryCotacao = query($SqlCotacao);
    $NumRowsCotacao = numrows($QryCotacao);
    if($NumRowsCotacao == 1){
        $ArCotacao = farray($QryCotacao);
        if($ArCotacao['vl_cotacao'] <= 0){
            return false;
        }
        return $ArCotacao['vl_cotacao'];
    }
    else{
        return false;
    }
}

function CheckCPF($CPF){
    //VERIFICA SE O QUE FOI INFORMADO É NÚMERO
    if(!is_numeric($CPF)){
        return false;
    } else{
        //VERIFICA
        if(($CPF == '11111111111') || ($CPF == '22222222222') ||
                ($CPF == '33333333333') || ($CPF == '44444444444') ||
                ($CPF == '55555555555') || ($CPF == '66666666666') ||
                ($CPF == '77777777777') || ($CPF == '88888888888') ||
                ($CPF == '99999999999') || ($CPF == '00000000000')){
            return false;
        } else{
            //PEGA O DIGITO VERIFIACADOR
            $dv_informado = substr($CPF,9,2);
            for($i = 0; $i <= 8; $i++){
                $digito[$i] = substr($CPF,$i,1);
            }
            //CALCULA O VALOR DO 10º DIGITO DE VERIFICAÇÂO
            $posicao = 10;
            $soma = 0;
            for($i = 0; $i <= 8; $i++){
                $soma = $soma + $digito[$i] * $posicao;
                $posicao = $posicao - 1;
            }

            $digito[9] = $soma % 11;

            if($digito[9] < 2){
                $digito[9] = 0;
            } else{
                $digito[9] = 11 - $digito[9];
            }

            //CALCULA O VALOR DO 11º DIGITO DE VERIFICAÇÃO
            $posicao = 11;
            $soma = 0;

            for($i = 0; $i <= 9; $i++){
                $soma = $soma + $digito[$i] * $posicao;
                $posicao = $posicao - 1;
            }

            $digito[10] = $soma % 11;

            if($digito[10] < 2){
                $digito[10] = 0;
            } else{
                $digito[10] = 11 - $digito[10];
            }

            //VERIFICA SE O DV CALCULADO É IGUAL AO INFORMADO
            $dv = $digito[9] * 10 + $digito[10];
            if($dv != $dv_informado){
                return false;
            } else{
                return true;
            }
        }
    }

}

function CheckCNPJ($CNPJ){
    $CNPJ = str_pad(ereg_replace('[^0-9]','',$CNPJ),14,'0',STR_PAD_LEFT);
    if(strlen($CNPJ) != 14){
        return false;
    } else {
        if($CNPJ == '00000000000000'
                || $CNPJ == '11111111111111'
                || $CNPJ == '22222222222222'
                || $CNPJ == '33333333333333'
                || $CNPJ == '44444444444444'
                || $CNPJ == '55555555555555'
                || $CNPJ == '66666666666666'
                || $CNPJ == '77777777777777'
                || $CNPJ == '88888888888888'
                || $CNPJ == '99999999999999'
        ){
            return false;
        }
        for($t = 12; $t < 14; $t++){
            for($d = 0,$p = $t - 7,$c = 0; $c < $t; $c++){
                $d += $CNPJ{$c} * $p;
                $p = ($p < 3)?9:--$p;
            }
            $d = ((10 * $d) % 11) % 10;
            if($CNPJ{$c} != $d){
                return false;
            }
        }
        return true;
    }
}

//Função de Validação de Incrição Estadual
function CheckIE($ie,$uf){
    $InscricaoEstadual = strtoupper(trim($ie));
    $Estado = strtoupper(trim($uf));
    if($InscricaoEstadual == ''){
        return 2;
    }
    elseif($Estado == '' || strlen($Estado) != 2){
        return 3;
    }
    if($InscricaoEstadual == 'ISENTO'){
        return 1;
    }
    $InscricaoEstadual = ereg_replace("[()-./,:]","", $InscricaoEstadual);

    //Acre
    if($Estado == 'AC'){
        if(strlen($ie) != 13){
            return 0;
        } else{
            if(substr($ie,0,2) != '01'){
                return 0;
            } else{
                $b = 4;
                $soma = 0;
                for($i = 0; $i <= 10; $i++){
                    $soma += $ie[$i] * $b;
                    $b--;
                    if($b == 1){
                        $b = 9;
                    }
                }
                $dig = 11 - ($soma % 11);
                if($dig >= 10){
                    $dig = 0;
                }
                if(!($dig == $ie[11])){
                    return 0;
                } else{
                    $b = 5;
                    $soma = 0;
                    for($i = 0; $i <= 11; $i++){
                        $soma += $ie[$i] * $b;
                        $b--;
                        if($b == 1){
                            $b = 9;
                        }
                    }
                    $dig = 11 - ($soma % 11);
                    if($dig >= 10){
                        $dig = 0;
                    }

                    return ($dig == $ie[12]);
                }
            }
        }
    }
    //Alagoas
    elseif($Estado == 'AL'){

        if(strlen($ie) != 9){
            return 0;
        } else{
            if(substr($ie,0,2) != '24'){
                return 0;
            } else{
                $b = 9;
                $soma = 0;
                for($i = 0; $i <= 7; $i++){
                    $soma += $ie[$i] * $b;
                    $b--;
                }
                $soma *= 10;
                $dig = $soma - ( ( (int)($soma / 11) ) * 11 );
                if($dig == 10){
                    $dig = 0;
                }

                return ($dig == $ie[8]);
            }
        }
    }
    //Amazonas
    elseif($Estado == 'AM'){
        if(strlen($ie) != 9){
            return 0;
        } else{
            $b = 9;
            $soma = 0;
            for($i = 0; $i <= 7; $i++){
                $soma += $ie[$i] * $b;
                $b--;
            }
            if($soma <= 11){
                $dig = 11 - $soma;
            } else{
                $r = $soma % 11;
                if($r <= 1){
                    $dig = 0;
                } else{
                    $dig = 11 - $r;
                }
            }

            return ($dig == $ie[8]);
        }
    }
    //Amapá
    elseif($Estado == 'AP'){
        if(strlen($ie) != 9){
            return 0;
        } else{
            if(substr($ie,0,2) != '03'){
                return 0;
            } else{
                $i = substr($ie,0,-1);
                if(($i >= 3000001) && ($i <= 3017000)){
                    $p = 5;
                    $d = 0;
                } elseif(($i >= 3017001) && ($i <= 3019022)){
                    $p = 9;
                    $d = 1;
                } elseif($i >= 3019023){
                    $p = 0;
                    $d = 0;
                }

                $b = 9;
                $soma = $p;
                for($i = 0; $i <= 7; $i++){
                    $soma += $ie[$i] * $b;
                    $b--;
                }
                $dig = 11 - ($soma % 11);
                if($dig == 10){
                    $dig = 0;
                } elseif($dig == 11){
                    $dig = $d;
                }
                return ($dig == $ie[8]);
            }
        }
    }
    //Bahia
    elseif($Estado == 'BA'){
        if(strlen($ie) != 8){
            return 0;
        } else{

            $arr1 = array('0','1','2','3','4','5','8');
            $arr2 = array('6','7','9');

            $i = substr($ie,0,1);

            if(in_array($i,$arr1)){
                $modulo = 10;
            } elseif(in_array($i,$arr2)){
                $modulo = 11;
            }

            $b = 7;
            $soma = 0;
            for($i = 0; $i <= 5; $i++){
                $soma += $ie[$i] * $b;
                $b--;
            }

            $i = $soma % $modulo;
            if($modulo == 10){
                if($i == 0){
                    $dig = 0;
                } else{
                    $dig = $modulo - $i;
                }
            } else{
                if($i <= 1){
                    $dig = 0;
                } else{
                    $dig = $modulo - $i;
                }
            }
            if(!($dig == $ie[7])){
                return 0;
            } else{
                $b = 8;
                $soma = 0;
                for($i = 0; $i <= 5; $i++){
                    $soma += $ie[$i] * $b;
                    $b--;
                }
                $soma += $ie[7] * 2;
                $i = $soma % $modulo;
                if($modulo == 10){
                    if($i == 0){
                        $dig = 0;
                    } else{
                        $dig = $modulo - $i;
                    }
                } else{
                    if($i <= 1){
                        $dig = 0;
                    } else{
                        $dig = $modulo - $i;
                    }
                }

                return ($dig == $ie[6]);
            }
        }
    }
    //Ceará
    elseif($Estado == 'CE'){
        if(strlen($ie) != 9){
            return 0;
        } else{
            $b = 9;
            $soma = 0;
            for($i = 0; $i <= 7; $i++){
                $soma += $ie[$i] * $b;
                $b--;
            }
            $dig = 11 - ($soma % 11);

            if($dig >= 10){
                $dig = 0;
            }

            return ($dig == $ie[8]);
        }
    }
    // Distrito Federal
    elseif($Estado == 'DF'){
        if(strlen($ie) != 13){
            return 0;
        } else{
            if(substr($ie,0,2) != '07'){
                return 0;
            } else{
                $b = 4;
                $soma = 0;
                for($i = 0; $i <= 10; $i++){
                    $soma += $ie[$i] * $b;
                    $b--;
                    if($b == 1){
                        $b = 9;
                    }
                }
                $dig = 11 - ($soma % 11);
                if($dig >= 10){
                    $dig = 0;
                }

                if(!($dig == $ie[11])){
                    return 0;
                } else{
                    $b = 5;
                    $soma = 0;
                    for($i = 0; $i <= 11; $i++){
                        $soma += $ie[$i] * $b;
                        $b--;
                        if($b == 1){
                            $b = 9;
                        }
                    }
                    $dig = 11 - ($soma % 11);
                    if($dig >= 10){
                        $dig = 0;
                    }

                    return ($dig == $ie[12]);
                }
            }
        }
    }
    //Espirito Santo
    elseif($Estado == 'ES'){
        if(strlen($ie) != 9){
            return 0;
        } else{
            $b = 9;
            $soma = 0;
            for($i = 0; $i <= 7; $i++){
                $soma += $ie[$i] * $b;
                $b--;
            }
            $i = $soma % 11;
            if($i < 2){
                $dig = 0;
            } else{
                $dig = 11 - $i;
            }

            return ($dig == $ie[8]);
        }
    }
    //Goias
    elseif($Estado == 'GO'){
        if(strlen($ie) != 9){
            return 0;
        } else{
            $s = substr($ie,0,2);

            if(!( ($s == 10) || ($s == 11) || ($s == 15) )){
                return 0;
            } else{
                $n = substr($ie,0,7);

                if($n == 11094402){
                    if($ie[8] != 0){
                        if($ie[8] != 1){
                            return 0;
                        } else{
                            return 1;
                        }
                    } else{
                        return 1;
                    }
                } else{
                    $b = 9;
                    $soma = 0;
                    for($i = 0; $i <= 7; $i++){
                        $soma += $ie[$i] * $b;
                        $b--;
                    }
                    $i = $soma % 11;

                    if($i == 0){
                        $dig = 0;
                    } else{
                        if($i == 1){
                            if(($n >= 10103105) && ($n <= 10119997)){
                                $dig = 1;
                            } else{
                                $dig = 0;
                            }
                        } else{
                            $dig = 11 - $i;
                        }
                    }

                    return ($dig == $ie[8]);
                }
            }
        }
    }
    //Maranhão
    elseif($Estado == 'MA'){
        if(strlen($ie) != 9){
            return 0;
        } else{
            if(substr($ie,0,2) != 12){
                return 0;
            } else{
                $b = 9;
                $soma = 0;
                for($i = 0; $i <= 7; $i++){
                    $soma += $ie[$i] * $b;
                    $b--;
                }
                $i = $soma % 11;
                if($i <= 1){
                    $dig = 0;
                } else{
                    $dig = 11 - $i;
                }

                return ($dig == $ie[8]);
            }
        }
    }
    //Mato Grosso
    elseif($Estado == 'MT'){
        if(strlen($ie) != 11){
            return 0;
        } else{
            $b = 3;
            $soma = 0;
            for($i = 0; $i <= 9; $i++){
                $soma += $ie[$i] * $b;
                $b--;
                if($b == 1){
                    $b = 9;
                }
            }
            $i = $soma % 11;
            if($i <= 1){
                $dig = 0;
            } else{
                $dig = 11 - $i;
            }

            return ($dig == $ie[10]);
        }
    }
    // Mato Grosso do Sul
    elseif($Estado == 'MS'){
        if(strlen($ie) != 9){
            return 0;
        } else{
            if(substr($ie,0,2) != 28){
                return 0;
            } else{
                $b = 9;
                $soma = 0;
                for($i = 0; $i <= 7; $i++){
                    $soma += $ie[$i] * $b;
                    $b--;
                }
                $i = $soma % 11;
                if($i == 0){
                    $dig = 0;
                } else{
                    $dig = 11 - $i;
                }

                if($dig > 9){
                    $dig = 0;
                }

                return ($dig == $ie[8]);
            }
        }
    }
    //Minas Gerais
    elseif($estado == 'MG'){
        if(strlen($ie) != 13){
            return 0;
        }
        $ToCalcOne = '';
        $controle = 1;
        $ie2 = substr($ie,0,3).'0'.substr($ie,3);
        for($x = 0; $x <= 11; $x++){
            $ToCalcOne.= substr($ie2,$x,1) * $controle;
            $controle++;
            if($controle == 3){
                $controle = 1;
            }
        }
        $soma = 0;
        for($x = 0; $x <= strlen($ToCalcOne); $x++){
            $soma += substr($ToCalcOne,$x,1);
        }
        $ToCalcTwo = substr($soma + 10,0,1).(0);
        $PrimeiroNumero = $ToCalcTwo - $soma;
        $b = 3;
        $soma = 0;
        for($i = 0; $i <= 11; $i++){
            $soma += substr($ie,$i,1) * $b;
            $b--;
            if($b == 1){
                $b = 11;
            }
        }
        $resto = floor($soma % 11);

        if($resto == 1 || $resto == 0){
            $SegundoNumero = 0;
        } else{
            $SegundoNumero = 11 - $resto;
        }
        if(substr($ie,strlen($ie) - 2,1) == $PrimeiroNumero && substr($ie,strlen($ie) - 1,1) == $SegundoNumero){
            return 1;
        } else{
            return 0;
        }
    }
    //Pará
    elseif($Estado == 'PA'){
        if(strlen($ie) != 9){
            return 0;
        } else{
            if(substr($ie,0,2) != 15){
                return 0;
            } else{
                $b = 9;
                $soma = 0;
                for($i = 0; $i <= 7; $i++){
                    $soma += $ie[$i] * $b;
                    $b--;
                }
                $i = $soma % 11;
                if($i <= 1){
                    $dig = 0;
                } else{
                    $dig = 11 - $i;
                }

                return ($dig == $ie[8]);
            }
        }
    }
    //Paraíba
    elseif($Estado == 'PB'){
        if(strlen($ie) != 9){
            return 0;
        } else{
            $b = 9;
            $soma = 0;
            for($i = 0; $i <= 7; $i++){
                $soma += $ie[$i] * $b;
                $b--;
            }
            $i = $soma % 11;
            if($i <= 1){
                $dig = 0;
            } else{
                $dig = 11 - $i;
            }

            if($dig > 9){
                $dig = 0;
            }

            return ($dig == $ie[8]);
        }
    }
    //Paraná
    elseif($estado == 'PR'){
        if(strlen($ie) != 10){
            return 0;
        } else{
            $b = 3;
            $soma = 0;
            for($i = 0; $i <= 7; $i++){
                $soma += $ie[$i] * $b;
                $b--;
                if($b == 1){
                    $b = 7;
                }
            }
            $i = $soma % 11;
            if($i <= 1){
                $dig = 0;
            } else{
                $dig = 11 - $i;
            }

            if(!($dig == $ie[8])){
                return 0;
            } else{
                $b = 4;
                $soma = 0;
                for($i = 0; $i <= 8; $i++){
                    $soma += $ie[$i] * $b;
                    $b--;
                    if($b == 1){
                        $b = 7;
                    }
                }
                $i = $soma % 11;
                if($i <= 1){
                    $dig = 0;
                } else{
                    $dig = 11 - $i;
                }

                return ($dig == $ie[9]);
            }
        }
    }
    //Pernambuco
    elseif($Estado == 'PE'){
        if(strlen($ie) == 9){
            $b = 8;
            $soma = 0;
            for($i = 0; $i <= 6; $i++){
                $soma += $ie[$i] * $b;
                $b--;
            }
            $i = $soma % 11;
            if($i <= 1){
                $dig = 0;
            } else{
                $dig = 11 - $i;
            }

            if(!($dig == $ie[7])){
                return 0;
            } else{
                $b = 9;
                $soma = 0;
                for($i = 0; $i <= 7; $i++){
                    $soma += $ie[$i] * $b;
                    $b--;
                }
                $i = $soma % 11;
                if($i <= 1){
                    $dig = 0;
                } else{
                    $dig = 11 - $i;
                }

                return ($dig == $ie[8]);
            }
        } elseif(strlen($ie) == 14){
            $b = 5;
            $soma = 0;
            for($i = 0; $i <= 12; $i++){
                $soma += $ie[$i] * $b;
                $b--;
                if($b == 0){
                    $b = 9;
                }
            }
            $dig = 11 - ($soma % 11);
            if($dig > 9){
                $dig = $dig - 10;
            }

            return ($dig == $ie[13]);
        } else{
            return 0;
        }
    }
    //Piauí
    elseif($Estado == 'PI'){
        if(strlen($ie) != 9){
            return 0;
        } else{
            $b = 9;
            $soma = 0;
            for($i = 0; $i <= 7; $i++){
                $soma += $ie[$i] * $b;
                $b--;
            }
            $i = $soma % 11;
            if($i <= 1){
                $dig = 0;
            } else{
                $dig = 11 - $i;
            }
            if($dig >= 10){
                $dig = 0;
            }

            return ($dig == $ie[8]);
        }
    }
    // Rio de Janeiro
    elseif($Estado == 'RJ'){
        if(strlen($ie) != 8){
            return 0;
        } else{
            $b = 2;
            $soma = 0;
            for($i = 0; $i <= 6; $i++){
                $soma += $ie[$i] * $b;
                $b--;
                if($b == 1){
                    $b = 7;
                }
            }
            $i = $soma % 11;
            if($i <= 1){
                $dig = 0;
            } else{
                $dig = 11 - $i;
            }

            return ($dig == $ie[7]);
        }
    }
    //Rio Grande do Norte
    elseif($Estado == 'RN'){
        if(!( (strlen($ie) == 9) || (strlen($ie) == 10) )){
            return 0;
        } else{
            $b = strlen($ie);
            if($b == 9){
                $s = 7;
            } else{
                $s = 8;
            }
            $soma = 0;
            for($i = 0; $i <= $s; $i++){
                $soma += $ie[$i] * $b;
                $b--;
            }
            $soma *= 10;
            $dig = $soma % 11;
            if($dig == 10){
                $dig = 0;
            }

            $s += 1;
            return ($dig == $ie[$s]);
        }
    }
    // Rio Grande do Sul
    elseif($Estado == 'RS'){
        if(strlen($ie) != 10){
            return 0;
        } else{
            $b = 2;
            $soma = 0;
            for($i = 0; $i <= 8; $i++){
                $soma += $ie[$i] * $b;
                $b--;
                if($b == 1){
                    $b = 9;
                }
            }
            $dig = 11 - ($soma % 11);
            if($dig >= 10){
                $dig = 0;
            }

            return ($dig == $ie[9]);
        }
    }
    // Rondônia
    elseif($Estado == 'RO'){
        if(strlen($ie) == 9){
            $b = 6;
            $soma = 0;
            for($i = 3; $i <= 7; $i++){
                $soma += $ie[$i] * $b;
                $b--;
            }
            $dig = 11 - ($soma % 11);
            if($dig >= 10){
                $dig = $dig - 10;
            }

            return ($dig == $ie[8]);
        } elseif(strlen($ie) == 14){
            $b = 6;
            $soma = 0;
            for($i = 0; $i <= 12; $i++){
                $soma += $ie[$i] * $b;
                $b--;
                if($b == 1){
                    $b = 9;
                }
            }
            $dig = 11 - ( $soma % 11);
            if($dig > 9){
                $dig = $dig - 10;
            }

            return ($dig == $ie[13]);
        } else{
            return 0;
        }
    }
    //Roraima
    elseif($Estado == 'RR'){
        if(strlen($ie) != 9){
            return 0;
        } else{
            if(substr($ie,0,2) != 24){
                return 0;
            } else{
                $b = 1;
                $soma = 0;
                for($i = 0; $i <= 7; $i++){
                    $soma += $ie[$i] * $b;
                    $b++;
                }
                $dig = $soma % 9;

                return ($dig == $ie[8]);
            }
        }
    }
    //Santa Catarina
    elseif($Estado == 'SC'){
        if(strlen($ie) != 9){
            return 0;
        } else{
            $b = 9;
            $soma = 0;
            for($i = 0; $i <= 7; $i++){
                $soma += $ie[$i] * $b;
                $b--;
            }
            $dig = 11 - ($soma % 11);
            if($dig <= 1){
                $dig = 0;
            }

            return ($dig == $ie[8]);
        }
    }
    //São Paulo
    elseif($Estado == 'SP'){
        if(strtoupper(substr($ie,0,1)) == 'P'){
            if(strlen($ie) != 13){
                return 0;
            } else{
                $b = 1;
                $soma = 0;
                for($i = 1; $i <= 8; $i++){
                    $soma += $ie[$i] * $b;
                    $b++;
                    if($b == 2){
                        $b = 3;
                    }
                    if($b == 9){
                        $b = 10;
                    }
                }
                $dig = $soma % 11;
                return ($dig == $ie[9]);
            }
        } else{
            if(strlen($ie) != 12){
                return 0;
            } else{
                $b = 1;
                $soma = 0;
                for($i = 0; $i <= 7; $i++){
                    $soma += $ie[$i] * $b;
                    $b++;
                    if($b == 2){
                        $b = 3;
                    }
                    if($b == 9){
                        $b = 10;
                    }
                }
                $dig = $soma % 11;
                if($dig > 9){
                    $dig = 0;
                }

                if($dig != $ie[8]){
                    return 0;
                } else{
                    $b = 3;
                    $soma = 0;
                    for($i = 0; $i <= 10; $i++){
                        $soma += $ie[$i] * $b;
                        $b--;
                        if($b == 1){
                            $b = 10;
                        }
                    }
                    $dig = $soma % 11;
                    if($dig > 9){
                        $dig = 0;
                    }

                    return ($dig == $ie[11]);
                }
            }
        }
    }
    //Sergipe
    elseif($Estado == 'SE'){
        if(strlen($ie) != 9){
            return 0;
        } else{
            $b = 9;
            $soma = 0;
            for($i = 0; $i <= 7; $i++){
                $soma += $ie[$i] * $b;
                $b--;
            }
            $dig = 11 - ($soma % 11);
            if($dig > 9){
                $dig = 0;
            }

            return ($dig == $ie[8]);
        }
    }
    //Tocantins
    elseif($Estado == 'TO'){
        if(strlen($ie) != 11){
            return 0;
        } else{
            $s = substr($ie,2,2);
            if(!( ($s == '01') || ($s == '02') || ($s == '03') || ($s == '99') )){
                return 0;
            } else{
                $b = 9;
                $soma = 0;
                for($i = 0; $i <= 9; $i++){
                    if(!(($i == 2) || ($i == 3))){
                        $soma += $ie[$i] * $b;
                        $b--;
                    }
                }
                $i = $soma % 11;
                if($i < 2){
                    $dig = 0;
                } else{
                    $dig = 11 - $i;
                }

                return ($dig == $ie[10]);
            }
        }
    }
}

function TrataFloatPost($Valor){
    $ValorRetorno = str_replace('.','',$Valor);
    $ValorRetorno = str_replace(',','.',$ValorRetorno);
    return $ValorRetorno;
}

/**
 * Realiza chmod de forma recursiva quando necessário
 * @param string $Caminho
 * @param int $Modo
 * @param bool $Recursivo true quando for executar de forma recursiva em um diretório
 * @return bool
 */
function chmodr($Caminho, $Modo,$Recursivo=false){
    if($Recursivo === false || is_file($Caminho)){
        return chmod($Caminho, $Modo);
    }
    $ListaDeDiretorios = opendir($Caminho);
    while(($Arquivo = readdir($ListaDeDiretorios)) !== false){
        if($Arquivo != '.' && $Arquivo != '..'){
            $CaminhoCompleto = $Caminho . '/' . $Arquivo;
            if(is_link($CaminhoCompleto)){
                return false;
            }
            elseif(!is_dir($CaminhoCompleto) && !chmod($CaminhoCompleto, $Modo)){
                return false;
            }
            elseif(!chmodr($CaminhoCompleto, $Modo)){
                return false;
            }
        }
    }
    closedir($ListaDeDiretorios);
    if (chmod($Caminho, $Modo)){
        return true;
    }
    else{
        return true;
    }
}



/*
 * FUNÇÕES DE MANIPULAÇÃO DE DATAS
 */

function MakeTime($DataHora){
//Criar timestamp a partir de string de data YYYY-MM-DD H:i:s
    $Ano = substr($DataHora,0,4);
    $Mes = substr($DataHora,5,2);
    $Dia = substr($DataHora,8,2);
    $Hora = substr($DataHora,11,2);
    $Minuto = substr($DataHora,14,2);
    $Segundo = substr($DataHora,17,2);
    $Hora = (!empty($Hora))?$Hora:0;
    $Minuto = (!empty($Minuto))?$Minuto:0;
    $Segundo = (!empty($Segundo))?$Segundo:0;

    return mktime($Hora,$Minuto,$Segundo,$Mes,$Dia,$Ano);
}

function DataHoraEncode($DataHora){
//Transformar data YYYY-MM-DD para DD/MM/YYYY
    if($DataHora != ''){
        return substr($DataHora,8,2)."/".substr($DataHora,5,2)."/".substr($DataHora,0,4);
    } else{
        return '';
    }
}

function DataHoraDecode($DataHora){
//Transformar data YYYY-MM-DD para DD/MM/YYYY
    if($DataHora != ''){
        return substr($DataHora,6,4)."-".substr($DataHora,3,2)."-".substr($DataHora,0,2);
    } else{
        return '';
    }
}

function DiferencaEntreDatas($DataHoraIni,$DataHoraFim,$Coeficiente=86400){
    $TimeStampDataHoraIni = MakeTime($DataHoraIni);
    $TimeStampDataHoraFim = MakeTime($DataHoraFim);
    $Diferenca = $TimeStampDataHoraFim - $TimeStampDataHoraIni;
    $Diferenca = $Diferenca / $Coeficiente;
    return round($Diferenca,0);
}


/**
* Calcula a quantidade de dias úteis entre duas datas (sem contar feriados)
* @author Marcos Regis
* @param String $datainicial
* @param String $datafinal=null
*/
function dias_uteis($datainicial,$datafinal=null){
	if (!isset($datainicial)) return false;
	if (!isset($datafinal)) $datafinal=time();

	$segundos_datainicial = strtotime(preg_replace("#(\d{2})/(\d{2})/(\d{4})#","$3/$2/$1",$datainicial));
	$segundos_datafinal = strtotime(preg_replace("#(\d{2})/(\d{2})/(\d{4})#","$3/$2/$1",$datafinal));
	$dias = abs(floor(floor(($segundos_datafinal-$segundos_datainicial)/3600)/24 ) );
	$uteis=0;

	for($i=1;$i<=$dias;$i++){
		$diai = $segundos_datainicial+($i*3600*24);
		$w = date('w',$diai);
	if ($w==0){
	//echo date('d/m/Y',$diai)." é Domingo<br />";
	}elseif($w==6){
	//echo date('d/m/Y',$diai)." é Sábado<br />";
	}else{
	//echo date('d/m/Y',$diai)." é dia útil<br />";
		$uteis++;
	}
}
return $uteis;
}

function tempoData($dataini, $datafim) {
	
	# Split para dia, mes, ano, hora, minuto e segundo da data inicial
	$_split_datehour = explode(' ',$dataini);
	$_split_data = explode("/", $_split_datehour[0]);
	$_split_hour = explode(":", $_split_datehour[1]);
	# Coloquei o parse (integer) caso o timestamp nao tenha os segundos, dai ele fica como 0
	$dtini = mktime ($_split_hour[0], $_split_hour[1], (integer)$_split_hour[2], $_split_data[1], $_split_data[0], $_split_data[2]);
	
	# Split para dia, mes, ano, hora, minuto e segundo da data final
	$_split_datehour = explode(' ',$datafim);
	$_split_data = explode("/", $_split_datehour[0]);
	$_split_hour = explode(":", $_split_datehour[1]);
	$dtfim = mktime ($_split_hour[0], $_split_hour[1], (integer)$_split_hour[2], $_split_data[1], $_split_data[0], $_split_data[2]);
	
	# Diminui a datafim que é a maior com a dataini
	$time = ($dtfim - $dtini);
	
	# Recupera os dias
	$days  = floor($time/86400);
	# Recupera as horas
	$hours = floor(($time-($days*86400))/3600);
	# Recupera os minutos
	$mins  = floor(($time-($days*86400)-($hours*3600))/60);
	# Recupera os segundos
	$secs  = floor($time-($days*86400)-($hours*3600)-($mins*60));
	
	# Monta o retorno no formato
	# 5d 10h 15m 20s
	# somente se os itens forem maior que zero
	$retorno  = "";
	$retorno .= ($days>0)  ?  $days .'d ' : ""  ;
	$retorno .= ($hours>0) ?  $hours .'h ': ""  ;
	$retorno .= ($mins>0)  ?  $mins .'m ' : ""  ;
	$retorno .= ($secs>0)  ?  $secs .'s ' : ""  ;
	
	# Se o dia for maior que 3 fica vermelho
	//if($days > 3){
	//	return "<span style='color:red'>".$retorno."</span>";
//	}
	return $retorno;

}



function TextoParaXML($Texto,$Htmlentities = true){
    if($Htmlentities === true){
        $Texto = htmlentities($Texto);
    }
    return str_replace('&','&amp;',$Texto);
}

function retiraAcentos($string){
    $array1 = array("á", "à", "â", "ã", "ä", "é", "è", "ê", "ë", "í", "ì", "î", "ï", "ó", "ò", "ô", "õ", "ö", "ú", "ù", "û", "ü", "ç" , "Á", "À", "Â", "Ã", "Ä", "É", "È", "Ê", "Ë", "Í", "Ì", "Î", "Ï", "Ó", "Ò", "Ô", "Õ", "Ö", "Ú", "Ù", "Û", "Ü", "Ç");
    $array2 = array("a", "a", "a", "a", "a", "e", "e", "e", "e", "i", "i", "i", "i", "o", "o", "o", "o", "o", "u", "u", "u", "u", "c" , "A", "A", "A", "A", "A", "E", "E", "E", "E", "I", "I", "I", "I", "O", "O", "O", "O", "O", "U", "U", "U", "U", "C");
    return str_replace($array1, $array2,$string);
}

function trata_dt_exp_dts($dt){
    $dtt = str_replace('/','',$dt);
    $dtt = str_replace('-','',$dtt);
    return $dtt;
}


#==============================================================================#
#============================ FUNÇÕES DA WEB ==================================#
function rmdirr($dir) {
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (filetype($dir . "/" . $object) == "dir")
                    rmdirr($dir . "/" . $object); else
                    unlink($dir . "/" . $object);
            }
        }
        reset($objects);
        rmdir($dir);
    }
}
#==============================================================================#
#========================== FIM FUNÇÕES DA WEB ================================#

function SaldoEstoque($cnx,$estabelecimento,$it_codigo_pai,$it_codigo_filho){
    $qry_estabel = mysql_query("SELECT * FROM is_estabel_x_depos WHERE cod_estabel = '".$estabelecimento."'");
    $ar_est = array();
    while($ar_estabel = mysql_fetch_array($qry_estabel)){
        $ar_est[] = $ar_estabel['cod_depos'];
    }

    $sql_estoque_odbc = "
						SELECT
						SUM(\"qtidade-atu\") AS QTIDADE_ATU,
						SUM(\"qt-alocada\") AS QT_ALOCADA,
						SUM(\"qt-aloc-prod\") AS QT_ALOC_PROD,
						SUM(\"qt-aloc-ped\") AS QT_ALOC_PED
						FROM pub.\"saldo-estoq\"
						WHERE
						\"it-codigo\" = '".(($it_codigo_filho == '')?$it_codigo_pai:$it_codigo_filho)."'
						AND \"cod-estabel\" = '".$estabelecimento."'
						";
    #echo str_replace("\\",'',$sql_estoque_odbc);
    $qry_estoque_odbc = odbc_exec($cnx,$sql_estoque_odbc);
    $rs_estoque_odbc = odbc_fetch_array($qry_estoque_odbc);

    $sql_qtde_aberto_odbc = "	SELECT
								SUM(t1.\"qt-pedida\" - t1.\"qt-atendida\") AS SOMA
								FROM pub.\"ped-item\" t1
								INNER JOIN pub.\"ped-venda\" t2 ON t1.\"nr-pedcli\" = t2.\"nr-pedcli\" AND t1.\"nome-abrev\" = t2.\"nome-abrev\"
								WHERE
								(t2.\"cod-sit-ped\" = '1' OR t2.\"cod-sit-ped\" = '2')
								AND t2.\"cod-estabel\" = '".$estabelecimento."'
								AND t1.\"it-codigo\" = '".(($it_codigo_filho == '')?$it_codigo_pai:$it_codigo_filho)."'
								";
    $qry_qtde_aberto_odbc = odbc_exec($cnx,$sql_qtde_aberto_odbc);
    $ar_qtde_aberto_odbc = odbc_fetch_array($qry_qtde_aberto_odbc);

    $quantidade = $rs_estoque_odbc['QTIDADE_ATU'] - $ar_qtde_aberto_odbc['SOMA'];

    return $quantidade;
}

function GetPessoa($id_pessoa,$ar_colunas=array()){
    $colunas = ' * ';
    if(count($ar_colunas) > 0){
        $colunas = implode(',',$ar_colunas);
    }
    $ar_cliente = farray(query("SELECT $colunas FROM is_pessoa WHERE numreg = '".$id_pessoa."'"));
    return $ar_cliente;
}

function GetProduto($id_produto,$ar_colunas=array()){
    $colunas = ' * ';
    if(count($ar_colunas) > 0){
        $colunas = implode(',',$ar_colunas);
    }
    $ar_produto = farray(query("SELECT $colunas FROM is_produtos WHERE id_produto = '".$id_produto."'"));
    return $ar_produto;
}

function GetParam($id_param){
    $ar_param = farray(query("SELECT parametro FROM is_parametros_sistema WHERE id_parametro = '".$id_param."'"));
    return $ar_param['parametro'];
}

function validarvl2float($value){
    #$value = str_replace('.','',$value);
    $value = str_replace(',','.',$value);
    if(is_numeric($value) === false){
        $value = '0.01';
    }
    return $value;
}

function valida_dt($dt,$frmt){
    if($frmt == 'en'){
        if(strpos($dt,'-') === false){
            return false;
        } else{
            $ar_dt = explode('-',$dt);
            $dia = $ar_dt[2];
            $mes = $ar_dt[1];
            $ano = $ar_dt[0];
        }
    } elseif($frmt == 'br'){
        if(strpos($dt,'/') === false){
            return false;
        } else{
            $ar_dt = explode('/',$dt);
            $dia = $ar_dt[0];
            $mes = $ar_dt[1];
            $ano = $ar_dt[2];
        }
    }
    return checkdate($mes,$dia,$ano);
}

function monta_combobox($tabela,$campo_id,$campo_desc,$nome,$readonly='',$vl_padrao='',$whereand=''){
    if($readonly == 'readonly'){
        if($whereand != ''){
            $whereand = ' AND '.$whereand;
        }
        $qry = query("SELECT ".$campo_id.",".$campo_desc." FROM ".$tabela." WHERE ".$campo_id." = '".$vl_padrao."'".$whereand);
    } else{
        if($whereand != ''){
            $whereand = ' WHERE '.$whereand;
        }
        $qry = query("SELECT ".$campo_id.",".$campo_desc." FROM ".$tabela.$whereand);
    }
    $combobox = '<select name="'.$nome.'" id="'.$nome.'">';
    $combobox .= '<option>Selecione</option>';
    while($ar = farray($qry)){
        $selected = ($ar[$campo_id] == $vl_padrao)?' selected="selected" ':'';
        $combobox .= utf8_encode('<option value="'.$ar[$campo_id].'" '.$selected.'>'.$ar[$campo_desc].'</option>');
    }
    $combobox .= '</select>';
    return $combobox;
}

function gera_nome_abreviado($nome,$num=0){
    $nome = substr(str_replace(' ','',$nome),0,8);
    $qry_nome_abreviado = mysql_query("SELECT * FROM is_pessoas WHERE nome_abreviado = '".$nome.$num."'");
    if(mysql_num_rows($qry_nome_abreviado) == 0){
        $nome_abreviado = $nome.$num;
        $novo_nome = strtoupper($nome_abreviado);
        return strtoupper($nome_abreviado);
    } else{
        $novo_nome = gera_nome_abreviado($nome,$num + 1);
    }
    return strtoupper($novo_nome);
}

function acentos2html($str){
    $array1 = array("á","à","â","ã","ä","é","è","ê","ë","í","ì","î","ï","ó","ò","ô","õ","ö","ú","ù","û","ü","ç","Á","À","Â","Ã","Ä","É","È","Ê","Ë","Í","Ì","Î","Ï","Ó","Ò","Ô","Õ","Ö","Ú","Ù","Û","Ü","Ç");
    $array2 = array("&aacute;","&agrave;","&acirc;","&atilde;","&auml;","&eacute;","&egrave;","&ecirc;","&euml;","&iacute;","&igrave;","&icirc;","&iuml;","&oacute;","&ograve;","&ocirc;","&otilde;","&ouml;","&uacute;","&ugrave;","&ucirc;","&uuml;","&ccedil;","&Aacute;","&Agrave;","&Acirc;","&Atilde;","&Auml;","&Eacute;","&Egrave;","&Ecirc;","&Euml;","&Iacute;","&Igrave;","&Icirc;","&Iuml;","&Oacute;","&Ograve;","&Ocirc;","&Otilde;","&Ouml;","&Uacute;","&Ugrave;","&Ucirc;","&Uuml;","&Ccedil;");
    return str_replace($array1,$array2,$str);
}

function max_id($tabela,$campo,$z_a_e = 0,$s_a_e = ''){
    $ar_max = farray(query("SELECT MAX(".$campo."*1) AS max FROM ".$tabela));
    $ar_max = $ar_max['max'] * 1;
    $ar_max = str_pad(($ar_max + 1),$z_a_e,$s_a_e,STR_PAD_LEFT);
    return $ar_max;
}

function make_time($time){
    $ano = substr($time,0,4);
    $mes = substr($time,5,2);
    $dia = substr($time,8,2);
    $hr = substr($time,11,2);
    $min = substr($time,14,2);
    $seg = substr($time,17,2);
    $hr = (!empty($hr))?$hr:0;
    $min = (!empty($min))?$min:0;
    $seg = (!empty($seg))?$seg:0;
    return mktime($hr,$min,$seg,$mes,$dia,$ano);
}

function real2float($value){
    $value = str_replace(".","",$value);
    $value = str_replace(",",".",$value);
    return $value;
}

function float2real($value){
    $value = number_format($value,2,',','.');
    return $value;
}

//Transformar data AAAA-MM-DD 00:00:00 para AAAA-MM-DD
function formata_data($dt){
    return substr($dt,0,4)."-".substr($dt,5,2)."-".substr($dt,8,2);
}

function valida_hora($hr){
    if(empty($hr)){
        return false;
    }
    $ar_hr = explode(':',$hr);
    //Validando as horas
    if(strlen($ar_hr[0]) <= 2 && is_numeric($ar_hr[0]) && $ar_hr[0] <= 24 && $ar_hr[0] >= 0 && !empty($ar_hr[0]) && strlen($ar_hr[1]) <= 2 && is_numeric($ar_hr[1]) && $ar_hr[1] <= 59 && $ar_hr[1] >= 0 && !empty($ar_hr[1])){
        return true;
    } else{
        return false;
    }
}

function valida_horas($hr,$campo=''){
    if(empty($hr)){
        return;
    }
    $alert = '';
    $hr_num = str_replace(':','',$hr);
    if((strlen($hr) != 5 || !is_numeric($hr_num)) && (!empty($hr))){
        return $alert .= utf8_encode("alert('-O campo ".$campo." deve conter 5 caracteres.');\n");
    }
    if(substr($hr_num,0,2) > 23 || substr($hr_num,4,2) > 59){
        return $alert .= utf8_encode("alert('-O campo ".$campo." deve ser de 00:00 à 23:59.'); ");
    }
    return $alert;
}

function dt_fds($dt){
    $dia = date("D",strtotime($dt));
    if($dia == "Sat"){
        return date("Y-m-d",strtotime($dt." + 2 days"));
    } elseif($dia == "Sun"){
        return date("Y-m-d",strtotime($dt." + 1 days"));
    } else{
        return $dt;
    }
}


//Função para calcular direfrenca entre horas
function diferenca_hr($hr1,$hr2,$sinal='',$decimais=0){
    $decimais = trim($decimais);
    $hr1 = date("H:i",strtotime($hr1));
    $hr2 = date("H:i",strtotime($hr2));

    $hr1 = mktime(substr($hr1,0,2),substr($hr1,3,2),0,0,0,0);
    $hr2 = mktime(substr($hr2,0,2),substr($hr2,3,2),0,0,0,0);
    if($decimais == 'S' || $decimais == ''){
        $diferenca_hr = ($hr2 - $hr1) / 60;
        $decimais = 0;
    } else{
        $diferenca_hr = ($hr2 - $hr1) / 3600;
    }

    if($sinal == "S"){
        if($diferenca_hr < 0){
            return round($diferenca_hr,$decimais);
        } else{
            return "+".round($diferenca_hr,$decimais);
        }
    } else{
        return abs(round($diferenca_hr,$decimais));
    }
}

//Transformar data en para br
function dten2br($dt){
    if($dt != ''){
        return substr($dt,8,2)."/".substr($dt,5,2)."/".substr($dt,0,4);
    } else{
        return '';
    }
}

//Transformar data br para en
function dtbr2en($dt){
    if($dt != ''){
        return substr($dt,6,4)."-".substr($dt,3,2)."-".substr($dt,0,2);
    } else{
        return '';
    }
}

//Função para calcular direfrenca de dias entre datas
function diferenca_dt($dt1,$dt2,$sinal=''){
    $dt1 = date("d/m/Y",strtotime($dt1));
    $dt2 = date("d/m/Y",strtotime($dt2));

    $dt1 = mktime(0,0,0,substr($dt1,3,2),substr($dt1,0,2),substr($dt1,6,4));
    $dt2 = mktime(0,0,0,substr($dt2,3,2),substr($dt2,0,2),substr($dt2,6,4));

    $diferenca_dias = ($dt2 - $dt1) / 86400;
    if($sinal == "S"){
        if($diferenca_dias < 0){
            return round($diferenca_dias,0);
        } else{
            return "+".round($diferenca_dias,0);
        }
    } else{
        return abs(round($diferenca_dias,0));
    }
}

function trata_atividade($numreg,$pai,$dt_inicio = '',$dt_prev_fim = '',$acao = '',$valida = ''){
    if($acao == "T"){
        $tabela_atividade = 'is_atividade_tmp';
    } else{
        $tabela_atividade = 'is_atividade';
    }
    //Pegando as infomações da atividade
    $sql_ativ = query("SELECT * FROM ".$tabela_atividade." WHERE id_atividade = '".$numreg."'");
    $ar_ativ = farray($sql_ativ);

    //Calculando a duracao da atividade
    $duracao_atividade = diferenca_dt($ar_ativ['dt_inicio'],$ar_ativ['dt_prev_fim']);

    //Verificando se as datas de inicio e fim foram alteradas
    if($dt_prev_fim > $ar_ativ['dt_prev_fim']){
        $diferenca_dias = " - ".diferenca_dt($dt_prev_fim,$ar_ativ['dt_prev_fim'])." days";
    } else{
        $diferenca_dias = "";
    }

    //Verificando se é a atividade de início para não atualizar sua dt de início
    if($pai == "PAI"){
        //Verifica se esta atrasado
        //if($ar_ativ['dt_prev_fim'] < date("Y-m-d")){
        //$nova_dt_prev_fim = dt_fds(date("Y-m-d"));
        //Se for temporária e validacao retorna
        //if($acao == "T" && $valida = "S"){
        //@session_start();
        //$_SESSION['atividades_afetadas'] .= $ar_ativ['id_atividade']."|";
        //}
        //else{
        //query("UPDATE ".$tabela_atividade." SET dt_prev_fim = '".$nova_dt_prev_fim."', id_situacao = 'T' WHERE id_atividade = '".$ar_ativ['id_atividade']."'");
        //}
        //}
    } else{
        $sql_ativ_pai = query("SELECT t1.*, t2.* FROM ".$tabela_atividade." t1 INNER JOIN is_ativ_dependencia t2 ON t1.id_atividade = t2.id_atividade_pai WHERE t2.id_atividade_filha = '".$ar_ativ['id_atividade']."' AND t1.id_situacao <> 'R' ORDER BY t1.dt_prev_fim ASC");

        while($ar_ativ_pai = farray($sql_ativ_pai)){
            $nova_dt_inicio_filha = dt_fds(date("Y-m-d",strtotime($ar_ativ_pai['dt_prev_fim']." + 1 days ")));
            $nova_dt_prev_fim_filha = dt_fds(date("Y-m-d",strtotime($nova_dt_inicio_filha." + ".$duracao_atividade." days ")));
            //Altera somente se for afetado
            if($ar_ativ_pai['dt_prev_fim'] >= $ar_ativ['dt_inicio'] || ($ar_ativ_pai['dt_prev_fim'] <= $ar_ativ['dt_prev_fim'] && $ar_ativ['id_situacao'] == "T")){
                //Se for temporária e validacao
                if($acao == "T" && $valida = "S"){
                    @session_start();
                    $ar_ativ_afetadas = explode('|',$_SESSION['atividades_afetadas']);
                    $existe = false;
                    for($j = 0; $j < count($ar_ativ_afetadas); $j++){
                        if($ar_ativ_afetadas[$j] == $ar_ativ['id_atividade']){
                            $existe = true;
                            break;
                        }
                    }
                    if($existe == false){
                        $_SESSION['atividades_afetadas'] .= $ar_ativ['id_atividade']."|";
                    }
                }
                //else{
                query("UPDATE ".$tabela_atividade." SET dt_inicio = '".$nova_dt_inicio_filha."', dt_prev_fim = '".$nova_dt_prev_fim_filha."' WHERE id_atividade = '".$ar_ativ['id_atividade']."'");
                //}
            }
        }
    }
    //Verificando se a atividade tem atividades que dependem dela
    $qry_dep = query("SELECT t1.id_atividade_filha, t2.dt_inicio, t2.dt_prev_fim FROM is_ativ_dependencia t1 INNER JOIN ".$tabela_atividade." t2 ON t1.id_atividade_pai = t2.id_atividade WHERE t1.id_atividade_pai = '".$numreg."' AND id_situacao <> 'R' ORDER BY t2.dt_prev_fim ASC");
    while($ar_dep = farray($qry_dep)){
        //Se for temporaria e vaudacao chama funcao temporaria e validacao
        if($acao == "T" && $valida = "S"){
            trata_atividade($ar_dep['id_atividade_filha'],"",$ar_dep['dt_inicio'],$ar_dep['dt_prev_fim'],'T','S');
        }
        //Se for temporaria chama funcao temporaria
        elseif($acao == "T"){
            trata_atividade($ar_dep['id_atividade_filha'],"",$ar_dep['dt_inicio'],$ar_dep['dt_prev_fim'],'T');
        }
        //Se for para alterar a tabela oficial
        else{
            trata_atividade($ar_dep['id_atividade_filha'],"",$ar_dep['dt_inicio'],$ar_dep['dt_prev_fim']);
        }
    }
}

function monta_div_ativ_all($texto_meio,$cor_ct,$cor_t_bg,$inicio,$duracao,$base_left,$width,$link_det,$style){
    $string_ret = '<div class="div_ativ_all" style="'.$style.' width:'.$width.'px;top:'.$inicio.'px;height:'.($duracao).'px;left:'.($base_left).'px"></div>';
    return $string_ret;
}

function search_name($tabela,$campoid,$camponome,$id){
    $ar_name = farray(query("SELECT ".$camponome." FROM ".$tabela." WHERE ".$campoid."='".$id."'"));
    return $ar_name[$camponome];
}

function monta_div_ativ($texto_meio,$cor_ct,$cor_t_bg,$inicio,$duracao,$base_left,$width,$onclick='',$style='',$onmousemove=''){


    //$onclick = 'onclick="window.open(\''.$link_det.'\',\'\',\'width=750, height=550,scrollbars=yes,top=100,left=100\');" ';
    //$onclick = '';

    $string_ret = '<div '.$onmousemove.' class="div_ativ" style="'.$style.'z-index:1000; width:'.$width.'px;top:'.$inicio.'px;height:'.($duracao).'px;left:'.($base_left).'px" '.$onclick.'>
	<table width="'.$width.'" border="0" cellspacing="0" cellpadding="0">
	  <tr>
		<td width="1" height="1"></td>
		<td width="1" height="1"></td>
		<td width="1" height="1"></td>
		<td width="1" height="1" bgcolor="'.$cor_ct.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_ct.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_ct.'"></td>
		<td width="1" height="1"></td>
		<td width="1" height="1"></td>
		<td width="1" height="1"></td>
	  </tr>
	  <tr>
		<td width="1" height="1"></td>
		<td width="1" height="1"></td>
		<td width="1" height="1" bgcolor="'.$cor_ct.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_ct.'"></td>
		<td width="1" height="1"></td>
		<td width="1" height="1"></td>
	  </tr>
	  <tr>
		<td width="1" height="1"></td>
		<td width="1" height="1" bgcolor="'.$cor_ct.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_ct.'"></td>
		<td width="1" height="1"></td>
	  </tr>
	  <tr>
		<td width="1" height="1" bgcolor="'.$cor_ct.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_ct.'"></td>
	  </tr>
	  <tr>
		<td width="1" height="1" bgcolor="'.$cor_ct.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="'.($width - 10).'" height="'.($duracao - 8).'" bgcolor="'.$cor_t_bg.'">
		<div style="overflow:hidden; width:100%;height:100%">'.$texto_meio.'</div>
		</td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_ct.'"></td>
	  </tr>
	  <tr>
		<td width="1" height="1" bgcolor="'.$cor_ct.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_ct.'"></td>
	  </tr>
	  <tr>
		<td width="1" height="1"></td>
		<td width="1" height="1" bgcolor="'.$cor_ct.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_ct.'"></td>
		<td width="1" height="1"></td>
	  </tr>
	  <tr>
		<td width="1" height="1"></td>
		<td width="1" height="1"></td>
		<td width="1" height="1" bgcolor="'.$cor_ct.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_ct.'"></td>
		<td width="1" height="1"></td>
		<td width="1" height="1"></td>
	  </tr>
	  <tr>
		<td width="1" height="1"></td>
		<td width="1" height="1"></td>
		<td width="1" height="1"></td>
		<td width="1" height="1" bgcolor="'.$cor_ct.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_ct.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_ct.'"></td>
		<td width="1" height="1"></td>
		<td width="1" height="1"></td>
		<td width="1" height="1"></td>
	  </tr>
	</table>
	</div>';

    return $string_ret;
}

function table_cca($texto_meio,$width,$height,$cor_ct,$cor_t_bg){
    $table = '<table width="'.$width.'" height="'.$height.'" border="0" cellspacing="0" cellpadding="0">
	  <tr>
		<td width="1" height="1"></td>
		<td width="1" height="1"></td>
		<td width="1" height="1"></td>
		<td width="1" height="1" bgcolor="'.$cor_ct.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_ct.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_ct.'"></td>
		<td width="1" height="1"></td>
		<td width="1" height="1"></td>
		<td width="1" height="1"></td>
	  </tr>
	  <tr>
		<td width="1" height="1"></td>
		<td width="1" height="1"></td>
		<td width="1" height="1" bgcolor="'.$cor_ct.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_ct.'"></td>
		<td width="1" height="1"></td>
		<td width="1" height="1"></td>
	  </tr>
	  <tr>
		<td width="1" height="1"></td>
		<td width="1" height="1" bgcolor="'.$cor_ct.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_ct.'"></td>
		<td width="1" height="1"></td>
	  </tr>
	  <tr>
		<td width="1" height="1" bgcolor="'.$cor_ct.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_ct.'"></td>
	  </tr>
	  <tr>
		<td width="1" height="1" bgcolor="'.$cor_ct.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="'.($width - 10).'" height="'.($duracao - 8).'" bgcolor="'.$cor_t_bg.'">
		<div style="line-height:10px;overflow:hidden; width:100%;height:'.($height).'px">'.$texto_meio.'</div>
		</td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_ct.'"></td>
	  </tr>
	  <tr>
		<td width="1" height="1" bgcolor="'.$cor_ct.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_ct.'"></td>
	  </tr>
	  <tr>
		<td width="1" height="1"></td>
		<td width="1" height="1" bgcolor="'.$cor_ct.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_ct.'"></td>
		<td width="1" height="1"></td>
	  </tr>
	  <tr>
		<td width="1" height="1"></td>
		<td width="1" height="1"></td>
		<td width="1" height="1" bgcolor="'.$cor_ct.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_t_bg.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_ct.'"></td>
		<td width="1" height="1"></td>
		<td width="1" height="1"></td>
	  </tr>
	  <tr>
		<td width="1" height="1"></td>
		<td width="1" height="1"></td>
		<td width="1" height="1"></td>
		<td width="1" height="1" bgcolor="'.$cor_ct.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_ct.'"></td>
		<td width="1" height="1" bgcolor="'.$cor_ct.'"></td>
		<td width="1" height="1"></td>
		<td width="1" height="1"></td>
		<td width="1" height="1"></td>
	  </tr>
	</table>';
    return $table;
}

function monta_div_info($numreg){
    $ar = farray(query("SELECT * FROM is_atividade WHERE numreg='".$numreg."'"));

    $div_info = " onMouseMove=\"dica('";
    $div_info .= '<strong>Assunto: </strong>'.$ar['assunto'].'<hr size=1 noshade=noshade>';
    $div_info .= '<strong>Dt. In&iacute;cio: </strong>'.dten2br($ar['dt_inicio']).'<hr size=1 noshade=noshade>';
    $div_info .= '<strong>Dt. Prev.: </strong>'.dten2br($ar['dt_prev_fim']).'<hr size=1 noshade=noshade>';
    $div_info .= '<strong>Hor&aacute;rio: </strong>'.$ar['hr_inicio'].' &agrave;s '.$ar['hr_prev_fim'].'<hr size=1 noshade=noshade>';
    $div_info .= '<strong>Conta: </strong>'.search_name('is_pessoa','numreg','razao_social_nome',$ar['id_pessoa']).'<hr size=1 noshade=noshade>';
    $div_info .= 'Clique para mais detalhes';
    $div_info .= "',event);\" onMouseOut=\"dica('',event);\" ";

    return $div_info;
}

function sonumeros($number){
    $retorno = '';
    for($j = 0; $j < strlen($number); $j++){
        $char = substr($number,$j,1);
        $nm = "";
        switch($char){
            case "0":$nm = "0";
                break;
            case "1":$nm = "1";
                break;
            case "2":$nm = "2";
                break;
            case "3":$nm = "3";
                break;
            case "4":$nm = "4";
                break;
            case "5":$nm = "5";
                break;
            case "6":$nm = "6";
                break;
            case "7":$nm = "7";
                break;
            case "8":$nm = "8";
                break;
            case "9":$nm = "9";
                break;
            default : "";
        }
        $retorno = $retorno.$nm;
    }
    return $retorno;
}

function GravaLogEvento($IdEvento,$Status,$Descricao,$DescricaoDetalhada=''){
    if($IdEvento == '' || $Status === NULL || $Descricao == ''){
        return false;
    }
    $ArInsertLog = array(
        'dt_evento'             => date("Y-m-d"),
        'hr_evento'             => date("H:i:s"),
        'id_evento'             => $IdEvento,
        'id_usuario'            => (($_SESSION)?$_SESSION['id_usuario']:''),
        'status'                => (($Status === true)?'1':'0'),
        'descricao'             => $Descricao,
        'detalhes'              => $DescricaoDetalhada
    );
    $SqlInsertLog = AutoExecuteSql(TipoBancoDados, 'is_log_evento', $ArInsertLog, 'INSERT');
    $QryInsertLog = query($SqlInsertLog);
    if(!$QryInsertLog){
        return false;
    }
    return true;
}

/**
 * Exibe o texto na tela caso o usuário logado seja Admin
 * @param type $String
 */
function echoAdmin($String){
    if($_SESSION['id_usuario'] == '1'){
        echo $String;
    }
}


/**
 * Retorna o registro do crm atraves do codigo do datasul
 * Eventos Disponíveis:
 * 500 - Importação de Pedido com erro erp Datasul via ODBC
 * 520 - Erro ao exportar pedido de venda erp Datasul via ODBC
 * 600 - Transformar cliente em prospect
 * @param type $String codigo ERP
 */
function deparaIdErpCrm($NumregERP,$ColunaCRM,$ColunaERP,$TabelaCRM){
    $SqlTabela = "SELECT ".$ColunaCRM." FROM ".$TabelaCRM." WHERE ".$ColunaERP." = '".$NumregERP."'";
    $QryTabela = query($SqlTabela);
    if($ArTabela = farray($QryTabela)){
        return $ArTabela[$ColunaCRM];
    }
    else{
        return false;
    }
}
?>