<?php
/*
 * is_produto_embalagem.php
 * Autor: Alex
 * 11/01/2011 11:00
 * -
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
session_start();
set_time_limit(600); /* 10 minutos */
include_once('../../../conecta.php');
include_once('../../../functions.php');
include_once('../../../classes/class.impODBCProgressTable.php');

$ArrayConf = parse_ini_file('../../../conecta_odbc_erp_datasul.ini',true);
$CnxODBC = ConectaODBCErpDatasul($ArrayConf,'embalag');
if(!$CnxODBC){
    echo 'Não foi possível estabelecer uma conexão com o ERP.';
    exit;
}

$ArDepara = array(
                'sigla-emb'     => 'sigla',
                'descricao'     => 'nome_embalagem',
                'embalagem'     => 'id_embalagem_erp',
                'narrativa'     => 'narrativa',
                'peso-embal'    => 'peso',
                'volume'        => 'volume',
                'emite-roman'   => 'sn_emite_etiqueta'
                );
$ArChaves = array('embalagem');

$Imp = new impODBCProgressTable();
$Imp->setCnxOdbc($CnxODBC);
$Imp->setTabelaOrigem('pub."embalag"');
$Imp->setTabelaDestino('is_embalagem');
$Imp->setArDepara($ArDepara);
$Imp->setChaves($ArChaves);

$Imp->Importa();
$Imp->mostraResultado();
odbc_close($CnxODBC);
?>