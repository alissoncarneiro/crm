<?php
/*
 * is_estabelecimentos.php
 * Autor: Anderson
 * 05/12/2010 16:16
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
$CnxODBC = ConectaODBCErpDatasul($ArrayConf,'estabelec');
if(!$CnxODBC){
    echo 'Não foi possível estabelecer uma conexão com o ERP.';
    exit;
}

class impODBCProgressTableCustom_is_estabelecimento extends impODBCProgressTable{
    public function setValorCustom($ArDados){
        $ArDados['nome_estabelecimento'] = '('.$ArDados['id_estabelecimento_erp'].') '.$ArDados['nome_estabelecimento'];
        return $ArDados;
    }
}

$ArDepara = array(
                'nome'          => 'nome_estabelecimento',
                'cod-estabel'   => 'id_estabelecimento_erp',
                'estado'        => 'uf',
                'pais'          => 'pais'
                );
$ArChaves = array('cod-estabel');

$Imp = new impODBCProgressTableCustom_is_estabelecimento();
$Imp->setCnxOdbc($CnxODBC);
$Imp->setTabelaOrigem('pub."estabelec"');
$Imp->setTabelaDestino('is_estabelecimento');
$Imp->setArDepara($ArDepara);
$Imp->setChaves($ArChaves);

$Imp->Importa();
$Imp->mostraResultado();
odbc_close($CnxODBC);
?>