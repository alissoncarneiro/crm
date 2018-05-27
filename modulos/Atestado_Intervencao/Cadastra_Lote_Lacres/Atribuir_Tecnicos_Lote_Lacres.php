<?php
@header("Content-Type: text/html;  charset=ISO-8859-1",true);

#Dependencias
require_once("../../../conecta.php");
require_once("../../../funcoes.php");
$titulo = "Atribuir Lacres à Tecnicos Interventores";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>:: OASIS :: <? $titulo; ?></title>
        <link href="../../../estilos_css/estilo.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" type="text/css" href="../../../estilos_css/cadastro.css" />
        <link rel="stylesheet" type="text/css" media="all" href="../../../estilos_css/calendar-blue.css" title="win2k-cold-1" />
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
                <script type="text/javascript" src="../../../js/function.js"></script>
                <script language="JavaScript" src="../../../js/ajax_menus.js"></script>
                <script type="text/javascript" src="../../../js/calendario/calendario.js"></script>
                <script type="text/javascript" src="../../../js/calendario/calendario-pt.js"></script>
                <script type="text/javascript" src="../../../js/calendario/calendario-config.js"></script>
                </head>

                <body topmargin="0" leftmargin="0" rightmargin="0" bottommargin="0">
                    <center>
                        <div id="principal_detalhes">
                            <div id="topo_detalhes">
                                <div id="logo_empresa"></div>
                                <!--logo -->
                            </div><!--topo -->
                            <div id="conteudo_detalhes">
                                <form method="POST" name="cad" id="cad" action="Atribuir_Tecnicos_Lote_Lacres_Post.php" enctype='multipart/form-data'>
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td width="5%"></td>
                                            <td colspan="3">
                                                <br/>
                                                    <div align="left">
                                                        <img src="../../../images/seta.gif" width="4" height="7" />
                                                        <span class="tit_detalhes"><? echo $titulo; ?></span>
                                                    </div>
                                                    <br/>
                                                        </td>
                                                        </tr>
                                                        <tr>
                                                            <td width="99%" colspan="3"></td>
                                                        </tr>
                                                        <?php
                                                        // Resgata os Tecnicos
                                                        $qr_tecnicos = mysql_query("select * from is_usuario order by nome_usuario");
                                                        $Qtd_Tecnicos = mysql_num_rows($qr_tecnicos);

                                                        // Resgata os Lacres
                                                        $qr_lacres = mysql_query("select * from is_lacres where n_atestado is NULL or n_atestado = '' order by n_lacre") or die(mysql_error());
                                                        $Qtd_Lacres_1 = mysql_num_rows($qr_lacres);

                                                        $qr_lacres2 = mysql_query("select * from is_lacres where n_atestado is NULL or n_atestado = '' order by n_lacre") or die(mysql_error());
                                                        $Qtd_Lacres_2 = mysql_num_rows($qr_lacres2);

                                                        /*

                                                          Verifica se existe mais de 1 lacre disponível para alteração, sendo que, os Lacres que estão em uso por um
                                                          Atestado não poderão ser atribuídos à um Técnico, ou seja: serão carregados apenas os Lacres que não estão
                                                          atrelados à nenhum Atestado

                                                         */
                                                        if($Qtd_Tecnicos == null){
                                                            echo '     <tr>
                                                                         <td>&nbsp;</td>
                                                                         <td>
                                                                             <div align="left">
                                                                                 Não há Técnicos cadastrados no Sistema, sendo assim, não é possivel efetuar o processo de
                                                                                 Atribuição de Lacres em Lote.
                                                                             </div>
                                                                         </dt>
                                                                     </tr>';
                                                            exit;
                                                        }
                                                        if($Qtd_Lacres_1 == null AND $Qtd_Lacres_2 == null){
                                                            echo ' <tr>
                                                                     <td>&nbsp;</td>
                                                                     <td>
                                                                         <div align="left">
                                                                             Não há Lacres cadastrados no Sistema, sendo assim, não é possivel efetuar o processo de
                                                                             Atribuição de Lacres em Lote.
                                                                         </div>
                                                                     </dt>
                                                                 </tr>';
                                                            exit;
                                                        }

                                                        if($Qtd_Lacres_1 == 1){

                                                            if($Qtd_Lacres_2 == 1){

                                                                echo '<tr>
                                                                         <td>&nbsp;</td>
                                                                         <td>
                                                                             <div align="left">
                                                                                 Existe apenas <b>'.$Qtd_Lacres_1.'</b> Lacre para ser atribuido à algum Técnico, sendo assim,
                                                                                 não é possivel efetuar o processo de Atribuição de Lacres em Lote. Esse processo pode ser
                                                                                 efetuado manualmente, selecionando este Lacre e adicionando um Técnico à ele.
                                                                             </div>
                                                                         </dt>
                                                                     </tr>';
                                                                exit;
                                                            }
                                                        }

                                                        echo '<tr>

                                     <td>&nbsp;</td>
                                     <td width="18%">
                                         <div align="right">
                                             Técnico:
                                         </div>
                                     </td>
                                     <td width="1%">&nbsp;</td>
                                     <td width="76%">
                                         <div align="left">
                                             <select name="tecnicos">';

                                                        while($ar_tecnicos = mysql_fetch_array($qr_tecnicos)){
                                                            echo '<option value="'.$ar_tecnicos['id_usuario'].'">'.$ar_tecnicos['nome_usuario'].'</option>';
                                                        }

                                                        echo '</select>
                                         </div>
                                     </td>
                                 </tr>
                                 <tr>
                                     <td>&nbsp;</td>
                                     <td colspan="3">&nbsp;</td>
                                 </tr>
                                 <tr>
                                     <td>&nbsp;</td>
                                     <td width="18%">
                                         <div align="right">
                                             Nº de Inicio do Lote:
                                         </div>
                                     </td>
                                     <td width="1%">&nbsp;</td>
                                     <td width="76%">
                                         <div align="left">
                                             <select name="lote_inicio">';

                                                        while($ar_lacres = mysql_fetch_array($qr_lacres)){
                                                            echo '<option value="'.$ar_lacres['n_lacre'].'">'.$ar_lacres['n_lacre'].'</option>';
                                                        }

                                                        echo '</select>
                                         </div>
                                     </td>
                                 </tr>
                                 <tr>
                                     <td>&nbsp;</td>
                                     <td colspan="3">&nbsp;</td>
                                 </tr>
                                 <tr>
                                     <td>&nbsp;</td>
                                     <td width="18%">
                                         <div align="right">
                                             Nº Final do Lote:
                                         </div>
                                     </td>
                                     <td width="1%">&nbsp;</td>
                                     <td width="76%">
                                         <div align="left">
                                             <select name="lote_final">';
                                                        while($ar_lacres2 = mysql_fetch_array($qr_lacres2)){
                                                            echo '<option value="'.$ar_lacres2['n_lacre'].'">'.$ar_lacres2['n_lacre'].'</option>';
                                                        }

                                                        echo '</select>
                                         </div>
                                     </td>
                                 </tr>
                                 <tr>
                                     <td>&nbsp;</td>
                                     <td colspan="3">&nbsp;</td>
                                 </tr>';
                                ?>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>
                        <div align="left">
                            <input name="Submit" type="submit" class="botao_form" value="Confirmar" />
                        </div>
                    </td>
                </tr>
                </table>
                </form>
                </body>
                </html>