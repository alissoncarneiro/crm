<?php
/*
 * detalhe_ini_todas_contas.php
 * Autor: Alex
 * 29/10/2012 10:06:59
 */
if($_GET['pfuncao'] == 'todas_contas'){
    if($_GET['pnumreg'] == '-1'){
        echo '<h2>Não permitido!</h2>';
        exit;
    }
    require_once('conecta.php');
    $SqlPessoa = "SELECT numreg,sn_cliente,sn_prospect,sn_suspect FROM is_pessoa WHERE numreg = '".$_GET['pnumreg']."'";
    $QryPessoa = query($SqlPessoa);
    $ArPessoa = farray($QryPessoa);
    CarregaClasse('Url','classes/class.Url.php');
    if($ArPessoa['sn_cliente'] == '1'){
        $Url = new Url();
        $Url->setUrl(curPageURL());
        $Url->AlteraParam('pfuncao','pessoa');
        $Url->AlteraParam('pgetcustom','Clientes');
        $Url->AlteraParam('pread','0');
        header("Location:".$Url->getUrl());
        exit;
    }
    elseif($ArPessoa['sn_prospect'] == '1'){
        $Url = new Url();
        $Url->setUrl(curPageURL());
        $Url->AlteraParam('pfuncao','pessoa');
        $Url->AlteraParam('pgetcustom','Prospects');
        $Url->AlteraParam('pread','0');
        header("Location:".$Url->getUrl());
        exit;
    }
    elseif($ArPessoa['sn_suspect'] == '1'){
        $Url = new Url();
        $Url->setUrl(curPageURL());
        $Url->AlteraParam('pfuncao','pessoa');
        $Url->AlteraParam('pgetcustom','Suspects');
        $Url->AlteraParam('pread','0');
        header("Location:".$Url->getUrl());
        exit;
    }
}
?>