<?php

header("Content-Type: text/html; charset=ISO-8859-1");
@session_start();
set_time_limit(6000); /* 100 minutos */
include_once('../../conecta.php');
include_once('../../functions.php');

echo '=========================================================' . '<BR>';
echo 'FATURAMENTO ( ATUALIZAÇÃO DE CAMPOS DO CRM ) : ' . date("H:i:s") . '<BR>';
echo '=========================================================' . '<BR>';

echo 'ATUALIZANDO CABEÇALHO : ' . date("H:i:s") . '<BR>';

$q_clientes = query("select distinct cd_emitente from is_dm_notas_cab where nome_emitente is null");
while ($a_clientes = farray($q_clientes)) {
    $a_pessoa = farray(query("select * from is_pessoa where id_pessoa_erp = '" . $a_clientes["cd_emitente"] . "'"));
    query("update is_dm_notas_cab set
            id_pessoa = '" . $a_pessoa["numreg"] . "',
            nome_emitente = '" . $a_pessoa["razao_social_nome"] . "'
            where cd_emitente = '" . $a_clientes["cd_emitente"] . "'");
}

$q_repr = query("select distinct cod_repr from is_dm_notas_cab where nome_repr is null");
while ($a_repr = farray($q_repr)) {
        $a_usuario = @farray(@query('select * from is_usuario where id_representante = ' . "'" . $a_repr["cod_repr"] . "'"));
    query("update is_dm_notas_cab set
            nome_repr = '" . $a_usuario["nome_usuario"] . "'
            where cod_repr = '" . $a_repr["cod_repr"] . "'");
}

echo 'ATUALIZANDO ITENS : ' . date("H:i:s") . '<BR>';

$q_it_nf = query("select * from is_dm_notas where nome_emitente is null");
while ($a_it_nf = farray($q_it_nf)) {

    $a_cab = farray(query("select * from is_dm_notas_cab where cod_estabel = '" . $a_it_nf["cod_estabel"] . "' and serie = '" . $a_it_nf["serie"] . "' and nr_nota_fis = '" . $a_it_nf["nr_nota_fis"] . "'"));
    $a_pessoa_crm = farray(query("select * from is_pessoa where id_pessoa_erp = '" . $a_cab["cd_emitente"] . "'"));
    $a_produto_crm = farray(query("select * from is_produto where id_produto_erp = '" . $a_it_nf["it_codigo"] . "'"));

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

    if ($a_cab["cod_repr"]) {
        $nome_repr = $a_cab["nome_repr"];
    }

    if ($a_pessoa_crm["id_canal_venda"]) {
        $a_canal = @farray(@query('select * from is_canal_venda where numreg = ' . "'" . $a_pessoa_crm["id_canal_venda"] . "'"));
        $nome_canal = str_replace('"', " ", str_replace("'", " ", $a_canal["nome_canal_venda"]));
    }

    $nome_familia = "";
    $nome_familia_com = "";

    $a_fam = @farray(@query('select * from is_familia where numreg = ' . "'" . $a_produto_crm["id_familia"] . "'"));
    $nome_familia = str_replace('"', " ", str_replace("'", " ", $a_fam["nome_familia"]));

    $a_fam_com = @farray(@query('select * from is_familia_comercial where numreg = ' . "'" . $a_produto_crm["id_familia_comercial"] . "'"));
    $nome_familia_com = str_replace('"', " ", str_replace("'", " ", $a_fam_com["nome_familia_comercial"]));


    $sql = "update is_dm_notas set
            id_pessoa = '" . $a_cab["id_pessoa"] . "',
            cd_emitente = '" . $a_cab["cd_emitente"] . "',
            nome_emitente = '" . $a_cab["nome_emitente"] . "',
            dt_emis_nota = '" . $a_cab["dt_emis_nota"] . "',
            nome_familia = '" . $nome_familia . "',
            id_produto = '" . $a_produto_crm["numreg"] . "',
            it_nome = '" . $a_produto_crm["nome_produto"] . "',
            nome_fantasia = '" . $nome_abrev . "',
            nome_grupo = '" . $nome_grupo . "',
            nome_canal = '" . $nome_canal . "',
            nome_ramo = '" . $nome_ramo . "',
            nome_familia_com = '" . $nome_familia_com . "',
            cnpj = '" . $cnpj . "',
            natureza = '" . $natureza . "',
            nome_pais = '" . $nome_pais . "',
            nome_estado = '" . $nome_estado . "',
            nome_cidade = '" . $nome_cidade . "',
            nome_regiao = '" . $nome_regiao . "',
            cod_repr = '" . $cod_repr . "',
            nome_repr = '" . $nome_repr . "'
            where cod_estabel = '" . $a_it_nf["cod_estabel"] . "' and serie = '" . $a_it_nf["serie"] . "' and nr_nota_fis = '" . $a_it_nf["nr_nota_fis"] . "'";

    $sql = str_replace("''", "NULL", $sql);
    query($sql);
}


echo '=========================================================' . '<BR>';
echo 'FIM DE PROCESSAMENTO : ' . date("H:i:s") . '<BR>';
echo '=========================================================' . '<BR>';
?>
