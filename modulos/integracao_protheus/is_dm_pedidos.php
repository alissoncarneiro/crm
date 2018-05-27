<?php

set_time_limit(7200);

echo "*============================================================*<br>";
echo "Carga de Analise de Vendas Pedidos Protheus ERP via ODBC " . date("H:i:s") . "<br>";
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

$dt_base = str_replace(" 00:00:00","",str_replace("-","",$ap["dt_base_ped"]));
$dt_base_fim =  str_replace(" 00:00:00","",str_replace("-","",$ap["dt_base_ped_fim"]));
$custo_campo = $ap["custo_campo"];

if ($dt_base_fim < $dt_base) {
    echo 'Período incorreto !';
    exit;
}

query("delete from is_dm_pedidos where dt_emis_nota >= '" . $dt_base . "' and dt_emis_nota <= '" . $dt_base_fim . "'");

/* =========================================================================================================== */
// Importa NFs
/* =========================================================================================================== */
echo 'Importando Itens de Pedido : <br>';

$sql = 'select
    c6_filial as "cod-estabel",
    c6_serie as "serie",
    c6_num as "nr-nota-fis",
    c6_item as "nr-seq-fat",
    c6_produto as "it-codigo",
    0 as "peso-bruto",
    c6_qtdven as "qt-faturada",
    c6_um as "un-fatur",
    c6_prcven as "vl-preuni",
    c6_valor as "vl-tot-item",
    c6_valor as "vl-merc-sicm",
    c6_valor as "vl-merc-liq",
    0 as "vl-ipi-it",
    0 as "vl-frete-it",
    c6_cf as "nat-operacao",
    c5_emissao	 as "dt-emis-nota",
    c6_num as "nr-pedcli",
    c6_num as "nr-pedido",
    c5_cliente as "cd-emitente",
    c5_cliente as "nome-ab-cli",
    0 as "ind-sit-nota",
    0 as "cod-rep",
    0 as "perc-desco1",
    0 as "perc-desco2",
    0 as "nome-transp",
    0 as "val-pct-desconto-tab-preco",
    0 as "des-pct-desconto-inform"
    from sc6'. $CodEmpresaProtheus . '
    left join sc5'.$CodEmpresaProtheus .' on c6_num = c5_num
    where c5_emissao >= ' . "'" . $dt_base . "' and " . 'c5_emissao' . " <= " . "'" . $dt_base_fim . "' order by c6_num";

echo "Buscando Pedidos " . date("H:i:s") . '<br>';
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

    $sql_insert = 'INSERT INTO is_dm_pedidos ( dt_cadastro , hr_cadastro , id_usuario_cad , dt_alteracao , hr_alteracao , id_usuario_alt , id_pessoa, id_produto, cod_estabel , serie , dt_emis_nota , nr_nota_fis , nr_seq_fat , nr_pedido , nr_pedcli , nome_familia , it_codigo , it_nome , cd_emitente , nome_emitente , nome_fantasia , nome_grupo , nome_canal , nome_ramo , peso_bruto , qt_faturada , vl_tot_item , vl_merc_sicm ,  nat_operacao , vl_tot_item_us , vl_merc_sicm_us , nome_familia_com , cnpj , natureza , nome_pais , nome_estado , nome_cidade, nome_regiao, cod_repr, nome_repr, vl_merc_liq) VALUES (';

    $sql_insert .= "'" . date("Y-m-d") . "','" . date("H:i:s") . "','" . $id_usuario . "','" . date("Y-m-d") . "','" . date("H:i:s") . "','" . $id_usuario . "',";
    $sql_insert .= ( $a_pessoa_crm["numreg"] ? $a_pessoa_crm["numreg"] : 'NULL') . "," . ($a_produto_crm["numreg"] ? $a_produto_crm["numreg"] : 'NULL') . ",'" . $a_it_nf["cod-estabel"] . "','" . $a_it_nf["serie"] . "','" . $a_it_nf["dt-emis-nota"] . "','" . $a_it_nf["nr-nota-fis"] . "','" . $a_it_nf["nr-seq-fat"] . "','" . $a_it_nf["nr-pedido"] . "','" . $a_it_nf["nr-pedcli"] . "','" . trata_acentos($nome_familia) . "','" . $a_it_nf["it-codigo"] . "','" . trata_acentos($it_nome) . "','" . $a_it_nf["cd-emitente"] . "','" . trata_acentos($nome_emitente) . "','" . trata_acentos($nome_abrev) . "','" . trata_acentos($nome_grupo) . "','" . trata_acentos($nome_canal) . "','" . trata_acentos($nome_ramo) . "','" . $a_it_nf["peso-bruto"] . "','" . $aqtde[0] . "','" . $a_it_nf["vl-tot-item"] . "','" . $vl_merc_liq . "','" . $a_it_nf["nat-operacao"] . "','" . $vl_tot_item_us . "','" . $vl_merc_liq_us . "','" . trata_acentos($nome_familia_com) . "','" . $cnpj . "','" . trata_acentos($natureza) . "','" . trata_acentos($nome_pais) . "','" . $nome_estado . "','" . trata_acentos($nome_cidade) . "','" . trata_acentos($nome_regiao) . "','" . $cod_repr . "','" . trata_acentos($nome_repr) . "','" . ($a_it_nf["vl-merc-liq"] * 1) . "')";

    $rq = query($sql_insert);
    if ($rq != "1") {
        echo $sql_insert;
    }
}


/* =========================================================================================================== */
// Fecha Conexões e atualiza data da importacao e tabela de cabecalho
/* =========================================================================================================== */

odbc_close($cnx_nf);

query("update is_dm_param set dt_base_ped = '" . date("Y-m-d") . "', dt_base_ped_fim = '" . date("Y-m-d") . "'");

echo 'Fim do Processamento : ' . date("H:i:s");

function trata_acentos($texto) {
    //  return utf8_encode($texto);
    return ($texto);
}

?>