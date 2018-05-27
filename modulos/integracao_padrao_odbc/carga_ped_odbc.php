<?php

set_time_limit(7200);

echo "*============================================================*<br>";
echo "Carga de Pedidos Datasul ERP via ODBC<br>";
echo "*============================================================*<br>";

require("../../conecta.php");
include "../../funcoes.php";

// Carregando Parâmetros
$ap = farray(query("select * from is_dm_param"));

$mov2dis = $ap["odbc_nf"];
$ems2adm = $ap["odbc_emit"];
$ems2dis = $ap["odbc_canal"];
$ems2ind = $ap["odbc_item"];
$ems2uni = $ap["odbc_moeda"];
$id_moeda = $ap["id_moeda"];

$nat_opers = "'" . str_replace(",", "','", str_replace(" ", "", $ap["nat_opers"])) . "'";
$dt_base = $ap["dt_base_ped"];
$dt_base_fim = $ap["dt_base_ped_fim"];


if ($_GET["apagar"] == "S") {
    exit;
}

$id_usuario = 'IMPORT';
//Conecta com os bancos ODBC
$cnx_nf = odbc_connect($mov2dis, "sysprogress", "sysprogress") or die("Erro na conexão com o Database");
$cnx_emit = odbc_connect($ems2adm, "sysprogress", "sysprogress") or die("Erro na conexão com o Database");
$cnx_canal = odbc_connect($ems2dis, "sysprogress", "sysprogress") or die("Erro na conexão com o Database");
$cnx_item = odbc_connect($ems2ind, "sysprogress", "sysprogress") or die("Erro na conexão com o Database");
$cnx_moeda = odbc_connect($ems2uni, "sysprogress", "sysprogress") or die("Erro na conexão com o Database");

// Checando Naturezas Válidas
$nat_opers = "";
$q_natureza = odbc_exec($cnx_emit, "select \"nat-operacao\" from pub.\"natur-oper\" where \"emite-duplic\" = 1");
while ($a_natureza = odbc_fetch_array($q_natureza)) {
    $nat_opers .= $a_natureza["nat-operacao"] . ',';
}
$nat_opers = substr($nat_opers, 0, strlen($nat_opers) - 1);
$nat_opers = "'" . str_replace(",", "','", str_replace(" ", "", $nat_opers)) . "'";

// Excluir qualquer NF fora nas Naturezas de Operacao parametrizadas ou com data de emissao superior a que sera feita a importacao
query("delete from is_dm_pedidos where not nat_operacao in (" . $nat_opers . ")");
query("delete from is_dm_pedidos where dt_emis_nota >= '" . $dt_base . "' and dt_emis_nota <= '" . $dt_base_fim . "'");

/* =========================================================================================================== */
// Importa NFs
/* =========================================================================================================== */
echo 'Importando Itens de Pedido :<br>';

$c = 'pub."ped-venda"';
$sql = 'select ' . $c . '."cod-estabel", 0 as "nr-nota-fis", "nr-sequencia", "it-codigo","qt-pedida", "vl-preuni","vl-tot-it", 0 as "val-ipi", 0 as "val-frete", "vl-liq-it",i."nat-operacao", ' . $c . '."dt-emissao", i."dt-entorig", ' . $c . '."nr-pedcli", ' . $c . '."nr-pedido","cod-emitente",i."nome-abrev", "cod-sit-item" from pub."ped-item" as i, ' . $c . ' where i."nome-abrev" = ' . $c . '."nome-abrev" and i."nr-pedcli" = ' . $c . '."nr-pedcli" and "dt-emissao" >= ' . "'" . $dt_base . "'" . ' and "dt-emissao" <= ' . "'" . $dt_base_fim . "' and " . 'i."nat-operacao" in (' . $nat_opers . ') order by ' . $c . '."nr-pedido"';

