<?php
   // No caso de Linux usar : @header("Content-Type: text/html;  charset=ISO-8859-1", true);
@session_start();

set_time_limit(7200);

echo "*============================================================*<br>";
echo "Carga de NFs Datasul ERP via ODBC ".date("H:i:s")."<br>";
echo "*============================================================*<br>";

require("../../conecta.php");
include "../../funcoes.php";
include "../../functions.php";
$ArrayConf = parse_ini_file('../../conecta_odbc_erp_datasul.ini', true);

$qtde_processada = 0;

// Carregando Parâmetros
$ap = farray(query("select * from is_dm_param"));

$mov2dis = $ap["odbc_nf"];
$natalias = $ap["odbc_nat_oper"];
$ems2adm = $ap["odbc_emit"];
$ems2dis = $ap["odbc_canal"];
$ems2ind = $ap["odbc_item"];
$ems2uni = $ap["odbc_moeda"];
$id_moeda = $ap["id_moeda"];

$nome_usuario = $_SESSION["nome_usuario"];
if(empty($nome_usuario)) {
    $nome_usuario = 'IMPORT';
}
//Conecta com os bancos ODBC
/* DESATIVADO POIS EM ALGUNS MOMENTOS NÃO CONSEGUE RECUPERAR O ALIAS
$cnx_nf = ConectaODBCErpDatasul($ArrayConf, 'nota-fiscal');
$cnx_emit = ConectaODBCErpDatasul($ArrayConf, 'emitente');
$cnx_canal = ConectaODBCErpDatasul($ArrayConf, 'canal-venda');
$cnx_item = ConectaODBCErpDatasul($ArrayConf, 'item');
$cnx_moeda = ConectaODBCErpDatasul($ArrayConf, 'moeda');
$cnx_nat = ConectaODBCErpDatasul($ArrayConf, 'natur-oper');
 *
 */
$MicroTimeInicio = microtime(true);
$QtdeErro = 0;
$NumregLog = CriaLog('is_dm_notas');

//Conecta com os bancos ODBC
$cnx_nf = odbc_connect($mov2dis,"sysprogress","sysprogress") or die("Erro na conexão com o Database 1");
$cnx_emit = odbc_connect($ems2adm,"sysprogress","sysprogress") or die("Erro na conexão com o Database 2");
$cnx_canal = odbc_connect($ems2dis,"sysprogress","sysprogress") or die("Erro na conexão com o Database 3");
$cnx_item = odbc_connect($ems2ind,"sysprogress","sysprogress") or die("Erro na conexão com o Database 4");
$cnx_moeda = odbc_connect($ems2uni,"sysprogress","sysprogress") or die("Erro na conexão com o Database 5");
$cnx_nat = odbc_connect($natalias,"sysprogress","sysprogress") or die("Erro na conexão com o Database 6");


// Checando Naturezas Válidas da Alpha
$nat_opers = "";
$q_natureza = odbc_exec($cnx_nat, 'select "nat-operacao" from pub."natur-oper" where "emite-duplic" = ' . "'1'". ' and ("nat-operacao" like '."'5%'".' or "nat-operacao" like '."'6%'".' or "nat-operacao" like '."'7%'".')');
while ($a_natureza = odbc_fetch_array($q_natureza)) {
    $nat_opers .= $a_natureza["nat-operacao"] . ',';
}
if (empty($nat_opers)) {
    echo "Não foi possivel encontrar Naturezas de Operação<br>";
    GravaLogDetalhe($NumregLog,'','Não foi possivel encontrar Naturezas de Operação','','Erro');
    FinalizaLog($NumregLog,$MicroTimeInicio,0,0,1,0,0);
    exit;
}

$nat_opers = substr($nat_opers, 0, strlen($nat_opers) - 1);

echo "Naturezas : " . $nat_opers . ' - '.date("H:i:s").'<br>';

if (empty($nat_opers)) {
    GravaLogDetalhe($NumregLog,'','Não foi possivel encontrar Naturezas de Operação','','Erro');
    FinalizaLog($NumregLog,$MicroTimeInicio,0,0,1,0,0);
    exit;
}

$nat_opers = "'" . str_replace(",", "','", str_replace(" ", "", $nat_opers)) . "','DEVOLUCAO','CANCELADA'";
$dt_base = $ap["dt_base"];
$dt_base_fim = $ap["dt_base_fim"];
$custo_campo = $ap["custo_campo"];

