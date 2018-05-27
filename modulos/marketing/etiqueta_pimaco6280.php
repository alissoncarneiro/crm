<?
  @header("Content-Type: text/html;  charset=ISO-8859-1",true);
  require_once("../../conecta.php");
?>

   <div id="conteudo_detalhes">

     <table width="100%" border="0" cellspacing="0" cellpadding="0">
       <tr>
         <td width="5%"></td>
         <td colspan="3"><br><div align="left"><img src="images/seta.gif" width="4" height="7" /><span class="tit_detalhes">Ferramentas : Gerar Etiquetas no modelo PIMACO 6280 ( 10 linhas x 3 colunas )</span></div><br></td>

        </tr>
	<tr>
		<td width="99%" colspan="3">
		<input type="hidden" name="pfuncao" value="relac_cad_lista">
		<input type="hidden" name="pnumreg" value="13">
		<input type="hidden" name="popc" value="alterar">
        </td>
	</tr>
    <tr><td>&nbsp;</td><td width="18%"><div align="right">Lista de Pessoas :</div></td><td width="1%">&nbsp;</td><td width="76%"><div align="left">
    <?
          	   echo '<select name="edtid_lista" id="edtid_lista">';
               echo '<option value=""></option>';

               $filtro_lupa = "select * from is_lista order by nome_lista";
               $sql_lupa = query($filtro_lupa);
               while ($qry_lupa = farray($sql_lupa)) {
                  echo '<option value="'.$qry_lupa["numreg"].'">'.$qry_lupa["nome_lista"].'</option>';
               }
               echo '</select>';

    ?>

    </div></td></tr>
   <tr><td>&nbsp;</td><td width="18%"><div align="right">Pular etiquetas utilizadas na folha inicial :</div></td><td width="1%">&nbsp;</td><td width="76%"><div align="left">
 	<input type="text" name="edtpular" id="edtpular" value="0"/>
    </div></td></tr>
    <tr><td>&nbsp;</td><td colspan="3">&nbsp;</td></tr>

       <tr>
         <td>&nbsp;</td>
         <td>&nbsp;</td>
         <td>&nbsp;</td>
         <td><div align="left">
           <input name="teste" type="button" class="botao_form" value="Confirmar" onclick="javascript:window.open('modulos/marketing/etiqueta_pimaco6280_post.php?id_lista=' + document.getElementById('edtid_lista').value + '&pular=' + document.getElementById('edtpular').value);" />
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



