<?php
header("Content-Type: text/html; charset=ISO-8859-1");
session_start();
$PrefixoIncludes = '../';
include('../includes.php');

if(empty($_GET['pnumreg'])){
    echo getError('0040010001',getParametrosGerais('RetornoErro'));
    exit;
}
$sql_modelo = "select * from is_modelo_orcamento where numreg='".$_GET['id_modelo']."'";
$qry_modelo = query($sql_modelo);
$ar_modelo = farray($qry_modelo);
include($ar_modelo['caminho_arquivo_principal']);
if($ar_modelo['tp_arquivo'] == 'html'){
    echo '<script language="JavaScript"> window.print(); </script>';
}
?>