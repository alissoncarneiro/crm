<?php
require("../../conecta.php");
require("../../functions.php");
@session_start();
$id_session = $_POST['edtid_session'];
//$tabela = $_SESSION[$id_session.'tabela'];
$tabela = $_POST['tabela'];
//$pfixo = $_SESSION['pfixo'];

$pfixo = str_replace('@igual', '=', $_POST['pfixo']);
$pfixo = str_replace('@diferente', '!=', $pfixo);
$pfixo = str_replace('@s', "'", $pfixo);
$pfixo = str_replace('@', "", $pfixo);


$pfixo = explode("=",$pfixo);
//print_r($pfixo);
$campo = $_POST['campo'];
$valor = $_POST['valor'];

if(empty($_POST['valor'])) {
	echo utf8_encode("Nenhum Produto foi Selecionado!");
	exit;
}

mysql_query("INSERT INTO `".$tabela."` (`dt_cadastro`,`hr_cadastro`,`id_usuario_cad`,`".$campo."`,`".trim($pfixo[0])."`) VALUES ('".date("Y-m-d")."','".date("H:i:s")."','".$_SESSION['id_usuario']."','".$valor."',".trim($pfixo[1]).")");
	
?>