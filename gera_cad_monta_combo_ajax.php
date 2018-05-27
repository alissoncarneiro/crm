<?php

@session_start();
@header("Content-Type: text/html;  charset=ISO-8859-1", true);
@header("Pragma: no-cache");
@header("Cache-Control: no-store, no-cache, must-revalidate");
@header("Cache-Control: post-check=0, pre-check=0", false);

require_once("conecta.php");
require_once("funcoes.php");

$sql_ajax_busca = $_POST["sql_busca"];
$conteudos_ajax_busca = $_POST["conteudos_busca"];
$a_conteudos_ajax_busca = explode(";", $conteudos_ajax_busca);

for ($i = 0; $i <= count($a_conteudos_ajax_busca); $i++) {
    $sql_ajax_busca = str_replace("@campo[" . ($i + 1) . "]", "'" . str_replace(",", "','", $a_conteudos_ajax_busca[$i]) . "'", $sql_ajax_busca);
}
$sql_ajax_busca_trat = str_replace("<br />", '', $sql_ajax_busca);

$q_ajax_busca = query($sql_ajax_busca_trat);

$ret_ajax_busca = '';

while ($a_ajax_busca = farray($q_ajax_busca)) {

        // Tratamento para numeros
        if (($a_ajax_busca[0] * 1) > 0) {
            $a_ajax_busca[0] = str_replace('.', ',', $a_ajax_busca[0]);
        }
        // Tratamento para data
        if (((strlen($a_ajax_busca[$i]) == '10') || (strlen($a_ajax_busca[$i]) == '19')) && (substr($a_ajax_busca[$i], 4, 1) == '-') && (substr($a_ajax_busca[$i], 7, 1) == '-')) {
            $a_ajax_busca[0] = DataGetBD($a_ajax_busca[0]);
        }

        $ret_ajax_busca .= RemoveAcentos($a_ajax_busca[0]) . '@descr_combo@'.RemoveAcentos($a_ajax_busca[1]).';';
}

$ret_ajax_busca = substr($ret_ajax_busca, 0, strlen($ret_ajax_busca) - 1);
echo $ret_ajax_busca;
?>
