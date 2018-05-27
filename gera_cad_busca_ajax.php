<?php

session_start();



header( "Cache-Control: no-cache" );
header( "Pragma: no-cache" );
header('Content-Type: text/html; charset=utf-8');

require_once("conecta.php");
require_once("funcoes.php");

$sql_ajax_busca         = $_POST["sql_busca"];
$conteudos_ajax_busca   = $_POST["conteudos_busca"];
$a_conteudos_ajax_busca = explode(";", $conteudos_ajax_busca);

for ($i = 0; $i <= count($a_conteudos_ajax_busca); $i++) {
    $sql_ajax_busca = str_replace("@campo[" . ($i + 1) . "]", "'".$a_conteudos_ajax_busca[$i]."'", $sql_ajax_busca);
}
$sql_ajax_busca_trat = str_replace("<br />", '', $sql_ajax_busca);
$a_ajax_busca = farray(query($sql_ajax_busca_trat));
$ret_ajax_busca = '';

for ($i = 0; $i <= count($a_ajax_busca)-1; $i++) {
   // Tratamento para numeros
    if (($a_ajax_busca[$i]*1) > 0) {
        $a_ajax_busca[$i] = str_replace('.', ',', $a_ajax_busca[$i]);
    }
   // Tratamento para data
    if (((strlen($a_ajax_busca[$i])=='10') || (strlen($a_ajax_busca[$i])=='19')) && (substr($a_ajax_busca[$i],4,1)=='-') && (substr($a_ajax_busca[$i],7,1)=='-')) {
        $a_ajax_busca[$i] = DataGetBD($a_ajax_busca[$i]);
    }

   $ret_ajax_busca .= $a_ajax_busca[$i] . ';';
}
$ret_ajax_busca = substr($ret_ajax_busca, 0, strlen($ret_ajax_busca) - 1);
echo $ret_ajax_busca;