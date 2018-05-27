<?php

echo "*============================================================*<br>";
echo "Carga de Titulos Datasul ERP via ODBC<br>";
echo "*============================================================*<br>";

require("../../../conecta.php");
include_once "../../../funcoes.php";
include_once "../../../functions.php";

// Parâmetros
$dt_base = date("Y-m-d", strtotime(" -1 days"));
$dt_base_atraso = date("Y-m-d", strtotime(" -3 days"));
echo 'Início : ' . date("d/m/Y") . '-' . date("H:i:s") . '<br>';
$i = 0;

$ArrayConf = parse_ini_file('../../../conecta_odbc_erp_datasul.ini',true);
if($sn_erp_datasul_ems5 == '0') {
    $versao_ems = "EMS2";
    $CnxODBC = ConectaODBCErpDatasul($ArrayConf,'titulo');
    if(!$CnxODBC){
        echo 'Não foi possível estabelecer uma conexão com o ERP';
        exit;
    }
}
else{
    $versao_ems = "EMS5";
    $CnxODBC = ConectaODBCErpDatasul($ArrayConf,'tit_acr');
    if(!$CnxODBC){
        echo 'Não foi possível estabelecer uma conexão com o ERP.';
        exit;
    }
}

$sn_primeira_carga = $_GET["sn_carga_completa"];
query("update is_pessoa set sn_inadimplente=0, qtde_titulos_em_atraso = 0");
if ($sn_primeira_carga == '1') {
    query("delete from is_titulo");
}
else {
    // Importar todos os titulos em aberto + os pagos com data maior que a base
    query('delete from is_titulo where vl_saldo > 0 or dt_pagamento between ' . "'" . $dt_base . "' and '" . date("Y-m-d") . "'");
}

if ($versao_ems == "EMS5") {
    /* =========================================================================================================== */
    // EMS5
    /* =========================================================================================================== */
    echo 'Importando Titulos EMS5 : ';

    if ($sn_primeira_carga == '1') {
        $filtro = '';
    } else {
        $filtro = 'where val_sdo_tit_acr > 0 or dat_liquidac_tit_acr between ' . "'" . $dt_base . "' and '" . date("Y-m-d") . "'" ;
    }

    $sql = 'select * from pub."tit_acr" ' . $filtro;

    $q_titulo = odbc_exec($CnxODBC, $sql);

    while ($a_titulo = odbc_fetch_array($q_titulo)) {
        $i++;
        $a_pessoa = farray(query("select numreg from is_pessoa where id_pessoa_erp = '" . $a_titulo["cdn_cliente"] . "'"));
        // A Receber
        if (($a_titulo["val_sdo_tit_acr"] * 1 > 0) && ($a_titulo["dat_vencto_tit_acr"] > $dt_base_atraso) && ($a_titulo["dat_liquidac_tit_acr"] == '9999-12-31')) {
            $situacao = 1;
        }
        // Recebido
        if ($a_titulo["val_sdo_tit_acr"] * 1 == 0) {
            $situacao = 2;
        }
        // Cancelado
        if (($a_titulo["val_sdo_tit_acr"] * 1 > 0) && ($a_titulo["dat_liquidac_tit_acr"] != '9999-12-31')) {
            $situacao = 3;
        }
        // Atrasado
        if (($a_titulo["val_sdo_tit_acr"] * 1 > 0) && ($a_titulo["dat_vencto_tit_acr"] <= $dt_base_atraso) && ($a_titulo["dat_liquidac_tit_acr"] == '9999-12-31')) {
            $situacao = 4;
            query("update is_pessoa set sn_inadimplente=1, qtde_titulos_em_atraso = qtde_titulos_em_atraso + 1 where id_pessoa_erp = '" . $a_titulo["cdn_cliente"] . "'");
            query("update is_pessoa set sn_grupo_inadimplente=1 where id_pertence_grupo = '" . $a_pessoa["numreg"] . "' or numreg = '".$a_pessoa["numreg"]."'");
        }

        $sql_insert = 'INSERT INTO is_titulo (
                        id_pessoa,
                        id_tp_situacao_titulo,
                        id_estabelecimento_erp,
                        id_especie_erp,
                        id_pessoa_erp,
                        id_titulo_erp,
                        n_parcela,
                        id_pedido_erp,
                        dt_emissao ,
                        dt_vencimento,
                        dt_vencimento_original,
                        dt_ult_pagamento,
                        dt_pagamento,
                        vl_titulo,
                        vl_saldo,
                        pct_multa,
                        pct_juros
                        ) VALUES (';
        $sql_insert .= "'" . $a_pessoa["numreg"] . "'," .
                "'" . $situacao . "'," .
                "'" . $a_titulo["cod_estab"] . "'," .
                "'" . $a_titulo["cod_espec_docto"] . "'," .
                "'" . $a_titulo["cdn_cliente"] . "'," .
                "'" . $a_titulo["cod_tit_acr"] . "'," .
                "'" . $a_titulo["cod_parcela"] . "'," .
                "'" . $a_titulo["nr-pedcli"] . "'," .
                "'" . $a_titulo["dat_emis_docto"] . "'," .
                "'" . $a_titulo["dat_vencto_tit_acr"] . "'," .
                "'" . $a_titulo["dat_vencto_origin_tit_acr"] . "'," .
                "'" . $a_titulo["dat_ult_liquidac_tit_acr"] . "'," .
                "'" . $a_titulo["dat_liquidac_tit_acr"] . "'," .
                "'" . $a_titulo["val_origin_tit_acr"] . "'," .
                "'" . $a_titulo["val_sdo_tit_acr"] . "'," .
                "'" . $a_titulo["val_perc_multa_atraso"] . "'," .
                "'" . $a_titulo["val_perc_juros_dia_atraso"] . "'" .
                ")";
        query(str_replace("'9999-12-31'", "NULL", str_replace("''", "NULL", $sql_insert)));
    }
}


