<?php
/*
 * is_pedido.php
 * Autor: Alex
 * 27/01/2011 15:14:00
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
session_start();
set_time_limit(600); /* 10 minutos */
include_once('../../../conecta.php');
include_once('../../../functions.php');

$QtdeRegistrosCriados = 0;
$QtdeRegistrosAtualizados = 0;
$QtdeRegistrosErro = 0;
$QtdeRegistrosIgnorados = 0;
$QtdeRegistrosProcessados = 0;

$MicroTimeInicio = microtime(true);
$NumregLog = CriaLog('is_pedido');

$ArrayConf = parse_ini_file('../../../conecta_odbc_erp_datasul.ini',true);
$CnxODBC = ConectaODBCErpDatasul($ArrayConf,'ped-venda');
if(!$CnxODBC){
    echo 'Não foi possível estabelecer uma conexão com o ERP.';
    GravaLogDetalhe($NumregLog,'','Não foi possível estabelecer uma conexão com o ERP','','Erro');
    FinalizaLog($NumregLog,$MicroTimeInicio,0,0,1,0,0);
    exit;
}

$SqlPedidosCRM = "SELECT numreg,id_pessoa,id_pedido_cliente FROM is_pedido WHERE id_situacao_pedido IN(1,2,4,5) AND (sn_exportado_erp = 1 OR sn_importado_erp = 1)";
$QryPedidosCRM = query($SqlPedidosCRM);
while($ArPedidoCRM = farray($QryPedidosCRM)){
    $QryPessoaCRM = query("SELECT * FROM is_pessoa WHERE numreg = ".$ArPedidoCRM['id_pessoa']);
    $ArPessoaCRM = farray($QryPessoaCRM);

    $QryPedidoERP = odbc_exec($CnxODBC,"SELECT \"nr-pedcli\",\"nome-abrev\",\"cod-sit-ped\",\"cod-sit-aval\" FROM pub.\"ped-venda\" WHERE \"nome-abrev\" = '".TrataApostrofoBD($ArPessoaCRM['fantasia_apelido'])."' AND \"nr-pedcli\" = '".$ArPedidoCRM['id_pedido_cliente']."'");
    $ArPedidoErp = odbc_fetch_array($QryPedidoERP);

    if($ArPedidoErp['nome-abrev'] == ''){
        continue;
    }

    $QrySituacaoCRM = query("SELECT numreg FROM is_situacao_pedido WHERE id_situacao_pedido_erp = '".$ArPedidoErp['cod-sit-ped']."'");
    $ArSituacaoCRM = farray($QrySituacaoCRM);
    if($ArSituacaoCRM['numreg'] == ''){
        echo 'Relacionamento de Situação com CRM não encontrado. Pedido CRM: '.$ArPedidoCRM['numreg'].'. Situação ERP: '.$ArPedidoErp['cod-sit-ped']."\r\n";
        continue;
    }

    $ArSqlPedido = array();
    $ArSqlPedido['numreg'] = $ArPedidoCRM['numreg'];
    $ArSqlPedido['id_situacao_pedido'] = $ArSituacaoCRM['numreg'];

    /* Tratamento de crédito */
    switch($ArPedidoErp['cod-sit-aval']){
        case '1': /* Não Avaliado */
            $ArSqlPedido['sn_avaliado_credito'] = '0';
            $ArSqlPedido['sn_aprovado_credito'] = '0';
            break;
        case '2': /* Avaliado */
            $ArSqlPedido['sn_avaliado_credito'] = '0';
            $ArSqlPedido['sn_aprovado_credito'] = '0';
            break;
        case '3': /* Aprovado */
            $ArSqlPedido['sn_avaliado_credito'] = '1';
            $ArSqlPedido['sn_aprovado_credito'] = '1';
            break;
        case '4': /* Não Aprovado */
            $ArSqlPedido['sn_avaliado_credito'] = '1';
            $ArSqlPedido['sn_aprovado_credito'] = '0';
            break;
        case '5': /* Pendente de Informação */
            $ArSqlPedido['sn_avaliado_credito'] = '1';
            $ArSqlPedido['sn_aprovado_credito'] = '0';
            break;
    }

    $SqlPedido = AutoExecuteSql(TipoBancoDados,'is_pedido',$ArSqlPedido,'UPDATE',array('numreg'));
    $QryUpdatePedido = query($SqlPedido);
    if(!$QryUpdatePedido){
        $QtdeRegistrosErro++;
        GravaLogDetalhe($NumregLog, $SqlPedido, 'Erro de SQL', print_r($ArPedidoErp,true), 'Erro');
        echo "<span style=\"color:#FF0000;\">Erro sql ao atualizar pedido:</span> ".$ArPessoaCRM['fantasia_apelido']." - ".$ArPedidoCRM['id_pedido_cliente']."<hr>";
        continue;
    }
    /*
     * FIM CABEÇALHO ----------------------------------------------------------------------------------------------------------------------------------
     *
     * ITENS ------------------------------------------------------------------------------------------------------------------------------------------
     */

    $QryItensERP = odbc_exec($CnxODBC,"SELECT \"cod-sit-item\",\"qt-atendida\",\"nr-sequencia\" FROM pub.\"ped-item\" WHERE \"nome-abrev\" = '".TrataApostrofoBD($ArPessoaCRM['fantasia_apelido'])."' AND \"nr-pedcli\" = '".TrataApostrofoBD($ArPedidoCRM['id_pedido_cliente'])."' ORDER BY \"nr-sequencia\" ASC");
    while($ArItemERP = odbc_fetch_array($QryItensERP)){

        $ArSqlPedidoItem = array();
        $ArSqlPedidoItem['qtde_faturada']       = $ArItemERP['qt-atendida'];
        $ArSqlPedidoItem['id_situacao_item']    = $ArItemERP['cod-sit-item'];

        $QryPedidoItemCRM = query("SELECT numreg,id_sequencia FROM is_pedido_item WHERE id_pedido = ".$ArPedidoCRM['numreg']." AND id_sequencia = '".$ArItemERP['nr-sequencia']."'");
        $NumrowsPedidoItemCRM = numrows($QryPedidoItemCRM);
        if($NumrowsPedidoItemCRM == 1){
            $ArPedidoItemCRM = farray($QryPedidoItemCRM);
            $ArSqlPedidoItem['numreg'] = $ArPedidoItemCRM['numreg'];
            $SqlPedidoItem = AutoExecuteSql(TipoBancoDados,'is_pedido_item',$ArSqlPedidoItem,'UPDATE',array('numreg'));
            $QryUpdatePedidoItem = query($SqlPedidoItem);
            if(!$QryUpdatePedidoItem){
                GravaLogDetalhe($NumregLog, $SqlPedidoItem, 'Erro de SQL', print_r($ArItemERP,true), 'Erro');
                echo "<span style=\"color:#FF0000;\">Erro de sql ao atualizar item do pedido:</span> ".$ArPessoaCRM['fantasia_apelido']." - ".$ArPedidoCRM['id_pedido_cliente']." - ".$ArSqlPedidoItem['id_sequencia']."<hr>";
            }
        }
    }
}
FinalizaLog($NumregLog,$MicroTimeInicio,$QtdeRegistrosCriados,$QtdeRegistrosAtualizados,$QtdeRegistrosErro,$QtdeRegistrosIgnorados,$QtdeRegistrosProcessados);
?>