<?php
/*
 * configura_odbc_ini_datasul_grava_arquivo_ini.php
 * Autor: Alex
 * 23/02/2011 12:38
 * -
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
header("Content-Type: text/html; charset=ISO-8859-1",true);
session_start();
require('../../classes/class.uB.php');
require('../../functions.php');
$_POST = uB::UrlDecodePost($_POST);

$DOM = new DOMDocument("1.0", "ISO-8859-1");
$DOM->preserveWhiteSpace = false;
$DOM->formatOutput = true;
$Root = $DOM->createElement("resposta");

for($i=1;$i<=6;$i++){
    $Status = 'false';
    if(trim($_POST['alias'.$i]) != ''){
        $TestaConexao = odbc_connect($_POST['alias'.$i],'sysprogress','sysprogress');
        if($TestaConexao){
            $Status = 'true';
            odbc_close($TestaConexao);
        }
    }
    $Conexao = $DOM->createElement("conexao");
    $NumeroConexao = $DOM->createElement("numero_conexao",$i);
    $StatusConexao = $DOM->createElement("status_conexao",$Status);

    $Conexao->appendChild($NumeroConexao);
    $Conexao->appendChild($StatusConexao);

    $Root->appendChild($Conexao);
}

$DOM->appendChild($Root);
//$DOM->save("contatos.xml");
header("Content-Type: text/xml");
print $DOM->saveXML();
?>