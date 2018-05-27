<?php

set_time_limit(7200);

echo "*============================================================*<br>";
echo "Carga de Analise de Vendas NFs Protheus ERP via ODBC " . date("H:i:s") . "<br>";
echo "*============================================================*<br>";

include_once('../../conecta.php');
include_once('../../functions.php');
include_once('../../conecta_odbc_protheus.php');

$CnxODBC = odbc_connect($AliasProtheus, $UsuarioProtheus, $SenhaProtheus);
if (!$CnxODBC) {
    echo 'Não foi possível estabelecer uma conexão com o ERP Protheus.';
    exit;
}

// Carregando Parâmetros
$ap = farray(query("select * from is_dm_param"));

$id_usuario = 'IMPORT';

$dt_base = str_replace("-", "", $ap["dt_base"]);
$dt_base_fim = str_replace("-", "", $ap["dt_base_fim"]);
$custo_campo = $ap["custo_campo"];

if ($dt_base_fim < $dt_base) {
    echo 'Período incorreto !';
    exit;
}

query("delete from is_dm_notas where dt_emis_nota >= '" . $dt_base . "' and dt_emis_nota <= '" . $dt_base_fim . "'");

/* =========================================================================================================== */
// Importa NFs
/* =========================================================================================================== */
echo 'Importando Itens de Nota Fiscal : <br>';

$sql = 'select
    d2_filial as "cod-estabel",
    d2_serie as "serie",
    d2_doc as "nr-nota-fis",
    d2_item as "nr-seq-fat",
    d2_cod as "it-codigo",
    0 as "peso-bruto",
    d2_quant as "qt-faturada",
    d2_um as "un-fatur",
    d2_prcven as "vl-preuni",
    d2_total as "vl-tot-item",
    d2_total as "vl-merc-sicm",
    d2_total as "vl-merc-liq",
    d2_valipi as "vl-ipi-it",
    0 as "vl-frete-it",
    d2_cf as "nat-operacao",
    d2_emissao as "dt-emis-nota",
    d2_pedido as "nr-pedcli",
    d2_pedido as "nr-pedido",
    d2_cliente as "cd-emitente",
    d2_cliente as "nome-ab-cli",
    0 as "ind-sit-nota",
    0 as "cod-rep",
    0 as "perc-desco1",
    0 as "perc-desco2",
    0 as "nome-transp",
    0 as "val-pct-desconto-tab-preco",
    0 as "des-pct-desconto-inform"
    from sd2'.$CodEmpresaProtheus.' 
    where d2_emissao >= ' . "'" . $dt_base . "' and " . 'd2_emissao' . " <= " . "'" . $dt_base_fim . "' order by d2_doc";

//echo $sql;

