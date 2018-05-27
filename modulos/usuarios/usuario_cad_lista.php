<table border="0" width="100%">
	<tr>
		<td bgcolor="#EEF2FB" colspan="15"><b>
		<font face="Verdana" size="1">Cadastro de Usuários</font></b></td>
   	</tr>
	<tr>
		<td width="50"><font face="Verdana" size="1">Busca :
		</font></td>
		<td width="94">';
			<select size="1" name="D1">
				<option selected value="id_usuario">Id.Usuário</option>
				<option value="nome_usuario">Nome do Usuário</option>
			</select>
		</td>
		<td width="159"><input type="text" name="edtbusca" size="20"></td>
		<td width="58">
			<input type="button" value="Filtrar" name="btnfiltrar"></td>
		<td width="395">
			<input type="button" value="+ Incluir" name="btnincluir">
		</td>
		<td width="30">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="6" height="100%">
		<table border="0" width="100%">
			<tr>
				<td bgcolor="#EEF2FB"><b>
				<font face="Verdana" size="1">Id.Usuário</font></b></td>
				<td bgcolor="#EEF2FB"><b>
				<font face="Verdana" size="1">Nome do Usuário</font></b></td>
				<td bgcolor="#EEF2FB"><b>
				<font face="Verdana" size="1">e-mail</font></b></td>
				<td bgcolor="#EEF2FB"><b>
				<font face="Verdana" size="1">Perfil</font></b></td>
				<td bgcolor="#EEF2FB"><b>
				<font face="Verdana" size="1">Dt.Cadastro</font></b></td>
				<td bgcolor="#EEF2FB"><b>
				<font face="Verdana" size="1">Excluir</font></b></td>
			</tr>
 	    <?php 
		require_once( '../../conecta.php' );
		$sql_cadastro = query( 'select * from is_usuarios' );

		while ($qry_cadastro = farray($sql_cadastro )) { ?>
			<tr>
			<td><font face="Verdana" size="1"><?php echo $qry_cadastro['id_usuario'] ;?></font></td>
			<td><font face="Verdana" size="1"><?php echo $qry_cadastro['nome_usuario'] ;?></font></td>
			<td><font face="Verdana" size="1"><?php echo $qry_cadastro['email'] ;?></font></td>
			<td><font face="Verdana" size="1"><?php echo $qry_cadastro['id_perfil'] ;?></font></td>
			<td><font face="Verdana" size="1"><?php echo $qry_cadastro['dt_cadastro'] ;?></font></td>
			<td>
			<input type="button" value="Excluir" name="B5"></td>
			</tr>
		<?php } ?>

		</table>
	</td>
</tr>
</table>
?>