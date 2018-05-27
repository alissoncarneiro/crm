<?
@header("Content-Type: text/html;  charset=ISO-8859-1",true);
require_once("../../conecta.php");
require_once("../../funcoes.php");
session_start();
$titulo = "Relatório de Horas";

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

        <form method="POST" name="cad" id="cad" action="rel_horas_post.php" enctype='multipart/form-data'>
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
                    $where = " WHERE id_usuario= '".$_SESSION['id_usuario']."'";
                }
                echo '<tr><td>&nbsp;</td><td width="18%"><div align="right">Responsável :</div></td><td width="1%">&nbsp;</td><td width="76%"><div align="left">';
                echo '<select name="edtid_usuario" id="edtid_usuario">';
                $vendedores = mysql_query("SELECT id_usuario,nome_usuario FROM is_usuarios ".$where." order by nome_usuario");
                while($vend = mysql_fetch_array($vendedores)){
                    echo '<option '.$disabled.' value="'.$vend['id_usuario'].'">'.$vend['nome_usuario'].'</option>';
                }
                echo '</select>';
                echo '</div></td></tr>';
                echo '<tr><td>&nbsp;</td><td colspan="3">&nbsp;</td></tr>';

                               echo '<tr><td>&nbsp;</td><td width="18%"><div align="right">Período ( De ) :</div></td><td width="1%">&nbsp;</td><td width="76%"><div align="left">';

               $vl_campo = date("Y").'-01-01';
               if ($vl_campo) {
                 $vl_campo_trat = substr($vl_campo,8,2).'/'.substr($vl_campo,5,2).'/'.substr($vl_campo,0,4);
               } else {
                 $vl_campo_trat = '';
               }
    		   echo '<input READONLY maxlength=10 type="text" class="inputoff" onfocus="this.className='."'".'inputon'."'".'" onblur="this.className='."'".'inputoff'."'".'" name="edt'.'dtini'.'" id="edt'.'dtini'.'" size="9" value="'.$vl_campo_trat.'"> ';
               echo '<input type=button name="btn'.'dtini'.'" class=loadcalendar onclick="if (gebi('."'cal".'dtini'."'".").style.display=='none') {gebi('cal".'dtini'."').style.display='block'; } else {gebi('cal".'dtini'."').style.display='none';}".'"><div style="width: 210px; zIndex: 0; display: none; position:absolute; " id="cal'.'dtini'.'"></div> ';

               echo '<script type="text/javascript"> ';
               echo 'function dateChanged(calendar) { ';
               echo 'if (calendar.dateClicked) { ';
    		   echo '   var y = calendar.date.getFullYear();';
    		   echo '   var m = calendar.date.getMonth();';     // integer, 0..11
    		   echo '   var d = calendar.date.getDate();';      // integer, 1..31
    		   echo '	  m=m+1;';
    		   echo '	  if (d.toString().length<2) d="0"+d;';
    		   echo '	  if (m.toString().length<2) m="0"+m;';
    		   echo '   data = d + "/" + m + "/" + y;';
    		   echo '   document.getElementById('."'edt".'dtini'."'".').value = data; document.getElementById('."'cal".'dtini'."').style.display='none'; ";
    		   echo ' } ';
    		   echo '}; ';

    		   echo 'Calendar.setup( ';
               echo '{ ';
    		   echo 'flat         : "cal'.'dtini'.'", '; // ID of the parent element
    		   echo ' flatCallback : dateChanged  ';         // our callback function
    		   echo '} ';
    		   echo '); ';
    	       echo '</script> ';

               echo '</div></td></tr>';

               echo '<tr><td>&nbsp;</td><td colspan="3">&nbsp;</td></tr>';

               // Data Fim
               echo '<tr><td>&nbsp;</td><td width="18%"><div align="right">Período ( Até ) :</div></td><td width="1%">&nbsp;</td><td width="76%"><div align="left">';

               $vl_campo = date("Y").'-12-31';
               if ($vl_campo) {
                 $vl_campo_trat = substr($vl_campo,8,2).'/'.substr($vl_campo,5,2).'/'.substr($vl_campo,0,4);
               } else {
                 $vl_campo_trat = '';
               }
    		   echo '<input READONLY maxlength=10 type="text" class="inputoff" onfocus="this.className='."'".'inputon'."'".'" onblur="this.className='."'".'inputoff'."'".'" name="edt'.'dtfim'.'" id="edt'.'dtifim'.'" size="9" value="'.$vl_campo_trat.'"> ';
               echo '<input type=button name="btn'.'dtifim'.'" class=loadcalendar onclick="if (gebi('."'cal".'dtifim'."'".").style.display=='none') {gebi('cal".'dtifim'."').style.display='block'; } else {gebi('cal".'dtifim'."').style.display='none';}".'"><div style="width: 210px; zIndex: 0; display: none; position:absolute; " id="cal'.'dtifim'.'"></div> ';

               echo '<script type="text/javascript"> ';
               echo 'function dateChanged(calendar) { ';
               echo 'if (calendar.dateClicked) { ';
    		   echo '   var y = calendar.date.getFullYear();';
    		   echo '   var m = calendar.date.getMonth();';     // integer, 0..11
    		   echo '   var d = calendar.date.getDate();';      // integer, 1..31
    		   echo '	  m=m+1;';
    		   echo '	  if (d.toString().length<2) d="0"+d;';
    		   echo '	  if (m.toString().length<2) m="0"+m;';
    		   echo '   data = d + "/" + m + "/" + y;';
    		   echo '   document.getElementById('."'edt".'dtifim'."'".').value = data; document.getElementById('."'cal".'dtifim'."').style.display='none'; ";
    		   echo ' } ';
    		   echo '}; ';

    		   echo 'Calendar.setup( ';
               echo '{ ';
    		   echo 'flat         : "cal'.'dtifim'.'", '; // ID of the parent element
    		   echo ' flatCallback : dateChanged  ';         // our callback function
    		   echo '} ';
    		   echo '); ';
    	       echo '</script> ';
               echo '</div></td></tr>';
               echo '<tr><td>&nbsp;</td><td colspan="3">&nbsp;</td></tr>';

                echo '<tr><td>&nbsp;</td><td width="18%"><div align="right">Imprimir Detalhes da Atividade :</div></td><td width="1%">&nbsp;</td><td width="76%"><div align="left">';
                echo '<select name="edtdetalhado" id="edtdetalhado">';
                echo '<option value="N" selected>Não</option>';
                echo '<option value="S" >Sim</option>';
                echo '</select>';
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
