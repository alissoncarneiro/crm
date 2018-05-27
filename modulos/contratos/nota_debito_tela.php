<?
@header("Content-Type: text/html;  charset=ISO-8859-1",true);
require_once("../../conecta.php");
require_once("../../funcoes.php");
session_start();
$titulo = "Nota de Débito";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>:: OASIS :: <? $titulo; ?></title>
        <link href="../../estilos_css/estilo.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" type="text/css" href="../../estilos_css/cadastro.css"/>
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
        <script type="text/javascript" src="../../js/function.js"></script>
        <script language="JavaScript" src="../../js/ajax_menus.js"></script>
        <script type="text/javascript" src="../../js/calendario/calendario.js"></script>
        <script type="text/javascript" src="../../js/calendario/calendario-pt.js"></script>
        <script type="text/javascript" src="../../js/calendario/calendario-config.js"></script>
    </head>
    <body topmargin="0" leftmargin="0" rightmargin="0" bottommargin="0">

        <div id="topo_detalhes">
            <div id="logo_empresa"></div>
            <!--logo -->
        </div><!--topo -->

        <form method="POST" name="cad" id="cad" action="nota_debito_post.php" enctype='multipart/form-data'>
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
                echo '<tr><td>&nbsp;</td><td width="18%"><div align="right">Contrato :</div></td><td width="1%">&nbsp;</td><td width="76%"><div align="left">';
                echo '<select name="id_pessoa" id="id_pessoa">';
                $q_contrato = query("SELECT p.id_pessoa, p.fantasia_apelido, concat(month(a.dt_inicio),'/',year(a.dt_inicio)) data FROM is_contrato c, is_pessoa p, is_atividade a, is_ativ_despesa d where a.id_tp_atividade = '4' and a.id_pessoa = p.numreg and d.id_atividade = a.numreg and p.numreg = c.id_pessoa and c.vl_km > 0 group by 1,2,3 order by a.dt_inicio desc, p.fantasia_apelido");
                while($a_contrato = farray($q_contrato)){
                    echo '<option '.$disabled.' value="'.$a_contrato['id_pessoa'].'-'.$a_contrato['data'].'">'.$a_contrato['fantasia_apelido'].' - '.$a_contrato['data'].'</option>';
                }
                echo '</select>';
                echo '</div></td></tr>';
                echo '<tr><td>&nbsp;</td><td colspan="3">&nbsp;</td></tr>';

                echo '<tr><td>&nbsp;</td><td width="18%"><div align="right">Número da Nota :</div></td><td width="1%">&nbsp;</td><td width="76%"><div align="left">';
                echo '<input name="numero" id="numero" value="" size=5>';
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
