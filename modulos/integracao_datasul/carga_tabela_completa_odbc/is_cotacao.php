<?php

/*
 * is_cotacao.php
 * Autor: Alex
 * 17/01/2011 14:44
 * -
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
session_start();
include_once('../../../conecta.php');
include_once('../../../functions.php');

$MicroTimeInicio = microtime(true);
$NumregLog = CriaLog('is_cotacao');

$ArrayConf = parse_ini_file('../../../conecta_odbc_erp_datasul.ini',true);
$CnxODBC = ConectaODBCErpDatasul($ArrayConf,'cotacao');
if(!$CnxODBC){
    echo 'Não foi possível estabelecer uma conexão com o ERP.';
    GravaLogDetalhe($NumregLog,'','Não foi possível estabelecer uma conexão com o ERP','','Erro');
    FinalizaLog($NumregLog,$MicroTimeInicio,0,0,1,0,0);
    exit;
}
$QtdeRegistrosCriados       = 0;
$QtdeRegistrosAtualizados   = 0;
$QtdeRegistrosErro          = 0;
$QtdeRegistrosProcessados   = 0;

$SqlCotacaoODBC = "SELECT \"mo-codigo\",\"ano-periodo\",\"cotacao\" FROM pub.\"cotacao\" WHERE \"ano-periodo\" >= '".date("Ym")."'";
$QryCotacaoODBC = odbc_exec($CnxODBC,$SqlCotacaoODBC);
while($ArCotacaoODBC = odbc_fetch_array($QryCotacaoODBC)){
    $Ano = substr($ArCotacaoODBC['ano-periodo'], 0,4);
    if($Ano < 1970){continue;}
    $Mes = substr($ArCotacaoODBC['ano-periodo'], 4,2);
    $ArCotacoes = explode(';', $ArCotacaoODBC['cotacao']);
    $QtdeDiasNoMes = cal_days_in_month(CAL_GREGORIAN, $Mes, $Ano);

    for($i=1;$i<=$QtdeDiasNoMes;$i++){
        $Dia        = str_pad($i, 2, '0', STR_PAD_LEFT);
        $DtCotacao  = $Ano.'-'.$Mes.'-'.$Dia;

        /*
         * Pegando o numreg da moeda
         */
        $SqlMoeda = "SELECT numreg FROM is_moeda WHERE id_moeda_erp = '".$ArCotacaoODBC['mo-codigo']."'";
        $QryMoeda = query($SqlMoeda);
        $ArMoeda = farray($QryMoeda);
        $IdMoeda = $ArMoeda['numreg'];
        if($IdMoeda == ''){
            $QtdeRegistrosErro++;
            continue;
        }

        /*
         * Definindo array de dados para sql
         */
        $ArSqlCotacao = array();
        $ArSqlCotacao['id_moeda']   = $IdMoeda;
        $ArSqlCotacao['dt_cotacao'] =  $DtCotacao;
        $ArSqlCotacao['vl_cotacao'] = ($ArCotacoes[$i-1] != '')?$ArCotacoes[$i-1]:0;

        /*
         * Verificando se a cotação existe no CRM
         */
        $SqlCotacao = "SELECT COUNT(*) AS CNT FROM is_cotacao WHERE id_moeda = '".$IdMoeda."' AND dt_cotacao = '".$DtCotacao."'";
        $QryCotacao = query($SqlCotacao);
        $ArCotacao = farray($QryCotacao);
        if($ArCotacao['CNT'] <= 0){
            $Sql = AutoExecuteSql(TipoBancoDados, 'is_cotacao', $ArSqlCotacao, 'INSERT');
            if(query($Sql)){
                $QtdeRegistrosCriados++;
            }
            else{
                $QtdeRegistrosErro++;
            }
        }
        else{
            $Sql = AutoExecuteSql(TipoBancoDados, 'is_cotacao', $ArSqlCotacao, 'UPDATE',array('id_moeda','dt_cotacao'));
            if(query($Sql)){
                $QtdeRegistrosAtualizados++;
            }
            else{
                GravaLogDetalhe($NumregLog, $Sql, 'Erro de SQL', print_r($ArCotacaoODBC,true), 'Erro');
                $QtdeRegistrosErro++;
            }
        }
        $QtdeRegistrosProcessados++;
    }
}
odbc_close($CnxODBC);

FinalizaLog($NumregLog,$MicroTimeInicio,$QtdeRegistrosCriados,$QtdeRegistrosAtualizados,$QtdeRegistrosErro,0,$QtdeRegistrosProcessados);

echo 'Quantidade de registros criados: '.$QtdeRegistrosCriados.'<br />';
echo 'Quantidade de registros atualizados: '.$QtdeRegistrosAtualizados.'<br />';
echo 'Quantidade de registros com erro: '.$QtdeRegistrosErro.'<br />';
echo 'Quantidade de registros processados: '.$QtdeRegistrosProcessados.'<br />';
echo 'Tempo Gasto: '.(round((microtime(true)-$MicroTimeInicio),2)).' segundos<br />';
?>