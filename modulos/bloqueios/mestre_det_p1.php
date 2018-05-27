<?
header("Content-Type:text/html; charset=iso-8859-1;");
?>
<input type="button" value="Confirmar Permiss&otilde;es do Perfil" class="botao_form" style="font-size:18px;color:#FF0000;" onClick="javascript:bloqueios_mestre_det_custom_p2();">
<table width="98%" align="center" border="0" style="border: 1px solid #0066CC;" cellspacing="2" cellpadding="0" >
  <tr style="background-color:#006699;">
    <td width="475">&nbsp;</td>
    <td width="52" align="center"><div align="center"><strong style="color:#FFFFFF;">Ver</strong></div></td>
    <td width="65" align="center"><div align="center"><strong style="color:#FFFFFF;">Editar</strong></div></td>
  </tr>
<?
require('../../conecta.php');
require('../../functions.php');
$qry_mestre_det = query("SELECT t1.numreg,t1.nome_sub,t2.titulo as titulo_mestre,t3.titulo as titulo_detalhe 
								FROM is_gera_cad_sub t1 
								INNER JOIN is_gera_cad t2 ON t1.id_funcao_mestre = t2.id_cad
								INNER JOIN is_gera_cad t3 ON t1.id_funcao_detalhe = t3.id_cad
								ORDER BY t1.nome_sub");
$count_row = 0;
$num_rows_mestre_det = numrows($qry_mestre_det);
while($ar_mestre_det = farray($qry_mestre_det)){
	$checkall_ver_all .= "document.getElementById('ver_".$ar_mestre_det['numreg']."').checked = this.checked; ";
	$checkall_editar_all .= "document.getElementById('editar_".$ar_mestre_det['numreg']."').checked = this.checked; ";

	$bgcolor = ($count_row % 2 == 0)?'#CCCCCC':'#FFFFFF';
	if($last_modulo != $ar_mestre_det['id_modulo'] && $count_row > 0){
	?>
	<tr bgcolor="<?=$bgcolor;?>">
	  <td>&nbsp;</td>
	  <td><input type="checkbox" onclick="javascript:<?=$checkall_ver;?>" checked="checked"><strong>Marcar Todos</strong></td>
	  <td><input type="checkbox" onclick="javascript:<?=$checkall_editar;?>" checked="checked"><strong>Marcar Todos</strong></td>
	</tr>
	<?
	$checkall_ver = '';
	$checkall_editar = '';
	$checkall_ver .= "document.getElementById('ver_".$ar_mestre_det['numreg']."').checked = this.checked; ";
	$checkall_editar .= "document.getElementById('editar_".$ar_mestre_det['numreg']."').checked = this.checked; ";
	}
	else{
		$checkall_ver .= "document.getElementById('ver_".$ar_mestre_det['numreg']."').checked = this.checked; ";
		$checkall_editar .= "document.getElementById('editar_".$ar_mestre_det['numreg']."').checked = this.checked; ";
	}
	if($last_modulo != $ar_mestre_det['id_modulo']){
	?>
	  <tr>
		<td colspan="3"><div style="background-color:#375C79; color:#FFFFFF; font-weight:bold; height:20px;"><?=search_name('is_modulos','id_modulo','nome_modulo',$ar_mestre_det['id_modulo']);?></div></td>
	  </tr>
	<?
	}
	if($last_grupo != $ar_mestre_det['nome_grupo']){
	?>
		<tr>
		  <td colspan="3"><div style="background-color:#3067A0; color:#FFFFFF; font-weight:bold;"><?=$ar_mestre_det['nome_grupo'];?></div></td>
		</tr>
	<?
	}
	$qry_bloqueio = query("SELECT * FROM is_perfil_funcao_bloqueio_mestre_det WHERE id_perfil = '".$_POST['edtid_perfil']."' AND numreg_sub = '".$ar_mestre_det['numreg']."'");
	$num_rows = numrows($qry_bloqueio);
	if($num_rows > 0){
		$ar_bloqueio = farray($qry_bloqueio);
		$check_ver = ($ar_bloqueio['sn_bloqueio_ver'] != 1)?' checked="checked" ':'';
		$check_editar = ($ar_bloqueio['sn_bloqueio_editar'] != 1)?' checked="checked" ':'';
	}
	else{
		$check_ver = ' checked="checked" ';
		$check_editar = ' checked="checked" ';
	}
	?>
	  <tr bgcolor="<?=$bgcolor;?>">
		<td align="right"><?=$ar_mestre_det['nome_sub'].' - '.$ar_mestre_det['titulo_mestre'].'/'.$ar_mestre_det['titulo_detalhe'];?></td>
		<td><input type="checkbox" id="ver_<?=$ar_mestre_det['numreg'];?>" name="ver_<?=$ar_mestre_det['numreg'];?>" <?=$check_ver;?> value="S" onclick="javascript:if(this.checked == false){document.getElementById('editar_<?=$ar_mestre_det['numreg'];?>').checked = false;}"></td>
		<td><input type="checkbox" id="editar_<?=$ar_mestre_det['numreg'];?>" name="editar_<?=$ar_mestre_det['numreg'];?>" <?=$check_editar;?> value="S" onclick="javascript:if(this.checked == true){document.getElementById('ver_<?=$ar_mestre_det['numreg'];?>').checked = true;}"></td>
	  </tr>
	<?
	$last_modulo = $ar_mestre_det['id_modulo'];
	$last_grupo = $ar_mestre_det['nome_grupo'];
	$count_row = $count_row + 1;
	if($count_row == $num_rows_mestre_det){?>
	<tr bgcolor="<?=$bgcolor;?>">
	  <td>&nbsp;</td>
	  <td><input type="checkbox" onclick="javascript:<?=$checkall_ver;?>" checked="checked"><strong>Marcar Todos</strong></td>
	  <td><input type="checkbox" onclick="javascript:<?=$checkall_editar;?>" checked="checked"><strong>Marcar Todos</strong></td>
	</tr>
<?
	}
}
?>
	<tr bgcolor="<?=$bgcolor;?>">
	  <td>&nbsp;</td>
	  <td><input type="checkbox" onclick="javascript:<?=$checkall_ver_all;?>" checked="checked"><strong>Marcar Tudo</strong></td>
	  <td><input type="checkbox" onclick="javascript:<?=$checkall_editar_all;?>" checked="checked"><strong>Marcar Tudo</strong></td>
	</tr>
</table>
<input type="button" value="Confirmar Permiss&otilde;es do Perfil" class="botao_form" style="font-size:18px;color:#FF0000;" onClick="javascript:bloqueios_mestre_det_custom_p2();">
