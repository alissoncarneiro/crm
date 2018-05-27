<?php

// SEGMENTACAO
if ($id_funcao == 'seg_cad_lista') {
    $id_lista = $_POST["edtid_lista"];
    // Limpa todo o conteudo da lista
    if ($_POST["edtsn_limpar_lista"] == '1') {
        query("delete from is_lista_pessoa where id_lista = '" . $id_lista . "'");
    }

    $filtro_segmentacao = trata_tags_sql($_POST["edtsql_filtro_conta"]);
    $filtro_segmentacao_contato = trata_tags_sql($_POST["edtsql_filtro_contato"]);

    // Prepara SQLs
    if ($filtro_segmentacao) {
        $sql_segmenta_pessoa = "select * from is_pessoa where " . $filtro_segmentacao;
        $sql_remove_pessoa = "delete from is_lista_pessoa where id_pesssoa in ( select numreg from is_pessoa " . $filtro_segmentacao . ")";
    } else {
        $sql_segmenta_pessoa = "select * from is_pessoa";
        $sql_remove_pessoa = "";
    }
    if ($filtro_segmentacao_contato) {
        $sql_segmenta_contato = "select * from is_contato where " . $filtro_segmentacao_contato . " and id_empresa = ";
        $sql_remove_contato = "delete from is_lista_pessoa where id_pesssoa_contato in ( select numreg from is_contato " . $filtro_segmentacao_contato . ")";
    } else {
        $sql_segmenta_contato = "select * from is_contato where id_empresa = ";
        $sql_remove_contato = "";
    }

    // Remove da Lista o resultado da segmentaчуo
    if ($_POST["edtsn_remover_lista"] == '1') {
        if ($filtro_segmentacao) {
            @query($sql_remove_pessoa);
        }
        if ($filtro_segmentacao_contato) {
            @query($sql_remove_contato);
        }
    } else {
        //Verifica se o campo filtra conta ou contato esta vazio 'Alisson'//

        if($filtro_segmentacao!= '' ||$filtro_segmentacao_contato!=''){

                // Ou inclui na Lista o resultado da segmentaчуo
                $ntot_conta = 0;
                $ntot_contato = 0;
                $sql_atualiza = query($sql_segmenta_pessoa);
                while ($qry_atualiza = farray($sql_atualiza)) {
                    $ntot_conta++;
                    // Se deve incluir contatos - щ necessсrio aplicar o filtro de contatos para adicionar cada contato de cada conta na lista
                    if ($_POST["edtsn_incluir_contato_lista"] == '1') {
                        $sql_atualiza_contato = query($sql_segmenta_contato . ' ' . $qry_atualiza["numreg"]);
                        $sn_possui_contato = 0;
                        while ($qry_atualiza_contato = farray($sql_atualiza_contato)) {
                            $sn_possui_contato = 1;
                            $sql_exec = sql_insert_lista_pessoa($id_lista, $qry_atualiza, $qry_atualiza_contato);
                            @query($sql_exec);
                            $ntot_contato++;
                        }
                        // Se nуo achou contato deve incluir sem os dados do contato
                        if ($sn_possui_contato == 0) {
                            $sql_exec = sql_insert_lista_pessoa($id_lista, $qry_atualiza, $qry_atualiza_contato);
                            @query($sql_exec);
                        }
                    } else {
                        $sql_exec = sql_insert_lista_pessoa($id_lista, $qry_atualiza, $qry_atualiza_contato);
                        @query($sql_exec);
                    }
                }
            }
        }
    // Recupera totais da Lista
    $a_tot_lista_conta = farray(query("select count(distinct id_pessoa) as total from is_lista_pessoa where id_lista = '" . $id_lista . "'"));
    $a_tot_lista_contato = farray(query("select count(distinct id_pessoa_contato) as total from is_lista_pessoa where id_lista = '" . $id_lista . "'"));
    //// Atualiza Resultado da Lista
    query("update is_lista set qtde_resultado_conta = ".($a_tot_lista_conta["total"]*1).", qtde_resultado_contato = ".($a_tot_lista_contato["total"]*1)." where numreg = '" . $id_lista . "'");
    query("update is_segmentacao set qtde_resultado_conta = ".($ntot_conta).", qtde_resultado_contato = ".($ntot_contato)." where numreg = '" . $pnumreg . "'");
}

function sql_insert_lista_pessoa($id_lista, $qry_atualiza, $qry_atualiza_contato) {
    global $tipoBanco;

    $razao_social_nome = str_replace("'", " ", TextoBD($tipoBanco, nl2br($qry_atualiza["razao_social_nome"])));
    $fantasia_apelido = str_replace("'", " ", TextoBD($tipoBanco, nl2br($qry_atualiza["razao_social_nome"])));
    $endereco = TextoBD($tipoBanco, nl2br(str_replace("'", " ", $qry_atualiza["endereco"])));
    $numero = TextoBD($tipoBanco, nl2br($qry_atualiza["numero"]));
    $complemento = TextoBD($tipoBanco, nl2br(str_replace("'", " ", $qry_atualiza["complemento"])));
    $bairro = TextoBD($tipoBanco, nl2br(str_replace("'", " ", $qry_atualiza["bairro"])));
    $cidade = TextoBD($tipoBanco, nl2br(str_replace("'", " ", $qry_atualiza["cidade"])));
    $nome_contato = str_replace("'", " ", TextoBD($tipoBanco, nl2br($qry_atualiza_contato["nome"])));

    $id_pessoa_contato = $qry_atualiza_contato["numreg"];
    $id_cargo = $qry_atualiza_contato["id_cargo"];
    $id_area = $qry_atualiza_contato["id_area"];

    if (empty($id_pessoa_contato)) {
        $id_pessoa_contato = 'NULL';
    }
    if (empty($id_cargo)) {
        $id_cargo = 'NULL';
    }
    if (empty($id_area)) {
        $id_area = 'NULL';
    }

    $ArSqlInsert = array(
        'id_lista' => $id_lista,
        'id_pessoa' => $qry_atualiza["numreg"],
        'razao_social_nome' => $razao_social_nome,
        'fantasia_apelido' => $fantasia_apelido,
        'tel1' => $qry_atualiza["tel1"],
        'tel2' => $qry_atualiza["tel2"],
        'email' => $qry_atualiza["email"],
        'cep' => $qry_atualiza["cep"],
        'endereco' => $endereco,
        'numero' => $numero,
        'complemento' => $complemento,
        'bairro' => $bairro,
        'cidade' => $cidade,
        'uf' => $qry_atualiza["uf"],
        'id_pessoa_contato' => $id_pessoa_contato,
        'nome_contato' => $nome_contato,
        'email_profissional' => $qry_atualiza_contato["email_profissional"],
        'email_pessoal' => $qry_atualiza_contato["email_pessoal"],
        'tel1_contato' => $qry_atualiza_contato["tel1_contato"],
        'tel2_contato' => $qry_atualiza_contato["tel2_contato"],
        'id_cargo' => $id_cargo,
        'id_area' => $id_area
    );
    $SqlInsert = AutoExecuteSql(TipoBancoDados, 'is_lista_pessoa', $ArSqlInsert, 'INSERT');

    return str_replace("'NULL'","NULL",$SqlInsert);
}
?>