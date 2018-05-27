<?php

    //phpinfo();die;

    @session_start( );
    header('Content-Type: text/html; charset=utf-8');


    require_once( "conecta.php" );

    $a_versao = farray( query( "select versao from is_sistema_atualizacao order by numero_release desc" ) );
    if ( $a_versao['versao'] )    {
        $_SESSION['versao'] = $a_versao['versao'];
    }

    $count_modulos = farray( query( "select count(*) as total from is_modulos where id_sistema like '%".$_SESSION['id_sistema']."%'" ) );
    require_once 'header.php';

?>

    <script> n_divs='<?php echo $count_modulos['total']?>';</script>
    <div name="divConteudo" id="divConteudo">
        <div id="login">
            <table width="100%" class="loginCampos" border="0" cellspacing="0" cellpadding="0">
                <tr class="loginCamposLinha">
                        <td class="loginCamposColuna">&nbsp;</td>
                </tr>
                <tr>
                  <td>
                    <input name="edtusuario" id="edtusuario" type="text" class="campos_form" value="">
                  </td>
                </tr>
                <tr>
                  <td>
                      <input name="edtsenha" id="edtsenha" type="password" class="campos_form" value="" onKeyPress="javascript: if (event.keyCode == 13) {   valida_usuario() }">
                  </td>
                </tr>
                <tr>
                      <td><div name="divLogin" id="divLogin"></div>&nbsp;</td>
                </tr>
                <input type="button" class="loginCamposBtn botao_form" value=" " onClick="javascript:valida_usuario()"/>
            </table>
        </div>
        <img src="images/background.jpg" id="fullScreenLogin">
    </div>
    <input name="edtfuncaoini" id="edtfuncaoini" type="hidden" value="" />
    <input name="edtnumregini" id="edtnumregini" type="hidden" value="" />
</body>
</html>