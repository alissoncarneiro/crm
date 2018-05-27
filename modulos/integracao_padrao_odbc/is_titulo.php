<?php

echo "*============================================================*<br>";
echo "Carga de Titulos Datasul ERP via ODBC<br>";
echo "*============================================================*<br>";

include_once('../../conecta.php');
include_once('../../functions.php');
include_once('../../classes/class.impODBCTable.php');

$CnxODBC = odbc_connect(IntPadODBCServidor, IntPadODBCUsuario, IntPadODBCSenha);
if(!$CnxODBC){
    echo 'Não foi possível estabelecer uma conexão com o ERP.';
    exit;
}

// Parâmetros
$dt_base = date("Y-m-d", strtotime(" -1 days"));
$dt_base_atraso = date("Y-m-d", strtotime(" -3 days"));
echo 'Início : ' . date("d/m/Y-H:i:s") . '<br>';
$i = 0;

$sn_primeira_carga = $_GET["sn_carga_completa"];
query("UPDATE is_pessoa SET sn_inadimplente=0, qtde_titulos_em_atraso = 0");
if($sn_primeira_carga == '1'){
    query("DELETE FROM is_titulo");
}
else{
    // Importar todos os titulos em aberto + os pagos com data maior que a base
    query('DELETE FROM is_titulo WHERE vl_saldo > 0 OR dt_pagamento BETWEEN '."'".$dt_base."' AND '".date("Y-m-d")."'");
}


/* =========================================================================================================== */
// EMS5
/* =========================================================================================================== */
echo 'Importando Titulos EMS5 : ';

if ($sn_primeira_carga == '1') {
    $filtro = '';
} else {
    $filtro = "WHERE (vl_titulo - vl_pago) > 0";
}

$sql = 'SELECT * FROM vw_is_int_titulos_receber '.$filtro;

$q_titulo = odbc_exec($CnxODBC, $sql);

while ($a_titulo = odbc_fetch_array($q_titulo)) {
    $i++;
    $q_pessoa = query("SELECT numreg FROM is_pessoa WHERE id_pessoa_erp = '".$a_titulo["id_pessoa_erp"]."'");
    $a_pessoa = farray($q_pessoa);

    $VlSaldo = $a_titulo['vl_titulo'] - $a_titulo['vl_pago'];
    $situacao = $a_titulo['situacao'];
    // Atrasado
    if (($VlSaldo > 0) && ($a_titulo["dt_vencto_atual"] <= $dt_base_atraso) && ($a_titulo["dt_pagto"] == '')) {
        $situacao = 4;
        query("UPDATE is_pessoa SET sn_inadimplente=1, qtde_titulos_em_atraso = (qtde_titulos_em_atraso + 1) WHERE id_pessoa_erp = '" . $a_titulo["cdn_cliente"] . "'");
        query("UPDATE is_pessoa SET sn_grupo_inadimplente=1 WHERE id_pertence_grupo = '" . $a_pessoa["numreg"] . "' OR numreg = '".$a_pessoa["numreg"]."'");
    }

    $ArSqlInsert = array(
        'id_pessoa'                 => $a_pessoa['numreg'],
        'id_tp_situacao_titulo'     => $situacao,
        'id_estabelecimento_erp'    => '',
        'id_especie_erp'            => '',
        'id_pessoa_erp'             => $a_titulo['id_pessoa_erp'],
        'id_titulo_erp'             => $a_titulo['id_titulo_erp'],
        'n_parcela'                 => $a_titulo['numero_parcela'],
        'id_pedido_erp'             => $a_titulo['id_pedido_erp'],
        'dt_emissao'                => $a_titulo['dt_emissao_titulo'],
        'dt_vencimento'             => $a_titulo['dt_vencto_atual'],
        'dt_vencimento_original'    => $a_titulo['dt_vencto_orig'],
        'dt_ult_pagamento'          => $a_titulo['dt_pagto'],
        'dt_pagamento'              => $a_titulo['dt_pagto'],
        'vl_titulo'                 => $a_titulo['vl_titulo'],
        'vl_saldo'                  => $a_titulo['vl_titulo'] - $a_titulo['vl_pago'],
        'pct_multa'                 => '',
        'pct_juros'                 => ''
    );
    $SqlInsert = AutoExecuteSql(TipoBancoDados, 'is_titulo', $ArSqlInsert, 'INSERT');
    query($SqlInsert);
}

query("UPDATE is_pessoa SET qtde_max_titulos_em_atraso = qtde_titulos_em_atraso WHERE qtde_max_titulos_em_atraso < qtde_titulos_em_atraso");

/* =========================================================================================================== */
// Fecha Conexões
/* =========================================================================================================== */
echo $i . ' Registro(s) Processado(s) <br>';
echo 'Fim : ' . date("d/m/Y") . '-' . date("H:i:s") . '<br>';
odbc_close($CnxODBC);
?>