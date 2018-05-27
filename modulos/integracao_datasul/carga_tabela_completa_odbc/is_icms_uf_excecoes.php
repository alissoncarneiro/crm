<?php
/*
 * is_icms_uf_excecoes.php
 * Autor: Bruno Fonseca
 * 29/12/2010 17:08
 * -
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
session_start();
include_once('../../../conecta.php');
include_once('../../../functions.php');

$MicroTimeInicio = microtime(true);
$QtdeErro = 0;
$NumregLog = CriaLog('is_icms_uf_excecoes');

$ArrayConf = parse_ini_file('../../../conecta_odbc_erp_datasul.ini',true);
$CnxODBC = ConectaODBCErpDatasul($ArrayConf,'unid-feder');
if(!$CnxODBC){
    echo 'Não foi possível estabelecer uma conexão com o ERP.';
    GravaLogDetalhe($NumregLog,'','Não foi possível estabelecer uma conexão com o ERP','','Erro');
    FinalizaLog($NumregLog,$MicroTimeInicio,0,0,1,0,0);
    exit;
}

$sql_uf_excecao = 'select * from pub."unid-feder"';
$QRY_ODBC = odbc_exec($CnxODBC, $sql_uf_excecao);

$atualizados = 0;
$inseridos = 0;

while($ArODBC = odbc_fetch_array($QRY_ODBC)){
    $uf_origem = $ArODBC['estado'];
    $pais_origem = $ArODBC['pais'];
    $pais_destino = $pais_origem;
    
    $ar_uf_destino = array();
    $ar_icms_destino = array();
    $ar_desc_icms = array();


    $ar_uf_destino = explode(';',$ArODBC['est-exc']);
    $ar_icms_destino = explode(';',$ArODBC['perc-exc']);
    $ar_desc_icms = explode(';',$ArODBC['per-desc-icms']);
    foreach($ar_uf_destino as $k=>$v){
        if(!empty($v)){
            $uf_destino = $v;
            $icms_destino = $ar_icms_destino[$k];
            $desc_icms = $ar_desc_icms[$k];
            
            $sql_verifica_existe = 'SELECT numreg FROM is_icms_uf_excecoes WHERE pais = \''.$pais_origem.'\' AND uf_origem = \''.$uf_origem.'\' AND pais_destino = \''.$pais_destino.'\' AND uf_destino = \''.$uf_destino.'\'';
            $qry_verifica_existe = query($sql_verifica_existe);
            $nrows_verifica_existe = numrows($qry_verifica_existe);
            if($nrows_verifica_existe > 0){
                $sql_grava = 'UPDATE is_icms_uf_excecoes SET pct_icms_interestadual = \''.$icms_destino.'\', pct_icms_desconto_icms = \''.$desc_icms.'\' WHERE pais = \''.$pais_origem.'\' AND uf_origem = \''.$uf_origem.'\' AND pais_destino = \''.$pais_destino.'\' AND uf_destino = \''.$uf_destino.'\'';
                $atualizados++;
            } else {
                $sql_grava = 'INSERT INTO is_icms_uf_excecoes (uf_origem, pais, uf_destino, pct_icms_interestadual, pct_icms_desconto_icms, pais_destino) VALUES
                    (\''.$uf_origem.'\', \''.$pais_origem.'\', \''.$uf_destino.'\', \''.$icms_destino.'\', \''.$desc_icms.'\', \''.$pais_destino.'\')';
                $inseridos++;
            }
            
            $qry = query($sql_grava);
            if(!$qry){
                GravaLogDetalhe($NumregLog, $sql_grava, 'Erro de SQL', print_r($ArODBC,true), 'Erro');
            }
        }
    }
}
FinalizaLog($NumregLog,$MicroTimeInicio,$QtdeRegistrosCriados,$QtdeRegistrosAtualizados,$QtdeRegistrosErro,0,$QtdeRegistrosProcessados);
echo '<p align="center">Carga efetuada com sucesso.<br /> Foram inseridos: '.$inseridos.'<br />Foram atualizados: '.$atualizados.'</p>';
?>