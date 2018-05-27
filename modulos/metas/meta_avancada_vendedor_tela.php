<?
@header("Content-Type: text/html;  charset=ISO-8859-1",true);
require_once("../../conecta.php");
require_once("../../funcoes.php");
require_once("../../functions.php");
session_start();
$titulo = "Relatório de Apuração de Metas de Vendedores Internos";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>:: OASIS :: <? echo $titulo; ?></title>
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

        <form method="POST" name="cad" id="cad" action="meta_avancada_vendedor_post.php" enctype='multipart/form-data'>
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
                if($_SESSION['sn_bloquear_leitura']=='S'){
                    //$disabled = " disabled ";
                    $where = " WHERE numreg= '".$_SESSION['id_usuario']."'";
                }
                echo '<tr><td>&nbsp;</td><td width="18%"><div align="right">Vendedor(a) :</div></td><td width="1%">&nbsp;</td><td width="76%"><div align="left">';
                echo '<select name="edtcod_rep" id="edtcod_rep">';
                $vendedores = query("SELECT numreg,nome_usuario FROM is_usuario ".$where." order by nome_usuario");
                while($vend = farray($vendedores)){
                    echo '<option '.$disabled.' value="'.$vend['numreg'].'">'.$vend['nome_usuario'].'</option>';
                }
                echo '</select>';
                echo '</div></td></tr>';
                echo '<tr><td>&nbsp;</td><td colspan="3">&nbsp;</td></tr>';

                echo '<tr><td>&nbsp;</td><td width="18%"><div align="right">Período :</div></td><td width="1%">&nbsp;</td><td width="76%"><div align="left">';
                echo '<select name="edtperiodo" id="edtperiodo">';
                $q_periodo = query("SELECT distinct dt_inicio, dt_fim FROM is_meta_avancada_vend order by dt_fim desc");
                while($a_periodo = farray($q_periodo)){
                    echo '<option value="'.$a_periodo['dt_inicio'].$a_periodo['dt_fim'].'">'.dten2br($a_periodo['dt_inicio']).' até '.dten2br($a_periodo["dt_fim"]).'</option>';
                }
                echo '</select>';
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