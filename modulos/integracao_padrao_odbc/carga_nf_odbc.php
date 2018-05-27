<?php
set_time_limit(7200);

echo "*============================================================*<br>";
echo "Carga de NFs Datasul ERP via ODBC<br>";
echo "*============================================================*<br>";

require("../../conecta.php");
require "../../funcoes.php";
require "../../functions.php";

//Carregando Parâmetros
$ap = farray(query("select * from is_dm_param"));

$mov2dis = $ap["odbc_nf"];
$ems2adm = $ap["odbc_emit"];
$ems2dis = $ap["odbc_canal"];
$ems2ind = $ap["odbc_item"];
$ems2uni = $ap["odbc_moeda"];


//Conecta com os bancos ODBC
$cnx_nf = odbc_connect($mov2dis, "crm", "crm") or die("Erro na conexão com o Database");
#$cnx_emit = odbc_connect($ems2adm, "sysprogress", "sysprogress") or die("Erro na conexão com o Database");
#$cnx_canal = odbc_connect($ems2dis, "sysprogress", "sysprogress") or die("Erro na conexão com o Database");
#$cnx_item = odbc_connect($ems2ind, "sysprogress", "sysprogress") or die("Erro na conexão com o Database");
#$cnx_moeda = odbc_connect($ems2uni, "sysprogress", "sysprogress") or die("Erro na conexão com o Database");

$id_moeda = $ap["id_moeda"];
$id_usuario = 'IMPORT';


// Checando Naturezas Válidas da Alpha
$dt_base = $ap["dt_base"];
$dt_base_fim = $ap["dt_base_fim"];
$custo_campo = $ap["custo_campo"];

// Excluir qualquer NF fora nas Naturezas de Operacao parametrizadas ou com data de emissao superior a que sera feita a importacao
query("delete from is_dm_notas where dt_emis_nota >= '" . $dt_base . "' and dt_emis_nota <= '" . $dt_base_fim . "'");

if($_GET["apagar"] == "S") {
    exit;
}

/* =========================================================================================================== */
// Importa NFs
/* =========================================================================================================== */
echo 'Importando Itens de Nota Fiscal : <br>';
    $sql = "SELECT
                t1.id_estabelecimento_erp,
                t1.serie,
                t1.numero_nota,
                t1.item_nota,
                t1.id_pedido_erp,
                t1.peso,
                t1.qtde,
                t1.vl_bruto,
                t1.vl_icms,
                t1.vl_ipi,
                t1.vl_frete,
                t2.cfop,
                t2.dt_emissao,
                t2.id_pessoa_erp,
                t2.situacao
            FROM
                vw_is_int_nf_itens t1, vw_is_int_nf t2
            WHERE
                (t1.numero_nota = t2.numero_nota AND t1.serie = t2.serie)
            AND
                (t2.dt_emissao BETWEEN '".$dt_base."' AND '".$dt_base_fim."')
            ORDER BY
                t1.serie,t1.numero_nota";

