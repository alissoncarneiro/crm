<?php

echo '*============================================================*<br>
      Carga de Tabela de Parametros de CFOP Datasul ERP via ODBC<br>
      *============================================================*<br>';

require('../../conecta.php');
include('../../funcoes.php');
include('../../functions.php');

$alias_odbc = "mgalpha_prod";
$tabela_erp = 'pub."es-cfop-param"'; //
$campo_chave_erp = '';
$campo_descr_erp = '';

$tabela_crm = 'is_param_cfop';
$campo_chave_crm = '';

query('DELETE FROM '.$tabela_crm);

$id_usuario = 'IMPORT';
//Conecta com os bancos ODBC
$cnx_erp = odbc_connect($alias_odbc,'sysprogress','sysprogress') or die('Erro na conexão com o Database');
/* =========================================================================================================== */
// Importa Clientes
/* =========================================================================================================== */
echo 'Importando Registros<br>';

$ar_depara = array(
    'cod-cfop-oper'     => 'id_cfop_oper',
    'cod-estabel'       => 'id_pedido_estabelecimento',
    'estado'            => 'pessoa_uf',
    'cidade'            => 'pessoa_cidade',
    'cod-gr-cli'        => 'id_pessoa_grupo_cliente',
    'ge-codigo'         => 'id_produto_grupo_estoque',
    'fm-cod-com'        => 'id_produto_familia_comercial',
    'fm-codigo'         => 'id_produto_familia',
    'it-codigo'         => 'id_produto',
    'nat-operacao-de'   => 'cfop_estadual',
    'nat-operacao-fe'   => 'cfop_interestadual',
    'nat-operacao-in'   => 'cfop_internacional',
    'nr-pontos'         => 'pontos',
    'ind-contrib-icms'  => 'sn_contribuinte_icms',
    'ind-natureza'      => 'id_tp_pessoa',
    'ind-dest-mercad'   => 'id_pedido_dest_merc',
    'cod-canal-venda'   => 'id_pessoa_canal_venda',
    'tp-pedido'         => 'id_pedido_tp_venda',
    'cod-tp-cliente'    => 'id_tp_cliente',
    'cod-tp-item'       => 'id_tp_item');

$ar_fixos = array();
$sql = 'SELECT * FROM '.$tabela_erp;
echo 'Buscando Registros ',date('H:i:s'),'<br>';
echo $sql;
$q_erp = odbc_exec($cnx_erp,$sql);
if(!$q_erp){
    echo odbc_errormsg().$sql;
}
$u = 0;
$i = 0;
while($a_erp = odbc_fetch_array($q_erp)){
    $ar_insert = array();
    foreach($ar_depara as $k => $v){
        if($k == 'nat-operacao-de' || $k == 'nat-operacao-fe' || $k == 'nat-operacao-in'){
            $ncfop = farray(query('select numreg from is_cfop where id_cfop_erp = \''.$a_erp[$k].'\''));
            $ar_insert[$v] = $ncfop['numreg'];
        } else if($a_erp[$k] != ''){
            //Esse pedaço de código é ESPECIFICO para a ALPHA PRINT, caso precise, descomentar as linhas comentadas a baixo e comentar as 5 próximas.
              $ar_insert[$v] = $a_erp[$k];
            //Trecho OFICIAL
            //$ar_insert[$v] = $a_erp[$k]; //descomentar se não for ALPHAPRINT
        }
        $ar_insert['sn_ativo'] = '1';
        $ar_insert['dthr_validade_ini'] = '2010-01-01';
        $ar_insert['dthr_validade_fim'] = '2099-01-01';

    }
    $sql_insert = autoExecuteSql('sqlserver',$tabela_crm,$ar_insert,'INSERT');
    //echo $sql_insert,'<br>';
    if(!query($sql_insert)){
        echo $sql_insert;
    } 
    $i = $i + 1;
}

/* =========================================================================================================== */
// Fecha Conexões
/* =========================================================================================================== */

odbc_close($cnx_erp);
echo 'Fim do Processamento : Total',($u + $i),' Inclusões : '.$i.' Atualizações : '.$u.' '.date('H:i:s');
?>