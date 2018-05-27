<?php

if($opc != 'excluir'){
    // Consistencia no Desconto
    if(($_POST['edtpct_reajuste'] * 1) > 100){
        $Url->AlteraParam('ppostback',$numreg_postback);
        $url_retorno = $Url->getUrl();
        echo "<script language=\"javascript\">alert('O reajuste não pode ser maior que 100% !'); window.location.href = '".$url_retorno."';</script>";
        exit;
    }else{
        $_POST['edtpct_reajuste'] = (str_replace(",",".",$_POST['edtpct_reajuste']) * 1);
    }
}
?>