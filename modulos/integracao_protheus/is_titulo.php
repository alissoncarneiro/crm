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
                    'e1_cliente'                 => 'id_pessoa',
                    'e1_naturez'              => 'id_especie_erp',
                    'e1_num'              => 'id_titulo_erp',
                    'e1_parcela'              => 'n_parcela',
                    'e1_pedido'              => 'id_pedido_erp',
                    'e1_emissao'              => 'dt_emissao',
                    'e1_vencto'              => 'dt_vencimento',
                    'e1_baixa'              => 'dt_pagamento',
                    'e1_valor'              => 'vl_titulo'
                    );

$ArFixos = array();

$ArChaves = array('e1_num');

$Imp = new impODBCProtheus();
$Imp->setCnxOdbc($CnxODBC);
$Imp->setTabelaOrigem('se1' . $CodEmpresaProtheus);
$Imp->setTabelaDestino('is_titulo');
$Imp->setArDepara($ArDepara);
$Imp->setChaves($ArChaves);
$Imp->setArFixos($ArFixos);

$Imp->setCampoDepara('id_pessoa');
$Imp->setCampoDeparaTabelaCRM('is_pessoa');
$Imp->addCampoDeparaTabelaChaveCRM('id_pessoa_erp');

$Imp->Importa();
$Imp->mostraResultado();
odbc_close($CnxODBC);
?>