if ($dt_base_fim < $dt_base) {
    echo 'Período incorreto !';
    GravaLogDetalhe($NumregLog,'','Período incorreto','','Erro');
    FinalizaLog($NumregLog,$MicroTimeInicio,0,0,1,0,0);
    exit;
}

// Excluir qualquer NF fora nas Naturezas de Operacao parametrizadas ou com data de emissao superior a que sera feita a importacao
if (($_GET["cancelada"] != "S") && ($_GET["devolucao"] != "S")) {
    query("delete from is_dm_notas where not nat_operacao in (" . $nat_opers . ")");
    query("delete from is_dm_notas where dt_emis_nota >= '" . $dt_base . "' and dt_emis_nota <= '" . $dt_base_fim . "'");
}

if ($_GET["apagar"] == "S") {
    GravaLogDetalhe($NumregLog,'','Limpando a Base','','Aviso');
    FinalizaLog($NumregLog,$MicroTimeInicio,0,0,0,0,0);
    exit;
}

/* =========================================================================================================== */
// Importa NFs
/* =========================================================================================================== */
echo 'Importando Itens de Nota Fiscal : '.date("H:i:s").'<br>';

if ($_GET["reimportar_devolucao"] == "S") {
// Query para reimportar as notas com devolucoes
    $sql = 'select t1."cod-estabel", t1."serie", t1."nr-nota-fis", t1."nr-seq-fat",t1."it-codigo",t1."peso-bruto",t1."qt-faturada", t1."un-fatur",t1."vl-preuni",t1."vl-tot-item", t1."vl-merc-sicm",t1."vl-merc-liq",t1."vl-ipi-it", t1."vl-frete-it", t1."nat-operacao", t1."dt-emis-nota", t1."nr-pedcli", t1."nr-pedido",t1."cd-emitente",t1."nome-ab-cli", t1."ind-sit-nota", t2."cod-rep", t2."perc-desco1", t2."perc-desco2", t2."nome-transp", t1."val-pct-desconto-tab-preco", t1."des-pct-desconto-inform", t2."cod-canal-venda" from pub."it-nota-fisc" t1 INNER JOIN pub."nota-fiscal" t2 ON t1."nr-nota-fis" = t2."nr-nota-fis" AND t1.serie = t2.serie AND t1."cod-estabel" = t2."cod-estabel" where t1."nr-nota-fis" in (select t5."nr-nota-fis" from pub."devol-cli" t5) AND t2."esp-docto"=' . "'22'";
} else {
    $sql = 'select t1."cod-estabel", t1."serie", t1."nr-nota-fis", t1."nr-seq-fat",t1."it-codigo",t1."peso-bruto",t1."qt-faturada", t1."un-fatur",t1."vl-preuni",t1."vl-tot-item", t1."vl-merc-sicm",t1."vl-merc-liq",t1."vl-ipi-it", t1."vl-frete-it", t1."nat-operacao", t1."dt-emis-nota", t1."nr-pedcli", t1."nr-pedido",t1."cd-emitente",t1."nome-ab-cli", t1."ind-sit-nota", t2."cod-rep", t2."perc-desco1", t2."perc-desco2", t2."nome-transp", t1."val-pct-desconto-tab-preco", t1."des-pct-desconto-inform", t2."cod-canal-venda" from pub."it-nota-fisc" t1 INNER JOIN pub."nota-fiscal" t2 ON t1."nr-nota-fis" = t2."nr-nota-fis" AND t1.serie = t2.serie AND t1."cod-estabel" = t2."cod-estabel" where ((t1."dt-emis-nota" >= ' . "'" . $dt_base . "' and " . 't1."dt-emis-nota"' . " <= " . "'" . $dt_base_fim . "') or ".'(t1."dt-confirma" >= ' . "'" . $dt_base . "' and " . 't1."dt-confirma"' . " <= " . "'" . $dt_base_fim . "')) and " . 't1."nat-operacao" in (' . $nat_opers . ') AND t2."esp-docto"=' . "'22'" . ' order by t1."nr-nota-fis"';
}
//echo $sql;
if (($_GET["cancelada"] != "S") && ($_GET["devolucao"] != "S")) {

    echo "Buscando NFs " . date("H:i:s") . '<br>';
    $q_it_nf = odbc_exec($cnx_nf, $sql);
    $nf_refer = "";
    echo "Iniciando carga " . date("H:i:s") . '<br>';

    while ($a_it_nf = odbc_fetch_array($q_it_nf)) {

        // Se mudou a NF
        if ($nf_refer != $a_it_nf["nr-nota-fis"]) {
            $qtde_processada++;

            $nf_refer = $a_it_nf["nr-nota-fis"];

            // Pesquisa o Cliente e outras tabelas relacionadas
//            $a_emit = odbc_fetch_array(odbc_exec($cnx_emit, 'select "nome-emit", "natureza", "cgc", "atividade", "pais", "estado", "cidade", "nome-mic-reg", "cod-gr-cli", "cod-canal-venda","cod-rep" from pub."emitente" where "cod-emitente" = ' . "'" . $a_it_nf["cd-emitente"] . "'"));

            $a_pessoa_crm = farray(query("select * from is_pessoa where id_pessoa_erp = '" . $a_it_nf["cd-emitente"] . "'"));
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
            echo $a_it_nf["nr-nota-fis"] . ' - ' . $nome_emitente . " " . $a_it_nf["dt-emis-nota"] . '<br>';

            $nome_pais = str_replace('"', " ", str_replace("'", " ", $a_pessoa_crm["pais"]));
            $nome_estado = str_replace('"', " ", str_replace("'", " ", $a_pessoa_crm["uf"]));
            $nome_cidade = str_replace('"', " ", str_replace("'", " ", $a_pessoa_crm["cidade"]));
            $micro_regiao = str_replace('"', " ", str_replace("'", " ", $a_pessoa_crm["id_regiao"]));

            if ($a_pessoa_crm["id_grupo_cliente"]) {
                $a_gr_cli = @farray(@query('select * from is_grupo_cliente where numreg = ' . "'" . $a_pessoa_crm["id_grupo_cliente"] . "'"));
                $nome_grupo = str_replace('"', " ", str_replace("'", " ", $a_gr_cli["nome_grupo_cliente"]));
            }

//		  $a_nf_cad = odbc_fetch_array(odbc_exec($cnx_nf,'select "cod-rep" from pub."nota-fiscal" where "cod-estabel" = '."'".$a_it_nf["cod-estabel"]."' and ".'"nr-nota-fis" = '."'".$a_it_nf["nr-nota-fis"]."'"));

            if ($a_it_nf["cod-rep"]) {
                $a_repr = @farray(@query('select * from is_usuario where id_representante = ' . "'" . $a_it_nf["cod-rep"] . "'"));
                $nome_repr = str_replace('"', " ", str_replace("'", " ", $a_repr["nome_abreviado"]));
                $cod_repr = $a_it_nf["cod-rep"];
            }

            if ($a_it_nf["cod-canal-venda"]) {
                $a_canal = @farray(@query('select * from is_canal_venda where id_canal_venda_erp = ' . "'" . $a_it_nf["cod-canal-venda"]. "'"));
                $nome_canal = str_replace('"', " ", str_replace("'", " ", $a_canal["nome_canal_venda"]));
            }

            //moedas
            $dt_refer = $a_it_nf["dt-emis-nota"];
            $a_moeda = odbc_fetch_array(odbc_exec($cnx_moeda, "SELECT cotacao FROM pub.cotacao WHERE \"mo-codigo\"='" . $id_moeda . "' AND \"ano-periodo\" = '" . substr($dt_refer, 0, 4) . substr($dt_refer, 5, 2) . "'"));
            $str_cotacoes = $a_moeda['cotacao'];
            $array_cotacoes = explode(";", $str_cotacoes);
            $ind_array_cotacoes = (substr($dt_refer, 8, 2) * 1) - 1;

            $cotacao_dia_anterior = $array_cotacoes[$ind_array_cotacoes] * 1;
            if ($cotacao_dia_anterior <= 0) {
                $cotacao_dia_anterior = 1;
            }
        }

        $nome_familia = "";
        $nome_familia_com = "";
        $it_nome = "";
        $linha = $a_it_nf["qt-faturada"];
        $aqtde = explode(';', $linha);
        $vl_tot_item_us = $a_it_nf["vl-tot-item"] / $cotacao_dia_anterior;
        $vl_merc_sicm_us = $a_it_nf["vl-merc-sicm"] / $cotacao_dia_anterior;

        $vl_merc_liq = (($a_it_nf["vl-tot-item"] * 1) - ($a_it_nf["vl-ipi-it"] * 1));
        $vl_merc_liq_us = $vl_merc_liq / $cotacao_dia_anterior;

        $vl_frete_it_us = $a_it_nf["vl-frete-it"] / $cotacao_dia_anterior;

        $a_produto_crm = farray(query("select * from is_produto where id_produto_erp = '" . $a_it_nf["it-codigo"] . "'"));

        if ($a_it_nf["it-codigo"]) {
            $a_item = odbc_fetch_array(odbc_exec($cnx_item,'select "desc-item", "fm-codigo", "fm-cod-com" from pub."item" where "it-codigo" = '."'".$a_it_nf["it-codigo"]."'"));
            $it_nome = str_replace('"', " ",str_replace("'", " ",$a_item["desc-item"]));

            $a_fam = @farray(@query('select * from is_familia where numreg = ' . "'" . $a_produto_crm["id_familia"] . "'"));
            $nome_familia = str_replace('"', " ", str_replace("'", " ", $a_fam["nome_familia"]));

            $a_fam_com = @farray(@query('select * from is_familia_comercial where numreg = ' . "'" . $a_produto_crm["id_familia_comercial"] . "'"));
            $nome_familia_com = str_replace('"', " ", str_replace("'", " ", $a_fam_com["nome_familia_comercial"]));
        }

        $vl_margem = $a_it_nf["vl-merc-liq"] - ($vl_custo * $aqtde[0]);
    
        query("delete from is_dm_notas where cod_estabel = '" . $a_it_nf["cod-estabel"]  . "' and nr_nota_fis = '" . $a_it_nf["nr-nota-fis"] . "' and serie = '".$a_it_nf["serie"]."' and nr_seq_fat = '".$a_it_nf["nr-seq-fat"]."'");

        $sql_insert = 'INSERT INTO is_dm_notas ( dt_cadastro , hr_cadastro , id_usuario_cad , dt_alteracao , hr_alteracao , id_usuario_alt , id_pessoa, id_produto, cod_estabel , serie , dt_emis_nota , nr_nota_fis , nr_seq_fat , nr_pedido , nr_pedcli , nome_familia , it_codigo , it_nome , cd_emitente , nome_emitente , nome_fantasia , nome_grupo , nome_canal , nome_ramo , peso_bruto , qt_faturada , vl_tot_item , vl_merc_sicm ,  nat_operacao , vl_tot_item_us , vl_merc_sicm_us , nome_familia_com , cnpj , natureza , nome_pais , nome_estado , nome_cidade, nome_regiao, cod_repr, nome_repr, vl_merc_liq, pct_desc1, pct_desc2, pct_desc3, pct_desc4, nome_transp ) VALUES (';

        $sql_insert .= "'" . date("Y-m-d") . "','" . date("H:i:s") . "','" . $nome_usuario . "','" . date("Y-m-d") . "','" . date("H:i:s") . "','" . $nome_usuario . "',";
        $sql_insert .= ( $a_pessoa_crm["numreg"] ? $a_pessoa_crm["numreg"] : 'NULL') . "," . ($a_produto_crm["numreg"] ? $a_produto_crm["numreg"] : 'NULL') . ",'" . $a_it_nf["cod-estabel"] . "','" . $a_it_nf["serie"] . "','" . $a_it_nf["dt-emis-nota"] . "','" . $a_it_nf["nr-nota-fis"] . "','" . $a_it_nf["nr-seq-fat"] . "','" . $a_it_nf["nr-pedido"] . "','" . $a_it_nf["nr-pedcli"] . "','" . trata_acentos_nf($nome_familia) . "','" . $a_it_nf["it-codigo"] . "','" . trata_acentos_nf($it_nome) . "','" . $a_it_nf["cd-emitente"] . "','" . trata_acentos_nf($nome_emitente) . "','" . trata_acentos_nf($nome_abrev) . "','" . trata_acentos_nf($nome_grupo) . "','" . trata_acentos_nf($nome_canal) . "','" . trata_acentos_nf($nome_ramo) . "','" . $a_it_nf["peso-bruto"] . "','" . $aqtde[0] . "','" . $a_it_nf["vl-tot-item"] . "','" . $vl_merc_liq . "','" . $a_it_nf["nat-operacao"] . "','" . $vl_tot_item_us . "','" . $vl_merc_liq_us . "','" . trata_acentos_nf($nome_familia_com) . "','" . $cnpj . "','" . trata_acentos_nf($natureza) . "','" . trata_acentos_nf($nome_pais) . "','" . $nome_estado . "','" . trata_acentos_nf($nome_cidade) . "','" . trata_acentos_nf($nome_regiao) . "','" . $cod_repr . "','" . trata_acentos_nf($nome_repr) . "','" . ($a_it_nf["vl-merc-liq"] * 1) . "','" . ($a_it_nf["perc-desco1"] * 1) . "','" . ($a_it_nf["perc-desco2"] * 1) . "','" . ($a_it_nf["val-pct-desconto-tab-preco"] * 1) . "','" . ($a_it_nf["des-pct-desconto-inform"] * 1) . "','" . trata_acentos_nf($a_it_nf["nome-transp"]) . "')";

        $rq = query($sql_insert);

        if ($rq != "1") {
            if(TipoBancoDados == 'mysql'){
                $MensagemErro = mysql_error();
            }
            elseif(TipoBancoDados == 'mssql'){
                $MensagemErro = mssql_get_last_message();
            }
            else{
                $MensagemErro = '';
            }
            $QtdeErro++;
            GravaLogDetalhe($NumregLog,$sql_insert,'Erro SQL: '.$MensagemErro,print_r($a_it_nf,true),'Erro');
            echo $sql_insert;
        }
    }
}

