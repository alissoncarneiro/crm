<?
  @header("Content-Type: text/html;  charset=ISO-8859-1",true);
  require_once("../../conecta.php");
  require_once("../../funcoes.php");
  
  $titulo = "Forma��o de Pre�o";
  
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
   <form method="POST" name="cad" id="cad" action="gerar_preco_post.php" enctype='multipart/form-data'>

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
               echo '<option value="N">N�o</option>';
			   echo '</select>';
			   echo '</div></td></tr>';
               echo '<tr><td>&nbsp;</td><td colspan="3">&nbsp;</td></tr>';
          
               echo '<tr><td>&nbsp;</td><td width="18%"><div align="right">Usar campo de custo :</div></td><td width="1%">&nbsp;</td><td width="76%"><div align="left">';
          	   echo '<select name="edtcusto" id="edtcusto">';
               echo '<option value="custo_repos" selected>Custo de Reposi��o</option>';
               echo '<option value="custo_ult_ent">Custo da �ltima Entrada</option>';
               echo '<option value="custo_base">Custo Base</option>';
			   echo '</select>';
			   echo '</div></td></tr>';
               echo '<tr><td>&nbsp;</td><td colspan="3">&nbsp;</td></tr>';

               // Formula
               echo '<tr><td>&nbsp;</td><td width="18%"><div align="right">F�rmula :</div></td><td width="1%">&nbsp;</td><td width="76%"><div align="left">';
           	   echo '<select name="edtformula" id="edtformula">';
               $filtro_lupa = "select * from  is_preco_formula order by nome_formula";
               $sql_lupa = query($filtro_lupa);
               while ($qry_lupa = farray($sql_lupa)) {
                    echo '<option value="'.$qry_lupa["id_formula"].'">'.$qry_lupa["nome_formula"].'</option>';
               }
               echo '</select>';
               echo '</div></td></tr>';
               echo '<tr><td>&nbsp;</td><td colspan="3">&nbsp;</td></tr>';

               // Alimentar Tabela de Pre�o
               echo '<tr><td>&nbsp;</td><td width="18%"><div align="right">Atualizar a Tabela de Pre�o :</div></td><td width="1%">&nbsp;</td><td width="76%"><div align="left">';
           	   echo '<select name="edttab" id="edttab">';
               $filtro_lupa = "select * from  is_tab_preco where sn_ativa = 'S' order by nome_tab_preco";
               $sql_lupa = query($filtro_lupa);
               while ($qry_lupa = farray($sql_lupa)) {
                    echo '<option value="'.$qry_lupa["id_tab_preco"].'">'.$qry_lupa["nome_tab_preco"].'</option>';
               }
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

