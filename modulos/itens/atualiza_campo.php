<?
@session_start();
$id_session = $_POST['edtid_session'];
$campo = $_POST['campo'];
$valor = $_POST['valor'];
$_SESSION[$id_session.$campo] = $valor;
echo $_SESSION[$id_session.$campo];

?>