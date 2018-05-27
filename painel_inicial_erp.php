	<?php

	@session_start(  );
	@header( 'Content-Type: text/html;  charset=ISO-8859-1', true );
	$cur_dir = dirname( $_SERVER[PHP_SELF] );
	ini_set( 'include_path', get_include_path(  ) . ';../../;' . $cur_dir );
	?>
  	<table width="890" border="0" cellpadding="2" cellspacing="2" align="left" 	>
	  	<tr>
		    <td valign="top" align="left"  width="890" style="width:820px; height:500px;overflow:auto;">
			  <div name="div_atividades" id="div_atividades" style="width:820px; height:500px; overflow:auto;">  
				<?php 
				$_GET = array(  );
				$_GET['pfuncao'] = 'atividades_cad_lista';
				$_GET['ppainel'] = 'S';
				$_GET['ppainel_div'] = 'div_atividades';
				$_GET['pfixo'] = 'id_usuario_resp@igual@s@vs_id_usuario@s';
				include( 'gera_cad_lista.php' );
				?>
		  	</frame>  	
			</td>
		</tr>
	</table>  