echo "Buscando NFs " . date("H:i:s") . '<br>';
$q_it_nf = odbc_exec($CnxODBC, $sql);
$nf_refer = "";
echo "Iniciando carga " . date("H:i:s") . '<br>';
while ($a_it_nf = odbc_fetch_array($q_it_nf)) {

    // Se mudou a NF
    if ($nf_refer != $a_it_nf["nr-nota-fis"]) {

        $nf_refer = $a_it_nf["nr-nota-fis"];

        // Pesquisa o Cliente e outras tabelas relacionadas

        $a_pessoa_crm = farray(query("select * from is_pessoa where id_pessoa_erp = '" . $a_it_nf["cd-emitente"] . "'"));
        $a_produto_crm = farray(query("select * from is_produto where id_produto_erp = '" . $a_it_nf["it-codigo"] . "'"));
        $nome_canal = "";
        $nome_grupo = "";
        $nome_repr = "";
        $cod_repr = "";
        $natureza = $a_pessoa_crm["id_tp_pessoa"];
        $nome_emitente = str_replace('"', " ", str_replace("'", " ", $a_pessoa_crm["razao_social_nome"]));
        $nome_abrev = str_replace('"', " ", str_replace("'", " ", $a_pessoa_crm["fantasia_apelido"]));
        $cnpj = $a_pessoa_crm["cnpj_cpf"];
        if ($a_pessoa_crm["id_ramo_atividade"]) {
            $a_ramo = @farray(@query('select * from is_ramo where numreg = ' . "'" . $a_pessoa_crm["id_ramo_atividade"] . "'"));
            $nome_ramo = str_replace('"', " ", str_replace("'", " ", $a_ramo["nome_ramo"]));
            if (empty($nome_ramo)) {
                $nome_ramo = $a_pessoa_crm["id_ramo_atividade"];
            }
        }


        $nome_pais = str_replace('"', " ", str_replace("'", " ", $a_pessoa_crm["pais"]));
        $nome_estado = str_replace('"', " ", str_replace("'", " ", $a_pessoa_crm["uf"]));
        $nome_cidade = str_replace('"', " ", str_replace("'", " ", $a_pessoa_crm["cidade"]));
        $micro_regiao = str_replace('"', " ", str_replace("'", " ", $a_pessoa_crm["id_regiao"]));

        if ($a_pessoa_crm["id_grupo_cliente"]) {
            $a_gr_cli = @farray(@query('select * from is_grupo_cliente where numreg = ' . "'" . $a_pessoa_crm["id_grupo_cliente"] . "'"));
            $nome_grupo = str_replace('"', " ", str_replace("'", " ", $a_gr_cli["nome_grupo_cliente"]));
        }

        if ($a_it_nf["cod-rep"]) {
            $a_repr = @farray(@query('select * from is_usuario where numreg = ' . "'" . $a_pessoa_crm["id_representante_padrao"] . "'"));
            $nome_repr = str_replace('"', " ", str_replace("'", " ", $a_repr["nome_usuario"]));
            $cod_repr = $a_it_nf["cod-rep"];
        }

        if ($a_pessoa_crm["id_canal_venda"]) {
            $a_canal = @farray(@query('select * from is_canal_venda where numreg = ' . "'" . $a_pessoa_crm["id_canal_venda"] . "'"));
            $nome_canal = str_replace('"', " ", str_replace("'", " ", $a_canal["nome_canal_venda"]));
        }

        //moedas
        $dt_refer = $a_it_nf["dt-emis-nota"];
        $cotacao_dia_anterior = 1;
    }

    $nome_familia = "";
    $nome_familia_com = "";
    $it_nome = "";
    $linha = $a_it_nf["qt-faturada"];
    $aqtde[0] = $a_it_nf["qt-faturada"];
    $vl_tot_item_us = $a_it_nf["vl-tot-item"] / $cotacao_dia_anterior;
    $vl_merc_sicm_us = $a_it_nf["vl-merc-sicm"] / $cotacao_dia_anterior;

    $vl_merc_liq = (($a_it_nf["vl-tot-item"] * 1) - ($a_it_nf["vl-ipi-it"] * 1));
    $vl_merc_liq_us = $vl_merc_liq / $cotacao_dia_anterior;

    $vl_frete_it_us = $a_it_nf["vl-frete-it"] / $cotacao_dia_anterior;

    if ($a_it_nf["it-codigo"]) {
        $a_item = @farray(@query('select * from is_produto where id_produto_erp = ' . "'" . $a_it_nf["it-codigo"] . "'"));
        $it_nome = str_replace('"', " ", str_replace("'", " ", $a_item["nome_produto"]));

        $a_fam = @farray(@query('select * from is_familia where numreg = ' . "'" . $a_item["id_familia"] . "'"));
        $nome_familia = str_replace('"', " ", str_replace("'", " ", $a_fam["nome_familia"]));

        $a_fam_com = @farray(@query('select * from is_familia_comercial where numreg = ' . "'" . $a_item["id_familia_comercial"] . "'"));
        $nome_familia_com = str_replace('"', " ", str_replace("'", " ", $a_fam_com["nome_familia_comercial"]));
    }

    $vl_margem = $a_it_nf["vl-merc-liq"] - ($vl_custo * $aqtde[0]);

    $sql_insert = 'INSERT INTO is_dm_notas ( dt_cadastro , hr_cadastro , id_usuario_cad , dt_alteracao , hr_alteracao , id_usuario_alt , id_pessoa, id_produto, cod_estabel , serie , dt_emis_nota , nr_nota_fis , nr_seq_fat , nr_pedido , nr_pedcli , nome_familia , it_codigo , it_nome , cd_emitente , nome_emitente , nome_fantasia , nome_grupo , nome_canal , nome_ramo , peso_bruto , qt_faturada , vl_tot_item , vl_merc_sicm ,  nat_operacao , vl_tot_item_us , vl_merc_sicm_us , nome_familia_com , cnpj , natureza , nome_pais , nome_estado , nome_cidade, nome_regiao, cod_repr, nome_repr, vl_merc_liq, pct_desc1, pct_desc2, pct_desc3, pct_desc4, nome_transp ) VALUES (';

    $sql_insert .= "'" . date("Y-m-d") . "','" . date("H:i:s") . "','" . $id_usuario . "','" . date("Y-m-d") . "','" . date("H:i:s") . "','" . $id_usuario . "',";
    $sql_insert .= ( $a_pessoa_crm["numreg"] ? $a_pessoa_crm["numreg"] : 'NULL') . "," . ($a_produto_crm["numreg"] ? $a_produto_crm["numreg"] : 'NULL') . ",'" . $a_it_nf["cod-estabel"] . "','" . $a_it_nf["serie"] . "','" . $a_it_nf["dt-emis-nota"] . "','" . $a_it_nf["nr-nota-fis"] . "','" . $a_it_nf["nr-seq-fat"] . "','" . $a_it_nf["nr-pedido"] . "','" . $a_it_nf["nr-pedcli"] . "','" . trata_acentos($nome_familia) . "','" . $a_it_nf["it-codigo"] . "','" . trata_acentos($it_nome) . "','" . $a_it_nf["cd-emitente"] . "','" . trata_acentos($nome_emitente) . "','" . trata_acentos($nome_abrev) . "','" . trata_acentos($nome_grupo) . "','" . trata_acentos($nome_canal) . "','" . trata_acentos($nome_ramo) . "','" . $a_it_nf["peso-bruto"] . "','" . $aqtde[0] . "','" . $a_it_nf["vl-tot-item"] . "','" . $vl_merc_liq . "','" . $a_it_nf["nat-operacao"] . "','" . $vl_tot_item_us . "','" . $vl_merc_liq_us . "','" . trata_acentos($nome_familia_com) . "','" . $cnpj . "','" . trata_acentos($natureza) . "','" . trata_acentos($nome_pais) . "','" . $nome_estado . "','" . trata_acentos($nome_cidade) . "','" . trata_acentos($nome_regiao) . "','" . $cod_repr . "','" . trata_acentos($nome_repr) . "','" . ($a_it_nf["vl-merc-liq"] * 1) . "','" . ($a_it_nf["perc-desco1"] * 1) . "','" . ($a_it_nf["perc-desco2"] * 1) . "','" . ($a_it_nf["val-pct-desconto-tab-preco"] * 1) . "','" . ($a_it_nf["des-pct-desconto-inform"] * 1) . "','" . trata_acentos($a_it_nf["nome-transp"]) . "')";

    $rq = query($sql_insert);
    if ($rq != "1") {
        echo $sql_insert;
    }
}