/* =========================================================================================================== */
// Excluir NFs Canceladas
/* =========================================================================================================== */
if ($_GET["cancelada"] == "S") {
    echo 'Processando Notas Canceladas : ' . date("H:i:s") . '<br>';
    query("delete from is_dm_notas where nat_operacao = 'CANCELADA'");

    $sql_canc = 'select "cod-estabel", "nr-nota-fis", "nr-seq-fat", "dt-cancela", "it-codigo","serie" from pub."it-nota-fisc" where "dt-cancela" >= ' . "'1980-01-01'";
    $q_canc = odbc_exec($cnx_nf, $sql_canc);
    while ($a_canc = odbc_fetch_array($q_canc)) {
        $q_nf_copia = farray(query("select * from is_dm_notas where cod_estabel = '" . $a_canc["cod-estabel"] . "' and serie = '" . $a_canc["serie"] . "' and nr_nota_fis = '" . $a_canc["nr-nota-fis"] . "' and nr_seq_fat = '" . $a_canc["nr-seq-fat"] . "'"));
        // Se a NF já foi importada pode-se copiar
        if ($q_nf_copia["nr_nota_fis"]) {
            $qtde = $q_nf_copia["qt_faturada"] * -1;
            $peso_bruto = (($q_nf_copia["peso_bruto"] * 1) * -1);
            $vl_tot_item = (($q_nf_copia["vl_tot_item"] * 1) * -1);
            $vl_merc_sicm = (($q_nf_copia["vl_merc_sicm"] * 1) * -1);
            $vl_tot_item_us = (($q_nf_copia["vl_tot_item_us"] * 1) * -1);
            $vl_merc_sicm_us = (($q_nf_copia["vl_merc_sicm_us"] * 1) * -1);
            $vl_merc_liq = (($q_nf_copia["vl_merc_liq"] * 1) * -1);

            $sql_insert = 'INSERT INTO is_dm_notas ( dt_cadastro , hr_cadastro , id_usuario_cad , dt_alteracao , hr_alteracao , id_usuario_alt , id_pessoa, id_produto, cod_estabel , serie , dt_emis_nota , nr_nota_fis , nr_seq_fat , nr_pedido , nr_pedcli , nome_familia , it_codigo , it_nome , cd_emitente , nome_emitente , nome_fantasia , nome_grupo , nome_canal , nome_ramo , peso_bruto , qt_faturada , vl_tot_item , vl_merc_sicm ,  nat_operacao , vl_tot_item_us , vl_merc_sicm_us , nome_familia_com , cnpj , natureza , nome_pais , nome_estado , nome_cidade, nome_regiao, cod_repr, nome_repr, vl_merc_liq, pct_desc1, pct_desc2, pct_desc3, pct_desc4, nome_transp) VALUES (';
            $sql_insert .= "'" . date("Y-m-d") . "','" . date("H:i:s") . "','" . $nome_usuario . "','" . date("Y-m-d") . "','" . date("H:i:s") . "','" . $nome_usuario . "','";
            $sql_insert .= $q_nf_copia["id_pessoa"] . "','" . $q_nf_copia["id_produto"] . "','" . $q_nf_copia["cod_estabel"] . "','" . $q_nf_copia["serie"] . "','" . $a_canc["dt-cancela"] . "','" . $q_nf_copia["nr_nota_fis"] . "','" . $q_nf_copia["nr_seq_fat"] . "C','" . $q_nf_copia["nr_pedido"] . "','" . $q_nf_copia["nr_pedcli"] . "','" . trata_acentos_nf($q_nf_copia["nome_familia"]) . "','" . $q_nf_copia["it_codigo"] . "','" . trata_acentos_nf($q_nf_copia["it_nome"]) . "','" . $q_nf_copia["cd_emitente"] . "','" . trata_acentos_nf($q_nf_copia["nome_emitente"]) . "','" . trata_acentos_nf($q_nf_copia["nome_fantasia"]) . "','" . trata_acentos_nf($q_nf_copia["nome_grupo"]) . "','" . trata_acentos_nf($q_nf_copia["nome_canal"]) . "','" . trata_acentos_nf($q_nf_copia["nome_ramo"]) . "','" . $peso_bruto . "','" . $qtde . "','" . $vl_tot_item . "','" . $vl_merc_sicm . "','CANCELADA','" . $vl_tot_item_us . "','" . $vl_merc_sicm_us . "','" . trata_acentos_nf($q_nf_copia["nome_familia_com"]) . "','" . $q_nf_copia["cnpj"] . "','" . $q_nf_copia["natureza"] . "','" . trata_acentos_nf($q_nf_copia["nome_pais"]) . "','" . trata_acentos_nf($q_nf_copia["nome_estado"]) . "','" . trata_acentos_nf($q_nf_copia["nome_cidade"]) . "','" . trata_acentos_nf($q_nf_copia["nome_regiao"]) . "','" . $q_nf_copia["cod_repr"] . "','" . trata_acentos_nf($q_nf_copia["nome_repr"]) . "','" . $vl_merc_liq . "','" . $q_nf_copia["pct_desc1"] . "','" . $q_nf_copia["pct_desc2"] . "','" . $q_nf_copia["pct_desc3"] . "','" . $q_nf_copia["pct_desc4"] . "','" . trata_acentos_nf($q_nf_copia["nome_transp"]) . "')";

            $rq = query($sql_insert);

            if ($rq != "1") {
                if(TipoBancoDados == 'mysql'){
                    $MensagemErro = mysql_error();
                }
                elseif(TipoBancoDados == 'mssql'){
                    $MensagemErro = mssql_get_last_message();
                }
                else{
                    $MensagemErro = '';
                }
                $QtdeErro++;
                GravaLogDetalhe($NumregLog,$sql_insert,'Erro SQL: '.$MensagemErro,print_r($a_canc,true),'Erro');
                echo $sql_insert;
            }
        }
        //echo $a_canc["nr-nota-fis"] . ' - ' . $a_canc["nr-seq-fat"] . ' - '. $a_canc["dt-cancela"].'<br>';
        $qtde_processada++;
    }
}

