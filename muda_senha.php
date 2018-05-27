<?php
	@header( 'Content-Type: text/html;  charset=ISO-8859-1', true );
	require_once( 'conecta.php' );
?>
	<div id="conteudo_detalhes">
    	<table width="100%" border="0" cellspacing="0" cellpadding="0">
       		<tr>
         		<td width="5%"></td>
         		<td colspan="3"><br>
         			<div align="left">
         				<img src="images/seta.gif" width="4" height="7" />
						<span class="tit_detalhes">Alterar Senha de Acesso</span>
					</div>
					<br>
				</td>
         	</tr>
		<tr>
			<td width="99%" colspan="3">
				<input type="hidden" name="pfuncao" value="relac_cad_lista">
				<input type="hidden" name="pnumreg" value="13">
				<input type="hidden" name="popc" value="alterar">
        	</td>
		</tr>
	    <tr>
	    	<td>&nbsp;</td>
	    	<td width="18%">
    			<div align="right">Senha Atual :</div>
    		</td>
    		<td width="1%">&nbsp;</td>
    		<td width="76%">
    			<div align="left">
    				<input type="password" name="edtsenha" id="edtsenha" size="15">
    			</div>
    		</td>
    	</tr>
        <tr>
        	<td>&nbsp;</td>
        	<td width="18%">
    			<div align="right">Nova Senha :</div>
    		</td>
    		<td width="1%">&nbsp;</td>
    		<td width="76%">
    			<div align="left">
    				<input type="password" name="edtsenhanova" id="edtsenhanova" size="15">
    			</div>
    		</td>
    	</tr>
        <tr>
        	<td>&nbsp;</td>
        	<td width="18%">
    			<div align="right">Confirme a Nova Senha :</div>
    		</td>
    		<td width="1%">&nbsp;</td>
    			<td width="76%">
    				<div align="left">
    					<input type="password" name="edtsenhaconf" id="edtsenhaconf" size="15">
    				</div>
    			</td>
    		</tr>
		    <tr>
		    	<td>&nbsp;</td>
		    	<td colspan="3">&nbsp;</td>
		    </tr>
	       <tr>
    	     <td>&nbsp;</td>
        	 <td>&nbsp;</td>
         	<td>&nbsp;</td>
         	<td>
         		<div align="left">
           			<input name="Submit" type="button" class="botao_form" value="Confirmar" onclick="javascript:muda_senha_post();" />
         		</div>
         	</td>
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
