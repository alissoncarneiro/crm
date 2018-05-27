<?php
  @header("Content-Type: text/html;  charset=ISO-8859-1",true);
  require_once("../../conecta.php");
  require_once("../../funcoes.php");

  $titulo = "Exportar Ordem de Faturamento";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>:: OASIS :: <? $titulo; ?></title>
<link href="../../estilos_css/estilo.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="../../estilos_css/cadastro.css" />
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
<center>
<div id="principal_detalhes">
   <div id="topo_detalhes">
   <div id="logo_empresa"></div>
   <!--logo -->
   </div><!--topo -->
   <div id="conteudo_detalhes">
    <table width="100%" border="0" cellspacing="2" cellpadding="0">
        <tr>
            <td width="1%"></td>
            <td colspan="3">
                <br />
                <div align="left"><img src="images/seta.gif" width="4" height="7" /><span class="tit_detalhes">Exportar Ordens de Faturamento </span></div>
                <br />
            </td>
        </tr>
        <tr>
            <td height="23" colspan="4">&nbsp;</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td align="right"><span style="font-weight: bold">Qtde. para Exportar</span>: </td>
            <td>&nbsp;</td>
            <td><?php
                    $SqlQtdePedido = "
                                SELECT
                                    COUNT(*) as CNT
                                FROM is_ordem_faturamento t1
                                    INNER JOIN is_pessoa t2 ON t1.id_pessoa = t2.numreg
                                    INNER JOIN is_cfop t7 ON t1.id_cfop = t7.numreg
                                    INNER JOIN is_estabelecimento t8 ON t1.id_estabelecimento = t8.numreg
                                WHERE
                                    t1.sn_exportado_erp = 0";
                    $QryQtdePedido = query($SqlQtdePedido);
                    $ArQtdePedido = farray($QryQtdePedido);

                    echo $ArQtdePedido['CNT'];
                ?>
            </td>
        </tr>

        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td width="24%">&nbsp;</td>
            <td width="2%">&nbsp;</td>
            <td width="73%">
                <div align="left">
                    <input name="Submit" type="button" class="botao_form" value="Confirmar" onClick="javascript:GeraArquivoTXTOrdemFaturamento();" />
                </div>
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
    </table>
</div>
</body>
</html>