echo "Buscando Pedidos " . date("H:i:s") . '<br>';
$q_it_ped = odbc_exec($cnx_nf, $sql);
$ped_refer = "";

while ($a_it_ped = odbc_fetch_array($q_it_ped)) {

    // Se mudou a NF
    if ($ped_refer != $a_it_ped["nr-pedido"]) {

        echo $a_it_ped["nr-pedido"] . ' - ' . $a_it_ped["nr-sequencia"] . " " . $nome_emitente . " " . $a_it_ped["dt-emissao"] . '<br>';

        $ped_refer = $a_it_ped["nr-pedido"];
        $a_emit = odbc_fetch_array(odbc_exec($cnx_emit, 'select "nome-emit", "natureza", "cgc", "atividade", "pais", "estado", "cidade", "nome-mic-reg", "cod-gr-cli", "cod-canal-venda","cod-rep" from pub."emitente" where "cod-emitente" = ' . "'" . $a_it_ped["cod-emitente"] . "'"));
        $a_pessoa_crm = farray(query("select numreg from is_pessoa where cnpj_cpf = '" . $a_emit["cgc"] . "'"));
        $a_produto_crm = farray(query("select numreg from is_produto where id_produto_erp = '" . $a_it_nf["it-codigo"] . "'"));

        //moedas
        $dt_refer = $a_it_ped["dt-emissao"];
        $a_moeda = odbc_fetch_array(odbc_exec($cnx_moeda, "SELECT cotacao FROM pub.cotacao WHERE \"mo-codigo\"='" . $id_moeda . "' AND \"ano-periodo\" = '" . substr($dt_refer, 0, 4) . substr($dt_refer, 5, 2) . "'"));
        $str_cotacoes = $a_moeda['cotacao'];
        $array_cotacoes = explode(";", $str_cotacoes);
        $ind_array_cotacoes = (substr($dt_refer, 8, 2) * 1) - 1;
        $cotacao_dia_anterior = $array_cotacoes[$ind_array_cotacoes] * 1;
        if ($cotacao_dia_anterior <= 0) {
            $cotacao_dia_anterior = 1;
        }

        $natureza = $a_emit["natureza"];
        $nome_emitente = str_replace('"', " ", str_replace("'", " ", $a_emit["nome-emit"]));
        $nome_abrev = str_replace('"', " ", str_replace("'", " ", $a_it_ped["nome-abrev"]));
        $cnpj = $a_emit["cgc"];
        $nome_ramo = str_replace('"', " ", str_replace("'", " ", $a_emit["atividade"]));
        $nome_pais = str_replace('"', " ", str_replace("'", " ", $a_emit["pais"]));
        $nome_estado = str_replace('"', " ", str_replace("'", " ", $a_emit["estado"]));
        $nome_cidade = str_replace('"', " ", str_replace("'", " ", $a_emit["cidade"]));
        $micro_regiao = str_replace('"', " ", str_replace("'", " ", $a_emit["nome-mic-reg"]));
        $nome_canal = "";
        $nome_grupo = "";
        $cod_repr = "";
        $nome_repr = "";

        if ($a_emit["cod-gr-cli"]) {
            $a_gr_cli = odbc_fetch_array(odbc_exec($cnx_emit, 'select "descricao" from pub."gr-cli" where "cod-gr-cli" = ' . "'" . $a_emit["cod-gr-cli"] . "'"));
            $nome_grupo = str_replace('"', " ", str_replace("'", " ", $a_gr_cli["descricao"]));
        }

        if ($a_emit["cod-rep"]) {
            $a_repr = odbc_fetch_array(odbc_exec($cnx_emit, 'select "nome" from pub."repres" where "cod-rep" = ' . "'" . $a_emit["cod-rep"] . "'"));
            $cod_repr = $a_emit["cod-rep"];
            $nome_repr = str_replace('"', " ", str_replace("'", " ", $a_repr["nome"]));
        }

        if ($a_emit["cod-canal-venda"]) {
            $a_canal = odbc_fetch_array(odbc_exec($cnx_canal, 'select "descricao" from pub."canal-venda" where "cod-canal-venda" = ' . "'" . $a_emit["cod-canal-venda"] . "'"));
            $nome_canal = str_replace('"', " ", str_replace("'", " ", $a_canal["descricao"]));
        }
    }


    $nome_familia = "";
    $nome_familia_com = "";
    $it_nome = "";
    $linha = $a_it_ped["qt-pedida"];
    $aqtde = explode(';', $linha);
    $vl_tot_item_us = $a_it_ped["vl-tot-it"] / $cotacao_dia_anterior;
    $vl_merc_sicm_us = $a_it_ped["vl-liq-it"] / $cotacao_dia_anterior;
    $vl_merc_liq = (($a_it_ped["vl-tot-it"] * 1) - ($a_it_ped["val-ipi"] * 1));
    $vl_merc_liq_us = $vl_merc_liq / $cotacao_dia_anterior;
    $vl_frete_it_us = ($a_it_ped["val-frete"] * 1) / $cotacao_dia_anterior;


    if ($a_it_ped["it-codigo"]) {
        $a_item = odbc_fetch_array(odbc_exec($cnx_item, 'select "desc-item", "fm-codigo", "fm-cod-com" from pub."item" where "it-codigo" = ' . "'" . $a_it_ped["it-codigo"] . "'"));
        $it_nome = str_replace('"', " ", str_replace("'", " ", $a_item["desc-item"]));
        $nome_familia = $a_item["fm-codigo"];

        $a_fam = odbc_fetch_array(odbc_exec($cnx_item, 'select "descricao" from pub."familia" where "fm-codigo" = ' . "'" . $a_item["fm-codigo"] . "'"));
        $nome_familia = str_replace('"', " ", str_replace("'", " ", $a_fam["descricao"]));

        $a_fam_com = odbc_fetch_array(odbc_exec($cnx_canal, 'select "descricao" from pub."fam-comerc" where "fm-cod-com" = ' . "'" . $a_item["fm-cod-com"] . "'"));
        $nome_familia_com = str_replace('"', " ", str_replace("'", " ", $a_fam_com["descricao"]));
    }


    $sql_insert = 'INSERT INTO is_dm_pedidos ( dt_cadastro , hr_cadastro , id_usuario_cad , dt_alteracao , hr_alteracao , id_usuario_alt , id_pessoa, id_produto, cod_estabel , serie , dt_emis_nota , dt_entorig, nr_nota_fis , nr_seq_fat , nr_pedido , nr_pedcli , nome_familia , it_codigo , it_nome , cd_emitente , nome_emitente , nome_fantasia , nome_grupo , nome_canal , nome_ramo , peso_bruto , qt_faturada , vl_tot_item , vl_merc_sicm , vl_merc_liq, vl_frete_it, nat_operacao , vl_tot_item_us , vl_merc_sicm_us , vl_merc_liq_us, vl_frete_it_us, nome_familia_com , cnpj , natureza , nome_pais , nome_estado , nome_cidade, nome_regiao, cod_repr, nome_repr ) VALUES (';

    $sql_insert .= "'" . date("Y-m-d") . "','" . date("H:i:s") . "','" . $id_usuario . "','" . date("Y-m-d") . "','" . date("H:i:s") . "','" . $id_usuario . "',";
    $sql_insert .= ($a_pessoa_crm["numreg"]?$a_pessoa_crm["numreg"]:'NULL') . "," . ($a_produto_crm["numreg"]?$a_produto_crm["numreg"]:'NULL') . "','" . $a_it_ped["cod-estabel"] . "','" . $a_it_ped["serie"] . "','" . $a_it_ped["dt-emissao"] . "','" . $a_it_ped["dt-entorig"] . "','" . $a_it_ped["nr-nota-fis"] . "','" . $a_it_ped["nr-sequencia"] . "','" . $a_it_ped["nr-pedido"] . "','" . $a_it_ped["nr-pedcli"] . "','" . trata_acentos($nome_familia) . "','" . $a_it_ped["it-codigo"] . "','" . trata_acentos($it_nome) . "','" . $a_it_ped["cod-emitente"] . "','" . trata_acentos($nome_emitente) . "','" . trata_acentos($nome_abrev) . "','" . trata_acentos($nome_grupo) . "','" . trata_acentos($nome_canal) . "','" . trata_acentos($nome_ramo) . "','" . number_format(($a_item["peso-bruto"] * 1), 2, '.', '') . "','" . $aqtde[0] . "','" . $a_it_ped["vl-tot-it"] . "','" . $a_it_ped["vl-liq-it"] . "','" . $vl_merc_liq . "','" . ($a_it_ped["val-frete"] * 1) . "','" . $a_it_ped["nat-operacao"] . "','" . $vl_tot_item_us . "','" . $vl_merc_sicm_us . "','" . $vl_merc_liq_us . "','" . $vl_frete_it_us . "','" . trata_acentos($nome_familia_com) . "','" . $cnpj . "','" . $natureza . "','" . trata_acentos($nome_pais) . "','" . trata_acentos($nome_estado) . "','" . trata_acentos($nome_cidade) . "','" . trata_acentos($nome_regiao) . "','" . $cod_repr . "','" . trata_acentos($nome_repr) . "')";

    $rq = query(TextoBD("mysql", $sql_insert));
    if ($rq != "1") {
        echo $sql_insert;
    }
}

