<?
@header("Content-Type: text/html;  charset=ISO-8859-1", true);
require_once("../../conecta.php");
require_once("../../funcoes.php");
require_once("../../functions.php");
session_start();
$titulo = "Relatório de Comissão por Representante e Período";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>:: OASIS :: <? echo $titulo; ?></title>
        <link href="../../estilos_css/estilo.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" type="text/css" href="../../estilos_css/cadastro.css"/>
        <link href="../../css/redmond/jquery-ui-1.8.5.custom.css" rel="stylesheet" type="text/css" />
        <style type="text/css">
            <!--
            body {
                margin-left: 0px;
                margin-top: 0px;
                margin-right: 0px;
                margin-bottom: 0px;
            }
            -->
        </style>
        <script type="text/javascript" src="../../js/function.js"></script>
        <script language="JavaScript" src="../../js/ajax_menus.js"></script>
        <script type="text/javascript" src="../../js/jquery.js"></script>
        <script type="text/javascript" src="../../js/jquery-ui-1.8.5.custom.min.js"></script>
        <script type="text/javascript" src="../../js/jquery.ui.datepicker-pt-BR.js"></script>
        <script language="JavaScript">
            $(document).ready(function(){
                $.datepicker.setDefaults($.datepicker.regional['pt-BR']);
            });
            // When the document loads do everything inside here ...
            $(document).ready(function(){
                var expanded = false;
                $(".content").hide();
                $(".content:first").show();
                $("a.tab").dblclick(function () {
                    $(".active").removeClass("active");
                    $(this).addClass("active");
                    var content_show = $(this).attr("href");
                    $("#"+content_show).fadeIn();
                    return false;
                });

                // When a link is clicked
                $("a.tab").click(function () {
                    if($(this).attr("href") != 'expand_all'){
                        // switch all tabs off
                        $(".active").removeClass("active");
                        // switch this tab on
                        $(this).addClass("active");
                        // slide all content up
                        $(".content").hide();
                        // slide this content up
                        var content_show = $(this).attr("href");
                        $("#"+content_show).fadeIn();
                        expanded = false;
                    }
                    else{
                        if(expanded == false){
                            expanded = true;
                            $(".content").fadeIn();
                        }
                        else{
                            expanded = false;
                            $(".content").fadeOut(function(){$(".content:first").show(); $(".active").removeClass("active"); $(".tabs a:first").addClass("active");});
                        }

                    }
                    return false;
                });
                });
            
        </script>

    </head>
    <body topmargin="0" leftmargin="0" rightmargin="0" bottommargin="0">

        <div id="topo_detalhes">
            <div id="logo_empresa"></div>
            <!--logo -->
        </div><!--topo -->

        <form method="POST" name="cad" id="cad" action="comissao_repres_post.php" enctype='multipart/form-data'>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td width="5%"></td>
                    <td colspan="3"><br/><div align="left"><img src="images/seta.gif" width="4" height="7" /><span class="tit_detalhes"><? echo $titulo; ?></span></div><br/></td>
                </tr>
                <tr>
                    <td width="99%" colspan="3">
                    </td>
                </tr>
                <?
//print_r($_SESSION);
                if ($_SESSION['sn_bloquear_leitura'] == 'S') {
                    //$disabled = " disabled ";
                    $where = " WHERE numreg= '" . $_SESSION['id_usuario'] . "'";
                }
                echo '<tr><td>&nbsp;</td><td width="18%"><div align="right">Representante :</div></td><td width="1%">&nbsp;</td><td width="76%"><div align="left">';
                echo '<select name="edtcod_rep" id="edtcod_rep">';
                $vendedores = query("SELECT numreg,nome_usuario FROM is_usuario " . $where . " order by nome_usuario");
                while ($vend = farray($vendedores)) {
                    echo '<option ' . $disabled . ' value="' . $vend['numreg'] . '">' . $vend['nome_usuario'] . '</option>';
                }
                echo '</select>';
                echo '</div></td></tr>';
                echo '<tr><td>&nbsp;</td><td colspan="3">&nbsp;</td></tr>';

                echo '<tr><td>&nbsp;</td><td width="18%"><div align="right">Período :</div></td><td width="1%">&nbsp;</td><td width="76%"><div align="left">';
                $id_campo = 'dtini';
                $vl_campo_trat = date("d/m/Y");
                echo '<input maxlength=10 type="text" name="edt' . $id_campo . '" id="edt' . $id_campo . '" ' . $evento_change . ' size="9" ' . $readonly . ' value=""> ';
                echo '<script language="JavaScript">$(document).ready(function(){ $("#edt' . $id_campo . '") . datepicker({showOn: "button",buttonImage: "../../images/agenda.gif",buttonImageOnly: true,changeMonth:true, changeYear:true});$("#edt' . $id_campo . '") . datepicker("option", "dateFormat", "dd/mm/yy"); $("#edt' . $id_campo . '").val(' . "'" . $vl_campo_trat . "'" . ');}); </script>';
                $id_campo = 'dtfim';
                echo "&nbsp;até&nbsp;";
                echo '<input maxlength=10 type="text" name="edt' . $id_campo . '" id="edt' . $id_campo . '" ' . $evento_change . ' size="9" ' . $readonly . ' value=""> ';
                echo '<script language="JavaScript">$(document).ready(function(){ $("#edt' . $id_campo . '") . datepicker({showOn: "button",buttonImage: "../../images/agenda.gif",buttonImageOnly: true,changeMonth:true, changeYear:true});$("#edt' . $id_campo . '") . datepicker("option", "dateFormat", "dd/mm/yy"); $("#edt' . $id_campo . '").val(' . "'" . $vl_campo_trat . "'" . ');}); </script>';

                echo '</td></tr>';
                echo '<tr><td>&nbsp;</td><td colspan="3">&nbsp;</td></tr>';

                echo '<tr><td>&nbsp;</td><td width="18%"><div align="right">Formato :</div></td><td width="1%">&nbsp;</td><td width="76%"><div align="left">';
                echo '<select name="edtformato" id="edtformato">';
                echo '<option value="html">HTML</option>';
                echo '<option value="excel">EXCEL</option>';
                echo '</select>';
                echo '</td></tr>';
                echo '<tr><td>&nbsp;</td><td colspan="3">&nbsp;</td></tr>';
                ?>
                <tr><td>&nbsp;</td><td colspan="3">&nbsp;</td></tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td><div align="left">
                            <input name="Submit" type="submit" class="botao_form" value="Confirmar" />
                        </div></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td colspan="3">&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td colspan="3">&nbsp;</td>
                </tr>
            </table>
        </form>
    </body>
</html>