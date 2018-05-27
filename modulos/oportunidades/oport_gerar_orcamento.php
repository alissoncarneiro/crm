<?php
/*
 * oport_gerar_orcamento.php
 * Autor: Alex
 * 20/04/2011 14:30
 * -
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
header("Content-Type: text/html;  charset=ISO-8859-1");
session_start();
$PrefixoIncludes = '../venda/';
require_once('../venda/includes.php');

$NumregOportunidade = $_POST['id_oportunidade'];
$Oportunidade = new Oportunidade($NumregOportunidade);
$NumeroOrcamento = $Oportunidade->GeraOrcamento();
echo $Oportunidade->getMensagem();
?>