/* =========================================================================================================== */
// Excluir Pedidos Cancelados
/* =========================================================================================================== */
echo 'Excluindo Pedidos Cancelados : ' . date("H:i:s") . '<br>';
$sql_canc = 'select distinct "cod-estabel", "nr-pedido", "dt-cancela" from pub."ped-venda" where not ("dt-cancela" is null) order by  "nr-pedido"';
$q_canc = odbc_exec($cnx_nf, $sql_canc);
while ($a_canc = odbc_fetch_array($q_canc)) {
    query("delete from is_dm_pedidos where cod_estabel = '" . $a_canc["cod-estabel"] . "' and nr_pedido = '" . $a_canc["nr-pedido"] . "'");
    echo $a_canc["nr-pedido"] . '<br>';
}

/* =========================================================================================================== */
// Excluir Pedidos Cancelados
/* =========================================================================================================== */
echo 'Excluindo Itens de Pedidos Cancelados : ' . date("H:i:s") . '<br>';
$sql_canc = 'select distinct "nome-abrev", "nr-pedcli", "nr-sequencia", "dt-canseq" from pub."ped-item" where not ("dt-canseq" is null)';
$q_canc = odbc_exec($cnx_nf, $sql_canc);
while ($a_canc = odbc_fetch_array($q_canc)) {
    query("delete from is_dm_pedidos where nome_fantasia = '" . trata_acentos($a_canc["nome-abrev"]) . "' and nr_pedcli = '" . $a_canc["nr-pedcli"] . "' and nr_seq_fat = '" . $a_canc["nr-sequencia"] . "'");
    echo $a_canc["nr-pedcli"] . ' ' . $a_canc["nr-sequencia"] . '<br>';
}


/* =========================================================================================================== */
// Fecha Conexões e atualiza data da importacao
/* =========================================================================================================== */

odbc_close($cnx_nf);
odbc_close($cnx_emit);
odbc_close($cnx_canal);
odbc_close($cnx_item);
odbc_close($cnx_moeda);

query("update is_dm_param set dt_base_ped = '" . date("Y-m-d") . "', dt_base_ped_fim = '" . date("Y-m-d") . "'");

echo 'Fim do Processamento : ' . date("H:i:s");

function trata_acentos($texto) {
    return ($texto);
}

?>