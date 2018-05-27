<?php
header("Content-Type: text/html; charset=ISO-8859-1"); 
$odbc_c = true;
// Parâmetros
$mov2adm = 'ems2mov_prod';

//$dt_base = date("Y-m-d",strtotime(" -3 days"));
$dt_base = date("Y-m-d");
echo $dt_base.'<br>';
$dt_base_pag = date("Y-m-d");


$id_usuario = 'IMPORTDTS';
//Conecta com os bancos ODBC
//$cnx2 = odbc_connect($mov2adm,"sysprogress","sysprogress") or die("Erro na conexão com o Database"); 

include('../../conecta.php');
include('../../functions.php');

function executaDepara($arDepara,$arDados){
	#Executa depara para sincronizar arrays de dados
	$na = array();
	foreach($arDepara as $k => $v){
		$na[$k] = $arDados[$v];
	
	}
	return $na;
}
if(isset($_GET['data'])){
	if(!empty($_GET['data'])){
		$data = $_GET['data'];
	} else {
		$data = date('2010-10-14');
	}
} else {
	$data = date('2010-10-14');
}

$sql_pedidos = "SELECT * FROM pub.\"ped-venda\" WHERE \"dt-emissao\" >= '2010-10-18'";
$qry_pedidos = odbc_exec($cnx2,$sql_pedidos);
echo $sql_pedidos, '<hr>';
while($ar_pedidos = odbc_fetch_array($qry_pedidos)){
	$sql_pedido = "SELECT t1.* FROM pub.\"ped-venda\" t1 WHERE t1.\"nr-pedido\" = '".$ar_pedidos['nr-pedido']."'";
	echo $sql_pedido , '<br />';
	$qry_pedido = odbc_exec($cnx2,$sql_pedido);
	$ar_pedido = odbc_fetch_array($qry_pedido);
	odbc_free_result($qry_pedido);
	
	/* Pegando o cód do representante */
	$sql_rep = "SELECT \"cod-rep\" FROM pub.\"repres\" WHERE \"nome-abrev\" = '".$ar_pedido['no-ab-reppri']."'";
	echo $sql_rep, '<br />';
	$qry_rep = odbc_exec($cnx1,$sql_rep);
	$ar_rep = odbc_fetch_array($qry_rep);
	/* Pegando o cód da transportadora */
	$sql_transp = "SELECT \"cod-transp\" FROM pub.\"transporte\" WHERE \"nome-abrev\" = '".$ar_pedido['nome-transp']."'";
	echo $sql_transp, '<br />';
	$qry_transp = odbc_exec($cnx1,$sql_transp);
	$ar_transp = odbc_fetch_array($qry_transp);
	/* Pegando o cód cliente Oasis */
	$sql_pessoa = "SELECT id_pessoa FROM is_pessoas WHERE id_pessoa_erp = '".$ar_pedido['cod-emitente']."'";
	echo $sql_pessoa , '<br />';
	$qry_pessoa = mysql_query($sql_pessoa);
	if(mysql_num_rows($qry_pessoa) != 1){
		echo 'Erro 1: Cliente não existe no CRM ---- Pedido :',$ar_pedidos['nr-pedido'],'<br>';
	}
	else{
		$ar_pessoa = mysql_fetch_array($qry_pessoa);
	}
	
	/* Trocando as infomrações necessárias */
	$ar_pedido['no-ab-reppri'] 	= $ar_rep['cod-rep'];
	$ar_pedido['nome-transp'] 	= $ar_transp['cod-transp'];
	$ar_pedido['cod-emitente'] 	= $ar_pessoa['id_pessoa'];
	
	$ar_depara_pedidos = array(
		'nr-pedcli' => 'id_pedido',
		'cod-emitente' => 'id_pessoa',
		'no-ab-reppri' => 'id_representante',
		'user-impl' => 'id_vendedor',
		'nr-tabpre' => 'id_tab_preco',
		'dt-emissao' => 'dt_pedido',
		'dt-entrega' => 'dt_entrega',
		'cod-cond-pag' => 'id_cond_pagto',
		'vl-tot-ped' => 'vl_bruto',
		'vl-liq-ped' => 'vl_liquido',
		'cod-sit-ped' => 'id_sit_ped',
		'cond-espec' => 'obs',
		'observacoes' => 'obs_nf',
		'nat-operacao' => 'natureza_operacao',
		'cod-estabel' => 'id_estabelecimento',
		'cod-entrega' => 'id_endereco_entrega',
		'cod-des-merc' => 'id_dest_merc',
		'nome-transp' => 'id_transportadora'
	);
	
	$ar_sql_insert = executaDepara(array_flip($ar_depara_pedidos),$ar_pedido);
	$ar_sql_insert['tipo_pedido'] = 'NOR';
	$ar_sql_insert['exportado_erp'] = 'S';
	
	$sql_count_pedido = autoExecuteSql('is_pedidos', $ar_sql_insert, 'COUNT', array('id_pedido'));
	$ar_count_pedido = mysql_fetch_array(mysql_query($sql_count_pedido));
	$sql_pedido = '';
	if($ar_count_pedido['CNT'] == 0){
		$sql_pedido = autoExecuteSql('is_pedidos', $ar_sql_insert, 'INSERT');
	}
	elseif($ar_count_pedido['CNT'] == 1){
		$sql_pedido = autoExecuteSql('is_pedidos', $ar_sql_insert, 'UPDATE', array('id_pedido'));
	}
	//Executando o SQL do Pedido
	echo '######################### SQL PEDIDOS ######################### <br />', $sql_pedido, '<br />';
	$qry_pedido = mysql_query($sql_pedido) or die (mysql_error());
	$qry_pedido = true;
	//Se ocorrer algum erro de SQL, pula o registro.
	if(!$qry_pedido){
		continue;
	}
	$sql_itens = "SELECT * FROM pub.\"ped-item\" WHERE \"nr-pedcli\" = '".$ar_pedido['nr-pedcli']."' AND \"nome-abrev\" = '".$ar_pedido['nome-abrev']."'";
	echo $sql_itens, '<br />';
	$qry_itens = odbc_exec($cnx2,$sql_itens);
	$ar_depara_pedidos_itens = array(
		'nr-pedcli' 					=> 'id_pedido',
		'it-codigo' 					=> 'id_produto',
		'qt-pedida' 					=> 'qtde',
		'vl-preuni' 					=> 'vl_tabela',
		'nr-sequencia' 					=> 'id_item',
		//'tp_desc' 					=> 'tp_desc',
		'des-pct-desconto-inform'                       => 'vl_desc',
		'nat-operacao' 					=> 'natureza_operacao',
		'cod-refer' 					=> 'id_referencia',
		'qt-atendida'					=> 'qtde_faturada'
	);
	$ar_itens_nr_sequencia = array();
	while($ar_itens = odbc_fetch_array($qry_itens)){
		
		/* Fixando valores nos dados */
		$ar_itens['tp_desc'] = '%';
                if(!is_numeric($ar_itens['des-pct-desconto-inform'])){
                    $ar_itens['des-pct-desconto-inform'] = 0;
                    $ar_depara_pedidos_itens['des-pct-desconto-inform'] = 'vl_desc';
                }
		
		$ar_sql_insert_itens = executaDepara(array_flip($ar_depara_pedidos_itens),$ar_itens);
		$sql_count_itens = autoExecuteSql('is_pedidos_itens', $ar_sql_insert_itens, 'COUNT', array('id_pedido','id_item'));
		$ar_count_itens = mysql_fetch_array(mysql_query($sql_count_itens));
		$sql_item = '';
		if($ar_count_itens['CNT'] == 0){
			$sql_item = autoExecuteSql('is_pedidos_itens', $ar_sql_insert_itens, 'INSERT');
		}
		elseif($ar_count_itens['CNT'] == 1){
			$sql_item = autoExecuteSql('is_pedidos_itens', $ar_sql_insert_itens, 'UPDATE', array('id_pedido','id_item'));
		}
		//Executando o SQL do Pedido
		echo '######################### SQL ITENS ######################### <br />', $sql_item, '<br />';
		$qry_item = mysql_query($sql_item) or die (mysql_error());
		$qry_item = true;
		//Se ocorrer algum erro de SQL, pula o registro.
		if(!$qry_item){
			continue;	
		}
		else{
			$ar_itens_nr_sequencia[] = $ar_itens['nr-sequencia'];	
		}
	}
	echo '<hr>';
}
?>