<?php

/*
 * post_before_usuario.php
 * Autor: Alex
 * 06/12/2010 10:23:00
 *
 *
 * Log de Altera��es
 * yyyy-mm-dd <Pessoa respons�vel> <Descri��o das altera��es>
 */

/*
 * Validando o se o Campo de Representante ERP est� repetido
 */
if(($id_funcao == 'usuario_cad_lista')){
    if($opc == 'incluir' || $opc == 'alterar'){
        if($_POST['edtid_representante'] != ''){
            $SqlVerifica = "SELECT numreg FROM is_usuario WHERE id_representante != '' AND id_representante = '".$_POST['edtid_representante']."'";
            $QryVerifica = query($SqlVerifica);
            $NumrowsVerifica = numrows($QryVerifica);
            if($NumrowsVerifica > 1){
                echo alert("C�d de Representante j� associado a outro usu�rio!. Registro n�o foi salvo");
                $geraCadPost->DoJsPostBack($Url);
                exit;
            }elseif($NumrowsVerifica == 1){
                $ArVerifica = farray($QryVerifica);
                if($ArVerifica['numreg'] != $_POST['pnumreg']){
                    echo alert("C�d de Representante j� associado a outro usu�rio!. Registro n�o foi salvo");
                    $geraCadPost->DoJsPostBack($Url);
                    exit;
                }
            }
        }
    }
}
/*
 * Validando o se o Login est� repetido
 */
if($id_funcao == 'usuario_cad_lista' && $opc == 'incluir') {
    $_POST['edtid_usuario'] = strtolower(ereg_replace("[^a-zA-Z0-9_.]", "", strtr($_POST['edtid_usuario'], "�������������������������� ", "aaaaeeiooouucAAAAEEIOOOUUC_")));
    $QryVerificaSeExiste = query("SELECT COUNT(*) AS CNT FROM is_usuario WHERE id_usuario = '".$_POST['edtid_usuario']."'");
    $ArVerificaSeExiste = farray($QryVerificaSeExiste);
    if($ArVerificaSeExiste['CNT'] > 0){
        $Url->AlteraParam('ppostback',$numreg_postback);
        $_POST['url_retorno'] = $Url->getUrl();
        echo alert('Login j� existe! Por favor especifique outro.');
        echo windowlocationhref($_POST['url_retorno']);
    }
}
?>