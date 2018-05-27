<?php
@session_start();
require_once("../../conecta.php");
require_once("../../functions.php");
$id_session = $_POST['edtid_session'];

if(!is_array($_SESSION[$id_session.'campos'])) {
    $_SESSION[$id_session.'campos'] = array();
    echo "exit;";
    exit;
}

foreach($_SESSION[$id_session.'campos'] as $k => $v) {
        if($_SESSION[$id_session.'campos'][$k]['numreg'] == $_POST['id_produto_delete']) {
            unset($_SESSION[$id_session.'campos'][$k]);
        }
}


$tabela = $_SESSION[$id_session.'tabela'];

mysql_query("DELETE FROM ".$tabela." WHERE numreg=".$_POST['id_produto_delete']."");
?>