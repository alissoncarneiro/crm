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
    'a3_cod' => 'id_usuario',
    'id_rep' => 'id_representante',
    'a3_nome' => 'nome_usuario',
    'a3_email' => 'email'
);

$ArFixos = array(
    'id_perfil' => '5',
    'idioma' => 'PT',
    'senha' => 'oasis'
);

$ArChaves = array('a3_cod');

$Imp = new impODBCProtheus();
$Imp->setCnxOdbc($CnxODBC);
$Imp->setTabelaOrigem('SA3' . $CodEmpresaProtheus);
$Imp->setSqlOdbcCustom("select a3_cod, a3_cod as id_rep, a3_cod as nome_ab, a3_nome, a3_email from ".'SA3' . $CodEmpresaProtheus);
$Imp->setTabelaDestino('is_usuario');
$Imp->setArDepara($ArDepara);
$Imp->setChaves($ArChaves);
$Imp->setArFixos($ArFixos);

$Imp->Importa();
$Imp->mostraResultado();
odbc_close($CnxODBC);
?>