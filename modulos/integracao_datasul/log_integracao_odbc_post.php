<?php
/*
 * log_integracao_odbc_post.php
 * Autor: Alex
 * 29/09/2011 13:21:09
 */
session_start();
header("Content-Type: text/html; charset=ISO-8859-1");
if($_SESSION['id_usuario'] == ''){
    echo '<script type="text/javascript"> alert(\'Usuário não está logado.\'); window.location.href = window.location.href; </script>';
    exit;
}
include('../../conecta.php');
include('../../functions.php');
$StringChecked = $_POST['string_checked'];
$SqlUpdate = "UPDATE is_log_integracao_odbc_erp_datasul SET id_usuario_check = '".$_SESSION['id_usuario']."', dt_check = '".date("Y-m-d")."', hr_check = '".date("H:i:s")."' WHERE numreg IN(".$StringChecked.")";
query($SqlUpdate);
?>