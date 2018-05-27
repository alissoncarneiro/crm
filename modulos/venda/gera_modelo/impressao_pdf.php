<?php
include('../../../conecta.php');
$sql_modelo = "select * from is_modelo_orcamento where numreg='".$_GET['id_modelo']."'";
$qry_modelo = query($sql_modelo);
$ar_modelo = farray($qry_modelo);
include($ar_modelo['caminho_modelo_orcamento']);
?>