if(($_GET['devolucao'] != 'S')){

    echo "Buscando NFs " . date("H:i:s") . '<br>';
    $q_it_nf = odbc_exec($cnx_nf, $sql);
    $nf_refer = "";
    
    query("delete from is_dm_notas where nat_operacao = 'CANCELADA'");

    while ($a_it_nf = odbc_fetch_array($q_it_nf)) {

        // Se mudou a NF
        if($nf_refer != $a_it_nf["numero_nota"]){
            //echo $a_it_nf["nr-nota-fis"] . ' - ' . $nome_emitente . " " . $a_it_nf["dt-emis-nota"] . ' ' . date("H:i:s") . '<br>';
            $nf_refer = $a_it_nf["numero_nota"];
            // Pesquisa o Cliente e outras tabelas relacionadas
            $a_emit = farray(query("SELECT razao_social_nome,id_tp_pessoa,cnpj_cpf,id_ramo_atividade,pais, uf,cidade,bairro,id_regiao,id_grupo_cliente,id_canal_venda,id_representante_padrao FROM is_pessoa WHERE id_pessoa_erp != '' AND id_pessoa_erp = '".$a_it_nf['id_pessoa_erp']."'"));
            $a_produto_crm = farray(query("select numreg,nome_produto from is_produto where id_produto_erp = '" . $a_it_nf["id_pedido_erp"] . "'"));
            $nome_canal = "";
            $nome_grupo = "";
            $nome_repr = "";
            $cod_repr = "";
            $natureza = $a_emit["natureza"];
            $nome_emitente = str_replace('"', " ", str_replace("'", " ", $a_emit["razao_social_nome"]));
            $nome_abrev = '';
            $cnpj = $a_emit["cnpj_cpf"];
            if($a_emit["id_ramo_atividade"]){
                $a_ramo = @farray(@query('select * from is_ramo where numreg = ' . "'" . $a_emit["atividade"] . "'"));
                $nome_ramo = str_replace('"', " ", str_replace("'", " ", $a_ramo["nome_ramo"]));
                if(empty($nome_ramo)){
                    $nome_ramo = $a_emit["atividade"];
                }
            }

            $nome_pais = str_replace('"', " ", str_replace("'", " ", $a_emit["pais"]));
            $nome_estado = str_replace('"', " ", str_replace("'", " ", $a_emit["uf"]));
            $nome_cidade = str_replace('"', " ", str_replace("'", " ", $a_emit["cidade"]));
            $micro_regiao = str_replace('"', " ", str_replace("'", " ", $a_emit["id_regiao"]));
            /*
            if ($a_emit["cod-gr-cli"]) {
                $a_gr_cli = odbc_fetch_array(odbc_exec($CnxODBC, 'select "descricao" from pub."gr-cli" where "cod-gr-cli" = ' . "'" . $a_emit["cod-gr-cli"] . "'"));
                $nome_grupo = str_replace('"', " ", str_replace("'", " ", $a_gr_cli["descricao"]));
            }

//		  $a_nf_cad = odbc_fetch_array(odbc_exec($CnxODBC,'select "cod-rep" from pub."nota-fiscal" where "cod-estabel" = '."'".$a_it_nf["cod-estabel"]."' and ".'"nr-nota-fis" = '."'".$a_it_nf["nr-nota-fis"]."'"));

            if ($a_it_nf["cod-rep"]) {
                $a_repr = odbc_fetch_array(odbc_exec($CnxODBC, 'select "nome" from pub."repres" where "cod-rep" = ' . "'" . $a_it_nf["cod-rep"] . "'"));
                $nome_repr = str_replace('"', " ", str_replace("'", " ", $a_repr["nome"]));
                $cod_repr = $a_it_nf["cod-rep"];
            }

            if ($a_emit["cod-canal-venda"]) {
                $a_canal = odbc_fetch_array(odbc_exec($CnxODBC, 'select "descricao" from pub."canal-venda" where "cod-canal-venda" = ' . "'" . $a_emit["cod-canal-venda"] . "'"));
                $nome_canal = str_replace('"', " ", str_replace("'", " ", $a_canal["descricao"]));
            }
            */
            //moedas
            $dt_refer = $a_it_nf["dt_emissao"];
            /*
            $a_moeda = odbc_fetch_array(odbc_exec($CnxODBC, "SELECT cotacao FROM pub.cotacao WHERE \"mo-codigo\"='" . $id_moeda . "' AND \"ano-periodo\" = '" . substr($dt_refer, 0, 4) . substr($dt_refer, 5, 2) . "'"));
            $str_cotacoes = $a_moeda['cotacao'];
            $array_cotacoes = explode(";", $str_cotacoes);
            $ind_array_cotacoes = (substr($dt_refer, 8, 2) * 1) - 1;

            $cotacao_dia_anterior = $array_cotacoes[$ind_array_cotacoes] * 1;
            if ($cotacao_dia_anterior <= 0) {
                $cotacao_dia_anterior = 1;
            }
             *
             */
        }

        $nome_familia = "";
        $nome_familia_com = "";
        $it_nome = "";
        $linha = $a_it_nf["qt-faturada"];
        $aqtde = explode(';', $linha);
        $vl_tot_item_us = 0;
        $vl_merc_sicm_us = 0;

        $vl_merc_liq = $a_it_nf["vl_bruto"];
        $vl_merc_liq_us = 0;

        $vl_frete_it_us = 0;
        
        $it_nome = str_replace('"', " ", str_replace("'", " ", $a_produto_crm["nome_produto"]));
        $nome_familia = str_replace('"', " ", str_replace("'", " ", $a_item[""]));
        $vl_custo = 0;

        #$a_fam = odbc_fetch_array(odbc_exec($CnxODBC, 'select "descricao" from pub."familia" where "fm-codigo" = ' . "'" . $a_item["fm-codigo"] . "'"));
        #$nome_familia = str_replace('"', " ", str_replace("'", " ", $a_fam[""]));

        #$a_fam_com = odbc_fetch_array(odbc_exec($CnxODBC, 'select "descricao" from pub."fam-comerc" where "fm-cod-com" = ' . "'" . $a_item["fm-cod-com"] . "'"));
        #$nome_familia_com = str_replace('"', " ", str_replace("'", " ", $a_fam_com["descricao"]));

        $vl_margem = 0;

        if ($a_it_nf['situacao'] == '2') {
            $qtde = -1;
            $peso_bruto = $peso_bruto * $qtde;
            $vl_tot_item = $vl_tot_item * $qtde;
            $vl_merc_sicm = $vl_merc_sicm * $qtde;
            $vl_tot_item_us = $vl_tot_item_us * $qtde;
            $vl_merc_sicm_us = $vl_merc_sicm_us * $qtde;
            $vl_merc_liq = $vl_merc_liq * $qtde;
            $a_it_nf["cfop"] = 'CANCELADA';
        }


        $ArSqlInsert = array(
            'dt_cadastro'       => date("Y-m-d"),
            'hr_cadastro'       => date("H:i:s"),
            'id_usuario_cad'    => $id_usuario,
            'dt_alteracao'      => date("Y-m-d"),
            'hr_alteracao'      => date("H:i:s"),
            'id_usuario_alt'    => $id_usuario,
            'id_pessoa'         => $a_emit["numreg"],
            'id_produto'        => $a_produto_crm["numreg"],
            'cod_estabel'       => $a_it_nf["id_estabelecimento_erp"],
            'serie'             => $a_it_nf["serie"],
            'dt_emis_nota'      => $a_it_nf["dt_emissao"],
            'nr_nota_fis'       => $a_it_nf["numero_nota"],
            'nr_seq_fat'        => $a_it_nf["item_nota"],
            #'nr_pedido'         => $a_it_nf[""],
            #'nr_pedcli'         => $a_it_nf["nr-pedcli"],
            'nome_familia'      => $nome_familia,
            'it_codigo'         => $a_it_nf["id_pedido_erp"],
            'it_nome'           => substr($it_nome,0,99),
            'cd_emitente'       => $a_it_nf["id_pessoa_erp"],
            'nome_emitente'     => $nome_emitente,
            'nome_fantasia'     => $nome_abrev,
            'nome_grupo'        => $nome_grupo,
            'nome_canal'        => $nome_canal,
            'nome_ramo'         => $nome_ramo,
            'peso_bruto'        => $a_it_nf["peso"],
            'qt_faturada'       => $a_it_nf["qtde"],
            'vl_tot_item'       => $a_it_nf["vl_bruto"],
            'vl_merc_sicm'      => $vl_merc_liq,
            'nat_operacao'      => $a_it_nf["cfop"],
            'vl_tot_item_us'    => $vl_tot_item_us,
            'vl_merc_sicm_us'   => $vl_merc_liq_us,
            'nome_familia_com'  => $nome_familia_com,
            'cnpj'              => $cnpj,
            'natureza'          => $natureza,
            'nome_pais'         => $nome_pais,
            'nome_estado'       => $nome_estado,
            'nome_cidade'       => $nome_cidade,
            'nome_regiao'       => $nome_regiao,
            'cod_repr'          => $cod_repr,
            'nome_repr'         => $nome_repr,
            'vl_merc_liq'       => $a_it_nf["vl_bruto"]
        );
        $SqlInsert = $ArSqlInsert = AutoExecuteSql(TipoBancoDados, 'is_dm_notas', $ArSqlInsert, 'INSERT');
        $rq = query($SqlInsert);
        if ($rq != "1") {
                echo $sql_insert;
        }
    }
}

