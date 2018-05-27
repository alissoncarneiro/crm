<?php

/*
 * is_cfop.php
 * Autor: Alex
 * 28/11/2010 09:04
 * -
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
session_start();
include_once('../../../conecta.php');
include_once('../../../functions.php');
include_once('../../../classes/class.impODBCProgressTable.php');

$ArrayConf = parse_ini_file('../../../conecta_odbc_erp_datasul.ini',true);
$CnxODBC = ConectaODBCErpDatasul($ArrayConf,'natur-oper');
if(!$CnxODBC){
    echo 'Não foi possível estabelecer uma conexão com o ERP.';
    exit;
}
$ArDepara = array(
                'nat-operacao'      => 'id_cfop_erp',
                'denominacao'       => 'nome_cfop',
                'cd-trib-icm'       => 'cd_trib_icm',
                'cd-trib-ipi'       => 'cd_trib_ipi',
                'aliquota-icm'      => 'aliquota_icm',
                'subs-trib'         => 'subs_trib',
                'consum-final'      => 'consum_final',
                'icms-subs-trib'    => 'icms_subs_trib',
                'emite-duplic'      => 'emite_duplic',
                'perc-red-icm'      => 'pct_reducao_icms',
                'perc-red-ipi'      => 'pct_reducao_ipi',
                'char-2'            => 'char-2'
                );
$ArChaves = array('nat-operacao');

class impODBCProgressTable_is_cfop extends impODBCProgressTable{
    public function setValorCustom($ArDados){
        $Char2 = $ArDados['char-2'];
        unset($ArDados['char-2']);
        $ArDados['base_ipi'] = substr($Char2,10,1);
        $ArDados['pct_desc_icms_zf'] = TrataFloatPost(substr($Char2,65,5));
        return $ArDados;
    }
}

$Imp = new impODBCProgressTable_is_cfop();
$Imp->setCnxOdbc($CnxODBC);
$Imp->setTabelaOrigem('pub."natur-oper"');
$Imp->setTabelaDestino('is_cfop');
$Imp->setArDepara($ArDepara);
$Imp->setChaves($ArChaves);

$Imp->Importa();
$Imp->mostraResultado();
odbc_close($CnxODBC);
?>