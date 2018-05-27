<?php

// Consistências de Data de Início em Fim
if ((($id_funcao == 'is_meta_avancada_vend') || ($id_funcao == 'is_meta_avancada_gerenc') || ($id_funcao == 'is_meta_avancada_repr')) && $opc != 'excluir') {
    $dt_inicio_trat = DataSetBD($_POST["edtdt_inicio"]);
    $dt_fim_trat = DataSetBD($_POST["edtdt_fim"]);
    if ($dt_inicio_trat > $dt_fim_trat) {
        $Url->AlteraParam('ppostback', $numreg_postback);
        $url_retorno = $Url->getUrl();
        echo "<script language=\"javascript\">alert('Data de Início não pode ser maior que Data de Fim !'); window.location.href = '" . $url_retorno . "';</script>";
        exit;
    }
}

// Consistência de Soma de Pesos que deve ser igual a 100
if ((($id_funcao == 'is_meta_avancada_vend') || ($id_funcao == 'is_meta_avancada_gerenc')) && $opc != 'excluir') {
    $vl_peso1 = str_replace(",",".",str_replace(".","",$_POST["edtpeso_meta_vl_unitario"]))*1;
    $vl_peso2 = str_replace(",",".",str_replace(".","",$_POST["edtpeso_meta_qtde"]))*1;
    if (($vl_peso1+$vl_peso2) != 100) {
        $Url->AlteraParam('ppostback', $numreg_postback);
        $url_retorno = $Url->getUrl();
        echo "<script language=\"javascript\">alert('A soma dos pesos deve ser igual a 100 !'); window.location.href = '" . $url_retorno . "';</script>";
        exit;
    }
}

// Consistências de Valor de Início e Fim
if ((($id_funcao == 'is_meta_faixas_preco_comissao')) && $opc != 'excluir') {
    $vl_inicio_trat = str_replace(",",".",str_replace(".","",$_POST["edtvl_unit_inicial"]))*1;
    $vl_final_trat = str_replace(",",".",str_replace(".","",$_POST["edtvl_unit_final"]))*1;
    if ($vl_inicio_trat > $vl_final_trat) {
        $Url->AlteraParam('ppostback', $numreg_postback);
        $url_retorno = $Url->getUrl();
        echo "<script language=\"javascript\">alert('Valor de Início não pode ser maior que o Valor Final !'); window.location.href = '" . $url_retorno . "';</script>";
        exit;
    }

}


?>
