<?php

// Itens do Contrato
if ($id_funcao == 'is_contratos_obj') {
    $a_total_obj = farray(query("SELECT sum(valor_rec) AS total, sum(vl_repasse1) as tot_repasse FROM is_contrato_obj WHERE sn_ativo_rec = 1 and id_contrato = ".$obj_id_contrato));
    query("update is_contrato set valor_rec = ".($a_total_obj["total"] * 1).", vl_repasse1 = ".($a_total_obj["tot_repasse"] * 1)." WHERE numreg = ".$obj_id_contrato);
    if ($opc != 'excluir') {
        $a_existe_nr_serie_equip = farray(query("select nr_serie from is_pessoa_equipamento where nr_serie = '".($_POST["edtnr_serie"]) . "' and id_pessoa = '".($_POST["edtid_pessoa"])."'"));
        if (empty($a_existe_nr_serie_equip["nr_serie"])) {
            query("insert into is_pessoa_equipamento(id_pessoa,nr_serie,id_produto,endereco) values ('".($_POST["edtid_pessoa"])."','".($_POST["edtnr_serie"])."','".($_POST["edtid_produto"])."','".($_POST["edtlocal"])."')");
        }
    }
}
?>