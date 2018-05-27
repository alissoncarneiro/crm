<?php

// Contrato
if($id_funcao == 'is_contratos'){
    if($opc != 'excluir'){
        $dt_inicio_trat = DataSetBD($_POST["edtdt_inicio"]);
        $dt_prev_fim_trat = DataSetBD($_POST["edtdt_fim"]);
        if($dt_inicio_trat > $dt_prev_fim_trat){
            $Url->AlteraParam('ppostback',$numreg_postback);
            $url_retorno = $Url->getUrl();
            echo "<script language=\"javascript\">alert('Data de Início não pode ser maior que Data de Fim !'); window.location.href = '".$url_retorno."';</script>";
            exit;
        }
    }
}
// Itens do Contrato
if($id_funcao == 'is_contratos_obj'){
    if($opc != 'excluir'){
        // Consistencia de No de Série
        $a_existe_num_serie = farray(query("SELECT nr_contrato FROM is_contrato_obj WHERE numreg <> ".$_POST["pnumreg"]." and nr_serie = '".$_POST['edtnr_serie']."'"));
        $contrato = farray(query("SELECT nr_contrato FROM is_contrato WHERE numreg = '".$_POST["edtid_contrato"]."'"));
        if($a_existe_num_serie["nr_contrato"]){
            $Url->AlteraParam('ppostback',$numreg_postback);
            $url_retorno = $Url->getUrl();
            echo "<script language=\"javascript\">alert('Este número de série já foi cadastrado no Contrato ".$contrato["nr_contrato"]." !'); window.location.href = '".$url_retorno."';</script>";
            exit;
        }
        // Consistencia no Desconto
        if((NumeroSetBD($_POST['edtqtde_rec'])) * 1 < 1){
            $Url->AlteraParam('ppostback',$numreg_postback);
            $url_retorno = $Url->getUrl();
            echo "<script language=\"javascript\">alert('Campo Qtde deve ser maior que 1 !'); window.location.href = '".$url_retorno."';</script>";
            exit;
        }
        $obj_item_qtde = NumeroSetBD($_POST['edtqtde_rec']) * 1;
        $obj_item_valor = NumeroSetBD($_POST['edtvl_unitario_rec']) * 1;
        $_POST['edtvalor_rec'] = number_format($obj_item_qtde * $obj_item_valor,2,',','.');

        $obj_repasse_qtde = NumeroSetBD($_POST['edtqtde_repas1']) * 1;
        $obj_repasse_valor = NumeroSetBD($_POST['edtvl_unitario_repas1']) * 1;
        $_POST['edtvl_repasse1'] = number_format($obj_repasse_qtde * $obj_repasse_valor,2,',','.');

        $obj_id_contrato = $_POST['edtid_contrato'];
    }else{
        // No caso de exclusão deve-se guardar o id_contrato para usar no post_after_contrato_obj.php
        $a_bck_obj_id = farray(query("SELECT id_contrato FROM is_contrato_obj WHERE numreg = ".$_GET['pnumreg']));
        $obj_id_contrato = $a_bck_obj_id["id_contrato"];
    }
}
?>
