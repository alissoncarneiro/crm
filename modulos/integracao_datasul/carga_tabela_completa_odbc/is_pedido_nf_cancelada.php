<?php
/*
 * is_pedido_nf_cancelada.php
 * Autor: Alex
 * 07/08/2012 11:34:15
 */
session_start();
set_time_limit(600); /* 10 minutos */
include_once('../../../conecta.php');
include_once('../../../functions.php');
include_once('../../../classes/class.impODBCProgressTable.php');
define("IntegracaoPedidoNFCancelada",true);

$MicroTimeInicio = microtime(true);
$QtdeErro = 0;
$NumregLog = CriaLog('is_pedido_nf_cancelada');

$ArrayConf = parse_ini_file('../../../conecta_odbc_erp_datasul.ini',true);
$CnxODBCNF = ConectaODBCErpDatasul($ArrayConf,'nota-fiscal');
if(!$CnxODBCNF){
    $Msg = 'Não foi possível estabelecer uma conexão com o ERP (nota-fiscal).';
    GravaLogDetalhe($NumregLog,'',$Msg,'','Erro');
    FinalizaLog($NumregLog,$MicroTimeInicio,0,0,1,0,0);
    echo $Msg;
    exit;
}
$ArrayConf = parse_ini_file('../../../conecta_odbc_erp_datasul.ini',true);
$CnxODBCPed = ConectaODBCErpDatasul($ArrayConf,'ped-venda');
if(!$CnxODBCPed){
    $Msg = 'Não foi possível estabelecer uma conexão com o ERP (ped-venda).';
    GravaLogDetalhe($NumregLog,'',$Msg,'','Erro');
    FinalizaLog($NumregLog,$MicroTimeInicio,0,0,1,0,0);
    echo $Msg;
    exit;
}
$DtBaseCarga = date("Y-m-d",strtotime(date("Y-m-d")." -1 day"));
$DtBaseCarga = ($_GET['dt_base_carga'] == '')?$DtBaseCarga:$_GET['dt_base_carga'];

$QtdeProcessada = 0;

$SqlNotasCanceladas = "SELECT \"cod-emitente\",\"nr-pedcli\",\"dt-cancela\",\"desc-cancela\" FROM pub.\"nota-fiscal\" WHERE \"dt-cancela\" = '".$DtBaseCarga."'";
$QryNotasCanceladas = odbc_exec($CnxODBCNF,$SqlNotasCanceladas);
while($ArNotasCanceladas = odbc_fetch_array($QryNotasCanceladas)){
    $IdPedidoCliente = $ArNotasCanceladas['nr-pedcli'];
    $IdClienteERP = $ArNotasCanceladas['cod-emitente'];
    
    $SqlPedidoERP = "SELECT \"cod-sit-ped\" FROM pub.\"ped-venda\" WHERE \"cod-emitente\" = '".$IdClienteERP."' AND \"nr-pedcli\" = '".TrataApostrofoBD($IdPedidoCliente)."'";
    $QryPedidoERP = odbc_exec($CnxODBCPed,$SqlPedidoERP);
    $ArPedidoERP = odbc_fetch_array($QryPedidoERP);
    if(!$ArPedidoERP){
        GravaLogDetalhe($NumregLog,'','Pedido '.TrataApostrofoBD($IdPedidoCliente).' não encontrado no ERP!','','Erro');
        continue;
    }
    
    include('is_pedido_nf_canceladas_custom.php');
    
    $SqlPedidoCRM = "SELECT t1.numreg FROM is_pedido t1 INNER JOIN is_pessoa t2 ON t1.id_pessoa = t2.numreg WHERE t2.id_pessoa_erp = '".$IdClienteERP."' AND t1.id_pedido_cliente = '".TrataApostrofoBD($IdPedidoCliente)."'";
    $QryPedidoCRM = query($SqlPedidoCRM);
    $ArPedidoCRM = farray($QryPedidoCRM);
    if(!$ArPedidoCRM){
        GravaLogDetalhe($NumregLog,'','Pedido '.TrataApostrofoBD($IdPedidoCliente).' não encontrado no CRM!','','Erro');
        continue;
    }   
    $SqlUpdatePedidoCRM = "UPDATE is_pedido SET id_situacao_pedido = '".$ArPedidoERP['cod-sit-ped']."' WHERE numreg = '".$ArPedidoCRM['numreg']."'";    
    query($SqlUpdatePedidoCRM);
    $QtdeProcessada++;
}
FinalizaLog($NumregLog,$MicroTimeInicio,0,0,0,0,$QtdeProcessada);
?>