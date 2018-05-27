<?php

// Itens da Proposta
if($id_funcao == 'is_propostas_prod'){
    // Consistencia na Inclusao
    if($opc == 'incluir'){
        $a_existe_prpr_produto = farray(query("SELECT COUNT(*) AS CNT FROM is_proposta_prod WHERE id_proposta = ".$_POST['edtid_proposta']." AND id_produto = ".$_POST['edtid_produto']));
        if(($a_existe_prpr_produto['CNT'] * 1) >= 1){
            $Url->AlteraParam('ppostback',$numreg_postback);
            $url_retorno = $Url->getUrl();
            echo "<script language=\"javascript\">alert('O produto não pode ser incluido mais de uma vez!'); window.location.href = '".$url_retorno."';</script>";
            exit;
        }
    }
    if($opc != 'excluir'){
        // Consistencia no Desconto
        if(($_POST['edtpct_desc'] * 1) > 100){
            $Url->AlteraParam('ppostback',$numreg_postback);
            $url_retorno = $Url->getUrl();
            echo "<script language=\"javascript\">alert('O desconto não pode ser maior que 100% !'); window.location.href = '".$url_retorno."';</script>";
            exit;
        }else{
            if((NumeroSetBD($_POST['edtqtde'])) * 1 < 1){
                $Url->AlteraParam('ppostback',$numreg_postback);
                $url_retorno = $Url->getUrl();
                echo "<script language=\"javascript\">alert('Campo Qtde deve ser maior que 1 !'); window.location.href = '".$url_retorno."';</script>";
                exit;
            }
            $pr_item_qtde = NumeroSetBD($_POST['edtqtde']) * 1;
            $pr_item_valor = NumeroSetBD($_POST['edtvalor']) * 1;
            $pr_item_pct_desc = NumeroSetBD($_POST['edtpct_desc']) * 1;
            $_POST['edtvalor_total'] = number_format($pr_item_qtde * ($pr_item_valor - ($pr_item_pct_desc * $pr_item_valor / 100)),2,',','.');
        }
        $proposta_itens_id = $_POST['edtid_proposta'];
    }else{
        // No caso de exclusão deve-se guardar o id_proptunidade para usar no post_after_proptunidade.php
        $a_bck_pr_itens_id_prop = farray(query("SELECT id_proposta FROM is_proposta_prod WHERE numreg = ".$_GET['pnumreg']));
        $proposta_itens_id = $a_bck_pr_itens_id_prop["id_proposta"];
    }
}

?>