/* =========================================================================================================== */
// Excluir Devoluções
/* =========================================================================================================== */
if($_GET["devolucao"] == "S"){
    echo 'Processando Devoluções: '.date("H:i:s").'<br>';

    query("delete from is_dm_notas where nat_operacao = 'DEVOLUCAO'");

    $sql_dev = 'SELECT * FROM vw_is_int_devolucoes';
    $q_dev = odbc_exec($cnx_nf, $sql_dev);
    while ($a_dev = odbc_fetch_array($q_dev)) {

        $q_nf_copia = farray(query("select * from is_dm_notas where serie = '" . $a_dev["serie"] . "' and nr_nota_fis = '" . $a_dev['nr_nota_fis'] . "' and nr_seq_fat = '" . $a_dev['item_nota'] . "'"));
        // Se a NF já foi importada pode-se copiar
        if ($q_nf_copia["nr_nota_fis"]) {
            $qtde = $a_dev["qt-devolvida"] * -1;
            $peso_bruto = (($q_nf_copia["peso_bruto"] / $q_nf_copia["qt_faturada"]) * $qtde);
            $vl_tot_item = $a_dev["vl-devol"] * -1; // (($q_nf_copia["vl_tot_item"] / $q_nf_copia["qt_faturada"]) * $qtde);
            $vl_merc_sicm = (($q_nf_copia["vl_merc_sicm"] / $q_nf_copia["qt_faturada"]) * $qtde);
            $vl_tot_item_us = (($q_nf_copia["vl_tot_item_us"] / $q_nf_copia["qt_faturada"]) * $qtde);
            $vl_merc_sicm_us = (($q_nf_copia["vl_merc_sicm_us"] / $q_nf_copia["qt_faturada"]) * $qtde);
            $vl_merc_liq = (($q_nf_copia["vl_merc_liq"] / $q_nf_copia["qt_faturada"]) * $qtde);

            $sql_insert = 'INSERT INTO is_dm_notas ( dt_cadastro , hr_cadastro , id_usuario_cad , dt_alteracao , hr_alteracao , id_usuario_alt , id_pessoa, id_produto, cod_estabel , serie , dt_emis_nota , nr_nota_fis , nr_seq_fat , nr_pedido , nr_pedcli , nome_familia , it_codigo , it_nome , cd_emitente , nome_emitente , nome_fantasia , nome_grupo , nome_canal , nome_ramo , peso_bruto , qt_faturada , vl_tot_item , vl_merc_sicm ,  nat_operacao , vl_tot_item_us , vl_merc_sicm_us , nome_familia_com , cnpj , natureza , nome_pais , nome_estado , nome_cidade, nome_regiao, cod_repr, nome_repr, vl_merc_liq) VALUES (';
            $sql_insert .= "'" . date("Y-m-d") . "','" . date("H:i:s") . "','" . $id_usuario . "','" . date("Y-m-d") . "','" . date("H:i:s") . "','" . $id_usuario . "','";
            $sql_insert .= $q_nf_copia["id_pessoa"] . "','" . $q_nf_copia["id_produto"] . "','" . $q_nf_copia["cod_estabel"] . "','" . $q_nf_copia["serie"] . "','" . $a_dev["dt-devol"] . "','" . $q_nf_copia["nr_nota_fis"] . "','" . $q_nf_copia["nr_seq_fat"] . "D','" . $q_nf_copia["nr_pedido"] . "','" . $q_nf_copia["nr_pedcli"] . "','" . trata_acentos($q_nf_copia["nome_familia"]) . "','" . $q_nf_copia["it_codigo"] . "','" . trata_acentos($q_nf_copia["it_nome"]) . "','" . $q_nf_copia["cd_emitente"] . "','" . trata_acentos($q_nf_copia["nome_emitente"]) . "','" . trata_acentos($q_nf_copia["nome_fantasia"]) . "','" . trata_acentos($q_nf_copia["nome_grupo"]) . "','" . trata_acentos($q_nf_copia["nome_canal"]) . "','" . trata_acentos($q_nf_copia["nome_ramo"]) . "','" . $peso_bruto . "','" . $qtde . "','" . $vl_tot_item . "','" . $vl_merc_sicm . "','DEVOLUCAO','" . $vl_tot_item_us . "','" . $vl_merc_sicm_us . "','" . trata_acentos($q_nf_copia["nome_familia_com"]) . "','" . $q_nf_copia["cnpj"] . "','" . $q_nf_copia["natureza"] . "','" . trata_acentos($q_nf_copia["nome_pais"]) . "','" . trata_acentos($q_nf_copia["nome_estado"]) . "','" . trata_acentos($q_nf_copia["nome_cidade"]) . "','" . trata_acentos($q_nf_copia["nome_regiao"]) . "','" . $q_nf_copia["cod_repr"] . "','" . trata_acentos($q_nf_copia["nome_repr"]) . "','" . $vl_merc_liq . "')";

            $rq = query(TextoBD("mysql", $sql_insert));

            if ($rq != "1") {
                echo $sql_insert;
            }
        }
        //echo $a_dev["nr-nota-fis"] . ' - ' . $a_dev["nr-sequencia"] . ' - '. $a_dev["dt-devol"]. '<br>';
    }
}

/* =========================================================================================================== */
// Fecha Conexões e atualiza data da importacao e tabela de cabecalho
/* =========================================================================================================== */

odbc_close($cnx_nf);

query("update is_dm_param set dt_base = '" . date("Y-m-d") . "', dt_base_fim = '" . date("Y-m-d") . "'");

query("delete from is_dm_notas_cab");

$sql_cab = 'insert into is_dm_notas_cab (
cod_estabel, id_pessoa, nr_nota_fis, serie, nome_emitente, dt_emis_nota, nr_pedido, nr_pedcli, nome_repr, cd_emitente, nat_operacao, vl_tot)
select cod_estabel, id_pessoa, nr_nota_fis, serie, nome_emitente, dt_emis_nota, nr_pedido, nr_pedcli, nome_repr, cd_emitente, nat_operacao, sum(vl_tot_item) as vl_tot from is_dm_notas
group by cod_estabel, id_pessoa, nr_nota_fis,serie, nome_emitente, dt_emis_nota, nr_pedido, nr_pedcli, nome_repr, cd_emitente, nat_operacao';
query($sql_cab);

echo 'Fim do Processamento : ' . date("H:i:s");

function trata_acentos($texto) {
  //  return utf8_encode($texto);
      return ($texto);
}
?>