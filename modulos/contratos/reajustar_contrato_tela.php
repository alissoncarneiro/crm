<?
@header("Content-Type: text/html;  charset=ISO-8859-1", true);
require_once("../../conecta.php");
require_once("../../funcoes.php");

$titulo = "Reajuste de Contratos";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>:: OASIS :: <? $titulo; ?></title>
        <link href="../../estilos_css/estilo.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" type="text/css" href="../../estilos_css/cadastro.css">
            <link rel="stylesheet" type="text/css" media="all" href="../../estilos_css/calendar-blue.css" title="win2k-cold-1" />
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
            <link href="../../css/redmond/jquery-ui-1.8.5.custom.css" rel="stylesheet" type="text/css" />
            <script type="text/javascript" src="../../js/function.js"></script>
            <script type="text/javascript" src="../../js/jquery.js"></script>
            <script type="text/javascript" src="../../js/jquery-ui-1.8.5.custom.min.js"></script>
            <script type="text/javascript" src="../../js/jquery.ui.datepicker-pt-BR.js"></script>
            <script language="JavaScript">
                $(document).ready(function(){
                    $.datepicker.setDefaults($.datepicker.regional['pt-BR']);
                });
            </script>
    </head>
    <body topmargin="0" leftmargin="0" rightmargin="0" bottommargin="0">
        <center>
            <div id="principal_detalhes">
                <div id="topo_detalhes">
                    <div id="logo_empresa"></div>
                    <!--logo -->
                </div><!--topo -->
                <div id="conteudo_detalhes">
                    <form method="POST" name="cad" id="cad" action="reajustar_contrato_post.php" enctype='multipart/form-data'>

                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="5%"></td>
                                <td colspan="3"><br><div align="left"><img src="images/seta.gif" width="4" height="7" /><span class="tit_detalhes"><? echo $titulo; ?></span></div><br></td>

                                            </tr>
                                            <tr>
                                                <td width="99%" colspan="3">
                                                </td>
                                            </tr>
                                            <?
                                            echo '<tr><td>&nbsp;</td><td width="18%"><div align="right">Processamento Simulado ? :</div></td><td width="1%">&nbsp;</td><td width="76%"><div align="left">';

                                            echo '<select name="edtsimulado" id="edtsimulado">';
                                            echo '<option value="S" selected>Sim</option>';
                                            echo '<option value="N">Não</option>';
                                            echo '</select>';
                                            echo '</div></td></tr>';

                                            $vl_campo_trat_ini = DataGetBD(soma_meses_data(-1, date("Y-m-d")));
                                            $vl_campo_trat_fim = date("d/m/Y");
                                            echo '<tr><td>&nbsp;</td><td width="18%"><div align="right">Reajustar contratos com data de reajuste programada no período de :</div></td><td width="1%">&nbsp;</td><td width="76%"><div align="left">';
                                            echo '<input maxlength=10 type="text" name="edtde" id="edtde"  size="9"  value=""> ';
                                            echo '<script language="JavaScript">$(document).ready(function(){ $("#edtde") . datepicker({showOn: "button",buttonImage: "../../images/agenda.gif",buttonImageOnly: true,changeMonth:true, changeYear:true});$("#edtde") . datepicker("option", "dateFormat", "dd/mm/yy"); $("#edtde").val(' . "'" . $vl_campo_trat_ini . "'" . ');}); </script>';
                                            echo '&nbsp;&nbsp;até&nbsp;&nbsp;';
                                            echo '<input maxlength=10 type="text" name="edtate" id="edtate"  size="9"  value=""> ';
                                            echo '<script language="JavaScript">$(document).ready(function(){ $("#edtate") . datepicker({showOn: "button",buttonImage: "../../images/agenda.gif",buttonImageOnly: true,changeMonth:true, changeYear:true});$("#edtate") . datepicker("option", "dateFormat", "dd/mm/yy"); $("#edtate").val(' . "'" . $vl_campo_trat_fim . "'" . ');}); </script>';
                                            echo '</div></td></tr>';


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

