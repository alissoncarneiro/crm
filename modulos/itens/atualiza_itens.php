<?
@session_start();
require_once("../../conecta.php");
require_once("../../functions.php");
$id_session = $_POST['edtid_session'];

$tabela = $_SESSION[$id_session.'tabela'];
$val_chave = $_POST['val_chave'];
$campo = $_POST['campo'];
$valor = $_POST['valor'];

mysql_query("UPDATE `".$tabela."` SET `".$campo."` = '".$valor."' WHERE `numreg` = '".$val_chave."'");


?>