/* =========================================================================================================== */
// Excluir Devoluções
/* =========================================================================================================== */
if ($_GET["devolucao"] == "S") {
    echo 'Processando Devoluções : ' . date("H:i:s") . '<br>';

    query("delete from is_dm_notas where nat_operacao = 'DEVOLUCAO'");

    $sql_dev = 'select "cod-estabel", "nr-nota-fis", "serie", "nr-sequencia", "qt-devolvida", "vl-devol", "dt-devol"  from pub."devol-cli"';
    $q_dev = odbc_exec($cnx_nf, $sql_dev);
    while ($a_dev = odbc_fetch_array($q_dev)) {

        $q_nf_copia = farray(query("select * from is_dm_notas where cod_estabel = '" . $a_dev["cod-estabel"] . "' and serie = '" . $a_dev["serie"] . "' and nr_nota_fis = '" . $a_dev["nr-nota-fis"] . "' and nr_seq_fat = '" . $a_dev["nr-sequencia"] . "'"));
        // Se a NF já foi importada pode-se copiar
        if ($q_nf_copia["nr_nota_fis"]) {
            $qtde = $a_dev["qt-devolvida"] * -1;
            $peso_bruto = (($q_nf_copia["peso_bruto"] / $q_nf_copia["qt_faturada"]) * $qtde);
            $vl_tot_item = $a_dev["vl-devol"] * -1; // (($q_nf_copia["vl_tot_item"] / $q_nf_copia["qt_faturada"]) * $qtde);
            $vl_merc_sicm = (($q_nf_copia["vl_merc_sicm"] / $q_nf_copia["qt_faturada"]) * $qtde);
            $vl_tot_item_us = (($q_nf_copia["vl_tot_item_us"] / $q_nf_copia["qt_faturada"]) * $qtde);
            $vl_merc_sicm_us = (($q_nf_copia["vl_merc_sicm_us"] / $q_nf_copia["qt_faturada"]) * $qtde);
            $vl_merc_liq = (($q_nf_copia["vl_merc_liq"] / $q_nf_copia["qt_faturada"]) * $qtde);

            $sql_insert = 'INSERT INTO is_dm_notas ( dt_cadastro , hr_cadastro , id_usuario_cad , dt_alteracao , hr_alteracao , id_usuario_alt , id_pessoa, id_produto, cod_estabel , serie , dt_emis_nota , nr_nota_fis , nr_seq_fat , nr_pedido , nr_pedcli , nome_familia , it_codigo , it_nome , cd_emitente , nome_emitente , nome_fantasia , nome_grupo , nome_canal , nome_ramo , peso_bruto , qt_faturada , vl_tot_item , vl_merc_sicm ,  nat_operacao , vl_tot_item_us , vl_merc_sicm_us , nome_familia_com , cnpj , natureza , nome_pais , nome_estado , nome_cidade, nome_regiao, cod_repr, nome_repr, vl_merc_liq, pct_desc1, pct_desc2, pct_desc3, pct_desc4, nome_transp) VALUES (';
            $sql_insert .= "'" . date("Y-m-d") . "','" . date("H:i:s") . "','" . $nome_usuario . "','" . date("Y-m-d") . "','" . date("H:i:s") . "','" . $nome_usuario . "','";
            $sql_insert .= $q_nf_copia["id_pessoa"] . "','" . $q_nf_copia["id_produto"] . "','" . $q_nf_copia["cod_estabel"] . "','" . $q_nf_copia["serie"] . "','" . $a_dev["dt-devol"] . "','" . $q_nf_copia["nr_nota_fis"] . "','" . $q_nf_copia["nr_seq_fat"] . "D','" . $q_nf_copia["nr_pedido"] . "','" . $q_nf_copia["nr_pedcli"] . "','" . trata_acentos_nf($q_nf_copia["nome_familia"]) . "','" . $q_nf_copia["it_codigo"] . "','" . trata_acentos_nf($q_nf_copia["it_nome"]) . "','" . $q_nf_copia["cd_emitente"] . "','" . trata_acentos_nf($q_nf_copia["nome_emitente"]) . "','" . trata_acentos_nf($q_nf_copia["nome_fantasia"]) . "','" . trata_acentos_nf($q_nf_copia["nome_grupo"]) . "','" . trata_acentos_nf($q_nf_copia["nome_canal"]) . "','" . trata_acentos_nf($q_nf_copia["nome_ramo"]) . "','" . $peso_bruto . "','" . $qtde . "','" . $vl_tot_item . "','" . $vl_merc_sicm . "','DEVOLUCAO','" . $vl_tot_item_us . "','" . $vl_merc_sicm_us . "','" . trata_acentos_nf($q_nf_copia["nome_familia_com"]) . "','" . $q_nf_copia["cnpj"] . "','" . $q_nf_copia["natureza"] . "','" . trata_acentos_nf($q_nf_copia["nome_pais"]) . "','" . trata_acentos_nf($q_nf_copia["nome_estado"]) . "','" . trata_acentos_nf($q_nf_copia["nome_cidade"]) . "','" . trata_acentos_nf($q_nf_copia["nome_regiao"]) . "','" . $q_nf_copia["cod_repr"] . "','" . trata_acentos_nf($q_nf_copia["nome_repr"]) . "','" . $vl_merc_liq . "','" . $q_nf_copia["pct_desc1"] . "','" . $q_nf_copia["pct_desc2"] . "','" . $q_nf_copia["pct_desc3"] . "','" . $q_nf_copia["pct_desc4"] . "','" . trata_acentos_nf($q_nf_copia["nome_transp"]) . "')";

            $rq = query($sql_insert);

            if ($rq != "1") {
                if(TipoBancoDados == 'mysql'){
                    $MensagemErro = mysql_error();
                }
                elseif(TipoBancoDados == 'mssql'){
                    $MensagemErro = mssql_get_last_message();
                }
                else{
                    $MensagemErro = '';
                }
                $QtdeErro++;
                GravaLogDetalhe($NumregLog,$sql_insert,'Erro SQL: '.$MensagemErro,print_r($a_dev,true),'Erro');
                echo $sql_insert;
            }
            $qtde_processada++;
        }
        //echo $a_dev["nr-nota-fis"] . ' - ' . $a_dev["nr-sequencia"] . ' - '. $a_dev["dt-devol"]. '<br>';
    }
}

