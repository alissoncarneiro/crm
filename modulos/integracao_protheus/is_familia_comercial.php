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
                'bm_grupo' 	=> 'id_familia_erp',
                'bm_desc'     => 'nome_familia_comercial'
                );
$ArFixos = array('sn_ativo' => 1);
$ArChaves = array('bm_grupo');

$Imp = new impODBCProtheus();
$Imp->setCnxOdbc($CnxODBC);
$Imp->setTabelaOrigem('sbm' . $CodEmpresaProtheus);
$Imp->setTabelaDestino('is_familia_comercial');
$Imp->setArDepara($ArDepara);
$Imp->setChaves($ArChaves);
$Imp->setArFixos($ArFixos);

$Imp->Importa();
$Imp->mostraResultado();
odbc_close($CnxODBC);
?>