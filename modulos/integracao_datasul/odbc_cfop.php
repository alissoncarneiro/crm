<?php
$odbc_c = true;
include('../../conecta.php');
include('../../functions.php');
include('../../classes/class.impODBCProgressTable.php');

$Imp = new impODBCProgressTable();
$Imp->cnxODBC = $cnx1;
$Imp->setTabelaOrigem('pub."natur-oper"');
$Imp->setTabelaDestino('is_cfop');
$Imp->arDepara = array(
                'nat-operacao' 	=> 'id_cfop_erp',
                'denominacao' 	=> 'nome_cfop',
                'cd-trib-icm' 	=> 'cd_trib_icm',
                'aliquota-icm' 	=> 'aliquota_icm',
                'subs-trib' 	=> 'subs_trib',
                'consum-final' 	=> 'consum_final',
                'icms-subs-trib' => 'icms_subs_trib',
                'emite-duplic' 	=> 'emite_duplic',
                'per-red-ipi'   => 'pct_reducao_ipi'
                );
$Imp->Chaves = array('nat-operacao');

$Imp->Importa();
$Imp->mostraResultado();
?>