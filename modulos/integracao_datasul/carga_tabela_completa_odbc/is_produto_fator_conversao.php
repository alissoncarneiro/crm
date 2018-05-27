<?php
/*
 * is_produto_fator_conversao.php
 * Autor: Alex
 * 26/05/2011 13:20:44
 */
session_start();
set_time_limit(600); /* 10 minutos */
include_once('../../../conecta.php');
include_once('../../../functions.php');
include_once('../../../classes/class.impODBCProgressTable.php');

$ArrayConf = parse_ini_file('../../../conecta_odbc_erp_datasul.ini',true);
$CnxODBC = ConectaODBCErpDatasul($ArrayConf,'item-unid-venda');
if(!$CnxODBC){
    echo 'Não foi possível estabelecer uma conexão com o ERP.';
    exit;
}

class impODBCProgressTableCustom_is_produto_fator_conversao extends impODBCProgressTable{
    public function setValorCustom($ArDados){
        $Coeficiente = str_pad('1',$ArDados['casas_decimais']+1,'0',STR_PAD_RIGHT);
        $TotalUnidades = $Coeficiente / $ArDados['fator_conversao'];
        $TotalUnidades = round($TotalUnidades,0);
        $TotalUnidades = ($TotalUnidades < 1)?1:$TotalUnidades;
        $ArDados['total_unidades'] = $TotalUnidades;
        return $ArDados;
    }
}

$ArDepara = array(
                'it-codigo'     => 'id_produto',
                'un'            => 'id_unid_medida',
                'fator-conver'  => 'fator_conversao',
                'num-casa-dec'  => 'casas_decimais',
                );
$ArChaves = array('it-codigo','un');

$ArCamposObrigatorios = array('id_produto','un');

$Imp = new impODBCProgressTableCustom_is_produto_fator_conversao();
$Imp->setCnxOdbc($CnxODBC);
$Imp->setTabelaOrigem('pub."item-unid-venda"');
$Imp->setTabelaDestino('is_produto_fator_conversao');
$Imp->setArDepara($ArDepara);
$Imp->setChaves($ArChaves);
$Imp->setArCamposObrigatorios($ArCamposObrigatorios);

$Imp->setCampoDepara('id_produto');
$Imp->setCampoDeparaTabelaCRM('is_produto');
$Imp->addCampoDeparaTabelaChaveCRM('id_produto_erp');

$Imp->setCampoDepara('id_unid_medida');
$Imp->setCampoDeparaTabelaCRM('is_unid_medida');
$Imp->addCampoDeparaTabelaChaveCRM('id_unid_medida_erp');

$Imp->Importa();
$Imp->mostraResultado();
odbc_close($CnxODBC);
?>