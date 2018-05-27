<?php
session_start();
include("conecta.php");
$proposta = $_POST["proposta"];

  	//recupera o próximo id para pedido
	$pegaid = mysql_fetch_array(mysql_query("SHOW TABLE STATUS LIKE 'is_propostas'"));
	$visualizarID = $pegaid["Auto_increment"];
	

$ver_one = mysql_fetch_array(mysql_query("select id_empresa_contato from is_atividades where id_atividade = '$proposta'"));
$id_emp = $ver_one["id_empresa_contato"];

$ver_one = mysql_fetch_array(mysql_query("select id_vendedor from is_pessoas where id_pessoa = $id_emp"));
$idvendedor = $ver_one["id_vendedor"];


//GERA ID DA PROPOSTA
$id_minhaproposta = date("ym").str_pad($visualizarID, 4, 0, STR_PAD_LEFT)."-".$idvendedor;
	
$data = date("Y-m-d");
$hora = date("H:m");
$user = $_SESSION["id_usuario"];

$sql = "INSERT INTO is_propostas (
		id_proposta,
		id_usuario_resp,
		dt_cadastro,
		hr_cadastro,
		id_usuario_cad,
		dt_alteracao,
		hr_alteracao,
		id_usuario_alt,
		id_pessoa,
		id_pessoa_contato
		)
		SELECT 
		'".$id_minhaproposta."',
		'".$idvendedor."',
		'$data',
		'$hora',
		'$user',
		'$data',
		'$hora',
		'$user',
		id_empresa_contato,
		id_pessoa_contato
		FROM
		is_atividades where id_atividade = '$proposta'";
		
		
$exec = mysql_query($sql);

$sql_two = "UPDATE is_atividades SET dt_real_fim = '$data', hr_real_fim = '$hora', id_situacao = 'R', obs_int = 'Proposta gerado pelo sistema' where id_atividade = '$proposta'";

$exec_two = mysql_query($sql_two);

mysql_query("DELETE FROM is_propostas WHERE id_proposta = '$id_minhaproposta' AND trim(id_pessoa_contato) = ''");

echo "Atividade num. $proposta foi transformada em uma proposta com num. $id_minhaproposta com sucesso.";
?>