<?

@header("Content-Type: text/html;  charset=ISO-8859-1", true);
@session_start();

require_once("../../conecta.php");
require_once("../../funcoes.php");

$pnumreg = $_GET["pnumreg"];
$id_usuario = $_SESSION["id_usuario"];

$qry_opor = farray(query("select * from is_oportunidade where numreg = '" . $pnumreg . "'"));

$qry_proposta = farray(query("select * from is_propostas where id_oportunidade = '" . $qry_opor["numreg"] . "'"));

if (empty($qry_proposta["id_proposta"])) {

    $sql = "INSERT INTO is_propostas ( id_proposta , id_proposta_orig, revisao , id_pessoa , id_usuario_resp , valor , id_oportunidade, id_tab_preco, id_cond_pagto) values ( '" . $ultima_proposta . "','" . $ultima_proposta . "','0','" . $qry_opor["id_pessoa"] . "','" . $qry_opor["id_usuario_resp"] . "','" . $qry_opor["valor"] . "','" . $qry_opor["numreg"] . "','" . $qry_opor["id_tab_preco"] . "','" . $qry_opor["id_cond_pagto"] . "')";
		
    query($sql);

    $qry_max_prop = farray(query("select max(numreg) as ultima from is_proposta"));
    $ultima_proposta = $qry_max_prop["ultima"]*1;

    $valor_total = 0;
    $sql_opor_iten = query("select * from is_opor_produto where id_oportunidade = '" . $qry_opor["numreg"] . "'");
    while ($qry_opor_iten = farray($sql_opor_iten)) {
        $valor_total = $valor_total + $qry_opor_iten["valor_total"];
        $sql_item = "INSERT INTO is_proposta_prod  ( id_proposta , id_produto , qtde , pct_desc , valor , valor_total) values ( '" . $ultima_proposta . "','" . $qry_opor_iten["id_produto"] . "','" . $qry_opor_iten["qtde"] . "','" . $qry_opor_iten["pct_desc"] . "','" . $qry_opor_iten["valor"] . "','" . $qry_opor_iten["valor_total"] . "')";
        query($sql_item);
    }
    query("update is_propostas set id_proposta = numreg, id_proposta_orig = numreg where numreg = '".$ultima_proposta."'");



    echo '<script language="Javascript"> ';
    echo "window.alert('Proposta " . (($qry_max_prop["ultima"] * 1) + 1) . " gerada com sucesso !'); ";
    echo ' window.setTimeout( "' . "window.close()" . '", 100);';
    echo '</script>';
} else {

    echo '<script language="Javascript"> ';
    echo "window.alert('Já existe a proposta " . $qry_proposta["id_proposta"] . " gerada para esta oportunidade ! Se deseja gerar revisão, este procedimento deve ser feito no cadastro da proposta.'); ";
    echo ' window.setTimeout( "' . "window.close()" . '", 100);';
    echo '</script>';
}

?>