if ($versao_ems == "EMS2") {
    /* =========================================================================================================== */
    // EMS2 - Em desenvolvimento
    /* =========================================================================================================== */
    echo 'Importando Titulos EMS2 : ';

    if ($sn_primeira_carga == '1') {
        $filtro = '';
    } else {
        $filtro = 'where "vl-saldo" > 0 or "dt-ult-pagto" between ' . "'" . $dt_base . "' and '" . date("Y-m-d") . "'";
    }

    $sql = 'select * from pub."titulo" ' . $filtro;

    $q_titulo = odbc_exec($CnxODBC, $sql);

    while ($a_titulo = odbc_fetch_array($q_titulo)) {
        $i++;
        $a_pessoa = farray(query("select numreg from is_pessoa where id_pessoa_erp = '" . $a_titulo["cod-emitente"] . "'"));
        // A Receber
        if (($a_titulo["vl-saldo"] * 1 > 0) && ($a_titulo["dt-vencimen"] > $dt_base_atraso) && ($a_titulo["dt-ult-pagto"] == '9999-12-31')) {
            $situacao = 1;
        }
        // Recebido
        if ($a_titulo["vl-saldo"] * 1 == 0) {
            $situacao = 2;
        }
        // Cancelado
        if (($a_titulo["vl-saldo"] * 1 > 0) && ($a_titulo["dt-ult-pagto"] != '9999-12-31')) {
            $situacao = 3;
        }
        // Atrasado
        if (($a_titulo["vl-saldo"] * 1 > 0) && ($a_titulo["dt-vencimen"] <= $dt_base_atraso) && ($a_titulo["dt-ult-pagto"] == '9999-12-31')) {
            $situacao = 4;
            if($a_titulo["cod-esp"]!='AN' )
            {
            query("update is_pessoa set sn_inadimplente=1, qtde_titulos_em_atraso = qtde_titulos_em_atraso + 1 where id_pessoa_erp = '" . $a_titulo["cod-emitente"] . "'");
            query("update is_pessoa set sn_grupo_inadimplente=1 where id_pertence_grupo = '" . $a_pessoa["numreg"] . "' or numreg = '".$a_pessoa["numreg"]."'");
            }
        }

        $sql_insert = 'INSERT INTO is_titulo (
                        id_pessoa,
                        id_tp_situacao_titulo,
                        id_estabelecimento_erp,
                        id_especie_erp,
                        id_pessoa_erp,
                        id_titulo_erp,
                        n_parcela,
                        id_pedido_erp,
                        dt_emissao ,
                        dt_vencimento,
                        dt_vencimento_original,
                        dt_ult_pagamento,
                        dt_pagamento,
                        vl_titulo,
                        vl_saldo,
                        pct_multa,
                        pct_juros
                        ) VALUES (';
        $sql_insert .= "'" . $a_pessoa["numreg"] . "'," .
                "'" . $situacao . "'," .
                "'" . $a_titulo["cod-estabel"] . "'," .
                "'" . $a_titulo["cod-esp"] . "'," .
                "'" . $a_titulo["cod-emitente"] . "'," .
                "'" . $a_titulo["nr-docto"] . "'," .
                "'" . $a_titulo["parcela"] . "'," .
                "'" . $a_titulo["nr-pedcli"] . "'," .
                "'" . $a_titulo["dt-emissao"] . "'," .
                "'" . $a_titulo["dt-vencimen"] . "'," .
                "'" . $a_titulo["dt-vecto-orig"] . "'," .
                "'" . $a_titulo["dt-ult-pagto"] . "'," .
                "'" . $a_titulo["dt-ult-pagto"] . "'," .
                "'" . $a_titulo["vl-original"] . "'," .
                "'" . $a_titulo["vl-saldo"] . "'," .
                "'" . $a_titulo["perc-multa"] . "'," .
                "'" . $a_titulo["perc-juros"] . "'" .
                ")";
        query(str_replace("'9999-12-31'", "NULL", str_replace("''", "NULL", $sql_insert)));
    }

}
query("update is_titulo set id_tp_situacao_titulo=5 where id_especie_erp = 'AN'");
query("update is_pessoa set qtde_max_titulos_em_atraso = qtde_titulos_em_atraso where qtde_max_titulos_em_atraso < qtde_titulos_em_atraso");

/* =========================================================================================================== */
// Fecha Conexões
/* =========================================================================================================== */
echo $i . ' Registro(s) Processado(s) <br>';
echo 'Fim : ' . date("d/m/Y") . '-' . date("H:i:s") . '<br>';

odbc_close($CnxODBC);
?>