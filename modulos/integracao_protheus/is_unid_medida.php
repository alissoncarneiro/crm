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
    'ah_unime' => 'id_unid_medida_erp',
    'ah_descpo' => 'nome_unid_medida'
);

$ArFixos = array();

$ArChaves = array('ah_unime');

$Imp = new impODBCProtheus();
$Imp->setCnxOdbc($CnxODBC);
$Imp->setTabelaOrigem('sah' . $CodEmpresaProtheus);
$Imp->setTabelaDestino('is_unid_medida');
$Imp->setArDepara($ArDepara);
$Imp->setChaves($ArChaves);
$Imp->setArFixos($ArFixos);

$Imp->Importa();
$Imp->mostraResultado();
odbc_close($CnxODBC);
?>