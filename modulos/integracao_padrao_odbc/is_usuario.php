<?php

/*
 * is_usuario.php
 * Autor: Alex
 * 09/02/2011 13:39
 * -
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
set_time_limit(300);
if(!defined('CARGAODBCPADRAO')){
    define('CARGAODBCPADRAO',true);
}
include_once('../../conecta.php');
include_once('../../functions.php');
include_once('../../classes/class.impODBCTable.php');

$CnxODBC = odbc_connect(IntPadODBCServidor, IntPadODBCUsuario, IntPadODBCSenha);
if(!$CnxODBC){
    echo 'Não foi possível estabelecer uma conexão com o ERP.';
    exit;
}

class impODBCTableCustom_is_usuario extends impODBCTable{
    public function setValorCustomInsert($ArDados){
        $ArDados['id_representante']       = $ArDados['id_usuario'];
        return $ArDados;
    }
}

$ArDepara = array(
                    'id_erp'            => 'id_usuario',
                    'nome'              => 'nome_usuario',
                    'email'             => 'email'
                );
$ArFixosInsert = array(
                    'idioma'                            => 'PT',
                    'id_perfil'                         => 5,
                    'id_cargo'                          => 0,
                    'custo_hr_normal'                   => 0,
                    'custo_hr_extra'                    => 0,
                    'id_calendario'                     => 0,
                    'nome_abreviado'                    => NULL,
                    'tel1'                              => NULL,
                    'tel2'                              => NULL,
                    'sn_trans_prospect_cliente'         => 0,
                    'sn_permite_alterar_prazo_todos'    => 0,
                    'sn_trans_suspect_prospect'         => 0,
                    'sn_permite_editar_cliente'         => 0,
                    'sn_permite_alterar_preco_venda'    => 0,
                    'sn_permite_aprovar_venda'          => 0,
                    'sn_permite_reprovar_venda'         => 0,
                    'sn_permite_alterar_cfop_item'      => 0,
                    'sn_permite_alterar_comis_part'     => 0,
                    'sn_permite_add_particip_venda'     => 0
                );

$ArChaves = array('id_erp');

$ArCamposObrigatorios = array('id_erp','nome');

$Imp = new impODBCTableCustom_is_usuario(IntPadODBCTipoBD);
$Imp->setCnxOdbc($CnxODBC);
$Imp->setTabelaOrigem('vw_is_int_vendedores');
$Imp->setTabelaDestino('is_usuario');
$Imp->setArDepara($ArDepara);
$Imp->setChaves($ArChaves);
$Imp->setArFixosInsert($ArFixosInsert);
$Imp->setArCamposObrigatorios($ArCamposObrigatorios);

$Imp->Importa();
$Imp->mostraResultado();
odbc_close($CnxODBC);
?>