<?php

/*
 * Tratamento para cadastro de prospect e clientes
 * Tratamento de duplicidade do campo de cnpj e Transformando prospect em cliente
 */
if($id_funcao == 'pessoa' && $opc != 'excluir'){
    $SnIntegradoERP = (GetParam('INT_ERP') == 1)?true:false;
    if($opc == 'incluir' && $_POST['pgetcustom'] == 'cliente'){
        $SqlUpdatePessoa = "UPDATE is_pessoa SET sn_cliente = 1, sn_importado_erp = 1, sn_exportado_erp = 1 WHERE numreg = '".$pnumreg."'";
        $QryUpdatePessoa = query($SqlUpdatePessoa);
    }
    if($_POST['ptpec'] == 1){
        $TransformarProspectEmCliente = $Pessoa->TranformarEmCliente();
        if($TransformarProspectEmCliente[0] != true){
            $Url->AlteraParam('ppostback',$numreg_postback);
            $_POST['url_retorno'] = $Url->getUrl();
            echo "<script language=\"javascript\">alert('".$TransformarProspectEmCliente[1]."'); window.location.href = '".$_POST['url_retorno']."';</script>";
            exit;
        } else{
            echo "<script language=\"javascript\">alert('".$TransformarProspectEmCliente[1]."');</script>";
            $New_pfixo = str_replace('sn_prospect@igual1','sn_cliente@igual1',$Url->getParam('pfixo'));
            $Url->AlteraParam('pfixo',$New_pfixo);
            $Url->AlteraParam('pgetcustom','cliente');
            $Url->RemoveParam('ptpec');
            $url_retorno = $Url->getURL();
        }
    }
    elseif($_POST['ptsep'] == 1){
        $TransformarSuspectEmProspect = $Pessoa->TranformarEmProspect();
        if($TransformarSuspectEmProspect[0] != true){
            $Url->AlteraParam('ppostback',$numreg_postback);
            $_POST['url_retorno'] = $Url->getUrl();
            echo "<script language=\"javascript\">alert('".$TransformarSuspectEmProspect[1]."'); window.location.href = '".$_POST['url_retorno']."';</script>";
            exit;
        } else{
            echo "<script language=\"javascript\">alert('".$TransformarSuspectEmProspect[1]."');</script>";
            $New_pfixo = str_replace('sn_suspect@igual1','sn_prospect@igual1',$Url->getParam('pfixo'));
            $Url->AlteraParam('pfixo',$New_pfixo);
            $Url->AlteraParam('pgetcustom','prospect');
            $Url->RemoveParam('ptsep');
            $url_retorno = $Url->getURL();
        }
    }
}
?>