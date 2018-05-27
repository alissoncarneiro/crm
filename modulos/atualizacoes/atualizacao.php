<?php
/*
 * atualizacao.php
 * Autor: Alex
 * 03/01/2011 17:45:00
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Content-Type: text/html; charset=ISO-8859-1");
include('../../conecta.php');
include('../../classes/class.uB.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
        <title>Atualização OASIS</title>
        <link href="../../estilos_css/estilo.css" rel="stylesheet" type="text/css" />
        <link href="../../estilos_css/cadastro.css" rel="stylesheet" type="text/css" />
        <link href="../../css/redmond/jquery-ui-1.8.5.custom.css" rel="stylesheet" type="text/css" />
        <link href="../../css/basic.css" rel="stylesheet" type="text/css" />
        <link href="../../css/enhanced.css" rel="stylesheet" type="text/css" />

        <script type="text/javascript" src="../../js/jquery.js"></script>
        <script type="text/javascript" src="../../js/jquery.qtip.js"></script>
        <script type="text/javascript" src="../../js/jquery-ui-1.8.5.custom.min.js"></script>
        <script type="text/javascript" src="../../js/jquery.fileinput.js"></script>

        <script language="JavaScript">
            $(document).ready(function(){
                $(".botao_jquery").button();
                $('#edtarquivo_atualizacao').customFileInput();

                $("#btn_sair").click(function(){
                    $("#jquery-dialog").attr("title","Alerta");
                    $("#jquery-dialog").html('Deseja sair ?');
                    $("#jquery-dialog").dialog({
                        dialogClass: 'jquery-dialog',
                        position: 'center',
                        resizable: false,
                        buttons:{
                            "Confirmar": function(){
                                window.opener.focus();
                                window.close();
                            },
                            Cancelar: function(){$(this).dialog("close");$("#jquery-dialog").dialog("destroy");}},
                        modal: true,
                        show: "fade",
                        hide: "fade"
                    });
                });
            });
        </script>
    </head>
    <body>
        <div id="jquery-dialog" style="display: none;"></div>
        <form method="post" action="atualizacao_post.php" enctype="multipart/form-data">
        <fieldset><legend style="font-weight:bold;font-size:14px;">Atualização OASIS</legend>
            <input type="file" name="edtarquivo_atualizacao" id="edtarquivo_atualizacao" />
            <br/>
            <input type="submit" id="submit_atualizacao" value="Atualizar" class="botao_jquery" />
            <input type="button" id="btn_sair" value="Fechar" class="botao_jquery" />
        </fieldset>
        </form>
        <fieldset><legend style="font-weight:bold;font-size:14px;">Atualizações Instaladas</legend>
            <?php
            $QryAtualizações = query("SELECT * FROM is_sistema_atualizacao ORDER BY numero_release ASC");
            while($ArAtualizações = farray($QryAtualizações)){
                echo '<strong>Versão: </strong>'.$ArAtualizações['versao'].'<br/><strong>Data: </strong>'.uB::DataEn2Br($ArAtualizações['dt_release'], true).'<br/><strong>Data da Atualização: </strong>'.uB::DataEn2Br($ArAtualizações['dt_atualizacao'],true).'<hr/>';
            }
            ?>
        </fieldset>
    </body>
</html>