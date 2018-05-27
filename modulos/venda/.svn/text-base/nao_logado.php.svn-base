<?php
/*
 * nao_logado.php
 * Autor: Alex
 * 22/11/2010 18:25
 * -
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
        <title>Não Logado</title>
        <link href="../../estilos_css/estilo.css" rel="stylesheet" type="text/css" />
        <link href="../../estilos_css/cadastro.css" rel="stylesheet" type="text/css" />

        <link href="../../css/jquery.autocomplete.css" rel="stylesheet" type="text/css" />
        <link href="../../css/jquery.dlg.css" rel="stylesheet" type="text/css" />
        <link href="../../css/redmond/jquery-ui-1.8.5.custom.css" rel="stylesheet" type="text/css" />

        <link href="estilo_venda.css" rel="stylesheet" type="text/css" />

        <script type="text/javascript" src="../../js/jquery.js"></script>
        <script type="text/javascript" src="../../js/jquery.qtip.js"></script>

        <script type="text/javascript" src="../../js/jquery.dlg.min.js"></script>
        <script type="text/javascript" src="../../js/jquery.easing.js"></script>

        <script type="text/javascript" src="../../js/jquery-ui-1.8.5.custom.min.js"></script>
        <script type="text/javascript" src="../../js/jquery.ui.datepicker-pt-BR.js"></script>

        <script type="text/javascript" src="../../js/jquery.autocomplete.js"></script>

        <script type="text/javascript" src="js/modal_det_pessoa.js"></script>

        <script type="text/javascript" src="js/functions_venda.js"></script>

    </head>

    <body>
        <script>
        $(document).ready(function(){
            $.dlg({
                title: 'Alerta',
                content: 'Você não está logado ou a sessão expirou, por favor faça login novamente.',
                drag: true,
                focusButton :'ok',
                onComplete: function(){
                    window.opener.location.href = window.opener.location.href;
                    window.opener.focus();
                    window.close();
                }
            });
        });
        </script>
    </body>
</html>