<?
header("Content-Type: text/html;  charset=ISO-8859-1",true);
session_start();
require_once("../../conecta.php");
require_once("../../funcoes.php");
require_once("../../functions.php");
$pnumreg = $_GET["pnumreg"];
$id_usuario = $_SESSION["id_usuario"];
$ar_proposta = mysql_fetch_array(mysql_query("SELECT * FROM is_propostas WHERE numreg = '".$pnumreg."'"));
$ar_cnt_pedido = mysql_fetch_array(mysql_query("SELECT COUNT(*) AS CNT FROM is_pedidos WHERE id_proposta = '".$ar_proposta['id_proposta']."'"));
$ar_cnt_itens_proposta = mysql_fetch_array(mysql_query("SELECT COUNT(*) AS CNT FROM is_propostas_prod WHERE id_proposta = '".$ar_proposta['id_proposta']."'"));
if($ar_cnt_itens_proposta['CNT'] == 0 || $ar_cnt_itens_proposta['CNT'] == ''){
	echo alert(utf8_decode("Esta proposta não possui itens. O pedido não pode ser gerado."));
	echo windowclose();
	exit;
}
if($ar_cnt_pedido['CNT'] == 0 || $ar_cnt_pedido['CNT'] == ''){
	$ar_cliente = GetPessoa($ar_proposta['id_pessoa'],array('id_representante'));
	$id_pedido = mysql_fetch_array(mysql_query("SELECT max FROM is_max_ids WHERE id_cadastro = 'pedidos_cad_lista'"));
	$id_pedido = ($id_pedido['max'])*1;
	mysql_query("UPDATE is_max_ids SET max = '".($id_pedido+1)."' WHERE id_cadastro = 'pedidos_cad_lista'");
	$id_pedido = $prefixo_id_pedido.$id_pedido;
	$ar_sql_insert_pedido = array();
	$ar_sql_insert_pedido['id_pedido'] = $id_pedido;
	$ar_sql_insert_pedido['id_pessoa'] = $ar_proposta['id_pessoa'];
	$ar_sql_insert_pedido['id_tab_preco'] = $ar_proposta['id_tab_preco'];
	$ar_sql_insert_pedido['id_cond_pagto'] = $ar_proposta['id_cond_pagto'];
	$ar_sql_insert_pedido['id_sit_ped'] = '10';
	$ar_sql_insert_pedido['dt_pedido'] = date("Y-m-d");
	$ar_sql_insert_pedido['dt_entrega_desejada'] = $ar_proposta['dt_entrega_desejada'];
	$ar_sql_insert_pedido['id_representante'] = $ar_cliente['id_representante'];
	$ar_sql_insert_pedido['natureza_operacao'] = $ar_cliente['natureza_operacao'];
	$ar_sql_insert_pedido['tipo_pedido'] = 'NOR';
	$ar_sql_insert_pedido['id_estabelecimento'] = $ar_proposta['id_estabelecimento'];
	$ar_sql_insert_pedido['id_proposta'] = $ar_proposta['id_proposta'];
	$sql_insert_pedido = AutoExecuteSql('is_pedidos',$ar_sql_insert_pedido,'INSERT','',array('`',"'"),'');
	if(mysql_query($sql_insert_pedido)){
		$qry_itens_proposta = mysql_query("SELECT * FROM is_propostas_prod WHERE id_proposta = '".$ar_proposta['id_proposta']."'");
		$cnt_id_item = 10;
		while($ar_itens_proposta = mysql_fetch_array($qry_itens_proposta)){
			$ar_produto = GetProduto($ar_itens_proposta['id_produto']);
			$total_unid = $ar_itens_proposta['qtde'];
			$pct_desc = $ar_itens_proposta['pct_desc'];
			$vl_unit = $ar_itens_proposta['valor'];
			$vl_desc = (($vl_unit * $pct_desc) / 100);
			$ar_sql_insert_pedido_item = array();
			$ar_sql_insert_pedido_item['id_pedido'] = $id_pedido;
			$ar_sql_insert_pedido_item['id_produto'] = $ar_itens_proposta['id_produto'];
			$ar_sql_insert_pedido_item['qtde'] = $total_unid;
			$ar_sql_insert_pedido_item['vl_tabela'] = $vl_unit;
			$ar_sql_insert_pedido_item['pct_desconto'] = $pct_desc;
			$ar_sql_insert_pedido_item['vl_desconto'] = $vl_desc;
			$ar_sql_insert_pedido_item['vl_total'] = ($vl_unit - $vl_desc) * $total_unid;
			$ar_sql_insert_pedido_item['total_unid'] = $total_unid;
			$ar_sql_insert_pedido_item['id_item'] = $cnt_id_item;
			$ar_sql_insert_pedido_item['tp_desc'] = '%';
			$ar_sql_insert_pedido_item['vl_desc'] = $pct_desc;
			$ar_sql_insert_pedido['natureza_operacao'] = $ar_cliente['natureza_operacao'];
			$sql_insert_pedido_item = autoExecuteSql('is_pedidos_itens',$ar_sql_insert_pedido_item,'INSERT','',array('`',"'"),'');
			mysql_query($sql_insert_pedido_item) or die(mysql_error());
			$cnt_id_item = $cnt_id_item + 10;
		}
		echo alert(utf8_decode("Foi gerado o pedido Nº ".$id_pedido."."));
		echo windowclose();
		exit;
	}
	else{
		echo mysql_error();
		echo alert(utf8_decode("Ocorreu um erro na geração do pedido. Entre em contato com o administrador do sistema."));
		echo windowclose();
		exit;
	}	
}
else {
	echo alert(utf8_decode("Já existe um pedido gerado a partir desta proposta. O pedido não pode ser gerado."));
	echo windowclose();
	exit;
}
?>