/* =========================================================================================================== */
// Fecha Conexões e atualiza data da importacao e tabela de cabecalho
/* =========================================================================================================== */

odbc_close($cnx_nf);

query("update is_dm_param set dt_base = '" . date("Y-m-d") . "', dt_base_fim = '" . date("Y-m-d") . "'");

query("delete from is_dm_notas_cab");

$sql_cab = 'insert into is_dm_notas_cab (
cod_estabel, id_pessoa, nr_nota_fis, serie, nome_emitente, dt_emis_nota, nr_pedido, nr_pedcli, nome_repr, cd_emitente, nome_transp, pct_desc1, pct_desc2, vl_tot)
select cod_estabel, id_pessoa, nr_nota_fis, serie, nome_emitente, dt_emis_nota, nr_pedido, nr_pedcli, nome_repr, cd_emitente, nome_transp, pct_desc1, pct_desc2, sum(vl_tot_item) as vl_tot from is_dm_notas
group by cod_estabel, id_pessoa, nr_nota_fis,serie, nome_emitente, dt_emis_nota, nr_pedido, nr_pedcli, nome_repr, cd_emitente, nome_transp, pct_desc1, pct_desc2';
query($sql_cab);

query("delete from is_inf_compra_faturamento");
$sql_cab = 'insert into is_inf_compra_faturamento(id_pessoa, id_produto, qtde, valor ) select id_pessoa, id_produto, sum(qt_faturada), sum(vl_tot_item) from is_dm_notas group by id_pessoa, id_produto';
query($sql_cab);

echo 'Fim do Processamento : ' . date("H:i:s");

function trata_acentos($texto) {
    //  return utf8_encode($texto);
    return ($texto);
}

?>