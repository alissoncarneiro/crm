<?php
require('../../conecta.php');
require('../../functions.php');
$ArrayFuncoesDesativadas = array('pessoa');
?>
<form>
<input type="button" value="Confirmar Permissões do Perfil" class="botao_form" style="font-size:18px;color:#FF0000;" onClick="javascript:bloqueios_campos_custom_p2();">
<table width="98%" align="center" border="0" style="border: 1px solid #0066CC;" cellspacing="2" cellpadding="0" >
  <tr style="background-color:#006699;">
    <td width="475">&nbsp;</td>
    <td width="52" align="center"><div align="center"><strong style="color:#FFFFFF;">Ver</strong></div></td>
    <td width="65" align="center"><div align="center"><strong style="color:#FFFFFF;">Editar</strong></div></td>
    <td width="60" align="center"><div align="center"><strong style="color:#FFFFFF;">Valor Padr&atilde;o</strong></div></td>
  </tr>
    <?php
$qry_campos = query("SELECT * FROM is_gera_cad_campos WHERE id_funcao = '".$_POST['edtid_cad']."' ORDER BY nome_grupo ASC, ordem ASC");
$count_row = 0;
$num_rows_campos = numrows($qry_campos);
while($ar_campo = farray($qry_campos)){
        if(array_search($ar_campo['id_funcao'], $ArrayFuncoesDesativadas) !== false){
            continue;
        }

	$checkall_ver_all .= "document.getElementById('ver_".$ar_campo['id_campo']."').checked = this.checked; ";
	$checkall_editar_all .= "document.getElementById('editar_".$ar_campo['id_campo']."').checked = this.checked; ";

	$bgcolor = ($count_row % 2 == 0)?'#CCCCCC':'#FFFFFF';
	if($last_grupo != $ar_campo['nome_grupo'] && $count_row > 0){
	?>
	<tr bgcolor="<?php echo $bgcolor;?>">
	  <td>&nbsp;</td>
	  <td><input type="checkbox" onclick="javascript:<?php echo $checkall_ver;?>" checked="checked"><strong>Marcar Todos</strong></td>
	  <td><input type="checkbox" onclick="javascript:<?php echo $checkall_editar;?>" checked="checked"><strong>Marcar Todos</strong></td>
      <td>&nbsp;</td>
	</tr>
        <?php
	$checkall_ver = '';
	$checkall_editar = '';
	$checkall_ver .= "document.getElementById('ver_".$ar_campo['id_campo']."').checked = this.checked; ";
	$checkall_editar .= "document.getElementById('editar_".$ar_campo['id_campo']."').checked = this.checked; ";
	}
	else{
		$checkall_ver .= "document.getElementById('ver_".$ar_campo['id_campo']."').checked = this.checked; ";
		$checkall_editar .= "document.getElementById('editar_".$ar_campo['id_campo']."').checked = this.checked; ";
	}
	if($last_grupo != $ar_campo['nome_grupo']){
	?>
	  <tr>
		<td colspan="4"><div style="background-color:#375C79; color:#FFFFFF; font-weight:bold; height:20px;"><?php echo search_name('is_modulos','id_modulo','nome_modulo',$ar_campo['nome_grupo']);?></div></td>
	  </tr>
        <?php
	}
	if($last_grupo != $ar_campo['nome_grupo']){
	?>
		<tr>
		  <td colspan="4"><div style="background-color:#3067A0; color:#FFFFFF; font-weight:bold;"><?php echo $ar_campo['nome_grupo'];?></div></td>
		</tr>
        <?php
	}
	$qry_bloqueio = query("SELECT * FROM is_perfil_funcao_bloqueio_campos WHERE id_cad = '".$ar_campo['id_funcao']."' AND id_campo = '".$ar_campo['id_campo']."' AND id_perfil = '".$_POST['edtid_perfil']."'");
	$num_rows = numrows($qry_bloqueio);
	if($num_rows > 0){
		$ar_bloqueio = farray($qry_bloqueio);
		$check_ver = ($ar_bloqueio['sn_bloqueio_ver'] != 1)?' checked="checked" ':'';
		$check_editar = ($ar_bloqueio['sn_bloqueio_editar'] != 1)?' checked="checked" ':'';
                $vl_padrao = $ar_bloqueio['valor_padrao'];
	}
	else{
		$check_ver = ' checked="checked" ';
		$check_editar = ' checked="checked" ';
                $vl_padrao = null;
	}
	?>
	  <tr bgcolor="<?php echo $bgcolor;?>">
		<td align="right"><?php echo $ar_campo['nome_campo'];?></td>
		<td><input type="checkbox" id="ver_<?php echo $ar_campo['id_campo'];?>" name="ver_<?php echo $ar_campo['id_campo'];?>" <?php echo $check_ver;?> value="S" onclick="javascript:if(this.checked == false){document.getElementById('editar_<?php echo $ar_campo['id_campo'];?>').checked = false;}"></td>
		<td><input type="checkbox" id="editar_<?php echo $ar_campo['id_campo'];?>" name="editar_<?php echo $ar_campo['id_campo'];?>" <?php echo $check_editar;?> value="S" onclick="javascript:if(this.checked == true){document.getElementById('ver_<?php echo $ar_campo['id_campo'];?>').checked = true;}"></td>
                <td align="center">
                    <?php
                    if(trim($ar_campo['id_campo']) == 'numreg'){
                        $style_text = 'background-color: #CCCCCC;';
                        $disabled = ' disabled="disabled"';
                    } else {
                        $style_text = '';
                        $disabled = '';
                    }
                    ?>
                    <input style="border: 1px solid #0066CC;<?php echo $style_text;?>"<?php echo $disabled;?> type="text" id="valor_padrao_<?php echo $ar_campo['id_campo'];?>" name="valor_padrao_<?php echo $ar_campo['id_campo'];?>" value="<?php echo $vl_padrao;?>" >
                </td>
	  </tr>
    <?php
	$last_grupo = $ar_campo['nome_grupo'];
	$last_grupo = $ar_campo['nome_grupo'];
	$count_row = $count_row + 1;
	if($count_row == $num_rows_funcoes){?>
	<tr bgcolor="<?php echo $bgcolor;?>">
	  <td>&nbsp;</td>
	  <td><input type="checkbox" onclick="javascript:<?php echo $checkall_ver;?>" checked="checked"><strong>Marcar Todos</strong></td>
	  <td><input type="checkbox" onclick="javascript:<?php echo $checkall_editar;?>" checked="checked"><strong>Marcar Todos</strong></td>
      <td>&nbsp;</td>
	</tr>
        <?php
	}
}
?>
	<tr bgcolor="<?php echo $bgcolor;?>">
	  <td>&nbsp;</td>
	  <td><input type="checkbox" onclick="javascript:<?php echo $checkall_ver_all;?>" checked="checked"><strong>Marcar Tudo</strong></td>
	  <td><input type="checkbox" onclick="javascript:<?php echo $checkall_editar_all;?>" checked="checked"><strong>Marcar Tudo</strong></td>
      <td>&nbsp;</td>
	</tr>
</table>
<input type="button" value="Confirmar Permiss&otilde;es do Perfil" class="botao_form" style="font-size:18px;color:#FF0000;" onClick="javascript:bloqueios_campos_custom_p2();">
</form>