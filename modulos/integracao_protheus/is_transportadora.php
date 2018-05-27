<?php
set_time_limit(600); /* 10 minutos */
include_once('../../conecta.php');
include_once('../../functions.php');
include_once('../../conecta_odbc_protheus.php');
include_once('../../classes/class.impODBCProtheus.php');

$CnxODBC = odbc_connect($AliasProtheus, $UsuarioProtheus, $SenhaProtheus);
if(!$CnxODBC){
    echo 'Não foi possível estabelecer uma conexão com o ERP Protheus.';
    exit;
}

$ArDepara = array(
                    'a4_cod'    => 'id_transportadora_erp',
                    'a4_nome'          => 'nome_transportadora',
                    'a4_nreduz'    => 'nome_abrev_transportadora'
                );

$ArFixos = array();

$ArChaves = array('a4_cod');


$Imp = new impODBCProtheus();
$Imp->setCnxOdbc($CnxODBC);
$Imp->setTabelaOrigem('SA4'.$CodEmpresaProtheus);
$Imp->setTabelaDestino('is_transportadora');
$Imp->setArDepara($ArDepara);
$Imp->setChaves($ArChaves);
$Imp->setArFixos($ArFixos);

$Imp->Importa();
$Imp->mostraResultado();
odbc_close($CnxODBC);
?>