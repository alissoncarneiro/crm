<?php

include_once('../../conecta.php');
include_once('../../functions.php');
include_once('../../conecta_odbc_protheus.php');
include_once('../../classes/class.impODBCProtheus.php');

$CnxODBC = odbc_connect($AliasProtheus, $UsuarioProtheus, $SenhaProtheus);
if(!$CnxODBC){
    echo 'Não foi possível estabelecer uma conexão com o ERP Protheus.';
    exit;
}

class impODBCProgressTableCustom_is_tab_preco extends impODBCProtheus{
    public function setValorCustom($ArDados){
        switch($ArDados['sn_ativa']){
            case '1':
                $ArDados['sn_ativa'] = 1;
                break;
            default:
                $ArDados['sn_ativa'] = 0;
                break;
        }
        return $ArDados;
    }
}

$ArDepara = array(
                    'da0_codtab'     => 'id_tab_preco_erp',
                    'da0_descri'     => 'nome_tab_preco',
                    'da0_datde'     => 'dt_vigencia_ini',
                    'da0_datate'     => 'dt_vigencia_fim',
                    'da0_ativo'      => 'sn_ativa'
                );
// 'mo-codigo'     => 'id_moeda',

$ArFixos = array();

$ArChaves = array('da0_codtab');

$Imp = new impODBCProgressTableCustom_is_tab_preco();
$Imp->setCnxOdbc($CnxODBC);
$Imp->setTabelaOrigem('DA0'.$CodEmpresaProtheus);
$Imp->setTabelaDestino('is_tab_preco');
$Imp->setArDepara($ArDepara);
$Imp->setChaves($ArChaves);
$Imp->setArFixos($ArFixos);

$Imp->Importa();
$Imp->mostraResultado();
odbc_close($CnxODBC);
?>