<?php

/*
 * post_before_perfil.php
 * Autor: Alex
 * 11/03/2011 12:08:00
 *
 * Log de Altera��es
 * yyyy-mm-dd <Pessoa respons�vel> <Descri��o das altera��es>
 */
if($id_funcao == 'perfil_cad_lista' && $opc == 'incluir') {
    $_POST['edtid_perfil'] = strtolower(ereg_replace("[^a-zA-Z0-9_.]", "", strtr($_POST['edtid_perfil'], "�������������������������� ", "aaaaeeiooouucAAAAEEIOOOUUC_")));
    $QryVerificaSeExiste = query("SELECT COUNT(*) AS CNT FROM is_perfil WHERE id_perfil = '".$_POST['edtid_perfil']."'");
    $ArVerificaSeExiste = farray($QryVerificaSeExiste);
    if($ArVerificaSeExiste['CNT'] > 0){
        $Url->AlteraParam('ppostback',$numreg_postback);
        $_POST['url_retorno'] = $Url->getUrl();
        echo alert('Id. �rea j� existe! Por favor especifique outra.');
        echo windowlocationhref($_POST['url_retorno']);
    }
}
?>