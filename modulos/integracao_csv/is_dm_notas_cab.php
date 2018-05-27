<?php
header("Content-Type: text/html; charset=ISO-8859-1");
@session_start();
set_time_limit(6000); /* 100 minutos */
include_once('../../conecta.php');
include_once('../../functions.php');
include_once('../../classes/class.csvimport.php');

$a_param_csv = farray(query("select * from is_parametros_sistema where id_parametro = 'diretorio_csv_raiz'"));


echo '========================================================='.'<BR>';
echo 'IMPORTANDO CABECALHO DE NF : '. date("H:i:s").'<BR>';
echo '========================================================='.'<BR>';

$Imp = new csvimport;
$subdir = 'nf_cab';
$Imp->Diretorio = $a_param_csv["parametro"].'erp/'.$subdir.'/pendentes_integracao/';
$Imp->DiretorioErro = $a_param_csv["parametro"].'erp/'.$subdir.'/com_erros/';
$Imp->DiretorioOK = $a_param_csv["parametro"].'erp/'.$subdir.'/backups/';

$Imp->ProcuraArquivo();

$ArDepara = array(
    '0' => 'cod_estabel',
    '1' => 'serie',
    '2' => 'nr_nota_fis',
    '3' => 'dt_emis_nota',
    '4' => 'nr_pedido',
    '5' => 'cd_emitente',
    '6' => 'nat_operacao',
//    '7' => 'id_situacao',
    '8' => 'vl_tot',
    '9' => 'vl_icms',
    '10' => 'vl_ipi',
    '11' => 'vl_pis',
    '12' => 'vl_cofins',
    '13' => 'vl_csll',
    '14' => 'vl_st',
    '15' => 'vl_iss',
    '16' => 'vl_ir',
    '17' => 'vl_comissoes',
    '18' => 'vl_frete',
    '19' => 'peso_bruto',
    '20' => 'cod_repr'
);

$ArFixos = array();

$ArChaves = array('0','1','2');

$Imp->setTabelaDestino('is_dm_notas_cab');
$Imp->setArDepara($ArDepara);
$Imp->setArFixos($ArFixos);
$Imp->setChaves($ArChaves);

$Imp->Importa();
$Imp->mostraResultado();

echo '========================================================='.'<BR>';
echo 'FIM DE PROCESSAMENTO : '. date("H:i:s").'<BR>';
echo '========================================================='.'<BR>';

?>