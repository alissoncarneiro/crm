<?php 
header("Content-Type: text/html; charset=ISO-8859-1");
include('../../../../conecta.php');
    
$modelo = $_POST['acao'];

$SqlParte = "select modelo.textohtm_corpo, modelo.wcp_id_curso, parte.nome_parte, parte.numreg
  from is_modelo_orcamento as modelo
  INNER JOIN c_coaching_parte AS parte
  ON parte.id_curso = modelo.wcp_id_curso
where modelo.numreg ='".$modelo."'";
$QrySqlParte = mysql_query($SqlParte);

 ?>
<select id="select_id_modulo" name="select_id_modulo"  onChange="atualizaHtml(this.value);">
	<option value="" >Selecione o Modulo</option>
	 <?php
		while($ArQrySqlParte = farray($QrySqlParte)){
			echo '<option value="'.$ArQrySqlParte['numreg'].'">'.$ArQrySqlParte['nome_parte'].'</option>';
		}
	?>
</select>