<?php

require_once("../../conecta.php");

?>

<form>
<div id="div_rec_js"></div>
<div id="div_relatorio_pedidos">
<div id="conteudo_detalhes">
   <table width="100%" border="0" cellspacing="2" cellpadding="0">
       <tr>
         <td width="1%"></td>
         <td colspan="3"><br>
         	<div align="left"><img src="images/seta.gif" width="4" height="7" /><span class="tit_detalhes">Bloqueios de Funções  </span></div>
         	<br></td>
	</tr>
	<tr>
		<td colspan="4">
		<input type="hidden" name="pfuncao" value="relac_cad_lista">
		<input type="hidden" name="pnumreg" value="13">
		<input type="hidden" name="popc" value="alterar"></td>
	</tr>
    <tr>
    	<td>&nbsp;</td>
    	<td width="24%">&nbsp;</td>
    	<td colspan="2">&nbsp;</td>
    </tr>
	<tr>
	  <td>&nbsp;</td>
	  <td><div align="right">Perfil:</div></td>
	  <td width="2%">&nbsp;</td>
	  <td width="73%"><div align="left">
	    <select name="edtid_perfil" id="selectBloqueioPerfil">
		<?php
		$qry_perfil = query("SELECT * FROM is_perfil");
		while($ar_perfil = farray($qry_perfil)){
			echo '<option value="'.$ar_perfil['id_perfil'].'">'.$ar_perfil['nome_perfil'].'</option>';
		}
		?>
		</select>
	    <select name="edtsistema" id="edtsistema">
	      <option value="CRM" selected="selected">CRM</option>
            </select>
            <input type="button" onclick="javascript:bloqueios_custom_p1();" value="Exibir" class="botao_form" />
	  </div></td>
	  </tr>
    </table>
<div id="div_cont_bloqueio" align="center"> 
</div>
</div>
   </form>