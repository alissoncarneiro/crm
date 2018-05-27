<?php

set_time_limit(600); /* 10 minutos */
include_once('../../conecta.php');
include_once('../../functions.php');
include_once('../../conecta_odbc_protheus.php');
include_once('../../classes/class.impODBCProtheus.php');

$CnxODBC = odbc_connect($AliasProtheus, $UsuarioProtheus, $SenhaProtheus);
if (!$CnxODBC) {
    echo 'Não foi possível estabelecer uma conexão com o ERP Protheus.';
    exit;
}

$ArDepara = array(
                'f4_codigo'      => 'id_cfop_erp',
                'f4_texto'       => 'nome_cfop'
                );
/*
                'f4_icm'       => 'cd_trib_icm',
                'f4_ipi'       => 'cd_trib_ipi'
                'aliquota-icm'      => 'aliquota_icm',
                'subs-trib'         => 'subs_trib',
                'consum-final'      => 'consum_final',
                'icms-subs-trib'    => 'icms_subs_trib',
                'emite-duplic'      => 'emite_duplic',
                'perc-red-icm'      => 'pct_reducao_icms',
                'perc-red-ipi'      => 'pct_reducao_ipi'
*/
$ArFixos = array();

$ArChaves = array('f4_codigo');

$Imp = new impODBCProtheus();
$Imp->setCnxOdbc($CnxODBC);
$Imp->setTabelaOrigem('sf4' . $CodEmpresaProtheus);
$Imp->setTabelaDestino('is_cfop');
$Imp->setArDepara($ArDepara);
$Imp->setChaves($ArChaves);
$Imp->setArFixos($ArFixos);

$Imp->Importa();
$Imp->mostraResultado();
odbc_close($CnxODBC);
?>