/* =========================================================================================================== */
// Fecha Conexões e atualiza data da importacao e tabela de cabecalho
/* =========================================================================================================== */

odbc_close($cnx_nf);
odbc_close($cnx_emit);
odbc_close($cnx_nat);
odbc_close($cnx_canal);
odbc_close($cnx_item);
odbc_close($cnx_moeda);

query("update is_dm_param set dt_base = '" . date("Y-m-d") . "', dt_base_fim = '" . date("Y-m-d") . "'");


// LOG
$obs = 'Período = '.$dt_base.' a '.$dt_base_fim;
if( $_GET["cancelada"]=='S') {
  $obs .= ' - Processamento de NF Canceladas';
}
if( $_GET["devolucao"]=='S') {
  $obs .= ' - Processamento de NF Devolvidas';
}
query("insert into is_log_analise_vendas(id_pai, usuario, dt_log, hr_log, qtde_processada, obs) values ('1','".$nome_usuario."','".date("Y-m-d")."','".date("H:i")."','".$qtde_processada."','".$obs."')");
// FIM LOG

echo "ATUALIZANDO TABELAS OASIS ".date("H:i:s").'<br>';

query("delete from is_dm_notas_cab");

$sql_cab = 'insert into is_dm_notas_cab (
cod_estabel, id_pessoa, nr_nota_fis, serie, nome_emitente, dt_emis_nota, nr_pedido, nr_pedcli, nome_repr, cd_emitente, nome_transp, pct_desc1, pct_desc2, vl_tot)
select cod_estabel, id_pessoa, nr_nota_fis, serie, nome_emitente, dt_emis_nota, nr_pedido, nr_pedcli, nome_repr, cd_emitente, nome_transp, pct_desc1, pct_desc2, sum(vl_tot_item) as vl_tot from is_dm_notas
group by cod_estabel, id_pessoa, nr_nota_fis,serie, nome_emitente, dt_emis_nota, nr_pedido, nr_pedcli, nome_repr, cd_emitente, nome_transp, pct_desc1, pct_desc2';
query($sql_cab);

query("delete from is_inf_compra_faturamento");
$sql_cab = 'insert into is_inf_compra_faturamento(id_pessoa, nome_produto, qtde, valor ) select id_pessoa, it_nome, sum(qt_faturada), sum(vl_tot_item) from is_dm_notas group by id_pessoa, it_nome';
query($sql_cab);

echo "ATUALIZANDO CAMPOS CANCELAMENTO E DEVOLUCAO ".date("H:i:s").'<br>';

query("update is_dm_notas set sn_cancelamento = 1 where nat_operacao = 'CANCELADA'");
query("update is_dm_notas set sn_cancelamento = 0 where nat_operacao <> 'CANCELADA'");
query("update is_dm_notas set sn_devolucao = 1 where nat_operacao = 'DEVOLUCAO'");
query("update is_dm_notas set sn_devolucao = 0 where nat_operacao <> 'DEVOLUCAO'");

echo 'Fim do Processamento : ' . date("H:i:s");

FinalizaLog($NumregLog,$MicroTimeInicio,0,0,$QtdeErro,0,$qtde_processada);

function trata_acentos_nf($texto) {
    //  return utf8_encode($texto);
   // No caso de Linux usar : return UTF8toiso8859_11($texto);
    return ($texto);
}

?>