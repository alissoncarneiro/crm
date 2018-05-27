<?php

header("Content-Type:text/html; charset=iso-8859-1;");

?>

<input type="button" value="Confirmar Permiss&otilde;es do Perfil" class="botao_form" style="font-size:18px;color:#FF0000;" onClick="javascript:bloqueios_cad_custom_p2();">

<table width="98%" align="center" border="0" style="border: 1px solid #0066CC;" cellspacing="2" cellpadding="0" >

  <tr style="background-color:#006699;">

    <td width="475">&nbsp;</td>

    <td width="52" align="center"><div align="center"><strong style="color:#FFFFFF;">Ver</strong></div></td>

    <td width="65" align="center"><div align="center"><strong style="color:#FFFFFF;">Editar</strong></div></td>

    <td width="52" align="center"><div align="center"><strong style="color:#FFFFFF;">Incluir</strong></div></td>

    <td width="65" align="center"><div align="center"><strong style="color:#FFFFFF;">Excluir</strong></div></td>

  </tr>

<?php

require('../../conecta.php');

require('../../functions.php');

$qry_cad = query("SELECT * FROM is_gera_cad ORDER BY titulo ASC");

$count_row = 0;

$num_rows_cad = numrows($qry_cad);

while($ar_cad = farray($qry_cad)){

	$checkall_ver_all .= "document.getElementById('ver_".$ar_cad['id_cad']."').checked = this.checked; ";

	$checkall_editar_all .= "document.getElementById('editar_".$ar_cad['id_cad']."').checked = this.checked; ";

	$checkall_incluir_all .= "document.getElementById('incluir_".$ar_cad['id_cad']."').checked = this.checked; ";

	$checkall_excluir_all .= "document.getElementById('excluir_".$ar_cad['id_cad']."').checked = this.checked; ";



	$bgcolor = ($count_row % 2 == 0)?'#CCCCCC':'#FFFFFF';

	if($last_modulo != $ar_cad['a'] && $count_row > 0){

	?>

	<tr bgcolor="<?php echo $bgcolor;?>">

	  <td>&nbsp;</td>

	  <td><input type="checkbox" onclick="javascript:<?=$checkall_ver;?>" checked="checked"><strong>Marcar Todos</strong></td>

	  <td><input type="checkbox" onclick="javascript:<?=$checkall_editar;?>" checked="checked"><strong>Marcar Todos</strong></td>

	  <td><input type="checkbox" onclick="javascript:<?=$checkall_incluir;?>" checked="checked"><strong>Marcar Todos</strong></td>

	  <td><input type="checkbox" onclick="javascript:<?=$checkall_excluir;?>" checked="checked"><strong>Marcar Todos</strong></td>

	</tr>

	<?php

	$checkall_ver = '';

	$checkall_editar = '';

	$checkall_incluir = '';

	$checkall_excluir = '';

	$checkall_ver .= "document.getElementById('ver_".$ar_cad['id_cad']."').checked = this.checked; ";

	$checkall_editar .= "document.getElementById('editar_".$ar_cad['id_cad']."').checked = this.checked; ";

	$checkall_incluir .= "document.getElementById('incluir_".$ar_cad['id_cad']."').checked = this.checked; ";

	$checkall_excluir .= "document.getElementById('excluir_".$ar_cad['id_cad']."').checked = this.checked; ";

	}

	else{

		$checkall_ver .= "document.getElementById('ver_".$ar_cad['id_cad']."').checked = this.checked; ";

		$checkall_editar .= "document.getElementById('editar_".$ar_cad['id_cad']."').checked = this.checked; ";

		$checkall_incluir .= "document.getElementById('incluir_".$ar_cad['id_cad']."').checked = this.checked; ";

		$checkall_excluir .= "document.getElementById('excluir_".$ar_cad['id_cad']."').checked = this.checked; ";

	}

	if($last_modulo != $ar_cad['a']){

	?>

	  <tr>

		<td colspan="3"><div style="background-color:#375C79; color:#FFFFFF; font-weight:bold; height:20px;"><?=search_name('is_modulos','id_modulo','nome_modulo',$ar_cad['id_modulo']);?></div></td>

	  </tr>

	<?php

	}

	if($last_grupo != $ar_cad['a']){

	?>

		<tr>

		  <td colspan="3"><div style="background-color:#3067A0; color:#FFFFFF; font-weight:bold;"><?=$ar_cad['nome_grupo'];?></div></td>

		</tr>

	<?php

	}

	$qry_bloqueio = query("SELECT * FROM is_perfil_funcao_bloqueio_cad WHERE id_perfil = '".$_POST['edtid_perfil']."' AND id_cad = '".$ar_cad['id_cad']."'");

	$num_rows = numrows($qry_bloqueio);

	if($num_rows > 0){

		$ar_bloqueio = farray($qry_bloqueio);

		$check_ver = ($ar_bloqueio['sn_bloqueio_ver'] != 1)?' checked="checked" ':'';

		$check_editar = ($ar_bloqueio['sn_bloqueio_editar'] != 1)?' checked="checked" ':'';

		$check_incluir = ($ar_bloqueio['sn_bloqueio_incluir'] != 1)?' checked="checked" ':'';

		$check_excluir = ($ar_bloqueio['sn_bloqueio_excluir'] != 1)?' checked="checked" ':'';

	}

	else{

		$check_ver = ' checked="checked" ';

		$check_editar = ' checked="checked" ';

		$check_incluir = ' checked="checked" ';

		$check_excluir = ' checked="checked" ';

	}

	?>

	  <tr bgcolor="<?php echo $bgcolor;?>">

		<td align="right"><?php echo $ar_cad['titulo'].' - '.$ar_cad['id_cad'];?></td>

		<td><input type="checkbox" id="ver_<?=$ar_cad['id_cad'];?>" name="ver_<?=$ar_cad['id_cad'];?>" <?=$check_ver;?> value="S" onclick="javascript:if(this.checked == false){gebi('editar_<?=$ar_cad['id_cad'];?>').checked = false;gebi('incluir_<?=$ar_cad['id_cad'];?>').checked = false;gebi('excluir_<?=$ar_cad['id_cad'];?>').checked = false;}"></td>

		<td><input type="checkbox" id="editar_<?=$ar_cad['id_cad'];?>" name="editar_<?=$ar_cad['id_cad'];?>" <?=$check_editar;?> value="S" onclick="javascript:if(this.checked == true){document.getElementById('ver_<?=$ar_cad['id_cad'];?>').checked = true;}"></td>

		<td><input type="checkbox" id="incluir_<?=$ar_cad['id_cad'];?>" name="incluir_<?=$ar_cad['id_cad'];?>" <?=$check_incluir;?> value="S" onclick="javascript:if(this.checked == true){document.getElementById('ver_<?=$ar_cad['id_cad'];?>').checked = true;}"></td>

		<td><input type="checkbox" id="excluir_<?=$ar_cad['id_cad'];?>" name="excluir_<?=$ar_cad['id_cad'];?>" <?=$check_excluir;?> value="S" onclick="javascript:if(this.checked == true){document.getElementById('ver_<?=$ar_cad['id_cad'];?>').checked = true;}"></td>

	  </tr>

	<?php

	$last_modulo = $ar_cad['a'];

	$last_grupo = $ar_cad['a'];

	$count_row = $count_row + 1;

	if($count_row == $num_rows_cad){

	/*

	?>

	<tr bgcolor="<?=$bgcolor;?>">

	  <td>&nbsp;</td>

	  <td><input type="checkbox" onclick="javascript:<?php echo $checkall_ver;?>" checked="checked"><strong>Marcar Todos</strong></td>

	  <td><input type="checkbox" onclick="javascript:<?php echo $checkall_editar;?>" checked="checked"><strong>Marcar Todos</strong></td>

	  <td><input type="checkbox" onclick="javascript:<?php echo $checkall_incluir;?>" checked="checked"><strong>Marcar Todos</strong></td>

	  <td><input type="checkbox" onclick="javascript:<?php echo $checkall_excluir;?>" checked="checked"><strong>Marcar Todos</strong></td>

	</tr>

<?php

	*/}

}

?>

	<tr bgcolor="<?=$bgcolor;?>">

	  <td>&nbsp;</td>

	  <td><input type="checkbox" onclick="javascript:<?php echo $checkall_ver_all;?>" checked="checked"><strong>Marcar Tudo</strong></td>

	  <td><input type="checkbox" onclick="javascript:<?php echo $checkall_editar_all;?>" checked="checked"><strong>Marcar Tudo</strong></td>

	  <td><input type="checkbox" onclick="javascript:<?php echo $checkall_incluir_all;?>" checked="checked"><strong>Marcar Tudo</strong></td>

	  <td><input type="checkbox" onclick="javascript:<?php echo $checkall_excluir_all;?>" checked="checked"><strong>Marcar Tudo</strong></td>

	</tr>

</table>

<input type="button" value="Confirmar Permiss&otilde;es do Perfil" class="botao_form" style="font-size:18px;color:#FF0000;" onClick="javascript:bloqueios_cad_custom_p2();">

